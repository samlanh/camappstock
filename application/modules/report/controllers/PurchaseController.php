<?php
class Report_PurchaseController extends Zend_Controller_Action
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
    public function rptPurchaseAction()//purchase report
    {
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    				'text_search'=>'',
    				'start_date'=>date("Y-m-d"),
    				'end_date'=>date("Y-m-d"),
    				'suppliyer_id'=>0,
    				'branch_id'=>-1,
    				'status_paid'=>-1,
					'saleagent_id'=>-1
    		);
    	}
    	$this->view->rssearch = $data;
    	$query = new report_Model_DbQuery();
    	$this->view->repurchase =  $query->getAllPurchaseReport($data);
    	$frm = new Application_Form_FrmReport();
    
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->form_purchase = $formFilter;
    	Application_Model_Decorator::removeAllDecorator($formFilter);
    	$this->view->getHeader = $formFilter->getHeaderAction();
    	$this->view->getFooter = $formFilter->getFooterAction();
    }
    public function rptPurchasevendorAction()//purchase report
    {
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    				'text_search'=>'',
    				'start_date'=>date("Y-m-d"),
    				'end_date'=>date("Y-m-d"),
    				'suppliyer_id'=>0,
    				'branch_id'=>-1,
    				'status_paid'=>-1,
    				'saleagent_id'=>-1
    		);
    	}
    	$this->view->rssearch = $data;
    	$query = new report_Model_DbQuery();
    	$this->view->repurchase =  $query->getAllPurchasebySupplier($data);
    	$frm = new Application_Form_FrmReport();
    
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->form_purchase = $formFilter;
    	Application_Model_Decorator::removeAllDecorator($formFilter);
    	$this->view->getHeader = $formFilter->getHeaderAction();
    	$this->view->getFooter = $formFilter->getFooterAction();
    }
    function rptPurchaseitemAction(){
    	if($this->getRequest()->isPost()){
    		$search = $this->getRequest()->getPost();
    	}else{
    		$search = array(
    				'txt_search'=>'',
    				'start_date'=>date("Y-m-d"),
    				'end_date'=>date("Y-m-d"),
    				'item'=>0,
    				'suppliyer_id'=>-1,
    				'category_id'=>0,
    				'brand_id'=>0,
    				'branch_id'=>0,
    		);
    	}
    	$this->view->rssearch=$search;
    	$query = new report_Model_DbQuery();
    	$this->view->product_rs =  $query->getPruchaseProductDetail($search);
    	
    	$frm = new Application_Form_FrmReport();
    	$form_search=$frm->productDetailReport($search);
    	Application_Model_Decorator::removeAllDecorator($form_search);
    	$this->view->form_search = $form_search;
    	
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->getHeader = $formFilter->getHeaderAction();
    	$this->view->getFooter = $formFilter->getFooterAction();
    }
    function purproductdetailAction(){
    	$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
    	if(empty($id)){
    		$this->_redirect("/report/index/rpt-purchase");
    	}
    	$query = new report_Model_DbQuery();
    	$this->view->product =  $query->getProductPruchaseById($id);
    }
    public function rptVandorbalanceAction()//purchase report
    {
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    				'text_search'=>'',
    				'start_date'=>date("Y-m-d"),
    				'end_date'=>date("Y-m-d"),
    				'suppliyer_id'=>0,
    				'branch_id'=>0,
    		);
    	}
    	$this->view->rssearch = $data;
    	$query = new report_Model_DbQuery();
    	$this->view->repurchase =  $query->getVendorBalance($data);
    	$frm = new Application_Form_FrmReport();
    
    	$form_search=$frm->FrmReportPurchase($data);
    	Application_Model_Decorator::removeAllDecorator($form_search);
    	$this->view->form_purchase = $form_search;
    	 
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->getHeader = $formFilter->getHeaderAction();
    	$this->view->getFooter = $formFilter->getFooterAction();
    }
    public function rptQuotationissueAction()//purchase report
    {
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$data['start_date']=date("Y-m-d",strtotime($data['start_date']));
    		$data['end_date']=date("Y-m-d",strtotime($data['end_date']));
    	}else{
    		$data = array(
    				'text_search'=>'',
    				'start_date'=>date("Y-m-d"),
    				'end_date'=>date("Y-m-d"),
    				'suppliyer_id'=>0,
    				'branch_id'=>0,
    				'customer_id'=>0,
    		);
    	}
    	$this->view->rssearch = $data;
    	$query = new report_Model_DbQuery();
    	$this->view->repurchase =  $query->getAllQuotation($data);
    	$frm = new Application_Form_FrmReport();
    
    	$formFilter = new Sales_Form_FrmSearch();
    	$this->view->form_purchase = $formFilter;
    	Application_Model_Decorator::removeAllDecorator($formFilter);
    	 
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->getHeader = $formFilter->getHeaderAction();
    	$this->view->getFooter = $formFilter->getFooterAction();
    }
    public function quotadetailAction(){
    	$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
    	if(empty($id)){
    		$this->_redirect("/report/index/rpt-quotationissue");
    	}
    	$query = new report_Model_DbQuery();
    	$this->view->product =  $query->getQuotationById($id);
    	$rs = $query->getQuotationById($id);
    	if(empty($rs)){
    		$this->_redirect("/report/index/rpt-sales");
    	}
    	$db= new Application_Model_DbTable_DbGlobal();
    	$this->view->rscondition = $db->getTermConditionById(1, $id);
    }
    public function rptExpenseAction(){
    	try{
    		if($this->getRequest()->isPost()){
    			$search=$this->getRequest()->getPost();
    		}
    		else{
    			$search = array(
    					"adv_search"=>'',
    					"branch_id"=>-1,
    					'title'=>-1,
    					"status"=>-1,
    					'start_date'=> date('Y-m-d'),
    					'end_date'=>date('Y-m-d'),
    			);
    		}
    		$db = new report_Model_DbQuery();
    		$this->view->expense = $db->getAllExpense($search);
    		$this->view->expense_po = $db->getAllExpensePurchaseNoSum($search);
    		$this->view->search = $search;
    
    	}catch(Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->formFilter = $formFilter;
    	Application_Model_Decorator::removeAllDecorator($formFilter);
    
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->getHeader = $formFilter->getHeaderAction();
    	$this->view->getFooter = $formFilter->getFooterAction();
    
    }
    public function rptExpensebytypeAction(){
    	try{
    		if($this->getRequest()->isPost()){
    			$search=$this->getRequest()->getPost();
    		}
    		else{
    			$search = array(
    					"adv_search"=>'',
    					"branch_id"=>-1,
    					'title'=>-1,
    					"status"=>-1,
    					'start_date'=> date('Y-m-d'),
    					'end_date'=>date('Y-m-d'),
    			);
    		}
    		$db = new report_Model_DbQuery();
    		$this->view->expense = $db->getAllExpenseType($search);
    		$this->view->expense_po = $db->getAllExpensePurchase($search);
    		$this->view->search = $search;
    
    	}catch(Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->formFilter = $formFilter;
    	Application_Model_Decorator::removeAllDecorator($formFilter);
    	 
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->getHeader = $formFilter->getHeaderAction();
    	$this->view->getFooter = $formFilter->getFooterAction();
    }
   
}