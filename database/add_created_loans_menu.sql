-- Add "Created Loans" menu item under the Loans parent menu
-- This allows admin users to see API-created loans that need completion

INSERT INTO menuitems (mid, label, method, fa_icon, sortt, active, show_menu)
VALUES (
    (SELECT id FROM menu WHERE label LIKE '%Loan%' AND type = 'parent' LIMIT 1),
    'Created Loans',
    'Loan/created_loans',
    'fa fa-plus-square',
    5,
    1,
    'Yes'
);
