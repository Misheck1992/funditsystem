# Forced Close Loan — Design Spec
Date: 2026-06-01

## Overview

Add a "Forced Close Loan" button to the loan repayment view. A user can pay off any amount that is at or above the original principal balance, regardless of the current date or outstanding interest. A mandatory reason/comment must be supplied. The loan closes immediately after submission — all remaining scheduled payments are marked settled and no further balance is shown.

---

## 1. UI: Button & Modal

### Button
- Location: Actions Panel in `application/views/loan/loan_repayment_view.php`, below the existing "Make Payment" button.
- Visibility: shown only when `loan_status != 'CLOSED'` and `loan_status != 'WRITTEN_OFF'`.
- Style: `btn-danger` (red) to signal this is an exceptional/destructive action.
- Label: "Forced Close Loan" with a lock/power icon.
- onclick: `open_force_close_modal(<?php echo $loan_id; ?>)`

### Modal (`force_close_modal`)
- Header: dark red background, title "Forced Close Loan".
- Warning banner inside: *"This will immediately close the loan. All remaining scheduled payments will be marked as settled."*

| Field | Type | Required | Default | Validation |
|---|---|---|---|---|
| Amount Paid | Number input | Yes | Principal balance | Must be >= principal balance |
| Reason / Comment | Textarea | Yes | — | Non-empty |
| Payment Date | Date input | Yes | Today | No date restriction |
| Payment Method | Dropdown (same as other modals) | Yes | — | Non-empty |
| Reference Number | Text input | Yes | — | Non-empty |
| Proof of Payment | File upload | No | — | jpg/png/jpeg/gif/pdf/docx/txt/zip, max 2MB |

---

## 2. Backend: Controller Method

### Route
`POST loan/force_close_loan`

### Method: `Loan::force_close_loan()`
File: `application/controllers/Loan.php`

**Input (POST):**
- `loan_id`
- `amount`
- `reason` (the mandatory comment)
- `paid_date`
- `payment_method`
- `reference`
- `pay_proof` (file, optional)

**Processing steps (in order):**

1. **Validate** — load the loan record; if `loan_status` is already `CLOSED` or `WRITTEN_OFF`, redirect back with error toast.
2. **Validate amount** — cast `amount` to float; if `amount < loan_principal`, redirect back with error toast: *"Amount must be at least the principal balance."*
3. **Validate reason** — if `reason` is empty/blank, redirect back with error toast.
4. **File upload** — same pattern as `pay_loan()`: optional proof-of-payment upload to `./uploads/`.
5. **Record transaction** — insert a credit entry into the `transaction` table against the loan's account number (same as other payment flows).
6. **Settle schedules** — update all `payment_schedules` rows where `loan_id = X` and `status = 'NOT PAID'`: set `status = 'PAID'`, `paid_amount = 0` (reflects force-settled, not individually cash-collected), `paid_date = paid_date`.
7. **Close loan** — `UPDATE loan SET loan_status = 'CLOSED', paid_off = 'Yes', next_payment_id = <last_payment_number + 1> WHERE loan_id = X`.
8. **Activity log** — log: `"Forced close loan, loan ID: X, amount: Y, reason: <reason>"` with `activity_cate = 'force_close_loan'`.
9. **Redirect** — success toast "Loan has been forcibly closed." then `redirect($_SERVER['HTTP_REFERER'])`.

---

## 3. After Closure: Balance Display

No new display logic needed. Existing view logic already handles this correctly:

- **Remaining Balance** summary card → `0.00` (all schedules are PAID, so `$total_paid` covers `$loan_amount_total`).
- **Make Payment** and **Forced Close Loan** buttons → hidden (both gated on `loan_status != 'CLOSED'`).
- **Next Payment Due** card → shows "Fully Paid" green state (no unpaid `next_payment_details`).
- **Payment schedule table** → all rows show green `PAID` badges.
- **Deposit/Payment History** → shows the one forced-close transaction entry.

---

## 4. Files Changed

| File | Change |
|---|---|
| `application/views/loan/loan_repayment_view.php` | Add "Forced Close Loan" button to Actions Panel; add `force_close_modal` markup; add `open_force_close_modal()` JS function |
| `application/controllers/Loan.php` | Add `force_close_loan()` method |
| `application/views/admin/footer.php` | No change needed (modal JS lives in the view) |

---

## 5. Out of Scope

- Approval workflow (decided: any logged-in user can use this).
- New database table for forced-close records (reason stored in activity log).
- Changes to reports or PDF output.
