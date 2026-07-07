-- SQL script to add sent_back columns to loan table
-- Run this script to enable the "Send Back" functionality

-- Add sent_back flag
ALTER TABLE `loan` ADD COLUMN `sent_back` TINYINT(1) DEFAULT 0 AFTER `loan_status`;

-- Add sent_back comment
ALTER TABLE `loan` ADD COLUMN `sent_back_comment` TEXT NULL AFTER `sent_back`;

-- Add sent_back by user ID
ALTER TABLE `loan` ADD COLUMN `sent_back_by` INT NULL AFTER `sent_back_comment`;

-- Add sent_back date
ALTER TABLE `loan` ADD COLUMN `sent_back_date` DATETIME NULL AFTER `sent_back_by`;

-- Add from_status and to_status columns to loan_approval_trail if they don't exist
ALTER TABLE `loan_approval_trail` ADD COLUMN `from_status` VARCHAR(50) NULL AFTER `comment`;
ALTER TABLE `loan_approval_trail` ADD COLUMN `to_status` VARCHAR(50) NULL AFTER `from_status`;
