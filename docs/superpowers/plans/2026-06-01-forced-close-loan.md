# Forced Close Loan Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a "Forced Close Loan" button to the loan repayment view that lets any user immediately close a loan by paying any amount >= the original principal balance, with a mandatory reason comment.

**Architecture:** A new `force_close_loan()` POST method in `Loan.php` handles all backend logic (validation → fund transfer → settle schedules → close loan → log). The view adds a button and a dedicated modal with inline JS. No new model methods or DB tables are needed — all required model methods already exist.

**Tech Stack:** CodeIgniter 3, PHP, Bootstrap 3 modals, jQuery AJAX (not used here — standard form POST), MySQL.

---

## File Map

| File | Change |
|---|---|
| `application/controllers/Loan.php` | Add `force_close_loan()` public method at end of controller |
| `application/views/loan/loan_repayment_view.php` | Add button in Actions Panel; add `force_close_modal` HTML; add `open_force_close_modal()` JS |

---

## Task 1: Add `force_close_loan()` Controller Method

**Files:**
- Modify: `application/controllers/Loan.php` (append near other pay methods, e.g. after `pay_off_loan()` around line 7787)

- [ ] **Step 1: Locate insertion point**

Open `application/controllers/Loan.php`. Find the closing brace of `pay_off_loan()` (search for `'loan/repayment_view/' . $loan_id` near line 7786). Insert the new method immediately after the closing `}` of `pay_off_loan()`.

- [ ] **Step 2: Insert the method**

```php
public function force_close_loan()
{
    $loan_id        = $this->input->post('loan_id');
    $amount         = (float) $this->input->post('amount');
    $reason         = trim($this->input->post('reason'));
    $paid_date      = $this->input->post('paid_date');
    $payment_method = $this->input->post('payment_method');
    $reference      = $this->input->post('reference');

    // Handle optional proof-of-payment upload
    $unique_name = "";
    if (!empty($_FILES['pay_proof']['name'])) {
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'jpg|png|jpeg|gif|pdf|docx|txt|zip';
        $config['max_size']      = 2048;
        $config['remove_spaces'] = TRUE;
        $this->load->library('upload', $config);
        $file_ext    = pathinfo($_FILES['pay_proof']['name'], PATHINFO_EXTENSION);
        $unique_name = 'file_' . time() . '_' . uniqid() . '.' . $file_ext;
        $config['file_name'] = $unique_name;
        $this->upload->initialize($config);
        $this->upload->do_upload('pay_proof');
    }

    // Load and validate loan
    $loan = $this->Loan_model->get_by_id($loan_id);
    if (!$loan) {
        $this->toaster->error('Error: Loan not found.');
        redirect($_SERVER['HTTP_REFERER']);
        return;
    }
    if ($loan->loan_status === 'CLOSED' || $loan->loan_status === 'WRITTEN_OFF') {
        $this->toaster->error('Error: This loan is already ' . $loan->loan_status . '.');
        redirect($_SERVER['HTTP_REFERER']);
        return;
    }

    // Amount must be at least the original principal
    if ($amount < (float) $loan->loan_principal) {
        $this->toaster->error('Error: Amount must be at least the principal balance (' . number_format($loan->loan_principal, 2) . ').');
        redirect($_SERVER['HTTP_REFERER']);
        return;
    }

    // Reason is mandatory
    if (empty($reason)) {
        $this->toaster->error('Error: A reason for forced closure is required.');
        redirect($_SERVER['HTTP_REFERER']);
        return;
    }

    // Get collection account
    $recepientt = get_by_id('account', 'collection_account', 'Yes');
    if (!$recepientt) {
        $this->toaster->error('Error: Collection account not configured.');
        redirect($_SERVER['HTTP_REFERER']);
        return;
    }

    $tid = "FCL-" . rand(1000, 9999) . date('Ymd');

    // Process fund movement
    if ($payment_method == "0") {
        // From loan savings account
        $check = $this->Account_model->get_account($loan->loan_number);
        if (!$check || $check->balance < $amount) {
            $this->toaster->error('Error: Insufficient funds in loan account.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }
        $transfer = $this->Account_model->transfer_funds(
            $loan->loan_number, $recepientt->account_number,
            $amount, $tid, $paid_date, $unique_name
        );
        if ($transfer != 'success') {
            $this->toaster->error('Error: Fund transfer failed.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }
    } else {
        // Cash via teller
        $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
        if (empty($get_account)) {
            $this->toaster->error('Error: Only cashiers can process this payment.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }
        $deposit = $this->Account_model->cash_transaction(
            $get_account->account, $loan->loan_number,
            $amount, 'deposit', $tid, $paid_date, $unique_name
        );
        if (!$deposit) {
            $this->toaster->error('Error: Deposit to loan account failed.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }
        $transfer = $this->Account_model->transfer_funds(
            $loan->loan_number, $recepientt->account_number,
            $amount, $tid, $paid_date, $unique_name
        );
        if ($transfer != 'success') {
            $this->toaster->error('Error: Fund transfer failed.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }
    }

    // Mark all unpaid/partial schedules as force-settled
    // paid_amount=0 because the forced amount is not allocated per-instalment;
    // the view sums $pp->amount (not paid_amount) for PAID rows, so balance shows 0 correctly.
    $unpaid = $this->Payement_schedules_model->get_unpaid_schedules($loan_id);
    foreach ($unpaid as $schedule) {
        $this->Payement_schedules_model->update($schedule->id, array(
            'status'      => 'PAID',
            'paid_amount' => 0,
            'paid_date'   => $paid_date,
        ));
    }

    // Close the loan
    $this->Loan_model->update($loan_id, array(
        'loan_status'   => 'CLOSED',
        'paid_off'      => 'Yes',
        'closed_date'   => $paid_date,
        'closed_by'     => $this->session->userdata('user_id'),
        'closing_notes' => 'FORCED CLOSE: ' . $reason,
    ));

    // Record transaction (type 5 = forced close, distinct from type 4 = normal payoff)
    $this->Transactions_model->insert(array(
        'ref'              => $tid,
        'loan_id'          => $loan_id,
        'amount'           => $amount,
        'transaction_type' => 5,
        'payment_number'   => 0,
        'date_stamp'       => $paid_date,
        'method'           => $payment_method,
        'payment_proof'    => $unique_name,
        'reference'        => $reference,
        'added_by'         => $this->session->userdata('user_id'),
    ));

    // Audit log with full detail including the reason
    log_activity(array(
        'user_id'       => $this->session->userdata('user_id'),
        'activity'      => 'Forced close loan ID: ' . $loan_id . ' (Loan #: ' . $loan->loan_number . '), amount: ' . $amount . ', reason: ' . $reason,
        'activity_cate' => 'force_close_loan',
    ));

    $this->toaster->success('Loan has been forcibly closed.');
    redirect('loan/repayment_view/' . $loan_id);
}
```

