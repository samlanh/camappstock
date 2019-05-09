<?php
class Product_transferController extends Zend_Controller_Action
{
public function init()
    {
        /* Initialize action controller here */
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }
    protected function GetuserInfoAction(){
    	$user_info = new Application_Model_DbTable_DbGetUserInfo();
    	$result = $user_info->getUserInfo();
    	return $result;
    }
    public function indexAction()
    {
    	try{
	    	$db = new Product_Model_DbTable_DbTransfer();
	    	if($this->getRequest()->isPost()){
	    		$data = $this->getRequest()->getPost();
	    	}else{
	    		$data = array(
	    			'avd_search'	=>	'',
	    			'start_date'	=>	date("Y-m-d"),
	    			'end_date'		=>	date("Y-m-d"),
	    			'status'	=>	1,
	    		);
	    	}
	    	$rows = $db->getTransfer($data);
	    	
	    	$columns=array("TRANSFER_NUM","FROM_BRANCH","TO_BRANCH","TRANSFER_DATE","TRANSFER_BY","STATUS");
	    	$link=array(
	    			'module'=>'product','controller'=>'transfer','action'=>'edit',
	    	);
	    	$list = new Application_Form_Frmlist();
	    	$this->view->list=$list->getCheckList(0, $columns, $rows,array('tran_no'=>$link,'transfer_date'=>$link,'cur_location'=>$link,'tran_location'=>$link));
	    	
    	}catch (Exception $e){
    		Application_Form_FrmMessage::messageError("INSERT_ERROR");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$formFilter = new Product_Form_FrmTransfer();
    	$this->view->formFilter = $formFilter->frmFilter();
    	Application_Model_Decorator::removeAllDecorator($formFilter);
	}
	public function addAction()
	{
		$db = new Product_Model_DbTable_DbTransfer();
			if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$db->add($post);
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
					Application_Form_FrmMessage::redirectUrl('/product/transfer');
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR");
				  	Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				  }
			}
			$formProduct = new Product_Form_FrmTransfer();
			$formStockAdd = $formProduct->add(null);
			Application_Model_Decorator::removeAllDecorator($formStockAdd);
			$this->view->formFilter = $formStockAdd;
	}
	public function editAction()
	{
		$id = $this->getRequest()->getParam("id");
		$db = new Product_Model_DbTable_DbTransfer();
			if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$db->edit($post);
					if(isset($post["save_close"]))
					{
						Application_Form_FrmMessage::message("INSERT_SUCCESS");
						Application_Form_FrmMessage::redirectUrl('/product/transfer');
					}
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR",$err = $e->getMessage());
				  }
			}
			
			$rs = $db->getTransferById($id);
			$rs_detail = $db->getTransferDettail($id);
			$this->view->rs_detail = $rs_detail;
			$formProduct = new Product_Form_FrmTransfer();
			$formStockAdd = $formProduct->add($rs);
			Application_Model_Decorator::removeAllDecorator($formStockAdd);
			$this->view->formFilter = $formStockAdd;
	}
	function requestapprAction(){
		$db = new Product_Model_DbTable_DbTransfer();
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    			'avd_search'	=>	'',
    			'start_date'	=>	date("d-m-Y"),
    			'end_date'		=>	date("d-m-Y"),
    			'status'		=>	1,
    			'branch'		=>	-1,
    		);
    	}
    	$this->view->product = $db->getRequestTransfer($data);
    	$formFilter = new Product_Form_FrmTransfer();
    	$this->view->formFilter = $formFilter->frmFilter();
    	Application_Model_Decorator::removeAllDecorator($formFilter);
}
	function addrequestapprAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Product_Model_DbTable_DbTransfer();
			if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$post["id"] = $id;
					$db->ApproveRequest($post);
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
					Application_Form_FrmMessage::redirectUrl('/product/transfer/requestappr/');
					
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR",$err = $e->getMessage());
				  }
			}
			$this->view->rs= $db->getReqTransferById($id);
			$this->view->rs_detail = $db->getReqTransferDetail($id);
	}
	
	function receiverequestAction(){
		$db = new Product_Model_DbTable_DbTransfer();
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    			'avd_search'	=>	'',
    			'start_date'	=>	date("d-m-Y"),
    			'end_date'		=>	date("d-m-Y"),
    			'status'		=>	1,
    			'branch'		=>	-1,
    		);
    	}
    	$this->view->product = $db->getRequestTransfer($data);
    	$formFilter = new Product_Form_FrmTransfer();
    	$this->view->formFilter = $formFilter->frmFilter();
    	Application_Model_Decorator::removeAllDecorator($formFilter);
	}
	function addreceiveAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Product_Model_DbTable_DbTransfer();
			if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$post["id"] = $id;
					$db->ReceiveTransfer($post);
					if(isset($post["save_close"]))
					{
						Application_Form_FrmMessage::message("INSERT_SUCCESS");
						Application_Form_FrmMessage::redirectUrl('/product/transfer/receiverequest');
					}
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR",$err = $e->getMessage());
				  }
			}
			
		$rs = $db->getTransferById($id);
		//print_r($rs);
		$rs_detail = $db->getTransferDettail($id);
		$this->view->rs_detail = $rs_detail;
		$formProduct = new Product_Form_FrmReceive();
		$formStockAdd = $formProduct->makeTransfers($rs);
		Application_Model_Decorator::removeAllDecorator($formStockAdd);
		$this->view->formFilter = $formStockAdd;
	}
	function editreceiveAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Product_Model_DbTable_DbTransfer();
			if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$post["id"] = $id;
					$db->editReceiveTransfer($post);
					
					if(isset($post["save_close"]))
					{
						Application_Form_FrmMessage::message("INSERT_SUCCESS");
						Application_Form_FrmMessage::redirectUrl('/product/transfer/receiverequest');
					}else{
						Application_Form_FrmMessage::message("INSERT_SUCCESS");
						Application_Form_FrmMessage::redirectUrl('/product/transfer/receiverequest');
					}
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR",$err = $e->getMessage());
				  }
			}
			
		$rs = $db->getReceiveTransferById($id);
		$rs_detail = $db->getReceiveTransferDetail($id);
		$this->view->rs_detail = $rs_detail;
		$formProduct = new Product_Form_FrmReceive();
		$formStockAdd = $formProduct->makeTransfers($rs);
		Application_Model_Decorator::removeAllDecorator($formStockAdd);
		$this->view->formFilter = $formStockAdd;
	}
	

	public function viewtransferAction(){
		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
    	if(empty($id)){
    		$this->_redirect("/report/index/rpt-purchase");
    	}
    	$query = new Product_Model_DbTable_DbTransfer();
		$rs = $query->getRequestPrint($id);
    	$this->view->product =  $query->getTransferPrint($id);
		$this->view->title_reprot = $query->getTitleReport($rs[0]["cur_location"]);
	}	
	
	public function viewreceiveAction(){
		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
    	if(empty($id)){
    		$this->_redirect("/report/index/rpt-purchase");
    	}
    	$query = new Product_Model_DbTable_DbTransfer();
		$rs = $query->getReceiveById($id);
    	$this->view->product =  $query->getReceiveById($id);
// 		print_r($rs);exit();
		$this->view->title_reprot = $query->getTitleReport($rs[0]["cu_loc"]);
	}	
	
	public function getRequestTransferNoAction(){
		if($this->getRequest()->isPost()){
			try {
				$post=$this->getRequest()->getPost();
				$db = new Product_Model_DbTable_DbTransfer();
				$no =$db->getRequestTransferNo($post["id"]);
				echo Zend_Json::encode($no);
				exit();
			}catch (Exception $e){
				$result = array('err'=>$e->getMessage());
				echo Zend_Json::encode($result);
				exit();
			}
		}
	}
	
	public function getTransferNoAction(){
		if($this->getRequest()->isPost()){
			try {
				$post=$this->getRequest()->getPost();
				$db = new Product_Model_DbTable_DbTransfer();
				$no =$db->getTransferNo($post["id"]);
				echo Zend_Json::encode($no);
				exit();
			}catch (Exception $e){
				$result = array('err'=>$e->getMessage());
				echo Zend_Json::encode($result);
				exit();
			}
		}
	}
	
	public function getproductAction(){
		if($this->getRequest()->isPost()) {
			$db = new Product_Model_DbTable_DbTransfer();
			$data = $this->getRequest()->getPost();
			$rs = $db->getProductQtyById($data["id"]);
			echo Zend_Json::encode($rs);
			exit();
		}
	}
}

