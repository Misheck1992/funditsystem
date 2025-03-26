<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Loan_model extends CI_Model
{

	public $table = 'loan';
	public $table_d = array('loan','transactions','payement_schedules');
	public $id = 'loan_id';
	public $order = 'DESC';

	function __construct()
	{
		parent::__construct();
	}
    function calculate($amount, $months, $loan_id, $loan_date, $interest)
    {
        // Get loan parameters
        $this->db->where('loan_product_id', $loan_id);
        $loan = $this->db->get('loan_products')->row();

        // Check calculation type
        if ($loan->calculation_type === 'Reducing Balance') {
            return $this->calculateReducingBalance($amount, $months, $loan_id, $loan_date, $interest);
        } elseif ($loan->calculation_type === 'Straight Line') {
            return $this->calculateStraightLine($amount, $months, $loan, $loan_date, $interest);
        } else {
            return "Invalid calculation type.";
        }
    }

    private function calculateStraightLine($amount, $months, $loan, $loan_date, $interest)
    {
        // Straight Line logic implementation
        $table = '<div id="calculator"><h3>Loan Info</h3>';
        $table .= '<table border="1" class="table">';
        $table .= '<tr><td>Loan Name:</td><td>' . $loan->product_name . '</td></tr>';
        $table .= '<tr><td>Interest:</td><td>' . $interest . '%</td></tr>';
        $table .= '<tr><td>Terms:</td><td>' . $months . '</td></tr>';
        $table .= '<tr><td>Frequency:</td><td>Every ' . $loan->frequency . '</td></tr>';
        $table .= '</table>';

        // Interest per month (fixed)
        $total_interest = ($amount * ($interest / 100));
        $total_payment = $amount + $total_interest;
        $monthly_payment = $total_payment / $months;

        $table .= '<h3>Computation</h3>';
        $table .= '<table>';
        $table .= '<tr><td>Loan Amount:</td><td>' . $this->config->item('currency_symbol') . number_format($amount, 2, '.', ',') . '</td></tr>';
        $table .= '<tr><td>Total Interest:</td><td>' . $this->config->item('currency_symbol') . number_format($total_interest, 2, '.', ',') . '</td></tr>';
        $table .= '<tr><td>Total Payment:</td><td>' . $this->config->item('currency_symbol') . number_format($total_payment, 2, '.', ',') . '</td></tr>';
        $table .= '<tr><td>Monthly Payment:</td><td>' . $this->config->item('currency_symbol') . number_format($monthly_payment, 2, '.', ',') . '</td></tr>';
        $table .= '</table>';

        // Generate payment schedule
        $table .= '<table class="table">';
        $table .= '<tr>
        <th>Pmt</th>
        <th>Payment</th>
        <th>Principal</th>
        <th>Interest</th>
        <th>Balance</th>
    </tr>';

        $current_balance = $amount;
        for ($i = 1; $i <= $months; $i++) {
            $interest_payment = $total_interest / $months;
            $principal_payment = $monthly_payment - $interest_payment;
            $current_balance -= $principal_payment;

            $table .= '<tr>';
            $table .= '<td>' . $i . '</td>';
            $table .= '<td>' . number_format($monthly_payment, 2, '.', ',') . '</td>';
            $table .= '<td>' . number_format($principal_payment, 2, '.', ',') . '</td>';
            $table .= '<td>' . number_format($interest_payment, 2, '.', ',') . '</td>';
            $table .= '<td>' . number_format($current_balance > 0 ? $current_balance : 0, 2, '.', ',') . '</td>';
            $table .= '</tr>';
        }

        $table .= '</table></div>';

        return $table;
    }
    function calculateReducingBalance($amount, $months, $loan_id, $loan_date,$interest)
	{
		//get loan parameters
		$this->db->where('loan_product_id',$loan_id);
		$loan = $this->db->get('loan_products')->row();

		//divisor
		switch ($loan->frequency) {
			case 'Monthly':
				$divisor = 1;
				$days = 30;
				break;
			case '2 Weeks':
				$divisor = 2;
				$days = 15;
				break;
			case 'Weekly':
				$divisor = 4;
				$days = 7;
				break;
		}

		//interest
		$amount_interest = $amount * ($interest/100)/$divisor;

		//total payments applying interest
		$amount_total = $amount + $amount_interest * $months * $divisor;

		//payment per term
		$amount_term = number_format(round($amount / ($months * $divisor), 2) + $amount_interest, 2, '.', ',');


		$date = $loan_date;
		$i=($interest/100);


		$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);
		$monthly_payment1 = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);
		$current_balance = $amount;
		$current_balance1 = $amount;
		$payment_counter = 1;
		$total_interest = 0;
		$total_interest1=0;




		while($current_balance1 > 0) {
			//create rows


			$towards_interest1 = ($i/12)*$current_balance1;  //this calculates the portion of your monthly payment that goes towards interest

			if ($monthly_payment1 > $current_balance1){
				$monthly_payment1 = $current_balance1 + $towards_interest1;
			}


			$towards_balance1 = $monthly_payment1 - $towards_interest1;
			$total_interest1 = $total_interest1 + $towards_interest1;
			$current_balance1 = $current_balance1 - $towards_balance1;

		}

		//Loan info
		$table = '<div id="calculator"><h3>Loan Info</h3>';
		$table = $table . '<table border="1" class="table">';
		$table = $table . '<tr><td>Loan Name:</td><td>'.$loan->product_name.'</td></tr>';
		$table = $table . '<tr><td>Interest:</td><td>'.$interest.'%</td></tr>';
		$table = $table . '<tr><td>Terms:</td><td>'.$months.'</td></tr>';
		$table = $table . '<tr><td>Frequency:</td><td>Every '.$loan->frequency.' days</td></tr>';
		$table = $table . '</table>';
		$table = $table . '<h3>Computation</h3>';
		$table = $table . '<table>';
		$table = $table . '<tr><td>Loan Amount:</td><td> '.$this->config->item('currency_symbol') . number_format($amount, 2, '.', ',').'</td></tr>';
