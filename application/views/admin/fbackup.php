<?php

$settings = get_by_id('settings','settings_id','1');

?>
<!-- Footer START -->
<footer class="footer">
    <div class="footer-content">
        <p class="m-b-0">Copyright © 2021 . All rights reserved. by Infocus Tech (0994099461)</p>
        <span>
                            <a href="#" class="text-gray m-r-15">Term &amp; Conditions</a>
                            <a href="#" class="text-gray">Privacy &amp; Policy</a>
                        </span>
    </div>
</footer>
<!-- Footer END -->

</div>
<!-- Page Container END -->

</div>
</div>

<div aria-hidden="true" class="onboarding-modal modal fade" id="recommendation_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title"> Recommendation comment </h4>

                <form action="<?php echo base_url('Loan/recommend_action')?>" method="post" class="form-row" enctype="multipart/form-data">
                    <input type="text" name="loan_id" id="loan_ids" hidden>
                    <div class="form-group col-lg-12">
                        <label for="comment-1">Comment</label>

                        <textarea name="comment"  cols="30" rows="10" id="comment-1" class="form-control" placeholder="Your comment"></textarea>
                    </div>



                    <button type="submit" class="btn btn-primary">Submit recommendation</button>

                </form>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="score_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Loan officer Recommendation checklist </h4>
                <h5 class="onboarding-title">Loan Requirements fulfilment area</h5>
                <div id="score_data">

                </div>
                <p id="btns"></p>
            </div>
        </div>
    </div>
</div><div aria-hidden="true" class="onboarding-modal modal fade" id="score_modall" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Loan officer Recommendation checklist </h4>
                <h5 class="onboarding-title">Loan Requirements fulfilment area</h5>
                <div id="score_dataa">

                </div>

            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="kyc_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Add KYC to this customer</h4>
                <form action="<?php echo base_url('Proofofidentity/create_action')?>" method="post" class="form-row" enctype="multipart/form-data">
                    <div class="form-group col-6">
                        <input type="text" hidden name="ClientId" id="Client">
                        <label for="enum">IDType </label>
                        <select class="form-control" name="IDType" id="IDType" required >
                            <option value="">--select--</option>
                            <option value="NATIONAL_IDENTITY_CARD">NATIONAL IDENTITY CARD</option>
                            <option value="DRIVING_LISENCE">DRIVING LICENCE</option>
                            <option value="PASSPORT">PASSPORT</option>
                            <option value="WORK_PERMIT">WORK PERMIT</option>
                            <option value="VOTER_REGISTRATION">VOTER REGISTRATION</option>
                            <option value="PUBLIC_STATE_OFFICIAL_LETTER">PUBLIC STATE OFFICIAL LETTER</option>

                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label for="varchar">IDNumber </label>
                        <input type="text" class="form-control" name="IDNumber" id="IDNumber" placeholder="IDNumber" required  />
                    </div>
                    <div class="form-group col-6">
                        <label for="date">IssueDate </label>
                        <input type="date" class="form-control" name="IssueDate" id="IssueDate" placeholder="IssueDate" required />
                    </div>
                    <div class="form-group col-6">
                        <label for="date">ExpiryDate * </label>
                        <input type="date" class="form-control" name="ExpiryDate" id="ExpiryDate" placeholder="ExpiryDate"  required />
                    </div>
                    <div class="form-group col-6">
                        <label for="id_front" class="custom-file-upload"> Upload front photo of ID </label>
                        <input type="file"  onchange="uploadcommon('id_front')"   id="id_front"  />
                        <input type="text" id="id_front1"  name="id_front" hidden required>
                        <div id="id_front2">
                            <img src="<?php echo base_url('uploads/holder.PNG')?>" alt="" height="100" width="100">
                        </div>
                    </div>
                    <div class="form-group col-6">
                        <label for="Id_back" class="custom-file-upload"> Upload Back photo of ID * </label>
                        <input type="file" class="upload-btn-wrapper"  onchange="uploadcommon('Id_back')"  id="Id_back" placeholder="Id Back"  />
                        <input type="text" id="Id_back1" name="Id_back" hidden required>
                        <div id="Id_back2">
                            <img src="<?php echo base_url('uploads/holder.PNG')?>" alt="" height="100" width="100">
                        </div>
                    </div>

                    <div class="form-group col-6">
                        <label for="photograph"  class="custom-file-upload">Upload Photograph </label>
                        <input type="file"  onchange="uploadcommon('photograph')"    id="photograph" placeholder="Photograph"  />
                        <input type="text" id="photograph1" name="photograph" hidden required>
                        <div id="photograph2">
                            <img src="<?php echo base_url('uploads/holder.PNG')?>" alt="" height="100" width="100">
                        </div>
                    </div>
                    <div class="form-group col-6">
                        <label for="signature" class="custom-file-upload"> Upload Signature </label>
                        <input type="file" onchange="uploadcommon('signature')"    id="signature" placeholder="Signature" />
                        <input type="text" id="signature1" name="signature" hidden required>
                        <div id="signature2">
                            <img src="<?php echo base_url('uploads/holder.PNG')?>" alt="" height="100" width="100">
                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary">Save Changes</button>

                </form>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="minutes_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Comment</h4>



                <div class="form-group col-6">
                    <textarea name="comment" id="" cols="30" rows="10" placeholder="Write your comments"></textarea>
                </div>


                <button type="button" onclick="approve_all_loans()" class="btn btn-primary">Save Changes</button>


            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="pay_charge_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Pay loan processing charge</h4>
                <form action="<?php echo base_url('Transactions/create_action')?>" method="post" class="form-row" enctype="multipart/form-data">
                    <input type="text" name="loan_id" id="loan_d" hidden>
                    <div class="form-group col-6">
                        <label for="varchar">Loan number </label>
                        <input type="text" class="form-control" name="loan" id="loan_idd" placeholder="Loan number" disabled required  />
                    </div>
                    <div class="form-group col-6">
                        <label for="date">Total amount MK</label>
                        <input type="text" class="form-control" name="amount" id="charge_amount" readonly required />
                    </div>


                    <button type="submit" class="btn btn-primary">Save Changes</button>

                </form>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="modal-approval" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Approval process</h4>
                <p style="color: red;">Are you sure you want to do this process <i id="textb"></i></p>
                <form action="<?php echo base_url('Groups/approval')?>" method="post" class="form-row" enctype="multipart/form-data">
                    <input type="text" name="group_id" id="group_id" hidden>
                    <input type="text" name="action" id="actionb" hidden>

                    <div class="form-group col-12">
                        <textarea name="comment" id="" cols="30" rows="10" placeholder="Write comment"></textarea>
                    </div>


                    <button type="submit" class="btn btn-primary">Save Changes</button>

                </form>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="modal-approval-group" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Approval process</h4>
                <p style="color: red;">Are you sure you want to do this process <i id="textc"></i></p>
                <form action="<?php echo base_url('Group_assigned_amount/approval')?>" method="post" class="form-row" enctype="multipart/form-data">
                    <input type="text" name="gid" id="gid" hidden>
                    <input type="text" name="action" id="actionc" hidden>

                    <div class="form-group col-12">
                        <textarea name="comment" id="" cols="30" rows="10" placeholder="Write comment"></textarea>
                    </div>


                    <button type="submit" class="btn btn-primary">Save Changes</button>

                </form>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="image_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Image preview</h4>
                <div id="image_data_preview" style="background-color: red;">

                </div>

            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="loan_files_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Loan files</h4>
                <div>
                    <table class="table">
                        <thead>
                        <th>File name</th>
                        <th>Action</th>
                        </thead>
                        <tbody id="loan_files_data">

                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>
