<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fixed Deposit Transactions Model
 * Handles CRUD operations for FD transactions
 */
class Fd_transactions_model extends CI_Model
{
    public $table = 'fd_transactions';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all transactions
     */
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    /**
     * Get transaction by ID
     */
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Get transaction by reference
     */
    function get_by_ref($transaction_ref)
    {
        $this->db->where('transaction_ref', $transaction_ref);
        return $this->db->get($this->table)->row();
    }

    /**
     * Insert new transaction
     */
    function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update transaction
     */
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete transaction
     */
    function delete($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->delete($this->table);
    }

    /**
     * Generate unique transaction reference
     */
    function generate_ref()
    {
        $this->db->select_max('id');
        $query = $this->db->get($this->table);
        $row = $query->row();

        $next_id = ($row && $row->id) ? $row->id + 1 : 1;
        return 'FDT' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get transactions by deposit
     */
    function get_by_deposit($deposit_id)
    {
        $this->db->where('deposit_id', $deposit_id);
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    /**
     * Get transactions by type
     */
    function get_by_type($deposit_id, $type)
    {
        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('transaction_type', $type);
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    /**
     * Get total amount by type for a deposit
     */
    function get_total_by_type($deposit_id, $type)
    {
        $this->db->select_sum('amount');
        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('transaction_type', $type);
        $result = $this->db->get($this->table)->row();
        return $result->amount ? $result->amount : 0;
    }

    /**
     * Get total paid interest for a deposit
     */
    function get_total_paid_interest($deposit_id)
    {
        return $this->get_total_by_type($deposit_id, 'INTEREST_PAYMENT');
    }

    /**
     * Get total penalties for a deposit
     */
    function get_total_penalties($deposit_id)
    {
        $this->db->select_sum('penalty_amount');
        $this->db->where('deposit_id', $deposit_id);
        $result = $this->db->get($this->table)->row();
        return $result->penalty_amount ? $result->penalty_amount : 0;
    }

    /**
     * Get transactions for a specific quarter
     */
    function get_by_quarter($deposit_id, $quarter, $year)
    {
        $this->db->where('deposit_id', $deposit_id);
        $this->db->where('quarter', $quarter);
        $this->db->where('year', $year);
        $this->db->order_by('created_at', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get recent transactions with deposit and customer info
     */
    function get_recent($limit = 10)
    {
        $this->db->select('t.*, d.deposit_number, c.first_name, c.last_name');
        $this->db->from($this->table . ' t');
        $this->db->join('fd_deposits d', 'd.id = t.deposit_id', 'left');
        $this->db->join('fd_customers c', 'c.id = d.customer_id', 'left');
        $this->db->order_by('t.id', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Get transactions for statement
     */
    function get_for_statement($deposit_id, $from_date = null, $to_date = null)
    {
        $this->db->where('deposit_id', $deposit_id);

        if ($from_date) {
            $this->db->where('DATE(created_at) >=', $from_date);
        }
        if ($to_date) {
            $this->db->where('DATE(created_at) <=', $to_date);
        }

        $this->db->order_by('created_at', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get total transactions count
     */
    function count_all()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Get transaction summary by type
     */
    function get_summary_by_type()
    {
        $this->db->select('transaction_type, COUNT(*) as count, SUM(amount) as total');
        $this->db->group_by('transaction_type');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get today's transactions
     */
    function get_todays_transactions()
    {
        $this->db->select('t.*, d.deposit_number, c.first_name, c.last_name');
        $this->db->from($this->table . ' t');
        $this->db->join('fd_deposits d', 'd.id = t.deposit_id', 'left');
        $this->db->join('fd_customers c', 'c.id = d.customer_id', 'left');
        $this->db->where('DATE(t.created_at)', date('Y-m-d'));
        $this->db->order_by('t.created_at', 'DESC');
        return $this->db->get()->result();
    }
}