//        $table = $table . '<tr><td>Interest per First Month:</td><td> '.$this->config->item('currency_symbol') . $amount*$i.'</td></tr>';
		$table = $table . '<tr><td>Total interest:</td><td> '.$this->config->item('currency_symbol') .number_format(($total_interest1),2) .'</td></tr>';
		$table = $table . '<tr><td>Amount Per Term:</td><td> '.$this->config->item('currency_symbol') . number_format($monthly_payment,2).'</td></tr>';
		$table = $table . '<tr><td>Total Payment:</td><td> '.$this->config->item('currency_symbol') . number_format($total_interest1+$amount, 2, '.', ',').'</td></tr>';
		$table = $table . '</table>';

		//$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);


		$table = $table . '<table class="table" >

				<tr>
					<th width="30" align="center"><b>Pmt</b></th>
					<th width="60" align="center"><b>Payment</b></th>
					<th width="60" align="center"><b>Principal</b></th>
					<th width="60" align="center"><b>Interest</b></th>
					<th width="85" align="center"><b>Interest Paid</b></th>
					<th width="70" align="center"><b>Balance</b></th>
				</tr>	
			';



		$table = $table ."<tr>";
		$table = $table . "<td width='30'>0</td>";
		$table = $table . "<td width='60'>&nbsp;</td>";
		$table = $table . "<td width='60'>&nbsp;</td>";
		$table = $table . "<td width='60'>&nbsp;</td>";
		$table = $table . "<td width='85'>&nbsp;</td>";
		$table = $table . "<td width='70'>".round($amount,2)."</td>";
		$table = $table . "</tr>";

		while($current_balance > 0) {
			//create rows


			$towards_interest = ($i/12)*$current_balance;  //this calculates the portion of your monthly payment that goes towards interest

			if ($monthly_payment > $current_balance){
				$monthly_payment = $current_balance + $towards_interest;
			}


			$towards_balance = $monthly_payment - $towards_interest;
			$total_interest = $total_interest + $towards_interest;
			$current_balance = $current_balance - $towards_balance;


			// display row

			$table = $table . "<tr class='table_info'>";
			$table = $table . "<td>".$payment_counter."</td>";
			$table = $table ."<td>".round($monthly_payment,2)."</td>";
			$table = $table . "<td>".round($towards_balance,2)."</td>";
			$table = $table . "<td>".round($towards_interest,2)."</td>";
			$table = $table ."<td>".round($total_interest,2)."</td>";
			$table = $table ."<td>".round($current_balance,2)."</td>";
			$table = $table . "</tr>";


			$payment_counter++;


		}

		$table = $table . '</table></div>';

		return $table;
	}
	function add_loan_backup($loan_number,$lamount, $lmonths,$interest, $product_id, $ldate,$loan_customer,$customer_type,$worthness_file,$narration,$added_by,$method, $fee_amount,$currency)
	{
		//set Time Zone
		//date_default_timezone_set('Africa/Blantyre');
        $loan = $this->db->select("*")->from('loan_products')->where('loan_product_id',$product_id)->get()->row();
		$this->db->select('MAX(counter) as max_c')->where('loan_product',$product_id);
		$lid = $this->db->get('loan');
		$result = $lid->row();
		if(empty($result)){
		    $mxc = 0;
        }else{
            $mxc = $result->max_c;
        }
		$loanid= $loan->abbreviation.'000'.($mxc+1).'/'. date('y');
		$fcounter=$mxc+1;
		$amount = $lamount;
		$loan_date = $ldate;
		
		
		$months = $lmonths;
		//get loan parameters
		
		
		  $day = date('d', strtotime($ldate));
		        $month = date('m', strtotime($ldate));
		    	
		    	if($day<13 ){
		    	    
		    	    
		    	  
                    $dayToUpdate = 25; // The day to update to
                    $date=new DateTime($ldate);
                    
                 
                    
                     $date->modify("{$dayToUpdate}{$date->format('-m-Y')}"); // Modify the day using the modify() method
                    $customised_date = $date->format('Y-m-d'); // Get the updated date string in the desired format

		    	}
		    	
		    	else {


                    $newDay = 25; // The new day you want to set
                    $newMonth = $month + 1;

                    // The new month you want to set
                    if ($newMonth == 13) {


                        $date_str = strtotime($ldate); // the original date string
                        $date_timestamp = strtotime($date_str); // convert the date string to a Unix timestamp
                        $day = date('d', $date_timestamp); // extract the day from the timestamp
                        $month = date('m', $date_timestamp); // extract the month from the timestamp
                        $year = date('Y', $date_timestamp); // extract the year from the timestamp

// update the day, month, and year
                        $day = 25;
                        $month = 1;
                        $year += 1;

// create a new date string using the updated day, month, and year
                        $new_date_str = sprintf('%04d-%02d-%02d', $year, $month, $day);

                        $customised_date = $new_date_str ; // Get the updated date as a string in the format "YYYY-MM-DD"

                    } else {


                        $dateTime = new DateTime($ldate); // Create a new DateTime object with the original date
                        $dateTime->modify("$newDay-$newMonth-{$dateTime->format('Y')}"); // Update the day and month using the modify() method

                        $customised_date = $dateTime->format('Y-m-d'); // Get the updated date as a string in the format "YYYY-MM-DD"
                        //echo $date; // Output: 2023-06-20


                    }
		    	}
		if($method === 01){
		    $disbursed =  $fee_amount;
        }else{
		    $disbursed = 0;
        }

		//divisor
		switch ($loan->frequency) {
			case 'Monthly':
				$divisor = 1;
				$days = 30;
				break;
			case '2 Weeks':
				$divisor = 2;
				$days = 15;
				break;
			case 'Weekly':
				$divisor = 4;
				$days = 7;
				break;
		}

		//interest
		$amount_interest = $amount *( ($interest/100)*12);


		//total payments applying interest
		$amount_total = $amount + $amount_interest * $months * $divisor;

		//payment per term
		$amount_term = number_format(round($amount / ($months * $divisor), 2) + $amount_interest, 2, '.', '');

		$date = $loan_date;
		//$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);

		$i=($interest/100);

		$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);
		$monthly_payment1 = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);
		$current_balance = $amount;
		$current_balance1 = $amount;
		$payment_counter = 1;
		$ii=1;
		$total_interest = 0;
		$total_interest1=0;




		while($current_balance1 > 0) {
			//create rows


			$towards_interest1 = ($i/12)*$current_balance1;
			//this calculates the portion of your monthly payment that goes towards interest

			if ($monthly_payment1 > $current_balance1){
				$monthly_payment1 = $current_balance1 + $towards_interest1;
			}


			$towards_balance1 = $monthly_payment1 - $towards_interest1;
			$total_interest1 = $total_interest1 + $towards_interest1;
			$current_balance1 = $current_balance1 - $towards_balance1;

		}



		//additional info to be insert


		$data = array(
			'loan_number'=>$loan_number,
			'loan_product'=>$product_id,
			'loan_customer'=>$loan_customer,
			'customer_type'=>$customer_type,
			'loan_date'=>$loan_date,
			'loan_principal'=>$lamount,
			'loan_period'=>$lmonths,
			'worthness_file'=>$worthness_file,
			'narration'=>$narration,
			'period_type'=> $loan->frequency,
			'loan_amount_term' => $monthly_payment,
			'loan_interest'=> $interest,
			'loan_interest_amount'=> $total_interest1,
			'loan_amount_total'=> $total_interest1+$amount,
			'next_payment_id'=>1,
			'loan_added_by'=>$added_by,
			'disbursed_amount'=>0,
			'reg_fee'=>$disbursed,
			'counter'=>$fcounter,
			'currency'=>$currency

		);
		$this->db->insert($this->table,$data);


		//borrower_loan_id
		$id = $this->db->insert_id();

		//insert each payment records to lend_payments
		if($loan->frequency == '2 Weeks') {
			$date = $loan_date;
			$frequency = $months*2;
			$start_day = 0;
			$loan_day = date('d', strtotime($date));
			$loan_month = date('m', strtotime($date));

			//get first payment day if 15 or 30
			if($loan_day >= 15) {
				if($loan_month == '02') {
					$start_day = 28;
				} else {
					$start_day = 30;
				}
			} elseif($loan_day == 30 OR $loan_day > 15) {
				$start_day = 15;
			} else {
				$start_day = 15;
			}

			$date = date('Y/m/'.$start_day, strtotime($date));
			for ($i=1; $i<=$frequency; $i++) {

				$this->db->insert(
					'payement_schedules', array(

						'customer' => $loan_customer,
						'loan_id' => $id,
						'payment_schedule' => $date,
						'payment_number' => $i,
						'amount' => $monthly_payment1,
						'principal' => $towards_balance1,
						'interest' => $total_interest1,
						'paid_amount' =>0.00,
						'loan_balance' => $current_balance1,
						'loan_date' => $loan_date,

					)
				);

				$day = date('d', strtotime($date));
				if($day == 15) {
					//check if February
					if(date('m', strtotime($date)) == '02') {
						$date = date('Y/02/28', strtotime($date));
					} else {
						$date = date('Y/m/30', strtotime($date));
					}
				} elseif($day == 30 OR $day > 15) {
					//check if January, going to February
					if(date('m', strtotime($date)) == '01') {
						$date = date('Y/02/15', strtotime('+1 month', strtotime($date)));
					} else {
						$date = date('Y/m/15', strtotime('+1 month', strtotime($date)));
					}
				}

			}
		} else {
		     
		    	
			while ($current_balance > 0)
			{

				$towards_interest = ($i/12)*$current_balance;  //this calculates the portion of your monthly payment that goes towards interest

				if ($monthly_payment > $current_balance){
					$monthly_payment = $current_balance + $towards_interest;
				}


				$towards_balance = $monthly_payment - $towards_interest;
				$total_interest = $monthly_payment - $towards_balance;
				$current_balance = $current_balance - $towards_balance;


				$this->db->insert(
					'payement_schedules', array(

						'customer' => $loan_customer,
						'loan_id' => $id,
						'payment_schedule' => $customised_date,
						'payment_number' => $ii,
						'amount' => $monthly_payment,
						'principal' => $towards_balance,
						'interest' => $total_interest,
						'paid_amount' => 0.00,
						'loan_balance' => $current_balance,
						'loan_date' => $loan_date,

					)
				);
			
				 $frequency = $days;
				$newdate = strtotime ('+'.$frequency.' day', strtotime ($customised_date)) ;

			

				$customised_date = date('Y-m-d', $newdate );

                    $dayToUpdate = 25; // The day to update to
                    $date=new DateTime($customised_date);
                    
                 
                    
                     $date->modify("{$dayToUpdate}{$date->format('-m-Y')}"); // Modify the day using the modify() method
                    $customised_date = $date->format('Y-m-d'); // Get the updated date string in the desired format
				$ii ++;
			}

		}

		//get next payment id and insert to lend_borrower_loans.next_payment_id
