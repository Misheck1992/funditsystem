<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payement_schedules_model extends CI_Model
{

    public $table = 'payement_schedules';
    public $id = '';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // get all
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }
    function get_all_by_id($id)
    {
        $this->db->select('*');
        $this->db->order_by($this->id, $this->order);
        $this->db->join('loan','loan.loan_id = payement_schedules.loan_id');
        $this->db->where('payement_schedules.loan_id',$id);
        return $this->db->get($this->table)->result();
    }
    function get_next($pay_number,$id)
    {

        $this->db->where('loan_id',$id);
        $this->db->where('payment_number',$pay_number);
        return $this->db->get($this->table)->row();
    }

    // get data by id
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }

    public function new_pay($loan_number,$pay_number,$amount, $date){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
        $to_pay = $get_real_amount->amount - $get_real_amount->paid_amount ;


        if(intval($to_pay) > intval($amount) ){


            $final_paid = $amount + $get_real_amount->paid_amount ;
            $data = array(
                'partial_paid'=>'YES',

                'paid_amount'=>$final_paid,
                'paid_date'=> $date
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);

            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id'),
                'date_stamp'=> $date

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        elseif(intval($amount) > intval($to_pay)) {

            $our_amount = $amount;
//get all loans
            $this->db->select("*")
                ->from($this->table)

                ->where('loan_id', $loan_number)
                ->where('status', 'NOT PAID');
//                ->or_where('partial_paid', 'NO');
            $this->db->order_by('payment_number', 'ASC');

            $result = $this->db->get()->result();

            foreach ($result as $lr){




                if($our_amount < ($lr->amount-$lr->paid_amount) ){
                    $data = array(
                        'partial_paid'=>'YES',
                        'paid_amount'=>$our_amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number',  $lr->payment_number);
                    $this->db->update($this->table,$data);

                    $transaction = array(
                        'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                        'loan_id' => $loan_number,
                        'amount' => $our_amount ,
                        'payment_number' =>  $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;
                }
                elseif($our_amount==($lr->amount-$lr->paid_amount)){
                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$our_amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $lr->payment_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$lr->payment_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($lr->payment_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                        'loan_id' => $loan_number,
                        'amount' => $our_amount,
                        'payment_number' => $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;
                }
                else{
                    $our_amount = $our_amount - ($lr->amount-$lr->paid_amount);
                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$lr->amount,
                        'paid_date'=> $date
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $lr->payment_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$lr->payment_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($lr->payment_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                        'loan_id' => $loan_number,
                        'amount' => $lr->amount,
                        'payment_number' => $lr->payment_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'added_by' => $this->session->userdata('user_id'),
                        'date_stamp'=> $date

                    );
                    $this->db->insert('transactions',$transaction);
                }

            }
            return true;

        }

        elseif(intval($to_pay) === intval($amount)){

            $new_to_pay = $amount;
            $final_paid = $new_to_pay + $get_real_amount->paid_amount ;

            $data = array(
                'partial_paid'=>'NO',
                'status'=>'PAID',
                'paid_amount'=>$final_paid,
                'paid_date'=> $date
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
            $count_schedules = $this->count_payments($loan_number);
            if(intval($count_schedules) == intval($pay_number)){
                $this->db->where('loan_id', $loan_number)->
                update('loan',array('loan_status'=>'CLOSED'));
            }
            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'added_by' => $this->session->userdata('user_id'),
                'date_stamp'=> $date

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        else{

        }
    }
    public function new_pay_old($loan_number,$pay_number,$amount,$paid_date){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
        $to_pay = $get_real_amount->amount - $get_real_amount->paid_amount ;



        if( $get_real_amount->partial_paid ='YES' ){
            $balanceDeposit=0;
            $new_to_pay = $amount;
            $arldypaid=$get_real_amount->paid_amount ;
            if(intval($to_pay)<intval($amount)){
                $balanceDeposit= $amount-$to_pay;
                $tid="TR-S".rand(100,9999).date('Y').date('m').date('d');
                $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
                $loan_account = get_by_id('loan', 'loan_id', $loan_number);
                $teller_account = $get_account->account;

                $account = $loan_account->loan_number;
                $pamount = $balanceDeposit;
                $mode = 'deposit';

                $res =	$this->Account_model->cash_transaction($teller_account,$account,$pamount,$mode,$tid,$paid_date);

                if($res){

                    $totaltopay=$arldypaid+$to_pay;

                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$totaltopay
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $pay_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($pay_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                        'loan_id' => $loan_number,
                        'amount' => $totaltopay,
                        'payment_number' => $pay_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'date_stamp' => $paid_date,
                        'added_by' => $this->session->userdata('user_id')

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;



                }
                else{
                    return false;
                }




            }
            elseif(intval($to_pay)==intval($amount)){


                $totaltopay=$arldypaid+$to_pay;

                $data = array(
                    'partial_paid'=>'NO',
                    'status'=>'PAID',
                    'paid_amount'=>$totaltopay
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $pay_number);
                $this->db->update($this->table,$data);
                $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
                $count_schedules = $this->count_payments($loan_number);
                if(intval($count_schedules) == intval($pay_number)){
                    $this->db->where('loan_id', $loan_number)->
                    update('loan',array('loan_status'=>'CLOSED'));
                }
                $transaction = array(
                    'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                    'loan_id' => $loan_number,
                    'amount' => $totaltopay,
                    'payment_number' => $pay_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);
                return true;



            }
            else{
                $totaltopay=$arldypaid+$to_pay;
                $data = array(
                    'partial_paid'=>'YES',
                    'paid_amount'=>$totaltopay
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $pay_number);
                $this->db->update($this->table,$data);

                $transaction = array(
                    'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                    'loan_id' => $loan_number,
                    'amount' => $totaltopay,
                    'payment_number' => $pay_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);
                return true;



            }





        }


        elseif(intval($to_pay) > intval($amount)  ){


            $final_paid = $amount + $get_real_amount->paid_amount ;
            $data = array(
                'partial_paid'=>'YES',
                'paid_amount'=>$final_paid
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);

            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'date_stamp' => $paid_date,
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        elseif(intval($to_pay) === intval($amount) ){


            $new_to_pay = $amount;
            $final_paid = $new_to_pay + $get_real_amount->paid_amount ;

            $data = array(
                'partial_paid'=>'NO',
                'status'=>'PAID',
                'paid_amount'=>$final_paid
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
            $count_schedules = $this->count_payments($loan_number);
            if(intval($count_schedules) == intval($pay_number)){
                $this->db->where('loan_id', $loan_number)->
                update('loan',array('loan_status'=>'CLOSED'));
            }
            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'date_stamp' => $paid_date,
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        /*
        elseif( $get_real_amount->partial_paid ='YES' ){
            $balanceDeposit=0;
            $new_to_pay = $amount;
            $arldypaid=$get_real_amount->paid_amount ;
            if(intval($to_pay)<intval($amount)){
                $balanceDeposit= $amount-$arldypaid;
                $tid="TR-S".rand(100,9999).date('Y').date('m').date('d');
                $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
                $loan_account = get_by_id('loan', 'loan_id', $loan_number);
                $teller_account = $get_account->account;

                $account = $loan_account->loan_number;
                $pamount = $balanceDeposit;
                $mode = 'deposit';

                $res =	$this->Account_model->cash_transaction($teller_account,$account,$pamount,$mode,$tid,$paid_date);

                if($res){

                    $totaltopay=$arldypaid+$to_pay;

                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$totaltopay
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $pay_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($pay_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                        'loan_id' => $loan_number,
                        'amount' => $totaltopay,
                        'payment_number' => $pay_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'date_stamp' => $paid_date,
                        'added_by' => $this->session->userdata('user_id')

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;



                }
                else{
                    return false;
                }




            }
            elseif(intval($to_pay)==intval($amount)){


                $totaltopay=$arldypaid+$to_pay;

                $data = array(
                    'partial_paid'=>'NO',
                    'status'=>'PAID',
                    'paid_amount'=>$totaltopay
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $pay_number);
                $this->db->update($this->table,$data);
                $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
                $count_schedules = $this->count_payments($loan_number);
                if(intval($count_schedules) == intval($pay_number)){
                    $this->db->where('loan_id', $loan_number)->
                    update('loan',array('loan_status'=>'CLOSED'));
                }
                $transaction = array(
                    'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                    'loan_id' => $loan_number,
                    'amount' => $totaltopay,
                    'payment_number' => $pay_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);
                return true;



            }
            else{
                $totaltopay=$arldypaid+$to_pay;
                $data = array(
                    'partial_paid'=>'YES',
                    'paid_amount'=>$totaltopay
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $pay_number);
                $this->db->update($this->table,$data);

                $transaction = array(
                    'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                    'loan_id' => $loan_number,
                    'amount' => $totaltopay,
                    'payment_number' => $pay_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);
                return true;



            }





        }
        */
        elseif(intval($to_pay) < intval($amount) ){
            $one_ref = "GF.".date('Y').date('m').date('d').'.'.rand(100,999);
            $the_current_payment_number = $pay_number;
            $current_payment_balance = $to_pay;
            $the_current_amount = $amount;


            $this->db->select("*")->from($this->table);
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $the_current_payment_number);
            $get_real_amount = $this->db->get()->row();
//      echo $loan_number."<br>";
//                echo $get_real_amount->amount."<br>";
//                echo $the_current_amount;
//                exit();
            if($get_real_amount->amount <= $the_current_amount){

                $data = array(
                    'status'=>'PAID',
                    'paid_amount'=>$get_real_amount->amount
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $the_current_payment_number);
                $this->db->update($this->table,$data);

                $transaction = array(
                    'ref' => $one_ref,
                    'loan_id' => $loan_number,
                    'amount' => $get_real_amount->amount-$get_real_amount->paid_amount,
                    'payment_number' => $the_current_payment_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);

                $the_current_amount = $the_current_amount - ($get_real_amount->amount - $get_real_amount->paid_amount);
                $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$the_current_payment_number+1));
                $count_schedules = $this->count_payments($loan_number);
                if(intval($count_schedules) == intval($the_current_payment_number)){
                    $this->db->where('loan_id', $loan_number)->
                    update('loan',array('loan_status'=>'CLOSED'));
                }

            }else{
                $data = array(
                    'partial_paid'=>'YES',

                    'paid_amount'=>$the_current_amount
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $pay_number);
                $this->db->update($this->table,$data);

                $transaction = array(
                    'ref' => $one_ref,
                    'loan_id' => $loan_number,
                    'amount' => $the_current_amount,
                    'payment_number' => $the_current_payment_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);
                $the_current_amount = 0;

            }




            return true;

        }
    }

    public function new_late_pay($loan_number,$pay_number,$amount,$paid_date)
    {
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
        $to_pay = $get_real_amount->amount - $get_real_amount->paid_amount ;

        if( $get_real_amount->partial_paid ='YES' ){
            $balanceDeposit=0;
            $new_to_pay = $amount;
            $arldypaid=$get_real_amount->paid_amount ;
            if(intval($to_pay)<intval($amount)){
                $balanceDeposit= $amount-$to_pay;
                $tid="TR-S".rand(100,9999).date('Y').date('m').date('d');
                $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
                $loan_account = get_by_id('loan', 'loan_id', $loan_number);
                $teller_account = $get_account->account;

                $account = $loan_account->loan_number;
                $pamount = $balanceDeposit;
                $mode = 'deposit';

                $res =	$this->Account_model->cash_transaction($teller_account,$account,$pamount,$mode,$tid,$paid_date);

                if($res){

                    $totaltopay=$arldypaid+$to_pay;

                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$totaltopay
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $pay_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($pay_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                        'loan_id' => $loan_number,
                        'amount' => $totaltopay,
                        'payment_number' => $pay_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'date_stamp' => $paid_date,
                        'added_by' => $this->session->userdata('user_id')

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;



                }
                else{
                    return false;
                }




            }
            elseif(intval($to_pay)==intval($amount)){


                $totaltopay=$arldypaid+$to_pay;

                $data = array(
                    'partial_paid'=>'NO',
                    'status'=>'PAID',
                    'paid_amount'=>$totaltopay
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $pay_number);
                $this->db->update($this->table,$data);
                $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
                $count_schedules = $this->count_payments($loan_number);
                if(intval($count_schedules) == intval($pay_number)){
                    $this->db->where('loan_id', $loan_number)->
                    update('loan',array('loan_status'=>'CLOSED'));
                }
                $transaction = array(
                    'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                    'loan_id' => $loan_number,
                    'amount' => $totaltopay,
                    'payment_number' => $pay_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);
                return true;



            }
            else{
                $totaltopay=$arldypaid+$to_pay;
                $data = array(
                    'partial_paid'=>'YES',
                    'paid_amount'=>$totaltopay
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $pay_number);
                $this->db->update($this->table,$data);

                $transaction = array(
                    'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                    'loan_id' => $loan_number,
                    'amount' => $totaltopay,
                    'payment_number' => $pay_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);
                return true;



            }





        }



        elseif(intval($to_pay) > intval($amount)  ){


            $final_paid = $amount + $get_real_amount->paid_amount ;
            $data = array(
                'partial_paid'=>'YES',

                'paid_amount'=>$final_paid
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);

            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'date_stamp' => $paid_date,
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        elseif(intval($to_pay) === intval($amount)  ){


            $new_to_pay = $amount;
            $final_paid = $new_to_pay + $get_real_amount->paid_amount ;

            $data = array(
                'partial_paid'=>'NO',
                'status'=>'PAID',
                'paid_amount'=>$final_paid
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
            $count_schedules = $this->count_payments($loan_number);
            if(intval($count_schedules) == intval($pay_number)){
                $this->db->where('loan_id', $loan_number)->
                update('loan',array('loan_status'=>'CLOSED'));
            }
            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $final_paid,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'date_stamp' => $paid_date,
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);
            return true;

        }
        /*
        elseif( $get_real_amount->partial_paid ='YES' ){
            $balanceDeposit=0;
            $new_to_pay = $amount;
            $arldypaid=$get_real_amount->paid_amount ;
            if(intval($to_pay)<intval($amount)){
                $balanceDeposit= $amount-$arldypaid;
                $tid="TR-S".rand(100,9999).date('Y').date('m').date('d');
                $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
                $loan_account = get_by_id('loan', 'loan_id', $loan_number);
                $teller_account = $get_account->account;

                $account = $loan_account->loan_number;
                $pamount = $balanceDeposit;
                $mode = 'deposit';

                $res =	$this->Account_model->cash_transaction($teller_account,$account,$pamount,$mode,$tid,$paid_date);

                if($res){

                    $totaltopay=$arldypaid+$to_pay;

                    $data = array(
                        'partial_paid'=>'NO',
                        'status'=>'PAID',
                        'paid_amount'=>$totaltopay
                    );
                    $this->db->where('loan_id', $loan_number);
                    $this->db->where('payment_number', $pay_number);
                    $this->db->update($this->table,$data);
                    $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
                    $count_schedules = $this->count_payments($loan_number);
                    if(intval($count_schedules) == intval($pay_number)){
                        $this->db->where('loan_id', $loan_number)->
                        update('loan',array('loan_status'=>'CLOSED'));
                    }
                    $transaction = array(
                        'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                        'loan_id' => $loan_number,
                        'amount' => $totaltopay,
                        'payment_number' => $pay_number,
                        'transaction_type' => 3,
                        'payment_proof' => 'null',
                        'date_stamp' => $paid_date,
                        'added_by' => $this->session->userdata('user_id')

                    );
                    $this->db->insert('transactions',$transaction);
                    return true;



                }
                else{
                    return false;
                }




            }
            elseif(intval($to_pay)==intval($amount)){


                $totaltopay=$arldypaid+$to_pay;

                $data = array(
                    'partial_paid'=>'NO',
                    'status'=>'PAID',
                    'paid_amount'=>$totaltopay
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $pay_number);
                $this->db->update($this->table,$data);
                $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
                $count_schedules = $this->count_payments($loan_number);
                if(intval($count_schedules) == intval($pay_number)){
                    $this->db->where('loan_id', $loan_number)->
                    update('loan',array('loan_status'=>'CLOSED'));
                }
                $transaction = array(
                    'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                    'loan_id' => $loan_number,
                    'amount' => $totaltopay,
                    'payment_number' => $pay_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);
                return true;



            }
            else{
                $totaltopay=$arldypaid+$to_pay;
                $data = array(
                    'partial_paid'=>'YES',
                    'paid_amount'=>$totaltopay
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $pay_number);
                $this->db->update($this->table,$data);

                $transaction = array(
                    'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                    'loan_id' => $loan_number,
                    'amount' => $totaltopay,
                    'payment_number' => $pay_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);
                return true;



            }





        }
            /*/
        elseif(intval($to_pay) < intval($amount) ){
            $one_ref = "GF.".date('Y').date('m').date('d').'.'.rand(100,999);
            $the_current_payment_number = $pay_number;
            $current_payment_balance = $to_pay;
            $the_current_amount = $amount;


            $this->db->select("*")->from($this->table);
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $the_current_payment_number);
            $get_real_amount = $this->db->get()->row();
//      echo $loan_number."<br>";
//                echo $get_real_amount->amount."<br>";
//                echo $the_current_amount;
//                exit();
            if($get_real_amount->amount <= $the_current_amount){

                $data = array(
                    'status'=>'PAID',
                    'paid_amount'=>$get_real_amount->amount
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $the_current_payment_number);
                $this->db->update($this->table,$data);

                $transaction = array(
                    'ref' => $one_ref,
                    'loan_id' => $loan_number,
                    'amount' => $get_real_amount->amount-$get_real_amount->paid_amount,
                    'payment_number' => $the_current_payment_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);

                $the_current_amount = $the_current_amount - ($get_real_amount->amount - $get_real_amount->paid_amount);
                $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$the_current_payment_number+1));
                $count_schedules = $this->count_payments($loan_number);
                if(intval($count_schedules) == intval($the_current_payment_number)){
                    $this->db->where('loan_id', $loan_number)->
                    update('loan',array('loan_status'=>'CLOSED'));
                }

            }else{
                $data = array(
                    'partial_paid'=>'YES',

                    'paid_amount'=>$the_current_amount
                );
                $this->db->where('loan_id', $loan_number);
                $this->db->where('payment_number', $pay_number);
                $this->db->update($this->table,$data);

                $transaction = array(
                    'ref' => $one_ref,
                    'loan_id' => $loan_number,
                    'amount' => $the_current_amount,
                    'payment_number' => $the_current_payment_number,
                    'transaction_type' => 3,
                    'payment_proof' => 'null',
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions',$transaction);
                $the_current_amount = 0;

            }




            return true;

        }
    }

    public function finish_pay($loan_number,$pay_number,$amount, $paid_date){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);


        $data = array(
            'partial_paid'=>'NO',
            'status'=>'PAID',
            'paid_amount'=>$amount
        );
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $this->db->update($this->table,$data);
        $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
        $this->db->where('loan_id', $loan_number)->
        update('loan',array('loan_status'=>'CLOSED'));
        $transaction = array(
            'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
            'loan_id' => $loan_number,
            'amount' => $amount,
            'payment_number' => $pay_number,
            'transaction_type' => 3,
            'payment_proof' => 'null',
            'date_stamp' => $paid_date,
            'added_by' => $this->session->userdata('user_id')

        );
        $this->db->insert('transactions',$transaction);
        return true;

    }
    function count_payments($loan_number){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        return $this->db->count_all_results();
    }
    function count_full_paid_payments($loan_id){
        $this->db->select("*")->from($this->table);
        $this->db->where("(status = 'PAID') AND loan_id = $loan_id");
        $count = $this->db->count_all_results();
        return  $count ;
    }
    function count_partial_paid_payments($loan_id){
        $this->db->select("*")->from($this->table);
        $this->db->where("(partial_paid = 'YES') AND loan_id = $loan_id");
        $count = $this->db->count_all_results();
        return  $count ;
    }
    function pay($loan_number,$pay_number,$amount,$paid_date)
    {
//get payment id
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
        if($get_real_amount->partial_paid == 'Yes'){
            $new_paid_bal = $get_real_amount->amount - $get_real_amount->paid_amount;
            $data = array(
                'partial_paid'=>'Yes',
                'paid_amount'=>$amount
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
//            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $amount,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'date_stamp' => $paid_date,
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);

        }else{

        }
        if($get_real_amount->amount == $amount){
            $data = array(
                'status'=>'PAID',
                'paid_amount'=>$amount
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $amount,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'date_stamp' => $paid_date,
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);
        }
        else{
            $data = array(
                'partial_paid'=>'Yes',
                'paid_amount'=>$amount
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
//            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));
            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $amount,
                'payment_number' => $pay_number,
                'transaction_type' => 3,
                'payment_proof' => 'null',
                'date_stamp' => $paid_date,
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);
        }



        return true;
    }
    public function sum_interests($from,$to){
        $this->db->select('SUM(interest) as interest');
        $this->db->from('payement_schedules')->where('status','PAID');

        // $this->db->join('lend_payments','lend_payments.borrower_loan_id=lend_borrower_loans.id');

        if($from !="" && $to !=""){
            $this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

        }

        return $this->db->get()->row();
    }
    public function bad_debits($from,$to){
        $this->db->select('SUM(principal) as principal')->join('loan','loan.loan_id=payement_schedules.loan_id');
        $this->db->from('payement_schedules')->where('loan_status','DEFAULTED');

        // $this->db->join('lend_payments','lend_payments.borrower_loan_id=lend_borrower_loans.id');

        if($from !="" && $to !=""){
            $this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

        }

        return $this->db->get()->row();
    }
    function get_last_payment($loan_number)
    {
        //get last payment info
        $this->db->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->order_by('payment_schedule', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get();

        if ($result->num_rows() > 0) {

            return $result->row();
        }

        return FALSE;
    }
    function get_first_payment($loan_number)
    {
        //get last payment info
        $this->db->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->order_by('payment_schedule', 'ASC');
        $this->db->limit(1);
        $result = $this->db->get();

        if ($result->num_rows() > 0) {
            return $result->row();
        }

        return FALSE;
    }
    function pay_advance($loan_number,$amount,$arr,  $paid_date)
    {
        for($i=0;$i <count($arr);$i++){

            $data = array(
                'status'=>'PAID',
                'paid_amount'=>$amount
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $arr[$i]);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$arr[$i]+1));

            $transaction = array(
                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
                'loan_id' => $loan_number,
                'amount' => $amount,
                'payment_number' => $arr[$i],
                'transaction_type' => 3,
                'date_stamp' => $paid_date,
                'added_by' => $this->session->userdata('user_id')

            );
            $this->db->insert('transactions',$transaction);


        }


        return true;
    }

    // get total rows
    function total_rows($q = NULL) {
        $this->db->like('', $q);
        $this->db->or_like('id', $q);
        $this->db->or_like('customer', $q);
        $this->db->or_like('loan_id', $q);
        $this->db->or_like('payment_schedule', $q);
        $this->db->or_like('payment_number', $q);
        $this->db->or_like('amount', $q);
        $this->db->or_like('principal', $q);
        $this->db->or_like('interest', $q);
        $this->db->or_like('paid_amount', $q);
        $this->db->or_like('loan_balance', $q);
        $this->db->or_like('status', $q);
        $this->db->or_like('loan_date', $q);
        $this->db->or_like('paid_date', $q);
        $this->db->or_like('marked_due', $q);
        $this->db->or_like('marked_due_date', $q);
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('', $q);
        $this->db->or_like('id', $q);
        $this->db->or_like('customer', $q);
        $this->db->or_like('loan_id', $q);
        $this->db->or_like('payment_schedule', $q);
        $this->db->or_like('payment_number', $q);
        $this->db->or_like('amount', $q);
        $this->db->or_like('principal', $q);
        $this->db->or_like('interest', $q);
        $this->db->or_like('paid_amount', $q);
        $this->db->or_like('loan_balance', $q);
        $this->db->or_like('status', $q);
        $this->db->or_like('loan_date', $q);
        $this->db->or_like('paid_date', $q);
        $this->db->or_like('marked_due', $q);
        $this->db->or_like('marked_due_date', $q);
        $this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    // update data
    function update1($id, $data)
    {
        $this->db->where('loan_id', $id);
        $this->db->update($this->table, $data);
    }
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
    }

    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }
//    arrears
    function  arrears($loan,$from,$to){
        $this->db->select("*")->from($this->table)
            ->join('loan','loan.loan_id = payement_schedules.loan_id')
            ->join('individual_customers','individual_customers.id = payement_schedules.customer')
            ->where('payment_schedule <',date('Y-m-d'))
            ->where('payement_schedules.status','NOT PAID')
            ->where('loan.loan_status','ACTIVE');
        if($from !="" && $to !=""){
            $this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

        }
        if($loan !="All"){
            $this->db->where('payement_schedules.loan_id',$loan);
        }
        return	$this->db->get()->result();
    }
    function  payment_today(){
        date_default_timezone_set("Africa/Blantyre");
        $date = new DateTime("now");

        $curr_date = $date->format('Y-m-d');


        $this->db->select("*")->from($this->table)
            ->join('loan','loan.loan_id = payement_schedules.loan_id')
            ->join('individual_customers','individual_customers.id = payement_schedules.customer')
            ->where('DATE(payment_schedule)',$curr_date)
            ->where('payement_schedules.status','NOT PAID')
            ->where('loan.loan_status','ACTIVE');

        return	$this->db->get()->result();
    }
    function  payment_month(){
        date_default_timezone_set("Africa/Blantyre");
        $date = new DateTime("now");

        $curr_date = $date->format('m');


        $this->db->select("*")->from($this->table)
            ->join('loan','loan.loan_id = payement_schedules.loan_id')
            ->join('individual_customers','individual_customers.id = payement_schedules.customer')
            ->where('MONTH(payment_schedule)',$curr_date)
            ->where('payement_schedules.status','NOT PAID')
            ->where('loan.loan_status','ACTIVE');

        return	$this->db->get()->result();
    }
    function  payment_week(){
        date_default_timezone_set("Africa/Blantyre");
        $date_start = strtotime('last Sunday');
        $week_start = date('Y-m-d', $date_start);
        $date_end = strtotime('next Sunday');
        $week_end = date('Y-m-d', $date_end);



        $this->db->select("*")->from($this->table)
            ->join('loan','loan.loan_id = payement_schedules.loan_id')
            ->join('individual_customers','individual_customers.id = payement_schedules.customer');
        $this->db->where('payment_schedule >=', $week_start);
        $this->db->where('payment_schedule <=', $week_end);
        $this->db->where('payement_schedules.status','NOT PAID')
            ->where('loan.loan_status','ACTIVE');

        return	$this->db->get()->result();
    }


    function get_filter_projection($from,$to)
    {

        $this->db->select_sum("paid_amount")
            ->join('loan','loan.loan_id = payement_schedules.loan_id')
            ->where('loan.loan_status','ACTIVE')
            ->where('status','PAID');

        $this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

        $this->db->order_by('payement_schedules.loan_id', 'DESC');
        $result = $this->db-> get($this->table)->row();
//		$result = $this->db-> get()->row();
        return array(

            'paid_amount'=>$result->paid_amount
        );
    }

    function get_filter_projections($from,$to)
    {

        $this->db->select_sum('amount')
            ->join('loan','loan.loan_id = payement_schedules.loan_id')
            ->where('loan.loan_status','ACTIVE');


        $this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

        $this->db->order_by('payement_schedules.loan_id', 'DESC');
        $result = $this->db-> get($this->table)->row();
        return array(
            'amount'=>$result->amount,

        );
    }

    function get_filter_projection_principal($from,$to)
    {
        $this->db->select_sum('principal')
            ->join('loan','loan.loan_id = payement_schedules.loan_id')
            ->where('loan.loan_status','ACTIVE');

        $this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

        $this->db->order_by('payement_schedules.loan_id', 'DESC');
        $result = $this->db-> get($this->table)->row();
        return array(
            'principal'=>$result->principal,

        );
    }

    function get_filter_projection_interest($from,$to)
    {
        $this->db->select_sum('interest')
            ->join('loan','loan.loan_id = payement_schedules.loan_id')
            ->where('loan.loan_status','ACTIVE');


        $this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

        $this->db->order_by('payement_schedules.loan_id', 'DESC');
        $result = $this->db-> get($this->table)->row();
        return array(
            'interest'=>$result->interest,

        );
    }
}

