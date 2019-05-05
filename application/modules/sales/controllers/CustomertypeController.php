<?php
class Sales_CustomertypeController extends Zend_Controller_Action
{
    public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    }
	
	public function indexAction()
    {
    	try{
    		$db = new Sales_Model_DbTable_DbCustomer();
    		if($this->getRequest()->isPost()){
    			$search = $this->getRequest()->getPost();
    		}
    		else{
    			$search = array(
    					'adv_search' => '',
    					'status_search' => '',
    					'type' => ''
    			);
    		}
    		$rs_rows= $db->getCustomerType($search);//call frome model
    		$this->view->rs = $rs_rows;
    	}catch (Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$fm = new Product_Form_FrmOther();
    	$frm = $fm->search();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->Form = $frm;
    }
    public function addAction()
    {
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$db = new Sales_Model_DbTable_DbCustomer();
    		try {
    			$db->addCustomerType($data);
    			Application_Form_FrmMessage::message('INSERT_SUCCESS');
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message("INSERT_FAIL");
    			$err = $e->getMessage();
    			Application_Model_DbTable_DbUserLog::writeMessageError($err);
    		}
    	}
    	$fm = new Sales_Form_FrmCustomerType();
    	$frm = $fm->add();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->Form = $frm;
    }

	public function editAction()
    {
    	$id = $this->getRequest()->getParam("id");
    	$db = new Sales_Model_DbTable_DbCustomer();
    	if($this->getRequest()->isPost()){
    		$data=$this->getRequest()->getPost();
    		$data["id"] = $id;
    		try {
    			$db->editCustomerType($data);
    			Application_Form_FrmMessage::Sucessfull('EDIT_SUCCESS', '/sales/customertype');
    		} catch (Exception $e) {
    			Application_Form_FrmMessage::message("EDIT_FAIL");
    			$err = $e->getMessage();
    			Application_Model_DbTable_DbUserLog::writeMessageError($err);
    		}
    	}
    	$rs = $db->getCustomerTypeId($id);
    	$fm = new Sales_Form_FrmCustomerType();
    	$frm = $fm->add($rs);
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->Form = $frm;
    }
	
	public function addCustomerAction(){
		if($this->getRequest()->isPost()){
			try {
			$post=$this->getRequest()->getPost();
			$add_customer = new Sales_Model_DbTable_DbCustomer();
			$customer_id = $add_customer->addNewCustomer($post);
			$result = array('cus_id'=>$customer_id);
			echo Zend_Json::encode($result);
			exit();
			}catch (Exception $e){
				$result = array('err'=>$e->getMessage());
				echo Zend_Json::encode($result);
				exit();
			}
		}
	}
	public function deleteCustomerAction() {
		$id = ($this->getRequest()->getParam('id'));
		$sql = "DELETE FROM tb_customer WHERE customer_id IN ($id)";
		$deleteObj = new Application_Model_DbTable_DbGlobal();
		$deleteObj->deleteRecords($sql);
		$this->_redirect('/sales/customer/index');
	}
	
	public function getCuCodeAction(){//dynamic by customer
	
		$post=$this->getRequest()->getPost();
		$get_code = new Sales_Model_DbTable_DbCustomer();
		$result = $get_code->getCustomerCode($post["id"]);
		echo Zend_Json::encode($result);
		exit();
	}
	
	function getcustomerlimitAction(){
		$post=$this->getRequest()->getPost();
		$get_code = new Sales_Model_DbTable_DbCustomer();
		$result = $get_code->getCustomerLimit($post["id"]);
		echo Zend_Json::encode($result);
		exit();
	}
	function getCustomerinfoAction(){
		$post=$this->getRequest()->getPost();
		$get_code = new Sales_Model_DbTable_DbCustomer();
		$result = $get_code->getCustomerinfo($post["customer_id"]);
		echo Zend_Json::encode($result);
		exit();
	}
}