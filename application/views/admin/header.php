<?php
if(!$this->session->userdata('user_id')){
	redirect(base_url().'auth/logout');
}
$session = get_by_id('user_access','Employee',$this->session->userdata('user_id'));
if($this->session->userdata('rand_id') !=$session->is_logged_in){
redirect(base_url().'auth/logout');

}else{

}

$settings = get_by_id('settings','settings_id','1');
if(!empty($toggles)){
	$tg = $toggles;
}else{
	$tg = '';
}
//$mm = implode(',',$this->session->userdata('access'));


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?php echo $settings->company_name; ?> - Admin Dashboard </title>

	<!-- Favicon -->
	<link rel="shortcut icon" href="<?php echo base_url('admin_assets')?>/images/logo/favicon.png">

	<!-- page css -->
	<link href="<?php echo base_url('admin_assets')?>/vendors/datatables/dataTables.bootstrap.min.css" rel="stylesheet">
	<!-- Core css -->
	<link href="<?php echo base_url('admin_assets')?>/css/app.min.css" rel="stylesheet">
	<link href="<?php echo base_url('admin_assets/')?>css/toastr.min.css" rel="stylesheet">
	<link href="//cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">
	<link href="<?php echo base_url('jquery-ui/')?>jquery-ui.css" rel="stylesheet">
	<link type="text/css" href="<?php echo base_url()  ?>gisttech/css/tableexport.min.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>lib/sweetalerts/sweetalert.css">
	<link href="<?php echo base_url('lib/')?>select2/dist/css/select2.min.css" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
	<style>
        .wrapper {
            position: relative;
        }
        .heading {
            margin: 25px 0;
            font-size: 24px;
        }
        .dashboard-stat {
            position: relative;
            display: block;
            margin: 0 0 25px;
            overflow: hidden;
            border-radius: 4px;
        }
        .dashboard-stat .visual {
            width: 80px;
            height: 80px;
            display: block;
            float: left;
            padding-top: 10px;
            padding-left: 15px;
            margin-bottom: 15px;
            font-size: 35px;
            line-height: 35px;
        }
        .dashboard-stat .visual > i {
            margin-left: -15px;
            font-size: 110px;
            line-height: 110px;
            color: #fff;
            opacity: 0.1;
        }
        .dashboard-stat .details {
            position: absolute;
            right: 15px;
            padding-right: 15px;
            color: #fff;
        }
        .dashboard-stat .details .number {
            padding-top: 25px;
            text-align: right;
            font-size: 34px;
            line-height: 36px;
            letter-spacing: -1px;
            margin-bottom: 0;
            font-weight: 300;
        }.dashboard-stat .details .numberr {
            padding-top: 25px;
            text-align: right;
            font-size: 20px;
            line-height: 36px;
            letter-spacing: -1px;
            margin-bottom: 0;
            font-weight: 300;
        }
        .dashboard-stat .details .number .desc {
            text-transform: capitalize;
            text-align: right;
            font-size: 16px;
            letter-spacing: 0;
            font-weight: 300;
        }
        .dashboard-stat.blue {
            background-color: #337ab7;
        }  .dashboard-stat.green {
            background-color: #24C16B;
        }
        .dashboard-stat.red {
            background-color: #e7505a;
        }
        .dashboard-stat.purple {
            background-color: #8E44AD;
        }
        .dashboard-stat.hoki {
            background-color: #67809F;
        }
        .dashboard-stat.orange {
            background-color: #ffaf7a;
        }
		fieldset {
			margin: 0 0 30px 0;
			border: 1px solid #ccc;
		}
        hr.dash {
            border: 1px solid #24C16B;
        }

		legend {
			background:  #eee;
			padding: 4px 10px;
			color: #000;
			margin: 0 auto;
			display: block;
		}
		input[type="file"] {
			display: none;
		}
		.custom-file-upload {
			border: 1px solid #ccc;
			display: inline-block;
			padding: 6px 12px;
			cursor: pointer;
		}

		.anticon {
			line-height: 0;c
			vertical-align: -.125em;
			color: #ffff;
		}

		.tableCss
		{
			border: solid 1px #e6e5e5;
		}


		.tableCss thead
		{
			background-color: #0094ff;
			color:#fff;
			padding: 5px;
			text-align:center;
		}

		.tableCss td
		{
			border: solid 1px #e6e5e5;
			padding: 5px;
		}

		/*for footer*/
		.tabTask tfoot
		{
			background-color: #000;
			color: #fff;
			padding: 5px;
		}

		/*for body*/
		.tabTask tbody
		{
			background-color: #e9e7e7;
			color: #000;
			padding: 5px;
		}
		.due {background-color: #F3D8D8}
		.paid {background-color: #D1EFD1}
		.due_now {background-color: #DFEFFF}

		.tool-tip {
			display: inline-block;
		}

		.tool-tip [disabled] {
			pointer-events: none;
		}
		.mishe{

		}
        :root{
            --white:#fff;
            --smoke-white:#f1f3f5;
            --blue:#4169e1;
        }
        .container{
            position:relative;
            width:100%;
            height:100%;
            display:flex;
            justify-content:center;
            align-items:center;
        }
        .selector{
            position:relative;
            width:60%;
            background-color:#f1f3f5;
            height:60px;
            display:flex;
            justify-content:space-around;
            align-items:center;
            border-radius:9999px;
            box-shadow:0 0 16px rgba(0,0,0,.2);
        }
        .selecotr-item{
            position:relative;
            flex-basis:calc(70% / 3);
            height:100%;
            display:flex;
            justify-content:center;
            align-items:center;
        }
        .selector-item_radio{
            appearance:none;
            display:none;
        }
        .selector-item_label{
            position:relative;
            height:80%;
            width:100%;
            text-align:center;
            border-radius:9999px;
            line-height:400%;
            font-weight:900;
            transition-duration:.5s;
            transition-property:transform, color, box-shadow;
            transform:none;
        }
        .selector-item_radio:checked + .selector-item_label{
            background-color:#24C16B;
            color:white;
            box-shadow:0 0 4px rgba(0,0,0,.5),0 2px 4px rgba(0,0,0,.5);
            transform:translateY(-2px);
        }
        @media (max-width:480px) {
            .selector{
                width: 90%;
            }
        }
        .btn-primary
        {

            color: #fff !important;
            background-color: #007bff !important;
            border-color: #007bff !important;
        }
        .btn-secondary
        {
            color: #fff !important;
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }
        .btn-info
        {
            color: #fff !important;
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
        }
        .btn-success
        {
            color: #fff !important;
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }
         .btn-warning
        {
             color: #212529 !important;
             background-color: #ffc107 !important;
             border-color: #ffc107 !important;
        }
        .btn-danger
        {
            color: #fff !important;
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }
        @media (max-width: 800px) {
            .hidden-mobile {
                display: none;
            }
        }

        .double-scroll{
            width: 100%;
        }


    </style>
    <style>
        .tree, .tree ul {
            margin:0;
            padding:0;
            list-style:none
        }
        .tree ul {
            margin-left:1em;
            position:relative
        }
        .tree ul ul {
            margin-left:.5em
        }
        .tree ul:before {
            content:"";
            display:block;
            width:0;
            position:absolute;
            top:0;
            bottom:0;
            left:0;
            border-left:1px solid
        }
        .tree li {
            margin:0;
            padding:0 1em;
            line-height:2em;
            color:#369;
            font-weight:700;
            position:relative
        }
        .tree ul li:before {
            content:"";
            display:block;
            width:10px;
            height:0;
            border-top:1px solid;
            margin-top:-1px;
            position:absolute;
            top:1em;
            left:0
        }
        .tree ul li:last-child:before {
            background:#fff;
            height:auto;
            top:1em;
            bottom:0
        }
        .indicator {
            margin-right:5px;
        }
        .tree li a {
            text-decoration: none;
            color:#369;
        }
        .tree li button, .tree li button:active, .tree li button:focus {
            text-decoration: none;
            color:#369;
            border:none;
            background:transparent;
            margin:0px 0px 0px 0px;
            padding:0px 0px 0px 0px;
            outline: 0;
        }
        .is-primary .header {
                    background-color: #0268bc
                }
                .style {
    font-family: 'fantasy';
    color: #0268bc;
    font-weight: bolder;
    background-color: #fff;
    padding: 0.5em;
    border-radius: 50px 0px 50px 0px;
}


        .shareholder-container {
            border: 2px solid #ccc;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f9f9f9;
        }
        .shareholder-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .removeRow {
            background: #e74c3c;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .removeRow:hover {
            background: #c0392b;
        }
        #addRow {
            background: #2ecc71;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }
        #addRow:hover {
            background: #27ae60;
        }
		/*custom style */
		/**
 * Finance Real - File Library Module
 * Main styling for file explorer and components
 */

		/* Core Layout Styles */
		.file-library-container {
			display: flex;
			flex-direction: column;
			height: 100%;
			min-height: 500px;
			background-color: #f8f9fa;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
			overflow: hidden;
		}

		.file-library-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 16px 20px;
			background-color: #fff;
			border-bottom: 1px solid #e9ecef;
			z-index: 10;
		}

		.file-library-title {
			font-size: 1.25rem;
			font-weight: 600;
			color: #343a40;
			margin: 0;
		}

		.file-library-main {
			display: flex;
			flex: 1;
			overflow: hidden;
		}

		.file-library-sidebar {
			width: 260px;
			background-color: #fff;
			border-right: 1px solid #e9ecef;
			display: flex;
			flex-direction: column;
			overflow: hidden;
		}

		.file-library-content {
			flex: 1;
			display: flex;
			flex-direction: column;
			overflow: hidden;
		}

		.file-library-toolbar {
			padding: 12px 16px;
			background-color: #fff;
			border-bottom: 1px solid #e9ecef;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.file-library-toolbar-left {
			display: flex;
			align-items: center;
		}

		.file-library-toolbar-right {
			display: flex;
			align-items: center;
		}

		.file-container {
			flex: 1;
			overflow-y: auto;
			padding: 20px;
			background-color: #f8f9fa;
			position: relative;
		}

		/* Sidebar Styles */
		.sidebar-section {
			margin-bottom: 15px;
		}

		.sidebar-section-title {
			padding: 10px 15px;
			font-weight: 600;
			font-size: 0.9rem;
			color: #495057;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.sidebar-section-title .toggle-icon {
			cursor: pointer;
			font-size: 0.8rem;
			color: #6c757d;
		}

		.sidebar-section-content {
			padding: 0 15px 10px;
		}

		.sidebar-menu {
			list-style: none;
			padding: 0;
			margin: 0;
		}

		.sidebar-menu-item {
			padding: 8px 12px;
			margin: 2px 0;
			border-radius: 4px;
			cursor: pointer;
			display: flex;
			align-items: center;
			color: #495057;
			transition: all 0.2s ease;
		}

		.sidebar-menu-item:hover {
			background-color: #f1f3f5;
			color: #212529;
		}

		.sidebar-menu-item.active {
			background-color: #e9ecef;
			color: #212529;
			font-weight: 500;
		}

		.sidebar-menu-item i {
			margin-right: 8px;
			width: 18px;
			text-align: center;
		}

		.folder-tree {
			padding: 0;
			margin: 0;
			list-style: none;
		}

		.folder-tree-item {
			padding: 4px 0;
		}

		.folder-tree-item-link {
			display: flex;
			align-items: center;
			padding: 6px 12px;
			border-radius: 4px;
			cursor: pointer;
			color: #495057;
			transition: all 0.2s ease;
		}

		.folder-tree-item-link:hover {
			background-color: #f1f3f5;
			color: #212529;
		}

		.folder-tree-item-link.active {
			background-color: #e9ecef;
			color: #212529;
			font-weight: 500;
		}

		.folder-tree-item-link i {
			margin-right: 8px;
			width: 18px;
			text-align: center;
		}

		.folder-tree-children {
			list-style: none;
			padding-left: 20px;
			margin: 0;
		}

		.folder-tree-item .folder-toggle {
			width: 16px;
			cursor: pointer;
			margin-right: 4px;
			text-align: center;
		}

		/* Breadcrumb Styles */



		/* File Container Styles */
		.file-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
			gap: 16px;
		}

		.file-list {
			display: flex;
			flex-direction: column;
		}

		/* File and Folder Items */
		.file-item,
		.folder-item {
			position: relative;
			border-radius: 8px;
			background-color: #fff;
			transition: all 0.2s ease;
			border: 1px solid #e9ecef;
			overflow: hidden;
		}

		/* Grid View */
		.file-grid .file-item,
		.file-grid .folder-item {
			display: flex;
			flex-direction: column;
			padding: 0;
			cursor: pointer;
		}

		.file-grid .file-preview,
		.file-grid .folder-icon {
			height: 120px;
			width: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
			background-color: #f8f9fa;
			border-bottom: 1px solid #e9ecef;
		}

		.file-grid .file-icon,
		.file-grid .folder-icon i {
			font-size: 48px;
			color: #6c757d;
		}

		.file-grid .folder-icon i {
			color: #ffc107;
		}

		.file-grid .file-thumbnail {
			max-width: 100%;
			max-height: 100%;
			object-fit: contain;
		}

		.file-grid .file-details,
		.file-grid .folder-details {
			padding: 12px;
			flex: 1;
		}

		.file-grid .file-name,
		.file-grid .folder-name {
			font-weight: 500;
			font-size: 0.9rem;
			margin-bottom: 4px;
			color: #212529;
			word-break: break-word;
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
			overflow: hidden;
		}

		.file-grid .file-meta,
		.file-grid .folder-meta {
			font-size: 0.75rem;
			color: #6c757d;
		}

		.file-grid .file-actions,
		.file-grid .folder-actions {
			position: absolute;
			top: 8px;
			right: 8px;
			display: none;
			background-color: rgba(255, 255, 255, 0.9);
			border-radius: 4px;
			padding: 4px;
			z-index: 10;
		}

		.file-grid .file-item:hover .file-actions,
		.file-grid .folder-item:hover .folder-actions {
			display: flex;
		}

		/* List View */
		.file-list .file-item,
		.file-list .folder-item {
			display: flex;
			align-items: center;
			padding: 12px 16px;
			cursor: pointer;
			margin-bottom: 8px;
		}

		.file-list .file-select,
		.file-list .folder-select {
			margin-right: 12px;
		}

		.file-list .file-icon,
		.file-list .folder-icon {
			font-size: 24px;
			margin-right: 16px;
			color: #6c757d;
			min-width: 24px;
			text-align: center;
		}

		.file-list .folder-icon i {
			color: #ffc107;
		}

		.file-list .file-details,
		.file-list .folder-details {
			flex: 1;
			min-width: 0;
		}

		.file-list .file-name,
		.file-list .folder-name {
			font-weight: 500;
			color: #212529;
			margin-bottom: 2px;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.file-list .file-category,
		.file-list .folder-meta {
			font-size: 0.8rem;
			color: #6c757d;
		}

		.file-list .file-meta {
			display: flex;
			align-items: center;
			margin: 0 16px;
			min-width: 280px;
		}

		.file-list .file-meta span {
			margin-right: 16px;
			font-size: 0.8rem;
			color: #6c757d;
		}

		.file-list .file-actions,
		.file-list .folder-actions {
			display: none;
		}

		.file-list .file-item:hover .file-actions,
		.file-list .folder-item:hover .folder-actions {
			display: flex;
		}

		/* Shared styles for file/folder items */
		.file-item:hover,
		.folder-item:hover {
			border-color: #ced4da;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
		}

		.file-item.selected {
			background-color: #e7f5ff;
			border-color: #74c0fc;
		}

		.folder-item.folder-drag-over {
			background-color: #e7f5ff;
			border-color: #74c0fc;
			box-shadow: 0 0 0 2px #74c0fc;
		}

		.btn-icon {
			width: 28px;
			height: 28px;
			padding: 0;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			border-radius: 4px;
			background-color: transparent;
			color: #6c757d;
			border: none;
			cursor: pointer;
			margin-left: 4px;
			transition: all 0.2s ease;
		}

		.btn-icon:hover {
			background-color: #f1f3f5;
			color: #212529;
		}

		.btn-icon:focus {
			outline: none;
			box-shadow: none;
		}

		/* Empty folder state */
		.empty-folder {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			padding: 60px 0;
			text-align: center;
		}

		.empty-folder i {
			font-size: 64px;
			color: #ced4da;
			margin-bottom: 16px;
		}

		.empty-folder p {
			font-size: 1.1rem;
			color: #6c757d;
			margin-bottom: 24px;
		}

		.empty-folder-actions {
			display: flex;
			gap: 12px;
		}

		/* Drag and Drop */
		.drag-over {
			border: 2px dashed #74c0fc !important;
			background-color: rgba(231, 245, 255, 0.6) !important;
		}

		#drop-overlay {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: rgba(248, 249, 250, 0.8);
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			z-index: 1000;
			display: none;
		}

		#drop-overlay i {
			font-size: 64px;
			color: #74c0fc;
			margin-bottom: 16px;
		}

		#drop-overlay p {
			font-size: 1.1rem;
			color: #495057;
		}

		/* File Upload */
		.upload-progress-modal .modal-body {
			max-height: 400px;
			overflow-y: auto;
		}

		.upload-progress-item {
			margin-bottom: 16px;
			border-radius: 8px;
			padding: 8px 12px;
			background-color: #f8f9fa;
		}

		.upload-progress-item[data-status="success"] {
			background-color: #edfbf3;
		}

		.upload-progress-item[data-status="error"] {
			background-color: #fbedee;
		}

		.progress-info {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 8px;
		}

		.progress-filename {
			font-weight: 500;
			font-size: 0.9rem;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			max-width: 80%;
		}

		.progress-percentage {
			font-size: 0.8rem;
			font-weight: 600;
		}

		.progress {
			height: 6px;
			margin-bottom: 4px;
		}

		.progress-status {
			display: flex;
			justify-content: flex-end;
			font-size: 0.8rem;
		}

		/* File Preview */
		.file-preview-modal .modal-body {
			padding: 0;
			position: relative;
		}

		#preview-container {
			min-height: 200px;
			max-height: 70vh;
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
			overflow: auto;
		}

		.preview-loading {
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
			padding: 40px;
			color: #6c757d;
		}

		.preview-loading i {
			font-size: 32px;
			margin-bottom: 16px;
		}

		.preview-error {
			display: flex;
			align-items: center;
			justify-content: center;
			flex-direction: column;
			padding: 40px;
			color: #dc3545;
		}

		.preview-error i {
			font-size: 32px;
			margin-bottom: 16px;
		}

		.preview-image {
			max-width: 100%;
			max-height: 70vh;
		}

		.pdf-container {
			width: 100%;
			height: 70vh;
		}

		.pdf-container iframe {
			width: 100%;
			height: 100%;
			border: none;
		}

		.text-preview {
			padding: 16px;
			background-color: #f8f9fa;
			white-space: pre-wrap;
			word-break: break-word;
			width: 100%;
			max-height: 70vh;
			overflow: auto;
			font-family: monospace;
			font-size: 0.9rem;
			color: #212529;
		}

		.no-preview {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			padding: 40px;
			text-align: center;
		}

		.no-preview .file-icon {
			font-size: 64px;
			color: #6c757d;
			margin-bottom: 16px;
		}

		.no-preview p {
			font-size: 1rem;
			color: #6c757d;
			margin-bottom: 24px;
		}

		/* File Sharing */
		.file-share-modal .user-list {
			max-height: 300px;
			overflow-y: auto;
		}

		.shared-user-item {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 12px;
			border-radius: 8px;
			background-color: #f8f9fa;
			margin-bottom: 8px;
		}

		.shared-user-info {
			display: flex;
			align-items: center;
		}

		.shared-user-avatar {
			width: 36px;
			height: 36px;
			border-radius: 50%;
			background-color: #e9ecef;
			display: flex;
			align-items: center;
			justify-content: center;
			margin-right: 12px;
			color: #6c757d;
			font-weight: 600;
		}

		.shared-user-name {
			font-weight: 500;
		}

		.shared-user-email {
			font-size: 0.8rem;
			color: #6c757d;
		}

		.shared-user-actions {
			display: flex;
			align-items: center;
		}

		.permission-select {
			margin-right: 12px;
		}

		/* Folder Creation */
		.create-folder-form .form-group {
			margin-bottom: 16px;
		}

		/* Context Menu */
		.context-menu {
			position: absolute;
			z-index: 1000;
			width: 200px;
			background-color: #fff;
			border-radius: 8px;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
			padding: 8px 0;
			display: none;
		}

		.context-menu-item {
			padding: 8px 16px;
			display: flex;
			align-items: center;
			cursor: pointer;
			color: #212529;
			transition: all 0.2s ease;
		}

		.context-menu-item:hover {
			background-color: #f1f3f5;
		}

		.context-menu-item i {
			margin-right: 8px;
			width: 18px;
			text-align: center;
			color: #6c757d;
		}

		.context-menu-divider {
			height: 1px;
			background-color: #e9ecef;
			margin: 4px 0;
		}

		/* Search */
		.search-container {
			position: relative;
			width: 100%;
			max-width: 400px;
		}

		.search-input {
			padding-right: 40px;
		}

		.search-icon {
			position: absolute;
			right: 12px;
			top: 50%;
			transform: translateY(-50%);
			color: #6c757d;
		}

		.search-loading {
			position: absolute;
			right: 12px;
			top: 50%;
			transform: translateY(-50%);
			color: #6c757d;
			display: none;
		}

		.search-results {
			position: absolute;
			top: 100%;
			left: 0;
			right: 0;
			background-color: #fff;
			border-radius: 8px;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
			z-index: 1000;
			max-height: 400px;
			overflow-y: auto;
			display: none;
		}

		.search-result-item {
			padding: 8px 12px;
			display: flex;
			align-items: center;
			cursor: pointer;
			transition: all 0.2s ease;
		}

		.search-result-item:hover {
			background-color: #f1f3f5;
		}

		.search-result-icon {
			margin-right: 8px;
			width: 24px;
			text-align: center;
			color: #6c757d;
		}

		.search-result-text {
			flex: 1;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.search-result-type {
			font-size: 0.8rem;
			color: #6c757d;
			margin-left: 8px;
		}

		/* Advanced Search */
		.advanced-search-form {
			padding: 16px;
			background-color: #f8f9fa;
			border-radius: 8px;
			margin-bottom: 16px;
		}

		.search-filters {
			display: flex;
			flex-wrap: wrap;
			gap: 12px;
		}

		.search-filter {
			flex: 1;
			min-width: 200px;
		}

		/* Notifications */
		.toast-container {
			position: fixed;
			bottom: 20px;
			right: 20px;
			z-index: 9999;
		}

		.toast {
			margin-bottom: 10px;
			min-width: 300px;
		}

		/* Animations */
		.folder-transition-in {
			animation: fadeIn 0.3s ease;
		}

		.folder-transition-out {
			animation: fadeOut 0.3s ease;
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
				transform: translateY(10px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		@keyframes fadeOut {
			from {
				opacity: 1;
				transform: translateY(0);
			}
			to {
				opacity: 0;
				transform: translateY(10px);
			}
		}

		/* File Drag Image */
		.file-drag-image {
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
			width: 40px;
			height: 40px;
		}

		.file-drag-image .file-icon {
			font-size: 32px;
			color: #6c757d;
		}

		.file-drag-image .badge {
			position: absolute;
			top: -5px;
			right: -5px;
		}

		/* Responsive Adjustments */
		@media (max-width: 992px) {
			.file-library-sidebar {
				width: 220px;
			}

			.file-grid {
				grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
			}
		}

		@media (max-width: 768px) {
			.file-library-main {
				flex-direction: column;
			}

			.file-library-sidebar {
				width: 100%;
				border-right: none;
				border-bottom: 1px solid #e9ecef;
				max-height: 200px;
				overflow-y: auto;
			}

			.file-grid {
				grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
			}

			.file-list .file-meta {
				display: none;
			}

			.file-list .file-item,
			.file-list .folder-item {
				padding: 8px 12px;
			}
		}

		@media (max-width: 576px) {
			.file-grid {
				grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
			}

			.file-grid .file-preview,
			.file-grid .folder-icon {
				height: 90px;
			}

			.file-grid .file-name,
			.file-grid .folder-name {
				font-size: 0.8rem;
			}

			.file-library-toolbar {
				flex-direction: column;
				align-items: flex-start;
			}

			.file-library-toolbar-right {
				margin-top: 8px;
				width: 100%;
				justify-content: space-between;
			}

			.search-container {
				max-width: 100%;
				margin-bottom: 8px;
			}
		}


		.preview-error {
			text-align: center;
			padding: 40px;
			color: #d9534f;
		}

		.image-preview {
			text-align: center;
			max-height: 60vh;
			overflow: auto;
		}

		.pdf-preview {
			height: 60vh;
		}

		.pdf-frame {
			width: 100%;
			height: 100%;
			border: 1px solid #ddd;
		}

		.text-preview {
			max-height: 60vh;
			overflow: auto;
			border: 1px solid #ddd;
			background: #f9f9f9;
		}

		.text-content {
			padding: 15px;
			white-space: pre-wrap;
			font-family: 'Courier New', Courier, monospace;
		}

		.no-preview-available, .generic-preview {
			text-align: center;
			padding: 30px;
		}

		.file-info-section {
			margin-top: 20px;
			border-top: 1px solid #ddd;
			padding-top: 15px;
		}

		.file-info-header {
			font-weight: bold;
			margin-bottom: 10px;
		}

		.file-info-table {
			width: 100%;
		}

		.file-info-table td {
			padding: 5px;
		}

		.file-info-table td:first-child {
			width: 120px;
			font-weight: bold;
		}

		/* Notification styles */
		#notification-container {
			position: fixed;
			top: 20px;
			right: 20px;
			z-index: 9999;
			max-width: 350px;
			width: 100%;
		}

		.notification {
			background-color: white;
			border-radius: 4px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
			margin-bottom: 10px;
			padding: 15px;
			display: flex;
			align-items: flex-start;
			opacity: 0;
			transform: translateX(50px);
			transition: opacity 0.3s, transform 0.3s;
		}

		.notification.show {
			opacity: 1;
			transform: translateX(0);
		}

		.notification.closing {
			opacity: 0;
			transform: translateX(50px);
		}

		.notification-icon {
			margin-right: 12px;
			font-size: 20px;
		}

		.notification-content {
			flex: 1;
			padding-right: 10px;
		}

		.notification-close {
			background: none;
			border: none;
			cursor: pointer;
			color: #999;
			padding: 0;
			margin-left: 10px;
			font-size: 14px;
		}

		.notification-close:hover {
			color: #555;
		}

		/* Notification types */
		.notification-success .notification-icon {
			color: #28a745;
		}

		.notification-error .notification-icon {
			color: #dc3545;
		}

		.notification-info .notification-icon {
			color: #17a2b8;
		}

		.notification-warning .notification-icon {
			color: #ffc107;
		}
		/* Loading overlay styles */
		#loading-overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.5);
			z-index: 9999;
			display: flex;
			justify-content: center;
			align-items: center;
			opacity: 0;
			visibility: hidden;
			transition: opacity 0.3s, visibility 0.3s;
		}

		#loading-overlay.show {
			opacity: 1;
			visibility: visible;
		}

		.loading-content {
			background-color: white;
			border-radius: 8px;
			padding: 30px;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
			text-align: center;
		}

		.loading-spinner {
			font-size: 40px;
			color: #007bff;
			margin-bottom: 15px;
		}

		.loading-message {
			font-size: 16px;
			color: #333;
		}
    </style>

