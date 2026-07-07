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
		} elseif ($loan->calculation_type === 'Bullet Payment') {
			return $this->calculateBulletPayment($amount, $months, $loan, $loan_date, $interest);
		} else {
			return "Invalid calculation type.";
		}
	}
	/**
	 * Calculate bullet payment with monthly interest
	 *
	 * @param float $amount Loan principal
	 * @param int $months Loan term in months
	 * @param object $loan Loan product details
	 * @param string $loan_date Start date of loan
	 * @param float $interest Annual interest rate
	 * @return string HTML table with loan details
	 */

    private function calculateStraightLine($amount, $months, $loan, $loan_date, $interest)
    {
        // Straight Line / Flat Rate calculation
        // Interest rate is PER PERIOD (monthly), not annual
        // Total Interest = Principal × Rate × Months

        $rate = $interest / 100;  // Convert percentage to decimal
        $total_interest = $amount * $rate * $months;
        $total_payment = $amount + $total_interest;
        $monthly_payment = round($total_payment / $months, 2);

        // Equal portions each month
        $principal_per_month = round($amount / $months, 2);
        $interest_per_month = round($total_interest / $months, 2);

        $currency = $this->config->item('currency_symbol');

        // Summary Section
        $table = '<div id="calculator">';
        $table .= '<div class="summary-highlight">';
        $table .= '<table style="width:100%">';
        $table .= '<tr><td>Loan Product:</td><td>' . $loan->product_name . '</td></tr>';
        $table .= '<tr><td>Principal Amount:</td><td>' . $currency . ' ' . number_format($amount, 2) . '</td></tr>';
        $table .= '<tr><td>Interest Rate:</td><td>' . $interest . '% per month (Flat)</td></tr>';
        $table .= '<tr><td>Loan Term:</td><td>' . $months . ' months</td></tr>';
        $table .= '<tr><td style="border-top:1px solid rgba(255,255,255,0.3); padding-top:8px;">Monthly Payment:</td><td style="border-top:1px solid rgba(255,255,255,0.3); padding-top:8px; font-size:1.2rem;">' . $currency . ' ' . number_format($monthly_payment, 2) . '</td></tr>';
        $table .= '<tr><td>Total Interest:</td><td>' . $currency . ' ' . number_format($total_interest, 2) . '</td></tr>';
        $table .= '<tr><td>Total Repayment:</td><td>' . $currency . ' ' . number_format($total_payment, 2) . '</td></tr>';
        $table .= '</table>';
        $table .= '</div>';

        $table .= '<h3>Repayment Schedule</h3>';
        $table .= '<table class="table">';
        $table .= '<tr>
            <th>#</th>
            <th>Bal Before</th>
            <th>Installment</th>
            <th>Principal</th>
            <th>Interest</th>
            <th>Bal After</th>
        </tr>';

        $current_balance = $amount;
        for ($i = 1; $i <= $months; $i++) {
            // Balance before payment
            $balance_before = $current_balance;

            // Last payment adjustment
            if ($i == $months) {
                $principal_per_month = $current_balance;
                $monthly_payment = $principal_per_month + $interest_per_month;
            }

            $current_balance -= $principal_per_month;
            if ($current_balance < 0) $current_balance = 0;

            $table .= '<tr>';
            $table .= '<td>' . $i . '</td>';
            $table .= '<td>' . number_format($balance_before, 2, '.', ',') . '</td>';
            $table .= '<td>' . number_format($monthly_payment, 2, '.', ',') . '</td>';
            $table .= '<td>' . number_format($principal_per_month, 2, '.', ',') . '</td>';
            $table .= '<td>' . number_format($interest_per_month, 2, '.', ',') . '</td>';
            $table .= '<td>' . number_format($current_balance, 2, '.', ',') . '</td>';
            $table .= '</tr>';
        }

        $table .= '</table></div>';

        return $table;
    }
    function calculateReducingBalance($amount, $months, $loan_id, $loan_date, $interest)
    {
        // Get loan parameters
        $this->db->where('loan_product_id', $loan_id);
        $loan = $this->db->get('loan_products')->row();

        // Interest rate is per period (monthly), NOT annual
        // e.g., 10% means 10% per month
        $rate = $interest / 100;  // Convert percentage to decimal

        // Calculate EMI using reducing balance formula
        // EMI = P * r * (1+r)^n / ((1+r)^n - 1)
        if ($rate > 0) {
            $emi = $amount * $rate * pow((1 + $rate), $months) / (pow((1 + $rate), $months) - 1);
        } else {
            $emi = $amount / $months;
        }
        $emi = round($emi, 2);

        // Pre-calculate total interest
        $temp_balance = $amount;
        $total_interest_calc = 0;
        $temp_emi = $emi;

        for ($i = 1; $i <= $months; $i++) {
            $int_portion = round($temp_balance * $rate, 2);
            $prin_portion = $temp_emi - $int_portion;

            if ($i == $months) {
                $prin_portion = $temp_balance;
                $temp_emi = $prin_portion + $int_portion;
            }

            $total_interest_calc += $int_portion;
            $temp_balance -= $prin_portion;
        }

        $currency = $this->config->item('currency_symbol');

        // Summary Section
        $table = '<div id="calculator">';
        $table .= '<div class="summary-highlight">';
        $table .= '<table style="width:100%">';
        $table .= '<tr><td>Loan Product:</td><td>' . $loan->product_name . '</td></tr>';
        $table .= '<tr><td>Principal Amount:</td><td>' . $currency . ' ' . number_format($amount, 2) . '</td></tr>';
        $table .= '<tr><td>Interest Rate:</td><td>' . $interest . '% per month</td></tr>';
        $table .= '<tr><td>Loan Term:</td><td>' . $months . ' months</td></tr>';
        $table .= '<tr><td style="border-top:1px solid rgba(255,255,255,0.3); padding-top:8px;">Monthly Payment:</td><td style="border-top:1px solid rgba(255,255,255,0.3); padding-top:8px; font-size:1.2rem;">' . $currency . ' ' . number_format($emi, 2) . '</td></tr>';
        $table .= '<tr><td>Total Interest:</td><td>' . $currency . ' ' . number_format($total_interest_calc, 2) . '</td></tr>';
        $table .= '<tr><td>Total Repayment:</td><td>' . $currency . ' ' . number_format($amount + $total_interest_calc, 2) . '</td></tr>';
        $table .= '</table>';
        $table .= '</div>';

        $table .= '<h3>Repayment Schedule</h3>';

        // Payment schedule table
        $table .= '<table class="table">';
        $table .= '<tr>
            <th>#</th>
            <th>Bal Before</th>
            <th>Installment</th>
            <th>Principal</th>
            <th>Interest</th>
            <th>Bal After</th>
        </tr>';

        // Generate schedule
        $current_balance = $amount;
        $cumulative_interest = 0;
        $monthly_payment = $emi;

        for ($payment_num = 1; $payment_num <= $months; $payment_num++) {
            // Balance before payment
            $balance_before = $current_balance;

            // Interest for this period
            $interest_payment = round($current_balance * $rate, 2);

            // Principal for this period
            $principal_payment = $monthly_payment - $interest_payment;

            // Last payment adjustment
            if ($payment_num == $months) {
                $principal_payment = $current_balance;
                $monthly_payment = $principal_payment + $interest_payment;
            }

            $cumulative_interest += $interest_payment;
            $current_balance = round($current_balance - $principal_payment, 2);
            if ($current_balance < 0) $current_balance = 0;

            // Display row
            $table .= '<tr>';
            $table .= '<td>' . $payment_num . '</td>';
            $table .= '<td>' . number_format($balance_before, 2) . '</td>';
            $table .= '<td>' . number_format($monthly_payment, 2) . '</td>';
            $table .= '<td>' . number_format($principal_payment, 2) . '</td>';
            $table .= '<td>' . number_format($interest_payment, 2) . '</td>';
            $table .= '<td>' . number_format($current_balance, 2) . '</td>';
            $table .= '</tr>';
        }

        $table .= '</table></div>';

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
    function add_loans($loan_numberr, $lamount, $lmonths,$interest, $product_id, $ldate, $loan_customer, $customer_type, $worthness_file, $narration, $added_by, $method, $fee_amount, $currency,$offtaker,$processing_fee)
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
        // Interest rate is PER PERIOD (monthly), not annual
        $rate = $interest / 100;  // Convert percentage to decimal

        if ($loan->calculation_type === 'Reducing Balance') {
            // EMI = P * r * (1+r)^n / ((1+r)^n - 1)
            if ($rate > 0) {
                $monthly_payment = $lamount * $rate * pow((1 + $rate), $lmonths) / (pow((1 + $rate), $lmonths) - 1);
            } else {
                $monthly_payment = $lamount / $lmonths;
            }
            $total_payment = $monthly_payment * $lmonths;
            $total_interest = $total_payment - $lamount;

        } elseif ($loan->calculation_type === 'Straight Line') {
            // Straight Line / Flat Rate calculation
            // Total Interest = Principal × Rate × Months
            $total_interest = $lamount * $rate * $lmonths;
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

	function add_loan($loan_number, $lamount, $lmonths, $interest, $product_id, $ldate, $loan_customer, $customer_type, $worthness_file, $narration, $added_by, $method, $fee_amount, $currency, $offtaker, $processing_fee, $appraisal_data = array())
	{
		// Retrieve loan product details
		$loan = $this->db->select("*")->from('loan_products')->where('loan_product_id', $product_id)->get()->row();

		if (!$loan) {
			return "Invalid loan product.";
		}

		// Generate loan ID
		$this->db->select('MAX(counter) as max_c')->where('loan_product', $product_id);
		$result = $this->db->get('loan')->row();
		$mxc = empty($result) ? 0 : $result->max_c;
		$loan_number = $loan->abbreviation . '000' . ($mxc + 1) . '-' . date('y');
		$fcounter = $mxc + 1;

		// Adjust the loan date to the 25th of the month if needed
		$customised_date = $this->adjust_date_to_25th($ldate);

		// Calculate interest and payment details based on calculation type
		$monthly_payment = 0;
		$total_interest = 0;
		$total_payment = 0;

		// IMPORTANT: Special handling for Bullet Payment type
		if ($loan->calculation_type === 'Bullet Payment') {
			// For bullet loans, interest is principal × monthly rate × terms
			$monthly_interest_rate = $interest / 100; // Convert percentage to decimal
			$total_interest = $lamount * $monthly_interest_rate * $lmonths;
			$total_payment = $lamount + $total_interest;
			$monthly_payment = 0; // No monthly payments for bullet loans

			// Log the calculation for debugging
			error_log("Bullet Payment Calculation: Principal=$lamount, Rate=$interest%, Terms=$lmonths");
			error_log("Interest Calculation: $lamount × " . ($interest/100) . " × $lmonths = $total_interest");
		}
		else if ($loan->calculation_type === 'Reducing Balance') {
			// Reducing Balance calculation
			// Interest rate is PER PERIOD (monthly), not annual
			$rate = $interest / 100; // Convert percentage to decimal
			if ($rate > 0) {
				$monthly_payment = $lamount * $rate * pow((1 + $rate), $lmonths) / (pow((1 + $rate), $lmonths) - 1);
			} else {
				$monthly_payment = $lamount / $lmonths;
			}
			$total_payment = $monthly_payment * $lmonths;
			$total_interest = $total_payment - $lamount;
		}
		else if ($loan->calculation_type === 'Straight Line') {
			// Straight Line / Flat Rate calculation
			// Interest rate is PER PERIOD (monthly), not annual
			// Total Interest = Principal × Rate × Months
			$rate = $interest / 100;
			$total_interest = $lamount * $rate * $lmonths;
			$total_payment = $lamount + $total_interest;
			$monthly_payment = $total_payment / $lmonths;
		}
		else {
			throw new Exception("Invalid calculation type: " . $loan->calculation_type);
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
			'processing_fee' => $processing_fee,
			'calculation_type' => $loan->calculation_type,
			// Appraisal fields
			'crb_search' => isset($appraisal_data['crb_search']) ? $appraisal_data['crb_search'] : null,
			'pacra_search' => isset($appraisal_data['pacra_search']) ? $appraisal_data['pacra_search'] : null,
			'previous_facilities' => isset($appraisal_data['previous_facilities']) ? $appraisal_data['previous_facilities'] : null,
			'past_loans_comment' => isset($appraisal_data['past_loans_comment']) ? $appraisal_data['past_loans_comment'] : null,
			'security_notes' => isset($appraisal_data['security_notes']) ? $appraisal_data['security_notes'] : null,
			'bank_statement_notes' => isset($appraisal_data['bank_statement_notes']) ? $appraisal_data['bank_statement_notes'] : null,
			'about_transaction' => isset($appraisal_data['about_transaction']) ? $appraisal_data['about_transaction'] : null,
			'risk_analysis' => isset($appraisal_data['risk_analysis']) ? $appraisal_data['risk_analysis'] : null
			// Note: Bank statement fields are now stored in separate bank_statements table
		];

		// Add maturity date for bullet loans
		if ($loan->calculation_type === 'Bullet Payment') {
			$maturity_date = date('Y-m-d', strtotime("+$lmonths months", strtotime($ldate)));
			//$data['maturity_date'] = $maturity_date;
		}

		// Insert loan into the database
		$this->db->insert($this->table, $data);

		// Retrieve the inserted loan ID
		$loan_id = $this->db->insert_id();

		// Insert into loan_approval_trail to track loan initiation
		$trail_data = array(
			'user_id' => $added_by,
			'action' => 'INITIATED',
			'comment' => 'Loan application initiated',
			'loan_id' => $loan_id
		);
		$this->db->insert('loan_approval_trail', $trail_data);

		// Insert payment schedules based on calculation type
		if ($loan->calculation_type === 'Bullet Payment') {
			// For bullet payment, create a single payment record
			$maturity_date = date('Y-m-d', strtotime("+$lmonths months", strtotime($ldate)));

			$this->db->insert('payement_schedules', [
				'customer' => $loan_customer,
				'loan_id' => $loan_id,
				'payment_schedule' => $maturity_date,
				'payment_number' => 1,
				'amount' => $total_payment, // Principal + Interest
				'principal' => $lamount,
				'interest' => $total_interest,
				'paid_amount' => 0.00,
				'loan_balance' => $lamount,
				'loan_date' => $ldate,
				'is_bullet_payment' => 1 // Flag for bullet payment
			]);
		} else {
			// For regular loans, insert the usual payment schedules
			$this->insert_payment_schedules($loan_id, $loan, $lamount, $lmonths, $interest, $ldate, $loan->calculation_type, $loan_customer);
		}

		// Create account
		$data_account = array(
			'client_id' => $loan_customer,
			'account_number' => $loan_number,
			'balance' => 0,
			'account_type' => 2,
			'account_type_product' => $product_id,
		);

		$this->db->insert('account', $data_account);
		$re = array(
			'loan_id' => $loan_id,
			'loan_number' => $loan_number
		);
		return  $re;
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
            // Straight Line / Flat Rate calculation
            // Interest rate is PER PERIOD (monthly), not annual
            // Total Interest = Principal × Rate × Months

            $rate = $interest / 100;  // Convert percentage to decimal
            $total_interest = $amount * $rate * $months;
            $total_payment = $amount + $total_interest;
            $emi = round($total_payment / $months, 2);

            // Each installment has equal principal and interest portions
            $principal_per_month = round($amount / $months, 2);
            $interest_per_month = round($total_interest / $months, 2);

            for ($payment_num = 1; $payment_num <= $months; $payment_num++) {
                // For last payment, adjust to clear balance exactly
                if ($payment_num == $months) {
                    $principal_per_month = $current_balance;
                    $emi = $principal_per_month + $interest_per_month;
                }

                $current_balance = round($current_balance - $principal_per_month, 2);
                if ($current_balance < 0) $current_balance = 0;

                $this->db->insert('payement_schedules', [
                    'customer' => $loan_customer,
                    'loan_id' => $loan_id,
                    'payment_schedule' => $date,
                    'payment_number' => $payment_num,
                    'amount' => round($emi, 2),
                    'principal' => round($principal_per_month, 2),
                    'interest' => round($interest_per_month, 2),
                    'paid_amount' => 0.00,
                    'loan_balance' => $current_balance,
                    'loan_date' => $start_date
                ]);

                $date = date('Y-m-d', strtotime("+1 month", strtotime($date)));
            }
        } elseif ($calculation_type === 'Reducing Balance') {
            // Calculate Reducing Balance (EMI - Equal Monthly Installments)
            // Formula: EMI = P * r * (1+r)^n / ((1+r)^n - 1)
            // Where: P = principal, r = periodic interest rate, n = number of periods
            // Interest rate is per period (monthly), NOT annual

            $periodic_rate = $interest / 100;  // Convert percentage to decimal (e.g., 10% = 0.10)

            // Calculate EMI (Equal Monthly Installment)
            if ($periodic_rate > 0) {
                $emi = $amount * $periodic_rate * pow((1 + $periodic_rate), $months) / (pow((1 + $periodic_rate), $months) - 1);
            } else {
                // If interest is 0, just divide principal by months
                $emi = $amount / $months;
            }

            // Round EMI to 2 decimal places for consistent payments
            $emi = round($emi, 2);

            for ($payment_num = 1; $payment_num <= $months; $payment_num++) {
                // Interest for this period = current balance * periodic rate
                $interest_payment = round($current_balance * $periodic_rate, 2);

                // Principal for this period = EMI - Interest
                $principal_payment = $emi - $interest_payment;

                // For last payment, adjust to clear the balance exactly
                if ($payment_num == $months) {
                    $principal_payment = $current_balance;
                    $emi = $principal_payment + $interest_payment;
                }

                // Update balance
                $current_balance = round($current_balance - $principal_payment, 2);
                if ($current_balance < 0) $current_balance = 0;

                $this->db->insert('payement_schedules', [
                    'customer' => $loan_customer,
                    'loan_id' => $loan_id,
                    'payment_schedule' => $date,
                    'payment_number' => $payment_num,
                    'amount' => round($emi, 2),
                    'principal' => round($principal_payment, 2),
                    'interest' => round($interest_payment, 2),
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
        $id = $loan_id;
        $loan = $this->db->select("*")->from($this->table)->where('loan_id', $loan_id)->get()->row();
        
        if (!$loan) {
            return false;
        }
        
        // Update loan date
        $this->db->where('loan_id', $id);
        $this->db->update($this->table, array('loan_date' => $new_date));
        
        // Delete existing payment schedules
        $this->db->where('loan_id', $id);
        $this->db->delete('payement_schedules');
        
        // Get loan product details for calculation type
        $loan_product = $this->db->select("*")->from('loan_products')->where('loan_product_id', $loan->loan_product)->get()->row();
        
        if (!$loan_product) {
            return false;
        }
        
        // Use the same payment schedule generation logic as add_loan
        $calculation_type = $loan_product->calculation_type;
        
        if ($calculation_type === 'Bullet Payment') {
            // For bullet payment, create a single payment record
            $maturity_date = date('Y-m-d', strtotime("+{$loan->loan_period} months", strtotime($new_date)));
            
            $this->db->insert('payement_schedules', [
                'customer' => $loan->loan_customer,
                'loan_id' => $id,
                'payment_schedule' => $maturity_date,
                'payment_number' => 1,
                'amount' => $loan->loan_amount_total, // Principal + Interest
                'principal' => $loan->loan_principal,
                'interest' => $loan->loan_interest_amount,
                'paid_amount' => 0.00,
                'loan_balance' => $loan->loan_principal,
                'loan_date' => $new_date,
                'is_bullet_payment' => 1 // Flag for bullet payment
            ]);
        } else {
            // For regular loans, use the same logic as insert_payment_schedules
            $this->restructure_payment_schedules($id, $loan, $loan_product, $new_date, $calculation_type);
        }
        
        return true;
    }
    
    private function restructure_payment_schedules($loan_id, $loan, $loan_product, $start_date, $calculation_type)
    {
        $date = $start_date;
        $current_balance = floatval($loan->loan_principal);
        $amount = $current_balance;
        $months = intval($loan->loan_period);
        $interest = floatval($loan->loan_interest);
        $loan_customer = $loan->loan_customer;
        
        if ($calculation_type === 'Straight Line') {
            // Straight Line / Flat Rate calculation
            // Interest rate is PER PERIOD (monthly), not annual
            // Total Interest = Principal × Rate × Months
            $rate = $interest / 100;
            $total_interest = $amount * $rate * $months;
            $total_payment = $amount + $total_interest;
            $emi = round($total_payment / $months, 2);

            $principal_per_month = round($amount / $months, 2);
            $interest_per_month = round($total_interest / $months, 2);

            for ($payment_num = 1; $payment_num <= $months; $payment_num++) {
                // Last payment adjustment
                if ($payment_num == $months) {
                    $principal_per_month = $current_balance;
                    $emi = $principal_per_month + $interest_per_month;
                }

                $current_balance = round($current_balance - $principal_per_month, 2);
                if ($current_balance < 0) $current_balance = 0;

                // Calculate next payment date based on frequency
                $next_date = $this->calculate_next_payment_date($date, $payment_num, $loan->period_type, $start_date);

                $this->db->insert('payement_schedules', [
                    'customer' => $loan_customer,
                    'loan_id' => $loan_id,
                    'payment_schedule' => $next_date,
                    'payment_number' => $payment_num,
                    'amount' => round($emi, 2),
                    'principal' => round($principal_per_month, 2),
                    'interest' => round($interest_per_month, 2),
                    'paid_amount' => 0.00,
                    'loan_balance' => $current_balance,
                    'loan_date' => $start_date
                ]);
            }
        } elseif ($calculation_type === 'Reducing Balance') {
            // Reducing Balance (EMI) calculation
            // Interest rate is PER PERIOD (monthly), not annual
            $rate = $interest / 100;

            // Calculate EMI
            if ($rate > 0) {
                $emi = $amount * $rate * pow((1 + $rate), $months) / (pow((1 + $rate), $months) - 1);
            } else {
                $emi = $amount / $months;
            }
            $emi = round($emi, 2);

            for ($payment_num = 1; $payment_num <= $months; $payment_num++) {
                $interest_payment = round($current_balance * $rate, 2);
                $principal_payment = $emi - $interest_payment;

                // Last payment adjustment
                if ($payment_num == $months) {
                    $principal_payment = $current_balance;
                    $emi = $principal_payment + $interest_payment;
                }

                $current_balance = round($current_balance - $principal_payment, 2);
                if ($current_balance < 0) $current_balance = 0;

                // Calculate next payment date based on frequency
                $next_date = $this->calculate_next_payment_date($date, $payment_num, $loan->period_type, $start_date);

                $this->db->insert('payement_schedules', [
                    'customer' => $loan_customer,
                    'loan_id' => $loan_id,
                    'payment_schedule' => $next_date,
                    'payment_number' => $payment_num,
                    'amount' => round($emi, 2),
                    'principal' => round($principal_payment, 2),
                    'interest' => round($interest_payment, 2),
                    'paid_amount' => 0.00,
                    'loan_balance' => $current_balance,
                    'loan_date' => $start_date
                ]);
            }
        }
    }
    
    private function calculate_next_payment_date($base_date, $payment_number, $frequency, $start_date)
    {
        switch ($frequency) {
            case 'Monthly':
                $next_date = date('Y-m-d', strtotime("+{$payment_number} month", strtotime($start_date)));
                break;
            case '2 Weeks':
                $weeks = $payment_number * 2;
                $next_date = date('Y-m-d', strtotime("+{$weeks} weeks", strtotime($start_date)));
                break;
            case 'Weekly':
                $next_date = date('Y-m-d', strtotime("+{$payment_number} week", strtotime($start_date)));
                break;
            default:
                $next_date = date('Y-m-d', strtotime("+{$payment_number} month", strtotime($start_date)));
        }
        
        // Check if payment date landed on weekend and adjust
        $timestamp = strtotime($next_date);
        if (date('D', $timestamp) == 'Sun') {
            $next_date = date('Y-m-d', strtotime('+1 day', $timestamp));
        } elseif (date('D', $timestamp) == 'Sat') {
            $next_date = date('Y-m-d', strtotime('-1 day', $timestamp));
        }
        
        return $next_date;
    }

	function add_loan_recent($amount, $months,$interest, $loan_id, $loan_date,$loan_customer)
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

	/**
	 * Get all loans for portfolio report (Active, Closed, Written Off)
	 */
	function get_loans_for_report()
	{
		$this->db->select("loan.*, loan_products.product_name, employees.Firstname as efname, employees.Lastname as elname")
			->from($this->table)
			->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'left')
			->join('employees', 'employees.id = loan.loan_added_by', 'left');

		// Get Active, Closed, and Written Off loans
		$this->db->where_in('loan_status', array('ACTIVE', 'CLOSED', 'WRITTEN_OFF'));
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
			->join('employees','employees.id = loan.loan_added_by', 'left');
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


	/**
	 * Calculate bullet payment with monthly interest rate
	 *
	 * @param float $amount Loan principal
	 * @param int $months Loan term in months
	 * @param object $loan Loan product details
	 * @param string $loan_date Start date of loan
	 * @param float $interest Interest rate (treated as monthly rate)
	 * @return string HTML table with loan details
	 */
	private function calculateBulletPayment($amount, $months, $loan, $loan_date, $interest)
	{
		// Use interest directly as monthly rate (not annual)
		$monthly_interest_rate = $interest / 100; // Convert percentage to decimal

		// Calculate total interest (principal × monthly rate × terms)
		$total_interest = $amount * $monthly_interest_rate * $months;

		// Total payment at maturity
		$total_payment = $amount + $total_interest;

		// Calculate maturity date
		$maturity_date = date('Y-m-d', strtotime("+$months months", strtotime($loan_date)));
		$maturity_date_formatted = date('d M Y', strtotime($maturity_date));

		$currency = $this->config->item('currency_symbol');

		// Summary Section with modern styling
		$table = '<div id="calculator">';
		$table .= '<div class="summary-highlight">';
		$table .= '<table style="width:100%">';
		$table .= '<tr><td>Loan Product:</td><td>' . $loan->product_name . '</td></tr>';
		$table .= '<tr><td>Principal Amount:</td><td>' . $currency . ' ' . number_format($amount, 2) . '</td></tr>';
		$table .= '<tr><td>Interest Rate:</td><td>' . $interest . '% per month</td></tr>';
		$table .= '<tr><td>Loan Term:</td><td>' . $months . ' months</td></tr>';
		$table .= '<tr><td>Maturity Date:</td><td>' . $maturity_date_formatted . '</td></tr>';
		$table .= '<tr><td style="border-top:1px solid rgba(255,255,255,0.3); padding-top:8px;">Payment at Maturity:</td><td style="border-top:1px solid rgba(255,255,255,0.3); padding-top:8px; font-size:1.2rem;">' . $currency . ' ' . number_format($total_payment, 2) . '</td></tr>';
		$table .= '<tr><td>Total Interest:</td><td>' . $currency . ' ' . number_format($total_interest, 2) . '</td></tr>';
		$table .= '</table>';
		$table .= '</div>';

		// Payment schedule (single payment at maturity)
		$table .= '<h3>Payment Schedule</h3>';
		$table .= '<table class="table">';
		$table .= '<tr>
			<th>#</th>
			<th>Due Date</th>
			<th>Principal</th>
			<th>Interest</th>
			<th>Total</th>
		</tr>';

		$table .= '<tr>';
		$table .= '<td>1</td>';
		$table .= '<td>' . $maturity_date_formatted . '</td>';
		$table .= '<td>' . number_format($amount, 2) . '</td>';
		$table .= '<td>' . number_format($total_interest, 2) . '</td>';
		$table .= '<td>' . number_format($total_payment, 2) . '</td>';
		$table .= '</tr>';

		$table .= '</table>';

		$table .= '<p style="margin-top:1rem; color:#6b7280; font-size:0.9rem;"><i class="fa fa-info-circle"></i> For bullet loans, the entire principal plus interest is paid at maturity.</p>';

		$table .= '</div>';

		return $table;
	}

	/**
	 * Calculate early payoff amount for a bullet loan using monthly interest
	 *
	 * @param int $loan_id Loan ID
	 * @param string $payment_date Date of payment (YYYY-MM-DD)
	 * @return array Payoff details
	 */
	public function calculateBulletPayoff($loan_id, $payment_date = null)
	{
		// If no payment date provided, use current date
		if ($payment_date === null) {
			$payment_date = date('Y-m-d');
		}

		// Get loan details
		$loan = $this->get_by_id($loan_id);

		if (!$loan) {
			return false;
		}

		// Principal amount
		$principal = $loan->loan_principal;

		// Monthly interest rate (directly use the stored interest rate)
		$monthly_interest_rate = $loan->loan_interest / 100;

		// Calculate full term interest
		$full_term_interest = $principal * $monthly_interest_rate * $loan->loan_period;

		// Convert dates to DateTime objects
		$loan_start_date = new DateTime($loan->loan_date);
		$payment_datetime = new DateTime($payment_date);

		// Calculate the number of complete months elapsed
		$months_elapsed = 0;
		$years_diff = $payment_datetime->format('Y') - $loan_start_date->format('Y');
		$months_diff = $payment_datetime->format('m') - $loan_start_date->format('m');
		$days_diff = $payment_datetime->format('d') - $loan_start_date->format('d');

		// Calculate total months
		$months_elapsed = ($years_diff * 12) + $months_diff;

		// If we haven't reached the day of the month the loan started, it's not a complete month
		if ($days_diff < 0) {
			$months_elapsed--;
		}

		// Calculate days in current partial month
		$days_in_current_month = date('t', strtotime($payment_date));
		$day_of_month = (int)$payment_datetime->format('d');
		$loan_start_day = (int)$loan_start_date->format('d');

		// Calculate partial month as a fraction
		$partial_month = 0;
		if ($days_diff < 0) {
			// If payment day is before loan start day, calculate partial month
			$days_in_prev_month = date('t', strtotime('-1 month', strtotime($payment_date)));
			$partial_month = (($days_in_prev_month - $loan_start_day) + $day_of_month) / $days_in_prev_month;
		} else {
			// If payment day is after or equal to loan start day, calculate partial month
			$partial_month = ($day_of_month - $loan_start_day) / $days_in_current_month;
		}

		// Add partial month to elapsed months
		$total_months_elapsed = $months_elapsed + $partial_month;

		// Calculate interest proportion based on elapsed time
		$interest_proportion = min(1, max(0, $total_months_elapsed / $loan->loan_period));
		$payoff_interest = $full_term_interest * $interest_proportion;

		// If at maturity or beyond, charge full interest
		if ($total_months_elapsed >= $loan->loan_period) {
			$payoff_interest = $full_term_interest;
		}

		// Calculate penalty for overdue loans (past maturity)
		$penalty = 0;
		$days_overdue = 0;
		$maturity_date = date('Y-m-d', strtotime("+{$loan->loan_period} months", strtotime($loan->loan_date)));

		if ($payment_date > $maturity_date) {
			// Calculate days overdue
			$maturity_datetime = new DateTime($maturity_date);
			$days_overdue = $payment_datetime->diff($maturity_datetime)->days;

			// Get loan product to check penalty settings
			$loan_product = $this->db->where('loan_product_id', $loan->loan_product)->get('loan_products')->row();

			if ($loan_product && isset($loan_product->penalty) && $loan_product->penalty == 'yes') {
				// Get penalty threshold (days before penalty applies)
				$penalty_threshold = isset($loan_product->penalty_threshold) ? (int)$loan_product->penalty_threshold : 0;

				if ($days_overdue > $penalty_threshold) {
					$overdue_days_for_penalty = $days_overdue - $penalty_threshold;

					// Determine which penalty structure to use based on threshold
					if ($days_overdue <= $penalty_threshold) {
						// Below threshold - use below penalty rates
						$charge_type = isset($loan_product->penalty_charge_type_below) ? $loan_product->penalty_charge_type_below : 'fixed';
						$fixed_charge = isset($loan_product->penalty_fixed_charge_below) ? (float)$loan_product->penalty_fixed_charge_below : 0;
						$variable_charge = isset($loan_product->penalty_variable_charge_below) ? (float)$loan_product->penalty_variable_charge_below : 0;
					} else {
						// Above threshold - use above penalty rates
						$charge_type = isset($loan_product->penalty_charge_type_above) ? $loan_product->penalty_charge_type_above : 'fixed';
						$fixed_charge = isset($loan_product->penalty_fixed_charge_above) ? (float)$loan_product->penalty_fixed_charge_above : 0;
						$variable_charge = isset($loan_product->penalty_variable_charge_above) ? (float)$loan_product->penalty_variable_charge_above : 0;
					}

					// Calculate penalty based on charge type
					if ($charge_type == 'fixed') {
						$penalty = $fixed_charge * $overdue_days_for_penalty;
					} elseif ($charge_type == 'variable') {
						// Variable charge is typically a percentage of outstanding amount per day
						$outstanding = $principal + $payoff_interest;
						$penalty = ($outstanding * ($variable_charge / 100)) * $overdue_days_for_penalty;
					} elseif ($charge_type == 'both') {
						$outstanding = $principal + $payoff_interest;
						$penalty = ($fixed_charge + ($outstanding * ($variable_charge / 100))) * $overdue_days_for_penalty;
					}
				}
			}
		}

		// Total payoff amount (principal + interest + penalty)
		$total_payoff = $principal + $payoff_interest + $penalty;

		return [
			'principal' => $principal,
			'interest' => $payoff_interest,
			'penalty' => $penalty,
			'total_payoff' => $total_payoff,
			'months_elapsed' => $total_months_elapsed,
			'total_months' => $loan->loan_period,
			'interest_proportion' => $interest_proportion,
			'payment_date' => $payment_date,
			'monthly_interest_rate' => $monthly_interest_rate,
			'full_term_interest' => $full_term_interest,
			'maturity_date' => $maturity_date,
			'days_overdue' => $days_overdue
		];
	}

	/**
	 * Insert bullet payment schedule with the correct interest calculation
	 *
	 * @param int $loan_id Loan ID
	 * @param object $loan Loan product details
	 * @param float $amount Principal amount
	 * @param int $months Loan term in months
	 * @param float $interest Monthly interest rate
	 * @param string $start_date Loan start date
	 * @param int $loan_customer Customer ID
	 */
	private function insert_bullet_payment_schedule($loan_id, $loan, $amount, $months, $interest, $start_date, $loan_customer)
	{
		// Calculate maturity date
		$maturity_date = date('Y-m-d', strtotime("+$months months", strtotime($start_date)));

		// Calculate total interest using monthly interest rate
		$monthly_interest_rate = $interest / 100;
		$total_interest = $amount * $monthly_interest_rate * $months;
		$total_payment = $amount + $total_interest;

		// For bullet loans, we create just one payment entry for the full amount
		$this->db->insert('payement_schedules', [
			'customer' => $loan_customer,
			'loan_id' => $loan_id,
			'payment_schedule' => $maturity_date,
			'payment_number' => 1,
			'amount' => $total_payment, // Principal + Interest
			'principal' => $amount,
			'interest' => $total_interest,
			'paid_amount' => 0.00,
			'loan_balance' => $amount,
			'loan_date' => $start_date,
			'is_bullet_payment' => 1 // Flag for bullet payment
		]);
	}

	/**
	 * Get Obligor Listing Report Data
	 * Returns all loans with customer details, outstanding balance, and maturity information
	 *
	 * @return array Query result
	 */
	/**
	 * Get Obligor Listing Report Data with filters
	 * Returns all loans with customer details, outstanding balance, and maturity information
	 *
	 * @param array $filters Array of filter parameters (loan_status, currency, customer_type, from_date, to_date)
	 * @return array Query result
	 */
	function obligor_listing($filters = array())
	{
		$this->db->select("
			loan.loan_id,
			loan.loan_number,
			loan.loan_principal,
			loan.loan_interest,
			loan.loan_status,
			loan.customer_type,
			loan.loan_customer,
			loan.currency as loan_currency,
			loan.loan_date,
			loan.loan_product,
			loan_products.product_name as facility_type,
			individual_customers.id as ind_id,
			individual_customers.Firstname as ind_firstname,
			individual_customers.Lastname as ind_lastname,
			individual_customers.Profession as ind_profession,
			corporate_customers.id as corp_id,
			corporate_customers.EntityName as corp_name,
			corporate_customers.industry_sector as corp_industry,
			corporate_customers.category as corp_category,
			currencies.currency_name,
			currencies.currency_code,
			(SELECT SUM(ps.amount) FROM payement_schedules ps WHERE ps.loan_id = loan.loan_id) as total_scheduled,
			(SELECT SUM(ps.paid_amount) FROM payement_schedules ps WHERE ps.loan_id = loan.loan_id) as total_paid,
			(SELECT MAX(ps.payment_schedule) FROM payement_schedules ps WHERE ps.loan_id = loan.loan_id) as last_payment_date
		");

		$this->db->from('loan');
		$this->db->join('individual_customers', 'individual_customers.id = loan.loan_customer AND loan.customer_type = "individual"', 'left');
		$this->db->join('corporate_customers', 'corporate_customers.id = loan.loan_customer AND loan.customer_type = "institution"', 'left');
		$this->db->join('currencies', 'currencies.currency_id = loan.currency', 'left');
		$this->db->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'left');

		// Default filter - show active, approved, and disbursed loans
		if(empty($filters['loan_status']) || $filters['loan_status'] == 'All'){
			$this->db->where_in('loan.loan_status', ['ACTIVE', 'APPROVED', 'DISBURSED']);
		} else {
			$this->db->where('loan.loan_status', $filters['loan_status']);
		}

		// Filter by currency
		if(!empty($filters['currency']) && $filters['currency'] != 'All'){
			$this->db->where('loan.currency', $filters['currency']);
		}

		// Filter by customer type
		if(!empty($filters['customer_type']) && $filters['customer_type'] != 'All'){
			$this->db->where('loan.customer_type', $filters['customer_type']);
		}

		// Filter by date range
		if(!empty($filters['from_date']) && !empty($filters['to_date'])){
			$this->db->where('loan.loan_date >=', $filters['from_date']);
			$this->db->where('loan.loan_date <=', $filters['to_date']);
		}

		// Filter by loan product
		if(!empty($filters['loan_product']) && $filters['loan_product'] != 'All'){
			$this->db->where('loan.loan_product', $filters['loan_product']);
		}

		$this->db->order_by('loan.loan_id', 'DESC');

		return $this->db->get()->result();
	}

	/**
	 * Get Portfolio Listing Report Data with filters
	 * Returns all loans with customer details, interest amounts, repayment details, and tenor
	 *
	 * @param array $filters Array of filter parameters (loan_status, currency, customer_type, from_date, to_date)
	 * @return array Query result
	 */
	function portfolio_listing($filters = array())
	{
		$this->db->select("
			loan.loan_id,
			loan.loan_number,
			loan.loan_principal,
			loan.loan_interest,
			loan.loan_interest_amount,
			loan.loan_status,
			loan.customer_type,
			loan.loan_customer,
			loan.currency as loan_currency,
			loan.loan_date,
			loan.loan_product,
			loan.loan_period,
			loan.period_type,
			loan_products.product_name as facility_type,
			loan_products.frequency as loan_frequency,
			individual_customers.id as ind_id,
			individual_customers.Firstname as ind_firstname,
			individual_customers.Lastname as ind_lastname,
			individual_customers.Profession as ind_profession,
			corporate_customers.id as corp_id,
			corporate_customers.EntityName as corp_name,
			corporate_customers.industry_sector as corp_industry,
			corporate_customers.category as corp_category,
			currencies.currency_name,
			currencies.currency_code,
			(SELECT SUM(ps.amount) FROM payement_schedules ps WHERE ps.loan_id = loan.loan_id) as total_scheduled,
			(SELECT SUM(ps.paid_amount) FROM payement_schedules ps WHERE ps.loan_id = loan.loan_id) as total_paid,
			(SELECT SUM(
				CASE
					WHEN ps.paid_amount >= ps.interest THEN ps.interest
					ELSE ps.paid_amount
				END
			) FROM payement_schedules ps WHERE ps.loan_id = loan.loan_id) as realized_interest,
			(SELECT MAX(ps.payment_schedule) FROM payement_schedules ps WHERE ps.loan_id = loan.loan_id) as last_payment_date
		");

		$this->db->from('loan');
		$this->db->join('individual_customers', 'individual_customers.id = loan.loan_customer AND loan.customer_type = "individual"', 'left');
		$this->db->join('corporate_customers', 'corporate_customers.id = loan.loan_customer AND loan.customer_type = "institution"', 'left');
		$this->db->join('currencies', 'currencies.currency_id = loan.currency', 'left');
		$this->db->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'left');

		// Default filter - show active, approved, and disbursed loans
		if(empty($filters['loan_status']) || $filters['loan_status'] == 'All'){
			$this->db->where_in('loan.loan_status', ['ACTIVE', 'APPROVED', 'DISBURSED']);
		} else {
			$this->db->where('loan.loan_status', $filters['loan_status']);
		}

		// Filter by currency
		if(!empty($filters['currency']) && $filters['currency'] != 'All'){
			$this->db->where('loan.currency', $filters['currency']);
		}

		// Filter by customer type
		if(!empty($filters['customer_type']) && $filters['customer_type'] != 'All'){
			$this->db->where('loan.customer_type', $filters['customer_type']);
		}

		// Filter by date range
		if(!empty($filters['from_date']) && !empty($filters['to_date'])){
			$this->db->where('loan.loan_date >=', $filters['from_date']);
			$this->db->where('loan.loan_date <=', $filters['to_date']);
		}

		// Filter by loan product
		if(!empty($filters['loan_product']) && $filters['loan_product'] != 'All'){
			$this->db->where('loan.loan_product', $filters['loan_product']);
		}

		$this->db->order_by('loan.loan_id', 'DESC');

		return $this->db->get()->result();
	}
}

