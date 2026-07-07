<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $payout = $deposit->current_principal + $net_interest; ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Close Deposit</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>"><?php echo $deposit->deposit_number; ?></a></li>
                    <li class="breadcrumb-item active">Close</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Maturity Notice -->
            <?php if ($is_matured): ?>
                <div class="fd-notice success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Deposit Matured</strong><br>
                        This deposit has reached its maturity date. No penalty will be applied.
                    </div>
                </div>
            <?php else: ?>
                <div class="fd-notice warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Early Closure</strong><br>
                        This deposit has not yet matured. Maturity Date: <strong><?php echo date('d M Y', strtotime($deposit->maturity_date)); ?></strong>.<br>
                        A penalty of <strong><?php echo $deposit->penalty_rate; ?>%</strong> will be applied to accrued interest.
                    </div>
                </div>
            <?php endif; ?>

            <!-- Closure Form -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Closure Summary</span>
                </div>
                <form method="post" action="<?php echo $action; ?>">
                    <input type="hidden" name="deposit_id" value="<?php echo $deposit->id; ?>">

                    <div class="fd-card-body">
                        <table class="fd-summary-table">
                            <tr>
                                <td>Principal Amount</td>
                                <td><strong>K <?php echo number_format($deposit->current_principal, 2); ?></strong></td>
                            </tr>
                            <tr>
                                <td>Accrued Interest</td>
                                <td>K <?php echo number_format($current_accrued, 2); ?></td>
                            </tr>
                            <?php if (!$is_matured): ?>
                                <tr class="deduction">
                                    <td>Early Closure Penalty (<?php echo $deposit->penalty_rate; ?>%)</td>
                                    <td>(K <?php echo number_format($penalty_preview, 2); ?>)</td>
                                </tr>
                                <tr>
                                    <td>Interest After Penalty</td>
                                    <td>K <?php echo number_format($current_accrued - $penalty_preview, 2); ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr class="deduction">
                                <td>Less: Withholding Tax (<?php echo $wht_rate; ?>%)</td>
                                <td>(K <?php echo number_format($wht_amount, 2); ?>)</td>
                            </tr>
                            <tr>
                                <td>Net Interest (after WHT)</td>
                                <td>K <?php echo number_format($net_interest, 2); ?></td>
                            </tr>
                            <tr class="total">
                                <td>Total Payout</td>
                                <td>K <?php echo number_format($payout, 2); ?></td>
                            </tr>
                        </table>

                        <hr class="fd-divider">

                        <div class="form-group">
                            <label>Reason for Closure</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional reason for closing the deposit..."></textarea>
                        </div>

                        <div style="padding: 12px 16px; background: #f8f9fa; border-radius: 4px; border: 1px solid #dadce0;">
                            <label style="display: flex; align-items: flex-start; gap: 10px; margin: 0; cursor: pointer; color: #202124;">
                                <input type="checkbox" id="confirmClose" required style="margin-top: 3px;">
                                I confirm that I want to close this deposit and disburse <strong>K <?php echo number_format($payout, 2); ?></strong> to the customer.
                            </label>
                        </div>
                    </div>

                    <div class="fd-card-footer">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to close this deposit? This action cannot be undone.')">
                            <i class="fas fa-times-circle mr-1"></i> Close Deposit
                        </button>
                        <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>" class="btn btn-default ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Deposit Info -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Deposit Information</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-borderless fd-kv-table">
                        <tr><td>Deposit #</td><td><strong><?php echo $deposit->deposit_number; ?></strong></td></tr>
                        <tr><td>Customer</td><td><?php echo $deposit->first_name . ' ' . $deposit->last_name; ?></td></tr>
                        <tr><td>Phone</td><td><?php echo $deposit->phone_number; ?></td></tr>
                        <tr><td>Start Date</td><td><?php echo date('d M Y', strtotime($deposit->start_date)); ?></td></tr>
                        <tr><td>Maturity Date</td><td><?php echo date('d M Y', strtotime($deposit->maturity_date)); ?></td></tr>
                        <tr><td>Duration</td><td><?php echo $deposit->duration_months; ?> months</td></tr>
                        <tr><td>Interest Rate</td><td><?php echo $deposit->interest_rate; ?>%</td></tr>
                        <tr><td>Interest Paid</td><td>K <?php echo number_format($deposit->paid_interest, 2); ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
