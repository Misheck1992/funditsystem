-- Add columns for tracking client signing in loan workflow
-- Run this script to add the new columns

ALTER TABLE `loan`
ADD COLUMN `client_signed_by` INT NULL AFTER `loan_approved_by`,
ADD COLUMN `client_signed_date` DATETIME NULL AFTER `client_signed_by`;

-- Note: The loan_status will now support a new value 'CLIENT_SIGNED'
-- Workflow: INITIATED -> RECOMMENDED -> APPROVED_FIRST -> APPROVED_SECOND -> APPROVED -> CLIENT_SIGNED -> ACTIVE
