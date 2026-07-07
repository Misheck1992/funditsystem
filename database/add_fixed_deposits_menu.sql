-- Fixed Deposits Menu Items
-- Run this SQL to add the Fixed Deposits menu to the system

-- =====================================================
-- OPTION A: Fresh install (menu not yet added)
-- =====================================================

-- Insert the main menu item (menu_type_id=1, active=1 to match display_menu_admin query)
INSERT INTO `menu` (`label`, `icon`, `parent`, `sort`, `active`, `roll`, `type`, `link`, `menu_type_id`)
VALUES ('Fixed Deposits', 'fas fa-piggy-bank', 0, 50, 1, NULL, 'dropdown', NULL, 1);

-- Get the ID of the newly inserted menu item
SET @parent_id = LAST_INSERT_ID();

-- Insert sub-menu items into menuitems table
INSERT INTO `menuitems` (`mid`, `method`, `label`, `sortt`, `show_menu`) VALUES
(@parent_id, 'Fixed_deposits/index', 'FD Dashboard', 1, 'Yes'),
(@parent_id, 'Fixed_deposits/customers', 'FD Customers', 2, 'Yes'),
(@parent_id, 'Fixed_deposits/deposits', 'All Deposits', 3, 'Yes'),
(@parent_id, 'Fixed_deposits/deposit_create', 'New Deposit', 4, 'Yes'),
(@parent_id, 'Fixed_deposits/report', 'FD Reports', 5, 'Yes');

-- =====================================================
-- Grant access to all roles (adjust role IDs as needed)
-- This inserts access entries for ALL existing roles
-- =====================================================
INSERT INTO `access` (`roleid`, `controllerid`)
SELECT r.id, mi.id
FROM `roles` r
CROSS JOIN `menuitems` mi
WHERE mi.mid = @parent_id
AND NOT EXISTS (
    SELECT 1 FROM `access` a WHERE a.roleid = r.id AND a.controllerid = mi.id
);

-- Verify
SELECT 'Fixed Deposits menu added successfully!' as message;
SELECT m.label as menu_label, mi.id as menuitem_id, mi.label as menuitem_label, mi.method
FROM menuitems mi JOIN menu m ON mi.mid = m.id WHERE mi.mid = @parent_id;


-- =====================================================
-- OPTION B: Fix existing entry (if old SQL was already run with wrong values)
-- Uncomment and run these instead if you already inserted the menu
-- =====================================================
-- UPDATE `menu` SET `menu_type_id` = 1, `active` = 1 WHERE `label` = 'Fixed Deposits';
--
-- -- Grant access to all roles for FD menu items
-- INSERT INTO `access` (`roleid`, `controllerid`)
-- SELECT r.id, mi.id
-- FROM `roles` r
-- CROSS JOIN `menuitems` mi
-- JOIN `menu` m ON mi.mid = m.id
-- WHERE m.label = 'Fixed Deposits'
-- AND NOT EXISTS (
--     SELECT 1 FROM `access` a WHERE a.roleid = r.id AND a.controllerid = mi.id
-- );
