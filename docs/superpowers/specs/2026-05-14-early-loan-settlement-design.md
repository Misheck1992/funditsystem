# Early Loan Settlement (Payoff) Feature

**Date:** 2026-05-14
**Status:** Approved

## Summary

When a borrower wants to settle a loan before its natural end date, the cashier enters the payoff date on the existing payment form. The system automatically calculates the total settlement amount for that date and displays a breakdown. If the cashier enters exactly that amount, the loan is automatically closed — overdue installments are collected in full, future installments have their interest waived and only principal recorded as collected.

No checkbox. No separate screen. No approval workflow. Detection is amount + date driven.

---

## Scenario

6-month loan. Payments 1 & 2 made. Cashier enters payment date = month 4.

| # | Due Date | Status | Payoff Treatment |
|---|----------|--------|-----------------|
| 1 | Month 1 | PAID   | Already done — untouched |
| 2 | Month 2 | PAID   | Already done — untouched |
| 3 | Month 3 | NOT PAID | Overdue → collect principal + interest + charges in full |
| 4 | Month 4 | NOT PAID | Current → collect principal + interest + charges in full |
| 5 | Month 5 | NOT PAID | Future → collect principal only, interest zeroed (waived) |
| 6 | Month 6 | NOT PAID | Future → collect principal only, interest zeroed (waived) |

---

## Payoff Amount Formula

```
overdue_amount   = SUM(amount - paid_amount)  WHERE payment_schedule <= payoff_date AND status != 'PAID'
future_principal = SUM(principal)             WHERE payment_schedule >  payoff_date AND status != 'PAID'
interest_waived  = SUM(interest)              WHERE payment_schedule >  payoff_date AND status != 'PAID'

total_payoff = overdue_amount + future_principal
```

`amount` in `payement_schedules` already contains principal + interest + any charges. Using `amount - paid_amount` for overdue rows correctly handles schedules that already have a partial payment recorded against them.

---

## UI Changes — Payment Form

When the cashier changes the **payment date** field on the existing payment form, an AJAX call fires to `Loan::get_payoff_amount()`. A callout panel renders below the amount field:

```
Early Settlement Amount for [date]:
  Overdue installments (principal + interest):   MWK 45,000
  Remaining future principal (interest waived):  MWK 30,000
  Interest being waived:                         MWK  8,000
  ─────────────────────────────────────────────────────────
  Total to pay for full settlement:              MWK 75,000
```

- Panel is **informational only** — cashier still types the amount manually
- If payoff amount = 0 (loan already closed) the panel is hidden

---

## Detection & Processing

### In `Loan::pay_loan()` (controller)

After receiving POSTed `amount` and `date`, before calling `new_pay()`:

```
1. Call calculate_payoff_amount(loan_id, date)
2. If abs(amount_entered - total_payoff) <= 0.01:
      → PAYOFF PATH: call Payement_schedules_model::payoff_loan(loan_id, amount, date)
   Else:
      → NORMAL PATH: existing new_pay() flow — unchanged
```

### `Payement_schedules_model::payoff_loan($loan_id, $amount, $date)`

```
For each overdue schedule (payment_schedule <= date, status != 'PAID'):
    status      = 'PAID'
    paid_amount = amount  ← set to full schedule amount (covers any prior partial payment)
    paid_date   = date
    partial_paid = 'NO'

For each future schedule (payment_schedule > date, status != 'PAID'):
    status      = 'PAID'
    interest    = 0          ← waived
    paid_amount = principal  ← only principal recorded as collected
    paid_date   = date
    partial_paid = 'NO'

Record one transaction row (type = 3) for the total payoff amount

Update loan table:
    loan_status = 'CLOSED'
    paid_off    = 'Yes'

Log to activity_logger:
    "Early payoff settlement — [currency] [interest_waived] interest waived"
```

`new_pay()` and `check_and_close_loan_if_paid()` are **not modified** — payoff path bypasses them entirely.

---

## New Components

| Component | Type | Location | Purpose |
|-----------|------|----------|---------|
| `calculate_payoff_amount($loan_id, $date)` | Model method | `Payement_schedules_model` | Returns overdue_amount, future_principal, interest_waived, total_payoff |
| `payoff_loan($loan_id, $amount, $date)` | Model method | `Payement_schedules_model` | Executes full payoff: marks schedules, closes loan, logs |
| `get_payoff_amount()` | Controller method | `Loan.php` | AJAX endpoint — calls calculate_payoff_amount, returns JSON |
| Payoff callout panel | View change | Existing payment view | Renders breakdown on date change via AJAX |
| Payoff detection branch | Controller change | `Loan::pay_loan()` | Routes to payoff_loan() when amount matches total_payoff |

---

## Edge Cases

| Case | Behaviour |
|------|-----------|
| All remaining schedules are overdue, none future | `future_principal = 0`; payoff = overdue total only; closure proceeds normally with nothing to zero out |
| Only the last installment remains | Same as above |
| Loan already CLOSED | `get_payoff_amount()` returns zero; panel hidden; `pay_loan()` rejects submission |
| Amount entered slightly off (rounding) | Tolerance of ±0.01 in detection check |
| Payoff date is before all remaining due dates | `overdue_amount = 0`; payoff = all remaining principal only; full interest waived |

---

## What Is NOT Changed

- `new_pay()` — untouched
- `check_and_close_loan_if_paid()` — untouched
- Existing close_loan approval workflow — untouched
- Write-off workflow — untouched
- Any corporate loan payment flows — untouched
