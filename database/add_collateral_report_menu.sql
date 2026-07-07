-- Add Collateral Report menu item to the Reports menu
-- Run this script in phpMyAdmin or MySQL command line

-- First check the Reports menu ID:
-- SELECT id, label FROM menu WHERE label LIKE '%Report%';

-- Check existing menu items for Reports:
-- SELECT * FROM menuitems WHERE mid = (SELECT id FROM menu WHERE label LIKE '%Report%' LIMIT 1);

-- Insert Collateral Report menu item (adjust mid value if needed - usually 7 for Reports)
-- You can also run: http://localhost/fundit/Admin/setup_collateral_report_menu
INSERT INTO menuitems (mid, label, method, fa_icon, sortt, active, show_menu)
VALUES (
    (SELECT id FROM menu WHERE label LIKE '%Report%' LIMIT 1),
    'Collateral Report',
    'Reports/collateral_report',
    'fa fa-shield',
    99,
    1,
    'Yes'
);

-- After adding, grant access to users who need it via the User Access management
