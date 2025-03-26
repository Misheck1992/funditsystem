<?php
function get_all($id){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $id ";
	return $query = $ci->db->query($sql)->result();


}function get_all_order($id, $order_field){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $id ORDER BY $order_field DESC";
	return $query = $ci->db->query($sql)->result();


}


//get all shareholders
function get_all_shareholders($id){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM corporate_shareholders INNER JOIN shareholders ON shareholders.id = corporate_shareholders.shareholder_id WHERE corporate_shareholders.corporate_id = '$id'";
    return $query = $ci->db->query($sql)->result();


}


function get_all_where($table, $where, $order_by = null, $order_direction = 'ASC') {
    $ci =& get_instance();
    $ci->load->database();

    $sql = "SELECT * FROM $table WHERE $where";

    // If order by parameters are provided, add them to the SQL query
    if ($order_by !== null) {
        $sql .= " ORDER BY $order_by $order_direction";
    }

    // Execute the SQL query and return the results
    return $ci->db->query($sql)->result();
}
function get_active_loan_products(){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM  loan_products ";
    return $query = $ci->db->query($sql)->result();


}

function delete_payments(){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM  loan_products ";
    return $query = $ci->db->query($sql)->result();


}


function  get_user_loan_individual($id){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *
FROM loan
WHERE loan_customer = '$id'
AND customer_type = 'individual' ";
    return $query = $ci->db->query($sql)->result();


}
function  get_all_un_paid_loans(){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT loan.*, payement_schedules.* FROM loan INNER JOIN ( SELECT loan_id, MIN(payment_number) AS min_payment_number FROM payement_schedules WHERE status = 'Not paid' GROUP BY loan_id ) AS first_payment_schedule ON loan.loan_id = first_payment_schedule.loan_id AND loan.loan_status = 'active' INNER JOIN payement_schedules ON loan.loan_id = payement_schedules.loan_id AND first_payment_schedule.min_payment_number = payement_schedules.payment_number";
    return $query = $ci->db->query($sql)->result();


}

function  get_all_full_un_paid_loans(){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT loan.* FROM loan JOIN payement_schedules ON loan.loan_id = payement_schedules.loan_id WHERE loan.loan_status = 'active' AND payement_schedules.status = 'NOT PAID' AND payement_schedules.payment_number = 1";
    return $query = $ci->db->query($sql)->result();


}



function get_all_data_imported_payments_cofi()
{
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `massrepayments` JOIN loan ON loan.loan_number = massrepayments.loan_number  WHERE `massrepayment_status`='imported'  ";
    return $query = $ci->db->query($sql)->result();
}


function  send_email($to, $body){


    $url = 'https://finrealm.infocustech-mw.com/Admin/mail_api/';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $data = array(
        'to' => $to,
        'subject' => 'Password reset',
        'body' => $body
    );

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;



}
function login_user($username){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM user_access inner join employees on employees.id=user_access.Employee 
     inner join roles on roles.id=employees.Role 
      WHERE AccessCode = '$username'";
    return $query = $ci->db->query($sql)->row();


}
function get_by_id($table,$key,$value){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $table  WHERE $key = '$value'";
	return $query = $ci->db->query($sql)->row();


}
function get_partial_paid($key ,$value){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `payement_schedules` WHERE `loan_id`= '$key ' AND `payment_number`= '$value' AND  `partial_paid`= 'YES'";
    return $query = $ci->db->query($sql)->row();


}

function get_partial_paid_last($key){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `payement_schedules` WHERE `loan_id`= '$key ' AND partial_paid = 'YES' ORDER BY id DESC
LIMIT 1";
    return $query = $ci->db->query($sql)->row();


}
function get_paid_last($key){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `payement_schedules` WHERE `loan_id`= '$key ' ORDER BY id DESC LIMIT 1";
    return $query = $ci->db->query($sql)->row();


}


function getlastrow($loannumber){

    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *
            FROM massrepayments
            WHERE loan_number = '$loannumber'
            ORDER BY massrepayment_id DESC
            LIMIT 1;
";
    return $query = $ci->db->query($sql)->row();


}
function get_all_by_id($table,$key,$value){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $table  WHERE $key = '$value'";
	return $query = $ci->db->query($sql)->result();


}


function get_allLoanPayRBM_by_id($loanid,$paymentnumber){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM payement_schedules WHERE loan_id=$loanid AND payment_number=$paymentnumber ";
	return $query = $ci->db->query($sql)->row();


}