</div>
<?php

$get_c = get_all('individual_customers');
?>
<div aria-hidden="true" class="onboarding-modal modal fade" id="add_group_member_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Assign customer to group</h4>
                <form action="<?php echo base_url('Customer_groups/create_action')?>" method="post" class="form-row" >
                    <input type="text" name="group_id" id="group_idd"  hidden required>
                    <div class="form-group col-12">
                        <label for="varchar">Search customer</label>
                        <select name="customer" id="" class="form-control" required>
                            <option value="">--select customer--</option>
                            <?php

                            foreach ($get_c as $item){
                                ?>
                                <option value="<?php echo $item->id ?>"><?php echo $item->Firstname.' '.$item->Lastname ?></option>
                                <?php

                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-12">

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


<!-- Core Vendors JS -->
<script src="<?php echo base_url('admin_assets')?>/js/vendors.min.js"></script>

<!-- page js -->
<script src="<?php echo base_url('admin_assets')?>/vendors/chartjs/Chart.min.js"></script>
<script src="<?php echo base_url('admin_assets')?>/js/pages/dashboard-default.js"></script>
<script src="<?php echo base_url('admin_assets')?>/vendors/quill/quill.min.js"></script>
<!--data tables fuck-->
<script src="<?php echo base_url('admin_assets')?>/vendors/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url('admin_assets')?>/vendors/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url('admin_assets')?>/js/pages/datatables.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>lib/sweetalerts/sweetalert.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>admin_assets/js/security.js"></script>

<!-- Core JS -->
<script src="<?php echo base_url('admin_assets')?>/js/app.min.js"></script>
<script src="<?php echo base_url('admin_assets/')?>js/toastr.min.js"></script>
<script src="<?php echo base_url('admin_assets/')?>ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url('jquery-ui/')?>jquery-ui.js"></script>
<script src="<?php echo base_url('lib/')?>select2/dist/js/select2.full.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()  ?>gisttech/js/xlsx.core.js"></script>
<script type="text/javascript" src="<?php echo base_url()  ?>gisttech/js/Blob.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()  ?>gisttech/js/FileSaver.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()  ?>gisttech/js/tableexport.min.js"></script>
<script>
    let logo = "<?php echo $settings->logo?>";
    var DefaultTable = document.getElementById('resulta');
    var loan = document.getElementById('loand');
    new TableExport(DefaultTable,{
        headers: true,                              // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
        footers: true,                              // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
        formats: ['xlsx', 'csv', 'txt'],            // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
        filename: 'Arrears Report',                             // (id, String), filename for the downloaded file, (default: 'id')
        bootstrap: false,                           // (Boolean), style buttons using bootstrap, (default: false)
        position: 'bottom',                         // (top, bottom), position of the caption element relative to table, (default: 'bottom')
        ignoreRows: null,                           // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
        ignoreCols: null,                           // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
        ignoreCSS: '.tableexport-ignore',           // (selector, selector[]), selector(s) to exclude cells from the exported file(s) (default: '.tableexport-ignore')
        emptyCSS: '.tableexport-empty',             // (selector, selector[]), selector(s) to replace cells with an empty string in the exported file(s) (default: '.tableexport-empty')
        trimWhitespace: true,                       // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: true)
        RTL: false,                                 // (Boolean), set direction of the worksheet to right-to-left (default: false)
        sheetname: 'Arrears Report'                             // (id, String), sheet name for the exported spreadsheet, (default: 'id')
    });


    new TableExport(loan, {
        headers: true,                              // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
        footers: true,                              // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
        formats: ['xlsx', 'csv', 'txt'],            // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
        filename: 'Arrears Report',                             // (id, String), filename for the downloaded file, (default: 'id')
        bootstrap: false,                           // (Boolean), style buttons using bootstrap, (default: false)
        position: 'bottom',                         // (top, bottom), position of the caption element relative to table, (default: 'bottom')
        ignoreRows: null,                           // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
        ignoreCols: null,                           // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
        ignoreCSS: '.tableexport-ignore',           // (selector, selector[]), selector(s) to exclude cells from the exported file(s) (default: '.tableexport-ignore')
        emptyCSS: '.tableexport-empty',             // (selector, selector[]), selector(s) to replace cells with an empty string in the exported file(s) (default: '.tableexport-empty')
        trimWhitespace: true,                       // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: true)
        RTL: false,                                 // (Boolean), set direction of the worksheet to right-to-left (default: false)
        sheetname: 'Arrears Report'                             // (id, String), sheet name for the exported spreadsheet, (default: 'id')
    });
    // **** jQuery **************************
    //    $(DefaultTable).tableExport({
    //        headers: true,
    //        footers: true,
    //        formats: ['xlsx', 'csv', 'txt'],
    //        filename: 'id',
    //        bootstrap: true,
    //        position: 'bottom',
    //        ignoreRows: null,
    //        ignoreCols: null,
    //        ignoreCSS: '.tableexport-ignore',
    //        emptyCSS: '.tableexport-empty',
    //        trimWhitespace: false,
    //        RTL: false,
    //        sheetname: 'id'
    //    });
    // **************************************

</script>

<script type="text/javascript">

    function configure_teller(id){
        $("#tellering_account").val(id)
        $("#textt").html(id)
        $("#tellering").modal('show')
    }
    $('loan').select2({
        minimumInputLength: 3 // only start searching when the user has input 3 or more characters
    });

    $(document).ready(function() {
        $('.select2').select2();
        $(".sselect").select2();
        $('#d2,#d3').DataTable( {
            responsive: true
        } );
        $("#vtrans").click(function () {
            show_my_trans();
            $("#panelp").show();
            $("#vtrans").hide();
            $("#htrans").show();
        })
        $("#htrans").click(function () {
            $("#panelp").hide();
            $("#vtrans").show();
            $("#htrans").hide();
        })
    });
    $(function() {
        $( "#datepicker" ).datepicker({ minDate: 0});
    });
    $(function() {
        $(".dpicker").datepicker();
    });

    <?php if ($this->session->flashdata('success')) {?>
    toastr["success"]("<?php echo $this->session->flashdata('success'); ?>");
    <?php } else if ($this->session->flashdata('error')) {?>
    toastr["error"]("<?php echo $this->session->flashdata('error'); ?>");
    <?php } else if ($this->session->flashdata('warning')) {?>
    toastr["warning"]("<?php echo $this->session->flashdata('warning'); ?>");
    <?php } else if ($this->session->flashdata('info')) {?>
    toastr["info"]("<?php echo $this->session->flashdata('info'); ?>");
    <?php }?>
    toastr.options = {
        "closeButton": false,
        "debug": true,
        "newestOnTop": false,
        "progressBar": true,
        "rtl": false,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": 300,
        "hideDuration": 1000,
        "timeOut": 5000,
        "extendedTimeOut": 1000,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

</script>

<script>
    function pay_charge(loan_id,loan_number){
        $.ajax({
            url:"<?php echo base_url()?>Loan/get_charges/"+loan_id,
            method:"GET",

            beforeSend:()=>{

            },success:function(res){

                $("#charge_amount").val(res);
                $("#loan_idd").val(loan_number);
                $("#loan_d").val(loan_id);
            },error:()=>{
                errorToast('Error','Failed to interact with the server')
            }
        });
        $("#pay_charge_modal").modal('show');
    }
    function get_ta(){
        let district = $("#City").val();
        $.ajax({
            url:"<?php echo base_url()?>Individual_customers/get_ta/"+district,
            method:"GET",

            beforeSend:()=>{

            },success:function(res){

                $("#Province").html(res);

            },error:()=>{
                errorToast('Error','Failed to interact with the server')
            }
        });

    }
    function add_expense(){

        $("#add_expense_modal").modal('show');
    }
    function approve_group(id,action,text){
        $("#group_id").val(id);
        $("#actionb").val(action);
        $("#textb").html(text);
        $("#modal-approval").modal('show');
    }function approve_group_amount(id,action,text){
        $("#gid").val(id);
        $("#actionc").val(action);
        $("#textc").html(text);
        $("#modal-approval-group").modal('show');
    }
    function pay_due(loan_id,schedule,loan_amount,paid_amount){
        console.log(loan_id)
        console.log(schedule)
        console.log(loan_amount)
        console.log(paid_amount)
        $.ajax({
            url:"<?php echo base_url()?>Loan/get_late_charg/"+loan_id,
            method:"GET",

            beforeSend:()=>{

            },success:function(res){

                $("#late_charge_amount").val(res);
                $("#spc").html(res);
                $("#pn").val(schedule);
                $("#spn").html(schedule);
                $("#lm").val((loan_amount)-(paid_amount));
                $("#slm").html(loan_amount);
            },error:()=>{
                errorToast('Error','Failed to interact with the server')
            }
        });
        $("#late_payment_modal").modal('show');
    }
    function pay_borrowed(id){

        $("#pay_borrowed_modal").modal('show');
    }
    function finish_payment(id){

        $("#finish_payment_modal").modal('show');
    }
    $('[data-toggle="tooltip"]').tooltip()
    var baseURL = "<?php echo base_url()?>";
    // tooltip on payment modals
    var checkboxes = $(".check-cls"),
        submitButt = $(".submit-btn");

    checkboxes.click(function() {
        submitButt.attr("disabled", !checkboxes.is(":checked"));
    });

    $(document).ready(function (){
        $("#charge_type").change(function () {
            if($("#charge_type").val()==="Variable"){
                $("#fixed_amount"). prop('readonly', true);
                $("#variable_value"). prop('readonly', false);
            }else  if ($("#charge_type").val()==="Fixed"){
                $("#fixed_amount"). prop('readonly', false);
                $("#variable_value"). prop('readonly', true);
            }

        });
        $("#search_account_form").submit(function (e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            // var url = form.attr('action');

            $.ajax({
                type: "POST",
                url: baseURL + 'Account/search',
                dataType: 'json',
                data: form.serialize(), // serializes the form's elements.
                beforeSend: () => {
                    $("#sbbtn").html("<i class='fa fa-spinner fa-spin'></i>Searching Details");
                },
                success: function (response) {
                    $("#sbbtn").html('Submit Search');
                    // $("#cover-spin").hide();
                    var event_data ="";
                    let lname = '';
                    $.each(response.data, function (index, value) {
                        if(value.Lastname !=undefined){
                            lname = value.name +" " +value.Lastname;
                        }else {
                            lname = value.name;
                        }

                        event_data +=`
            <tr>
					<td><u><a href='#' class='selects' onclick='populate("${value.account_number}","${value.account_type_name}","${lname}","${value.balance}","${value.Currency}","${value.date_added }","${value.account_status}","${value.photograph}","${value.signature}","${value.id_front}","${value.Id_back}")'>select</a></u></td>

                    <td>${value.account_number}</td>

                    <td>${value.account_type_name}</td>
                    <td>${lname}</td>

                             </tr>
            `;

                    });
                    $("#search-r").html(event_data);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // $("#cover-spin").hide();
                    $("#sbbtn").html('Submit Search');
                    alert('connection error')


                }
            });


        });

        $("#search_input").click(function(){

            $("#search_modal").modal('show');
            // get_account_types();
        })
        $("#search_acount").click(function(){

            $("#search_modal").modal('show');
            // get_account_types();
        })

        $("#transaction_deposit_form").submit(function (e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.
            let account_name = $('#account_name').val();
            var form = $(this);
            // var url = form.attr('action');
            if($("#tt").val()===''||$("#tt").val()===null){
                swal("Error","amount cannot be empty","error");
                return;
            }	if($("#dacn").val()===''||$("#dacn").val()===null){
                swal("Error","Sorry, Select account first","error");
                return;
            }
            if (!$("input[name='transaction_mode']:checked").val()) {
                swal("Error","Sorry Select Deposit or withdraw first","error");
                return false;
            }

            swal({
                title: "Are you sure you want to  make "+$("input[name='transaction_mode']:checked").val()+"?",
                text: "You need to be careful with this",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, Proceed",
                closeOnConfirm: false
            }, function (isConfirm) {
                if (!isConfirm) return;
                $.ajax({
                    type: "POST",
                    url: baseURL + 'Account/cash_transaction',
                    dataType: 'json',
                    data: form.serialize(), // serializes the form's elements.
                    beforeSend: () => {
                        $("#svbutton").html("<i class='fa fa-spinner fa-spin'></i>Transacting please wait");
                    },
                    success: function (response) {
                        $("#svbutton").html("Save changes");
                        if(response.status=='success'){
                            $("#tt").val('');
                            $("#chartof").val('');
                            $("#dacn").val('');
                            $("#account_name").val('');
                            $("#account_balance").val('');
                            $("#account_date").val('');
                            $("#account_status").val('');
                            // $('#transaction_deposit_form input[type="radio":checked]').each(function(){
                            // 	$(this).prop('checked', false);
                            // });
                            get_teller_balance();
                            show_my_trans()
                            const winHtml = `
<!DOCTYPE html>
<html lang="en" >

<head>

	<meta charset="UTF-8">

		<meta name="gsgs" content="hsh">


	<style>
		* {
			border: 0;
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}
		body, input {
			color: #242424;
			font-size: calc(20px + (30 - 20) * (100vw - 480px) / (1136 - 480));
			line-height: 1;
		}
		body {
			background-color: #555;
			font-family: Helvetica, sans-serif;
			overflow-x: hidden;
		}
		button, input[type=checkbox] {
			position: fixed;
		}
		button {
			background: #5785f6;
			border-radius: 0.25em;
			color: #fff;
			cursor: pointer;
			font-size: inherit;
			font-weight: 700;
			margin: auto;
			padding: 0.375em 0.75em;
			top: 0.75em;
			left: 0.75em;
			transition: filter 0.1s linear, -webkit-filter 0.1s linear;
			-webkit-appearance: none;
			z-index: 9;
		}
		button:hover {
			filter: brightness(1.1);
			-webkit-filter: brightness(1.1);
		}
		button:active {
			filter: brightness(0.9);
			-webkit-filter: brightness(0.9);
		}
		form {
			margin: 3em auto 0 auto;
			position: relative;
			perspective: 1000px;
			height: 13.75em;
			width: 30em;
		}
		h1 {
			font-size: 0.6em;
			text-transform: uppercase;
		}
		form input {
			background: transparent;
			font-family: "Shadows Into Light", sans-serif;
			height: 1.5em;
		}
		form label {
			font-size: 0.4em;
			font-weight: bold;
		}
		label.amount-label {
			box-shadow: 1px 0 0 inset, 0 -1px 0 inset;
			display: inline-block;
			padding: 0.25rem 0 0.8rem 0.4rem;
			height: 1rem;
			text-transform: none;
		}
		code {
			font-family: monospace;
			font-size: 0.8em;
			font-weight: bold;
			letter-spacing: 0.1em;
			padding: 0.1em 0;
		}
		input,
		input[type=number]::-webkit-inner-spin-button,
		input[type=number]::-webkit-outer-spin-button {
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;
		}
		input {
			border-radius: 0;
		}
		input:focus {
			background: #5785f622;
			outline: 0;
		}
		input[type=number] {
			-moz-appearance: textfield;
		}
		input[type=number]::-webkit-inner-spin-button,
		input[type=number]::-webkit-outer-spin-button  {
			margin: 0;
		}
		table {
			border-collapse: collapse;
			margin-bottom: 0.75em;
		}
		tr:last-of-type td:first-of-type {
			text-align: center;
		}
		tbody tr:not(:last-of-type) td:nth-of-type(2) {
			text-align: right;
		}
		tbody tr:not(:last-of-type) td:nth-of-type(2):before {
			content: "X";
			float: left;
		}
		tbody tr:last-of-type {
			font-size: 0.6em;
			font-weight: bold;
		}
		tbody tr:not(:last-of-type) td {
			padding: 0.625em 0.5em 0.125em 0.5em;
		}
		tbody tr:last-of-type td {
			padding: 0.125em 0.25em;
		}
		tr {
			font-size: 0.4em;
		}
		th, td {
			border: 1px solid;
		}
		th {
			font-weight: normal;
			padding: 0.125em 0.5em;
		}
		td:nth-of-type(1) {
			width: 38%;
		}
		td:nth-of-type(2) {
			width: 12%;
		}
		td:nth-of-type(3) {
			width: 35%;
		}
		td:nth-of-type(4) {
			width: 15%;
		}

		/* Sides */
		form, .side1, .side2 {
			transition: transform 0.2s linear;
		}
		input[type=checkbox] {
			top: -1em;
			left: -1em;
		}
		input[type=checkbox]:checked ~ form {
			transform: translateY(8.1em) rotateZ(90deg);
		}
		input[type=checkbox]:checked ~ form .side1 {
			transform: rotateX(180deg);
		}
		input[type=checkbox]:checked ~ form .side2 {
			transform: translate(-50%,-50%) scaleX(-1) rotateZ(90deg) rotateY(-180deg);
		}
		.side1, .side2 {
			background: #fff;
			position: absolute;
		}
		.side1 {
			display: grid;
			grid-template-columns: 1.2em 17.5em 10.675em;
			padding: 0.5em 0.5em 1.5em 0.125em;
			top: 0;
			left: 0;
			height: inherit;
			width: inherit;
			backface-visibility: hidden;
			-webkit-backface-visibility: hidden;
			z-index: 2;
		}
		.side2 {
			color: #aaa;
			font-family: "Tinos", serif;
			padding: 0.5em 0.75em;
			top: 50%;
			left: 50%;
			text-transform: uppercase;
			transform: translate(-50%,-50%) scaleX(-1) rotateZ(90deg);
			height: 30em;
			width: 13.75em;
			z-index: 1;
		}
		.number-fields {
			margin-bottom: -0.4em;
		}
		.number-fields input {
			box-shadow: 0 0 0 0.15em #ddd inset;
			margin-right: -0.15em;
			text-align: center;
			height: 1.5em;
			width: 1.2em;
		}
		.note p em, .details label {
			text-transform: uppercase;
		}

		/* Note column */
		.note p {
			font-size: 0.25em;
			text-align: center;
			writing-mode: tb-rl;
			transform: translateY(3.3em) rotate(180deg);
			width: 100%;
		}
		.form-no {
			line-height: 2;
		}

		/* Details column (main) */
		.customer-no {
			margin: 0 0 0.5em 1em;
			text-align: center;
			width: 11.5em;
		}
		.customer-details {
			display: flex;
			flex-wrap: wrap;
			justify-content: space-between;
			align-items: flex-end;
			margin: 0 0.75em 0 0.25em;
		}
		.customer-details label:not(:last-of-type) {
			flex-basis: 8%;
		}
		.customer-details input {
			flex-basis: 100%;
			transform: translateY(0.2em);
		}
		.customer-details label + input, input + .detail-tip:not(:last-of-type) {
			flex-basis: 92%;
		}
		.signature {
			font-family: "Mr Dafoe", cursive;
			text-align: center;
		}
		.detail-tip {
			box-shadow: 0 1px 0 inset;
			display: block;
			font-size: 0.3em;
			line-height: 2;
			margin: -0.5em 0 -1.5em 8%;
			text-align: center;
			text-transform: uppercase;
			flex-basis: 100%;
		}
		input + .detail-tip:last-of-type {
			margin-left: 0;
		}
		.bank-name {
			font: 0.4em "Tinos", serif;
			margin: 0.8rem auto 0 auto;
		}
		.bank-name span {
			color: #888;
		}
		.bank-no:before, .bank-no:after {
			content: "|:";
			letter-spacing: -0.4em;
		}
		.bank-no:before {
			margin-right: 0.25em;
		}
		.bank-no:after {
			margin-left: -0.25em;
		}
		.details label.amount-label {
			text-transform: none;
			padding-left: 0.25em;
			transform: translateX(2em);
		}

		/* Amounts column */
		.amounts {
			display: grid;
			grid-template-columns: 28% 50% 22%;
			grid-template-rows: 0.6em repeat(7, 1.5em);
		}
		.amount-col-title {
			font-size: 0.5em;
			font-weight: bold;
			text-align: center;
		}
		.amounts label.amount-label {
			font-weight: normal;
			margin-right: 0.9em;
			align-self: end;
		}
		.amounts label.amount-label:first-of-type {
			font-size: 0.24em;
			padding-top: 0;
			text-align: center;
			text-transform: uppercase;
		}
		.amounts .number-fields {
			grid-column: 2 / 4;
		}
		.amounts .number-fields input {
			height: 1.6em;
		}
		.amounts .number-fields:not(:last-of-type) input:nth-child(5),
		.amounts .number-fields:last-of-type input:nth-child(8) {
			margin-right: 0;
		}
		.amounts .number-fields:last-of-type {
			grid-column: 1 / 4;
		}
		.two-line, .two-line-small {
			display: inline-block;
		}
		.two-line {
			transform: translateY(-50%);
		}
		.two-line-small {
			font-size: 0.65em;
			transform: translateY(-25%);
		}
		.dollars {
			display: inline-block;
			font-weight: bold;
			text-align: center;
			width: 0.9em;
		}
		.two-digits {
			margin-left: 1.2em;
		}

		/* Side 2 */
		.checks {
			display: grid;
			grid-template-columns: 27% 52% 21%;
			grid-template-rows: 0.75em repeat(12, 1.7em);
		}
		.checks-col-main-title {
			font-size: 0.75em;
		}
		.checks-col-main-title span {
			font-size: 0.6em;
			vertical-align: top;
		}
		.checks-col-title, .checks input, .total-tip {
			align-self: end;
		}
		.checks-col-title, .total-tip {
			text-align: center;
		}
		.checks-col-title {
			font-size: 0.4em;
		}
		.checks > input {
			box-shadow: 0 -1px 0 #aaa inset;
			width: 100%;
		}
		.checks input + div, .checks .total-tip + div {
			grid-column: 2 / 4;
		}
		.checks .number-fields {
			color: #fff;
			margin: 0;
			justify-self: end;
			align-self: end;
			position: relative;
		}
		.checks .number-fields input {
			box-shadow:
				0 -0.7em 0 #ddd,
				-0.3em -0.7em 0 #ddd,
				-0.3em 0 0 #ddd,
				-0.3em 1px 0 #ddd;
			margin-right: 0.2em;
			text-align: center;
			vertical-align: top;
			height: 1em;
			width: 1em;
		}
		.checks .number-fields input:first-of-type {
			box-shadow:
				0 -0.7em 0 #ddd,
				-0.5em -0.7em 0 #ddd,
				-0.5em 0 0 #ddd,
				-0.5em 1px 0 #ddd;
		}
		.checks .number-fields input:last-of-type {
			box-shadow:
				0 -0.7em 0 #ddd,
				-0.15em -0.7em 0 #ddd,
				-0.15em 0 0 #ddd,
				0.3em -0.7em 0 #ddd,
				0.3em 0 0 #ddd,
				0.3em 1px 0 #ddd;
			transform: translateX(-0.1em);
		}
		.checks .number-fields:before, .checks .number-fields:after {
			color: inherit;
			content: "";
			display: block;
			position: absolute;
			top: 100%;
			z-index: 1;
		}
		.checks .number-fields:before {
			border: 0;
			border-left: 0.2em solid transparent;
			border-right: 0.2em solid transparent;
			border-bottom: 0.4em solid;
			left: 2em;
			width: 0;
			height: 0;
		}
		.checks .number-fields:after {
			background: currentColor;
			box-shadow:
				-1.15em -0.65em 0,
				-1.25em -0.65em 0,
				-2.35em -0.65em 0,
				-2.45em -0.65em 0,
				-3.55em -0.65em 0,
				-3.65em -0.65em 0,
				-4.75em -0.65em 0,
				-4.85em -0.65em 0;
			right: 2.4em;
			width: 0.3em;
			height: 0.3em;
		}
		.checks .number-fields:last-child {
			box-shadow: -0.5em 0.7em 0 #ddd, 0 0.7em 0 #ddd;
		}
		.total-tip {
			font-size: 0.32em;
		}
	</style>




</head>

<body translate="no" >
<form action="">
	<div class="side1">
		<div class="note">
			<p><span class="form-no">Form# FinanceREm-0011</span> <br><em>Checks and other items are received for deposit subject to the provisions <br>of the uniform commercial code or applicable collection agreement</em>.</p>
		</div>
		<div class="details">
			<div class="customer-no">
				<h1>Transaction Receipt</h1>
				<div class="">
					<input type="text" size="20" value="${response.data.account}" style="border: solid thick gray;">
				</div>
				<label>Customer Number</label>
			</div>
			<div class="customer-details">
<span><label class="amount-label">Total deposit -MWK  :${response.data.amount}</label> </span><input type="text"  style="font-size: 5px;" value="">
				<label for="customername">Name</label><input id="customername" type="text" name="customername" value="${account_name}">
				<span class="detail-tip"><em>(Please print)</em></span>
				<label for="checkdate">Date</label><input id="checkdate" type="text" name="checkdate" value="${new Date().toISOString()}">
				<span class="detail-tip"><em>Deposits may not be available for immediate withdrawal</em></span>
				<input type="text" name="signature" class="signature">
				<span class="detail-tip">Sign here for cash received (if required)</span>
				<div class="bank-name">Finance Realm<span> Micro finance Bank</span></div>

			</div>
			<code class="bank-no">${response.data.transid}</code>
		</div>
		<div class="amounts">

		<img src="${baseURL+'/uploads/'+logo}" alt="">


		</div>

	</div>

</form>
</body>

</html>
`;

                            const winUrl = URL.createObjectURL(
                                new Blob([winHtml], { type: "text/html" })
                            );
                            swal("",'Transaction was successful','success');
                            const win = window.open(
                                winUrl,
                                "win",
                                `width=800,height=400,screenX=200,screenY=200`
                            );


                        }else if(response.status=='error') {

                            swal("Error",'Transaction failed because:'+response.message,'error');
                            // alert('Transaction failed because: '+response.message);
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // $("#cover-spin").hide();
                        $("#svbutton").html("Save changes");
                        swal("Error","Failed to interact with server","error");

                    }
                });

            });



        });
        $("#customer_transact").change(function (){
            var id = $("#customer_transact").val();
            $.ajax({
                url:"<?php echo base_url()?>Loan/get_by_customer/"+id,
                method:"GET",

                beforeSend:()=>{

                },success:function(res){
                    $("#loan_dis").html(res);
                },error:()=>{
                    errorToast('Error','Failed to interact with the server')
                }
            });

        });
        $("#misheck").change(function (){

            $.ajax({
                type: "GET",
                url: baseURL + 'Account/get_vv/'+$("#misheck").val(),
                dataType: 'json',

                beforeSend: () => {
                    // $("#cover-spin").show();
                },
                success: function (response) {
                    // $("#cover-spin").hide();
                    if (response.status == 'success') {



                        let boss_a = `
						<table class="table table-bordered">
										<tr>
											<td>Account no:</td>
											<td>${response.data.account_number}</td>
										</tr>
										<tr>
											<td>Account Balance: MK</td>
											<td>${response.data.balance}</td>
										</tr>
									</table>
						`;




                        $("#teller_display").html(boss_a);
                    }else {
                        swal('','Sorry, this account is not cashier','error')
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // $("#cover-spin").hide();
                    alert('Sorry, error while interacting with the server')

                }
            });


        });
        $("#add_kyc").click(function (){
            // alert('hello')
            $("#Client").val($("#cid").val());
            $("#kyc_modal").modal('show');
        });
        $("#edit_kyc").click(function (){
            // alert('hello')
            $("#Client").val($("#cid").val());
            $("#kyc_modal_edit").modal('show');
        });

        $("#customer_loan").change(function (){
            var id = $("#customer_loan").val();
            $.ajax({
                url:"<?php echo base_url()?>Individual_customers/view_customer/"+id,
                method:"GET",
                dataType:"json",

                beforeSend:()=>{
                    $("#image_actions").html("<i class='fa fa-spinner fa-spin'></i>Loading data");
                },success:function(res){

                    var det = '';
                    det +=`
				<table class="table">
									<tr>
										<td>Firstname</td>
										<td>${res.data.Firstname}</td>
									</tr>
									<tr>
										<td>Lastname</td>
										<td>${res.data.Lastname}</td>
									</tr>
									<tr>
										<td>Gender</td>
										<td>${res.data.Gender}</td>
									</tr>
									<tr>
										<td>Date of Birth</td>
										<td>${res.data.DateOfBirth}</td>
									</tr>
									<tr>
										<td>Contact No</td>
										<td>${res.data.PhoneNumber}</td>
									</tr><tr>
										<td>Profession</td>
										<td>${res.data.Profession}</td>
									</tr><tr>
										<td>Source of Income</td>
										<td>${res.data.SourceOfIncome}</td>
									</tr>
								</table>`;

                    $("#customer-results").html(det);

                    let dd ='';
                    $.each(res.data.loan, function (index, value) {
                        var color = 'orange';
                        if(value.loan_status==='INITIATED'){
                            color = "orange";
                        }
                        if(value.loan_status==='ACTIVE'){
                            color = "green";
                        }
                        if(value.loan_status==='CLOSED'){
                            color = "red";
                        }
                        dd += `

                    <li><a href="<?php echo base_url('loan/view/')?>${value.loan_id}">#${value.loan_number}-</a><span style="color: ${color}">${value.loan_status}</span></li>

    `
                    });

                    $("#loandd").html(dd);
                    let kyc = '';
                    if(isEmpty(res.data.kyc)){
                        kyc = '';
                    }else {
                        kyc +=`
				<tr>
							<td>Photo</td>
							<td><img src="${baseURL+'uploads/'+res.data.kyc.photograph}" alt="" width="100" height="50"></td>
						</tr>
						<tr>
							<td>ID type</td>
							<td>${res.data.kyc.IDType}</td>
						</tr>
						<tr>
							<td>ID Number</td>
							<td>${res.data.kyc.IDNumber}</td>
						</tr>
						<tr>
							<td>ID issue date</td>
							<td>${res.data.kyc.IssueDate}</td>
						</tr>
						<tr>
							<td>ID Expiry date</td>
							<td>${res.data.kyc.ExpiryDate}</td>
						</tr>
						<tr>
							<td>ID front</td>
							<td><img src="${baseURL+'uploads/'+res.data.kyc.id_front}" alt="" width="100" height="50"></td>
						</tr>
						<tr>
							<td>ID back</td>
							<td><img src="${baseURL+'uploads/'+res.data.kyc.Id_back}" alt="" width="100" height="50"></td>
						</tr>
						<tr>
							<td>Sig/fingerprint</td>
							<td><img src="${baseURL+'uploads/'+res.data.kyc.signature}" alt="" width="100" height="50"></td>
						</tr>
				`;
                    }


                    $("#kyc_data").html(kyc)

                },error:()=>{

                    alert('Failed to interact with server check internet connection')
                }
            });

        });
        $("#group_c").change(function (){
            var id = $("#group_c").val();
            if(id===null || id===""){

            }else {
                $.ajax({
                    url:"<?php echo base_url()?>Customer_groups/get_members/"+id,
                    method:"GET",
                    dataType:"json",

                    beforeSend:()=>{
                        $("#customer_loan").html("<i class='fa fa-spinner fa-spin'></i>Loading data");
                    },success:function(res){

                        let det = "";
                        $.each(res.data, function (index, value) {
                            det  +=`<li >${value.Firstname} &nbsp; ${value.Lastname}</li>`
                        })
                        $("#customer_loan").html(det);
                        let dd ='';
                        $.each(res.loan, function (index, value) {
                            var color = 'orange';
                            if(value.loan_status==='INITIATED'){
                                color = "orange";
                            }
                            if(value.loan_status==='ACTIVE'){
                                color = "green";
                            }
                            if(value.loan_status==='CLOSED'){
                                color = "red";
                            }
                            dd += `

                    <li><a href="<?php echo base_url('loan/view/')?>${value.loan_id}">#${value.loan_number}-</a><span style="color: ${color}">${value.loan_status}</span></li>

    `
                        });

                        $("#loandd").html(dd);

                        let kyc = '';
                        if(isEmpty(res.group)){
                            kyc = '';
                        }else {
                            kyc +=`

						<tr>
							<td>Group Code</td>
							<td>${res.group.group_code}</td>
						</tr>
						<tr>
							<td>Group name</td>
							<td>${res.group.group_name}</td>
						</tr>
						<tr>
							<td>Registered date date</td>
							<td>${res.group.group_registered_date}</td>
						</tr>
						<tr>
							<td>Group Business type</td>
							<td>${res.group.group_category}</td>
						</tr>

						<tr>
							<td>Description</td>
								<td>${res.group.group_description}</td>
						</tr>
				`;
                        }


                        $("#kyc_data").html(kyc)

                    },error:()=>{

                        alert('Failed to interact with server check internet connection')
                    }
                });
            }


        });

    });

    function isEmpty(obj) {
        for(var prop in obj) {
            if(obj.hasOwnProperty(prop))
                return false;
        }

        return true;
    }

    CKEDITOR.replace( 'AddressLine1' );
    CKEDITOR.replace( 'AddressLine2' );
    CKEDITOR.replace( 'address' );
    CKEDITOR.replace( 'group_description' );
    CKEDITOR.replace( 'AddressLine3' );
    CKEDITOR.replace( 'narration' );
    function populate(id,chart,name,balance,currency,cdate,status,photo,si,id_fron,id_back){
        $("#search_modal").modal('hide');
        $("#dacn").val(id);
        $("#chartof").val(chart);
        $("#account_name").val(name);
        $("#account_balance").val(balance);
        $("#account_currency").val(currency);
        $("#account_date").val(cdate);
        $("#account_status").val(status);
        let photograph = '<img src="'+baseURL+'uploads/'+photo+'" style="border: thick solid coral; border-radius: 15px;" height="150" width="150">';
        let siginature = '<img src="'+baseURL+'uploads/'+si+'" style="border: thick solid coral; border-radius: 15px;" height="150" width="150">';
        let front_id = '<img src="'+baseURL+'uploads/'+id_fron+'" style="border: thick solid coral; border-radius: 15px;" height="150" width="300">';
        let back_id = '<img src="'+baseURL+'uploads/'+id_back+'" style="border: thick solid coral; border-radius: 15px;" height="150" width="300">';
        $("#photoid").html(photograph);
        $("#sigid").html(siginature);
        $("#idfront").html(front_id);
        $("#idback").html(back_id);
    }
    function show_my_trans(){
        $.ajax({

            url: baseURL + 'Account/get_teller_transaction/'+$("#myacc").val(),
            type: "GET",
            // data: user_data, // serializes the form's elements.
            success: function (data) {
                $("#panelp").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                swal("Error","server interaction failed",'error');

            }
        });

    }
    function get_customer(){

        $.ajax({
            url:"<?php echo base_url()?>Individual_customers/view_customer/"+id,
            method:"GET",
            dataType:"json",

            beforeSend:()=>{
                $("#image_actions").html("<i class='fa fa-spinner fa-spin'></i>Loading data");
            },success:function(res){


            },error:()=>{

                alert('Failed to interact with server check internet connection')
            }
        });

    }
    function image_preview(name){
        var ii = "<?php echo base_url('uploads/')?>";
        var image = "<img src='"+ii+name+"'  style='object-fit: cover; height: 100%; width: 100%;'>";
        $('#image_data_preview').html(image)
        $("#image_modal").modal('show');

    }


    function get_teller_balance(){
        let id = "<?php echo $this->session->userdata('user_id') ?>";
        $.ajax({
            url:"<?php echo base_url()?>Account/get_teller_balance/"+id,
            method:"GET",
            dataType:"json",

            beforeSend:()=>{

            },success:function(res){

                $("#drawer_balance").val(res.balance);

            },error:()=>{

                alert('Failed to interact with server check  connection')
            }
        });
    }
    function add_group_member(id){
        $("#group_idd").val(id);
        $("#add_group_member_modal").modal('show');
    }
    function recommend_loan(id){
        $("#loan_ids").val(id);
        $("#recommendation_modal").modal('show');
    }
    function score_card(id){

        $("#score_modal").modal('show');
        let tht = `
	<a href="${baseURL}loan/approval_action?id=${id}&action=APPROVED"  onclick="return confirm('Are you sure you want to approve this loan?')" class="btn btn-sm btn-danger">Approve</a>
	<a href="${baseURL}loan/approval_action?id=${id}&action=REJECT"  onclick="return confirm('Are you sure you want to reject this?')" class="btn btn-sm btn-warning">Reject</a>
`;
        $('#btns').html(tht);



    }
    function score_cardp(id){

        $("#score_modall").modal('show');

        $.ajax({
            url:"<?php echo base_url()?>Loan/score_data/"+id,
            method:"GET",


            beforeSend:()=>{
                $("#score_dataa").html("<i class='fa fa-spinner fa-spin'></i>Loading data");
            },success:function(res){

                $("#score_dataa").html(res);

            },error:()=>{

                alert('Failed to interact with server check internet connection')
            }
        });


    }
    function pay_current(){
        $("#payment_modal").modal('show');
    }
    function advance_payment(){
        $("#advance_payment_modal").modal('show');
    }

    function uploadpro(id) {

        uploadp(document.getElementById(id).files[0],id);
    }
    function uploadp(file,id){

        let formData = new FormData();
        let photo = file;
        formData.append("file", photo);

        $.ajax({
            url: "<?php echo base_url()?>Proofofidentity/upload",
            method: "POST",
            contentType:false,
            data: formData,
            cache: false,
            processData: false,
            dataType:"json",
            beforeSend: () => {
                $("#ppp").attr("disabled", true);
                $("#ppp").html("<i class='fa fa-spinner fa-spin'></i>Processing");
            },
            success: (response)=>{
                $("#ppp").attr("disabled", false);
                $("#ppp").html("<font color='green'>Featured image uploaded</font>");
                if (response.status == "success") {

                    // alert(response.data.file_name);
                    var tf = "<?php echo base_url('uploads/')?>"+response.data.file_name;
                    $("#"+id+"1").val(response.data.file_name);


                } else {
                    $("#ppp").attr("disabled", false);
                    $("#ppp").html("Featured image was not uploaded");
                    // $("#pvu").html("");

                    alert(response.message);
                }

            }, error: (xht, error, e)=>{
                // $("#pvu").html("");
                alert("Error "+xht.status);
            }
        });


    }
    function uploadcommon(id) {

        upload2(document.getElementById(id).files[0],id);
    }
    function upload_minutes(id) {

        upload_live(document.getElementById(id).files[0],id);
    }
    function upload_live(file,id){

        let formData = new FormData();
        let photo = file;
        formData.append("file", photo);
        console.log('started upload');
        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();

                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt(percentComplete * 100);
                        // var $link = $('.'+ids);
                        // var $img = $link.find('i');
                        $("#minutes4").html('Uploading..('+percentComplete+'%)');
                        // $link.append($img);
                    }
                }, false);

                return xhr;
            },
            url: "<?php echo base_url()?>Proofofidentity/upload",
            method: "POST",
            contentType:false,
            data: formData,
            cache: false,
            processData: false,
            dataType:"json",
            beforeSend: () => {
                //var tf = "<?php //echo base_url('uploads/scanningwoohoo.gif')?>//";
                //$("#featured_image").val(tf);
                //var img = `<img src="${tf}"  alt="" height="70" width="100">`;
                //$("#pvu").html(img);
            },
            success: (response)=>{

                if (response.status == "success") {
                    // $("#ppp").attr("disabled", false);
                    $("#minutes4").html("<i class='fa fa-check bg-success' style='color: green;'>File was uploaded</i>");
                    $("#minutes4").html("<i class='fa fa-check'></i>Files uploaded");
                    $("#minutes1").val(response.data.file_name);



                } else {
                    // $("#pvu").html("");

                    alert(response.message);
                }

            }, error: (xht, error, e)=>{
                // $("#pvu").html("");
                alert("Error "+xht.status);
            }
        });


    }
    function uploadcommon3(id, l) {

        upload3(document.getElementById(id).files[0],l);
    }
    function upload2(file,id){

        let formData = new FormData();
        let photo = file;
        formData.append("file", photo);

        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();

                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt(percentComplete * 100);
                        // var $link = $('.'+ids);
                        // var $img = $link.find('i');
                        $("#"+id+"3").html('Uploading..('+percentComplete+'%)');
                        // $link.append($img);
                    }
                }, false);

                return xhr;
            },
            url: "<?php echo base_url()?>Proofofidentity/upload",
            method: "POST",
            contentType:false,
            data: formData,
            cache: false,
            processData: false,
            dataType:"json",
            beforeSend: () => {
                //var tf = "<?php //echo base_url('uploads/scanningwoohoo.gif')?>//";
                //$("#featured_image").val(tf);
                //var img = `<img src="${tf}"  alt="" height="70" width="100">`;
                //$("#pvu").html(img);
            },
            success: (response)=>{

                if (response.status == "success") {

                    // alert(response.data.file_name);
                    var tf = "<?php echo base_url('uploads/')?>"+response.data.file_name;
                    $("#"+id+"1").val(response.data.file_name);
                    var img = `<img src="${tf}"  alt="" height="100" width="100">`;
                    $("#"+id+"2").html(img);



                } else {
                    // $("#pvu").html("");

                    alert(response.message);
                }

            }, error: (xht, error, e)=>{
                // $("#pvu").html("");
                alert("Error "+xht.status);
            }
        });


    }
    function upload3(file,id){

        let formData = new FormData();
        let photo = file;
        formData.append("file", photo);

        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();

                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt(percentComplete * 100);
                        // var $link = $('.'+ids);
                        // var $img = $link.find('i');
                        // $("#"+id+"3").html('Uploading..('+percentComplete+'%)');
                        // $link.append($img);
                    }
                }, false);

                return xhr;
            },
            url: "<?php echo base_url()?>Proofofidentity/upload",
            method: "POST",
            contentType:false,
            data: formData,
            cache: false,
            processData: false,
            dataType:"json",
            beforeSend: () => {
                //var tf = "<?php //echo base_url('uploads/scanningwoohoo.gif')?>//";
                //$("#featured_image").val(tf);
                //var img = `<img src="${tf}"  alt="" height="70" width="100">`;
                //$("#pvu").html(img);
            },
            success: (response)=>{

                if (response.status == "success") {

                    // alert(response.data.file_name);
                    var tf = "<?php echo base_url('uploads/')?>"+response.data.file_name;
                    $("#"+id).val(response.data.file_name);




                } else {
                    // $("#pvu").html("");

                    alert(response.message);
                }

            }, error: (xht, error, e)=>{
                // $("#pvu").html("");
                alert("Error "+xht.status);
            }
        });


    }
    function uploadprofile(id) {

        uploadd(document.getElementById(id).files[0],id);
    }
    function uploadd(file,id){

        let formData = new FormData();
        let photo = file;
        formData.append("file", photo);

        $.ajax({
            url: "<?php echo base_url()?>Proofofidentity/upload",
            method: "POST",
            contentType:false,
            data: formData,
            cache: false,
            processData: false,
            dataType:"json",
            beforeSend: () => {
                $("#ppp").attr("disabled", true);
                $("#ppp").html("<i class='fa fa-spinner fa-spin'></i>Uploading file please wait");
            },
            success: (response)=>{


                if (response.status == "success") {
                    $("#ppp").attr("disabled", false);
                    $("#ppp").html("<i class='fa fa-check bg-success' style='color: green;'>File was uploaded</i>");
                    // alert(response.data.file_name);
                    successToast('Success','file was successfully you may proceed')
                    $("#"+id+"1").val(response.data.file_name);




                } else {
                    // $("#pvu").html("");
                    successToast('Success','file was successfully you may proceed')
                    errorToast('Sorry something went wrong when uploading, please try again');
                }

            }, error: (xht, error, e)=>{
                // $("#pvu").html("");
                errorToast("Error "+xht.status);

            }
        });


    }



    function showToast() {
        var toastHTML = `<div class="toast fade hide bg-success" data-delay="3000">
        <div class="toast-header">
            <i class="anticon anticon-info-circle text-white m-r-5"></i>
            <strong class="mr-auto">Upload success</strong>

            <button type="button" class="ml-2 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body text-white">
            Wow, image was uploaded successfully.
        </div>
    </div>`

        $('#notification-toast').append(toastHTML)
        $('#notification-toast .toast').toast('show');
        setTimeout(function(){
            $('#notification-toast .toast:first-child').remove();
        }, 3000);
    }
    function successToast(header,message) {
        var toastHTML = `<div class="toast fade hide bg-success" data-delay="3000">
        <div class="toast-header">
            <i class="anticon anticon-info-circle text-white m-r-5"></i>
            <strong class="mr-auto">${header}</strong>

            <button type="button" class="ml-2 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body text-white">
            ${message}.
        </div>
    </div>`

        $('#notification-toast').append(toastHTML)
        $('#notification-toast .toast').toast('show');
        setTimeout(function(){
            $('#notification-toast .toast:first-child').remove();
        }, 3000);
    }
    function errorToast(header,message) {
        var toastHTML = `<div class="toast fade hide bg-danger" data-delay="3000">
        <div class="toast-header">
            <i class="anticon anticon-info-circle text-white m-r-5"></i>
            <strong class="mr-auto">${header}</strong>

            <button type="button" class="ml-2 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body text-white">
            ${message}.
        </div>
    </div>`

        $('#notification-toast').append(toastHTML)
        $('#notification-toast .toast').toast('show');
        setTimeout(function(){
            $('#notification-toast .toast:first-child').remove();
        }, 3000);
    }
    function deleteToast() {
        var toastHTML = `<div class="toast fade hide bg-success" data-delay="3000">
        <div class="toast-header">
            <i class="anticon anticon-info-circle text-white m-r-5"></i>
            <strong class="mr-auto">Delete success</strong>

            <button type="button" class="ml-2 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body text-white">
            Wow, image was deleted successfully.
        </div>
    </div>`

        $('#notification-toast').append(toastHTML)
        $('#notification-toast .toast').toast('show');
        setTimeout(function(){
            $('#notification-toast .toast:first-child').remove();
        }, 3000);
    }

    function mish(id){

        $("#tellering_account").val(id)
        $("#textt").html(id)
        $("#tellering-modal").modal('show');
    }

