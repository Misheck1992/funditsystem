<style>
    .exchange-rate-card {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
    }

    .exchange-rate-table {
        width: 100%;
        border-collapse: collapse;
    }

    .exchange-rate-table thead {
        background: linear-gradient(135deg, #3498db 0%, #5dade2 100%);
        color: #fff;
    }

    .exchange-rate-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .exchange-rate-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.2s;
    }

    .exchange-rate-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .exchange-rate-table td {
        padding: 15px;
        vertical-align: middle;
        color: #495057;
    }

    .currency-info {
        display: flex;
        align-items: center;
    }

    .currency-flag {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 16px;
        margin-right: 12px;
    }

    .currency-details {
        flex: 1;
    }

    .currency-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 15px;
        margin-bottom: 2px;
    }

    .currency-country {
        font-size: 12px;
        color: #95a5a6;
    }

    .rate-input-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rate-input {
        width: 140px;
        padding: 8px 12px;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.2s;
    }

    .rate-input:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .rate-label {
        font-size: 12px;
        color: #7f8c8d;
        font-weight: 600;
        min-width: 35px;
    }

    .save-btn {
        padding: 8px 20px;
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        box-shadow: 0 2px 5px rgba(39, 174, 96, 0.2);
    }

    .save-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(39, 174, 96, 0.3);
    }

    .save-btn:active {
        transform: translateY(0);
    }

    .save-btn:disabled {
        background: #95a5a6;
        cursor: not-allowed;
        transform: none;
    }

    .status-icon {
        margin-left: 8px;
        font-size: 16px;
        display: none;
    }

    .status-icon.success {
        color: #27ae60;
        display: inline;
    }

    .status-icon.error {
        color: #e74c3c;
        display: inline;
    }

    @media (max-width: 992px) {
        .exchange-rate-table {
            font-size: 13px;
        }

        .rate-input {
            width: 100px;
        }

        .save-btn {
            padding: 6px 12px;
            font-size: 12px;
        }
    }

    @media (max-width: 768px) {
        .exchange-rate-card {
            padding: 15px;
            overflow-x: auto;
        }

        .rate-input {
            width: 80px;
        }
    }
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Currency Exchange Rates</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">Settings</a>
                <span class="breadcrumb-item active">Exchange Rates</span>
            </nav>
        </div>
    </div>

    <div class="exchange-rate-card">
        <div class="mb-3">
            <h5 style="color: #2c3e50; margin-bottom: 5px;">Manage Exchange Rates</h5>
            <p style="color: #7f8c8d; font-size: 14px; margin: 0;">Update ZMK and FX rates for each currency. Changes are saved individually.</p>
        </div>

        <div style="overflow-x: auto;">
            <table class="exchange-rate-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 30%;">Currency</th>
                        <th style="width: 15%;">Code</th>
                        <th style="width: 20%;">ZMK Rate</th>
                        <th style="width: 20%;">FX Rate</th>
                        <th style="width: 10%; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(!empty($currencies)) {
                        $n = 1;
                        foreach($currencies as $currency) {
                    ?>
                    <tr id="row_<?php echo $currency->currency_id; ?>">
                        <td><?php echo $n; ?></td>
                        <td>
                            <div class="currency-info">
                                <div class="currency-flag">
                                    <?php echo strtoupper(substr($currency->currency_code, 0, 2)); ?>
                                </div>
                                <div class="currency-details">
                                    <div class="currency-name"><?php echo $currency->currency_name; ?></div>
                                    <div class="currency-country"><?php echo $currency->country_name; ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong style="color: #3498db; font-size: 15px;"><?php echo $currency->currency_code; ?></strong>
                        </td>
                        <td>
                            <div class="rate-input-group">
                                <span class="rate-label">ZMK:</span>
                                <input type="number"
                                       class="rate-input zmk-input"
                                       id="zmk_<?php echo $currency->currency_id; ?>"
                                       value="<?php echo number_format($currency->zmk, 2, '.', ''); ?>"
                                       step="0.01"
                                       min="0">
                            </div>
                        </td>
                        <td>
                            <div class="rate-input-group">
                                <span class="rate-label">FX:</span>
                                <input type="number"
                                       class="rate-input fx-input"
                                       id="fx_<?php echo $currency->currency_id; ?>"
                                       value="<?php echo number_format($currency->fx, 2, '.', ''); ?>"
                                       step="0.01"
                                       min="0">
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <button class="save-btn" onclick="saveRate(<?php echo $currency->currency_id; ?>)">
                                <i class="fa fa-save"></i> Save
                            </button>
                            <span class="status-icon" id="status_<?php echo $currency->currency_id; ?>"></span>
                        </td>
                    </tr>
                    <?php
                        $n++;
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #95a5a6;">
                            <i class="fa fa-info-circle" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                            No currencies found in the system.
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function saveRate(currencyId) {
    var zmk = document.getElementById('zmk_' + currencyId).value;
    var fx = document.getElementById('fx_' + currencyId).value;
    var btn = document.querySelector('#row_' + currencyId + ' .save-btn');
    var statusIcon = document.getElementById('status_' + currencyId);

    // Validation
    if(!zmk || !fx) {
        toastr.error('Please enter both ZMK and FX rates');
        return;
    }

    if(parseFloat(zmk) < 0 || parseFloat(fx) < 0) {
        toastr.error('Rates cannot be negative');
        return;
    }

    // Disable button
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
    statusIcon.className = 'status-icon';

    // AJAX request
    $.ajax({
        url: '<?php echo base_url("currency/update_exchange_rate"); ?>',
        type: 'POST',
        data: {
            currency_id: currencyId,
            zmk: zmk,
            fx: fx
        },
        dataType: 'json',
        success: function(response) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-save"></i> Save';

            if(response.status == 'success') {
                toastr.success(response.message);
                statusIcon.className = 'status-icon success';
                statusIcon.innerHTML = '<i class="fa fa-check-circle"></i>';

                // Hide icon after 3 seconds
                setTimeout(function() {
                    statusIcon.className = 'status-icon';
                }, 3000);
            } else {
                toastr.error(response.message);
                statusIcon.className = 'status-icon error';
                statusIcon.innerHTML = '<i class="fa fa-times-circle"></i>';

                // Hide icon after 3 seconds
                setTimeout(function() {
                    statusIcon.className = 'status-icon';
                }, 3000);
            }
        },
        error: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-save"></i> Save';
            toastr.error('An error occurred. Please try again.');
            statusIcon.className = 'status-icon error';
            statusIcon.innerHTML = '<i class="fa fa-times-circle"></i>';

            // Hide icon after 3 seconds
            setTimeout(function() {
                statusIcon.className = 'status-icon';
            }, 3000);
        }
    });
}

// Allow Enter key to save
document.addEventListener('DOMContentLoaded', function() {
    var inputs = document.querySelectorAll('.rate-input');
    inputs.forEach(function(input) {
        input.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                var currencyId = this.id.split('_')[1];
                saveRate(currencyId);
            }
        });
    });
});
</script>