//		$payment = $this->Loan_model->next_payment($id);
//		$this->db->update('lend_borrower_loans', array('next_payment_id' => $payment->id), array('id' => $id));
		$data_account = array(
			'client_id' => $loan_customer,
			'account_number' => $loan_number,
			'balance' => 0,
			'account_type' => 2,
			'account_type_product' => $product_id,


		);

		$this->db->insert('account',$data_account);
		if($loan->grace_period > 0){
           $this->add_repayments($id,$loan->grace_period, $days);
        }
		return $id;
	}
    function add_loan($loan_numberr, $lamount, $lmonths,$interest, $product_id, $ldate, $loan_customer, $customer_type, $worthness_file, $narration, $added_by, $method, $fee_amount, $currency,$offtaker,$processing_fee)
    {
        // Set time zone
        date_default_timezone_set('Africa/Blantyre');

        // Retrieve loan product details
        $loan = $this->db->select("*")->from('loan_products')->where('loan_product_id', $product_id)->get()->row();

        if (!$loan) {
            return "Invalid loan product.";
        }

        // Generate loan ID
        $this->db->select('MAX(counter) as max_c')->where('loan_product', $product_id);
        $result = $this->db->get('loan')->row();
        $mxc = empty($result) ? 0 : $result->max_c;
        $loan_number = $loan->abbreviation . '000' . ($mxc + 1) . '/' . date('y');
        $fcounter = $mxc + 1;

        // Adjust the loan date to the 25th of the month
        $customised_date = $this->adjust_date_to_25th($ldate);

        // Calculate amortization
        $amortization_table = '';
        if ($loan->calculation_type === 'Reducing Balance') {
            $amortization_table = $this->calculateReducingBalance($lamount, $lmonths, $product_id, $ldate, $interest);
        } elseif ($loan->calculation_type === 'Straight Line') {
            $amortization_table = $this->calculateStraightLine($lamount, $lmonths, $loan, $ldate, $interest);
        } else {
            return "Invalid calculation type.";
        }

        // Interest and payment details


        // Prepare loan data for insertion
        // Dynamic calculation of loan parameters based on calculation type
        if ($loan->calculation_type === 'Reducing Balance') {
            $monthly_payment = $lamount * ($interest / 100 / 12) * pow((1 + $interest / 100 / 12), $lmonths) / (pow((1 + $interest / 100 / 12), $lmonths) - 1);
            $total_payment = $monthly_payment * $lmonths;
            $total_interest = $total_payment - $lamount;

        } elseif ($loan->calculation_type === 'Straight Line') {
            $total_interest = ($lamount * ($interest / 100)) * $lmonths / $lmonths;
            $total_payment = $lamount + $total_interest;
            $monthly_payment = $total_payment / $lmonths;

        } else {
            throw new Exception("Invalid calculation type.");
        }

// Prepare loan data for insertion
        $data = [
            'loan_number' => $loan_number,
            'loan_product' => $product_id,
            'loan_customer' => $loan_customer,
            'customer_type' => $customer_type,
            'loan_date' => $ldate,
            'loan_principal' => $lamount,
            'loan_period' => $lmonths,
            'worthness_file' => $worthness_file,
            'narration' => $narration,
            'period_type' => $loan->frequency,
            'loan_amount_term' => $monthly_payment,
            'loan_interest' => $interest,
            'loan_interest_amount' => $total_interest,
            'loan_amount_total' => $total_payment,
            'next_payment_id' => 1,
            'loan_added_by' => $added_by,
            'disbursed_amount' => 0,
            'reg_fee' => $method === 01 ? $fee_amount : 0,
            'counter' => $fcounter,
            'currency' => $currency,
            'off_taker' => $offtaker,
         'processing_fee' => $processing_fee

        ];

// Insert loan into the database
        $this->db->insert($this->table, $data);


        // Retrieve the inserted loan ID
        $loan_id = $this->db->insert_id();
        $calculation_type = $loan->calculation_type; // Ensure this is valid
        // Insert payment schedules
        $this->insert_payment_schedules($loan_id, $loan, $lamount, $lmonths,$interest, $ldate,$calculation_type,$loan_customer);
        $data_account = array(
            'client_id' => $loan_customer,
            'account_number' => $loan_number,
            'balance' => 0,
            'account_type' => 2,
            'account_type_product' => $product_id,


        );

        $this->db->insert('account', $data_account);
        return $loan_id;
    }

    private function adjust_date_to_25th($date)
    {
        $day = date('d', strtotime($date));
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));

        if ($day < 13) {
            $day = 25;
        } else {
            $month++;
            if ($month > 12) {
                $month = 1;
                $year++;
            }
            $day = 25;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    private function insert_payment_schedules($loan_id, $loan, $amount, $months,$interest, $start_date, $calculation_type,$loan_customer)
    {
        $date = $start_date;
        $current_balance = $amount;
        $total_interest = 0;
        $monthly_payment = 0;

        if ($calculation_type === 'Straight Line') {
            // Calculate Straight Line details
            $monthly_interest = ($amount * ($interest / 100)) / $months;
            $monthly_payment = ($amount / $months) + $monthly_interest;

            for ($i = 1; $i <= $months; $i++) {
                $principal_payment = $amount / $months;
                $interest_payment = $monthly_interest;
                $current_balance -= $principal_payment;

                $this->db->insert('payement_schedules', [
                    'customer' => $loan_customer,
                    'loan_id' => $loan_id,
                    'payment_schedule' => $date,
                    'payment_number' => $i,
                    'amount' => $monthly_payment,
                    'principal' => $principal_payment,
                    'interest' => $interest_payment,
                    'paid_amount' => 0.00,
                    'loan_balance' => $current_balance,
                    'loan_date' => $start_date
                ]);

                $date = date('Y-m-d', strtotime("+1 month", strtotime($date)));
            }
        } elseif ($calculation_type === 'Reducing Balance') {
            // Calculate Reducing Balance details
            $i = $interest / 100;
            $monthly_payment = $amount * ($i / 12) * pow((1 + $i / 12), $months) / (pow((1 + $i / 12), $months) - 1);

            for ($i = 1; $i <= $months; $i++) {
                $interest_payment = ($i / 12) * $current_balance;
                $principal_payment = $monthly_payment - $interest_payment;
                $current_balance -= $principal_payment;

                $this->db->insert('payement_schedules', [
                    'customer' => $loan_customer,
                    'loan_id' => $loan_id,
                    'payment_schedule' => $date,
                    'payment_number' => $i,
                    'amount' => $monthly_payment,
                    'principal' => $principal_payment,
                    'interest' => $interest_payment,
                    'paid_amount' => 0.00,
                    'loan_balance' => $current_balance,
                    'loan_date' => $start_date
                ]);

                $date = date('Y-m-d', strtotime("+1 month", strtotime($date)));
            }
        } else {
            throw new Exception("Invalid calculation type.");
        }
    }

    function add_loan_edit($loan_id,$loan_number,$lamount, $lmonths,$interest, $product_id, $ldate,$loan_customer,$customer_type,$worthness_file,$narration,$added_by)
	{
		//set Time Zone
		//date_default_timezone_set('Africa/Blantyre');
        $loan = $this->db->select("*")->from('loan_products')->where('loan_product_id',$product_id)->get()->row();
		$this->db->select('MAX(counter) as max_c')->where('loan_product',$product_id);
		$lid = $this->db->get('loan');
		$result = $lid->row();
		if(empty($result)){
		    $mxc = 0;
        }else{
            $mxc = $result->max_c;
        }
		$loanid= $loan->abbreviation.'000'.($mxc+1).'/'. date('y');
		$fcounter=$mxc+1;
		$amount = $lamount;
		$loan_date = $ldate;


		$months = $lmonths;
		//get loan parameters


		  $day = date('d', strtotime($ldate));
		        $month = date('m', strtotime($ldate));

		    	if($day<13 ){



                    $dayToUpdate = 25; // The day to update to
                    $date=new DateTime($ldate);



                     $date->modify("{$dayToUpdate}{$date->format('-m-Y')}"); // Modify the day using the modify() method
                    $customised_date = $date->format('Y-m-d'); // Get the updated date string in the desired format

		    	}

		    	else {


                    $newDay = 25; // The new day you want to set
                    $newMonth = $month + 1;

                    // The new month you want to set
                    if ($newMonth == 13) {


                        $date_str = strtotime($ldate); // the original date string
                        $date_timestamp = strtotime($date_str); // convert the date string to a Unix timestamp
                        $day = date('d', $date_timestamp); // extract the day from the timestamp
                        $month = date('m', $date_timestamp); // extract the month from the timestamp
                        $year = date('Y', $date_timestamp); // extract the year from the timestamp

// update the day, month, and year
                        $day = 25;
                        $month = 1;
                        $year += 1;

// create a new date string using the updated day, month, and year
                        $new_date_str = sprintf('%04d-%02d-%02d', $year, $month, $day);

                        $customised_date = $new_date_str ; // Get the updated date as a string in the format "YYYY-MM-DD"

                    } else {


                        $dateTime = new DateTime($ldate); // Create a new DateTime object with the original date
                        $dateTime->modify("$newDay-$newMonth-{$dateTime->format('Y')}"); // Update the day and month using the modify() method

                        $customised_date = $dateTime->format('Y-m-d'); // Get the updated date as a string in the format "YYYY-MM-DD"
                        //echo $date; // Output: 2023-06-20


                    }
		    	}


		//divisor
		switch ($loan->frequency) {
			case 'Monthly':
				$divisor = 1;
				$days = 30;
				break;
			case '2 Weeks':
				$divisor = 2;
				$days = 15;
				break;
			case 'Weekly':
				$divisor = 4;
				$days = 7;
				break;
		}

		//interest
		$amount_interest = $amount *( ($interest/100)*12);


		//total payments applying interest
		$amount_total = $amount + $amount_interest * $months * $divisor;

		//payment per term
		$amount_term = number_format(round($amount / ($months * $divisor), 2) + $amount_interest, 2, '.', '');

		$date = $loan_date;
		//$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);

		$i=($interest/100);

		$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);
		$monthly_payment1 = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);
		$current_balance = $amount;
		$current_balance1 = $amount;
		$payment_counter = 1;
		$ii=1;
		$total_interest = 0;
		$total_interest1=0;




		while($current_balance1 > 0) {
			//create rows


			$towards_interest1 = ($i/12)*$current_balance1;
			//this calculates the portion of your monthly payment that goes towards interest

			if ($monthly_payment1 > $current_balance1){
				$monthly_payment1 = $current_balance1 + $towards_interest1;
			}


			$towards_balance1 = $monthly_payment1 - $towards_interest1;
			$total_interest1 = $total_interest1 + $towards_interest1;
			$current_balance1 = $current_balance1 - $towards_balance1;

		}



		//additional info to be insert


		$data = array(
			'loan_number'=>$loan_number,
			'loan_product'=>$product_id,
			'loan_customer'=>$loan_customer,
			'customer_type'=>$customer_type,
			'loan_date'=>$loan_date,
			'loan_principal'=>$lamount,
			'loan_period'=>$lmonths,
			'worthness_file'=>$worthness_file,
			'narration'=>$narration,
			'period_type'=> $loan->frequency,
			'loan_amount_term' => $monthly_payment,
			'loan_interest'=> $interest,
			'loan_interest_amount'=> $total_interest1,
			'loan_amount_total'=> $total_interest1+$amount,
			'next_payment_id'=>1,
			'loan_added_by'=>$added_by,
			'disbursed_amount'=>0,
			'counter'=>$fcounter
		);

        $this->db->where('loan_id', $loan_id);
        $this->db->update($this->table, $data);


		//borrower_loan_id
		$id = $loan_id;

		//insert each payment records to lend_payments
		if($loan->frequency == '2 Weeks') {
			$date = $loan_date;
			$frequency = $months*2;
			$start_day = 0;
			$loan_day = date('d', strtotime($date));
			$loan_month = date('m', strtotime($date));

			//get first payment day if 15 or 30
			if($loan_day >= 15) {
				if($loan_month == '02') {
					$start_day = 28;
				} else {
					$start_day = 30;
				}
			} elseif($loan_day == 30 OR $loan_day > 15) {
				$start_day = 15;
			} else {
				$start_day = 15;
			}

			$date = date('Y/m/'.$start_day, strtotime($date));
            $this->db->where('loan_id',$loan_id)->delete('payement_schedules');
			for ($i=1; $i<=$frequency; $i++) {

				$this->db->insert(
					'payement_schedules', array(

						'customer' => $loan_customer,
						'loan_id' => $id,
						'payment_schedule' => $date,
						'payment_number' => $i,
						'amount' => $monthly_payment1,
						'principal' => $towards_balance1,
						'interest' => $total_interest1,
						'paid_amount' =>0.00,
						'loan_balance' => $current_balance1,
						'loan_date' => $loan_date,

					)
				);

				$day = date('d', strtotime($date));
				if($day == 15) {
					//check if February
					if(date('m', strtotime($date)) == '02') {
						$date = date('Y/02/28', strtotime($date));
					} else {
						$date = date('Y/m/30', strtotime($date));
					}
				} elseif($day == 30 OR $day > 15) {
					//check if January, going to February
					if(date('m', strtotime($date)) == '01') {
						$date = date('Y/02/15', strtotime('+1 month', strtotime($date)));
					} else {
						$date = date('Y/m/15', strtotime('+1 month', strtotime($date)));
					}
				}

			}
		} else {

            $this->db->where('loan_id',$loan_id)->delete('payement_schedules');
			while ($current_balance > 0)
			{

				$towards_interest = ($i/12)*$current_balance;  //this calculates the portion of your monthly payment that goes towards interest

				if ($monthly_payment > $current_balance){
					$monthly_payment = $current_balance + $towards_interest;
				}


				$towards_balance = $monthly_payment - $towards_interest;
				$total_interest = $monthly_payment - $towards_balance;
				$current_balance = $current_balance - $towards_balance;


				$this->db->insert(
					'payement_schedules', array(

						'customer' => $loan_customer,
						'loan_id' => $id,
						'payment_schedule' => $customised_date,
						'payment_number' => $ii,
						'amount' => $monthly_payment,
						'principal' => $towards_balance,
						'interest' => $total_interest,
						'paid_amount' => 0.00,
						'loan_balance' => $current_balance,
						'loan_date' => $loan_date,

					)
				);

				 $frequency = $days;
				$newdate = strtotime ('+'.$frequency.' day', strtotime ($customised_date)) ;



				$customised_date = date('Y-m-d', $newdate );

                    $dayToUpdate = 25; // The day to update to
                    $date=new DateTime($customised_date);



                     $date->modify("{$dayToUpdate}{$date->format('-m-Y')}"); // Modify the day using the modify() method
                    $customised_date = $date->format('Y-m-d'); // Get the updated date string in the desired format
				$ii ++;
			}

		}

		//get next payment id and insert to lend_borrower_loans.next_payment_id
