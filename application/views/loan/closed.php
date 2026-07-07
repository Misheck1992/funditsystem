<?php
$settings = get_by_id('settings', 'settings_id', '1');
$currency = $settings ? $settings->currency : 'ZMW';
?>
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Closed Loans</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">Loans</a>
                <span class="breadcrumb-item active">Closed Loans</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">

            <table id="data-table" class="tableCss">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Loan Number</th>
                    <th>Loan Product</th>
                    <th>Loan Customer</th>
                    <th>Loan Date</th>
                    <th>Loan Principal</th>
                    <th>Loan Period</th>
                    <th>Loan Interest</th>
                    <th>Loan Amount Total</th>
                    <th>Loan File</th>
                    <th>Loan Status</th>
                    <th>Closed Date</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $n = 1;
                foreach ($loan_data as $loan) {
                    // Get customer info based on customer type
                    $customer_name = '';
                    $customer_link = '#';

                    if (isset($loan->customer_type) && $loan->customer_type == 'corporate') {
                        $corporate = get_by_id('corporate_customers', 'id', $loan->loan_customer);
                        if ($corporate) {
                            $customer_name = $corporate->EntityName;
                            $customer_link = base_url('corporate_customers/read/' . $corporate->id);
                        } else {
                            $customer_name = 'Corporate Customer';
                        }
                    } else {
                        $individual = get_by_id('individual_customers', 'id', $loan->loan_customer);
                        if ($individual) {
                            $customer_name = $individual->Firstname . ' ' . $individual->Lastname;
                            $customer_link = base_url('individual_customers/view/' . $individual->id);
                        } else {
                            $customer_name = 'Individual Customer';
                        }
                    }
                ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $loan->loan_number ?></td>
                        <td><?php echo isset($loan->product_name) ? $loan->product_name : ''; ?></td>
                        <td><a href="<?php echo $customer_link; ?>"><?php echo $customer_name; ?></a></td>
                        <td><?php echo $loan->loan_date ?></td>
                        <td><?php echo $currency; ?> <?php echo number_format($loan->loan_principal, 2) ?></td>
                        <td><?php echo $loan->loan_period ?></td>
                        <td><?php echo $loan->loan_interest ?>%</td>
                        <td><?php echo $currency; ?> <?php echo number_format($loan->loan_amount_total, 2) ?></td>
                        <td>
                            <?php if (!empty($loan->worthness_file)): ?>
                                <a href="<?php echo base_url('uploads/' . $loan->worthness_file); ?>" download>
                                    Download <i class="fa fa-download"></i>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge-secondary"><?php echo $loan->loan_status ?></span></td>
                        <td><?php echo $loan->loan_added_date ?></td>
                    </tr>
                <?php
                    $n++;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
