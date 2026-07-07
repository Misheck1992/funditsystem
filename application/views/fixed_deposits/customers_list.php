<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?php echo $page_title; ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item active">Customers</li>
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
                    <a href="<?php echo site_url('Fixed_deposits/customer_create'); ?>" class="btn btn-primary">
                        <i class="fas fa-user-plus mr-1"></i> Add Customer
                    </a>
                    <span style="color: #5f6368; font-size: 13px;">Total: <strong><?php echo $total_rows; ?></strong> customers</span>
                </div>
                <form method="get" action="<?php echo site_url('Fixed_deposits/customers'); ?>">
                    <div class="input-group" style="width: auto;">
                        <input type="text" name="q" class="form-control" placeholder="Search name, phone, ID..." value="<?php echo htmlspecialchars($q ?? ''); ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                            <?php if (!empty($q)): ?>
                                <a href="<?php echo site_url('Fixed_deposits/customers'); ?>" class="btn btn-default" title="Clear"><i class="fas fa-times"></i></a>
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
                        <th>Customer #</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>ID Number</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center; width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 48px; color: #5f6368;">
                                <i class="fas fa-user-slash" style="font-size: 40px; color: #dadce0; display: block; margin-bottom: 12px;"></i>
                                No customers found
                                <?php if (!empty($q)): ?>
                                    <br><a href="<?php echo site_url('Fixed_deposits/customers'); ?>" class="btn btn-default" style="margin-top: 12px;">Clear Search</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = $start + 1; ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td style="font-family: monospace; font-size: 13px;"><?php echo $customer->customer_number; ?></td>
                                <td>
                                    <strong><?php echo $customer->first_name . ' ' . $customer->last_name; ?></strong>
                                    <br><small style="color: #5f6368;"><?php echo $customer->gender; ?> | <?php echo $customer->province; ?></small>
                                </td>
                                <td><?php echo $customer->phone_number; ?></td>
                                <td><?php echo $customer->email ?: '<span style="color: #dadce0;">-</span>'; ?></td>
                                <td><?php echo $customer->id_number ?: '<span style="color: #dadce0;">-</span>'; ?></td>
                                <td style="text-align: center;">
                                    <?php if ($customer->status == 'ACTIVE'): ?>
                                        <span class="badge fd-badge-active">Active</span>
                                    <?php else: ?>
                                        <span class="badge fd-badge-overdue">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?php echo site_url('Fixed_deposits/customer_view/' . $customer->id); ?>" class="btn btn-default btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="<?php echo site_url('Fixed_deposits/customer_update/' . $customer->id); ?>" class="btn btn-default btn-sm" title="Edit" style="color: #e37400;"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo site_url('Fixed_deposits/deposit_create/' . $customer->id); ?>" class="btn btn-default btn-sm" title="New Deposit" style="color: #1e8e3e;"><i class="fas fa-plus"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($customers)): ?>
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
