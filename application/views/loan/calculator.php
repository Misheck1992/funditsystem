<?php
$loan_types = $this->Loan_products_model->get_all();
?>

<style>
.calculator-container {
    background: #1e3a5f;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.calc-card {
    background: #fff;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.calc-title {
    color: #1e3a5f;
    font-weight: 700;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 3px solid #1e3a5f;
}

.calc-form .form-group {
    margin-bottom: 1.25rem;
}

.calc-form label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: block;
    font-size: 0.9rem;
}

.calc-form .form-control {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.calc-form .form-control:focus {
    border-color: #1e3a5f;
    box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
}

.calc-form select.form-control {
    cursor: pointer;
}

.btn-calculate {
    background: #1e3a5f;
    border: none;
    color: #fff;
    padding: 0.875rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    width: 100%;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-calculate:hover {
    background: #2d5a87;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(30, 58, 95, 0.4);
}

.btn-reset {
    background: #6b7280;
    border: none;
    color: #fff;
    padding: 0.875rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    margin-left: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-reset:hover {
    background: #4b5563;
}

/* Results Section */
.results-card {
    background: #fff;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    height: 100%;
}

.results-title {
    color: #1e3a5f;
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.results-title i {
    color: #1e3a5f;
}

/* Override the calculator output styles */
#calculator {
    font-family: inherit;
}

#calculator h3 {
    color: #1e3a5f;
    font-size: 1.1rem;
    font-weight: 600;
    margin: 1.25rem 0 0.75rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

#calculator table {
    width: 100%;
    margin-bottom: 1rem;
}

#calculator table.table {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

#calculator table.table th {
    background: #1e3a5f;
    color: #fff;
    padding: 0.75rem 0.5rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
}

#calculator table.table td {
    padding: 0.6rem 0.5rem;
    text-align: center;
    border-bottom: 1px solid #e5e7eb;
    font-size: 0.85rem;
}

#calculator table.table tr:nth-child(even) {
    background: #f8fafc;
}

#calculator table.table tr:hover {
    background: #e8eef5;
}

/* Info table styling */
#calculator > table:first-of-type td {
    padding: 0.5rem;
    border: none;
}

#calculator > table:first-of-type td:first-child {
    font-weight: 600;
    color: #374151;
    width: 40%;
}

#calculator > table:first-of-type td:last-child {
    color: #1e3a5f;
}

/* Summary box */
.summary-highlight {
    background: #1e3a5f;
    color: #fff;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.summary-highlight table td {
    color: #fff !important;
    padding: 0.4rem 0.5rem !important;
    border: none !important;
}

.summary-highlight table td:first-child {
    opacity: 0.9;
}

.summary-highlight table td:last-child {
    font-weight: 700;
}

/* Input group styling */
.input-icon {
    position: relative;
}

.input-icon i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.input-icon .form-control {
    padding-left: 2.5rem;
}

/* Responsive */
@media (max-width: 992px) {
    .calculator-container {
        padding: 1rem;
    }

    .calc-card, .results-card {
        margin-bottom: 1.5rem;
    }

    #calculator table.table th,
    #calculator table.table td {
        padding: 0.5rem 0.3rem;
        font-size: 0.75rem;
    }
}
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Loan Calculator</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">Loans</a>
                <span class="breadcrumb-item active">Calculator</span>
            </nav>
        </div>
    </div>

    <div class="calculator-container">
        <div class="row">
            <!-- Calculator Form -->
            <div class="col-lg-5 col-md-6">
                <div class="calc-card">
                    <h4 class="calc-title"><i class="fa fa-calculator"></i> Calculate Loan</h4>

                    <form action="<?php echo base_url('loan/calculate')?>" method="get" class="calc-form">
                        <div class="form-group">
                            <label><i class="fa fa-money"></i> Loan Amount</label>
                            <div class="input-icon">
                                <input type="number" name="amount" class="form-control"
                                       value="<?php echo $this->input->get('amount'); ?>"
                                       placeholder="Enter loan amount" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-tags"></i> Loan Product</label>
                            <select name="loan_type" id="loan_type" class="form-control" required>
                                <option value="">-- Select Product --</option>
                                <?php foreach ($loan_types as $lt): ?>
                                    <option value="<?php echo $lt->loan_product_id; ?>"
                                        <?php echo ($lt->loan_product_id == $this->input->get('loan_type')) ? 'selected' : ''; ?>>
                                        <?php echo $lt->product_name . " (" . $lt->calculation_type . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fa fa-clock-o"></i> Term (Months)</label>
                                    <input type="number" name="months" class="form-control"
                                           value="<?php echo $this->input->get('months'); ?>"
                                           placeholder="e.g. 12" required />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><i class="fa fa-percent"></i> Interest (%/month)</label>
                                    <input type="number" step="0.01" name="interest" class="form-control"
                                           value="<?php echo $this->input->get('interest'); ?>"
                                           placeholder="e.g. 10" required />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-calendar"></i> Start Date</label>
                            <input type="date" name="loan_date" class="form-control"
                                   value="<?php echo $this->input->get('loan_date') ?: date('Y-m-d'); ?>" required />
                        </div>

                        <?php if (validation_errors()): ?>
                            <div class="alert alert-danger" style="border-radius: 10px;">
                                <?php echo validation_errors(); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" style="border-radius: 10px;">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group" style="margin-top: 1.5rem;">
                            <button type="submit" name="submit_loan" class="btn-calculate">
                                <i class="fa fa-calculator"></i> Calculate
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results -->
            <div class="col-lg-7 col-md-6">
                <div class="results-card">
                    <h4 class="results-title"><i class="fa fa-bar-chart"></i> Results</h4>

                    <?php if (isset($result)): ?>
                        <div style="overflow-x: auto;">
                            <?php echo $result; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem; color: #9ca3af;">
                            <i class="fa fa-info-circle" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                            <p>Enter loan details and click <strong>Calculate</strong> to see the repayment schedule.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