//		$payment = $this->Loan_model->next_payment($id);
//		$this->db->update('lend_borrower_loans', array('next_payment_id' => $payment->id), array('id' => $id));

		if($loan->grace_period > 0){
           $this->add_repayments_edit($id,$loan->grace_period, $days);
        }
		return $id;
	}

	//mass repayments
    function mass_full_payments($loannumber,$recipient,$monthly_payment,$id,$fdate,$loanperiod)
    {

        $contpayment = 0;
        $start = 0;
        $paymentnumberm = 0;
        $patialpaidamount=0;
        $modulus = 0;
        $getPaidrows = $this->Payement_schedules_model->count_full_paid_payments($id);
        $getpartislPaidrows = $this->Payement_schedules_model->count_partial_paid_payments($id);
        if ($getpartislPaidrows == 0) {

            $start = $getPaidrows + 1;
        } else {

            $getpartialpaidrow = get_partial_paid_last($id);


            $start = $getpartialpaidrow->payment_number;
        }


        $check = $this->Account_model->get_account($loannumber);
        $num_strtotal = floatval(str_replace(',', '', $check->balance));
        $num_strtorp = floatval(str_replace(',', '', $monthly_payment));
        $cycles = 0;
        if ($num_strtotal >= $num_strtorp) {
            $cycles = intval( ($num_strtotal / $num_strtorp));
            $paymentnumberm = ($num_strtotal / $num_strtorp);
            $modulus = fmod($num_strtotal, $num_strtorp);

        } else {
            $paymentnumberm = $start;
            $patialpaidamount = $num_strtotal;
            $specialpartialpaid = $patialpaidamount;
            $patialpaymentnumber = $start;
        }


        if ($paymentnumberm == $loanperiod) {
            $cycles = $loanperiod;
            $patialpaidamount = 0;
        } else {
            $nextpayment = $paymentnumberm + 1;
        }


        if ($paymentnumberm == 1) {
            $paymentnumber = $start;
        } else {
            $paymentnumber = ($paymentnumberm + $start) - 1;

        }


        $tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');
        if ($patialpaidamount > 5000 || $modulus > 5000) {

            $patialpaymentnumber = $cycles + 1;
            $patialpaidamount = $modulus;


//            $partialpaid = get_partial_paid($id, $patialpaymentnumber);
//
//
//            $total = $partialpaid->paid_amount + $modulus;

 $partialn=intval($patialpaymentnumber);
            echo  $partialn;

                $data = array(
                    'partial_paid' => 'YES',
                    'paid_amount' => $patialpaidamount,
                    'paid_date' => $fdate
                );


                $this->db->where('loan_id', $id);
                $this->db->where('payment_number', $partialn);
                $this->db->update('payement_schedules', $data);


                $transaction = array(
                    'ref' => "GF." . $fdate . '.' . rand(100, 999),
                    'loan_id' => $id,
                    'amount' => $patialpaidamount,
                    'payment_number' => $partialn,
                    'transaction_type' => 3,
                    'added_by' => $this->session->userdata('user_id')

                );
                $this->db->insert('transactions', $transaction);
                $this->db->where('loan_id', $id)->update('loan', array('next_payment_id' => $partialn));


                $do_transactions = $this->Account_model->transfer_funds($loannumber, $recipient, $modulus, $tid, $fdate);


            }

            $nextpayment = 0;
            $contpayment = $loanperiod;
            if($cycles>0) {
                for ($i = $start; $i <= $cycles; $i++) {


                    $do_transactions = $this->Account_model->transfer_funds($loannumber, $recipient, $monthly_payment, $tid, $fdate);

                    if ($do_transactions == 'success') {
                        $data = array(
                            'status' => 'PAID',
                            'paid_amount' => $monthly_payment,
                            'paid_date' => $fdate
                        );
                        $this->db->where('loan_id', $id);
                        $this->db->where('payment_number', $i);
                        $this->db->update('payement_schedules', $data);

                        $transaction = array(
                            'ref' => "GF." . $fdate . '.' . rand(100, 999),
                            'loan_id' => $id,
                            'amount' => $monthly_payment,
                            'payment_number' => $i,
                            'transaction_type' => 3,
                            'added_by' => $this->session->userdata('user_id')

                        );


                        $this->db->insert('transactions', $transaction);

                        $nextpayment = $i + 1;

                    } else {
                        continue;
                    }


                    $this->db->where('loan_number', $loannumber)->update('massrepayments', array('massrepayment_status' => 'payment_made'));

                }
                $this->db->where('loan_id', $id)->update('loan', array('next_payment_id' => $nextpayment));

                $count_schedules = $this->count_payments($id);
                if (intval($count_schedules) == $contpayment) {
                    $this->db->where('loan_id', $id)->
                    update('loan', array('loan_status' => 'CLOSED', 'next_payment_id' => $contpayment));

                }


            }

            return true;


    }

    function add_loan_migration($loan_number,$lamount, $lmonths, $product_id,
	$ldate,$loan_customer,$customer_type,$worthness_file,$narration,$added_by,$method, $fee_amount,$paymentnumber,$patialpaidamount,  $patialpaymentnumber,$nextpayment,$fdate )
	{
		//set Time Zone
		//date_default_timezone_set('Africa/Blantyre');
        $loan = $this->db->select("*")->from('loan_products')->where('loan_product_id',$product_id)->get()->row();
		$this->db->select('MAX(counter) as max_c')->where('loan_product',$product_id);
		$lid = $this->db->get('loan');
		$result = $lid->row();
		if(empty($result)){
		    $mxc = 0;
        }else{
            $mxc = $result->max_c;
        }
		$loanid= $loan_number;
		$fcounter=$mxc+1;
		$amount = $lamount;
		$loan_date = $ldate;
		$months = $lmonths;
		
		
		  $day = date('d', strtotime($ldate));
		        $month = date('m', strtotime($ldate));
		    	
		    	if($day<13 ){
		    	    
		    	    
		    	  
                    $dayToUpdate = 25; // The day to update to
                    $date=new DateTime($ldate);
                    
                 
                    
                     $date->modify("{$dayToUpdate}{$date->format('-m-Y')}"); // Modify the day using the modify() method
                    $customised_date = $date->format('Y-m-d'); // Get the updated date string in the desired format

		    	}
		    	
		    	else {


                    $newDay = 25; // The new day you want to set
                    $newMonth = $month + 1;

                    // The new month you want to set
                    if ($newMonth == 13) {


                        $date_str = strtotime($ldate); // the original date string
                        $date_timestamp = strtotime($date_str); // convert the date string to a Unix timestamp
                        $day = date('d', $date_timestamp); // extract the day from the timestamp
                        $month = date('m', $date_timestamp); // extract the month from the timestamp
                        $year = date('Y', $date_timestamp); // extract the year from the timestamp

// update the day, month, and year
                        $day = 25;
                        $month = 1;
                        $year += 1;

// create a new date string using the updated day, month, and year
                        $new_date_str = sprintf('%04d-%02d-%02d', $year, $month, $day);

                        $customised_date = $new_date_str ; // Get the updated date as a string in the format "YYYY-MM-DD"

                    } else {


                    $dateTime = new DateTime($ldate); // Create a new DateTime object with the original date
                    $dateTime->modify("$newDay-$newMonth-{$dateTime->format('Y')}"); // Update the day and month using the modify() method

                    $customised_date = $dateTime->format('Y-m-d'); // Get the updated date as a string in the format "YYYY-MM-DD"
                    //echo $date; // Output: 2023-06-20


                }

		    	}
		    	
		    	
		    
		//get loan parameters
		if($method === 01){
		    $disbursed = $lamount;
        }else{
		    $disbursed = $lamount;
        }

		//divisor
		switch ($loan->frequency) {
			case 'Monthly':
				$divisor = 1;
				$days = 30;
				break;
			case '2 Weeks':
				$divisor = 2;
				$days = 15;
				break;
			case 'Weekly':
				$divisor = 4;
				$days = 7;
				break;
		}

		//interest
		$amount_interest = $amount *( ($interest/100)*12);


		//total payments applying interest
		$amount_total = $amount + $amount_interest * $months * $divisor;

		//payment per term
		$amount_term = number_format(round($amount / ($months * $divisor), 2) + $amount_interest, 2, '.', '');

		$date = $loan_date;
		//$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);

		$i=($interest/100);

		$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);
		$monthly_payment1 = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);
		$current_balance = $amount;
		$current_balance1 = $amount;
		$payment_counter = 1;
		$ii=1;
		$total_interest = 0;
		$total_interest1=0;




		while($current_balance1 > 0) {
			//create rows


			$towards_interest1 = ($i/12)*$current_balance1;
			//this calculates the portion of your monthly payment that goes towards interest

			if ($monthly_payment1 > $current_balance1){
				$monthly_payment1 = $current_balance1 + $towards_interest1;
			}


			$towards_balance1 = $monthly_payment1 - $towards_interest1;
			$total_interest1 = $total_interest1 + $towards_interest1;
			$current_balance1 = $current_balance1 - $towards_balance1;

		}




		//additional info to be insert


		$data = array(
			'loan_number'=>$loanid,
			'loan_product'=>$product_id,
			'loan_customer'=>$loan_customer,
			'customer_type'=>$customer_type,
			'loan_date'=>$loan_date,
			'loan_principal'=>$lamount,
			'loan_period'=>$lmonths,
			'worthness_file'=>$worthness_file,
			'narration'=>$narration,
			'period_type'=> $loan->frequency,
			'loan_amount_term' => $monthly_payment,
			'loan_interest'=> $interest,
			'loan_interest_amount'=> $total_interest1,
			'loan_amount_total'=> $total_interest1+$amount,
			'loan_status'=> 'ACTIVE',
			'next_payment_id'=>1,
			'loan_added_by'=>$added_by,
			'disbursed_amount'=>$disbursed,
			'counter'=>$fcounter
		);
	
	
		    	$this->db->insert($this->table,$data);
	


		//borrower_loan_id
		$id = $this->db->insert_id();

		//insert each payment records to lend_payments
		if($loan->frequency == '2 Weeks') {
			$date = $loan_date;
			$frequency = $months*2;
			$start_day = 0;
			$loan_day = date('d', strtotime($date));
			$loan_month = date('m', strtotime($date));

			//get first payment day if 15 or 30
			if($loan_day >= 15) {
				if($loan_month == '02') {
					$start_day = 28;
				} else {
					$start_day = 30;
				}
			} elseif($loan_day == 30 OR $loan_day > 15) {
				$start_day = 15;
			} else {
				$start_day = 15;
			}

			$date = date('Y/m/'.$start_day, strtotime($date));
			for ($i=1; $i<=$frequency; $i++) {

				$this->db->insert(
					'payement_schedules', array(

						'customer' => $loan_customer,
						'loan_id' => $id,
						'payment_schedule' => $date,
						'payment_number' => $i,
						'amount' => $monthly_payment1,
						'principal' => $towards_balance1,
						'interest' => $total_interest1,
						'paid_amount' => 0.00,
						'loan_balance' => $current_balance1,
						'loan_date' => $loan_date,

					)
				);

				$day = date('d', strtotime($date));
				if($day == 15) {
					//check if February
					if(date('m', strtotime($date)) == '02') {
						$date = date('Y/02/28', strtotime($date));
					} else {
						$date = date('Y/m/30', strtotime($date));
					}
				} elseif($day == 30 OR $day > 15) {
					//check if January, going to February
					if(date('m', strtotime($date)) == '01') {
						$date = date('Y/02/15', strtotime('+1 month', strtotime($date)));
					} else {
						$date = date('Y/m/15', strtotime('+1 month', strtotime($date)));
					}
				}

			}
		} else {
			while ($current_balance > 0)
			{

				$towards_interest = ($i/12)*$current_balance;  //this calculates the portion of your monthly payment that goes towards interest

				if ($monthly_payment > $current_balance){
					$monthly_payment = $current_balance + $towards_interest;
				}


				$towards_balance = $monthly_payment - $towards_interest;
				$total_interest = $monthly_payment - $towards_balance;
				$current_balance = $current_balance - $towards_balance;


				
				$this->db->insert(
					'payement_schedules', array(

						'customer' => $loan_customer,
						'loan_id' => $id,
						'payment_schedule' => $customised_date,
						'payment_number' => $ii,
						'amount' => $monthly_payment,
						'principal' => $towards_balance,
						'interest' => $total_interest,
						'paid_amount' =>  $monthly_payment,
						'loan_balance' => $current_balance,
						'loan_date' => $loan_date,

					)
				);

$frequency = $days;
				$newdate = strtotime ('+'.$frequency.' day', strtotime ($customised_date)) ;

			

				$customised_date = date('Y-m-d', $newdate );

                    $dayToUpdate = 25; // The day to update to
                    $date=new DateTime($customised_date);
                    
                 
                    
                     $date->modify("{$dayToUpdate}{$date->format('-m-Y')}"); // Modify the day using the modify() method
                    $customised_date = $date->format('Y-m-d'); // Get the updated date string in the desired format
				$ii ++;
	
			
			}

		}

		//get next payment id and insert to lend_borrower_loans.next_payment_id