function get_previous_loan($customerID){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM loan WHERE loan_customer = $customerID AND loan_id < (SELECT MAX(loan_id) FROM loan WHERE loan_customer = $customerID) ORDER BY loan_id DESC LIMIT 1 ";
	return $query = $ci->db->query($sql)->row();


}
function get_all_loan_balances_by_product($product){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID' AND loan.loan_product= '$product' AND loan.loan_status = 'ACTIVE'";
    return $query = $ci->db->query($sql)->result();
}
function institutional_portfolio(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE'";
    return $query = $ci->db->query($sql)->result();
}



   function get_number_of_arreas($loadID){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT COUNT(*) AS num_arrears
                FROM loan l
                JOIN payement_schedules ps ON l.loan_id = ps.loan_id
                WHERE l.loan_id = '$loadID'
                  AND l.loan_status = 'active'
                  AND ps.payment_schedule < CURDATE()
                  AND ps.status <> 'paid' ";
    return $query = $ci->db->query($sql)->row();
}



function get_days_of_arreas($loadID){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT DATEDIFF(CURRENT_DATE(), MAX(ps.payment_schedule)) AS days_in_arrears
            FROM loan l
            JOIN payement_schedules ps ON l.loan_id = ps.loan_id
            WHERE l.loan_id = '$loadID'
            AND payment_schedule <  CURDATE()
          AND l.loan_status = 'active'
                  
                  AND ps.status <> 'paid' ";
    return $query = $ci->db->query($sql)->row();
}

   function get_amount_of_arreas($loadID){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT SUM(ps.amount)  AS amount_arrears
                FROM loan l
                JOIN payement_schedules ps ON l.loan_id = ps.loan_id
                WHERE l.loan_id = '$loadID'
                  AND l.loan_status = 'active'
                  AND ps.payment_schedule < CURDATE()
                  AND ps.status <> 'paid' ";
    return $query = $ci->db->query($sql)->row();
}
//get distinct loan
function get_all_distinctLoan_cofi(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT DISTINCT(`LOANNO`) FROM `completedlimbenew` ";
    return $query = $ci->db->query($sql)->result();
}

//get_all_from_cofi_salarybacked()


function get_all_sme_cofi(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT DISTINCT(`IDNumber`) FROM `smeloans` WHERE 1";
    return $query = $ci->db->query($sql)->result();
}


function get_all_sme_cofi_by_id($idloan){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

     $sql="SELECT * FROM `smeloans` WHERE `IDNumber`='$idloan'";
    return $query = $ci->db->query($sql)->row();
}

function get_all_sme_cofi_by_all($idloan){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

     $sql="SELECT * FROM `smeloans` WHERE `IDNumber`='$idloan'";
    return $query = $ci->db->query($sql)->result();
}

function check_individual($idloan){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `proofofidentity` WHERE `IDNumber`='$idloan'";
    return $query = $ci->db->query($sql)->result();
}

//get_all_from_cofi_salarybacked()


function get_all_cust_cofi($idloan){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `completedlimbenew` WHERE `LOANNO`='$idloan'";
    return $query = $ci->db->query($sql)->row();
}

function get_total_loan_amount($status){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT SUM(loan_amount_total) AS total_amount
            FROM loan
            WHERE loan_status = '$status'";

    return $query = $ci->db->query($sql)->row();
}

function get_total_loan_amount_product($product){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT SUM(loan_amount_total) AS total_amount_product
            FROM loan
            WHERE loan_product = '$product'";

    return $query = $ci->db->query($sql)->row();
}

function get_total_loan_amount_product_by_id($status,$product){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT SUM(loan_amount_total) AS total_amount_product
            FROM loan
            WHERE loan_status = '$status' AND loan_product = '$product'";

    return $query = $ci->db->query($sql)->row();
}


function get_all_loanCheck($loadID){
    $ci =& get_instance();
    $ci->load->database();


    $sql="SELECT * FROM loan  WHERE `Loan_number`='$loadID'";
    return $query = $ci->db->query($sql)->result();
}
function get_all_accountCheck($loadID){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `account` WHERE `account_number`='$loadID'";
    return $query = $ci->db->query($sql)->result();
}

function get_all_cust_cofi_group(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM completedlimbenew GROUP BY`LOANNO`";
    return $query = $ci->db->query($sql)->result();
}

function get_all_groups_filter(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT DISTINCT(`CLIENTNAME` ),id,LOCATION,PHONENUMBER FROM completedlimbenew";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule < CURDATE()";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_today(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule = SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}


function institutional_arrears_today_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id  WHERE  loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule = SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_threedays(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),3) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}


function institutional_arrears_threedays_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id  WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),3) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_week(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),7) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}

function institutional_arrears_week_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),7) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}


function institutional_arrears_month(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),30) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}

function institutional_arrears_month_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id  WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),30) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_2month(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),60) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}


