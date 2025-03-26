<?php


class Reports extends CI_Controller
{
    public  function __construct()
    {
        parent::__construct();
        $this->load->model('Payement_schedules_model');
        $this->load->model('Loan_model');
        $this->load->model('Employees_model');
        $this->load->model('Individual_customers_model');
        $this->load->model('Transactions_model');
        $this->load->model('Global_config_model');
        $this->load->model('Borrowed_repayements_model');
    }
    public function parfilter($page = null){
        $officerid= $this->input->get('officer');


        if($officerid){
            $this->session->set_userdata('officerid',$officerid);

            $config = array(
                'base_url' => base_url('report/par_report'), // Set the base URL for pagination
                'total_rows' => $this->Loan_model->count_summaryu($this->session->userdata('officerid')), // Count total rows
                'per_page' => 100, // Number of rows per page
                'uri_segment' => 3, // URI segment that contains the page number
            );

            $this->pagination->initialize($config);
            $data['pagination'] = $this->pagination->create_links();

            $offset = ($page - 1) * $config['per_page'];
            $data['summary'] = $this->Loan_model->get_summaryu($this->session->userdata('officerid'), $config['per_page'], $offset);

            $this->load->view('admin/header');
            $this->load->view('reports/par_report',$data);
            $this->load->view('admin/footer');

        }else{
            $this->session->set_userdata('officerid',$officerid);
            $config = array(
                'base_url' => base_url('report/par_report'), // Set the base URL for pagination
                'total_rows' => $this->Loan_model->count_summaryu($this->session->userdata('officerid')), // Count total rows
                'per_page' => 100, // Number of rows per page
                'uri_segment' => 3, // URI segment that contains the page number
            );

            $this->pagination->initialize($config);
            $data['pagination'] = $this->pagination->create_links();

            $offset = ($page - 1) * $config['per_page'];
            $data['summary'] = $this->Loan_model->get_summaryu($this->session->userdata('officerid'), $config['per_page'], $offset);

            $this->load->view('admin/header');
            $this->load->view('reports/par_report',$data);
            $this->load->view('admin/footer');

        }

    }
    public function period_analysis(){


        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){
            $data['total_loan_principal'] = $this->Loan_model->sum_loans($from,$to);
            $data['total_loans'] = $this->Loan_model->count_disbursed_loans($from,$to);
            $data['customers'] = $this->Individual_customers_model->count_active($from,$to);
            $data['employees'] = $this->Employees_model->count_active($from,$to);
            $this->load->view('admin/header');
            $this->load->view('reports/period_analysis',$data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['total_loan_principal'] = $this->Loan_model->sum_loans($from,$to);
            $data['total_loans'] = $this->Loan_model->count_disbursed_loans($from,$to);
            $data['customers'] = $this->Individual_customers_model->count_active($from,$to);
            $data['employees'] = $this->Employees_model->count_active($from,$to);
            $data['product'] ='Report';
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('reports/analysis_pdf', $data,true);
            $this->pdf->createPDF($html, "Period analysis report as on".date('Y-m-d'), true,'A4','landscape');
        }elseif($search=='excel'){

        }else {
            $data['total_loan_principal'] = $this->Loan_model->sum_loans($from,$to);
            $data['total_loans'] = $this->Loan_model->count_disbursed_loans($from,$to);
            $data['customers'] = $this->Individual_customers_model->count_active($from,$to);
            $data['employees'] = $this->Employees_model->count_active($from,$to);
            $this->load->view('admin/header');
            $this->load->view('reports/period_analysis', $data);
            $this->load->view('admin/footer');
        }
    }public function customers_report(){

    $q = urldecode($this->input->get('q', TRUE));
    $from = $this->input->get('from');
    $start = intval($this->input->get('start'));
    $to = $this->input->get('to');
    $search = $this->input->get('search');
    if($search=="filter"){
        $data['total_loan_principal'] = $this->Loan_model->sum_loans($from,$to);
        $data['total_loans'] = $this->Loan_model->count_disbursed_loans($from,$to);
        $data['customers'] = $this->Individual_customers_model->count_active($from,$to);
        $data['employees'] = $this->Employees_model->count_active($from,$to);
        $this->load->view('admin/header');
        $this->load->view('reports/period_analysis',$data);
        $this->load->view('admin/footer');
    }elseif($search=='pdf'){
        $data['total_loan_principal'] = $this->Loan_model->sum_loans($from,$to);
        $data['total_loans'] = $this->Loan_model->count_disbursed_loans($from,$to);
        $data['customers'] = $this->Individual_customers_model->count_active($from,$to);
        $data['employees'] = $this->Employees_model->count_active($from,$to);
        $data['product'] ='Report';
        $data['from'] = $from;
        $data['to'] = $to;
        $this->load->library('Pdf');
        $html = $this->load->view('reports/analysis_pdf', $data,true);
        $this->pdf->createPDF($html, "Period analysis report as on".date('Y-m-d'), true,'A4','landscape');
    }elseif($search=='excel'){

    }else {
        if ($from <> '' || $to <> '') {
            $config['base_url'] = base_url() . 'Reports/index?from=' . urlencode($from);
            $config['first_url'] = base_url() . 'Reports/index?from=' . urlencode($from);
        } else {
            $config['base_url'] = base_url() . 'Reports/index';
            $config['first_url'] = base_url() . 'Reports/index';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Individual_customers_model->total_rows($from);
        $individual_customers = $this->Individual_customers_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'individual_customers_data' => $this->Individual_customers_model->get_all(),
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
//        $menu_toggle['toggles'] = 43;
        $this->load->view('admin/header');
        $this->load->view('reports/customers_report', $data);
        $this->load->view('admin/footer');
    }
}

    public function collateral()
    {

        $this->load->view('admin/header');
        $this->load->view('reports/collateral_view');
        $this->load->view('admin/footer');

    }

    public function track_collateral()
    {

        $loannumber = $this->input->POST('loannumber');

        $loandata=get_by_id('loan','loan_id',$loannumber );

        $search = $this->input->POST('search');
        if($search=="filter"){
            $data['loan_data'] = $this->Loan_files_model->track_collateral($loandata->loan_id);
            $this->load->view('admin/header');
            $this->load->view('reports/collateral_details',$data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Loan_files_model->track_collateral($loannumber);

            $this->load->library('Pdf');
            $html = $this->load->view('reports/collateral_pdf', $data,true);
            $this->pdf->createPDF($html, "collateral report as on".date('Y-m-d'), true,'A4','landscape');
        }else {
            $this->load->view('admin/header');
            $this->load->view('reports/collateral_view');
            $this->load->view('admin/footer');
        }
    }


    public function financial_analysis(){


        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){
            $data['interests_income'] = $this->Payement_schedules_model->sum_interests($from,$to);
            $data['admin_income'] = $this->Transactions_model->sum_admin_charges($from,$to);
            $data['late_fee'] = $this->Transactions_model->sum_admin_charges_late($from,$to);
            $data['bad_debits'] = $this->Payement_schedules_model->bad_debits($from,$to);
            $data['commissions'] = 0;
            $data['interest_paid'] = $this->Borrowed_repayements_model->sum_interest_paid($from,$to);
            $data['expenses'] = $this->Transactions_model->sum_expenses($from,$to);
            $this->load->view('admin/header');
            $this->load->view('reports/financial_analysis', $data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['interests_income'] = $this->Payement_schedules_model->sum_interests($from,$to);
            $data['admin_income'] = $this->Transactions_model->sum_admin_charges($from,$to);
            $data['late_fee'] = $this->Transactions_model->sum_admin_charges_late($from,$to);
            $data['bad_debits'] = $this->Payement_schedules_model->bad_debits($from,$to);
            $data['commissions'] = 0;
            $data['interest_paid'] = $this->Borrowed_repayements_model->sum_interest_paid($from,$to);
            $data['expenses'] = $this->Transactions_model->sum_expenses($from,$to);
            $this->load->library('Pdf');
            $html = $this->load->view('reports/financial_analysis_pdf', $data,true);
            $this->pdf->createPDF($html, "Financial analysis report as on".date('Y-m-d'), true,'A4','landscape');
        }elseif($search=='excel'){

        }else {
            $data['interests_income'] = $this->Payement_schedules_model->sum_interests($from,$to);
            $data['admin_income'] = $this->Transactions_model->sum_admin_charges($from,$to);
            $data['late_fee'] = $this->Transactions_model->sum_admin_charges_late($from,$to);
            $data['bad_debits'] = $this->Payement_schedules_model->bad_debits($from,$to);
            $data['commissions'] = 0;
            $data['interest_paid'] = $this->Borrowed_repayements_model->sum_interest_paid($from,$to);
            $data['expenses'] = $this->Transactions_model->sum_expenses($from,$to);
            $this->load->view('admin/header');
            $this->load->view('reports/financial_analysis', $data);
            $this->load->view('admin/footer');
        }
    }

    function client_summary(){
        $product = $this->input->get('loannumber');
        $search = $this->input->get('search');
        if($search=="filter"){
            $data['loan_data'] = $this->Loan_model->report_client_summary($product);
            $data['loannumber'] = $product;
            $this->load->view('admin/header');
            $this->load->view('reports/client_summary_reports',$data);
            $this->load->view('admin/footer');
        }else {

            $this->load->view('admin/header');
            $this->load->view('reports/client_summary_filter');
            $this->load->view('admin/footer');
        }
    }


    public function tray(){
        $this->Loan_model->update_defaulters();
//	$Date = "2010-09-17";
//	echo date('Y-m-d', strtotime($Date. ' + 1 days'));
//	echo date('Y-m-d', strtotime($Date. ' + 2 days'));
    }
    public function arrears(){

        $product = $this->input->get('loan');

        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){
            $data['loan_data'] = $this->Payement_schedules_model->arrears($product,$from,$to);
            $this->load->view('admin/header');
            $this->load->view('reports/arrears',$data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Payement_schedules_model->arrears($product,$from,$to);

            $data['product'] =($product=="All") ? "All loans" : get_by_id('loan','loan_id',$product)->loan_number;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('reports/arrears_pdf', $data,true);
            $this->pdf->createPDF($html, "Arrears report as on".date('Y-m-d'), true,'A4','landscape');
        }elseif($search=='excel'){

        }else {
            $data['loan_data'] = $this->Payement_schedules_model->arrears($product,$from,$to);
            $this->load->view('admin/header');
            $this->load->view('reports/arrears', $data);
            $this->load->view('admin/footer');
        }

    }
    public function to_pay_today(){


        $search = $this->input->get('search');
        if($search=='pdf'){
            $data['loan_data'] = $this->Payement_schedules_model->payment_today();
            $this->load->library('Pdf');
            $html = $this->load->view('reports/to_pay_today_pdf', $data,true);
            $this->pdf->createPDF($html, "Arrears report as on".date('Y-m-d'), true,'A4','landscape');
        }elseif($search=='excel'){

        }else {
            $data['loan_data'] = $this->Payement_schedules_model->payment_today();
            $menu_toggle['toggles'] = 50;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('reports/to_pay_today', $data);
            $this->load->view('admin/footer');
        }

    }
    public function to_pay_month(){


        $search = $this->input->get('search');
        if($search=='pdf'){
            $data['loan_data'] = $this->Payement_schedules_model->payment_month();
            $this->load->library('Pdf');
            $html = $this->load->view('reports/to_pay_today_pdf', $data,true);
            $this->pdf->createPDF($html, "Arrears report as on".date('Y-m-d'), true,'A4','landscape');
        }elseif($search=='excel'){

        }else {
            $data['loan_data'] = $this->Payement_schedules_model->payment_month();
            $menu_toggle['toggles'] = 50;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('reports/to_pay_month', $data);
            $this->load->view('admin/footer');
        }

    }
    public function to_pay_week(){


        $search = $this->input->get('search');
        if($search=='pdf'){
            $data['loan_data'] = $this->Payement_schedules_model->payment_today();
            $this->load->library('Pdf');
            $html = $this->load->view('reports/to_pay_today_pdf', $data,true);
            $this->pdf->createPDF($html, "Arrears report as on".date('Y-m-d'), true,'A4','landscape');
        }elseif($search=='excel'){

        }else {
            $data['loan_data'] = $this->Payement_schedules_model->payment_week();
            $menu_toggle['toggles'] = 50;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('reports/to_pay_week', $data);
            $this->load->view('admin/footer');
        }

    }
    public function par_report(){

        $product = $this->input->get('product');
        $officer = $this->input->get('officer');
        $loan_number = $this->input->get('loan_number');


        $data['summary'] = $this->Loan_model->get_summaryu($officer,$product, $loan_number);

        $this->load->view('admin/header');
        $this->load->view('reports/par_report',$data);
            $this->load->view('admin/footer');


    }
    public function payments(){

        $product = $this->input->get('loan');
        $transaction_type_id = $this->input->get('transaction_type_id');

        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){
            $data['loan_data'] = $this->Transactions_model->report($transaction_type_id,$product,$from,$to);
            $this->load->view('admin/header');
            $this->load->view('reports/payments',$data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Transactions_model->report($transaction_type_id,$product,$from,$to);
            $data['product'] =($product=="All") ? "All loans" : get_by_id('loan','loan_id',$product)->loan_number;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('reports/payments_pdf', $data,true);
            $this->pdf->createPDF($html, "Payments report as on".date('Y-m-d'), true,'A4','landscape');
        }elseif($search=='excel'){
            $data['payments_data'] = $this->Transactions_model->report($transaction_type_id,$product,$from,$to);
            $this->excel_payments($data);
        }else {
            $data['loan_data'] = $this->Transactions_model->report($transaction_type_id,$product,$from,$to);
            $this->load->view('admin/header');
            $this->load->view('reports/payments', $data);
            $this->load->view('admin/footer');
        }
    }
    function try_export(){
        $this->load->view('export');

    }

    function export_it()
    {

        $filename = $this->input->get('filename');
        $search = $this->input->get('search');


        if($search == 'filter'){
            $data['toexport'] = $this->Global_config_model->get_all() ;
            $this->load->view('export', $data);
        }elseif($search=='export'){
            $namaFile = "agent_cro_report.xls";

            $tablehead = 0;
            $tablebody = 1;
            $nourut = 1;
            //penulisan header
            xlsHeaders($namaFile);

            xlsBOF();

            $kolomhead = 0;
            xlsWriteLabel($tablehead, $kolomhead++, "No");
            xlsWriteLabel($tablehead, $kolomhead++, "Repayment Automatic");
            xlsWriteLabel($tablehead, $kolomhead++, "cron path");
            $toe = $this->Global_config_model->get_all() ;
            foreach ($toe as $data) {
                $kolombody = 0;

                //ubah xlsWriteLabel menjadi xlsWriteNumber untuk kolom numeric
                xlsWriteNumber($tablebody, $kolombody++, $nourut);
                xlsWriteLabel($tablebody, $kolombody++, $data->repayment_automatic);
                xlsWriteLabel($tablebody, $kolombody++, $data->cron_path);


                $tablebody++;
                $nourut++;
            }

            xlsEOF();
            exit();
        }else{
            $data['toexport'] = array();
            $this->load->view('export', $data);
        }



    }

}
