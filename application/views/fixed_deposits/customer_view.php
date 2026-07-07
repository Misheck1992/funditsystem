<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$active_count = 0;
foreach ($deposits as $d) {
    if ($d->status == 'ACTIVE') $active_count++;
}
?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?php echo $customer->first_name . ' ' . $customer->last_name; ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits/customers'); ?>">Customers</a></li>
                    <li class="breadcrumb-item active"><?php echo $customer->customer_number; ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('message')): ?>
        <div class="fd-notice success">
            <i class="fas fa-check-circle"></i>
            <div><?php echo $this->session->flashdata('message'); ?></div>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="fd-notice danger">
            <i class="fas fa-exclamation-circle"></i>
            <div><?php echo $this->session->flashdata('error'); ?></div>
        </div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="fd-stats">
        <div class="fd-stat">
            <div class="fd-stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Total Principal</div>
                <div class="fd-stat-value sm">K <?php echo number_format($total_principal, 2); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon green"><i class="fas fa-coins"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Accrued Interest</div>
                <div class="fd-stat-value sm">K <?php echo number_format($total_accrued, 2); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon purple"><i class="fas fa-piggy-bank"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Active Deposits</div>
                <div class="fd-stat-value"><?php echo $active_count; ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon gray"><i class="fas fa-list"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Total Deposits</div>
                <div class="fd-stat-value"><?php echo count($deposits); ?></div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="fd-card" style="margin-bottom: 24px;">
        <div class="fd-card-body" style="padding: 12px 24px;">
            <div class="d-flex flex-wrap" style="gap: 8px;">
                <a href="<?php echo site_url('Fixed_deposits/customer_update/' . $customer->id); ?>" class="btn btn-warning">
                    <i class="fas fa-edit mr-1"></i> Edit Customer
                </a>
                <a href="<?php echo site_url('Fixed_deposits/deposit_create/' . $customer->id); ?>" class="btn btn-success">
                    <i class="fas fa-plus mr-1"></i> New Deposit
                </a>
                <?php if ($active_count >= 2): ?>
                    <a href="<?php echo site_url('Fixed_deposits/merge_deposits/' . $customer->id); ?>" class="btn btn-primary">
                        <i class="fas fa-compress-arrows-alt mr-1"></i> Merge Deposits
                    </a>
                <?php endif; ?>
                <a href="<?php echo site_url('Fixed_deposits/customer_profile_pdf/' . $customer->id); ?>" class="btn btn-default" target="_blank">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Customer Information -->
        <div class="col-lg-5">
            <!-- Profile Card -->
            <div class="fd-card">
                <div class="fd-card-body" style="text-align: center; padding: 32px 24px;">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #e8f0fe; color: #1a73e8; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 style="font-weight: 500; color: #202124; margin: 0 0 4px 0;">
                        <?php echo $customer->first_name . ' ' . $customer->last_name; ?>
                    </h3>
                    <p style="font-family: monospace; color: #5f6368; margin: 0 0 8px 0;"><?php echo $customer->customer_number; ?></p>
                    <?php if ($customer->status == 'ACTIVE'): ?>
                        <span class="badge fd-badge-active">Active Customer</span>
                    <?php else: ?>
                        <span class="badge fd-badge-overdue">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Personal Information</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-borderless fd-kv-table">
                        <tr><td>Full Name</td><td><?php echo $customer->first_name . ' ' . $customer->last_name; ?></td></tr>
                        <tr><td>Gender</td><td><?php echo $customer->gender; ?></td></tr>
                        <tr><td>Date of Birth</td><td><?php echo !empty($customer->date_of_birth) ? date('d M Y', strtotime($customer->date_of_birth)) : '-'; ?></td></tr>
                        <tr><td>Province</td><td><?php echo $customer->province; ?></td></tr>
                        <tr><td>District</td><td><?php echo !empty($customer->district) ? $customer->district : '-'; ?></td></tr>
                        <tr><td>City/Town</td><td><?php echo !empty($customer->city) ? $customer->city : '-'; ?></td></tr>
                        <tr><td>Address</td><td><?php echo !empty($customer->address) ? $customer->address : '-'; ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Contact Information</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-borderless fd-kv-table">
                        <tr><td>Phone Number</td><td><?php echo $customer->phone_number; ?></td></tr>
                        <tr><td>Alt. Phone</td><td><?php echo !empty($customer->alt_phone_number) ? $customer->alt_phone_number : '-'; ?></td></tr>
                        <tr><td>Email</td><td><?php echo !empty($customer->email) ? $customer->email : '-'; ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- Employment -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Employment / Source of Funds</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-borderless fd-kv-table">
                        <tr><td>Occupation</td><td><?php echo !empty($customer->occupation) ? $customer->occupation : '-'; ?></td></tr>
                        <tr><td>Employer</td><td><?php echo !empty($customer->employer) ? $customer->employer : '-'; ?></td></tr>
                        <tr><td>Source of Funds</td><td><?php echo !empty($customer->source_of_funds) ? $customer->source_of_funds : '-'; ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Middle Column -->
        <div class="col-lg-3">
            <!-- Identification -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Identification</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-borderless fd-kv-table">
                        <tr><td>ID Type</td><td><?php echo !empty($customer->id_type) ? $customer->id_type : '-'; ?></td></tr>
                        <tr><td>ID Number</td><td><?php echo !empty($customer->id_number) ? $customer->id_number : '-'; ?></td></tr>
                        <tr><td>Expiry Date</td><td><?php echo !empty($customer->id_expiry_date) ? date('d M Y', strtotime($customer->id_expiry_date)) : '-'; ?></td></tr>
                    </table>
                </div>
                <div class="fd-card-footer">
                    <?php if (!empty($customer->nrc_photo)): ?>
                        <a href="<?php echo base_url($customer->nrc_photo); ?>" target="_blank" class="btn btn-default btn-sm" style="margin-bottom: 4px;">
                            <i class="fas fa-eye mr-1"></i> View NRC/ID
                        </a>
                    <?php else: ?>
                        <small style="color: #d93025;"><i class="fas fa-times-circle"></i> No NRC/ID uploaded</small>
                    <?php endif; ?>
                    <br>
                    <?php if (!empty($customer->proof_of_income)): ?>
                        <a href="<?php echo base_url($customer->proof_of_income); ?>" target="_blank" class="btn btn-default btn-sm">
                            <i class="fas fa-eye mr-1"></i> View Proof of Income
                        </a>
                    <?php else: ?>
                        <small style="color: #d93025;"><i class="fas fa-times-circle"></i> No proof of income</small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Next of Kin -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Next of Kin</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-borderless fd-kv-table">
                        <tr><td>Name</td><td><?php echo !empty($customer->nok_name) ? $customer->nok_name : '-'; ?></td></tr>
                        <tr><td>Relationship</td><td><?php echo !empty($customer->nok_relationship) ? $customer->nok_relationship : '-'; ?></td></tr>
                        <tr><td>Phone</td><td><?php echo !empty($customer->nok_phone) ? $customer->nok_phone : '-'; ?></td></tr>
                        <tr><td>ID Number</td><td><?php echo !empty($customer->nok_id_number) ? $customer->nok_id_number : '-'; ?></td></tr>
                        <tr><td>Address</td><td><?php echo !empty($customer->nok_address) ? $customer->nok_address : '-'; ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- Bank Details -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Bank Details</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-borderless fd-kv-table">
                        <tr><td>Bank Name</td><td><?php echo !empty($customer->bank_name) ? $customer->bank_name : '-'; ?></td></tr>
                        <tr><td>Account No.</td><td><?php echo !empty($customer->bank_account_number) ? $customer->bank_account_number : '-'; ?></td></tr>
                        <tr><td>Branch</td><td><?php echo !empty($customer->bank_branch) ? $customer->bank_branch : '-'; ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- KYC Status -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">KYC Status</span>
                </div>
                <div class="fd-card-body">
                    <?php
                    $kyc_items = array(
                        array('label' => 'ID Number', 'complete' => !empty($customer->id_number)),
                        array('label' => 'NRC/ID Photo', 'complete' => !empty($customer->nrc_photo)),
                        array('label' => 'Proof of Income', 'complete' => !empty($customer->proof_of_income)),
                        array('label' => 'Next of Kin', 'complete' => !empty($customer->nok_name)),
                        array('label' => 'Bank Details', 'complete' => !empty($customer->bank_account_number))
                    );
                    $completed = 0;
                    foreach ($kyc_items as $item) {
                        if ($item['complete']) $completed++;
                    }
                    $percentage = ($completed / count($kyc_items)) * 100;
                    ?>
                    <div style="background: #f1f3f4; border-radius: 4px; height: 8px; margin-bottom: 16px; overflow: hidden;">
                        <div style="background: <?php echo $percentage == 100 ? '#1e8e3e' : ($percentage >= 60 ? '#e37400' : '#d93025'); ?>; height: 100%; width: <?php echo $percentage; ?>%; border-radius: 4px; transition: width 0.3s;"></div>
                    </div>
                    <?php foreach ($kyc_items as $item): ?>
                        <div style="display: flex; align-items: center; gap: 8px; padding: 4px 0; font-size: 13px;">
                            <i class="fas fa-<?php echo $item['complete'] ? 'check-circle' : 'times-circle'; ?>"
                               style="color: <?php echo $item['complete'] ? '#1e8e3e' : '#d93025'; ?>;"></i>
                            <span><?php echo $item['label']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Right Column - Deposits -->
        <div class="col-lg-4">
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Deposits</span>
                    <span class="badge fd-badge-active"><?php echo count($deposits); ?></span>
                </div>
                <div class="fd-card-body compact">
                    <?php if (empty($deposits)): ?>
                        <div style="text-align: center; padding: 48px 24px; color: #5f6368;">
                            <i class="fas fa-piggy-bank" style="font-size: 40px; color: #dadce0; margin-bottom: 12px;"></i>
                            <p style="margin-bottom: 16px;">No deposits found</p>
                            <a href="<?php echo site_url('Fixed_deposits/deposit_create/' . $customer->id); ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-plus mr-1"></i> Create First Deposit
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($deposits as $deposit): ?>
                            <?php
                            $days_to_maturity = floor((strtotime($deposit->maturity_date) - time()) / 86400);
                            $is_maturing_soon = $days_to_maturity <= 30 && $days_to_maturity > 0 && $deposit->status == 'ACTIVE';
                            $accrued = ($deposit->status == 'ACTIVE') ? calculate_accrued_interest($deposit) : 0;
                            $status_badges = array('ACTIVE' => 'fd-badge-active', 'MATURED' => 'fd-badge-matured', 'CLOSED' => 'fd-badge-closed', 'MERGED' => 'fd-badge-pending');
                            ?>
                            <div style="padding: 16px 24px; border-bottom: 1px solid #e8eaed;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>" style="font-family: monospace; font-weight: 500;">
                                            <?php echo $deposit->deposit_number; ?>
                                        </a>
                                        <span class="badge <?php echo $status_badges[$deposit->status] ?? 'fd-badge-closed'; ?>" style="margin-left: 6px;">
                                            <?php echo $deposit->status; ?>
                                        </span>
                                    </div>
                                    <div style="text-align: right;">
                                        <strong style="color: #1a73e8;">K <?php echo number_format($deposit->current_principal, 2); ?></strong>
                                        <br><small style="color: #5f6368;"><?php echo $deposit->interest_rate; ?>%</small>
                                    </div>
                                </div>
                                <div style="margin-top: 8px; font-size: 13px; color: #5f6368;">
                                    <?php echo date('d M Y', strtotime($deposit->start_date)); ?> -
                                    <?php echo date('d M Y', strtotime($deposit->maturity_date)); ?>
                                    <?php if ($deposit->status == 'ACTIVE' && $accrued > 0): ?>
                                        <br><span style="color: #1e8e3e;"><i class="fas fa-coins"></i> Accrued: K <?php echo number_format($accrued, 2); ?></span>
                                    <?php endif; ?>
                                    <?php if ($is_maturing_soon): ?>
                                        <br><span style="color: #e37400;"><i class="fas fa-clock"></i> <?php echo $days_to_maturity; ?> days to maturity</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($deposit->status == 'ACTIVE'): ?>
                                    <div style="margin-top: 8px;">
                                        <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>" class="btn btn-default btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="<?php echo site_url('Fixed_deposits/pay_interest/' . $deposit->id); ?>" class="btn btn-default btn-sm" title="Interest" style="color: #1e8e3e;"><i class="fas fa-coins"></i></a>
                                        <a href="<?php echo site_url('Fixed_deposits/placement_certificate/' . $deposit->id); ?>" class="btn btn-default btn-sm" title="Certificate" target="_blank" style="color: #1a73e8;"><i class="fas fa-certificate"></i></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Linked Loan Customer -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Linked Loan Customer</span>
                </div>
                <div class="fd-card-body">
                    <?php if (!empty($linked_individual)): ?>
                        <table class="table table-borderless fd-kv-table">
                            <tr>
                                <td>Name</td>
                                <td><?php echo $linked_individual->Firstname . ' ' . $linked_individual->Lastname; ?></td>
                            </tr>
                            <tr>
                                <td>Client ID</td>
                                <td><?php echo $linked_individual->ClientId; ?></td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td><?php echo $linked_individual->PhoneNumber; ?></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><?php echo $linked_individual->EmailAddress; ?></td>
                            </tr>
                        </table>
                        <a href="<?php echo site_url('individual_customers/view/' . $linked_individual->id); ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-external-link-alt"></i> View Customer
                        </a>
                    <?php else: ?>
                        <p style="margin-bottom: 0; font-size: 13px; color: #5f6368;">
                            <i class="fas fa-unlink" style="color: #d93025; width: 16px;"></i>
                            Not linked to any loan customer
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Account Info -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Account Info</span>
                </div>
                <div class="fd-card-body">
                    <p style="margin-bottom: 8px; font-size: 13px; color: #5f6368;">
                        <i class="fas fa-calendar-plus" style="color: #1a73e8; width: 16px;"></i>
                        <strong style="color: #202124;">Created:</strong> <?php echo date('d M Y H:i', strtotime($customer->created_at)); ?>
                    </p>
                    <?php if (!empty($customer->updated_at)): ?>
                        <p style="margin-bottom: 0; font-size: 13px; color: #5f6368;">
                            <i class="fas fa-calendar-check" style="color: #1e8e3e; width: 16px;"></i>
                            <strong style="color: #202124;">Updated:</strong> <?php echo date('d M Y H:i', strtotime($customer->updated_at)); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
