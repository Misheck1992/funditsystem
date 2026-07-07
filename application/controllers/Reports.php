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
            // Excel export can be added here
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
            // Excel export can be added here
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
            $data['payments_data'] = $data['loan_data'];
            $this->excel_payments($data);
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
            $this->excel_obligor_listing($data);
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
            $this->excel_portfolio_listing($data);
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

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('reports/user_roles_report', $data);
        $this->load->view('admin/footer');
    }

}
