<?php
$settings = get_by_id('settings','settings_id','1');


?>
<!-- Footer START -->
<footer class="footer">
    <div class="footer-content">
        <p class="m-b-0">Copyright © 2025  Infocus Tech (0994099461)</p>
        <span>
                            <a href="#" class="text-gray m-r-15">Term &amp; Conditions</a>
                            <a href="#" class="text-gray">Privacy &amp; Policy</a>
                        </span>
    </div>
</footer>
<!-- Footer END -->
</div>
</div>
</div>

<div aria-hidden="true" class="onboarding-modal modal fade" id="registration_fees_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <span></span><button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title" >Registration Payments</h4>
                <p style="color: red;">Are you sure you want to pay  registration fees  as below details?</p>

                <form action="<?php echo base_url('loan/pay_registration')?>" class="form-row" method="POST" >

                    <div class="form-group col-lg-12" style="padding: 5em;">
                        <label for="date">To pay amount  </label>
                        <input type="text" name="customer_id" id="reg_customer_id" hidden>

                        <input style="border: thin red solid;" type="text" class="form-control" id="rlm" name="amount"  readonly required />
                    </div>
                    <button class="btn btn-sm btn-block btn-danger" type="submit">Submit Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" class="onboarding-modal modal fade" id="recommendation_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Loan officer Recommendation checklist </h4>
                <h5 class="onboarding-title">Loan Requirements fulfilment area</h5>
                <form action="<?php echo base_url('Loan/recommend_action')?>" method="post" class="form-row" enctype="multipart/form-data">
                    <table class="table">
                        <tr>
                            <th>Application requirement</th>
                            <th>Score (Fail, Average, good, Very Good, excellent)</th>
                            <th>Comments</th>
                        </tr>  <tr>
                            <td>Application Form  (score out of 15)</td>
                            <td><input type="text" name="loan_id" id="loan_ids" hidden><input type="number" name="application_form" max="15" required></td>
                            <td><textarea name="application_form_comment" id="" cols="10" rows="5"></textarea></td>
                        </tr>
                        <tr>
                            <td>Letter from the Local Authority (score out of 10)</td>
                            <td><input type="number" name="letter_from_auth" max="10" required></td>
                            <td><textarea name="letter_from_auth_comment" id="" cols="10" rows="5"></textarea></td>
                        </tr>
                        <tr>
                            <td>Commitment Fee  (10)</td>
                            <td><input type="number" name="commitment_fee" max="10" required></td>
                            <td><textarea name="commitment_fee_comment" id="" cols="10" rows="10"></textarea></td>
                        </tr>
                        <tr>
                            <td>Evidence of Access to Land / Existence of the business (score out of 15))</td>
                            <td><input type="number" name="land_evidence" max="15" required></td>
                            <td><textarea name="land_evidence_comment" id="" cols="10" rows="5"></textarea></td>
                        </tr>
                        <tr>
                            <td>
                                Off taker Agreement (score out of 10)
                            </td>
                            <td><input type="number" name="offtaker_evidence" max="10" required></td>
                            <td><textarea name="offtaker_evidence_comment" id="" cols="10" rows="5"></textarea></td>
                        </tr>
                        <tr>
                            <td>
                                Training Received (score out of 10)
                            </td>
                            <td><input type="number" name="training_recieved" max="10" required></td>
                            <td><textarea name="training_recieved_comment" id="" cols="10" rows="5"></textarea></td>
                        </tr>
                        <tr>
                            <td>
                                Loans owed (score out of 15)
                            </td>
                            <td><input type="number" name="loans_owed" max="15" required></td>
                            <td><textarea name="loans_owed_comment" id="" cols="10" rows="5"></textarea></td>
                        </tr>
                        <tr>
                            <td>
                                Character In the community (score out of 15)
                            </td>
                            <td><input type="number" name="community_character" max="15" required></td>
                            <td><textarea name="community_character_comment" id="" cols="10" rows="5"></textarea></td>
                        </tr>
                    </table>


                    <button type="submit" class="btn btn-primary">Submit recommendation</button>

                </form>
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
                <h4 class="onboarding-title">comments</h4>



                <div class="form-group col-12">
                    <textarea name="comment" id="" cols="30" rows="10" placeholder="Write your comments"></textarea>
                </div>


                <button type="button" onclick="approve_all_loans()" class="btn btn-primary">Save Changes</button>


            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="minutes_modal1" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">comments</h4>



                <div class="form-group col-12">
                    <textarea name="comment" id="" cols="30" rows="10" placeholder="Write your comments"></textarea>
                </div>


                <button type="button" onclick="recommend_all_loans()" class="btn btn-primary">Save Changes</button>


            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="disburse_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Disbursement</h4>


                <form action="<?php echo base_url('Loan/disburse_loan'); ?>" class="form-row" method="post">
                    <div class="form-group col-12">
                        <label for="pdate">Previous loan start date</label>
                        <input type="text" id="dloan_id" name="loan_id" hidden>
                        <input type="date" id="pdate" class="form-control" name="pdate" readonly>
                    </div>


                    <div class="form-group col-12">
                        <textarea name="comment" id="" cols="30" rows="10" placeholder="Write your comments"></textarea>
                    </div>
                    <button type="submit"  class="btn btn-primary">Save Changes</button>
                </form>




            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" class="onboarding-modal modal fade" id="disburse_modal_pre_paid_charge" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Disbursement</h4>


                <form action="<?php echo base_url('Loan/disburse_loan_pre_paid'); ?>" class="form-row" method="post">
                    <div class="form-group col-12">
                        <label for="pdate">Previous loan start date</label>
                        <input type="text" id="dloan_iddd" name="loan_id" hidden>
                        <input type="date" id="pdatee" class="form-control" name="pdate" readonly>
                    </div>
                    <!--
                    <div class="form-group col-12">
                        <label for="cdate">You can change loan start date here</label>
                        <input type="date" id="cdate" class="form-control" name="cdate">
                    </div>
                    -->
                    <div class="form-group col-12">
                        <textarea name="comment" id="" cols="30" rows="10" placeholder="Write your comments"></textarea>
                    </div>
                    <button type="submit"  class="btn btn-primary">Save Changes</button>
                </form>




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
                        <label for="date">Total processing fee amount </label>
                        <input type="text" class="form-control" name="amount" id="charge_amount" readonly required />
                    </div>
                    <div class="form-group col-12">
                        <label for="payment_method">Payment Method</label>
                        <?php

                        $methods = get_all('payment_method')
                        ?>
                        <select name="payment_method" id="payment_method" class="form-control">
                            <option value="">-select--</option>
                            <option value="0">Bank savings</option>
                            <?php

                            foreach ($methods as $method){
                                ?>
                                <option value="<?php  echo $method->payment_method ?>"><?php  echo $method->payment_method_name ?></option>
                                <?php

                            }
                            ?>

                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label for="payment_method">Payment Reference number</label>
                        <input type="text" class="form-control" name="reference" id="reference"   />
                    </div>
                    <div class="form-group col-12">
                        <label for="signature" class="custom-file-upload"> Upload Attachment if available </label>
                        <input type="file" onchange="uploadcommon('refid')"    id="refid" placeholder="Rewfenrence attachemnt" />
                        <input type="text" id="refid1" name="referencedoc" hidden >

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

