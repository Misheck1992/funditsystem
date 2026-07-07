<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fixed Deposit Helper Functions
 * Helper functions for fixed deposit calculations and utilities
 */

/**
 * Get quarter dates for a given quarter and year
 * @param int $quarter Quarter number (1-4)
 * @param int $year Year
 * @return array Array with 'start' and 'end' dates
 */
function get_quarter_dates($quarter, $year) {
    $quarters = array(
        1 => array('start' => '-01-01', 'end' => '-03-31'),
        2 => array('start' => '-04-01', 'end' => '-06-30'),
        3 => array('start' => '-07-01', 'end' => '-09-30'),
        4 => array('start' => '-10-01', 'end' => '-12-31')
    );

    return array(
        'start' => $year . $quarters[$quarter]['start'],
        'end' => $year . $quarters[$quarter]['end']
    );
}

/**
 * Get current quarter number and year
 * @return array Array with 'quarter' and 'year'
 */
function get_current_quarter() {
    $month = (int) date('n');
    $quarter = ceil($month / 3);
    return array(
        'quarter' => $quarter,
        'year' => (int) date('Y')
    );
}

/**
 * Get quarter number for a specific date
 * @param string $date Date string
 * @return array Array with 'quarter' and 'year'
 */
function get_quarter_for_date($date) {
    $month = (int) date('n', strtotime($date));
    $quarter = ceil($month / 3);
    return array(
        'quarter' => $quarter,
        'year' => (int) date('Y', strtotime($date))
    );
}

/**
 * Calculate daily interest rate from annual rate
 * @param float $annual_rate Annual interest rate as percentage
 * @return float Daily rate as decimal
 */
function calculate_daily_rate($annual_rate) {
    return ($annual_rate / 100) / 365;
}

/**
 * Calculate prorated interest for a given number of days
 * @param float $principal Principal amount
 * @param float $annual_rate Annual interest rate as percentage
 * @param int $days Number of days
 * @return float Interest amount
 */
function calculate_prorated_interest($principal, $annual_rate, $days) {
    $daily_rate = calculate_daily_rate($annual_rate);
    return round($principal * $daily_rate * $days, 2);
}

/**
 * Calculate penalty on interest
 * @param float $interest_amount Interest amount
 * @param float $penalty_rate Penalty rate as percentage
 * @return float Penalty amount
 */
function calculate_penalty($interest_amount, $penalty_rate) {
    return round($interest_amount * ($penalty_rate / 100), 2);
}

/**
 * Get number of days between two dates
 * @param string $start Start date
 * @param string $end End date
 * @return int Number of days
 */
function get_days_between($start, $end) {
    $start_date = new DateTime($start);
    $end_date = new DateTime($end);
    $interval = $start_date->diff($end_date);
    return $interval->days;
}

/**
 * Check if a date is before quarter end
 * @param string $date Date to check
 * @param int $quarter Quarter number
 * @param int $year Year
 * @return bool
 */
function is_before_quarter_end($date, $quarter, $year) {
    $quarter_dates = get_quarter_dates($quarter, $year);
    return strtotime($date) < strtotime($quarter_dates['end']);
}

/**
 * Get the next quarter start date from a given date
 * @param string $date Date
 * @return string Next quarter start date
 */
function get_next_quarter_start($date) {
    $current = get_quarter_for_date($date);
    $next_quarter = $current['quarter'] + 1;
    $next_year = $current['year'];

    if ($next_quarter > 4) {
        $next_quarter = 1;
        $next_year++;
    }

    $quarter_dates = get_quarter_dates($next_quarter, $next_year);
    return $quarter_dates['start'];
}

/**
 * Generate FD customer number
 * @param object $ci CodeIgniter instance
 * @return string Customer number like FDC00001
 */
function generate_fd_customer_number($ci = null) {
    if ($ci === null) {
        $ci =& get_instance();
    }

    $ci->db->select_max('id');
    $query = $ci->db->get('fd_customers');
    $row = $query->row();

    $next_id = ($row && $row->id) ? $row->id + 1 : 1;
    return 'FDC' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
}

/**
 * Generate FD deposit number
 * @param object $ci CodeIgniter instance
 * @return string Deposit number like FDD00001
 */
function generate_fd_deposit_number($ci = null) {
    if ($ci === null) {
        $ci =& get_instance();
    }

    $ci->db->select_max('id');
    $query = $ci->db->get('fd_deposits');
    $row = $query->row();

    $next_id = ($row && $row->id) ? $row->id + 1 : 1;
    return 'FDD' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
}

