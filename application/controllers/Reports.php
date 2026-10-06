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
        $this->load->model('Collateral_model');
        $this->load->helper('exportexcel');
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
            $data['total_loan_principal'] = $this->Loan_model->sum_loans($from, $to);
            $data['total_loans'] = $this->Loan_model->count_disbursed_loans($from, $to);
            $data['customers'] = $this->Individual_customers_model->count_active($from, $to);
            $data['employees'] = $this->Employees_model->count_active($from, $to);
            $this->export_database_report_excel('Period_Analysis_Report', 'reports/period_analysis', $data);
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

    $q = urldecode((string) ($this->input->get('q', TRUE) ?? ''));
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
        $data = array('individual_customers_data' => $this->Individual_customers_model->get_all(), 'q' => $q, 'pagination' => '', 'total_rows' => 0, 'start' => 0);
        $this->export_database_report_excel('Customers_Report', 'reports/customers_report', $data, 'data-table');
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
            $data['interests_income'] = $this->Payement_schedules_model->sum_interests($from, $to);
            $data['admin_income'] = $this->Transactions_model->sum_admin_charges($from, $to);
            $data['late_fee'] = $this->Transactions_model->sum_admin_charges_late($from, $to);
            $data['bad_debits'] = $this->Payement_schedules_model->bad_debits($from, $to);
            $data['commissions'] = 0;
            $data['interest_paid'] = $this->Borrowed_repayements_model->sum_interest_paid($from, $to);
            $data['expenses'] = $this->Transactions_model->sum_expenses($from, $to);
            $this->export_database_report_excel('Financial_Analysis_Report', 'reports/financial_analysis', $data);
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
        } elseif ($search === 'excel') {
            $data['loan_data'] = $this->Loan_model->report_client_summary($product);
            $data['loannumber'] = $product;
            $this->export_database_report_excel('Client_Summary_Report', 'reports/client_summary_reports', $data, 'resultclientsummary');
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

        // Set default values to show all arrears
        $product = $this->input->get('loan') ? $this->input->get('loan') : 'All';
        $from = $this->input->get('from') ? $this->input->get('from') : '';
        $to = $this->input->get('to') ? $this->input->get('to') : '';
        $search = $this->input->get('search');

        // Get arrears data - always populate (not just on filter)
        $data['loan_data'] = $this->Payement_schedules_model->arrears($product,$from,$to);

        if($search=='pdf'){
            $data['product'] =($product=="All") ? "All loans" : get_by_id('loan','loan_id',$product)->loan_number;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('reports/arrears_pdf', $data,true);
            $this->pdf->createPDF($html, "Arrears report as on".date('Y-m-d'), true,'A4','landscape');
        }elseif($search=='excel'){
            $this->export_database_report_excel('Arrears_Report', 'reports/arrears', $data, 'data-table');
        }else {
            // Always show arrears data (default view or after filter)
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
            $data['loan_data'] = $this->Payement_schedules_model->payment_today();
            $this->export_database_report_excel('To_Pay_Today_Report', 'reports/to_pay_today', $data, 'data-table');
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
            $data['loan_data'] = $this->Payement_schedules_model->payment_month();
            $this->export_database_report_excel('To_Pay_Month_Report', 'reports/to_pay_month', $data, 'data-table');
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
            $data['loan_data'] = $this->Payement_schedules_model->payment_week();
            $this->export_database_report_excel('To_Pay_Week_Report', 'reports/to_pay_week', $data, 'data-table');
        }else {
            $data['loan_data'] = $this->Payement_schedules_model->payment_week();
            $menu_toggle['toggles'] = 50;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('reports/to_pay_week', $data);
            $this->load->view('admin/footer');
        }

    }

    public function collection_sheet(){
        // Get filter parameters
        $period_type = $this->input->get('period') ? $this->input->get('period') : 'daily';
        $from_date = $this->input->get('from') ? $this->input->get('from') : '';
        $to_date = $this->input->get('to') ? $this->input->get('to') : '';
        $loan_id = $this->input->get('loan') ? $this->input->get('loan') : 'All';
        $search = $this->input->get('search');

        // Get collection data
        $data['loan_data'] = $this->Payement_schedules_model->collection_sheet($period_type, $from_date, $to_date, $loan_id);
        $data['period_type'] = $period_type;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;

        if($search == 'pdf'){
            $this->load->library('Pdf');
            $html = $this->load->view('reports/collection_sheet_pdf', $data, true);
            $this->pdf->createPDF($html, "Collection Sheet - ".date('Y-m-d'), true, 'A4', 'landscape');
        }elseif($search == 'excel'){
            $this->export_database_report_excel('Collection_Sheet_Report', 'reports/collection_sheet', $data, 'data-table');
        }else {
            $menu_toggle['toggles'] = 50;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('reports/collection_sheet', $data);
            $this->load->view('admin/footer');
        }
    }
    public function par_report(){

        $product = $this->input->get('product');
        $officer = $this->input->get('officer');
        $loan_number = $this->input->get('loan_number');


        $data['summary'] = $this->Loan_model->get_summaryu($officer,$product, $loan_number);

        $search = $this->input->get('search');
        if ($search === 'excel') {
            $this->export_database_report_excel('PAR_Report', 'reports/par_report', $data, 'resulta');
        }

        $this->load->view('admin/header');
        $this->load->view('reports/par_report',$data);
            $this->load->view('admin/footer');


    }
    public function payments(){

        // Set default values to show all payments
        $product = $this->input->get('loan') ? $this->input->get('loan') : 'All';
        $transaction_type_id = $this->input->get('transaction_type_id') ? $this->input->get('transaction_type_id') : 'All';
        $from = $this->input->get('from') ? $this->input->get('from') : '';
        $to = $this->input->get('to') ? $this->input->get('to') : '';
        $search = $this->input->get('search');

        // Always get payment data (not just on filter)
        $data['loan_data'] = $this->Transactions_model->report($transaction_type_id,$product,$from,$to);

        if($search=='pdf'){
            $data['product'] =($product=="All") ? "All loans" : get_by_id('loan','loan_id',$product)->loan_number;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('reports/payments_pdf', $data,true);
            $this->pdf->createPDF($html, "Payments report as on".date('Y-m-d'), true,'A4','landscape');
        }elseif($search=='excel'){
            $this->export_database_report_excel('Payments_Report', 'reports/payments', $data, 'data-table');
        }else {
            // Always show payment data (default view or after filter)
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

    public function obligor_listing(){

        $search = $this->input->get('search');

        // Get filter parameters
        $filters = array(
            'loan_status' => $this->input->get('loan_status') ? $this->input->get('loan_status') : 'All',
            'currency' => $this->input->get('currency') ? $this->input->get('currency') : 'All',
            'customer_type' => $this->input->get('customer_type') ? $this->input->get('customer_type') : 'All',
            'loan_product' => $this->input->get('loan_product') ? $this->input->get('loan_product') : 'All',
            'loan_officer' => $this->input->get('loan_officer') ? $this->input->get('loan_officer') : 'All',
            'from_date' => $this->input->get('from_date') ? $this->input->get('from_date') : '',
            'to_date' => $this->input->get('to_date') ? $this->input->get('to_date') : ''
        );

        // Get obligor listing data with filters
        $data['loan_data'] = $this->Loan_model->obligor_listing($filters);
        $data['filters'] = $filters;

        if($search == 'pdf'){
            $this->load->library('Pdf');
            $html = $this->load->view('reports/obligor_listing_pdf', $data, true);
            $this->pdf->createPDF($html, "Obligor Listing - ".date('Y-m-d'), true, 'A4', 'landscape');
        }elseif($search == 'excel'){
            $this->export_database_report_excel('Obligor_Listing', 'reports/obligor_listing', $data, 'data-table');
        }else {
            $this->load->view('admin/header');
            $this->load->view('reports/obligor_listing', $data);
            $this->load->view('admin/footer');
        }

    }

    private function excel_obligor_listing($data){
        $this->load->helper('exportexcel');

        $namaFile = "obligor_listing_".date('Y-m-d').".xls";

        $tablehead = 0;
        $tablebody = 1;
        $nourut = 1;

        //penulisan header
        xlsHeaders($namaFile);
        xlsBOF();

        $kolomhead = 0;
        xlsWriteLabel($tablehead, $kolomhead++, "No");
        xlsWriteLabel($tablehead, $kolomhead++, "Client Name");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Number");
        xlsWriteLabel($tablehead, $kolomhead++, "Amount Disbursed");
        xlsWriteLabel($tablehead, $kolomhead++, "Amount Outstanding");
        xlsWriteLabel($tablehead, $kolomhead++, "% of Loan Book");
        xlsWriteLabel($tablehead, $kolomhead++, "Currency");
        xlsWriteLabel($tablehead, $kolomhead++, "Days to Maturity");
        xlsWriteLabel($tablehead, $kolomhead++, "Facility Type");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Status");
        xlsWriteLabel($tablehead, $kolomhead++, "Offtaker");
        xlsWriteLabel($tablehead, $kolomhead++, "Client Industry");

        foreach ($data['loan_data'] as $loan) {
            // Determine customer name and details based on customer type
            if($loan->customer_type == 'individual'){
                $customer_name = $loan->ind_firstname.' '.$loan->ind_lastname;
                $offtaker = '-';
                $industry = $loan->ind_profession ? $loan->ind_profession : '-';
            }elseif($loan->customer_type == 'institution'){
                $customer_name = $loan->corp_name ? $loan->corp_name : 'N/A';
                $offtaker = $loan->corp_category ? ucfirst(str_replace('_', ' ', $loan->corp_category)) : '-';
                $industry = $loan->corp_industry ? $loan->corp_industry : '-';
            }else{
                $customer_name = 'Unknown';
                $offtaker = '-';
                $industry = '-';
            }

            // Calculate outstanding balance
            $outstanding = $loan->total_scheduled - $loan->total_paid;

            // Calculate days to maturity
            $days_to_maturity = '-';
            if($loan->last_payment_date){
                $today = new DateTime();
                $maturity_date = new DateTime($loan->last_payment_date);
                $interval = $today->diff($maturity_date);

                if($maturity_date < $today){
                    $days_to_maturity = 'Overdue (' . $interval->days . ' days)';
                }else{
                    $days_to_maturity = $interval->days . ' days';
                }
            }

            // Currency display
            $currency = $loan->currency_name ? $loan->currency_name : ($loan->currency_code ? $loan->currency_code : '-');

            // Facility type
            $facility_type = $loan->facility_type ? $loan->facility_type : '-';

            $kolombody = 0;

            xlsWriteNumber($tablebody, $kolombody++, $nourut);
            xlsWriteLabel($tablebody, $kolombody++, $customer_name);
            xlsWriteLabel($tablebody, $kolombody++, $loan->loan_number);
            xlsWriteNumber($tablebody, $kolombody++, $loan->loan_principal);
            xlsWriteNumber($tablebody, $kolombody++, $outstanding);
            xlsWriteLabel($tablebody, $kolombody++, $loan->loan_interest.'%');
            xlsWriteLabel($tablebody, $kolombody++, $currency);
            xlsWriteLabel($tablebody, $kolombody++, $days_to_maturity);
            xlsWriteLabel($tablebody, $kolombody++, $facility_type);
            xlsWriteLabel($tablebody, $kolombody++, $loan->loan_status);
            xlsWriteLabel($tablebody, $kolombody++, $offtaker);
            xlsWriteLabel($tablebody, $kolombody++, $industry);

            $tablebody++;
            $nourut++;
        }

        xlsEOF();
        exit();
    }

    public function portfolio_listing(){

        $search = $this->input->get('search');

        // Get filter parameters
        $filters = array(
            'loan_status' => $this->input->get('loan_status') ? $this->input->get('loan_status') : 'All',
            'currency' => $this->input->get('currency') ? $this->input->get('currency') : 'All',
            'customer_type' => $this->input->get('customer_type') ? $this->input->get('customer_type') : 'All',
            'loan_product' => $this->input->get('loan_product') ? $this->input->get('loan_product') : 'All',
            'from_date' => $this->input->get('from_date') ? $this->input->get('from_date') : '',
            'to_date' => $this->input->get('to_date') ? $this->input->get('to_date') : ''
        );

        // Get portfolio listing data with filters
        $data['loan_data'] = $this->Loan_model->portfolio_listing($filters);
        $data['filters'] = $filters;

        if($search == 'pdf'){
            $this->load->library('Pdf');
            $html = $this->load->view('reports/portfolio_listing_pdf', $data, true);
            $this->pdf->createPDF($html, "Portfolio Listing - ".date('Y-m-d'), true, 'A4', 'landscape');
        }elseif($search == 'excel'){
            $this->export_database_report_excel('Portfolio_Listing', 'reports/portfolio_listing', $data, 'data-table');
        }else {
            $this->load->view('admin/header');
            $this->load->view('reports/portfolio_listing', $data);
            $this->load->view('admin/footer');
        }

    }

    private function excel_portfolio_listing($data){
        $namaFile = "portfolio_listing_".date('Y-m-d').".xls";

        $tablehead = 0;
        $tablebody = 1;
        $nourut = 1;

        //penulisan header
        xlsHeaders($namaFile);
        xlsBOF();

        $kolomhead = 0;
        xlsWriteLabel($tablehead, $kolomhead++, "No");
        xlsWriteLabel($tablehead, $kolomhead++, "Client Name");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Number");
        xlsWriteLabel($tablehead, $kolomhead++, "Amount Disbursed");
        xlsWriteLabel($tablehead, $kolomhead++, "Amount Outstanding");
        xlsWriteLabel($tablehead, $kolomhead++, "Interest Amount");
        xlsWriteLabel($tablehead, $kolomhead++, "Rollover Fees");
        xlsWriteLabel($tablehead, $kolomhead++, "Realized Interest");
        xlsWriteLabel($tablehead, $kolomhead++, "Amount Repaid");
        xlsWriteLabel($tablehead, $kolomhead++, "% of Loan Book");
        xlsWriteLabel($tablehead, $kolomhead++, "Currency");
        xlsWriteLabel($tablehead, $kolomhead++, "Tenor (Days)");
        xlsWriteLabel($tablehead, $kolomhead++, "Days to Maturity");
        xlsWriteLabel($tablehead, $kolomhead++, "Facility Type");
        xlsWriteLabel($tablehead, $kolomhead++, "Loan Status");
        xlsWriteLabel($tablehead, $kolomhead++, "Offtaker");
        xlsWriteLabel($tablehead, $kolomhead++, "Client Industry");

        foreach ($data['loan_data'] as $loan) {
            // Determine customer name and details based on customer type
            if($loan->customer_type == 'individual'){
                $customer_name = $loan->ind_firstname.' '.$loan->ind_lastname;
                $offtaker = '-';
                $industry = $loan->ind_profession ? $loan->ind_profession : '-';
            }elseif($loan->customer_type == 'institution'){
                $customer_name = $loan->corp_name ? $loan->corp_name : 'N/A';
                $offtaker = $loan->corp_category ? ucfirst(str_replace('_', ' ', $loan->corp_category)) : '-';
                $industry = $loan->corp_industry ? $loan->corp_industry : '-';
            }else{
                $customer_name = 'Unknown';
                $offtaker = '-';
                $industry = '-';
            }

            // Calculate outstanding balance
            $outstanding = $loan->total_scheduled - $loan->total_paid;

            // Amount Repaid (total paid amount)
            $amount_repaid = $loan->total_paid ? $loan->total_paid : 0;

            // Realized Interest
            $realized_interest = $loan->realized_interest ? $loan->realized_interest : 0;

            // Calculate tenor in days based on frequency
            $tenor_days = 0;
            $frequency = $loan->period_type ? $loan->period_type : $loan->loan_frequency;
            if($loan->loan_period && $frequency){
                switch($frequency){
                    case 'Monthly':
                        $tenor_days = $loan->loan_period * 30;
                        break;
                    case '2 Weeks':
                        $tenor_days = $loan->loan_period * 15;
                        break;
                    case 'Weekly':
                        $tenor_days = $loan->loan_period * 7;
                        break;
                    default:
                        $tenor_days = $loan->loan_period * 30;
                }
            }

            // Calculate days to maturity
            $days_to_maturity = '-';
            if($loan->last_payment_date){
                $today = new DateTime();
                $maturity_date = new DateTime($loan->last_payment_date);
                $interval = $today->diff($maturity_date);

                if($maturity_date < $today){
                    $days_to_maturity = 'Overdue (' . $interval->days . ' days)';
                }else{
                    $days_to_maturity = $interval->days . ' days';
                }
            }

            // Currency display
            $currency = $loan->currency_name ? $loan->currency_name : ($loan->currency_code ? $loan->currency_code : '-');

            // Facility type
            $facility_type = $loan->facility_type ? $loan->facility_type : '-';

            // Interest Amount
            $interest_amount = $loan->loan_interest_amount ? $loan->loan_interest_amount : 0;

            $kolombody = 0;

            xlsWriteNumber($tablebody, $kolombody++, $nourut);
            xlsWriteLabel($tablebody, $kolombody++, $customer_name);
            xlsWriteLabel($tablebody, $kolombody++, $loan->loan_number);
            xlsWriteNumber($tablebody, $kolombody++, $loan->loan_principal);
            xlsWriteNumber($tablebody, $kolombody++, $outstanding);
            xlsWriteNumber($tablebody, $kolombody++, $interest_amount);
            xlsWriteNumber($tablebody, $kolombody++, 0); // Rollover fees
            xlsWriteNumber($tablebody, $kolombody++, $realized_interest);
            xlsWriteNumber($tablebody, $kolombody++, $amount_repaid);
            xlsWriteLabel($tablebody, $kolombody++, $loan->loan_interest.'%');
            xlsWriteLabel($tablebody, $kolombody++, $currency);
            xlsWriteNumber($tablebody, $kolombody++, $tenor_days);
            xlsWriteLabel($tablebody, $kolombody++, $days_to_maturity);
            xlsWriteLabel($tablebody, $kolombody++, $facility_type);
            xlsWriteLabel($tablebody, $kolombody++, $loan->loan_status);
            xlsWriteLabel($tablebody, $kolombody++, $offtaker);
            xlsWriteLabel($tablebody, $kolombody++, $industry);

            $tablebody++;
            $nourut++;
        }

        xlsEOF();
        exit();
    }

    public function crb_report()
    {
        $status = trim((string) $this->input->get('crb_status'));
        $search = $this->input->get('search');
        $filters = array(
            'loan_status' => 'All', 'currency' => 'All', 'customer_type' => 'All',
            'loan_product' => 'All',
            'from_date' => (string) $this->input->get('from_date'),
            'to_date' => (string) $this->input->get('to_date')
        );
        $loans = $this->Loan_model->portfolio_listing($filters);
        if ($status !== '' && strcasecmp($status, 'All') !== 0) {
            $loans = array_values(array_filter($loans, function ($loan) use ($status) {
                return strcasecmp((string) ($loan->crb_search ?? ''), $status) === 0;
            }));
        }
        $data = array('loan_data' => $loans, 'crb_status' => $status ?: 'All', 'filters' => $filters);
        if ($search === 'excel') {
            $this->export_database_report_excel('CRB_Report', 'reports/crb_report', $data);
            return;
        }
        if ($search === 'pdf') {
            $data['export'] = true;
            $this->load->library('Pdf');
            $html = $this->load->view('reports/crb_report', $data, true);
            $this->pdf->createPDF($html, 'CRB Report - ' . date('Y-m-d'), true, 'A4', 'landscape');
            return;
        }
        $this->load->view('admin/header');
        $this->load->view('reports/crb_report', $data);
        $this->load->view('admin/footer');
    }

    private function excel_crb_report($data)
    {
        xlsHeaders('crb_report_' . date('Y-m-d') . '.xls');
        xlsBOF();
        $headers = array('No', 'Customer', 'Client ID', 'Loan Number', 'Loan Product', 'Principal', 'CRB Status', 'Loan Status', 'Loan Date');
        foreach ($headers as $column => $label) xlsWriteLabel(0, $column, $label);
        $row = 1;
        foreach ($data['loan_data'] as $loan) {
            if ($loan->customer_type === 'individual') {
                $name = trim(($loan->ind_firstname ?? '') . ' ' . ($loan->ind_lastname ?? ''));
                $client_id = $loan->ind_client_id ?? '';
            } else {
                $name = $loan->corp_name ?? 'Unknown';
                $client_id = $loan->corp_client_id ?? '';
            }
            $values = array($row, $name, $client_id, $loan->loan_number, $loan->facility_type ?? '', $loan->loan_principal, $loan->crb_search ?? 'Not Specified', $loan->loan_status, $loan->loan_date);
            foreach ($values as $column => $value) {
                if (in_array($column, array(0, 5), true)) xlsWriteNumber($row, $column, (float) $value);
                else xlsWriteLabel($row, $column, (string) $value);
            }
            $row++;
        }
        xlsEOF();
        exit;
    }
    /**
     * Collateral Report - Comprehensive report of all collaterals
     */
    public function collateral_report() {
        $menu_toggle['toggles'] = 23;

        // Get filter parameters
        $filters = array(
            'customer_type' => $this->input->get('customer_type'),
            'collateral_type' => $this->input->get('collateral_type'),
            'status' => $this->input->get('status'),
            'from_date' => $this->input->get('from'),
            'to_date' => $this->input->get('to')
        );

        $search = $this->input->get('search');

        // Get data
        $data['collaterals'] = $this->Collateral_model->get_all_for_report($filters);
        $data['summary'] = $this->Collateral_model->get_report_summary($filters);
        $data['collateral_types'] = $this->Collateral_model->get_collateral_types();
        $data['filters'] = $filters;

        // Get currency - default to ZMW (Zambian Kwacha)
        $settings = get_by_id('settings', 'settings_id', 1);
        $zmw_currency = $this->db->get_where('currencies', array('currency_code' => 'ZMW'))->row();
        if ($zmw_currency) {
            $data['currency'] = $zmw_currency;
        } else {
            $data['currency'] = get_by_id('currencies', 'currency_id', $settings->default_currency ?? 1);
        }

        if ($search == 'pdf') {
            $this->load->library('Pdf');
            $html = $this->load->view('reports/collateral_report_pdf', $data, true);
            $this->pdf->createPDF($html, "Collateral_Report_" . date('Y-m-d'), true, 'A4', 'landscape');
        } elseif ($search == 'excel') {
            $this->export_database_report_excel('Collateral_Report', 'reports/collateral_report', $data, 'collateral-table');
        } else {
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('reports/collateral_report', $data);
            $this->load->view('admin/footer');
        }
    }

    /**
     * User Roles and Permissions Report
     * Shows all roles, users assigned to each role, and their permissions
     */
    public function user_roles_report()
    {
        $menu_toggle['toggles'] = 7;

        // Get all roles
        $roles = $this->db->order_by('RoleName', 'ASC')->get('roles')->result();

        $report_data = array();

        foreach ($roles as $role) {
            $role_info = array(
                'role_id' => $role->id,
                'role_name' => $role->RoleName,
                'users' => array(),
                'permissions' => array()
            );

            // Get users in this role
            $this->db->select('employees.id, employees.Firstname, employees.Lastname, employees.EmailAddress, employees.PhoneNumber, user_access.AccessCode, user_access.status');
            $this->db->from('employees');
            $this->db->join('user_access', 'user_access.Employee = employees.id', 'left');
            $this->db->where('employees.Role', $role->id);
            $this->db->order_by('employees.Firstname', 'ASC');
            $users = $this->db->get()->result();

            foreach ($users as $user) {
                $role_info['users'][] = array(
                    'id' => $user->id,
                    'name' => $user->Firstname . ' ' . $user->Lastname,
                    'email' => $user->EmailAddress,
                    'phone' => $user->PhoneNumber,
                    'username' => $user->AccessCode,
                    'status' => $user->status
                );
            }

            // Get permissions for this role
            $this->db->select('menuitems.id, menuitems.label, menuitems.method, menuitems.mid');
            $this->db->from('access');
            $this->db->join('menuitems', 'menuitems.id = access.controllerid');
            $this->db->where('access.roleid', $role->id);
            $this->db->order_by('menuitems.label', 'ASC');
            $permissions = $this->db->get()->result();

            // Group permissions by parent menu
            $grouped_permissions = array();
            foreach ($permissions as $perm) {
                // Get parent menu name if exists
                $parent_name = 'General';
                if ($perm->mid > 0) {
                    $parent = $this->db->get_where('menuitems', array('id' => $perm->mid))->row();
                    if ($parent) {
                        $parent_name = $parent->label;
                    }
                }

                if (!isset($grouped_permissions[$parent_name])) {
                    $grouped_permissions[$parent_name] = array();
                }
                $grouped_permissions[$parent_name][] = array(
                    'id' => $perm->id,
                    'name' => $perm->label,
                    'method' => $perm->method
                );
            }

            $role_info['permissions'] = $grouped_permissions;
            $role_info['permission_count'] = count($permissions);
            $role_info['user_count'] = count($users);

            $report_data[] = $role_info;
        }

        // Get summary stats
        $data['total_roles'] = count($roles);
        $data['total_users'] = $this->db->count_all('employees');
        $data['total_permissions'] = $this->db->count_all('menuitems');
        $data['report_data'] = $report_data;

        $search = $this->input->get('search');
        if ($search === 'excel') {
            $this->export_database_report_excel('User_Roles_Report', 'reports/user_roles_report', $data);
        }

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('reports/user_roles_report', $data);
        $this->load->view('admin/footer');
    }

    /** Export a freshly queried result set. No report view or browser table is read. */
    private function export_database_report_excel($filename, $view, array $data, $table_id = null)
    {
        list($headers, $rows) = $this->database_excel_dataset($filename, $data);
        $this->stream_database_excel($filename, $headers, $rows);
    }

    private function database_excel_dataset($filename, array $data)
    {
        $records = $data['loan_data'] ?? array();
        $rows = array();
        $value = function ($record, $key, $default = '') {
            if (is_object($record) && isset($record->$key)) return $record->$key;
            if (is_array($record) && array_key_exists($key, $record)) return $record[$key];
            return $default;
        };
        $customer = function ($record) use ($value) {
            $name = trim($value($record, 'customer_name', ''));
            if ($name !== '') return $name;
            if ($value($record, 'customer_type') === 'institution') return $value($record, 'corp_name', 'Corporate customer');
            if ($value($record, 'customer_type') === 'group') return $value($record, 'group_name', 'Group customer');
            return trim($value($record, 'ind_firstname', $value($record, 'Firstname', '')) . ' ' . $value($record, 'ind_lastname', $value($record, 'Lastname', '')));
        };

        if ($filename === 'Period_Analysis_Report') {
            return array(
                array('Metric', 'Value'),
                array(
                    array('Total value of loans disbursed', (float) (($data['total_loan_principal']->total ?? 0))),
                    array('Number of loans disbursed', (int) ($data['total_loans'] ?? 0)),
                    array('Clients in book', (int) ($data['customers'] ?? 0)),
                    array('Employees', (int) ($data['employees'] ?? 0)),
                    array('Agents and brokers', 0),
                    array('People employed by agents and brokers', 0)
                )
            );
        }
        if ($filename === 'Financial_Analysis_Report') {
            $interest = (float) ($data['interests_income']->interest ?? 0); $admin = (float) ($data['admin_income']->amount ?? 0);
            $commission = (float) ($data['commissions'] ?? 0); $late = (float) ($data['late_fee']->amount ?? 0);
            $bad = (float) ($data['bad_debits']->principal ?? 0); $funding = (float) ($data['interest_paid']->interest_paid ?? 0);
            $expenses = (float) ($data['expenses']->amount ?? 0); $gross = $interest + $admin + $commission + $late; $net = $gross - $bad - $funding;
            return array(array('Metric', 'Amount'), array(
                array('Interest income from loans', $interest), array('Administration fee income', $admin), array('Commissions', $commission),
                array('Late payment income', $late), array('Gross lending income', $gross), array('Bad debts', $bad),
                array('Interest paid / cost of funding', $funding), array('Net microcredit income', $net),
                array('Operating expenses', $expenses), array('Net profit before tax', $net - $expenses)
            ));
        }
        if ($filename === 'Customers_Report') {
            foreach (($data['individual_customers_data'] ?? array()) as $r) $rows[] = array($value($r,'ClientId'),$value($r,'Title'),$value($r,'Firstname'),$value($r,'Middlename'),$value($r,'Lastname'),$value($r,'Gender'),$value($r,'DateOfBirth'),$value($r,'EmailAddress'),$value($r,'PhoneNumber'),$value($r,'CreatedOn'));
            return array(array('Client ID','Title','First Name','Middle Name','Last Name','Gender','Date of Birth','Email','Phone','Created On'),$rows);
        }
        if ($filename === 'Arrears_Report') {
            foreach ($records as $r) { $due=$value($r,'payment_schedule'); $rows[]=array($customer($r),$value($r,'loan_number'),$value($r,'calculation_type','Regular'),$due,(float)$value($r,'amount',0),(float)$value($r,'amount',0),$due ? max(0,(int)floor((strtotime(date('Y-m-d'))-strtotime($due))/86400)) : 0); }
            return array(array('Customer','Loan Number','Type','Due Date','Original Amount','Amount Due','Days Overdue'),$rows);
        }
        if (in_array($filename,array('To_Pay_Today_Report','To_Pay_Month_Report','To_Pay_Week_Report'),true)) {
            foreach ($records as $r) $rows[]=array($customer($r),$value($r,'loan_number'),$value($r,'payment_schedule'),(float)$value($r,'amount',0),$value($r,'payment_number'));
            return array(array('Customer','Loan Number','Due Date','Amount to Collect','Payment Number'),$rows);
        }
        if ($filename === 'Collection_Sheet_Report') {
            foreach ($records as $r) $rows[]=array($customer($r),$value($r,'loan_number'),$value($r,'payment_schedule'),$value($r,'payment_number'),(float)$value($r,'principal',0),(float)$value($r,'interest',0),(float)$value($r,'amount',0),(float)$value($r,'paid_amount',0),(float)$value($r,'amount',0)-(float)$value($r,'paid_amount',0),$value($r,'status'));
            return array(array('Customer','Loan Number','Due Date','Payment Number','Principal','Interest','Amount Due','Amount Paid','Balance','Status'),$rows);
        }
        if ($filename === 'Payments_Report') {
            foreach ($records as $r) $rows[]=array($value($r,'ref'),$value($r,'loan_number'),$value($r,'name'),$value($r,'payment_number'),(float)$value($r,'amount',0),$value($r,'date_stamp'),trim($value($r,'Firstname').' '.$value($r,'Lastname')));
            return array(array('Transaction Reference','Loan Number','Transaction Type','Payment Number','Amount','Payment Date','Officer'),$rows);
        }
        if ($filename === 'PAR_Report') {
            $records=$data['summary'] ?? array(); foreach ($records as $r) $rows[]=array($value($r,'loan_number'),$customer($r),trim($value($r,'eFirstname').' '.$value($r,'eLastname')),$value($r,'product_name'),(float)$value($r,'loan_principal',0),(float)$value($r,'total_amount_not_paid',0),(float)$value($r,'total_principal_not_paid',0),(float)$value($r,'total_interest_not_paid',0),$value($r,'max_date'));
            return array(array('Loan Number','Customer','Officer','Loan Product','Loan Amount','Total Arrears','Outstanding Principal','Outstanding Interest','Oldest Due Date'),$rows);
        }
        if ($filename === 'Portfolio_Listing') {
            $number = 1;
            foreach ($records as $r) {
                $principal = (float) $value($r, 'disbursed_amount', 0);
                if ($principal <= 0) $principal = (float) $value($r, 'loan_principal', 0);
                $interest = (float) $value($r, 'loan_interest_amount', 0);
                $scheduledInterest = (float) $value($r, 'total_scheduled_interest', 0);
                if ($interest <= 0) $interest = $scheduledInterest;
                $rollover = max(0, $scheduledInterest - $interest);
                $repaid = (float) $value($r, 'total_paid', 0);
                $realized = max(0, $repaid - $principal);
                $outstanding = max(0, $principal + $interest + $rollover - $repaid);
                $disbursed = $value($r, 'disbursed_date');
                if (!$disbursed || substr((string) $disbursed, 0, 10) === '0000-00-00') $disbursed = $value($r, 'loan_date');
                $disbursed = $disbursed ? substr((string) $disbursed, 0, 10) : '';
                $due = $value($r, 'maturity_date');
                $due = $due ? substr((string) $due, 0, 10) : '';
                $tenor = ($disbursed && $due) ? max(0, (int) floor((strtotime($due) - strtotime($disbursed)) / 86400)) : 0;
                $daysToMaturity = ($outstanding > 0 && $due) ? (int) floor((strtotime(date('Y-m-d')) - strtotime($due)) / 86400) : '';
                $clientSector = $value($r, 'corp_industry', $value($r, 'ind_profession', $value($r, 'group_category', '-')));
                $lastPayment = $value($r, 'last_payment_date');
                $lastPayment = ($lastPayment && substr((string) $lastPayment, 0, 10) !== '0000-00-00') ? substr((string) $lastPayment, 0, 10) : '';
                $rows[] = array($number++, $customer($r), $principal, (float) $value($r, 'loan_interest', 0) / 100, $interest, $rollover, $realized, $repaid,
                    $lastPayment, $outstanding, $disbursed, $tenor,
                    $daysToMaturity, $due, $value($r, 'loan_officer', '-'), $value($r, 'facility_type', $value($r, 'product_name')), $value($r, 'off_taker_name', '-'),
                    $clientSector ?: '-', $value($r, 'off_taker_sector', '-') ?: '-');
            }
            return array(array('No.','Client Name','Amount Disbursed','Interest Rate','Interest Amount','Rollover Fees','Realized Interest','Amount Repaid','Date of Last Payment','Amount Outstanding','Date Disbursed','Tenor (Days)','Days to Maturity','Due Date','Loan Officer','Facility Type','Offtaker','Client Sector','Off Taker Sector'),$rows);
        }
        if ($filename === 'Obligor_Listing') {
            foreach ($records as $r) { $out=(float)$value($r,'total_scheduled',0)-(float)$value($r,'total_paid',0); $rows[]=array($customer($r),$value($r,'loan_number'),(float)$value($r,'loan_principal',0),$out,(float)$value($r,'loan_interest_amount',0),(float)$value($r,'realized_interest',0),(float)$value($r,'total_paid',0),$value($r,'currency_code',$value($r,'currency_name')),$value($r,'loan_period').' '.$value($r,'period_type'),$value($r,'last_payment_date'),$value($r,'facility_type',$value($r,'product_name')),$value($r,'loan_status'),$value($r,'corp_category','-'),$value($r,'corp_industry',$value($r,'ind_profession','-'))); }
            return array(array('Client Name','Loan Number','Amount Disbursed','Amount Outstanding','Interest Amount','Realized Interest','Amount Repaid','Currency','Tenor','Maturity Date','Facility Type','Loan Status','Offtaker','Client Industry'),$rows);
        }
        if ($filename === 'CRB_Report') {
            foreach ($records as $r) $rows[]=array($customer($r),$value($r,'ind_client_id',$value($r,'corp_client_id')),$value($r,'loan_number'),$value($r,'facility_type',$value($r,'product_name')),(float)$value($r,'loan_principal',0),$value($r,'crb_search','Not Specified'),$value($r,'loan_status'),$value($r,'loan_date'));
            return array(array('Customer','Client ID','Loan Number','Loan Product','Principal','CRB Status','Loan Status','Loan Date'),$rows);
        }
        if ($filename === 'Collateral_Report') {
            $records=$data['collaterals'] ?? array(); foreach ($records as $r) $rows[]=array($value($r,'collateral_name'),$value($r,'collateral_type'),$value($r,'collateral_serial'),$value($r,'customer_name'),$value($r,'customer_type'),(float)$value($r,'market_value',0),(float)$value($r,'force_sale_value',0),(float)$value($r,'total_utilized',0),(float)$value($r,'available_balance',0),(float)$value($r,'utilization_percent',0),$value($r,'active_loans_count'),$value($r,'collateral_status'),$value($r,'added_at'));
            return array(array('Collateral Name','Type','Serial Number','Customer','Customer Type','Market Value','Force Sale Value','Utilized','Available','Utilization %','Active Loans','Status','Date Added'),$rows);
        }
        if ($filename === 'Client_Summary_Report') {
            foreach ($records as $r) $rows[]=array($value($r,'payment_schedule'),(float)$value($r,'amount',0),(float)$value($r,'loan_balance',0),(float)$value($r,'paid_amount',0),(float)$value($r,'amount',0)-(float)$value($r,'paid_amount',0));
            return array(array('Date','Arrears Due','Loan Outstanding','Amount Paid','Variance'),$rows);
        }
        if ($filename === 'User_Roles_Report') {
            foreach (($data['report_data'] ?? array()) as $role) foreach (($role['users'] ?? array()) as $user) $rows[]=array($role['role_name'] ?? '',$user['name'] ?? '',$user['email'] ?? '',$user['phone'] ?? '',$user['username'] ?? '',$user['status'] ?? '',count($role['permissions'] ?? array()));
            return array(array('Role','User','Email','Phone','Username','Status','Permission Groups'),$rows);
        }

        $preferred = array('loan_data', 'collaterals', 'individual_customers_data', 'client_data', 'summary', 'report_data', 'loanreports');
        $records = null;
        foreach ($preferred as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                $records = $data[$key];
                break;
            }
        }

        if ($records === null) {
            $record = array();
            foreach ($data as $key => $value) {
                if (is_scalar($value) || $value === null) {
                    $record[$key] = $value;
                } elseif (is_object($value)) {
                    $values = get_object_vars($value);
                    $record[$key] = count($values) === 1 ? reset($values) : json_encode($values);
                }
            }
            $records = $record ? array($record) : array();
        }

        $normalized = array();
        $headers = array();
        foreach ($records as $record) {
            if (is_object($record)) $record = get_object_vars($record);
            if (!is_array($record)) $record = array('value' => $record);
            $flat = array();
            foreach ($record as $key => $value) {
                if (is_object($value)) $value = get_object_vars($value);
                if (is_array($value)) $value = json_encode($value);
                $flat[(string)$key] = $value;
                if (!in_array((string)$key, $headers, true)) $headers[] = (string)$key;
            }
            $normalized[] = $flat;
        }

        $labels = array_map(function ($header) {
            return ucwords(str_replace(array('_', '-'), ' ', $header));
        }, $headers);
        $rows = array();
        foreach ($normalized as $record) {
            $row = array();
            foreach ($headers as $header) $row[] = array_key_exists($header, $record) ? $record[$header] : '';
            $rows[] = $row;
        }
        return array($labels, $rows);
    }

    /** SpreadsheetML version of the approved ZMW Portfolio workbook layout. */
    private function stream_portfolio_excel(array $headers, array $rows)
    {
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Portfolio_Report_' . date('Y-m-d') . '.xls"');
        header('Cache-Control: max-age=0');
        $esc = function ($v) {
            $v = strip_tags(html_entity_decode((string) $v, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            return htmlspecialchars(trim($v), ENT_QUOTES | ENT_XML1, 'UTF-8');
        };
        $money=array(2,4,5,6,7,9); $dates=array(8,10,13); $totals=array_fill(0,19,0); $rate=0; $rateCount=0;
        echo '<?xml version="1.0" encoding="UTF-8"?><?mso-application progid="Excel.Sheet"?>';
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"><Styles>';
        echo '<Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Center"/><Font ss:FontName="Calibri" ss:Size="10"/></Style>';
        echo '<Style ss:ID="H"><Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#203864" ss:Pattern="Solid"/></Style>';
        echo '<Style ss:ID="T"><Alignment ss:Vertical="Center" ss:WrapText="1"/></Style><Style ss:ID="I"><NumberFormat ss:Format="0"/><Alignment ss:Horizontal="Center"/></Style>';
        echo '<Style ss:ID="M"><NumberFormat ss:Format="#,##0.00;[Red]-#,##0.00"/></Style><Style ss:ID="P"><NumberFormat ss:Format="0.00%"/></Style><Style ss:ID="D"><NumberFormat ss:Format="dd-mmm-yyyy"/></Style>';
        echo '<Style ss:ID="TL"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#4472C4" ss:Pattern="Solid"/></Style><Style ss:ID="TM"><Font ss:Bold="1"/><Interior ss:Color="#D9EAF7" ss:Pattern="Solid"/><NumberFormat ss:Format="#,##0.00"/></Style><Style ss:ID="TP"><Font ss:Bold="1"/><Interior ss:Color="#D9EAF7" ss:Pattern="Solid"/><NumberFormat ss:Format="0.00%"/></Style>';
        echo '</Styles><Worksheet ss:Name="ZMW Portfolio"><Table>'; foreach(array(42,190,90,75,90,85,90,90,95,95,90,72,82,90,125,110,125,120,120) as $w) echo '<Column ss:Width="'.$w.'"/>';
        echo '<Row ss:Height="36">'; foreach($headers as $h) echo '<Cell ss:StyleID="H"><Data ss:Type="String">'.$esc($h).'</Data></Cell>'; echo '</Row>';
        foreach($rows as $row){ echo '<Row>'; foreach($row as $i=>$v){
            if(in_array($i,$money,true)){ $n=(float)$v; $totals[$i]+=$n; echo '<Cell ss:StyleID="M"><Data ss:Type="Number">'.$n.'</Data></Cell>'; }
            elseif($i===3){ $n=(float)$v; $rate+=$n; $rateCount++; echo '<Cell ss:StyleID="P"><Data ss:Type="Number">'.$n.'</Data></Cell>'; }
            elseif(in_array($i,$dates,true)&&$v!=='') echo '<Cell ss:StyleID="D"><Data ss:Type="DateTime">'.$esc(substr((string)$v,0,10)).'T00:00:00.000</Data></Cell>';
            elseif(in_array($i,array(0,11,12),true)&&$v!=='') echo '<Cell ss:StyleID="I"><Data ss:Type="Number">'.(int)$v.'</Data></Cell>';
            else echo '<Cell ss:StyleID="T"><Data ss:Type="String">'.$esc($v).'</Data></Cell>';
        } echo '</Row>'; }
        echo '<Row><Cell ss:MergeAcross="1" ss:StyleID="TL"><Data ss:Type="String">TOTAL / AVERAGE</Data></Cell>';
        for($i=2;$i<19;$i++){
            if(in_array($i,$money,true)) echo '<Cell ss:StyleID="TM"><Data ss:Type="Number">'.$totals[$i].'</Data></Cell>';
            elseif($i===3) echo '<Cell ss:StyleID="TP"><Data ss:Type="Number">'.($rateCount?$rate/$rateCount:0).'</Data></Cell>';
            else echo '<Cell ss:StyleID="TL"><Data ss:Type="String"></Data></Cell>';
        }
        echo '</Row></Table><WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel"><FreezePanes/><FrozenNoSplit/><SplitHorizontal>1</SplitHorizontal><TopRowBottomPane>1</TopRowBottomPane></WorksheetOptions></Worksheet></Workbook>';
        exit;
    }

    private function stream_database_excel($filename, array $headers, array $rows)
    {
        if ($filename === 'Portfolio_Listing') $this->stream_portfolio_excel($headers, $rows);
        $safe = trim(preg_replace('/[^A-Za-z0-9_-]+/', '_', $filename), '_') . '_' . date('Y-m-d') . '.xls';
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $safe . '"');
        header('Cache-Control: max-age=0');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?mso-application progid="Excel.Sheet"?>';
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"><Styles><Style ss:ID="Header"><Font ss:Bold="1"/><Interior ss:Color="#D9EAF7" ss:Pattern="Solid"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/></Borders></Style><Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Top" ss:WrapText="1"/></Style></Styles><Worksheet ss:Name="Report"><Table>';
        $write = function (array $values, $header = false) {
            echo '<Row>';
            foreach ($values as $value) {
                if ($value === null) $value = '';
                if (is_bool($value)) $value = $value ? 'Yes' : 'No';
                $is_number = is_int($value) || is_float($value);
                $type = $is_number ? 'Number' : 'String';
                $clean = strip_tags(html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $clean = preg_replace('/\x{00C2}\x{00A0}|\x{00A0}/u', ' ', $clean);
                $clean = preg_replace('/[^\x09\x0A\x0D\x20-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $clean);
                $clean = trim(preg_replace('/[\t\r\n ]+/u', ' ', $clean));
                $clean = htmlspecialchars($clean, ENT_QUOTES | ENT_XML1, 'UTF-8');
                echo '<Cell' . ($header ? ' ss:StyleID="Header"' : '') . '><Data ss:Type="' . $type . '">' . $clean . '</Data></Cell>';
            }
            echo '</Row>';
        };
        if ($headers) $write($headers, true);
        foreach ($rows as $row) $write($row);
        echo '</Table><WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel"><FreezePanes/><FrozenNoSplit/><SplitHorizontal>1</SplitHorizontal><TopRowBottomPane>1</TopRowBottomPane></WorksheetOptions></Worksheet></Workbook>';
        exit;    }
}
