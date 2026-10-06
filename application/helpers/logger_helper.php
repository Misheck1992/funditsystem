<?php
function log_activity($data){
    $ci =& get_instance();
    $ci->load->database();
    $ci->load->model('Activity_logger_model');

    if (!isset($data['user_id'])) {
        $data['user_id'] = (int) $ci->session->userdata('user_id');
    }
    if (!isset($data['old_data'])) $data['old_data'] = '{}';
    if (!isset($data['new_data'])) $data['new_data'] = '{}';
    if (!isset($data['system_time'])) $data['system_time'] = date('Y-m-d H:i:s');
    $context = array();
    $ip = $ci->input->ip_address();
    if ($ip) $context[] = 'IP: ' . $ip;
    $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
    $route = $ci->uri->uri_string();
    if ($method !== '' || $route !== '') {
        $context[] = 'Request: ' . trim($method . ' /' . ltrim($route, '/'));
    }
    if (!empty($context)) {
        $data['activity'] = rtrim((string) ($data['activity'] ?? ''), ' .') . ' [' . implode('; ', $context) . ']';
    }

    return $ci->db->insert('activity_logger', $data);
}
function log_crud($data){
    $ci =& get_instance();
    $ci->load->database();
    $ci->load->model('Crud_logger_model');
    return $ci->db->insert('crud_logger', $data);
}
function auth_logger($data){
    $ci =& get_instance();
    $ci->load->database();
    return $ci->db->insert('approval_edits', $data);
}
function get_all_table_with_key($table,$field,$key){
    $ci =& get_instance();
    $ci->load->database();
    $sql="SELECT * FROM $table WHERE $field='$key'";
    return $ci->db->query($sql)->result();
}