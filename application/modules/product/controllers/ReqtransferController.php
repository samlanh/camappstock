<?php
class Product_ReqtransferController extends Zend_Controller_Action
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
	function indexAction(){
		try{
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
    	}catch (Exception $e){
    		Application_Form_FrmMessage::messageError("INSERT_ERROR");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$formFilter = new Product_Form_FrmTransfer();
    	$this->view->formFilter = $formFilter->frmFilter();
    	Application_Model_Decorator::removeAllDecorator($formFilter);
	}
	function addAction(){
			if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$db = new Product_Model_DbTable_DbTransfer();
					$db->addRequest($post);
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR");
    				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				  }
			}
			$formProduct = new Product_Form_FrmTransfer();
			$formStockAdd = $formProduct->addRequest(null);
			Application_Model_Decorator::removeAllDecorator($formStockAdd);
			$this->view->formFilter = $formStockAdd;
	}
	function editAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Product_Model_DbTable_DbTransfer();
			if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$post["id"] = $id;
					$db->editRequest($post);
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
					Application_Form_FrmMessage::redirectUrl('/product/reqtransfer');
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR");
    				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				  }
			}
			$row = $db->getReqTransferById($id);
			$this->view->rs_detail = $db->getReqTransferDetail($id);
			$formProduct = new Product_Form_FrmTransfer();
			$formStockAdd = $formProduct->addRequest($row);
			Application_Model_Decorator::removeAllDecorator($formStockAdd);
			$this->view->formFilter = $formStockAdd;
	}
	public function viewAction(){
		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
		if(empty($id)){
			$this->_redirect("/report/index/rpt-purchase");
		}
		$query = new Product_Model_DbTable_DbTransfer();
		$rs = $query->getRequestPrint($id);
		$this->view->product =  $query->getRequestPrint($id);	
		$this->view->title_reprot = $query->getTitleReport($rs[0]["cur_location"]);
	}
}