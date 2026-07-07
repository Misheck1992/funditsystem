<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan_notes_model extends CI_Model {

    private $table = 'loan_notes';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Insert a new note
     */
    public function insert($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Get all notes for a loan
     */
    public function get_by_loan($loan_id) {
        $this->db->select('loan_notes.*, employees.Firstname, employees.Lastname');
        $this->db->from($this->table);
        $this->db->join('employees', 'employees.id = loan_notes.notes_by', 'left');
        $this->db->where('loan_id', $loan_id);
        $this->db->order_by('datetime', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get a single note by ID
     */
    public function get_by_id($id) {
        $this->db->where('note_id', $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Delete a note
     */
    public function delete($id) {
        $this->db->where('note_id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Count notes for a loan
     */
    public function count_by_loan($loan_id) {
        $this->db->where('loan_id', $loan_id);
        return $this->db->count_all_results($this->table);
    }
}