//		$payment = $this->Loan_model->next_payment($id);
//		$this->db->update('lend_borrower_ loans', array('next_payment_id' => $payment->id), array('id' => $id));
	

		$data_account = array(
			'client_id' => $loan_customer,
			'account_number' => $loanid,
			'balance' => 0,
			'account_type' => 2,
			'account_type_product' => $product_id,


		);

		$this->db->insert('account',$data_account);
		$contpayment=0;
		//update transactions
		
		$paidbalance=$monthly_payment-$patialpaidamount;
			
		for($i=1;$i <=$paymentnumber;$i++){

			$data = array(
				'status'=>'PAID',
				'paid_amount'=>$monthly_payment
			);
			$this->db->where('loan_id', $id);
			$this->db->where('payment_number', $i);
			$this->db->update('payement_schedules',$data);
		
			$transaction = array(
				'ref' => "GF.".$fdate .'.'.rand(100,999),
				'loan_id' => $id,
				'amount' => $monthly_payment,
				'payment_number' => $i,
				'transaction_type' => 3,
				'added_by' => $this->session->userdata('user_id')

			);
			
			

			$this->db->insert('transactions',$transaction);
			

$contpayment=$i+1;
		}
		$this->db->where('loan_id',$id)->update('loan',array('next_payment_id'=>$contpayment));
		//$count_schedules = $this->count_payments($loan_number);
		 $count_schedules = $this->count_payments($id);
        if(intval($count_schedules) == $lmonths){
            $this->db->where('loan_id', $id)->
            update('loan',array('loan_status'=>'CLOSED','next_payment_id'=>$lmonths));
            
        }
        
        if($patialpaidamount>0 && $paidbalance>1000){
            
            	$data = array(
				 	'partial_paid'=>'YES',
				'paid_amount'=>$patialpaidamount
			);


			$this->db->where('loan_id', $id);
			$this->db->where('payment_number', $contpayment);
			$this->db->update('payement_schedules',$data);
			

			$transaction = array(
				'ref' => "GF.".$fdate .'.'.rand(100,999),
				'loan_id' => $id,
				'amount' => $patialpaidamount,
				'payment_number' => $i,
				'transaction_type' => 3,
				'added_by' => $this->session->userdata('user_id')

			);
			$this->db->insert('transactions',$transaction);
            	$this->db->where('loan_id',$id)->update('loan',array('next_payment_id'=>$contpayment));
            	
            
        }
        else {
            	$data = array(
				 'status'=>'PAID',
				'paid_amount'=>$monthly_payment
			);
			$this->db->where('loan_id', $id);
			$this->db->where('payment_number', $contpayment);
			$this->db->update('payement_schedules',$data);
			

			$transaction = array(
				'ref' => "GF.".$fdate .'.'.rand(100,999),
				'loan_id' => $id,
				'amount' => $monthly_payment,
				'payment_number' => $i,
				'transaction_type' => 3,
				'added_by' => $this->session->userdata('user_id')

			);
			$this->db->insert('transactions',$transaction);
            	$this->db->where('loan_id',$id)->update('loan',array('next_payment_id'=>$contpayment));
            $count_schedules=	$this->count_payments($id);
            	if(intval($count_schedules) == $contpayment){
            $this->db->where('loan_id', $id)->
            update('loan',array('loan_status'=>'CLOSED','next_payment_id'=>$contpayment));
            
        }
            
            
        }
        $checkifexist=get_all_accountCheck($loan_number);
    if(sizeof($checkifexist) == 0){
		$data_account = array(
			'client_id' => $loan_customer,
			'account_number' => $loanid,
			'balance' => 0,
			'account_type' => 2,
			'account_type_product' => $product_id,


		);

		$this->db->insert('account',$data_account);
    }
		if($loan->grace_period > 0){
           $this->add_repayments($id,$loan->grace_period, $days);
        }
		return $id;
	}
	
	function count_payments($loan_number){
        $this->db->select("*")->from('payement_schedules');
        $this->db->where('loan_id', $loan_number);
        $this->db->where('status', 'PAID');
        return $this->db->count_all_results();
    }


    // function report_client_summary_view()
    // {

    //     $this->db->select("*")
    //         ->from($this->table)
    //         ->join('loan_products','loan_products.loan_product_id =loan.loan_product')
    //         ->join('payement_schedules','payement_schedules.loan_id = loan.loan_id');
    //         return $this->db->get()->result();
    // }

    function add_repayments($id,$grace_period,$days){
$loan = $this->db->where('loan_id',$id)->get($this->table)->row();
//	    equal installments
$to_pay_installments = $loan->loan_period - $grace_period;
$equal_installments = $loan->loan_amount_total/$to_pay_installments;
$count = 0;

//    get the start payment date
$this->db->select("*")->from('payement_schedules')->where('loan_id',$id)->where('payment_number',$grace_period);
$r = $this->db->get()->row();

while ($to_pay_installments > $count){

$count ++;
$frequency = $days * $count;
$newdate = strtotime ('+'.$frequency.' day', strtotime ($r->payment_schedule)) ;

    //check if payment date landed on weekend
    //if Sunday, make it Monday. If Saturday, make it Friday
if(date ('D', $newdate) == 'Sun') {
$newdate = strtotime('+1 day', $newdate) ;
} elseif(date('D', $newdate) == 'Sat') {
$newdate = strtotime('-1 day', $newdate) ;
}

$newdate = date('Y-m-d', $newdate );
$data = array(
    'loan_id' => $loan->loan_id,
    'customer_id' => $loan->loan_customer,
    'customer_type' => $loan->customer_type,
    'payment_number' => $count,
    'payment_amount' => $equal_installments,
    'payment_date' => $newdate,

);
$this->db->insert('rescheduled_payments',$data);

}


}
function add_repayments_edit($id,$grace_period,$days){
$loan = $this->db->where('loan_id',$id)->get($this->table)->row();
//	    equal installments
$to_pay_installments = $loan->loan_period - $grace_period;
$equal_installments = $loan->loan_amount_total/$to_pay_installments;
$count = 0;

//    get the start payment date
$this->db->select("*")->from('payement_schedules')->where('loan_id',$id)->where('payment_number',$grace_period);
$r = $this->db->get()->row();
$this->db->where('loan_id',$id)->delete('rescheduled_payments');
while ($to_pay_installments > $count){

$count ++;
$frequency = $days * $count;
$newdate = strtotime ('+'.$frequency.' day', strtotime ($r->payment_schedule)) ;

    //check if payment date landed on weekend
    //if Sunday, make it Monday. If Saturday, make it Friday
if(date ('D', $newdate) == 'Sun') {
$newdate = strtotime('+1 day', $newdate) ;
} elseif(date('D', $newdate) == 'Sat') {
$newdate = strtotime('-1 day', $newdate) ;
}

$newdate = date('Y-m-d', $newdate );
$data = array(
    'loan_id' => $loan->loan_id,
    'customer_id' => $loan->loan_customer,
    'customer_type' => $loan->customer_type,
    'payment_number' => $count,
    'payment_amount' => $equal_installments,
    'payment_date' => $newdate,

);
$this->db->insert('rescheduled_payments',$data);

}


}
    function restructure($loan_id, $new_date)
    {


        //borrower_loan_id
        $id = $loan_id;
        $loan  = $this->db->select("*")->from($this->table)->where('loan_id',$loan_id)->get()->row();
        $current_balance = intval($loan->loan_principal);
        $i=($loan->loan_interest/100)*12;
        $ii=1;
        $date = $new_date;
        $this->db->where('loan_id',$id);
        $this->db->update($this->table,array('loan_date'=>$new_date));
        //insert each payment records to lend_payments
        $monthly_payment = intval($loan->loan_amount_term);
        switch ($loan->period_type) {
            case 'Monthly':
                $divisor = 1;
                $days = 30;
                break;
            case '2 Weeks':
                $divisor = 2;
                $days = 15;
                break;
            case 'Weekly':
                $divisor = 4;
                $days = 7;
                break;
        }
        $this->db->where('loan_id',$id);
        $this->db->delete('payement_schedules');

            while ($current_balance > 0)
            {

                $towards_interest = ($i/12)*$current_balance;  //this calculates the portion of your monthly payment that goes towards interest

                if ($monthly_payment > $current_balance){
                    $monthly_payment = $current_balance + $towards_interest;
                }


                $towards_balance = $monthly_payment - $towards_interest;
                $total_interest = $monthly_payment - $towards_balance;
                $current_balance = $current_balance - $towards_balance;


                $frequency = $days * $ii;
                $newdate = strtotime ('+'.$frequency.' day', strtotime ($date)) ;

                //check if payment date landed on weekend
                //if Sunday, make it Monday. If Saturday, make it Friday
                if(date ('D', $newdate) == 'Sun') {
                    $newdate = strtotime('+1 day', $newdate) ;
                } elseif(date('D', $newdate) == 'Sat') {
                    $newdate = strtotime('-1 day', $newdate) ;
                }

                $newdate = date('Y-m-d', $newdate );

                $this->db->insert(
                    'payement_schedules', array(

                        'customer' => $loan->loan_customer,
                        'loan_id' => $id,
                        'payment_schedule' => $newdate,
                        'payment_number' => $ii,
                        'amount' => $monthly_payment,
                        'principal' => $towards_balance,
                        'interest' => $total_interest,
                        'paid_amount' => 0,
                        'loan_balance' => $current_balance,
                        'loan_date' => $new_date,

                    )
                );


                $ii ++;
            }



        return $id;
    }



	function add_loans($amount, $months,$interest, $loan_id, $loan_date,$loan_customer)
	{


		//get loan parameters
		$this->db->where('loan_product_id',$loan_id);
		$loan = $this->db->get('loan_products')->row();

		//divisor
		switch ($loan->frequency) {
			case 'Monthly':
				$divisor = 1;
				$days = 30;
				break;
			case '2 Weeks':
				$divisor = 2;
				$days = 15;
				break;
			case 'Weekly':
				$divisor = 4;
				$days = 7;
				break;
		}

		//interest
		$amount_interest = $amount * ($interest/100)/$divisor;

		//total payments applying interest
		$amount_total = $amount + $amount_interest * $months * $divisor;

		//payment per term
		$amount_term = number_format(round($amount / ($months * $divisor), 2) + $amount_interest, 2, '.', ',');


		$date = $loan_date;
		$i=($interest/100)*12;


		$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);
		$monthly_payment1 = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);
		$current_balance = $amount;
		$current_balance1 = $amount;
		$payment_counter = 1;
		$total_interest = 0;
		$total_interest1=0;




		while($current_balance1 > 0) {
			//create rows


			$towards_interest1 = ($i/12)*$current_balance1;  //this calculates the portion of your monthly payment that goes towards interest

			if ($monthly_payment1 > $current_balance1){
				$monthly_payment1 = $current_balance1 + $towards_interest1;
			}


			$towards_balance1 = $monthly_payment1 - $towards_interest1;
			$total_interest1 = $total_interest1 + $towards_interest1;
			$current_balance1 = $current_balance1 - $towards_balance1;

		}

		//Loan info
		$table = '<div id="calculator"><h3>Loan Info</h3>';
		$table = $table . '<table border="1" class="table">';
		$table = $table . '<tr><td>Loan Name:</td><td>'.$loan->product_name.'</td></tr>';
		$table = $table . '<tr><td>Interest:</td><td>'.$interest.'%</td></tr>';
		$table = $table . '<tr><td>Terms:</td><td>'.$months.'</td></tr>';
		$table = $table . '<tr><td>Frequency:</td><td>Every '.$loan->frequency.' days</td></tr>';
		$table = $table . '</table>';
		$table = $table . '<h3>Computation</h3>';
		$table = $table . '<table>';
		$table = $table . '<tr><td>Loan Amount:</td><td> '.$this->config->item('currency_symbol') . number_format($amount, 2, '.', ',').'</td></tr>';