<link href="<?php echo base_url('admin_assets')?>/css/style.css" rel="stylesheet">
	<link href="<?php echo base_url('admin_assets')?>/css/modern-theme.css" rel="stylesheet">
</head>

<body>
<div class="app is-primary">
	<div class="layout">
		<!-- Header START -->
		<div class="header" style="border:thin #24C16B solid;border-radius: 50px 50px 0px 0px;">
			<div class="logo logo-dark">
				<a href="<?php echo base_url('Admin')?>">
					<img src="<?php echo base_url('uploads/').$settings->logo?>" alt="Logo">
					<img class="logo-fold" src="<?php echo base_url('uploads/').$settings->logo?>" alt="Logo">
				</a>
			</div>
			<div class="logo logo-white">
				<a href="<?php echo base_url('Admin')?>">
					<img src="<?php echo base_url('uploads/').$settings->logo?>" alt="Logo" style="border-radius: 15px;">
					<img class="logo-fold" src="<?php echo base_url('uploads/').$settings->logo?>" alt="Logo">
				</a>
			</div>
			<div class="nav-wrap">
				<ul class="nav-left">
					<li class="desktop-toggle">
						<a href="javascript:void(0);">
							<i class="anticon"></i>
						</a>
					</li>
					<li class="mobile-toggle">
						<a href="javascript:void(0);">
							<i class="anticon"></i>
						</a>
					</li>

				</ul>
				<ul class="nav-right">
                    <h5 style="font-family:'fantasy';color: #0268bc; font-weight: bolder;background-color: #fff; padding: 0.5em;border-radius: 12px;" class="hidden-mobile">SESSION: <font color="#0268bc" style="text-underline: green;"><?php echo $this->session->userdata('Firstname')." ".$this->session->userdata('Lastname')."(".$this->session->userdata('RoleName').")"; ?></font> </h5>

                    <li class="dropdown dropdown-animated scale-left">
						<div class="pointer" data-toggle="dropdown">
							<div class="avatar avatar-image  m-h-10 m-r-15">
								<img src="<?php echo base_url('uploads/').$this->session->userdata('profile_photo')?>"  alt="">
							</div>
						</div>
						<div class="p-b-15 p-t-20 dropdown-menu pop-profile">
							<div class="p-h-20 p-b-15 m-b-10 border-bottom">
								<div class="d-flex m-r-50">
									<div class="avatar avatar-lg avatar-image">
										<img src="<?php echo base_url('uploads/').$this->session->userdata('profile_photo')?>" alt="">
									</div>
									<div class="m-l-10">
										<p class="m-b-0 text-dark font-weight-semibold"><?php echo $this->session->userdata('Firstname')." " .  $this->session->userdata('Lastname')?></p>
										<p class="m-b-0 opacity-07"><?php echo $this->session->userdata('Firstname') ?></p>
									</div>
								</div>
							</div>
							<a href="<?php  echo base_url('Employees/profile')?>" class="dropdown-item d-block p-h-15 p-v-10">
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<i class="anticon opacity-04 font-size-16 anticon-user"></i>
										<span class="m-l-10">Edit Profile</span>
									</div>
									<i class="anticon font-size-10 anticon-right"></i>
								</div>
							</a>

							<a href="<?php  echo base_url('auth/logout')?>" class="dropdown-item d-block p-h-15 p-v-10">
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<i class="anticon opacity-04 font-size-16 anticon-logout"></i>
										<span class="m-l-10">Logout</span>
									</div>
									<i class="anticon font-size-10 anticon-right"></i>
								</div>
							</a>
						</div>
					</li>

				</ul>
			</div>
		</div>
		<!-- Header END -->

		<!-- Side Nav START -->
		<div class="side-nav">
			<div class="side-nav-inner" style="border: thin solid #0268bc; border-radius: 50px 0px 50px 0px;">
                <br>

				<ul class="side-nav-menu scrollable">
					<li class="nav-item">
						<a  href="<?php echo base_url('Admin')?>">
                                <span class="icon-holder">
                                    <i class="bi bi-house"></i>
                                </span>
							<span class="title">Dashboard</span>
							<span class="arrow">
                                    <i class="arrow-icon"></i>
                                </span>
						</a>

					</li>


					<?= display_menu_admin(0, 1, $tg); ?>

					<li class="nav-item">
						<a  href="<?php echo base_url('files_controller')?>">
                                <span class="icon-holder">
                                    <i class="bi bi-folder"></i>
                                </span>
							<span class="title">File Library</span>
							<span class="arrow">
                                    <i class="arrow-icon"></i>
                                </span>
						</a>

					</li>

				</ul>
			</div>
		</div>
		<!-- Side Nav END -->
		<div class="page-container">
