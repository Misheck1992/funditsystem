<?php
function log_activity($data){

	$ci =& get_instance();
	$ci->load->database();
	$ci->load->model('Activity_logger_model');

	$sql=$ci->db->insert('activity_logger',$data);


}
function log_crud($data){

	$ci =& get_instance();
	$ci->load->database();
	$ci->load->model('Crud_logger_model');
	$sql=$ci->db->insert('crud_logger',$data);

}
function auth_logger($data){

	$ci =& get_instance();
	$ci->load->database();

	$sql=$ci->db->insert('approval_edits',$data);

}
function get_all_table_with_key($table,$field,$key){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM $table WHERE $field='$key'";
    return $query = $ci->db->query($sql)->result();
}