//        $table = $table . '<tr><td>Interest per First Month:</td><td> '.$this->config->item('currency_symbol') . $amount*$i.'</td></tr>';
//		$table = $table . '<tr><td>Interest per Term:</td><td> '.$this->config->item('currency_symbol') . $amount_interest.'</td></tr>';
		$table = $table . '<tr><td>Amount Per Term:</td><td> '.$this->config->item('currency_symbol') . round($monthly_payment,2).'</td></tr>';
		$table = $table . '<tr><td>Total Payment:</td><td> '.$this->config->item('currency_symbol') . number_format($total_interest1+$amount, 2, '.', ',').'</td></tr>';
		$table = $table . '</table>';

		//$monthly_payment = $amount*($i/12)*pow((1+$i/12),$months)/(pow((1+$i/12),$months)-1);


		$table = $table . '<table class="table" cellpadding="15" >
				<tr>
					<td width="30" align="center"><b>Pmt</b></td>
					<td width="60" align="center"><b>Payment</b></td>
					<td width="60" align="center"><b>Principal</b></td>
					<td width="60" align="center"><b>Interest</b></td>
					<td width="85" align="center"><b>Interest Paid</b></td>
					<td width="70" align="center"><b>Balance</b></td>
				</tr>	
			</table>';

		$table = $table ."<table  class='table' cellpadding='15' ";

		$table = $table ."<tr>";
		$table = $table . "<td width='30'>0</td>";
		$table = $table . "<td width='60'>&nbsp;</td>";
		$table = $table . "<td width='60'>&nbsp;</td>";
		$table = $table . "<td width='60'>&nbsp;</td>";
		$table = $table . "<td width='85'>&nbsp;</td>";
		$table = $table . "<td width='70'>".round($amount,2)."</td>";
		$table = $table . "</tr>";
		$data = array(
			'loan_number'=>rand(100,9999),
			'loan_product'=>$loan_id,
			'loan_customer'=>$loan_customer,
			'loan_date'=>$loan_date,
			'loan_principal'=>$amount,
			'loan_period'=>$months,
			'period_type'=> $loan->frequency,
			'loan_interest'=> $interest,
			'loan_amount_total'=> $total_interest1+$amount,
			'next_payment_id'=>1,
			'loan_added_by'=>$this->session->userdata('user_id')
		);
		$this->db->insert($this->table,$data);
		$lid= $this->db->insert_id();
		while($current_balance > 0) {
			//create rows


			$towards_interest = ($i/12)*$current_balance;  //this calculates the portion of your monthly payment that goes towards interest

			if ($monthly_payment > $current_balance){
				$monthly_payment = $current_balance + $towards_interest;
			}


			$towards_balance = $monthly_payment - $towards_interest;
			$total_interest = $total_interest + $towards_interest;
			$current_balance = $current_balance - $towards_balance;


			// display row

			$table = $table . "<tr class='table_info'>";
			$table = $table . "<td>".$payment_counter."</td>";
			$table = $table ."<td>".round($monthly_payment,2)."</td>";
			$table = $table . "<td>".round($towards_balance,2)."</td>";
			$table = $table . "<td>".round($towards_interest,2)."</td>";
			$table = $table ."<td>".round($total_interest,2)."</td>";
			$table = $table ."<td>".round($current_balance,2)."</td>";
			$table = $table . "</tr>";

			$schedules = array(

				'customer' => $loan_customer,
				'loan_id' => $lid,
				'payment_schedule' => $this->input->post('payment_schedule',TRUE),
				'payment_number' => $payment_counter,
				'amount' => $monthly_payment,
				'principal' => $towards_balance,
				'interest' => $total_interest,
				'paid_amount' => 0,
				'loan_balance' => $current_balance,
				'loan_date' => $loan_date,

			);
			$payment_counter++;


		}

		$table = $table . '</table></div>';


		return true;
	}
	// get all
	function get_all($status)
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');
		if($status !=""){
			$this->db->where('loan_status',$status);
		}else{
            $this->db->where('loan_status !=',"DELETED");
        }
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
    function get_all_not_disbursed()
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product');
//

