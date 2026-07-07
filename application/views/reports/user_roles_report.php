<style>
/* Report Card Styles */
.report-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
    overflow: hidden;
}
.report-header {
    background: #1e3a5f;
    color: #fff;
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}
.report-header h4 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #fff !important;
}
.report-body {
    padding: 1.5rem;
}

/* Summary Stats */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
    border-radius: 10px;
    padding: 1.25rem;
    color: #fff;
    text-align: center;
}
.stat-card.green {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
}
.stat-card.amber {
    background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
}
.stat-card.purple {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
}
.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}
.stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
}

/* Role Cards */
.role-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    margin-bottom: 1rem;
    overflow: hidden;
}
.role-card-header {
    background: #f8fafc;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}
.role-card-header:hover {
    background: #f1f5f9;
}
.role-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e3a5f;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.role-badges {
    display: flex;
    gap: 0.5rem;
}
.role-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.role-badge.users {
    background: #dbeafe;
    color: #1e40af;
}
.role-badge.permissions {
    background: #dcfce7;
    color: #166534;
}
.role-card-body {
    padding: 1.25rem;
    display: none;
}
.role-card.expanded .role-card-body {
    display: block;
}
.role-card-header .toggle-icon {
    transition: transform 0.3s;
}
.role-card.expanded .toggle-icon {
    transform: rotate(180deg);
}

/* Users Table */
.users-section, .permissions-section {
    margin-bottom: 1.5rem;
}
.section-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.section-title i {
    color: #3b82f6;
}
.mini-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
}
.mini-table th {
    background: #f8fafc;
    padding: 0.6rem 0.75rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}
.mini-table td {
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid #e5e7eb;
}
.mini-table tr:hover {
    background: #f8fafc;
}
.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}
.status-badge.authorized {
    background: #dcfce7;
    color: #166534;
}
.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}
.status-badge.blocked {
    background: #fee2e2;
    color: #991b1b;
}

/* Permissions Grid */
.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}
.permission-group {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
}
.permission-group-title {
    font-weight: 600;
    color: #1e3a5f;
    margin-bottom: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
    font-size: 0.85rem;
}
.permission-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.permission-list li {
    padding: 0.35rem 0;
    font-size: 0.8rem;
    color: #4b5563;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.permission-list li i {
    color: #059669;
    font-size: 0.7rem;
}
.permission-method {
    font-size: 0.7rem;
    color: #9ca3af;
    margin-left: auto;
}

/* No data state */
.no-data {
    text-align: center;
    padding: 1.5rem;
    color: #9ca3af;
    font-style: italic;
}

