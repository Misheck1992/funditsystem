<?php

class Ussd extends CI_Controller {
    public function __construct(){
          parent::__construct();
           $this->load->model('Individual_customers_model');
    }
    
    public function get_accounts($value){
        $my_num = "+265".$value;
        $get_number = get_by_id('individual_customers','PhoneNumber',$my_num);
     
        $r = get_all_by_id('loan','loan_customer='.$get_number->id,'loan_id');
             echo json_encode($r);
    }
    
}