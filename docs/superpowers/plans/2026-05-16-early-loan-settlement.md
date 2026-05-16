# Early Loan Settlement Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add an Early Settlement payoff feature for non-bullet loans: when a cashier selects a settlement date, the system auto-calculates the payoff amount (overdue installments in full + remaining principal only, interest waived on future schedules), and if the cashier enters that amount, the loan is immediately closed.

**Architecture:** Two new model methods in `Payement_schedules_model` handle the calculation and execution. A new AJAX controller method provides the date-driven breakdown to the UI. A new `process_early_settlement()` controller method handles account transactions and calls the model payoff method. A new modal is added to `loan_repayment_view.php`.

**Tech Stack:** CodeIgniter 3, PHP, MySQL, jQuery AJAX, Bootstrap modal

---

## File Map

| File | Change |
|------|--------|
| `application/models/Payement_schedules_model.php` | Add `calculate_payoff_amount()` and `payoff_loan()` methods |
| `application/controllers/Loan.php` | Add `get_early_settlement_amount()` AJAX endpoint and `process_early_settlement()` method |
| `application/views/loan/loan_repayment_view.php` | Add Early Settlement button, modal, and JS |

No routes changes needed — CI3 auto-routing handles `controller/method` automatically.

---

## Task 1: Add `calculate_payoff_amount()` to model

**Files:**
- Modify: `application/models/Payement_schedules_model.php` (append before the final closing `}`)

- [ ] **Step 1: Open the file and find the last method**

  Open `application/models/Payement_schedules_model.php`. Scroll to the very end. The last method before the class closing `}` is `collection_sheet()`. You will insert the new method after it, before the final `}`.

- [ ] **Step 2: Add `calculate_payoff_amount()`**

  Insert this method after `collection_sheet()`, before the final `}` of the class:

  ```php
  public function calculate_payoff_amount($loan_id, $date)
  {
      $this->db->select('SUM(amount - paid_amount) AS overdue_amount')
               ->from($this->table)
               ->where('loan_id', $loan_id)
               ->where('status !=', 'PAID')
               ->where('payment_schedule <=', $date);
      $overdue        = $this->db->get()->row();
      $overdue_amount = $overdue->overdue_amount ? (float)$overdue->overdue_amount : 0;

      $this->db->select('SUM(principal) AS future_principal, SUM(interest) AS interest_waived')
               ->from($this->table)
               ->where('loan_id', $loan_id)
               ->where('status !=', 'PAID')
               ->where('payment_schedule >', $date);
      $future          = $this->db->get()->row();
      $future_principal = $future->future_principal ? (float)$future->future_principal : 0;
      $interest_waived  = $future->interest_waived  ? (float)$future->interest_waived  : 0;

      return [
          'overdue_amount'   => round($overdue_amount, 2),
          'future_principal' => round($future_principal, 2),
          'interest_waived'  => round($interest_waived, 2),
          'total_payoff'     => round($overdue_amount + $future_principal, 2),
      ];
  }
  ```