<div aria-hidden="true" class="onboarding-modal modal fade" id="loan_co_files_modal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Loan collateral files</h4>
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
<div aria-hidden="true" class="onboarding-modal modal fade" id="add_rejection_reason" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Add reason for rejecting loan</h4>
                <form action="<?php echo base_url('loan/single_reject')?>" method="post" class="form-row" >
                    <input type="text" name="loan_id" id="loan_id_reject"  hidden required>
                    <div class="form-group col-12">
                        <label for="varchar">Reasons</label>
                        <textarea class="form-control" name="rejectedReasons" id="" cols="30" rows="6"></textarea>
                    </div>
                    <div class="form-group col-12">

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="add_approval_reason" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Add reason for recommending loan</h4>
                <form action="<?php echo base_url('loan/single_recommend')?>" method="post" class="form-row" >
                    <input type="text" name="loan_id" id="loan_id_approval"  hidden required>
                    <div class="form-group col-12">
                        <label for="varchar">Reasons for recommendations</label>
                        <textarea class="form-control" name="recommend_reasons" id="" cols="30" rows="6"></textarea>
                    </div>
                    <div class="form-group col-12">

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="addAllrejection_reason" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Reason for rejecting all loan</h4>
                <form action="<?php echo base_url('/loan/bulkreject')?>" method="post" class="form-row" >
                    <input type="text" name="group_id" id="group_idd"  hidden required>
                    <div class="form-group col-12">
                        <label for="varchar">Reasons</label>
                        <textarea class="form-control" name="rejectedReasons" id="" cols="30" rows="6"></textarea>
                    </div>
                    <div class="form-group col-12">

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="recommende_rejection_reason" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Reason for rejecting all loan</h4>
                <form action="<?php echo base_url('/loan/bulkreject_recommend')?>" method="post" class="form-row" >
                    <input type="text" name="group_id" id="group_idd"  hidden required>
                    <div class="form-group col-12">
                        <label for="varchar">Reasons</label>
                        <textarea class="form-control" name="rejectedReasons" id="" cols="30" rows="6"></textarea>
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
<!--<script src="--><?php //echo base_url('admin_assets')?><!--/js/pages/dashboard-default.js"></script>-->
<script src="<?php echo base_url('admin_assets')?>/vendors/quill/quill.min.js"></script>
<!--data tables fuck-->
<script src="<?php echo base_url('admin_assets')?>/vendors/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url('admin_assets')?>/vendors/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url('admin_assets')?>/js/pages/datatables.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>lib/sweetalerts/sweetalert.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css">

<!-- Additional DataTables Scripts -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()?>lib/sweetalerts/sweetalert.min.js"></script>
<!--<script type="text/javascript" src="--><?php //echo base_url()?><!--admin_assets/js/security.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>

<!-- Core JS -->
<script src="<?php echo base_url('admin_assets')?>/js/app.min.js"></script>
<script src="<?php echo base_url('admin_assets/')?>js/toastr.min.js"></script>
<script src="<?php echo base_url('admin_assets/')?>ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url('jquery-ui/')?>jquery-ui.js"></script>
<script src="<?php echo base_url('lib/')?>select2/dist/js/select2.full.min.js"></script>
<script type="text/javascript" src="<?php echo base_url()  ?>gisttech/js/xlsx.core.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js"></script>

<script>
	let baseURL = "<?php echo base_url()?>";
	var apiURL = baseURL + "index.php/";
	let logo = "<?php echo $settings->logo?>";
	var DefaultTable = document.getElementById('resulta');
	var loan = document.getElementById('loand');


</script>
<script type="text/javascript" src="<?php echo base_url()?>lib/jquery.doubleScroll.js"></script>
<script src="<?= base_url('admin_assets/js/file_library/main.js'); ?>"></script>
<script src="<?= base_url('admin_assets/js/file_library/uploader.js'); ?>"></script>
<script src="<?= base_url('admin_assets/js/file_library/explorer.js'); ?>"></script>
<script src="<?= base_url('admin_assets/js/file_library/preview.js'); ?>"></script>
<script src="<?= base_url('admin_assets/js/file_library/dragdrop.js'); ?>"></script>



