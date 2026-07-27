-- Adds principal_paid to payement_schedules.
-- Tracks how much of a schedule's PRINCIPAL portion has been settled
-- (including surplus/overpayment prepaid onto future schedules), so the
-- customer statement can show principal reducing while interest stays per
-- contract. Additive and backward-compatible: total owed per schedule is
-- still (principal + accrued_interest - paid_amount); principal_paid only
-- drives the principal-vs-interest attribution shown to the customer.
ALTER TABLE `payement_schedules`
    ADD COLUMN `principal_paid` DECIMAL(18,2) NOT NULL DEFAULT 0.00 AFTER `paid_amount`;

-- Backfill for schedules that were already paid before this column existed.
-- Without this every historic payment looks like it settled 0 principal, so
-- dashboard outstanding balances and customer statements overstate principal.
-- Mirrors the runtime allocation rule: interest is settled first, remainder
-- reduces principal. Uses the contractual `interest` column as the accrued
-- figure, which matches non-bullet schedules exactly and is a close
-- approximation for bullet loans.
UPDATE `payement_schedules`
SET `principal_paid` = CASE
        WHEN `status` = 'PAID' THEN `principal`
        ELSE LEAST(`principal`, GREATEST(`paid_amount` - `interest`, 0))
    END
WHERE `paid_amount` > 0;
