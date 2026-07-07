-- Add chiefta and district columns to individual_customers table
-- Run this script if the columns don't already exist

ALTER TABLE `individual_customers`
ADD COLUMN `chiefta` VARCHAR(100) NULL AFTER `village`;

ALTER TABLE `individual_customers`
ADD COLUMN `district` VARCHAR(100) NULL AFTER `City`;

-- Update any NULL Country values to 'Zambia' as default
UPDATE `individual_customers` SET `Country` = 'Zambia' WHERE `Country` IS NULL OR `Country` = '';