<script type="text/javascript">

    function configure_teller(id){
        $("#tellering_account").val(id)
        $("#textt").html(id)
        $("#tellering").modal('show')
    }
    function pay_reg_fees (id){

        $("#registration_fees_modal").modal('show')
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
        $("#payment_method1").change(function () {
            let choice =  $("#payment_method1").val();
            if(choice==="01"){
                $("#reference1").prop('disabled', true);

                $("#llshos").hide();



            }else {
                $("#reference1").prop('disabled', false);

                $("#llshos").show();

            }

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
    function pay_charge(loan_id,loan_number,loan_product){
        $.ajax({
            url:"<?php echo base_url()?>Loan/get_charges_fundit/"+loan_id+"/"+loan_product,
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
    function penalty_toggler_above() {
        let choice =  $("#penalty_charge_type_above").val();
        if(choice==="Fixed"){
            $("#penalty_fixed_charge_above").prop('disabled', false);

            $("#penalty_variable_charge_above").prop('disabled', true);



        }else if(choice === "Variable"){
            $("#penalty_fixed_charge_above").prop('disabled', true);

            $("#penalty_variable_charge_above").prop('disabled', false);

        }

    }
    function penalty_toggler_below() {
        let choice =  $("#penalty_charge_type_below").val();
        if(choice==="Fixed"){

            $("#penalty_fixed_charge_below").prop('disabled', false);

            $("#penalty_variable_charge_below").prop('disabled', true);


        }else if(choice === "Variable"){

            $("#penalty_fixed_charge_below").prop('disabled', true);

            $("#penalty_variable_charge_below").prop('disabled', false);
        }

    }
    function penalty_toggler2_above() {
        let choice = $("#processing_charge_type_above").val();
        if (choice === "Fixed") {
            $("#processing_fixed_charge_above").prop('disabled', false);

            $("#processing_variable_charge_above").prop('disabled', true);


        } else if (choice === "Variable") {
            $("#processing_fixed_charge_above").prop('disabled', true);

            $("#processing_variable_charge_above").prop('disabled', false);


        }
    }
    function penalty_toggler2_below() {
        let choice = $("#processing_charge_type_below").val();
        if (choice === "Fixed") {

            $("#processing_fixed_charge_below").prop('disabled', false);

            $("#processing_variable_charge_below").prop('disabled', true);



        } else if (choice === "Variable") {

            $("#processing_fixed_charge_below").prop('disabled', true);

            $("#processing_variable_charge_below").prop('disabled', false);


        }
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

                $("#late_charge_amount").val(0);
                $("#spc").html(res);
                $("#pn").val(schedule);
                $("#spn").html(schedule);
                $("#lm_late").val(loan_amount);
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
    function disburse_loan(id,date){
        $("#dloan_id").val(id);
        $("#pdate").val(date);
        $("#disburse_modal").modal('show');
    }

    function disburse_loan_charge_pre_paid(id,date){
        $("#dloan_iddd").val(id);
        $("#pdatee").val(date);
        $("#disburse_modal_pre_paid_charge").modal('show');
    }
    function finish_payment(id){

        $("#finish_payment_modal").modal('show');
    }
    $('[data-toggle="tooltip"]').tooltip()

    // tooltip on payment modals
    var checkboxes = $(".check-cls"),
        submitButt = $(".submit-btn");

    checkboxes.click(function() {
        submitButt.attr("disabled", !checkboxes.is(":checked"));
    });


    $(document).ready(function() {
        // Select the input field by its ID
        var lateChargeInput = $("#late_charge_amount");

        // Add an event listener to the input field to format input with commas
        lateChargeInput.on("input", function() {
            var inputValue = lateChargeInput.val();

            // Remove any non-numeric or non-decimal characters from the input value
            var sanitizedValue = inputValue.replace(/[^0-9.]/g, "");

            // Format the sanitized value with commas
            var formattedValue = formatWithCommas(sanitizedValue);

            // Update the input value with the formatted value
            lateChargeInput.val(formattedValue);
        });

        // Function to format a number with commas
        function formatWithCommas(value) {
            var parts = value.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }

        // Submit handler
        $("#latepaymentform").on("submit", function() {
            // Get the input value without commas for submission
            var inputValue = lateChargeInput.val();
            var sanitizedValue = inputValue.replace(/[^0-9.]/g, "");
            lateChargeInput.val(sanitizedValue);
        });
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
											<td>Account Balance: </td>
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
                    $("#fee_amount").val(res.data.feeamount);
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

                $.ajax({
                    url:"<?php echo base_url()?>Customer_groups/get_members_corporate/"+id,
                    method:"GET",
                    dataType:"json",

                    beforeSend:()=>{
                        $("#customer_loan1").html("<i class='fa fa-spinner fa-spin'></i>Loading data");
                    },success:function(res){

                        let det = "";
                        $.each(res.data, function (index, value) {
                            det  +=`<li >${value.first_name} &nbsp; ${value.last_name}</li>`
                        })
                        $("#customer_loan1").html(det);
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

                        $("#loandd_corporate").html(dd);

                        let kyc = '';

                            kyc +=`
<table class="table">
						<tr>
							<td>Corporate  Code</td>
							<td>${res.corporate.ClientId}</td>
						</tr>
						<tr>
							<td>Corporate name</td>
							<td>${res.corporate.EntityName}</td>
						</tr>
						<tr>
							<td>Registered date date</td>
							<td>${res.corporate.DateOfIncorporation}</td>
						</tr>
						<tr>
							<td>Business type</td>
							<td>${res.corporate.nature_of_business}</td>
						</tr>

						<tr>
							<td>Sector</td>
								<td>${res.corporate.industry_sector}</td>
						</tr>
</table>
				`;



                        $("#customer_loan1").html(kyc)

                    },error:()=>{

                        alert('Failed to interact with server check internet connection')
                    }
                });



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
    function get_transaction_usage(id){
       $("#breakdown_usage").modal("show");
        $.ajax({
            url:"<?php echo base_url()?>Transactions/get_transactions/"+id,
            method:"GET",
            dataType:"json",

            beforeSend:()=>{
                $("#breakdown_content").html("<i class='fa fa-spinner fa-spin'></i>Loading data");
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
    function reject_single_loan(id){
        $("#loan_id_reject").val(id);
        $("#add_rejection_reason").modal('show');


    }  function approve_single_loan(id){
        $("#loan_id_approval").val(id);

        $("#add_approval_reason").modal('show');


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
    function pay_current_r(){
        $("#payment_modal_r").modal('show');
    }
    function advance_payment_r(){
        $("#advance_payment_modal_r").modal('show');
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

    function uploadprofiledocument(file, id) {
        let formData = new FormData();
        formData.append("file", file);

        const labelElement = document.getElementById(id + '_label');
        const originalLabelContent = labelElement.innerHTML;

        $.ajax({
            url: "<?php echo base_url()?>Proofofidentity/upload",
            method: "POST",
            contentType: false,
            data: formData,
            cache: false,
            processData: false,
            dataType: "json",
            beforeSend: () => {
                // Disable the file input and update the label with spinning icon
                document.getElementById(id + '_input').disabled = true;
                labelElement.innerHTML = "<i class='fa fa-spinner fa-spin'></i> Uploading file please wait";
            },
            success: (response) => {
                document.getElementById(id + '_input').disabled = false;

                if (response.status == "success") {
                    labelElement.innerHTML = "<i class='fa fa-check' style='color: green;'></i> File was uploaded";
                    document.getElementById(id + '_hidden').value = response.data.file_name;
                    successToast('Success', 'File was uploaded successfully');

                    // Reset label after 3 seconds
                    setTimeout(() => {
                        labelElement.innerHTML = originalLabelContent;
                    }, 3000);
                } else {
                    labelElement.innerHTML = originalLabelContent;
                    errorToast('Sorry, something went wrong when uploading, please try again');
                }
            },
            error: (xhr) => {
                document.getElementById(id + '_input').disabled = false;
                labelElement.innerHTML = originalLabelContent;
                errorToast("Error " + xhr.status);
            }
        });
    }
    function uploadprofileDoc(elementId) {
        const fileInput = document.getElementById(elementId);
        if (fileInput && fileInput.files.length > 0) {
            uploadp(fileInput.files[0], elementId);
        } else {
            alert("Please select a file to upload.");
        }
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

    function uploadprofileCompany(id) {
        const fileInput = document.getElementById(id + '_input');
        const file = fileInput.files[0];
        uploadprofiledocument(file, id);
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
    function recommend_prompt() {
        $("#minutes_modal1").modal('show');
    }
    function recommend_all_loans(){
        if(confirm('Are you sure you want perform bulk recommendations of selected loans?')) {
            document.frmrecommend.action = baseURL+"/Loan/bulkactions_recommend";
            document.frmrecommend.submit();
        }
    }
    function recommend_reject_all_loans(){

        $("#recommende_rejection_reason").modal('show');


    }
    function approve_all_loans(){
        if(confirm('Are you sure you want perform bulk approval of selected loans?')) {
            document.frmUser.action = baseURL+"/Loan/bulkactions";
            document.frmUser.submit();
        }
    }
    function reject_all_loans(){

        $("#addAllrejection_reason").modal('show');


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
    function addFieldCorporate() {
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
        addElement('forms1', 'div', 'field-'+ fieldId, html);

    } function addField() {
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
	function addloan_corporate_files(){

        fieldId1++;
        var html = `
                                    <div class="row">
                                        <div class="col-6"><br><br><input type="text" name="file_name[]" placeholder="File name" class="form-control" required></div>
                                        <div class="col-6 "><label for="llsid"  >upload file</label><input id="llsid" type="file" name="loan_files[]" style="display: block" placeholder="Attachment" class="form-control"></div>

                                    </div>

<button class="btn btn-danger" onclick="removeFieldloan('${fieldId1}');"><span class="fa fa-minus"></span></button><br />
`;
        addElement('loan_forms1', 'div', 'loan_field-'+ fieldId1, html);
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

    function get_loan_co_files(loan_id){
        $("#loan_co_files_modal").modal('show');
        $.ajax({
            url:"<?php echo base_url()?>Loan/get_loan_co_files/"+loan_id,
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

<script>
    $(document).ready(function(){
        $('.double-scroll').doubleScroll();
    });

    //Initialization of treeviews


</script>
<script>
    $.fn.extend({
        treed: function (o) {

            var openedClass = 'fa fa-minus';
            var closedClass = 'fa fa-plus';

            if (typeof o != 'undefined'){
                if (typeof o.openedClass != 'undefined'){
                    openedClass = o.openedClass;
                }
                if (typeof o.closedClass != 'undefined'){
                    closedClass = o.closedClass;
                }
            };

            //initialize each of the top levels
            var tree = $(this);
            tree.addClass("tree");
            tree.find('li').has("ul").each(function () {
                var branch = $(this); //li with children ul
                branch.prepend("<i class='indicator " + closedClass + "'></i>");
                branch.addClass('branch');
                branch.on('click', function (e) {
                    if (this == e.target) {
                        var icon = $(this).children('i:first');
                        icon.toggleClass(openedClass + " " + closedClass);
                        $(this).children().children().toggle();
                    }
                })
                branch.children().children().toggle();
            });
            //fire event from the dynamically added icon
            tree.find('.branch .indicator').each(function(){
                $(this).on('click', function () {
                    $(this).closest('li').click();
                });
            });
            //fire event to open branch if the li contains an anchor instead of text
            tree.find('.branch>a').each(function () {
                $(this).on('click', function (e) {
                    $(this).closest('li').click();
                    e.preventDefault();
                });
            });
            //fire event to open branch if the li contains a button instead of text
            tree.find('.branch>button').each(function () {
                $(this).on('click', function (e) {
                    $(this).closest('li').click();
                    e.preventDefault();
                });
            });
        }
    });

    //Initialization of treeviews

    $('#tree1').treed();

    $('#tree2').treed({openedClass:'fa-folder-open', closedClass:'glyphicon-folder-close'});

    $('#tree3').treed({openedClass:'fa-chevron-right', closedClass:'glyphicon-chevron-down'});



    // Initiate the long task via AJAX
   function start_mass_payment(id){
       $.ajax({
           url: '<?php  echo  base_url() ?>/loan/start_long_task',
           type: 'POST',
           success: function(response) {
               // Show acknowledgment message or handle response
               console.log(response);
           },
           error: function(xhr, status, error) {
               console.error(error);
               // Handle error
           }
       });
   }


</script>


<script>

// Add this JavaScript code to your view or in a separate .js file
$(document).ready(function() {
    // Handle loan type selection change
    $('select[name="loan_type"]').on('change', function() {
        var loanProductId = $(this).val();
        
        if (loanProductId) {
            // Make AJAX call to get loan product details
            $.ajax({
                url: baseURL + 'loan/get_loan_product_details',
                type: 'POST',
                data: {
                    loan_product_id: loanProductId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Update the form fields with the returned data
                        $('#interest_min').val(response.data.interest_min);
                        $('#interest_max').val(response.data.interest_max);
                        
                        // Clear the interest field for user input
                        $('#interest').val('');
                        
                        // Optional: Add validation for interest input
                        $('#interest').attr({
                            'min': response.data.interest_min,
                            'max': response.data.interest_max
                        });
                        
                        // Optional: Show any additional product details
                        if (response.data.product_description) {
                            // You can add a div to show this information
                            $('#product_details').html(response.data.product_description);
                        }
                    } else {
                        // Handle error
                        alert('Error loading loan product details');
                    }
                },
                error: function() {
                    alert('Error connecting to server');
                }
            });
        } else {
            // Clear fields if no loan type is selected
            $('#interest_min').val('');
            $('#interest_max').val('');
            $('#interest').val('');
            $('#product_details').html('');
        }
    });
    
    // Optional: Add validation for interest input
    $('#interest').on('input', function() {
        var value = parseFloat($(this).val());
        var min = parseFloat($('#interest_min').val());
        var max = parseFloat($('#interest_max').val());
        
        if (value < min || value > max) {
            $(this).addClass('is-invalid');
            // You can add an error message here
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
$(document).ready(function () {
    // Listen for changes on the loan type dropdown
    $('#loan_type').change(function () {
        // Get the selected option text
        var selectedOptionText = $(this).find('option:selected').text();

        // Extract the frequency from the selected option text
        var frequency = selectedOptionText.match(/\((.*?)\-/); // Matches text between '(' and '-'

        // If a frequency is found
        if (frequency && frequency[1]) {
            // Append the frequency next to the Loan Term label
            $('#loan_term_label').html(`Loan Term (${frequency[1]}):`);
        } else {
            // Reset the label if no frequency is found
            $('#loan_term_label').html('Loan Term:');
        }
    });
});


</script>
<?php

$countries = get_all('geo_countries');
?>
<script>

    let countries = <?= json_encode($countries); ?>;
    let shareholderCount = 1;
    $(document).ready(function () {
        // Initialize shareholder count based on existing shareholders (for edit page)
        let existingShareholders = $("#shareholdersTable .shareholder-container").length;
        if (existingShareholders > 0) {
            shareholderCount = existingShareholders;
        }

        $("#addRow").click(function () {
            shareholderCount++;

            let countryOptions = '<option value="">-- Select Country --</option>';
            countries.forEach(c => {
                countryOptions += `<option value="${c.code}">${c.name}</option>`;
            });

            let newRow = `
  <div class="shareholder-container">
                    <div class="shareholder-title">Shareholder ${shareholderCount}</div>
                    <table>
                <tr><td>Title</td><td><select class="form-control" name="title[]" required>
<option value="">--select--</option>
<option value="Mr">Mr</option>
<option value="Mrs">Mrs</option>
<option value="Miss">Miss</option>
<option value="Dr">Dr</option>
<option value="Prof">Prof</option>
<option value="Rev">Rev</option>
<option value="Bishop">Bishop</option>
<option value="Other">Other</option>
</select>
</td></tr>
                <tr><td>First Name</td><td><input type="text" class="form-control" name="first_name[]" required></td></tr>
                <tr><td>Last Name</td><td><input type="text" class="form-control" name="last_name[]" required></td></tr>
                <tr><td>Gender</td>
                    <td>
                        <select class="form-control" name="gender[]">
                            <option value="">--select--</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </td>
                </tr>
                <tr><td>Nationality</td><td><select class="form-control select2" name="nationality[]" required>${countryOptions}</select></td></tr>
                <tr><td>Phone Number</td><td><input class="form-control" type="text" name="phone_number[]" required></td></tr>
                <tr><td>Email Address</td><td><input class="form-control" type="email" name="email_address[]" required></td></tr>
                <tr><td>Full Address</td> <td><textarea class="form-control" type="text" name="full_address[]" >Address here</textarea></td></tr>
                <tr><td>ID Type *</td>
                    <td>
                        <select class="form-control" name="idtype[]" required>
                            <option value="NATIONAL_IDENTITY_CARD">National ID</option>
                            <option value="PASSPORT">Passport</option>
                            <option value="WORK_PERMIT">Work Permit</option>
                            <option value="DRIVER_LICENSE">Driver's License</option>
                            <option value="NONE">NONE</option>
                        </select>
                    </td>
                </tr>
                <tr><td>Identity Number *</td><td><input class="form-control" type="text" name="idnumber[]" placeholder="Enter ID number" required></td></tr>
                <tr><td>ID File (optional)</td><td><input class="form-control" type="file" style="display: block;" name="idfile[]" ></td></tr>
                 <tr>
                        <td>Ownership share value (%)</td>
                        <td><input class="form-control" style="display: block !important;"  type="number" name="percentage_value[]" required></td>
                    </tr>

                <tr><td></td><td><button type="button" class="removeRow">Remove</button></td></tr>
</table>
</div>
            `;
            $("#shareholdersTable").append(newRow);
        });

        $(document).on("click", ".removeRow", function () {
            $(this).closest(".shareholder-container").remove();
        });
    });

</script>


<script type="text/javascript">
	function calculate_payoff(loan_id) {
		// Reset form
		document.getElementById('payment_options').style.display = 'none';
		document.getElementById('accrued_interest_row').style.display = 'none';
		document.getElementById('total_payoff_row').style.display = 'none';
		document.getElementById('current_balance').textContent = '';
		document.getElementById('accrued_interest').textContent = '';
		document.getElementById('total_payoff').textContent = '';
		document.getElementById('payoff_amount').value = '0';

		// Set the default date to today
		var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
		var yyyy = today.getFullYear();
		today = yyyy + '-' + mm + '-' + dd;
		document.getElementById('payoff_date').value = today;

		// Show the modal
		$('#payoff_modal').modal('show');
	}

	function fetchPayoffAmount() {
		var loan_id = document.querySelector('input[name="loan_id"]').value;
		var payoff_date = document.getElementById('payoff_date').value;

		if (!payoff_date) {
			alert('Please select a valid payoff date.');
			return;
		}

		// Show loading indicator
		document.getElementById('calculate_btn').textContent = 'Calculating...';
		document.getElementById('calculate_btn').disabled = true;

		// Make AJAX request to calculate payoff amount
		$.ajax({
			url: '<?php echo base_url("loan/calculate_payoff"); ?>',
			type: 'POST',
			data: {
				loan_id: loan_id,
				payoff_date: payoff_date
			},
			dataType: 'json',
			success: function(response) {
				if (response.status === 'success') {
					// Update the UI with calculated values
					document.getElementById('current_balance').textContent = formatNumber(response.current_balance);
					document.getElementById('accrued_interest').textContent = formatNumber(response.accrued_interest);
					document.getElementById('total_payoff').textContent = formatNumber(response.total_payoff);
					document.getElementById('payoff_amount').value = response.total_payoff;

					// Show the rows and payment options
					document.getElementById('accrued_interest_row').style.display = '';
					document.getElementById('total_payoff_row').style.display = '';
					document.getElementById('payment_options').style.display = 'block';
					$("#total_amount").val(response.total_payoff)
					$("#total_amount1").val(response.total_payoff)

				} else {
					alert('Error: ' + response.message);
				}
			},
			error: function() {
				alert('An error occurred while calculating the payoff amount. Please try again.');
			},
			complete: function() {
				// Reset loading state
				document.getElementById('calculate_btn').textContent = 'Calculate Pay-off Amount';
				document.getElementById('calculate_btn').disabled = false;
			}
		});
	}

	function formatNumber(number) {
		return parseFloat(number).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
	}
</script>


<!-- Chart JS -->


<script>
	// DataTables initialization with export and loading indicators
	// DataTables initialization with export progress indicator
	$(document).ready(function() {
		// Create a loading overlay function
		function createLoadingOverlay() {
			return $('<div>', {
				id: 'export-loading-overlay',
				css: {
					position: 'fixed',
					top: 0,
					left: 0,
					width: '100%',
					height: '100%',
					backgroundColor: 'rgba(0, 0, 0, 0.5)',
					display: 'flex',
					justifyContent: 'center',
					alignItems: 'center',
					zIndex: 9999
				}
			}).append(
				$('<div>', {
					css: {
						backgroundColor: 'white',
						padding: '20px',
						borderRadius: '10px',
						textAlign: 'center',
						boxShadow: '0 4px 6px rgba(0,0,0,0.1)'
					}
				}).append(
					$('<div>', {
						class: 'spinner-border text-primary',
						role: 'status'
					}),
					$('<p>', {
						text: 'Exporting data. This may take a few moments...',
						css: { marginTop: '15px' }
					})
				)
			);
		}

		// Check if DataTables is loaded
		if ($.fn.DataTable) {
			$('#data-table1').DataTable({
				responsive: true,
				dom: 'Bfrtip',
				buttons: [
					{
						extend: 'copy',
						text: 'Copy',
						action: function(e, dt, node, config) {
							var overlay = createLoadingOverlay().appendTo('body');

							// Use setTimeout to ensure overlay is visible
							setTimeout(() => {
								try {
									// Perform the actual copy action
									$.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, node, config);
								} catch(err) {
									console.error('Copy failed:', err);
								} finally {
									// Remove overlay
									overlay.remove();
								}
							}, 100);
						}
					},
					{
						extend: 'csv',
						text: 'CSV',
						title: 'Data export ' + new Date().getTime(),
						action: function(e, dt, node, config) {
							var overlay = createLoadingOverlay().appendTo('body');

							setTimeout(() => {
								try {
									$.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, node, config);
								} catch(err) {
									console.error('CSV export failed:', err);
								} finally {
									overlay.remove();
								}
							}, 100);
						}
					},
					{
						extend: 'excel',
						text: 'Excel',
						title: 'Audit Trail Export',
						action: function(e, dt, node, config) {
							var overlay = createLoadingOverlay().appendTo('body');

							setTimeout(() => {
								try {
									$.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, node, config);
								} catch(err) {
									console.error('Excel export failed:', err);
								} finally {
									overlay.remove();
								}
							}, 100);
						}
					},
					{
						extend: 'pdf',
						text: 'PDF',
						title: 'Audit Trail Export',
						action: function(e, dt, node, config) {
							var overlay = createLoadingOverlay().appendTo('body');

							setTimeout(() => {
								try {
									$.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, node, config);
								} catch(err) {
									console.error('PDF export failed:', err);
								} finally {
									overlay.remove();
								}
							}, 100);
						}
					},
					{
						extend: 'print',
						text: 'Print',
						action: function(e, dt, node, config) {
							var overlay = createLoadingOverlay().appendTo('body');

							setTimeout(() => {
								try {
									$.fn.dataTable.ext.buttons.print.action.call(this, e, dt, node, config);
								} catch(err) {
									console.error('Print failed:', err);
								} finally {
									overlay.remove();
								}
							}, 100);
						}
					}
				]
			});
		} else {
			console.error('DataTables is not loaded');
		}
	});


	// File upload function using AJAX

</script>
<script>
	// Initialize in correct order after all files are loaded
	$(document).ready(function() {
		// First initialize the core
		FileLibrary.init();

		// Then initialize all modules that need the DOM
		if (FileLibrary.Explorer) FileLibrary.Explorer.init();
		if (FileLibrary.Uploader) FileLibrary.Uploader.init();
		if (FileLibrary.Preview) FileLibrary.Preview.init();
		if (FileLibrary.DragDrop) FileLibrary.DragDrop.init();
	});
</script>

<script>
	// Initialize tabs
	document.addEventListener('DOMContentLoaded', function() {
		// Manual tab functionality in case Bootstrap's built-in doesn't work
		const tabButtons = document.querySelectorAll('#loanTabs .nav-link');
		const tabContents = document.querySelectorAll('.tab-pane');

		tabButtons.forEach(button => {
			button.addEventListener('click', function(e) {
				e.preventDefault();

				// Remove active class from all buttons and content
				tabButtons.forEach(btn => {
					btn.classList.remove('active');
					btn.setAttribute('aria-selected', 'false');
				});

				tabContents.forEach(content => {
					content.classList.remove('show', 'active');
				});

				// Add active class to current button and content
				this.classList.add('active');
				this.setAttribute('aria-selected', 'true');

				const target = document.querySelector(this.dataset.bsTarget);
				target.classList.add('show', 'active');
			});
		});

		// Console log to debug
		console.log('Tab initialization complete');
	});
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get references to form elements
        const categorySelect = document.getElementById('category');
        const shareholdersTable = document.getElementById('shareholdersTable');
        const addRowButton = document.getElementById('addRow');
        const corporateForm = document.getElementById('corporateForm');
        const shareholderSection = document.querySelector('.col-lg-6:nth-child(2)');
        const shareholderTitle = document.querySelector('.col-lg-6:nth-child(2) > p');

        // Function to toggle required attribute on shareholder fields
        function toggleShareholderRequired(isRequired) {
            // Get all required inputs in the shareholders section
            const shareholderInputs = shareholdersTable.querySelectorAll('input[required], select[required], textarea[required]');

            // Set or remove required attribute based on category
            shareholderInputs.forEach(function(input) {
                if (isRequired) {
                    input.setAttribute('required', 'required');
                } else {
                    input.removeAttribute('required');
                }
            });

            // Update UI to indicate optional/required status
            if (isRequired) {
                // Normal display for client category
                shareholderTitle.textContent = 'Shareholder Information (Required)';
                shareholderTitle.style.color = '#000';

                // Add a visual indicator that it's required
                const requiredIndicator = document.createElement('span');
                requiredIndicator.textContent = ' *';
                requiredIndicator.style.color = 'red';
                if (!shareholderTitle.querySelector('.required-indicator')) {
                    requiredIndicator.classList.add('required-indicator');
                    shareholderTitle.appendChild(requiredIndicator);
                }
            } else {
                // Still fully accessible for off_taker category, just not required
                shareholderTitle.textContent = 'Shareholder Information (Optional)';
                shareholderTitle.style.color = '#666';

                // Remove required indicator if exists
                const requiredIndicator = shareholderTitle.querySelector('.required-indicator');
                if (requiredIndicator) {
                    shareholderTitle.removeChild(requiredIndicator);
                }
            }
        }

        // Initial setup based on the current value
        if (categorySelect) {
            // Set initial state
            toggleShareholderRequired(categorySelect.value !== 'off_taker');

            // Add event listener for changes
            categorySelect.addEventListener('change', function() {
                toggleShareholderRequired(this.value !== 'off_taker');
            });
        }

        // Modify form submission to validate conditionally
        if (corporateForm) {
            corporateForm.addEventListener('submit', function(event) {
                // If category is Off taker, we'll let the form submit regardless of shareholder fields
                const isOffTaker = categorySelect.value === 'off_taker';

                if (isOffTaker) {
                    // Remove required validation before submitting
                    toggleShareholderRequired(false);
                } else {
                    // Make sure required validation is enforced
                    toggleShareholderRequired(true);

                    // Additional validation if needed for client category
                    // Check if at least one shareholder exists and has data
                    const shareholderContainers = shareholdersTable.querySelectorAll('.shareholder-container');
                    if (shareholderContainers.length === 0) {
                        alert('At least one shareholder is required for Client corporate customers.');
                        event.preventDefault();
                        return false;
                    }
                }

                // Form can submit normally
                return true;
            });
        }

        // Add a tooltip to clarify the behavior
        const categoryHelp = document.createElement('small');
        categoryHelp.className = 'form-text text-muted';
        categoryHelp.textContent = 'Note: For Off taker category, shareholder information is optional';
        categorySelect.parentNode.appendChild(categoryHelp);
    });
</script>
<script>
    $(document).ready(function() {
        // Bootstrap 4 tabs work automatically with data-toggle="tab"

        // If you're using DataTables, initialize them for each table
        if ($.fn.DataTable) {
            $('#offtaker-table').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: 'Copy',
                        action: function(e, dt, node, config) {
                            var overlay = createLoadingOverlay().appendTo('body');

                            // Use setTimeout to ensure overlay is visible
                            setTimeout(() => {
                                try {
                                    // Perform the actual copy action
                                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, node, config);
                                } catch(err) {
                                    console.error('Copy failed:', err);
                                } finally {
                                    // Remove overlay
                                    overlay.remove();
                                }
                            }, 100);
                        }
                    },
                    {
                        extend: 'csv',
                        text: 'CSV',
                        title: 'Data export ' + new Date().getTime(),
                        action: function(e, dt, node, config) {
                            var overlay = createLoadingOverlay().appendTo('body');

                            setTimeout(() => {
                                try {
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, node, config);
                                } catch(err) {
                                    console.error('CSV export failed:', err);
                                } finally {
                                    overlay.remove();
                                }
                            }, 100);
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        title: 'Off Taker Export',
                        action: function(e, dt, node, config) {
                            var overlay = createLoadingOverlay().appendTo('body');

                            setTimeout(() => {
                                try {
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, node, config);
                                } catch(err) {
                                    console.error('Excel export failed:', err);
                                } finally {
                                    overlay.remove();
                                }
                            }, 100);
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        title: 'Audit Trail Export',
                        action: function(e, dt, node, config) {
                            var overlay = createLoadingOverlay().appendTo('body');

                            setTimeout(() => {
                                try {
                                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, node, config);
                                } catch(err) {
                                    console.error('PDF export failed:', err);
                                } finally {
                                    overlay.remove();
                                }
                            }, 100);
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        action: function(e, dt, node, config) {
                            var overlay = createLoadingOverlay().appendTo('body');

                            setTimeout(() => {
                                try {
                                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, node, config);
                                } catch(err) {
                                    console.error('Print failed:', err);
                                } finally {
                                    overlay.remove();
                                }
                            }, 100);
                        }
                    }
                ]
            });
            $('#client-table').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: 'Copy',
                        action: function(e, dt, node, config) {
                            var overlay = createLoadingOverlay().appendTo('body');

                            // Use setTimeout to ensure overlay is visible
                            setTimeout(() => {
                                try {
                                    // Perform the actual copy action
                                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, node, config);
                                } catch(err) {
                                    console.error('Copy failed:', err);
                                } finally {
                                    // Remove overlay
                                    overlay.remove();
                                }
                            }, 100);
                        }
                    },
                    {
                        extend: 'csv',
                        text: 'CSV',
                        title: 'Data export ' + new Date().getTime(),
                        action: function(e, dt, node, config) {
                            var overlay = createLoadingOverlay().appendTo('body');

                            setTimeout(() => {
                                try {
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, node, config);
                                } catch(err) {
                                    console.error('CSV export failed:', err);
                                } finally {
                                    overlay.remove();
                                }
                            }, 100);
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        title: 'Corporate Client Export',
                        action: function(e, dt, node, config) {
                            var overlay = createLoadingOverlay().appendTo('body');

                            setTimeout(() => {
                                try {
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, node, config);
                                } catch(err) {
                                    console.error('Excel export failed:', err);
                                } finally {
                                    overlay.remove();
                                }
                            }, 100);
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        title: 'Audit Trail Export',
                        action: function(e, dt, node, config) {
                            var overlay = createLoadingOverlay().appendTo('body');

                            setTimeout(() => {
                                try {
                                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, node, config);
                                } catch(err) {
                                    console.error('PDF export failed:', err);
                                } finally {
                                    overlay.remove();
                                }
                            }, 100);
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        action: function(e, dt, node, config) {
                            var overlay = createLoadingOverlay().appendTo('body');

                            setTimeout(() => {
                                try {
                                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, node, config);
                                } catch(err) {
                                    console.error('Print failed:', err);
                                } finally {
                                    overlay.remove();
                                }
                            }, 100);
                        }
                    }
                ]
            });
        }
    });
</script>
</body>
</html>
