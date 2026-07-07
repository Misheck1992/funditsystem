<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Collateral_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->ensure_location_status_column();
    }

    /**
     * Ensure location_status column exists in collaterals table
     */
    private function ensure_location_status_column() {
        if (!$this->db->field_exists('location_status', 'collaterals')) {
            $this->db->query("ALTER TABLE collaterals ADD COLUMN location_status VARCHAR(50) DEFAULT 'In Our Possession' AFTER collateral_status");
        }
    }

    // ==================== COLLATERAL CRUD ====================

    /**
     * Get all collaterals for a customer with available balance
     */
    public function get_by_customer($customer_id, $customer_type) {
        $this->db->where('customer_id', $customer_id);
        $this->db->where('customer_type', $customer_type);
        $collaterals = $this->db->get('collaterals')->result();

        // Calculate available balance for each collateral
        foreach ($collaterals as &$collateral) {
            $utilized = $this->get_utilized_amount($collateral->id);
            $collateral->utilized_amount = $utilized;
            $collateral->available_balance = max(0, ($collateral->force_sale_value ?? 0) - $utilized);
        }

        return $collaterals;
    }

    /**
     * Get collateral by ID
     */
    public function get_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('collaterals')->row();
    }

    /**
     * Insert new collateral
     */
    public function insert($data) {
        $this->db->insert('collaterals', $data);
        return $this->db->insert_id();
    }

    /**
     * Update collateral
     */
    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('collaterals', $data);
    }

    /**
     * Delete collateral
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('collaterals');
    }

    /**
     * Get utilized amount for a collateral (sum from all active loans)
     */
    public function get_utilized_amount($collateral_id) {
        $this->db->select_sum('amount_utilized');
        $this->db->where('collateral_id', $collateral_id);
        $this->db->where('status', 'ACTIVE');
        $result = $this->db->get('loan_collateral_links')->row();
        return $result->amount_utilized ?? 0;
    }

    /**
     * Get available force sale balance
     */
    public function get_available_balance($collateral_id) {
        $collateral = $this->get_by_id($collateral_id);
        if (!$collateral) return 0;

        $utilized = $this->get_utilized_amount($collateral_id);
        return max(0, $collateral->force_sale_value - $utilized);
    }

    // ==================== LOAN-COLLATERAL LINKS ====================

    /**
     * Link collateral to a loan
     */
    public function link_to_loan($data) {
        $this->db->insert('loan_collateral_links', $data);
        return $this->db->insert_id();
    }

    /**
     * Get all collaterals linked to a loan
     */
    public function get_loan_collaterals($loan_id) {
        $this->db->select('loan_collateral_links.*, collaterals.*');
        $this->db->from('loan_collateral_links');
        $this->db->join('collaterals', 'collaterals.id = loan_collateral_links.collateral_id', 'left');
        $this->db->where('loan_collateral_links.loan_id', $loan_id);
        return $this->db->get()->result();
    }

    /**
     * Get all loans using a collateral
     */
    public function get_collateral_loans($collateral_id) {
        $this->db->select('loan_collateral_links.*, loan.loan_number, loan.loan_principal, loan.loan_status');
        $this->db->from('loan_collateral_links');
        $this->db->join('loan', 'loan.loan_id = loan_collateral_links.loan_id', 'left');
        $this->db->where('loan_collateral_links.collateral_id', $collateral_id);
        return $this->db->get()->result();
    }

    /**
     * Update link status (e.g., when loan is closed)
     */
    public function update_link_status($link_id, $status, $user_id = null) {
        $data = array(
            'status' => $status,
            'released_at' => ($status == 'RELEASED') ? date('Y-m-d H:i:s') : null,
            'released_by' => ($status == 'RELEASED') ? $user_id : null
        );
        $this->db->where('id', $link_id);
        return $this->db->update('loan_collateral_links', $data);
    }

    /**
     * Release all collaterals for a loan
     */
    public function release_loan_collaterals($loan_id, $user_id) {
        $data = array(
            'status' => 'RELEASED',
            'released_at' => date('Y-m-d H:i:s'),
            'released_by' => $user_id
        );
        $this->db->where('loan_id', $loan_id);
        $this->db->where('status', 'ACTIVE');
        return $this->db->update('loan_collateral_links', $data);
    }

    /**
     * Get link by ID
     */
    public function get_link_by_id($link_id) {
        $this->db->where('id', $link_id);
        return $this->db->get('loan_collateral_links')->row();
    }

    /**
     * Delete link
     */
    public function delete_link($link_id) {
        $this->db->where('id', $link_id);
        return $this->db->delete('loan_collateral_links');
    }

    // ==================== COLLATERAL HISTORY ====================

    /**
     * Log collateral status change
     */
    public function log_history($data) {
        return $this->db->insert('collateral_history', $data);
    }

    /**
     * Get collateral history
     */
    public function get_history($collateral_id) {
        $this->db->where('collateral_id', $collateral_id);
        return $this->db->get('collateral_history')->result();
    }

    // ==================== REPORT METHODS ====================

    /**
     * Get all collaterals with customer info and utilization for reports
     */
    public function get_all_for_report($filters = array()) {
        $this->db->select('collaterals.*,
            COALESCE(SUM(CASE WHEN loan_collateral_links.status = "ACTIVE" THEN loan_collateral_links.amount_utilized ELSE 0 END), 0) as total_utilized,
            COUNT(DISTINCT CASE WHEN loan_collateral_links.status = "ACTIVE" THEN loan_collateral_links.loan_id END) as active_loans_count');
        $this->db->from('collaterals');
        $this->db->join('loan_collateral_links', 'loan_collateral_links.collateral_id = collaterals.id', 'left');

        // Apply filters
        if (!empty($filters['customer_type'])) {
            $this->db->where('collaterals.customer_type', $filters['customer_type']);
        }
        if (!empty($filters['collateral_type'])) {
            $this->db->where('collaterals.collateral_type', $filters['collateral_type']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('collaterals.collateral_status', $filters['status']);
        }
        if (!empty($filters['from_date'])) {
            $this->db->where('collaterals.added_at >=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $this->db->where('collaterals.added_at <=', $filters['to_date'] . ' 23:59:59');
        }

        $this->db->group_by('collaterals.id');
        $this->db->order_by('collaterals.added_at', 'DESC');

        $collaterals = $this->db->get()->result();

        // Add customer name and available balance
        foreach ($collaterals as &$c) {
            $c->available_balance = max(0, floatval($c->force_sale_value) - floatval($c->total_utilized));
            $c->utilization_percent = $c->force_sale_value > 0
                ? round(($c->total_utilized / $c->force_sale_value) * 100, 1)
                : 0;

            // Get customer name
            if ($c->customer_type == 'individual') {
                $customer = $this->db->get_where('individual_customers', array('id' => $c->customer_id))->row();
                $c->customer_name = $customer ? $customer->Firstname . ' ' . $customer->Lastname : 'N/A';
            } else {
                $customer = $this->db->get_where('corporate_customers', array('id' => $c->customer_id))->row();
                $c->customer_name = $customer ? $customer->EntityName : 'N/A';
            }
        }

        return $collaterals;
    }

    /**
     * Get collateral summary statistics
     */
    public function get_report_summary($filters = array()) {
        // Total collaterals count
        $this->db->from('collaterals');
        if (!empty($filters['customer_type'])) {
            $this->db->where('customer_type', $filters['customer_type']);
        }
        $total_count = $this->db->count_all_results();

        // Total market value
        $this->db->select_sum('market_value');
        $this->db->from('collaterals');
        if (!empty($filters['customer_type'])) {
            $this->db->where('customer_type', $filters['customer_type']);
        }
        $market_value = $this->db->get()->row()->market_value ?? 0;

        // Total force sale value
        $this->db->select_sum('force_sale_value');
        $this->db->from('collaterals');
        if (!empty($filters['customer_type'])) {
            $this->db->where('customer_type', $filters['customer_type']);
        }
        $force_sale_value = $this->db->get()->row()->force_sale_value ?? 0;

        // Total utilized
        $this->db->select_sum('amount_utilized');
        $this->db->from('loan_collateral_links');
        $this->db->where('status', 'ACTIVE');
        $total_utilized = $this->db->get()->row()->amount_utilized ?? 0;

        // Count by type
        $this->db->select('collateral_type, COUNT(*) as count, SUM(force_sale_value) as total_value');
        $this->db->from('collaterals');
        $this->db->group_by('collateral_type');
        $by_type = $this->db->get()->result();

        // Count by status
        $this->db->select('collateral_status, COUNT(*) as count');
        $this->db->from('collaterals');
        $this->db->group_by('collateral_status');
        $by_status = $this->db->get()->result();

        return array(
            'total_count' => $total_count,
            'total_market_value' => $market_value,
            'total_force_sale_value' => $force_sale_value,
            'total_utilized' => $total_utilized,
            'total_available' => $force_sale_value - $total_utilized,
            'utilization_rate' => $force_sale_value > 0 ? round(($total_utilized / $force_sale_value) * 100, 1) : 0,
            'by_type' => $by_type,
            'by_status' => $by_status
        );
    }

    /**
     * Get distinct collateral types for filter dropdown
     */
    public function get_collateral_types() {
        $this->db->distinct();
        $this->db->select('collateral_type');
        $this->db->from('collaterals');
        $this->db->where('collateral_type IS NOT NULL');
        $this->db->order_by('collateral_type', 'ASC');
        return $this->db->get()->result();
    }
}