/**
 * Generate FD transaction reference
 * @param object $ci CodeIgniter instance
 * @return string Transaction reference like FDT00001
 */
function generate_fd_transaction_ref($ci = null) {
    if ($ci === null) {
        $ci =& get_instance();
    }

    $ci->db->select_max('id');
    $query = $ci->db->get('fd_transactions');
    $row = $query->row();

    $next_id = ($row && $row->id) ? $row->id + 1 : 1;
    return 'FDT' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
}

/**
 * Calculate maturity date from start date and duration
 * @param string $start_date Start date
 * @param int $months Duration in months
 * @return string Maturity date
 */
function calculate_maturity_date($start_date, $months) {
    $date = new DateTime($start_date);
    $date->modify("+{$months} months");
    return $date->format('Y-m-d');
}

/**
 * Get all quarters between two dates
 * @param string $start_date Start date
 * @param string $end_date End date
 * @return array Array of quarters with dates
 */
function get_quarters_between_dates($start_date, $end_date) {
    $quarters = array();
    $current = get_quarter_for_date($start_date);
    $end = get_quarter_for_date($end_date);

    while ($current['year'] < $end['year'] ||
           ($current['year'] == $end['year'] && $current['quarter'] <= $end['quarter'])) {

        $quarter_dates = get_quarter_dates($current['quarter'], $current['year']);

        $quarters[] = array(
            'quarter' => $current['quarter'],
            'year' => $current['year'],
            'start' => $quarter_dates['start'],
            'end' => $quarter_dates['end']
        );

        $current['quarter']++;
        if ($current['quarter'] > 4) {
            $current['quarter'] = 1;
            $current['year']++;
        }
    }

    return $quarters;
}

/**
 * Format currency amount
 * @param float $amount Amount to format
 * @param string $symbol Currency symbol
 * @return string Formatted amount
 */
function fd_format_currency($amount, $symbol = 'K') {
    return $symbol . ' ' . number_format($amount, 2);
}

/**
 * Get quarter name
 * @param int $quarter Quarter number
 * @return string Quarter name
 */
function get_quarter_name($quarter) {
    $names = array(
        1 => 'Q1 (Jan-Mar)',
        2 => 'Q2 (Apr-Jun)',
        3 => 'Q3 (Jul-Sep)',
        4 => 'Q4 (Oct-Dec)'
    );
    return isset($names[$quarter]) ? $names[$quarter] : '';
}

/**
 * Calculate accrued interest on-demand
 * @param object $deposit Deposit object with principal, rate, start_date, etc.
 * @param string $to_date Calculate up to this date (default: today)
 * @return float Accrued interest amount
 */
function calculate_accrued_interest($deposit, $to_date = null) {
    if ($to_date === null) {
        $to_date = date('Y-m-d');
    }

    // Start from last payment date or start date
    $from_date = !empty($deposit->last_accrual_date) ? $deposit->last_accrual_date : $deposit->start_date;

    // Don't calculate for dates before start
    if (strtotime($from_date) > strtotime($to_date)) {
        return 0;
    }

    $days = get_days_between($from_date, $to_date);
    $principal = floatval($deposit->current_principal);
    $rate = floatval($deposit->interest_rate);

    return calculate_prorated_interest($principal, $rate, $days);
}

/**
 * Get deposit status badge HTML
 * @param string $status Status string
 * @return string HTML badge
 */
function fd_status_badge($status) {
    $badges = array(
        'ACTIVE' => '<span class="badge badge-success">Active</span>',
        'MATURED' => '<span class="badge badge-info">Matured</span>',
        'CLOSED' => '<span class="badge badge-secondary">Closed</span>',
        'MERGED' => '<span class="badge badge-warning">Merged</span>',
        'INACTIVE' => '<span class="badge badge-danger">Inactive</span>',
        'PENDING' => '<span class="badge badge-warning">Pending</span>',
        'PAID' => '<span class="badge badge-success">Paid</span>',
        'PARTIAL' => '<span class="badge badge-info">Partial</span>',
        'SKIPPED' => '<span class="badge badge-secondary">Skipped</span>'
    );

    return isset($badges[$status]) ? $badges[$status] : '<span class="badge badge-light">' . $status . '</span>';
}
