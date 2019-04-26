<?php
class report_ProductController extends Zend_Controller_Action
{
	
    public function init()
    {
        /* Initialize action controller here */
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    }
    protected function GetuserInfo(){
    	$user_info = new Application_Model_DbTable_DbGetUserInfo();
    	$result = $user_info->getUserInfo();
    	return $result;
    }
    public function indexAction()
    {
    }
    public function rptproductlistAction()
    {
    	$db = new report_Model_DbProduct();
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    				'ad_search'	=>	'',
    				'branch'	=>	'',
    				'brand'		=>	'',
    				'category'	=>	'',
    				'model'		=>	-1,
    				'color'		=>	'',
    				'size'		=>	'',
    				'status'	=>	1,
    				'status_qty'=>-1
    		);
    	}
    	$this->view->product = $db->getAllProduct($data);
    	$formFilter = new Product_Form_FrmProduct();
    	$this->view->formFilter = $formFilter->productFilter();
    	Application_Model_Decorator::removeAllDecorator($formFilter);
    	
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->getHeader = $formFilter->getHeaderAction();
    	$this->view->getFooter = $formFilter->getFooterAction();
    }
	
    public function rptadjuststockAction()
    {
    	$db = new report_Model_DbProduct();
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    				'ad_search'	=>	'',
    				'pro_id'	=>	'',
    		);
    	}
    	$this->view->product = $db->getAllAdjustStock($data);
    	$formFilter = new Product_Form_FrmProduct();
    	$this->view->formFilter = $formFilter->productFilter();
    	Application_Model_Decorator::removeAllDecorator($formFilter);
    	
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->getHeader = $formFilter->getHeaderAction();
    	$this->view->getFooter = $formFilter->getFooterAction();
    
    }
    public function rpttransferAction()
    {
    	$db = new Product_Model_DbTable_DbTransfer();
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    			'tran_num'	=>	'',
    			'tran_date'	=>	date("d-m-Y"),
    			'type'		=>	'',
    			'status'	=>	1,
    			'to_loc'	=>	'',
    		);
    	}
    	$this->view->product = $db->getTransfer($data);
    	$formFilter = new Product_Form_FrmTransfer();
    	$this->view->formFilter = $formFilter->frmFilter();
    	Application_Model_Decorator::removeAllDecorator($formFilter); 
    }
    function showbarcodeAction(){
    	$id = ($this->getRequest()->getParam('id'));
    	$sql ="SELECT id,barcode,item_name,cate_id,
			((SELECT name FROM `tb_category` WHERE id=cate_id)) as cate_name
    	FROM `tb_product` WHERE id IN (".$id.")";
    	$db = new Application_Model_DbTable_DbGlobal();
//     	print_r($db->getGlobalDb($sql));
    	$this->view->rsproduct = $db->getGlobalDb($sql);
    }
    public function generateBarcodeAction(){
    	$loan_code = $this->getRequest()->getParam('pro_code');
    	header('Content-type: image/png');
    	$this->_helper->layout()->disableLayout();
    	//$barcodeOptions = array('text' => "$_itemcode",'barHeight' => 30);
    	$barcodeOptions = array('text' => "$loan_code",'barHeight' => 40);
    	//'font' => 4(set size of label),//'barHeight' => 40//set height of img barcode
    	$rendererOptions = array();
    	$renderer = Zend_Barcode::factory(
    			'Code128', 'image', $barcodeOptions, $rendererOptions
    	)->render();
    
    }
    public function sochartAction(){
    	$_db = new report_Model_DbQuery();
    	$_rows=$_db->getTopTenProductSO();$_arr="";
    	foreach ($_rows As $i =>$row){
    		if($i==count($_rows)-1){
    			$_arr.= "['".$row["item_name"]."',".$row["qty"]."]";
    		}
    		else{
    			$_arr.= "['".$row["item_name"]."',".$row["qty"]."],";
    		}
    	}
//     	print_r($_arr);exit();
    	$this->view->top_product = $_arr;
    
    }
    public function sochartbydateAction(){
    	$data = $this->getRequest()->getPost();
    	if(strtotime($end_date)+86400 > strtotime($start_date)) {
    		$_db = new report_Model_DbQuery();
    		$_rows=$_db->getTopTenProductSOByDate();
    		$_arr="";
    		$this->view->getsales_item = $getSaleItem;
    		$this->view->start_date = $start_date;
    		$this->view->end_date = $end_date;
    		if(!empty($data["LocationId"])){
    			$brand=$query->getLocationName($data["LocationId"]);
    			$this->view-> branch = $brand;
    		}
    	}
    	else {
    		Application_Form_FrmMessage::message("End Date Must Greater Then Start Date");
    	}
    	if(!empty($brand)){
    		$this->view->branch = $brand;
    	}
    
    
    
    
    	// 		$_db = new report_Model_DbQuery();
    	// 		$_rows=$_db->getTopTenProductSOByDate();
    	// 		$_arr="";
    	// 		foreach ($_rows As $i =>$row){
    	// 			if($i==count($_rows)-1){
    	// 				$_arr.= "['".$row["item_name"]."',".$row["qty"]."]";
    	// 			}
    	// 			else{
    	// 				$_arr.= "['".$row["item_name"]."',".$row["qty"]."],";
    	// 			}
    	// 		}
    	$this->view->top_product = $_arr;
    	$frm = new Application_Form_FrmReport();
    	$form_search=$frm->salseReport();
    	Application_Model_Decorator::removeAllDecorator($form_search);
    	$this->view->form_transfer = $form_search;
    }
    public function pochartAction(){
    	$_db = new report_Model_DbQuery();
    	$_rows=$_db->getTopTenProductPO();
    	$_arr="";
    	foreach ($_rows As $i =>$row){
    		if($i==count($_rows)-1){
    			$_arr.= "['".$row["item_name"]."',".$row["qty"]."]";
    		}
    		else{
    			$_arr.= "['".$row["item_name"]."',".$row["qty"]."],";    				
    		}
    	}
    	$this->view->top_product = $_arr;
    }
    public function pochartdateAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    
    		$start_date = $data["start_date"];
    		$end_date = $data["end_date"];
    
    		if(strtotime($end_date)+86400 > strtotime($start_date)) {
    			$query = new report_Model_DbQuery();
    			$getSaleItem = $query->getQtyTransfer($data);
    			$this->view->getsales_item = $getSaleItem;
    			$this->view->start_date = $start_date;
    			$this->view->end_date = $end_date;
    			if(!empty($data["LocationId"])){
    				$brand=$query->getLocationName($data["LocationId"]);
    				$this->view-> branch = $brand;
    			}
    		}
    		else {
    			Application_Form_FrmMessage::message("End Date Must Greater Then Start Date");
    		}
    	}
    	$dbuser = new report_Model_DbQuery();
    	$brand=$dbuser->getBrandByUser();
    	if(!empty($brand)){
    		$this->view->branch = $brand;
    	}
    	$data = $this->getRequest()->getPost();
    	$_db = new report_Model_DbQuery();
    	$_rows=$_db->getTopTenProductSO();$_arr="";
    	foreach ($_rows As $i =>$row){
    		if($i==count($_rows)-1){
    			$_arr.= "['".$row["item_name"]."',".$row["qty"]."]";
    		}
    		else{
    			$_arr.= "['".$row["item_name"]."',".$row["qty"]."],";
    		}
    	}
    	$this->view->top_product = $_arr;
    	$frm = new Application_Form_FrmReport();
    	$form_search=$frm->salseReport();
    	Application_Model_Decorator::removeAllDecorator($form_search);
    	$this->view->form_transfer = $form_search;
    }	
    public function rptSummaryAction()
    {
    	$data = null;
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    
    		$start_date = $data["start_date"];
    		$end_date = $data["end_date"];
    			
    		$query = new report_Model_DbQuery();
    		$geProducttQty = $query->geProducttQty($data);
    		$this->view->get_product_qty = $geProducttQty;
    			
    		$this->view->start_date = $start_date;
    		$this->view->end_date = $end_date;
    			
    		if(!empty($data["LocationId"])){
    			$brand=$query->getLocationName($data["LocationId"]);
    			$this->view-> branch = $brand;
    		}
    	}
    	$dbuser = new report_Model_DbQuery();
    	$brand=$dbuser->getBrandByUser();
    	if(!empty($brand)){
    		$this->view->branch = $brand;
    	}
    
    	$frm = new Application_Form_FrmReport();
    	$form_search=$frm->productDetailReport($data);
    	Application_Model_Decorator::removeAllDecorator($form_search);
    	$this->view->form_product = $form_search;
    	// 		if($this->getRequest()->isPost()){
    	// 			$data = $this->getRequest()->getPost();
    
    	// 			$start_date = $data["start_date"];
    	// 			$end_date = $data["end_date"];
    
    	// 			if(strtotime($end_date)+86400 > strtotime($start_date)) {
    	// 				$query = new report_Model_DbQuery();
    	// 				//$vendor_sql .= " AND p.date_order BETWEEN '$start_date' AND '$end_date'";
    	// 				$getSaleItem = $query->getProductSummary($data);
    	// 				$this->view->getsales_item = $getSaleItem;
    	// 				$this->view->start_date = $start_date;
    	// 				$this->view->end_date = $end_date;
    	// 				if(!empty($data["LocationId"])){
    	// 					$branch=$query->getLocationName($data["LocationId"]);
    	// 					$this->view-> branch = $branch;
    	// 				}
    	// 			}
    	// 			else {
    	// 				Application_Form_FrmMessage::message("End Date Must Greater Then Start Date");
    	// 			}
    	// 		}
    	// 		$user = $this->GetuserInfo();
    	// 		if($user["level"]!=1 AND $user["level"]!=2){
    	// 			$this->_redirect("/default/index/home");
    
    	// 		}
    	// 		$frm = new Application_Form_FrmReport();
    	// 		$form_search=$frm->productDetailReport($data);
    	// 		Application_Model_Decorator::removeAllDecorator($form_search);
    	// 		$this->view->form_salse = $form_search;
    
    }
}