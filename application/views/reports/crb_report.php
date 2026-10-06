<?php $export = !empty($export); ?>
<?php if (!$export): ?><div class="main-content"><div class="page-header"><h2 class="header-title">CRB Report</h2></div><div class="card"><div class="card-body">
<form method="get" action="<?php echo base_url('reports/crb_report'); ?>" class="form-inline mb-3">
<label class="mr-2">CRB Status</label><select name="crb_status" class="form-control mr-2"><option>All</option><?php foreach (array('Yes','No','Clean','Has Issues','Not Done') as $option): ?><option value="<?php echo $option; ?>" <?php echo $crb_status === $option ? 'selected' : ''; ?>><?php echo $option; ?></option><?php endforeach; ?></select>
<input type="date" name="from_date" value="<?php echo htmlspecialchars($filters['from_date']); ?>" class="form-control mr-2"><input type="date" name="to_date" value="<?php echo htmlspecialchars($filters['to_date']); ?>" class="form-control mr-2">
<button name="search" value="filter" class="btn btn-primary mr-2">Filter</button><button name="search" value="pdf" class="btn btn-danger mr-2">PDF</button><button name="search" value="excel" class="btn btn-success">Excel</button></form>
<?php endif; ?>
<h3>CRB Report</h3><p>Generated <?php echo date('d M Y H:i'); ?> | Records: <?php echo count($loan_data); ?></p>
<table class="table table-bordered" style="width:100%;border-collapse:collapse"><thead><tr><th>#</th><th>Customer</th><th>Loan Number</th><th>Product</th><th>Principal</th><th>CRB Status</th><th>Loan Status</th><th>Loan Date</th></tr></thead><tbody>
<?php foreach ($loan_data as $i => $loan): $name = $loan->customer_type === 'individual' ? trim(($loan->ind_firstname ?? '').' '.($loan->ind_lastname ?? '')) : ($loan->corp_name ?? 'Unknown'); ?>
<tr><td><?php echo $i+1; ?></td><td><?php echo htmlspecialchars($name); ?></td><td><?php echo htmlspecialchars($loan->loan_number); ?></td><td><?php echo htmlspecialchars($loan->facility_type ?? ''); ?></td><td><?php echo number_format((float)$loan->loan_principal,2); ?></td><td><?php echo htmlspecialchars($loan->crb_search ?? 'Not Specified'); ?></td><td><?php echo htmlspecialchars($loan->loan_status); ?></td><td><?php echo htmlspecialchars($loan->loan_date); ?></td></tr>
<?php endforeach; ?></tbody></table>
<?php if (!$export): ?></div></div></div><?php endif; ?>