function institutional_arrears_2month_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id  WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),60) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_3month(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),90) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_3month_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id  WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),90) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}

function payments_today(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule = CURDATE()";
    return $query = $ci->db->query($sql)->result();
}


function payments_today_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule = CURDATE()";
    return $query = $ci->db->query($sql)->result();
}
function payments_week(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN adddate(curdate(), 7) AND adddate(curdate(), 1)";
    return $query = $ci->db->query($sql)->result();
}

function payments_week_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id WHERE loan.loan_product='$id'  AND  payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN adddate(curdate(), 7) AND adddate(curdate(), 1)";
    return $query = $ci->db->query($sql)->result();
}
function payments_month(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN subdate(curdate(), 30) AND subdate(curdate(), 1)";
    return $query = $ci->db->query($sql)->result();
}

function payments_month_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN subdate(curdate(), 30) AND subdate(curdate(), 1)";
    return $query = $ci->db->query($sql)->result();
}

function rbm_report(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * from individual_customers 
    inner join  proofofidentity on proofofidentity.ClientID=individual_customers.ClientID 
    INNER JOIN loan ON loan.loan_customer=individual_customers.id ORDER by loan.loan_id DESC limit 0,300";
    return $query = $ci->db->query($sql)->result();
}




function check_paid_fees($loan_id){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM transactions WHERE loan_id = $loan_id AND transaction_type= '1'";
	return $query = $ci->db->query($sql)->result();


}
function get_active_loan(){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM loan WHERE loan_status = 'ACTIVE'";
	return $query = $ci->db->query($sql)->result();


}
function get_logs($table,$key,$value){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $table  WHERE $key = $value LIMIT 5";
	return $query = $ci->db->query($sql)->result();


}
function get_all_features($value){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM listing_features JOIN features ON features.feature_id = listing_features.feature_id WHERE listing_features.listing_id = '$value'";
	return $query = $ci->db->query($sql)->result();


}

function get_all_customersGroup($value){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * from individual_customers INNER JOIN customer_groups on customer_groups.customer=individual_customers.id 
	WHERE customer_groups.group_id
 = '$value'";
	return $query = $ci->db->query($sql)->result();


}
function get_features(){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM features ";
	return $query = $ci->db->query($sql)->result();


}
function get_listing_images($id){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM listing_images WHERE listing_images.listing_id = '$id'";

	return $query = $ci->db->query($sql)->result();


}
function get_recent(){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM  listings  JOIN districts ON districts.district_id = listings.listing_location  JOIN release_type ON release_type.rtype_id = listings.rtype JOIN categories ON categories.category_id = listings.listing_category LIMIT 5";

	return $query = $ci->db->query($sql)->result();


}
function get_similar(){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM  listings  JOIN districts ON districts.district_id = listings.listing_location  JOIN release_type ON release_type.rtype_id = listings.rtype JOIN categories ON categories.category_id = listings.listing_category LIMIT 5";
	return $query = $ci->db->query($sql)->result();


}
function get_featured(){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM  listings  JOIN districts ON districts.district_id = listings.listing_location  JOIN categories ON categories.category_id= listings.listing_category  JOIN release_type ON release_type.rtype_id = listings .rtype WHERE  is_featured= 'YES' LIMIT 5";
	return $query = $ci->db->query($sql)->result();


}
function check_exist_in_table($table,$field,$key){
	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $table WHERE $field='$key'";
	return $query = $ci->db->query($sql)->row();

}
function getDaysDifference($startDate, $endDate) {
    $startTimestamp = strtotime($startDate);
    $endTimestamp = strtotime($endDate);

    $difference = $endTimestamp - $startTimestamp;

    return floor($difference / (60 * 60 * 24));
}

 function calculateLateFeeAmount($paymentAmount, $daysOverdue) {
    $lateFeePercentage = 5;
    $daysPerCycle = 5;

    // Calculate the number of cycles (every 5 days)
    $cycles = intval($daysOverdue / $daysPerCycle);
if($cycles > 4){
    $real_cylce = 4;
}else{
    $real_cylce = $cycles;
}
    // Calculate the late fee
    $lateFee = ($lateFeePercentage / 100) * $paymentAmount * $real_cylce;

    return $lateFee;
}
function findFifthDayOfNextMonth($dateString) {
    // Create a DateTime object from the input date string
    $date = new DateTime($dateString);

    // Add one month to the current date
    $date->modify('+1 month');

    // Set the day of the month to 5
    $date->setDate($date->format('Y'), $date->format('m'), 5);

    // Format the result as 'Y-m-d'
    $result = $date->format('Y-m-d');

    return $result;
}
