-- SQL script to add extended fields to fd_customers table
-- Run this script to add new columns for enhanced customer information

-- Personal Information
ALTER TABLE `fd_customers` ADD COLUMN `date_of_birth` date NULL AFTER `gender`;
ALTER TABLE `fd_customers` ADD COLUMN `district` varchar(100) NULL AFTER `province`;
ALTER TABLE `fd_customers` ADD COLUMN `city` varchar(100) NULL AFTER `district`;

-- Contact Information
ALTER TABLE `fd_customers` ADD COLUMN `alt_phone_number` varchar(20) NULL AFTER `phone_number`;

-- Employment / Source of Funds
ALTER TABLE `fd_customers` ADD COLUMN `occupation` varchar(100) NULL AFTER `alt_phone_number`;
ALTER TABLE `fd_customers` ADD COLUMN `employer` varchar(150) NULL AFTER `occupation`;
ALTER TABLE `fd_customers` ADD COLUMN `source_of_funds` varchar(200) NULL AFTER `employer`;

-- Identification Documents
ALTER TABLE `fd_customers` ADD COLUMN `id_expiry_date` date NULL AFTER `id_number`;
ALTER TABLE `fd_customers` ADD COLUMN `nrc_photo` varchar(255) NULL AFTER `id_expiry_date`;
ALTER TABLE `fd_customers` ADD COLUMN `proof_of_income` varchar(255) NULL AFTER `nrc_photo`;

-- Next of Kin Details
ALTER TABLE `fd_customers` ADD COLUMN `nok_name` varchar(150) NULL AFTER `proof_of_income`;
ALTER TABLE `fd_customers` ADD COLUMN `nok_relationship` varchar(50) NULL AFTER `nok_name`;
ALTER TABLE `fd_customers` ADD COLUMN `nok_phone` varchar(20) NULL AFTER `nok_relationship`;
ALTER TABLE `fd_customers` ADD COLUMN `nok_address` text NULL AFTER `nok_phone`;
ALTER TABLE `fd_customers` ADD COLUMN `nok_id_number` varchar(50) NULL AFTER `nok_address`;

-- Bank Details (for payouts)
ALTER TABLE `fd_customers` ADD COLUMN `bank_name` varchar(100) NULL AFTER `nok_id_number`;
ALTER TABLE `fd_customers` ADD COLUMN `bank_account_number` varchar(50) NULL AFTER `bank_name`;
ALTER TABLE `fd_customers` ADD COLUMN `bank_branch` varchar(100) NULL AFTER `bank_account_number`;

-- Add currency column to fd_deposits table
ALTER TABLE `fd_deposits` ADD COLUMN `currency` varchar(10) DEFAULT 'ZMW' AFTER `customer_id`;