/* Print styles */
@media print {
    .report-header {
        background: #1e3a5f !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .role-card-body {
        display: block !important;
    }
    .no-print {
        display: none !important;
    }
}
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">User Roles & Permissions Report</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">Reports</a>
                <span class="breadcrumb-item active">User Roles & Permissions</span>
            </nav>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $total_roles; ?></div>
            <div class="stat-label"><i class="fa fa-user-shield"></i> Total Roles</div>
        </div>
        <div class="stat-card green">
            <div class="stat-number"><?php echo $total_users; ?></div>
            <div class="stat-label"><i class="fa fa-users"></i> Total Users</div>
        </div>
        <div class="stat-card amber">
            <div class="stat-number"><?php echo $total_permissions; ?></div>
            <div class="stat-label"><i class="fa fa-key"></i> Total Menu Items</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-number"><?php echo date('d M Y'); ?></div>
            <div class="stat-label"><i class="fa fa-calendar"></i> Report Date</div>
        </div>
    </div>

    <!-- Main Report Card -->
    <div class="report-card">
        <div class="report-header">
            <h4><i class="fa fa-users-cog mr-2"></i>Roles, Users & Permissions</h4>
            <div class="no-print">
                <button onclick="expandAll()" class="btn btn-light btn-sm mr-2">
                    <i class="fa fa-expand-arrows-alt"></i> Expand All
                </button>
                <button onclick="collapseAll()" class="btn btn-light btn-sm mr-2">
                    <i class="fa fa-compress-arrows-alt"></i> Collapse All
                </button>
                <button onclick="window.print()" class="btn btn-info btn-sm">
                    <i class="fa fa-print"></i> Print Report
                </button>
            </div>
        </div>
        <div class="report-body">
            <?php if (empty($report_data)): ?>
                <div class="no-data">No roles found in the system.</div>
            <?php else: ?>
                <?php foreach ($report_data as $role): ?>
                <div class="role-card" id="role-<?php echo $role['role_id']; ?>">
                    <div class="role-card-header" onclick="toggleRole(<?php echo $role['role_id']; ?>)">
                        <div class="role-name">
                            <i class="fa fa-shield-alt" style="color: #3b82f6;"></i>
                            <?php echo htmlspecialchars($role['role_name']); ?>
                        </div>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="role-badges">
                                <span class="role-badge users">
                                    <i class="fa fa-users"></i> <?php echo $role['user_count']; ?> Users
                                </span>
                                <span class="role-badge permissions">
                                    <i class="fa fa-key"></i> <?php echo $role['permission_count']; ?> Permissions
                                </span>
                            </div>
                            <i class="fa fa-chevron-down toggle-icon" style="color: #9ca3af;"></i>
                        </div>
                    </div>
                    <div class="role-card-body">
                        <!-- Users Section -->
                        <div class="users-section">
                            <div class="section-title">
                                <i class="fa fa-users"></i> Users in this Role
                            </div>
                            <?php if (empty($role['users'])): ?>
                                <div class="no-data">No users assigned to this role.</div>
                            <?php else: ?>
                                <table class="mini-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $n = 1; foreach ($role['users'] as $user): ?>
                                        <tr>
                                            <td><?php echo $n++; ?></td>
                                            <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($user['username'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                            <td>
                                                <?php
                                                $status = strtolower($user['status'] ?? 'pending');
                                                $status_class = 'pending';
                                                if ($status == 'authorized') $status_class = 'authorized';
                                                elseif ($status == 'blocked' || $status == 'suspended') $status_class = 'blocked';
                                                ?>
                                                <span class="status-badge <?php echo $status_class; ?>">
                                                    <?php echo htmlspecialchars($user['status'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>

                        <!-- Permissions Section -->
                        <div class="permissions-section">
                            <div class="section-title">
                                <i class="fa fa-key"></i> Permissions / Menu Access
                            </div>
                            <?php if (empty($role['permissions'])): ?>
                                <div class="no-data">No permissions assigned to this role.</div>
                            <?php else: ?>
                                <div class="permissions-grid">
                                    <?php foreach ($role['permissions'] as $group_name => $perms): ?>
                                    <div class="permission-group">
                                        <div class="permission-group-title">
                                            <i class="fa fa-folder"></i> <?php echo htmlspecialchars($group_name); ?>
                                            <span style="float: right; font-weight: normal; color: #9ca3af;">(<?php echo count($perms); ?>)</span>
                                        </div>
                                        <ul class="permission-list">
                                            <?php foreach ($perms as $perm): ?>
                                            <li>
                                                <i class="fa fa-check-circle"></i>
                                                <?php echo htmlspecialchars($perm['name']); ?>
                                                <span class="permission-method"><?php echo htmlspecialchars($perm['method']); ?></span>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Aggregated Users Table -->
    <div class="report-card">
        <div class="report-header">
            <h4><i class="fa fa-table mr-2"></i>All Users Summary</h4>
        </div>
        <div class="report-body">
            <table class="table" id="users_summary_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $n = 1;
                    foreach ($report_data as $role):
                        foreach ($role['users'] as $user):
                    ?>
                    <tr>
                        <td><?php echo $n++; ?></td>
                        <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['username'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                        <td>
                            <span style="background: #e0e7ff; color: #3730a3; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                <?php echo htmlspecialchars($role['role_name']); ?>
                            </span>
                        </td>
                        <td>
                            <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                <?php echo $role['permission_count']; ?> permissions
                            </span>
                        </td>
                        <td>
                            <?php
                            $status = strtolower($user['status'] ?? 'pending');
                            $status_class = 'pending';
                            if ($status == 'authorized') $status_class = 'authorized';
                            elseif ($status == 'blocked' || $status == 'suspended') $status_class = 'blocked';
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($user['status'] ?? 'N/A'); ?>
                            </span>
                        </td>
                    </tr>
                    <?php
                        endforeach;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleRole(roleId) {
    var card = document.getElementById('role-' + roleId);
    card.classList.toggle('expanded');
}

function expandAll() {
    document.querySelectorAll('.role-card').forEach(function(card) {
        card.classList.add('expanded');
    });
}

function collapseAll() {
    document.querySelectorAll('.role-card').forEach(function(card) {
        card.classList.remove('expanded');
    });
}

// Initialize DataTable for users summary
$(document).ready(function() {
    $('#users_summary_table').DataTable({
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[5, "asc"], [1, "asc"]],
        "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>B',
        "buttons": [
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'User_Roles_Report_<?php echo date("Y-m-d"); ?>'
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'User Roles Report',
                orientation: 'landscape'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-info btn-sm'
            }
        ],
        "language": {
            "search": "<i class='fa fa-search'></i> Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ users",
            "paginate": {
                "first": "<i class='fa fa-angle-double-left'></i>",
                "last": "<i class='fa fa-angle-double-right'></i>",
                "next": "<i class='fa fa-angle-right'></i>",
                "previous": "<i class='fa fa-angle-left'></i>"
            }
        }
    });
});
</script>