- [ ] **Step 3: Verify PHP syntax**

```bash
php -l application/controllers/Loan.php
```
Expected output: `No syntax errors detected in application/controllers/Loan.php`

- [ ] **Step 4: Commit backend**

```bash
git add application/controllers/Loan.php
git commit -m "feat: add force_close_loan controller method"
```

---

## Task 2: Add Button to Actions Panel

**Files:**
- Modify: `application/views/loan/loan_repayment_view.php` (Actions Panel, lines ~519–562)

- [ ] **Step 1: Locate insertion point**

In `loan_repayment_view.php`, find the closing `endif;` of the outer `if($loan_status != 'CLOSED' && $loan_status != 'WRITTEN_OFF')` block (around line 560). It looks like:

```php
        }
    endif;
    ?>
</div>
```

- [ ] **Step 2: Insert the Forced Close button just before that `endif;`**

Replace the existing closing block:

```php
        }
    endif;
    ?>
```

with:

```php
        }

        // Forced Close — available whenever loan is active (regardless of next payment state)
        ?>
        <button onclick="open_force_close_modal()" class="btn-action btn-danger" style="width: 100%; justify-content: center; margin-top: 0.5rem;">
            <i class="fa fa-lock"></i> Forced Close Loan
        </button>
        <?php
    endif;
    ?>
```

- [ ] **Step 3: Verify the button appears in the browser**

Open the repayment view for any ACTIVE loan. The Actions Panel should now show "Forced Close Loan" button in red below the Make Payment button. Click it — nothing happens yet (no modal attached).

---

## Task 3: Add Forced Close Modal HTML

**Files:**
- Modify: `application/views/loan/loan_repayment_view.php` (after the last existing modal, around line 1015)

- [ ] **Step 1: Locate insertion point**

Find the closing `</div>` of the `breakdown_usage` modal (the last modal in the file, ends around line 1015). Insert after it.

- [ ] **Step 2: Add the modal markup**

