<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fixed Deposit Interest Accruals Model
 * Handles daily interest accrual audit trail (optional table)
 */
class Fd_accruals_model extends CI_Model
{
    public $table = 'fd_interest_accruals';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all accruals
     */
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    /**
     * Get accrual by ID
     */
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Insert daily accrual record
     */
    function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Insert daily accrual (alias)
     */
    function insert_daily_accrual($data)
    {
        return $this->insert($data);
    }

    /**
     * Update accrual
     */
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete accrual
     */
    function delete($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->delete($this->table);
    }

    /**
     * Get accruals for a deposit
     */
    function get_by_deposit($deposit_id)
    {
        $this->db->where('deposit_id', $deposit_id);
        $this->db->order_by('accrual_date', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get accruals for a specific quarter
     */
    function get_accruals_for_quarter($deposit_id, $quarter, $year)
    {
        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('quarter', $quarter);
        $this->db->where('year', $year);
        $this->db->order_by('accrual_date', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get total accrued interest for a deposit
     */
    function get_total_accrued($deposit_id)
    {
        $this->db->select_sum('interest_amount');
        $this->db->where('deposit_id', $deposit_id);
        $result = $this->db->get($this->table)->row();
        return $result->interest_amount ? $result->interest_amount : 0;
    }

    /**
     * Get total accrued for a quarter
     */
    function get_total_for_quarter($deposit_id, $quarter, $year)
    {
        $this->db->select_sum('interest_amount');
        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('quarter', $quarter);
        $this->db->where('year', $year);
        $result = $this->db->get($this->table)->row();
        return $result->interest_amount ? $result->interest_amount : 0;
    }

    /**
     * Get last accrual date for a deposit
     */
    function get_last_accrual_date($deposit_id)
    {
        $this->db->select_max('accrual_date');
        $this->db->where('deposit_id', $deposit_id);
        $result = $this->db->get($this->table)->row();
        return $result->accrual_date;
    }

    /**
     * Check if accrual exists for date
     */
    function accrual_exists($deposit_id, $date)
    {
        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('accrual_date', $date);
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Get accruals between dates
     */
    function get_between_dates($deposit_id, $from_date, $to_date)
    {
        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('accrual_date >=', $from_date);
        $this->db->where('accrual_date <=', $to_date);
        $this->db->order_by('accrual_date', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get total accrued between dates
     */
    function get_total_between_dates($deposit_id, $from_date, $to_date)
    {
        $this->db->select_sum('interest_amount');
        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('accrual_date >=', $from_date);
        $this->db->where('accrual_date <=', $to_date);
        $result = $this->db->get($this->table)->row();
        return $result->interest_amount ? $result->interest_amount : 0;
    }

    /**
     * Delete accruals by deposit
     */
    function delete_by_deposit($deposit_id)
    {
        $this->db->where('deposit_id', $deposit_id);
        return $this->db->delete($this->table);
    }

    /**
     * Run daily accrual for all active deposits (optional - for audit trail)
     */
    function run_daily_accrual()
    {
        $this->load->model('Fd_deposits_model');
        $this->load->helper('fd');

        $today = date('Y-m-d');
        $current_quarter = get_current_quarter();
        $deposits = $this->Fd_deposits_model->get_active_deposits();
        $count = 0;

        foreach ($deposits as $deposit) {
            // Skip if already accrued today
            if ($this->accrual_exists($deposit->id, $today)) {
                continue;
            }

            // Skip if deposit hasn't started yet
            if (strtotime($deposit->start_date) > strtotime($today)) {
                continue;
            }

            // Skip if deposit has matured
            if (strtotime($deposit->maturity_date) < strtotime($today)) {
                continue;
            }

            // Calculate daily interest
            $daily_rate = calculate_daily_rate($deposit->interest_rate);
            $interest_amount = round($deposit->current_principal * $daily_rate, 4);

            $accrual_data = array(
                'deposit_id' => $deposit->id,
                'accrual_date' => $today,
                'principal_balance' => $deposit->current_principal,
                'daily_rate' => $daily_rate,
                'interest_amount' => $interest_amount,
                'quarter' => $current_quarter['quarter'],
                'year' => $current_quarter['year']
            );

            $this->insert($accrual_data);

            // Update deposit accrued interest
            $new_accrued = $deposit->accrued_interest + $interest_amount;
            $this->Fd_deposits_model->update($deposit->id, array(
                'accrued_interest' => $new_accrued,
                'last_accrual_date' => $today
            ));

            $count++;
        }

        return $count;
    }
}
