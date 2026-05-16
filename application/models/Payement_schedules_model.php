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

    public function new_pay($loan_number,$pay_number,$amount, $date, $acrued=0)
	{
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
        $to_pay = $get_real_amount->amount - $get_real_amount->paid_amount ;

		// Check if bullet payment - use multiple checks to be safe
		$is_bullet = false;
		if (isset($get_real_amount->is_bullet_payment) && $get_real_amount->is_bullet_payment == 1) {
			$is_bullet = true;
		} else {
			// Also check loan product calculation type as fallback
			$this->db->select('lp.calculation_type');
			$this->db->from('loan l');
			$this->db->join('loan_products lp', 'l.loan_product = lp.loan_product_id');
			$this->db->where('l.loan_id', $loan_number);
			$product_check = $this->db->get()->row();
			if ($product_check && $product_check->calculation_type == 'Bullet Payment') {
				$is_bullet = true;
			}
		}

if($is_bullet)
{
	// For bullet payments, use the calculated payoff amount passed from the modal ($acrued parameter)
	// This is the amount shown to user after clicking "Calculate Pay-off Amount"
	$current_amount = (float)$amount;
	$calculated_payoff = (float)$acrued; // This is the payoff amount from the modal
	$previously_paid = (float)$get_real_amount->paid_amount;
	$total_paid_after = $current_amount + $previously_paid;

	// Simple logic:
	// - If payment amount >= calculated payoff: CLOSE the loan (full payment or overpayment)
	// - If payment amount < calculated payoff: DO NOT CLOSE (partial payment)
	$should_close = ($current_amount >= ($calculated_payoff - 0.01));

	// Log the calculation for debugging
	$log_data = array(
		'user_id' => $this->session->userdata('user_id'),
		'activity' => 'Bullet Payment - Loan: ' . $loan_number .
					  ' | Payment Amount: ' . number_format($current_amount, 2) .
					  ' | Calculated Payoff: ' . number_format($calculated_payoff, 2) .
					  ' | Will Close: ' . ($should_close ? 'YES' : 'NO'),
		'activity_cate' => 'loan_payment_debug'
	);
	$this->db->insert('activity_logger', $log_data);

	if($should_close)
	{
		// Full payment or overpayment - close the loan
		$data = array(
			'partial_paid'=>'NO',
			'status'=>'PAID',
			'paid_amount'=>$total_paid_after,
			'paid_date'=> $date,
		);
		$this->db->where('loan_id', $loan_number);
		$this->db->where('payment_number', $pay_number);
		$this->db->update($this->table,$data);
		$this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id'=>$pay_number+1));

		// Close the loan
		$this->db->where('loan_id', $loan_number)->
			update('loan',array('loan_status'=>'CLOSED', 'paid_off'=> 'Yes'));

		$transaction = array(
			'ref' => "BLT.".date('Y').date('m').date('d').'.'.rand(100,999),
			'loan_id' => $loan_number,
			'amount' => $current_amount,
			'payment_number' => $pay_number,
			'transaction_type' => 3,
			'payment_proof' => 'null',
			'added_by' => $this->session->userdata('user_id'),
			'date_stamp'=> $date
		);
		$this->db->insert('transactions',$transaction);
		return true;
	}
	else
	{
		// Partial payment - do NOT close the loan
		$data = array(
			'partial_paid'=>'YES',
			'status'=>'PARTIAL PAID',
			'paid_amount'=>$total_paid_after,
			'paid_date'=> $date
		);
		$this->db->where('loan_id', $loan_number);
		$this->db->where('payment_number', $pay_number);
		$this->db->update($this->table,$data);

		$transaction = array(
			'ref' => "BLT.".date('Y').date('m').date('d').'.'.rand(100,999),
			'loan_id' => $loan_number,
			'amount' => $current_amount,
			'payment_number' => $pay_number,
			'transaction_type' => 3,
			'payment_proof' => 'null',
			'added_by' => $this->session->userdata('user_id'),
			'date_stamp'=> $date
		);
		$this->db->insert('transactions',$transaction);

		// Do NOT close - partial payment
		return true;
	}
}

if(intval($to_pay) > intval($amount) )
{


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
            // Check if loan is fully paid and close it
            $this->check_and_close_loan_if_paid($loan_number);
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
            // Check if loan is fully paid and close it
            $this->check_and_close_loan_if_paid($loan_number);
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
            // Check if loan is fully paid and close it
            $this->check_and_close_loan_if_paid($loan_number);
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

    /**
     * Check if loan is fully paid and close it automatically
     *
     * @param int $loan_id The loan ID
     * @return bool True if loan was closed, false otherwise
     */
    function check_and_close_loan_if_paid($loan_id) {
        // Get loan details
        $loan = $this->db->where('loan_id', $loan_id)->get('loan')->row();
        if (!$loan || $loan->loan_status == 'CLOSED') {
            return false;
        }

        // Get loan product to check calculation type
        $loan_product = $this->db->where('loan_product_id', $loan->loan_product)->get('loan_products')->row();
        $is_bullet = ($loan_product && $loan_product->calculation_type == 'Bullet Payment');

        // Get total amount paid
        $this->db->select_sum('paid_amount', 'total_paid');
        $this->db->where('loan_id', $loan_id);
        $total_paid_result = $this->db->get($this->table)->row();
        $total_paid = $total_paid_result->total_paid ?? 0;

        // For bullet loans, calculate actual payoff amount (includes accrued interest)
        if ($is_bullet) {
            $CI =& get_instance();
            $CI->load->model('Loan_model');

            // Calculate actual payoff amount
            $payoff_result = $CI->Loan_model->calculateBulletPayoff($loan_id, date('Y-m-d'));

            if ($payoff_result && isset($payoff_result['total_payoff'])) {
                $total_due = $payoff_result['total_payoff'];
            } else {
                // Fallback to scheduled amount if calculation fails
                $this->db->select_sum('amount', 'total_due');
                $this->db->where('loan_id', $loan_id);
                $total_due_result = $this->db->get($this->table)->row();
                $total_due = $total_due_result->total_due ?? 0;
            }
        } else {
            // For non-bullet loans, use scheduled amount
            $this->db->select_sum('amount', 'total_due');
            $this->db->where('loan_id', $loan_id);
            $total_due_result = $this->db->get($this->table)->row();
            $total_due = $total_due_result->total_due ?? 0;
        }

        // Calculate remaining balance
        $remaining = $total_due - $total_paid;

        // Close loan ONLY if fully paid (remaining is 0 or negative)
        // Use a small tolerance (0.01) to handle floating point issues
        // IMPORTANT: Do NOT close based on unpaid_count alone - must check actual balance
        if ($remaining <= 0.01) {
            // Mark all remaining schedules as PAID
            $this->db->where('loan_id', $loan_id);
            $this->db->where('status !=', 'PAID');
            $this->db->update($this->table, array(
                'status' => 'PAID',
                'partial_paid' => 'NO',
                'paid_date' => date('Y-m-d')
            ));

            // Close the loan
            $this->db->where('loan_id', $loan_id);
            $this->db->update('loan', array(
                'loan_status' => 'CLOSED',
                'paid_off' => 'Yes'
            ));

            // Log the auto-close
            $logger = array(
                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'Loan auto-closed (fully paid), Loan ID: ' . $loan_id .
                              ' Total Due: ' . number_format($total_due, 2) .
                              ' Total Paid: ' . number_format($total_paid, 2),
                'activity_cate' => 'loan_closure'
            );
            $this->db->insert('activity_logger', $logger);

            return true;
        }

        return false;
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
        $this->db->select("payement_schedules.*, loan.*, loan.customer_type,
                          individual_customers.Firstname as ind_firstname,
                          individual_customers.Lastname as ind_lastname,
                          individual_customers.id as ind_id")
            ->from($this->table)
            ->join('loan','loan.loan_id = payement_schedules.loan_id')
            ->join('individual_customers','individual_customers.id = payement_schedules.customer AND loan.customer_type = "individual"', 'left')
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

	// Add these methods to the Payement_schedules_model.php file

	/**
	 * Get all unpaid payment schedules for a loan
	 *
	 * @param int $loan_id The loan ID
	 * @return array Array of unpaid schedule objects
	 */
	/**
	 * Get all unpaid payment schedules for a loan
	 *
	 * @param int $loan_id The loan ID
	 * @return array Array of unpaid schedule objects
	 */
	function get_unpaid_schedules($loan_id) {
		$this->db->where('loan_id', $loan_id);
		$this->db->group_start();
		$this->db->where('status', 'NOT PAID');
		$this->db->or_where('partial_paid', 'YES');
		$this->db->group_end();
		$this->db->order_by('payment_number', 'ASC');
		return $this->db->get('payement_schedules')->result();
	}
	function get_payment_by_l($loan_id, $payment_no) {
		$this->db->where('loan_id', $loan_id);
		$this->db->where('payment_number', $payment_no);

		return $this->db->get('payement_schedules')->row();
	}

	/**
	 * Get the last paid payment schedule for a loan
	 *
	 * @param int $loan_id The loan ID
	 * @return object|null The last paid schedule object or null if none found
	 */
	/**
	 * Get the last paid payment schedule for a loan
	 *
	 * @param int $loan_id The loan ID
	 * @return object|null The last paid schedule object or null if none found
	 */
	function get_last_paid_schedule($loan_id) {
		$this->db->where('loan_id', $loan_id);
		$this->db->where('status', 'PAID');
		// Order by paid_date instead of payment_schedule to get the most recent payment
		$this->db->order_by('paid_date', 'DESC');
		// Also order by payment_number as a secondary sort in case multiple payments were made on the same day
		$this->db->order_by('payment_number', 'DESC');
		$this->db->limit(1);
		$result = $this->db->get('payement_schedules')->row();

		return $result;
	}
	/**
	 * Get the remaining principal for a loan
	 *
	 * @param int $loan_id The loan ID
	 * @return float The remaining principal amount
	 */
	function get_remaining_principal($loan_id) {
		$this->db->select_sum('principal');
		$this->db->where('loan_id', $loan_id);
		$this->db->where('status', 'NOT PAID');
		$result = $this->db->get('payement_schedules')->row();

		$principal = isset($result->principal) ? $result->principal : 0;

		// Also check partial paid schedules
		$this->db->select('principal, paid_amount');
		$this->db->where('loan_id', $loan_id);
		$this->db->where('partial_paid', 'YES');
		$partial_paid = $this->db->get('payement_schedules')->result();

		foreach ($partial_paid as $schedule) {
			// Calculate the proportion of principal that's been paid
			$payment_ratio = $schedule->paid_amount / $schedule->amount;
			$principal_paid = $schedule->principal * $payment_ratio;

			// Add the remaining principal
			$principal += ($schedule->principal - $principal_paid);
		}

		return $principal;
	}

	// Collection sheet - unified payment schedule report
	function collection_sheet($period_type, $from_date = '', $to_date = '', $loan_id = 'All') {
		date_default_timezone_set("Africa/Blantyre");

		$this->db->select("payement_schedules.*, loan.*, loan.customer_type,
						  individual_customers.Firstname as ind_firstname,
						  individual_customers.Lastname as ind_lastname,
						  individual_customers.id as ind_id")
			->from($this->table)
			->join('loan','loan.loan_id = payement_schedules.loan_id')
			->join('individual_customers','individual_customers.id = payement_schedules.customer AND loan.customer_type = "individual"', 'left')
			->where('payement_schedules.status','NOT PAID')
			->where('loan.loan_status','ACTIVE');

		// Apply date filters based on period type
		switch($period_type) {
			case 'daily':
				$curr_date = date('Y-m-d');
				$this->db->where('DATE(payment_schedule)', $curr_date);
				break;

			case 'weekly':
				$date_start = strtotime('last Sunday');
				$week_start = date('Y-m-d', $date_start);
				$date_end = strtotime('next Sunday');
				$week_end = date('Y-m-d', $date_end);
				$this->db->where('payment_schedule >=', $week_start);
				$this->db->where('payment_schedule <=', $week_end);
				break;

			case 'monthly':
				$curr_month = date('m');
				$curr_year = date('Y');
				$this->db->where('MONTH(payment_schedule)', $curr_month);
				$this->db->where('YEAR(payment_schedule)', $curr_year);
				break;

			case 'custom':
				if(!empty($from_date) && !empty($to_date)){
					$this->db->where('payment_schedule BETWEEN "'. date('Y-m-d', strtotime($from_date)). '" and "'. date('Y-m-d', strtotime($to_date)).'"');
				}
				break;

			default:
				// All upcoming payments (no date filter)
				break;
		}

		// Filter by specific loan if provided
		if($loan_id != 'All'){
			$this->db->where('payement_schedules.loan_id', $loan_id);
		}

		$this->db->order_by('payment_schedule', 'ASC');
		return $this->db->get()->result();
	}

	public function calculate_payoff_amount($loan_id, $date)
	{
		$this->db->select('SUM(amount - paid_amount) AS overdue_amount')
				 ->from($this->table)
				 ->where('loan_id', $loan_id)
				 ->where('status !=', 'PAID')
				 ->where('payment_schedule <=', $date);
		$overdue        = $this->db->get()->row();
		$overdue_amount = $overdue->overdue_amount ? (float)$overdue->overdue_amount : 0;

		$this->db->select('SUM(principal) AS future_principal, SUM(interest) AS interest_waived')
				 ->from($this->table)
				 ->where('loan_id', $loan_id)
				 ->where('status', 'NOT PAID')
				 ->where('payment_schedule >', $date);
		$future          = $this->db->get()->row();
		$future_principal = $future->future_principal ? (float)$future->future_principal : 0;
		$interest_waived  = $future->interest_waived  ? (float)$future->interest_waived  : 0;

		return [
			'overdue_amount'   => round($overdue_amount, 2),
			'future_principal' => round($future_principal, 2),
			'interest_waived'  => round($interest_waived, 2),
			'total_payoff'     => round($overdue_amount + $future_principal, 2),
		];
	}

	public function payoff_loan($loan_id, $amount, $date)
	{
		// Snapshot interest_waived before making changes (for the activity log)
		$breakdown       = $this->calculate_payoff_amount($loan_id, $date);
		$interest_waived = $breakdown['interest_waived'];

		// Mark overdue schedules (due <= date) as PAID; set paid_amount = the full schedule amount
		$this->db->set('status',       'PAID');
		$this->db->set('paid_amount',  'amount', FALSE); // FALSE = treat 'amount' as a column reference
		$this->db->set('paid_date',    $date);
		$this->db->set('partial_paid', 'NO');
		$this->db->where('loan_id',    $loan_id);
		$this->db->where('status !=',  'PAID');
		$this->db->where('payment_schedule <=', $date);
		$this->db->update($this->table);

		// Mark future schedules (due > date) as PAID; zero interest, set paid_amount = principal only
		$this->db->set('status',       'PAID');
		$this->db->set('interest',     0);
		$this->db->set('paid_amount',  'principal', FALSE); // FALSE = column reference
		$this->db->set('paid_date',    $date);
		$this->db->set('partial_paid', 'NO');
		$this->db->where('loan_id',    $loan_id);
		$this->db->where('status !=',  'PAID');
		$this->db->where('payment_schedule >', $date);
		$this->db->update($this->table);

		// Close the loan
		$this->db->where('loan_id', $loan_id)->update('loan', [
			'loan_status' => 'CLOSED',
			'paid_off'    => 'Yes',
		]);

		// Log the settlement
		$loan          = $this->db->where('loan_id', $loan_id)->get('loan')->row();
		$currency      = $this->db->where('currency_id', $loan->currency)->get('currencies')->row();
		$currency_code = $currency ? $currency->currency_code : '';

		$this->db->insert('activity_logger', [
			'user_id'       => $this->session->userdata('user_id'),
			'activity'      => 'Early payoff settlement — Loan ID: ' . $loan_id .
							   ' | Settled: ' . number_format((float)$amount, 2) .
							   ' | ' . $currency_code . ' ' . number_format($interest_waived, 2) . ' interest waived',
			'activity_cate' => 'loan_closure',
		]);

		return true;
	}
}