```php
<!-- Forced Close Loan Modal -->
<div class="modal fade modern-modal" id="force_close_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #7f1d1d;">
                <h5 class="modal-title"><i class="fa fa-lock mr-2"></i>Forced Close Loan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 0.75rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #991b1b; font-size: 0.9rem;">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>This will immediately close the loan. All remaining scheduled payments will be marked as settled.</strong>
                    </div>
                </div>

                <div class="info-grid mb-4">
                    <div class="info-item">
                        <div class="label">Loan Number</div>
                        <div class="value"><?php echo $loan_number; ?></div>
                    </div>
                    <div class="info-item" style="border-left-color: #dc2626;">
                        <div class="label">Minimum Amount (Principal Balance)</div>
                        <div class="value" style="color: #dc2626;"><?php echo $currency->currency_code; ?> <?php echo number_format($loan_principal, 2); ?></div>
                    </div>
                </div>

                <form action="<?php echo base_url('loan/force_close_loan'); ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">

                    <div class="form-group">
                        <label>Amount Paid (<?php echo $currency->currency_code; ?>) &mdash; minimum: <?php echo number_format($loan_principal, 2); ?></label>
                        <input type="number" step="0.01" min="<?php echo $loan_principal; ?>" class="form-control" name="amount" value="<?php echo $loan_principal; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Reason for Forced Closure <span style="color: #dc2626;">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Enter reason why this loan is being forcibly closed..." required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Method</label>
                                <?php $methods_fc = get_all('payment_method'); ?>
                                <select name="payment_method" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="0">Institution's Bank Savings</option>
                                    <?php foreach ($methods_fc as $method_fc): ?>
                                    <option value="<?php echo $method_fc->payment_method; ?>"><?php echo $method_fc->payment_method_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reference Number</label>
                                <input type="text" class="form-control" name="reference" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Payment Date</label>
                        <input type="date" class="form-control" name="paid_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Proof of Payment <span style="color: #6b7280; font-weight: normal;">(optional)</span></label>
                        <input type="file" class="form-control-file" name="pay_proof" style="border: 1px solid #ced4da; padding: 8px; border-radius: 4px; width: 100%; background: #fff;">
                    </div>

                    <button type="submit" class="btn-action btn-danger" style="width: 100%; justify-content: center; padding: 0.75rem;"
                        onclick="return confirm('Are you sure you want to forcibly close this loan? This cannot be undone.')">
                        <i class="fa fa-lock"></i> Confirm Forced Close
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
```

- [ ] **Step 3: Add JS function**

Inside the existing `<script>` block at the bottom of `loan_repayment_view.php` (before the closing `</script>`), add:

```js
function open_force_close_modal() {
    $('#force_close_modal').modal('show');
}
```

- [ ] **Step 4: Verify PHP syntax**

```bash
php -l application/views/loan/loan_repayment_view.php
```
Expected: `No syntax errors detected`

- [ ] **Step 5: Commit view changes**

```bash
git add application/views/loan/loan_repayment_view.php
git commit -m "feat: add forced close loan button, modal, and JS to repayment view"
```

---

## Task 4: Manual End-to-End Testing

- [ ] **Test 1 — Modal opens correctly**

Navigate to `loan/repayment_view/<id>` for an ACTIVE loan. Click "Forced Close Loan". Verify:
- Dark red modal header appears
- Warning banner visible
- Amount field pre-filled with principal balance
- Min attribute prevents submitting below principal

- [ ] **Test 2 — Validation: amount below principal**

In the modal, clear the amount field and enter a value less than the principal (e.g., principal is 5000, enter 4000). Submit. Verify server returns error toast: "Amount must be at least the principal balance (5,000.00)".

- [ ] **Test 3 — Validation: empty reason**

Enter a valid amount but leave Reason blank. The `required` attribute on the textarea should prevent submission. If somehow it bypasses (JS disabled), verify server returns error toast: "A reason for forced closure is required."

- [ ] **Test 4 — Successful forced close**

Fill all fields with valid data (amount >= principal, reason text, payment method, reference, today's date). Submit. Verify:
- Redirected back to `loan/repayment_view/<id>`
- Success toast: "Loan has been forcibly closed."
- Loan status badge now shows "CLOSED"
- "Make Payment" button gone
- "Forced Close Loan" button gone
- All payment schedule rows show green "PAID" badges
- Remaining Balance summary card shows 0.00
- Deposit/Payment History table has the new forced-close transaction

- [ ] **Test 5 — Already closed loan**

Try navigating back and submitting the force-close form again (e.g., using browser back + resubmit). Verify server returns error toast: "This loan is already CLOSED."

- [ ] **Test 6 — Activity log**

Check the activity log (admin panel or DB: `SELECT * FROM activity_log WHERE activity_cate = 'force_close_loan' ORDER BY id DESC LIMIT 1`). Verify the entry contains loan ID, amount, and the reason text.

- [ ] **Final commit**

```bash
git add .
git commit -m "feat: forced close loan — complete implementation and manual testing"
```