</script>
<script>
    function makeid(length) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() *
                charactersLength));
        }
        return result;
    }
    $(document).ready(function(){

        var count = 1;

        dynamic_field(count);

        function dynamic_field(number)
        {
            let f =  makeid(10);
            html = '<tr>';
            html += '<td><input type="text" name="first_name[]" class="form-control" /></td>';
            html += `<td><label for="id_front${number}" class="custom-file-upload"> Upload document </label>
					<input type="file"  onchange="uploadcommon3('id_front${number}', '${f}')"   id="id_front${number}"  /><input type="text" id="${f}"  name="filen[]"  ></td>`;
            if(number > 1)
            {
                html += '<td><button type="button" name="remove" id="" class="btn btn-danger remove">Remove</button></td></tr>';
                $('#addf').append(html);
            }
            else
            {
                html += '<td><button type="button" name="add" id="add" class="btn btn-success">Add</button></td></tr>';
                $('#addf').html(html);
            }
        }

        $(document).on('click', '#add', function(){
            count++;
            dynamic_field(count);
        });

        $(document).on('click', '.remove', function(){
            count--;
            $(this).closest("tr").remove();
        });



    });

    function approve_all(){
        if(confirm('Are you sure you want perform bulk approval of selected items?')) {
            document.frmUser.action = baseURL+"/Individual_customers/bulkactions";
            document.frmUser.submit();
        }
    }
    function reject(){
        if(confirm('Are you sure you want perform bulk reject?')) {
            document.frmUser.action = baseURL+"/Individual_customers/bulkreject";
            document.frmUser.submit();
        }

    }
    function minutes_upload() {
        $("#minutes_modal").modal('show');
    }
    function approve_all_loans(){
        if(confirm('Are you sure you want perform bulk approval of selected loans?')) {
            document.frmUser.action = baseURL+"/Loan/bulkactions";
            document.frmUser.submit();
        }
    }
    function reject_all_loans(){
        if(confirm('Are you sure you want perform bulk reject?')) {
            document.frmUser.action = baseURL+"/Loan/bulkreject";
            document.frmUser.submit();
        }

    }
    function checkAll(o) {
        var boxes = document.getElementsByTagName("input");
        for (var x = 0; x < boxes.length; x++) {
            var obj = boxes[x];
            if (obj.type == "checkbox") {
                if (obj.name != "check")
                    obj.checked = o.checked;
            }
        }
    }



    var fieldId = 0;
    var fieldId1 = 0;
    function addField() {
        fieldId++;
        var html = '<br /><hr/>  <div class="row">\n' +
            '                                    <div class="col-6 mt-2"><input type="text" name="name[]" placeholder="collateral name" class="form-control"></div>\n' +
            '                                    <div class="col-6 mt-2"><input type="text" name="type[]" placeholder="collateral type" class="form-control"></div>\n' +
            '                                </div>\n' +
            '                                <div class="row">\n' +
            '                                    <div class="col-6 mt-2"><input type="text" name="serial[]" placeholder="serial number" class="form-control"></div>\n' +
            '                                    <div class="col-6 mt-2"><input type="text" name="value[]" placeholder="collateral value" class="form-control"></div>\n' +
            '                                </div>\n' +
            '                                <div class="row">\n' +
            '                                    <div class="col-6 mt-2"><label for="ifi"  >upload attachment</label><input type="file" name="files[]" style="display: block" placeholder="Attachment" class="form-control"></div>\n' +
            '                                </div>\n' +
            '                                <div class="row">\n' +
            '                                    <div class="col-12 mt-2"><textarea class="form-control" name="desc[]" id="" cols="30" rows="6"></textarea></div>\n' +
            '                                </div>' + '<button class="btn btn-danger" onclick="removeField(' + fieldId + ');"><span class="fa fa-minus"></span></button><br />' +
            '';
        addElement('forms', 'div', 'field-'+ fieldId, html);

    }
    function removeField(elementId){
        var fieldId = "field-"+elementId;
        var element = document.getElementById(fieldId);
        element.parentNode.removeChild(element);
    }
    function removeFieldloan(elementId){
        var fieldId = "loan_field-"+elementId;
        var element = document.getElementById(fieldId);
        element.parentNode.removeChild(element);
    }

    function addloan_files(){

        fieldId1++;
        var html = `
                                    <div class="row">
                                        <div class="col-6"><br><br><input type="text" name="file_name[]" placeholder="File name" class="form-control" required></div>
                                        <div class="col-6 "><label for="llsid"  >upload file</label><input id="llsid" type="file" name="loan_files[]" style="display: block" placeholder="Attachment" class="form-control"></div>

                                    </div>

<button class="btn btn-danger" onclick="removeFieldloan('${fieldId1}');"><span class="fa fa-minus"></span></button><br />
`;
        addElement('loan_forms', 'div', 'loan_field-'+ fieldId1, html);
    }
    function addElement(parentId, elementTag, elementId, html){
        var id = document.getElementById(parentId);
        var newElement = document.createElement(elementTag);
        newElement.setAttribute('id', elementId);
        newElement.innerHTML = html;
        id.appendChild(newElement);

    }

    function get_loan_files(loan_id){
        $("#loan_files_modal").modal('show');
        $.ajax({
            url:"<?php echo base_url()?>Loan/get_loan_files/"+loan_id,
            method:"GET",

            beforeSend:()=>{

                $("#loan_files_data").html("<i class='fa fa-spinner fa-spin'></i>loading files please wait");
            },success:function(res){
                $("#loan_files_data").html(res);
            },error:()=>{
                errorToast('Error','Failed to interact with the server')
            }
        });
    }
</script>

</body>
</html>