- [ ] **Step 3: Verify by manual inspection**

  Load `http://localhost/fundit/loan/repayment_view/1` in a browser. If no PHP parse error appears, the syntax is valid. (We'll test the logic in Task 3 after the AJAX endpoint is wired up.)

- [ ] **Step 4: Commit**

  ```bash
  git add application/models/Payement_schedules_model.php
  git commit -m "feat: add calculate_payoff_amount() to Payement_schedules_model"
  ```

---

## Task 2: Add `payoff_loan()` to model

**Files:**
- Modify: `application/models/Payement_schedules_model.php` (append after `calculate_payoff_amount()`)

- [ ] **Step 1: Insert `payoff_loan()` after `calculate_payoff_amount()`**

  ```php
  public function payoff_loan($loan_id, $amount, $date)
  {
      // Snapshot interest_waived before making changes (for the activity log)
      $breakdown       = $this->calculate_payoff_amount($loan_id, $date);
      $interest_waived = $breakdown['interest_waived'];

      // Mark overdue schedules (due <= date) as PAID; set paid_amount = the full schedule amount
      $this->db->set('status',       'PAID');
      $this->db->set('paid_amount',  'amount', FALSE); // FALSE = treat 'amount' as a column reference
      $this->db->set('paid_date',    $date);
      $this->db->set('partial_paid', 'NO');
      $this->db->where('loan_id',    $loan_id);
      $this->db->where('status !=',  'PAID');
      $this->db->where('payment_schedule <=', $date);
      $this->db->update($this->table);

      // Mark future schedules (due > date) as PAID; zero interest, set paid_amount = principal only
      $this->db->set('status',       'PAID');
      $this->db->set('interest',     0);
      $this->db->set('paid_amount',  'principal', FALSE); // FALSE = column reference
      $this->db->set('paid_date',    $date);
      $this->db->set('partial_paid', 'NO');
      $this->db->where('loan_id',    $loan_id);
      $this->db->where('status !=',  'PAID');
      $this->db->where('payment_schedule >', $date);
      $this->db->update($this->table);

      // Close the loan
      $this->db->where('loan_id', $loan_id)->update('loan', [
          'loan_status' => 'CLOSED',
          'paid_off'    => 'Yes',
      ]);

      // Log the settlement
      $loan          = $this->db->where('loan_id', $loan_id)->get('loan')->row();
      $currency      = $this->db->where('currency_id', $loan->currency)->get('currencies')->row();
      $currency_code = $currency ? $currency->currency_code : '';

      $this->db->insert('activity_logger', [
          'user_id'       => $this->session->userdata('user_id'),
          'activity'      => 'Early payoff settlement — Loan ID: ' . $loan_id .
                             ' | Settled: ' . number_format((float)$amount, 2) .
                             ' | ' . $currency_code . ' ' . number_format($interest_waived, 2) . ' interest waived',
          'activity_cate' => 'loan_closure',
      ]);

      return true;
  }
  ```

- [ ] **Step 2: Verify syntax**

  Load `http://localhost/fundit/loan/repayment_view/1`. No PHP parse error = syntax is fine.

- [ ] **Step 3: Commit**

  ```bash
  git add application/models/Payement_schedules_model.php
  git commit -m "feat: add payoff_loan() to Payement_schedules_model"
  ```

---

## Task 3: Add AJAX endpoint `get_early_settlement_amount()` to controller

**Files:**
- Modify: `application/controllers/Loan.php` (add after `calculate_payoff_inline()` around line 7002)

- [ ] **Step 1: Find the insertion point**

  Open `application/controllers/Loan.php`. Search for `public function calculate_payoff_inline`. Insert the new method immediately after the closing `}` of that function.

- [ ] **Step 2: Insert `get_early_settlement_amount()`**

  ```php
  public function get_early_settlement_amount($loan_id)
  {
      if (!$this->input->is_ajax_request()) {
          show_error('No direct script access allowed', 403);
          return;
      }

      $date = $this->input->get('date');
      if (!$loan_id || !$date) {
          echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
          return;
      }

      $loan = $this->Loan_model->get_by_id($loan_id);
      if (!$loan || $loan->loan_status === 'CLOSED') {
          echo json_encode(['status' => 'error', 'message' => 'Loan is already closed']);
          return;
      }

      $breakdown     = $this->Payement_schedules_model->calculate_payoff_amount($loan_id, $date);
      $currency      = get_by_id('currencies', 'currency_id', $loan->currency);
      $currency_code = $currency ? $currency->currency_code : '';

      echo json_encode([
          'status'           => 'success',
          'overdue_amount'   => $breakdown['overdue_amount'],
          'future_principal' => $breakdown['future_principal'],
          'interest_waived'  => $breakdown['interest_waived'],
          'total_payoff'     => $breakdown['total_payoff'],
          'currency_code'    => $currency_code,
      ]);
  }
  ```

- [ ] **Step 3: Test the endpoint manually**

  Open your browser's dev tools (F12 → Network tab). Go to a loan repayment view page. In the browser console run:

  ```javascript
  fetch('/fundit/loan/get_early_settlement_amount/YOUR_LOAN_ID?date=2026-06-01', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
  }).then(r => r.json()).then(console.log);
  ```

  Expected response:
  ```json
  {
    "status": "success",
    "overdue_amount": 15000.00,
    "future_principal": 30000.00,
    "interest_waived": 9000.00,
    "total_payoff": 45000.00,
    "currency_code": "MWK"
  }
  ```

  Verify the numbers by manually summing the relevant rows in the `payement_schedules` table for that loan.

- [ ] **Step 4: Commit**

  ```bash
  git add application/controllers/Loan.php
  git commit -m "feat: add get_early_settlement_amount() AJAX endpoint to Loan controller"
  ```

---

## Task 4: Add `process_early_settlement()` controller method

**Files:**
- Modify: `application/controllers/Loan.php` (add after `get_early_settlement_amount()`)

- [ ] **Step 1: Insert `process_early_settlement()` after `get_early_settlement_amount()`**

  ```php
  public function process_early_settlement()
  {
      $loan_id    = $this->input->post('loan_id');
      $paid_date  = $this->input->post('paid_date');
      $amount     = (float)$this->input->post('amount');
      $pay_method = $this->input->post('payment_method');

      // Guard: loan must be active
      $loan = $this->Loan_model->get_by_id($loan_id);
      if (!$loan || $loan->loan_status !== 'ACTIVE') {
          $this->toaster->error('This loan is not active.');
          redirect($_SERVER['HTTP_REFERER']);
          return;
      }

      // Guard: amount must match the calculated payoff (±0.01)
      $breakdown = $this->Payement_schedules_model->calculate_payoff_amount($loan_id, $paid_date);
      if (abs($amount - $breakdown['total_payoff']) > 0.01) {
          $this->toaster->error('Settlement amount does not match the calculated payoff of ' .
              number_format($breakdown['total_payoff'], 2) . '. Please recalculate.');
          redirect($_SERVER['HTTP_REFERER']);
          return;
      }

      // Handle proof of payment upload (same as pay_loan_r)
      $unique_name = '';
      $config = [
          'upload_path'   => './uploads/',
          'allowed_types' => 'jpg|png|jpeg|gif|pdf|docx|txt|zip',
          'max_size'      => 2048,
          'remove_spaces' => TRUE,
      ];
      $this->load->library('upload', $config);
      if (!empty($_FILES['pay_proof']['name'])) {
          $file_ext    = pathinfo($_FILES['pay_proof']['name'], PATHINFO_EXTENSION);
          $unique_name = 'file_' . time() . '_' . uniqid() . '.' . $file_ext;
          $config['file_name'] = $unique_name;
          $this->upload->initialize($config);
          $this->upload->do_upload('pay_proof');
      }

      $tid          = 'ES-' . rand(1000, 9999) . date('Ymd');
      $loan_account = get_by_id('loan', 'loan_id', $loan_id);
      $recepientt   = get_by_id('account', 'collection_account', 'Yes');

      if ($pay_method === '0') {
          // Institution's Bank Savings path
          $check = $this->Account_model->get_account($loan_account->loan_number);
          if ($check->balance < $amount) {
              $this->toaster->error('Insufficient funds in loan savings account.');
              redirect($_SERVER['HTTP_REFERER']);
              return;
          }
          $txn = $this->Account_model->transfer_funds(
              $loan_account->loan_number, $recepientt->account_number, $amount, $tid, $paid_date, $unique_name
          );
          if ($txn !== 'success') {
              $this->toaster->error('Account transfer failed.');
              redirect($_SERVER['HTTP_REFERER']);
              return;
          }
      } else {
          // Cash deposit via teller
          $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
          if (empty($get_account)) {
              $this->toaster->error('You are not authorized to do this transaction, only cashiers.');
              redirect($_SERVER['HTTP_REFERER']);
              return;
          }
          $deposit = $this->Account_model->cash_transaction(
              $get_account->account, $loan_account->loan_number, $amount, 'deposit', $tid, $paid_date, $unique_name
          );
          if (!$deposit) {
              $this->toaster->error('Cash deposit failed.');
              redirect($_SERVER['HTTP_REFERER']);
              return;
          }
          $txn = $this->Account_model->transfer_funds(
              $loan_account->loan_number, $recepientt->account_number, $amount, $tid, $paid_date, $unique_name
          );
          if ($txn !== 'success') {
              $this->toaster->error('Transfer to collection account failed.');
              redirect($_SERVER['HTTP_REFERER']);
              return;
          }
      }

      // Execute the payoff
      $this->Payement_schedules_model->payoff_loan($loan_id, $amount, $paid_date);

      $this->toaster->success('Loan settled successfully. All remaining schedules have been closed and interest waived.');
      redirect($_SERVER['HTTP_REFERER']);
  }
  ```

- [ ] **Step 2: Verify syntax**

  Load any loan repayment view page. No parse error = syntax OK.

- [ ] **Step 3: Commit**

  ```bash
  git add application/controllers/Loan.php
  git commit -m "feat: add process_early_settlement() to Loan controller"
  ```

---

## Task 5: Add Early Settlement button, modal, and JS to the view

**Files:**
- Modify: `application/views/loan/loan_repayment_view.php`

This task has three sub-steps: button, modal HTML, JS. Make all three changes before committing.

- [ ] **Step 1: Add "Early Settlement" button in the actions panel**

  In `loan_repayment_view.php`, find this block (around line 570):

  ```php
                  endif;
                  ?>
              </div>
          </div>
  ```

  Insert the Early Settlement button immediately before `endif;`:

  ```php
                  <?php if($loan_status == 'ACTIVE' && $calculation_type != 'Bullet Payment'): ?>
                  <button onclick="open_early_settlement()" class="btn-action btn-warning" style="width: 100%; justify-content: center; margin-top: 0.5rem;">
                      <i class="fa fa-flag-checkered"></i> Early Settlement
                  </button>
                  <?php endif; ?>
  ```

- [ ] **Step 2: Add the Early Settlement modal**

  Find the closing `<!-- Payoff Modal -->` block ending around line 1115. Insert the new modal **after** the `</div>` that closes the payoff modal, before the `<script>` tag:

  ```html
  <!-- Early Settlement Modal -->
  <div class="modal fade modern-modal" id="early_settlement_modal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header" style="background: #d97706;">
                  <h5 class="modal-title"><i class="fa fa-flag-checkered mr-2"></i>Early Settlement</h5>
                  <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
              </div>
              <div class="modal-body">
                  <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 1.5rem;">
                      Select a settlement date. Overdue installments are collected in full; future installments collect principal only — interest is waived.
                  </p>

                  <div class="form-group">
                      <label style="font-weight:600; color:#374151;">Settlement Date</label>
                      <input type="date" class="form-control" id="es_date" value="<?php echo date('Y-m-d'); ?>" onchange="fetchEarlySettlementAmount()">
                  </div>

                  <div id="es_breakdown" style="display:none; background:#f8fafc; border:1px solid #e5e7eb; border-radius:8px; padding:1rem; margin-bottom:1.5rem;">
                      <div style="font-weight:600; color:#1e3a5f; margin-bottom:0.75rem; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em;">Settlement Breakdown</div>
                      <div style="display:flex; justify-content:space-between; padding:0.4rem 0; border-bottom:1px solid #e5e7eb; font-size:0.9rem;">
                          <span style="color:#6b7280;">Overdue installments (principal + interest)</span>
                          <span id="es_overdue" style="font-weight:600;"><?php echo $currency->currency_code; ?> 0.00</span>
                      </div>
                      <div style="display:flex; justify-content:space-between; padding:0.4rem 0; border-bottom:1px solid #e5e7eb; font-size:0.9rem;">
                          <span style="color:#6b7280;">Remaining future principal (interest waived)</span>
                          <span id="es_future" style="font-weight:600;"><?php echo $currency->currency_code; ?> 0.00</span>
                      </div>
                      <div style="display:flex; justify-content:space-between; padding:0.4rem 0; border-bottom:1px solid #e5e7eb; font-size:0.9rem;">
                          <span style="color:#6b7280;">Interest being waived</span>
                          <span id="es_waived" style="font-weight:600; color:#059669;"><?php echo $currency->currency_code; ?> 0.00</span>
                      </div>
                      <div style="display:flex; justify-content:space-between; padding:0.6rem 0; font-size:1rem;">
                          <span style="font-weight:700; color:#1e3a5f;">Total to pay for full settlement</span>
                          <span id="es_total" style="font-weight:700; color:#d97706; font-size:1.1rem;"><?php echo $currency->currency_code; ?> 0.00</span>
                      </div>
                  </div>

                  <form action="<?php echo base_url('loan/process_early_settlement'); ?>" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
                      <input type="hidden" name="paid_date" id="es_hidden_date" value="<?php echo date('Y-m-d'); ?>">

                      <div class="form-group">
                          <label style="font-weight:600; color:#374151;">Settlement Amount (<?php echo $currency->currency_code; ?>)</label>
                          <input type="text" class="form-control" name="amount" id="es_amount" placeholder="Enter settlement amount" required>
                      </div>

                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label style="font-weight:600; color:#374151;">Payment Method</label>
                                  <?php $methods_es = get_all('payment_method'); ?>
                                  <select name="payment_method" class="form-control" required>
                                      <option value="">-- Select --</option>
                                      <option value="0">Institution's Bank Savings</option>
                                      <?php foreach ($methods_es as $method): ?>
                                      <option value="<?php echo $method->payment_method; ?>"><?php echo $method->payment_method_name; ?></option>
                                      <?php endforeach; ?>
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label style="font-weight:600; color:#374151;">Reference Number</label>
                                  <input type="text" class="form-control" name="reference" required>
                              </div>
                          </div>
                      </div>

                      <div class="form-group">
                          <label style="font-weight:600; color:#374151;">Proof of Payment</label>
                          <input type="file" class="form-control-file" name="pay_proof" style="border:1px solid #ced4da; padding:8px; border-radius:4px; width:100%; background:#fff;">
                      </div>

                      <button type="submit" class="btn-action btn-warning" style="width:100%; justify-content:center; padding:0.75rem;">
                          <i class="fa fa-check"></i> Complete Early Settlement
                      </button>
                  </form>
              </div>
          </div>
      </div>
  </div>
  ```

- [ ] **Step 3: Add JS functions**

  Inside the existing `<script>` block (after the `get_transaction_usage` function), add:

  ```javascript
  function open_early_settlement() {
      $('#early_settlement_modal').modal('show');
      fetchEarlySettlementAmount();
  }

  function fetchEarlySettlementAmount() {
      var date = document.getElementById('es_date').value;
      if (!date) return;
      document.getElementById('es_hidden_date').value = date;

      $.ajax({
          url: '<?php echo base_url("loan/get_early_settlement_amount/"); ?><?php echo $loan_id; ?>',
          type: 'GET',
          data: { date: date },
          dataType: 'json',
          success: function(r) {
              if (r.status !== 'success') return;
              var fmt = function(v) {
                  return parseFloat(v).toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
              };
              var cc = r.currency_code + ' ';
              document.getElementById('es_overdue').innerText = cc + fmt(r.overdue_amount);
              document.getElementById('es_future').innerText  = cc + fmt(r.future_principal);
              document.getElementById('es_waived').innerText  = cc + fmt(r.interest_waived);
              document.getElementById('es_total').innerText   = cc + fmt(r.total_payoff);
              document.getElementById('es_amount').value      = parseFloat(r.total_payoff).toFixed(2);
              document.getElementById('es_breakdown').style.display = 'block';
          }
      });
  }
  ```

- [ ] **Step 4: Visual check**

  Reload an active non-bullet loan repayment view page in the browser:
  - Verify "Early Settlement" button appears in the actions panel (left column)
  - Click it — the modal opens
  - Change the date — the breakdown panel appears with calculated figures
  - Verify the amounts update when the date changes
  - Verify the Settlement Amount field auto-fills with `total_payoff`
  - Verify the "Early Settlement" button does NOT appear on bullet loan repayment pages

- [ ] **Step 5: Commit**

  ```bash
  git add application/views/loan/loan_repayment_view.php
  git commit -m "feat: add early settlement modal and AJAX breakdown to loan_repayment_view"
  ```

---

## Task 6: End-to-end functional test

No automated test suite exists in this project. Follow these manual steps to verify the full flow.

- [ ] **Step 1: Set up a test loan**

  In the database, find or create an active non-bullet loan with at least 4 unpaid schedules where payment #1 and #2 are PAID, #3 is overdue, and #4+ are future. Note the `loan_id`.

- [ ] **Step 2: Open the repayment view**

  Navigate to `http://localhost/fundit/loan/repayment_view/{loan_id}`.

  Verify:
  - "Early Settlement" button is visible in the left actions panel
  - Existing payment buttons still work as before

- [ ] **Step 3: Test the AJAX calculation**

  Click "Early Settlement". The modal opens with today as the default date. Verify the breakdown shows:
  - Overdue installments = SUM of (amount - paid_amount) for all NOT PAID schedules with due date ≤ today
  - Future principal = SUM of principal for all NOT PAID schedules with due date > today
  - Interest waived = SUM of interest for all NOT PAID schedules with due date > today
  - Total = overdue + future principal

  Cross-check by running directly in MySQL:
  ```sql
  SELECT
    SUM(IF(payment_schedule <= CURDATE(), amount - paid_amount, 0)) AS overdue_amount,
    SUM(IF(payment_schedule > CURDATE(), principal, 0))             AS future_principal,
    SUM(IF(payment_schedule > CURDATE(), interest, 0))              AS interest_waived
  FROM payement_schedules
  WHERE loan_id = {loan_id} AND status != 'PAID';
  ```

- [ ] **Step 4: Change the date and verify recalculation**

  Change the settlement date to a past date (before any overdue schedules). Verify:
  - Overdue amount = 0
  - Future principal = SUM of all remaining principals
  - Amount field updates accordingly

- [ ] **Step 5: Submit the settlement**

  Enter the displayed total payoff amount, select "Cash Deposit" payment method, enter a reference number, and click "Complete Early Settlement".

  Verify after redirect:
  - Success toast message appears: "Loan settled successfully..."
  - Loan status shows CLOSED
  - In `payement_schedules` table: all previously NOT PAID rows now show `status = 'PAID'`
  - Future schedule rows have `interest = 0` and `paid_amount = principal`
  - Overdue schedule rows have `paid_amount = amount` (full schedule amount)
  - Activity log entry exists with "Early payoff settlement" and the interest waived amount

- [ ] **Step 6: Verify mismatched amount is rejected**

  Open a second active loan. Open Early Settlement modal. Manually change the amount field to a different value (e.g., subtract 500). Submit. Verify an error toast appears: "Settlement amount does not match the calculated payoff..."

- [ ] **Step 7: Verify bullet loans are unaffected**

  Open a bullet loan repayment view. Verify no "Early Settlement" button appears.

- [ ] **Step 8: Final commit (if any cleanup)**

  ```bash
  git add -p
  git commit -m "test: verify early settlement end-to-end flow"
  ```
