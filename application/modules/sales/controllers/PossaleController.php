<?php
class Sales_PossaleController extends Zend_Controller_Action
{	
    public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    }
    protected function GetuserInfoAction(){
    	$user_info = new Application_Model_DbTable_DbGetUserInfo();
    	$result = $user_info->getUserInfo();
    	return $result;
    }
   	public function indexAction()
	{
		$db = new Sales_Model_DbTable_Dbpos();
		if($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost();
			try {
				if(!empty($data['identity'])){
					$db->addSaleOrder($data);
				}
				Application_Form_FrmMessage::message("INSERT_SUCESS");
			}catch (Exception $e){
				Application_Form_FrmMessage::message('INSERT_FAIL');
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
 		$db = new Sales_Model_DbTable_Dbpos();
		$this->view->rsproduct = $db->getAllProductName();
		$this->view->rscustomer = $db->getAllCustomerName();
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->term_opt = $db->getAllTermCondition();
		
		$formpopup = new Sales_Form_FrmCustomer(null);
		$formpopup = $formpopup->Formcustomer(null);
		Application_Model_Decorator::removeAllDecorator($formpopup);
		$this->view->form_customer = $formpopup;
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->invoice = $db->getSalesNumber(1);
		
		$db = new Sales_Model_DbTable_Dbexchangerate();
		$this->view->rsrate= $db->getExchangeRate();
	}
	
	public function addAction()
	{
		$db = new Sales_Model_DbTable_Dbpos();
		if($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost();
			try {
				if(!empty($data['identity'])){
					$db->addSaleOrder($data);
				}
				Application_Form_FrmMessage::message("INSERT_SUCESS");
			}catch (Exception $e){
				Application_Form_FrmMessage::message('INSERT_FAIL');
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db = new Sales_Model_DbTable_Dbpos();
		$this->view->rsproduct = $db->getAllProductName();
		$this->view->rscustomer = $db->getAllCustomerName();
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->term_opt = $db->getAllTermCondition();
	
		$formpopup = new Sales_Form_FrmCustomer(null);
		$formpopup = $formpopup->Formcustomer(null);
		Application_Model_Decorator::removeAllDecorator($formpopup);
		$this->view->form_customer = $formpopup;
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->invoice = $db->getSalesNumber(1);
	
		$db = new Sales_Model_DbTable_Dbexchangerate();
		$this->view->rsrate= $db->getExchangeRate();
	}
	
	public function editAction()
	{
		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
		if(empty($id)){
			$this->_redirect("/sales/index");
		}
		$query = new Sales_Model_DbTable_Dbpos();
		
		$db = new Sales_Model_DbTable_Dbpos();
		if($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost();
			try {
				if(!empty($data['identity'])){
					$db->editSale($data);
				}
				Application_Form_FrmMessage::message("UPDATE_SUCESS");
			}catch (Exception $e){
				Application_Form_FrmMessage::message('INSERT_FAIL');
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$db = new Sales_Model_DbTable_Dbpos();
		$this->view->rsproduct = $db->getAllProductName();
		$this->view->rscustomer = $db->getAllCustomerName();
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->term_opt = $db->getAllTermCondition();
	
		$formpopup = new Sales_Form_FrmCustomer(null);
		$formpopup = $formpopup->Formcustomer(null);
		Application_Model_Decorator::removeAllDecorator($formpopup);
		$this->view->form_customer = $formpopup;
		$db = new Application_Model_DbTable_DbGlobal();
		$this->view->invoice = $db->getSalesNumber(1);
		
		$query = new Sales_Model_DbTable_Dbpos();
		$rs = $query->getInvoiceById($id);
		$this->view->rs = $rs;
		$this->view->rsdetail =  $query->getInvoiceDetailById($id);
		if(empty($rs)){
			$this->_redirect("/sales/index");
		}
	}
	public function deleteAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Sales_Model_DbTable_Dbpos();
		echo "<script language='javascript'>
		var txt;
		var r = confirm('áž�áž¾áž›áŸ„áž€áž¢áŸ’áž“áž€áž–áž·áž�áž…áž„áŸ‹áž›áž»áž”ážœáž·áž€áŸ’áž€áž™áž”áž�áŸ’ážšáž“áŸ�áŸ‡áž«!');
		if (r == true) {";
			//$db->deleteSale($id);
			echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/sales/possale/deleteitem/id/".$id."'";
		echo"}";
		echo"else {";
			echo "window.location ='".Zend_Controller_Front::getInstance()->getBaseUrl()."/sales/index/'";
		echo"}
		</script>";
	}
	function deleteitemAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Sales_Model_DbTable_Dbpos();
		$db->deleteSale($id);
		$this->_redirect("sales/index");
	}
	function invoiceAction(){
		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
		if(empty($id)){
			$this->_redirect("/sales/index");
		}
		$query = new Sales_Model_DbTable_Dbpos();
		$rs = $query->getInvoiceById($id);
		$this->view->rs = $rs;
		$this->view->rsdetail =  $query->getInvoiceDetailById($id);
		if(empty($rs)){
			$this->_redirect("/sales/");
		}
	}		
	function getproductAction(){
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$db = new Sales_Model_DbTable_Dbpos();
			$rs =$db->getProductById($post['product_id'],$post['branch_id']);
			print_r(Zend_Json::encode($rs));
			exit();
		}
	}
		
}