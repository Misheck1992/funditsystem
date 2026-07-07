<?php
/**
 * Created by PhpStorm.
 * User: Techsoft
 * Date: 7/10/2019
 * Time: 3:18 PM
 */


if(!function_exists('display_menu_admin')) {

	function display_menu_admin($parent, $level, $toggle) {

		$ci =& get_instance();
		$ci->load->database();
		$ci->load->library('session');
		$ci->load->model('menu_model');

		// $mm=$ci->session->userdata('mid');
		$mm= 1;
//		if($mm==1){
		$result = $ci->db->query("SELECT a.id,a.roll, a.label,a.icon_color, a.type, a.link,a.icon, Deriv1.Count FROM `menu` a  LEFT OUTER JOIN (SELECT parent, COUNT(*) AS Count FROM `menu` GROUP BY parent) Deriv1 ON a.id = Deriv1.parent WHERE a.menu_type_id = 1 AND   a.parent=" . $parent." AND active = 1   order by `sort` ASC")->result();

//		}else{
//			$result = $ci->db->query("SELECT a.id,a.roll, a.label,a.icon_color, a.type, a.link,a.icon, Deriv1.Count FROM `menu` a  LEFT OUTER JOIN (SELECT parent, COUNT(*) AS Count FROM `menu` GROUP BY parent) Deriv1 ON a.id = Deriv1.parent WHERE a.menu_type_id = 1 AND   a.parent=" . $parent." AND active = 1  AND a.roll='$mm' order by `sort` ASC")->result();
//
//		}

        $mm = array();

        foreach ($ci->session->userdata('access') as $rr){
            array_push($mm, $rr->controllerid);
        }
		$ret = '';

		if ($result) {

			foreach ($result as $row) {
            $make_count = $ci->db->query("SELECT COUNT(*) as tt FROM menuitems WHERE mid=$row->id  AND  id != 113 AND show_menu = 'Yes' AND id IN (".implode(',',$mm).")")->row();

if($row->id == $toggle){
	$is_active = 'open';
}else{
	$is_active = "";
}
if($make_count->tt==0){
	$remove = 'display:none;';
}else{
    $remove = "";
}
				$ret .= '

 <li class="nav-item dropdown '.$is_active.' " style="'.$remove .'">
 <a class="dropdown-toggle " href="javascript:void(0);">
  <span class="icon-holder">
                                    <i class="'.$row->icon.'"></i>
                                </span>

						<span class="title" >  ' . $row->label .'</span>
						
							<span class="arrow">
                                    <i class="arrow-icon"></i>
                                </span>
					</a>
         
        
							<ul class="dropdown-menu">
          ';
				$result2 = $ci->db->query("Select * FROM menuitems WHERE mid=$row->id  AND  id != 113 AND show_menu = 'Yes' order by `sortt` ASC ")->result();

				$CI =& get_instance();

				$CI->load->library('session');
				foreach ($result2 as $row2) {
					$flag = false;
					foreach ($CI->session->userdata('access') as $r) {
						if ($r->controllerid == $row2->id) {
							$flag = true;
							break;
						}


					}
					if ($flag) {
						// Check for specific menu items and add badge counts
						$badge_html = '';
						if ($row2->method == 'Loan/recommend') {
							// Count loans with INITIATED status for recommend menu
							$loan_count_result = $ci->db->query("SELECT COUNT(*) as count FROM loan WHERE loan_status = 'INITIATED'")->row();
							$loan_count = $loan_count_result->count;
							if ($loan_count > 0) {
								$badge_html = ' <span class="badge badge-danger ml-2" style="background-color: #dc3545; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-left: 8px;">' . $loan_count . '</span>';
							}
						} elseif ($row2->method == 'loan/initiated') {
							// Count loans with RECOMMENDED status for initiated menu
							$loan_count_result = $ci->db->query("SELECT COUNT(*) as count FROM loan WHERE loan_status = 'RECOMMENDED'")->row();
							$loan_count = $loan_count_result->count;
							if ($loan_count > 0) {
								$badge_html = ' <span class="badge badge-warning ml-2" style="background-color: #ffc107; color: #212529; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-left: 8px;">' . $loan_count . '</span>';
							}
						} elseif ($row2->method == 'Loan/get_approved_first') {
							// Count loans with APPROVED_FIRST status for get_approved_first menu
							$loan_count_result = $ci->db->query("SELECT COUNT(*) as count FROM loan WHERE loan_status = 'APPROVED_FIRST'")->row();
							$loan_count = $loan_count_result->count;
							if ($loan_count > 0) {
								$badge_html = ' <span class="badge badge-info ml-2" style="background-color: #17a2b8; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-left: 8px;">' . $loan_count . '</span>';
							}
						} elseif ($row2->method == 'Loan/get_approved_second') {
							// Count loans with APPROVED_SECOND status for Loan/get_approved_second menu
							$loan_count_result = $ci->db->query("SELECT COUNT(*) as count FROM loan WHERE loan_status = 'APPROVED_SECOND'")->row();
							$loan_count = $loan_count_result->count;
							if ($loan_count > 0) {
								$badge_html = ' <span class="badge badge-success ml-2" style="background-color: #28a745; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-left: 8px;">' . $loan_count . '</span>';
							}
						} elseif ($row2->method == 'loan/approved') {
							// Count loans with APPROVED status for loan/approved menu
							$loan_count_result = $ci->db->query("SELECT COUNT(*) as count FROM loan WHERE loan_status = 'APPROVED'")->row();
							$loan_count = $loan_count_result->count;
							if ($loan_count > 0) {
								$badge_html = ' <span class="badge badge-primary ml-2" style="background-color: #007bff; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-left: 8px;">' . $loan_count . '</span>';
							}
						} elseif ($row2->method == 'loan/unified_approval' || $row2->method == 'Loan/unified_approval') {
							// Count loans with RECOMMENDED status for unified approval menu
							$loan_count_result = $ci->db->query("SELECT COUNT(*) as count FROM loan WHERE loan_status = 'RECOMMENDED'")->row();
							$loan_count = $loan_count_result->count;
							if ($loan_count > 0) {
								$badge_html = ' <span class="badge badge-warning ml-2" style="background-color: #f59e0b; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-left: 8px;">' . $loan_count . '</span>';
							}
						} elseif ($row2->method == 'Loan/created_loans') {
							// Count loans with CREATED status for created loans menu
							$loan_count_result = $ci->db->query("SELECT COUNT(*) as count FROM loan WHERE loan_status = 'CREATED'")->row();
							$loan_count = $loan_count_result->count;
							if ($loan_count > 0) {
								$badge_html = ' <span class="badge badge-secondary ml-2" style="background-color: #6c757d; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-left: 8px;">' . $loan_count . '</span>';
							}
						}
						
						$ret .= '
               <li><a href="' . base_url() .$row2->method . '">' . $row2->label . $badge_html . '</a></li>
               
               ';
					} else {

					}
//               if($row2->method=="read"||$row2->method=="delete"||$row2->method=="update") {
//               }else{

//               }
				}

				$ret .= '
          </ul>
         
        </li>

';

			}
		}



		return $ret;
	}
}
