-- Fix Fixed Deposits menu visibility
-- Run this if the FD menu was already inserted with wrong values

-- Fix the menu entry: set menu_type_id=1 and active=1
UPDATE `menu` SET `menu_type_id` = 1, `active` = 1 WHERE `label` = 'Fixed Deposits';

-- Grant access to all roles for FD menu items
INSERT INTO `access` (`roleid`, `controllerid`)
SELECT r.id, mi.id
FROM `roles` r
CROSS JOIN `menuitems` mi
JOIN `menu` m ON mi.mid = m.id
WHERE m.label = 'Fixed Deposits'
AND NOT EXISTS (
    SELECT 1 FROM `access` a WHERE a.roleid = r.id AND a.controllerid = mi.id
);

-- Verify the fix
SELECT m.id, m.label, m.menu_type_id, m.active FROM `menu` m WHERE m.label = 'Fixed Deposits';
SELECT mi.id, mi.label, mi.method FROM `menuitems` mi JOIN `menu` m ON mi.mid = m.id WHERE m.label = 'Fixed Deposits';
