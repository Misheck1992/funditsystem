<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?php echo $page_title; ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item active">All Deposits</li>
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

    <div class="fd-card">
        <div class="fd-card-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap w-100" style="gap: 12px;">
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <a href="<?php echo site_url('Fixed_deposits/deposit_create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> New Deposit
                    </a>
                    <span style="color: #5f6368; font-size: 13px;">Total: <strong><?php echo $total_rows; ?></strong></span>
                </div>
                <form method="get" action="<?php echo site_url('Fixed_deposits/deposits'); ?>" class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <select name="status" class="form-control" onchange="this.form.submit()" style="width: auto;">
                        <option value="">All Status</option>
                        <option value="ACTIVE" <?php echo $status == 'ACTIVE' ? 'selected' : ''; ?>>Active</option>
                        <option value="MATURED" <?php echo $status == 'MATURED' ? 'selected' : ''; ?>>Matured</option>
                        <option value="CLOSED" <?php echo $status == 'CLOSED' ? 'selected' : ''; ?>>Closed</option>
                        <option value="MERGED" <?php echo $status == 'MERGED' ? 'selected' : ''; ?>>Merged</option>
                    </select>
                    <div class="input-group" style="width: auto;">
                        <input type="text" name="q" class="form-control" placeholder="Search deposit, customer..." value="<?php echo htmlspecialchars($q ?? ''); ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                            <?php if (!empty($q) || !empty($status)): ?>
                                <a href="<?php echo site_url('Fixed_deposits/deposits'); ?>" class="btn btn-default" title="Clear"><i class="fas fa-times"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="fd-card-body compact">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Deposit #</th>
                        <th>Customer</th>
                        <th style="text-align: right;">Principal</th>
                        <th style="text-align: center;">Rate</th>
                        <th>Start</th>
                        <th>Maturity</th>
                        <th style="text-align: center;">Payment</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center; width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deposits)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 48px; color: #5f6368;">
                                <i class="fas fa-piggy-bank" style="font-size: 40px; color: #dadce0; display: block; margin-bottom: 12px;"></i>
                                No deposits found
                                <?php if (!empty($q) || !empty($status)): ?>
                                    <br><a href="<?php echo site_url('Fixed_deposits/deposits'); ?>" class="btn btn-default" style="margin-top: 12px;">Clear Filters</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = $start + 1; ?>
                        <?php foreach ($deposits as $deposit): ?>
                            <?php
                            $days_to_maturity = floor((strtotime($deposit->maturity_date) - time()) / 86400);
                            $is_maturing_soon = $days_to_maturity <= 30 && $days_to_maturity > 0 && $deposit->status == 'ACTIVE';
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>" style="font-family: monospace; font-weight: 500;">
                                        <?php echo $deposit->deposit_number; ?>
                                    </a>
                                    <?php if ($is_maturing_soon): ?>
                                        <br><small style="color: #e37400;"><i class="fas fa-clock"></i> <?php echo $days_to_maturity; ?> days</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo site_url('Fixed_deposits/customer_view/' . $deposit->customer_id); ?>">
                                        <strong><?php echo $deposit->first_name . ' ' . $deposit->last_name; ?></strong>
                                    </a>
                                    <br><small style="color: #5f6368;"><?php echo $deposit->customer_number; ?></small>
                                </td>
                                <td style="text-align: right;">
                                    <strong style="color: #1a73e8;">K <?php echo number_format($deposit->current_principal, 2); ?></strong>
                                </td>
                                <td style="text-align: center;"><?php echo $deposit->interest_rate; ?>%</td>
                                <td><?php echo date('d M Y', strtotime($deposit->start_date)); ?></td>
                                <td><?php echo date('d M Y', strtotime($deposit->maturity_date)); ?></td>
                                <td style="text-align: center;">
                                    <?php if ($deposit->payment_option == 'QUARTERLY'): ?>
                                        <span class="badge fd-badge-matured">Quarterly</span>
                                    <?php else: ?>
                                        <span class="badge fd-badge-closed">Maturity</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php
                                    $status_badges = array(
                                        'ACTIVE' => 'fd-badge-active',
                                        'MATURED' => 'fd-badge-matured',
                                        'CLOSED' => 'fd-badge-closed',
                                        'MERGED' => 'fd-badge-pending'
                                    );
                                    $badge = isset($status_badges[$deposit->status]) ? $status_badges[$deposit->status] : 'fd-badge-closed';
                                    ?>
                                    <span class="badge <?php echo $badge; ?>"><?php echo $deposit->status; ?></span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>" class="btn btn-default btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                    <?php if ($deposit->status == 'ACTIVE'): ?>
                                        <a href="<?php echo site_url('Fixed_deposits/pay_interest/' . $deposit->id); ?>" class="btn btn-default btn-sm" title="Interest" style="color: #1e8e3e;"><i class="fas fa-coins"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($deposits)): ?>
            <div class="fd-card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <span style="color: #5f6368; font-size: 13px;">
                        Showing <strong><?php echo $start + 1; ?></strong> to <strong><?php echo min($start + 10, $total_rows); ?></strong> of <strong><?php echo $total_rows; ?></strong>
                    </span>
                    <nav><?php echo $pagination; ?></nav>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