//        $this->db->where('disbursed ',"No");
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
	function get_all_delete()
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');

			$this->db->where('delete_requested ',"Yes");
        $this->db->where('delete_by !=',null);
        $this->db->where('delete_approve_by ',null);
        $this->db->where('loan_status !=',"DELETED");
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
	function get_all_delete_approve()
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');

			$this->db->where('delete_requested ',"Yes");
			$this->db->where('delete_approve_by !=',null);
			$this->db->where('delete_athourise_by =',null);
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
    function get_all_deleted()
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');

			$this->db->where('delete_requested ',"Yes");
			$this->db->where('delete_approve_by !=',null);
			$this->db->where('delete_athourise_by !=',null);
			$this->db->where('loan_status',"DELETED");
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}

	function get_all_initiate()
	{
		/*$this->db->select('*');
			$this->db->from($this->table);
			$this->db->join('loan_products', 'loan_products.loan_product_id = loan.loan_product')
			//$this->db->join('individual_customers', 'individual_customers.id = loan.loan_customer');
			->join('close_loan', 'close_loan.loan_id = loan.loan_id', 'left');
		
			$this->db->where('loan_status','Active');
			$this->db->where('close_loan.loan_id IS NULL');
		
		
		$this->db->order_by('loan.loan_id', 'DESC');
			$result = $this->db->get()->result();
			return $result;*/
			$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product')
			->join('payement_schedules','payement_schedules.loan_id  =loan.loan_id');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');

	
			$this->db->where('loan_status','Active');
			$this->db->where('partial_paid','YES');
			
		
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}

