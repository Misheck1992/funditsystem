<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fixed Deposit Quarterly Schedules Model
 * Handles quarterly payment schedules for FD deposits
 */
class Fd_schedules_model extends CI_Model
{
    public $table = 'fd_quarterly_schedules';
    public $id = 'id';
    public $order = 'ASC';

    function __construct()
    {
        parent::__construct();
        $this->load->helper('fd');
    }

    /**
     * Get all schedules
     */
    function get_all()
    {
        $this->db->order_by('year', 'ASC');
        $this->db->order_by('quarter', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get schedule by ID
     */
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Insert new schedule
     */
    function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update schedule
     */
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete schedule
     */
    function delete($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->delete($this->table);
    }

    /**
     * Delete all schedules for a deposit
     */
    function delete_by_deposit($deposit_id)
    {
        $this->db->where('deposit_id', $deposit_id);
        return $this->db->delete($this->table);
    }

    /**
     * Get schedules by deposit
     */
    function get_by_deposit($deposit_id)
    {
        $this->db->where('deposit_id', $deposit_id);
        $this->db->order_by('year', 'ASC');
        $this->db->order_by('quarter', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Generate quarterly schedule for a deposit
     */
    function generate_quarterly_schedule($deposit_id, $start_date, $maturity_date, $principal, $interest_rate)
    {
        // Delete existing schedules
        $this->delete_by_deposit($deposit_id);

        // Get all quarters between start and maturity
        $quarters = get_quarters_between_dates($start_date, $maturity_date);

        foreach ($quarters as $q) {
            // Calculate deposit days in this quarter
            $quarter_start = $q['start'];
            $quarter_end = $q['end'];

            // Deposit contribution to this quarter
            $deposit_start_in_quarter = max($start_date, $quarter_start);
            $deposit_end_in_quarter = min($maturity_date, $quarter_end);

            // Only create schedule if deposit is active in this quarter
            if (strtotime($deposit_start_in_quarter) <= strtotime($deposit_end_in_quarter)) {
                $days_in_quarter = get_days_between($quarter_start, $quarter_end) + 1;
                $deposit_days = get_days_between($deposit_start_in_quarter, $deposit_end_in_quarter) + 1;

                // Calculate expected interest for this quarter
                $expected_interest = calculate_prorated_interest($principal, $interest_rate, $deposit_days);

                $schedule_data = array(
                    'deposit_id' => $deposit_id,
                    'quarter' => $q['quarter'],
                    'year' => $q['year'],
                    'quarter_start' => $quarter_start,
                    'quarter_end' => $quarter_end,
                    'deposit_start_in_quarter' => $deposit_start_in_quarter,
                    'days_in_quarter' => $days_in_quarter,
                    'deposit_days' => $deposit_days,
                    'expected_interest' => $expected_interest,
                    'paid_interest' => 0,
                    'penalty_amount' => 0,
                    'status' => 'PENDING'
                );

                $this->insert($schedule_data);
            }
        }

        return true;
    }

    /**
     * Get pending payments for a deposit
     */
    function get_pending_payments($deposit_id)
    {
        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('status', 'PENDING');
        $this->db->order_by('year', 'ASC');
        $this->db->order_by('quarter', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get current quarter payment for a deposit
     */
    function get_current_quarter_payment($deposit_id)
    {
        $current = get_current_quarter();

        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('quarter', $current['quarter']);
        $this->db->where('year', $current['year']);
        return $this->db->get($this->table)->row();
    }

    /**
     * Get schedule for specific quarter
     */
    function get_schedule_for_quarter($deposit_id, $quarter, $year)
    {
        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('quarter', $quarter);
        $this->db->where('year', $year);
        return $this->db->get($this->table)->row();
    }

    /**
     * Mark schedule as paid
     */
    function mark_paid($id, $amount, $penalty = 0)
    {
        $data = array(
            'paid_interest' => $amount,
            'penalty_amount' => $penalty,
            'status' => 'PAID',
            'payment_date' => date('Y-m-d')
        );
        return $this->update($id, $data);
    }

    /**
     * Mark schedule as partial
     */
    function mark_partial($id, $amount, $penalty = 0)
    {
        $schedule = $this->get_by_id($id);
        $new_paid = $schedule->paid_interest + $amount;

        $data = array(
            'paid_interest' => $new_paid,
            'penalty_amount' => $schedule->penalty_amount + $penalty,
            'status' => 'PARTIAL'
        );
        return $this->update($id, $data);
    }

    /**
     * Get total expected interest for a deposit
     */
    function get_total_expected_interest($deposit_id)
    {
        $this->db->select_sum('expected_interest');
        $this->db->where('deposit_id', $deposit_id);
        $result = $this->db->get($this->table)->row();
        return $result->expected_interest ? $result->expected_interest : 0;
    }

    /**
     * Get total paid interest for a deposit
     */
    function get_total_paid_interest($deposit_id)
    {
        $this->db->select_sum('paid_interest');
        $this->db->where('deposit_id', $deposit_id);
        $result = $this->db->get($this->table)->row();
        return $result->paid_interest ? $result->paid_interest : 0;
    }

    /**
     * Get overdue payments (past quarter end, still pending)
     */
    function get_overdue_payments()
    {
        $today = date('Y-m-d');

        $this->db->select('s.*, d.deposit_number, d.current_principal, d.interest_rate, c.first_name, c.last_name, c.phone_number');
        $this->db->from($this->table . ' s');
        $this->db->join('fd_deposits d', 'd.id = s.deposit_id', 'left');
        $this->db->join('fd_customers c', 'c.id = d.customer_id', 'left');
        $this->db->where('s.status', 'PENDING');
        $this->db->where('s.quarter_end <', $today);
        $this->db->where('d.status', 'ACTIVE');
        $this->db->order_by('s.quarter_end', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get upcoming payments (due within X days)
     */
    function get_upcoming_payments($days = 30)
    {
        $today = date('Y-m-d');
        $future = date('Y-m-d', strtotime("+{$days} days"));

        $this->db->select('s.*, d.deposit_number, d.current_principal, d.interest_rate, c.first_name, c.last_name, c.phone_number');
        $this->db->from($this->table . ' s');
        $this->db->join('fd_deposits d', 'd.id = s.deposit_id', 'left');
        $this->db->join('fd_customers c', 'c.id = d.customer_id', 'left');
        $this->db->where('s.status', 'PENDING');
        $this->db->where('s.quarter_end >=', $today);
        $this->db->where('s.quarter_end <=', $future);
        $this->db->where('d.status', 'ACTIVE');
        $this->db->order_by('s.quarter_end', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Recalculate schedule interest after principal change
     */
    function recalculate_schedule($deposit_id, $new_principal, $interest_rate, $from_date = null)
    {
        if (!$from_date) {
            $from_date = date('Y-m-d');
        }

        // Get pending schedules from this date forward
        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('status', 'PENDING');
        $this->db->where('quarter_end >=', $from_date);
        $schedules = $this->db->get($this->table)->result();

        foreach ($schedules as $schedule) {
            // Recalculate expected interest
            $deposit_days = $schedule->deposit_days;
            $expected_interest = calculate_prorated_interest($new_principal, $interest_rate, $deposit_days);

            $this->update($schedule->id, array(
                'expected_interest' => $expected_interest
            ));
        }

        return true;
    }
}
