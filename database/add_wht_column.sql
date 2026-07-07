-- Add WHT (Withholding Tax) tracking column to fd_transactions
-- WHT @ 15% is deducted from all interest payments

ALTER TABLE fd_transactions ADD COLUMN wht_amount DECIMAL(18,2) DEFAULT 0.00 AFTER penalty_amount;