function get_all_recomended_edit_loan()
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product')
			->join('edit_loan','edit_loan.loan_id  =loan.loan_id');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');

			$this->db->where('is_initiated','yes');
			$this->db->where('is_recommended','no');
		
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}

	function get_all_recomended_close_loan()
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product')
			->join('close_loan','close_loan.loan_id  =loan.loan_id');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');

	
			$this->db->where('loan_status','Active');
			$this->db->where('is_initiated','yes');
			$this->db->where('is_recommended','no');
		
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
	function get_all_approved_close_loan()
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product')
			->join('close_loan','close_loan.loan_id  =loan.loan_id');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');

	
			$this->db->where('loan_status','Active');
			$this->db->where('is_initiated','yes');
			$this->db->where('is_recommended','yes');
			$this->db->where('close_loan_status','no');
		
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
		function get_all_approved_edit_loan()
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product')
			->join('edit_loan','edit_loan.loan_id  =loan.loan_id');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');

			$this->db->where('is_initiated','yes');
			$this->db->where('is_recommended','yes');
			$this->db->where('edit_loan_status','no');
		
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
	function  get_all2(){
		$this->db->select("*")
			->from($this->table);
		$this->db->where('loan_status',"CLOSED");
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
	function get_all_mod($status)
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');
		if($status !=""){
			$this->db->where('loan_status',$status);
//			$this->db->where('written_off_by !=', NULL);
			$this->db->where('written_off_by is NOT NULL', NULL, FALSE);
		}
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
	function get_disbursed()
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');

		$this->db->where('disbursed','Yes');

		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}

	function track_individual($user)
	{
		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');
		if($user !=""){
			$this->db->where('loan_added_by',$user);
		}
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
	function loan_user($id)
	{
		$this->db->select("*")
			->from($this->table);

//			->join('individual_customers','individual_customers.id = loan.loan_customer');

		$this->db->where('loan_id',$id);

		return $this->db->get()->row();
	}
	public function sum_loans($from ,$to){
		$this->db->select('SUM(loan_principal) as total');
		$this->db->from('loan');
//	$this->db->join('payement_schedules','payement_schedules.loan_id=loan.loan_id');
		// $this->db->join('lend_payments','lend_payments.borrower_loan_id=lend_borrower_loans.id');
		$this->db->where('disbursed','Yes');
		$this->db->where('loan_status','ACTIVE');
		if($from !="" && $to !=""){
			$this->db->where('loan_added_date BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		}
		return $this->db->get()->row();
	}
	public  function update_defaulters(){
		$get_days = check_exist_in_table('settings','settings_id ',1);
		$this->db->select("*")
			->from($this->table);
		$r = $this->db->get()->result();
		foreach ($r as $m){
			$this->db->select_max('payment_schedule')
				->from('payement_schedules')
				->where('loan_id',$m->loan_id);
			$result = $this->db->get()->row();
			$date=	date('Y-m-d', strtotime($result->payment_schedule. ' + '.$get_days->defaulter_durations.' days'));
//		echo $result->payment_schedule.' '.$date;
//		echo "<br>";
			if($date < date('Y-m-d')){
//				echo $result->payment_schedule.' '.$date;

				$this->db->where('loan_id',$m->loan_id)
					->update('loan',array('loan_status'=>'DEFAULTED'));
			}
		}


	}
	public function count_disbursed_loans($from,$to){
		$this->db->select('*');
		$this->db->from('loan');
		$this->db->where('disbursed','Yes');
		if($from !="" && $to !=""){
			$this->db->where('loan_added_date BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		}
		return $this->db->count_all_results();
	}
	public function sum_total($from,$to){
		$this->db->select('*,loan.loan_principal as lm');
		$this->db->from('loan');
		$this->db->join('payement_schedules','payement_schedules.loan_id=loan.loan_id');
		// $this->db->join('lend_payments','lend_payments.borrower_loan_id=lend_borrower_loans.id');
		$this->db->where('disbursed','Yes');
		if($from !="" && $to !=""){
			$this->db->where('loan_added_date BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		}

		return $this->db->get()->result();
	}
	public function sum_total_par(){
        $this->db->select('
    SUM(ps.amount) AS portfolio_outstanding');

        $this->db->from('loan l');
        $this->db->join('payement_schedules ps', 'l.loan_id = ps.loan_id');

        $this->db->where('ps.status', 'NOT PAID');
        $this->db->where('l.loan_status', 'ACTIVE');
        $this->db->group_by('l.loan_id');


        $query = $this->db->get();
        return $query->result();


	return $this->db->get()->result();
}
	public function sum_total2($q){
		if(!empty($q)){
			$this->db->select('*,loan.loan_principal as lm');
			$this->db->from('loan');
			$this->db->join('payement_schedules','payement_schedules.loan_id=loan.loan_id');
			$this->db->where('loan_status','ACTIVE');
		}else{
			$this->db->select('*,loan.loan_principal as lm');
			$this->db->from('loan');
			$this->db->join('payement_schedules','payement_schedules.loan_id=loan.loan_id');
			$this->db->where('loan_status','ACTIVE');

		}


		return $this->db->get()->result();
	}
	public function getTotalSummaryRecords() {
		$user='';
        if ($user == "") {
            $q = "";
        } else {
            $q = "WHERE AA.loan_added_by = $user";
        }
		
        $query = $this->db->query("SELECT COUNT(*) as total_records FROM loan AS AA $q");
        return $query->row()->total_records;
    }

    // get all
    function get_all_disbursed()
    {

        $this->db->select("*")
            ->from($this->table)
            ->join('loan_products','loan_products.loan_product_id =loan.loan_product');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');

        $this->db->where('disbursed','yes');

        $this->db->order_by('loan.loan_id', 'DESC');
        return $this->db->get()->result();
    }

    function get_summaryu($user, $product, $ln)
    {

        $this->db->select('l.*, 
    CASE 
        WHEN l.customer_type = "individual" THEN CONCAT(ic.Firstname, " ", ic.Lastname, "(", ic.ClientId, ")")
        WHEN l.customer_type = "group" THEN CONCAT(g.group_name, " ", g.group_code)
        ELSE NULL
    END AS customer_name, 
    e.Firstname as eFirstname , e.Lastname as eLastname,
    lp.product_name,
    SUM(ps.amount) AS total_amount_not_paid,
    SUM(ps.principal) AS total_principal_not_paid, 
    SUM(ps.interest) AS total_interest_not_paid, 
    MAX(ps.payment_schedule) AS max_date');

        $this->db->from('loan l');
        $this->db->join('payement_schedules ps', 'l.loan_id = ps.loan_id');
        $this->db->join('employees e', 'l.loan_added_by = e.id');
        $this->db->join('loan_products lp', 'l.loan_product = lp.loan_product_id');
        $this->db->join('individual_customers ic', 'ic.id = l.loan_customer', 'left');
        $this->db->join('groups g', 'g.group_id = l.loan_customer', 'left');
        $this->db->where('ps.status', 'NOT PAID');
        $this->db->where('l.loan_status', 'ACTIVE');
        $this->db->where('DATE(ps.payment_schedule) < DATE(NOW())');
        if($user !=""){
            $this->db->where('l.loan_added_by', $user);
        }
        if($product !=""){
            $this->db->where('l.loan_product', $product);
        }
        if($ln !=""){
            $this->db->where('l.loan_number', $ln);
        }
        $this->db->group_by('l.loan_id');
        $this->db->order_by('l.loan_id');

        $query = $this->db->get();
      return $query->result();





    }


    public function count_summaryu()
    {
        // Count total rows without limiting the result
        // You may need to modify this query based on your existing logic
        set_time_limit(2000);

        $this->db->distinct();
        $this->db->select('loan.loan_id');
        $this->db->from('loan');
        $this->db->join('payement_schedules', 'loan.loan_id = payement_schedules.loan_id');
        $this->db->where('payement_schedules.status', 'NOT PAID');

        $query = $this->db->get();

        return $query->num_rows();
    }
	function get_filter($user,$product,$status,$from,$to)
	{

		$this->db->select("*,employees.Firstname as efname, employees.Lastname as elname")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product')
//			->join('individual_customers','individual_customers.id = loan.loan_customer')
			->join('employees','employees.id = loan.loan_added_by');
		if($status !="All"){
			$this->db->where('loan_status',$status);
		}
		if($user !="All"){
			$this->db->where('loan_added_by',$user);
		}
		if($product !="All"){
			$this->db->where('loan_product',$product);
		}
		if($from !="" && $to !=""){
			$this->db->where('loan_added_date BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		}
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
	

	function report_client_summary($loan_number)
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product')
		->join('payement_schedules','payement_schedules.loan_id = loan.loan_id');
		
		$this->db->where('loan_number', $loan_number);
		$this->db->where('payement_schedules.status','NOT PAID')
		->where('loan.loan_status','ACTIVE');

		
		return $this->db->get()->result();
	}
	

	function  report_lctr($from,$to){

    	$this->db->select("*")
			->from($this->table)
			
		->where('disbursed','yes');
    	
		if($from !="" && $to !=""){
			$this->db->where('disbursed_date BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		}
		$re= $this->db->get()->result();
    	return $re;
	}

	
		function rbm_reportFilter($from,$to)
	{
	   

		$this->db->select("*")
			->from('individual_customers')
			->join('proofofidentity','proofofidentity.ClientID=individual_customers.ClientID')

			->join('loan','loan.loan_customer=individual_customers.id');
		if($from !="" && $to !=""){
			$this->db->where('loan_added_date BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		}
		$this->db->order_by('loan.loan_id', 'DESC');
		return $this->db->get()->result();
	}
	
	function get_user_loan($id)
	{
		$this->db->order_by($this->id, $this->order);
		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product');
		//->join('individual_customers','individual_customers.id = loan.loan_customer');


       // $this->db->where('customer_type','individual');
        $this->db->where('loan_customer',$id);


		return $this->db->get()->result();
	}


    function get_user_loan_individual($id)
    {
        $this->db->order_by($this->id, $this->order);
        $this->db->select("*")
            ->from($this->table);
          //  ->join('loan_products','loan_products.loan_product_id =loan.loan_product')
		 //->join('individual_customers','individual_customers.id = loan.loan_customer');

       // $this->db->where('id',$id);
        $this->db->where('loan_customer',$id);


        return $this->db->get()->result();
    }


    // get data by id
	function get_by_id($id)
	{
		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product')
//			->join('individual_customers','individual_customers.id = loan.loan_customer');
			->join('employees','employees.id = loan.loan_added_by');
		$this->db->where($this->id, $id);
		return $this->db->get()->row();
	}
	
	 // get data by id
	function get_by_id_recommend($id)
	{
		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product')
//			->join('individual_customers','individual_customers.id = loan.loan_customer');
			->join('edit_loan','edit_loan.loan_id  =loan.loan_id');
//			->join('individual_customers','individual_customers.id = loan.loan_customer');

        	$this->db->where('loan.loan_id', $id);
			$this->db->where('edit_loan.is_initiated','yes');
			$this->db->where('edit_loan.is_recommended','no');
			$this->db->where('edit_loan.edit_loan_status','no');
		
		return $this->db->get()->row();
	}
	// get data by id
	// get data by id
	function get_by_id_report($id)
	{
		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product')
	->join('individual_customers','individual_customers.id = loan.loan_customer');
	
		$this->db->where($this->id, $id);
		return $this->db->get()->row();
	}
	// get data by id groups
	function get_by_id_group($id)
	{
		$this->db->select("*")
			->from($this->table)
			->join('loan_products','loan_products.loan_product_id =loan.loan_product')
	->join('corporate_customers','corporate_customers.id = loan.loan_customer');
	
		$this->db->where($this->id, $id);
		return $this->db->get()->row();
	}
	// get total rows
	function total_rows($q = NULL) {
		$this->db->like('loan_id', $q);
		$this->db->or_like('loan_number', $q);
		$this->db->or_like('loan_product', $q);
		$this->db->or_like('loan_customer', $q);
		$this->db->or_like('loan_date', $q);
		$this->db->or_like('loan_principal', $q);
		$this->db->or_like('loan_period', $q);
		$this->db->or_like('period_type', $q);
		$this->db->or_like('loan_interest', $q);
		$this->db->or_like('loan_amount_total', $q);
		$this->db->or_like('next_payment_id', $q);
		$this->db->or_like('loan_added_by', $q);
		$this->db->or_like('loan_approved_by', $q);
		$this->db->or_like('loan_status', $q);
		$this->db->or_like('loan_added_date', $q);
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	// get data with limit and search
	function get_limit_data($limit, $start = 0, $q = NULL) {
		$this->db->order_by($this->id, $this->order);
		$this->db->like('loan_id', $q);
		$this->db->or_like('loan_number', $q);
		$this->db->or_like('loan_product', $q);
		$this->db->or_like('loan_customer', $q);
		$this->db->or_like('loan_date', $q);
		$this->db->or_like('loan_principal', $q);
		$this->db->or_like('loan_period', $q);
		$this->db->or_like('period_type', $q);
		$this->db->or_like('loan_interest', $q);
		$this->db->or_like('loan_amount_total', $q);
		$this->db->or_like('next_payment_id', $q);
		$this->db->or_like('loan_added_by', $q);
		$this->db->or_like('loan_approved_by', $q);
		$this->db->or_like('loan_status', $q);
		$this->db->or_like('loan_added_date', $q);
		$this->db->limit($limit, $start);
		return $this->db->get($this->table)->result();
	}

	// insert data
	function insert($data)
	{
		$this->db->insert($this->table, $data);
	}

	// update data
	function update($id, $data)
	{
		$this->db->where($this->id, $id);
		$this->db->update($this->table, $data);
	}
	function update1($id, $data)
	{
		$this->db->where('loan_customer', $id);
		$this->db->update($this->table, $data);
	}

	// delete data
	function delete($id)
	{
		$this->db->where($this->id, $id);
		$this->db->delete($this->table);
	}

	// delete data
	function delete_data($id)
	{
		$this->db->where($this->id, $id);
		$this->db->delete($this->table_d);
	}
}

