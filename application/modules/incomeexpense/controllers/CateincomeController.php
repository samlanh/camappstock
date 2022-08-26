<?php
class Incomeexpense_CateincomeController extends Zend_Controller_Action
{
	public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }
    public function indexAction()
    {
    	try{
			$db = new Incomeexpense_Model_DbTable_DbCateIncome();
			$rows = $db->getCateIcome();
			$glClass = new Application_Model_GlobalClass();
			$rows = $glClass->getImgStatus($rows, BASE_URL, true);
			$list = new Application_Form_Frmlist();
			$columns=array("TITLE","ACCOUNT_CODE","PARENT","STATUS");
			$link=array(
					'module'=>'incomeexpense','controller'=>'cateincome','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $columns, $rows, array('category_name'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::messageError("INSERT_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function addAction()
	{
		$session_stock = new Zend_Session_Namespace('stock');
		if($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost();
			$db = new Incomeexpense_Model_DbTable_DbCateIncome();
			$db->addCateIncome($data);
			Application_Form_FrmMessage::message("INSERT_SUCCESS");
		}
		$db = new Incomeexpense_Model_DbTable_DbCateIncome();
		$this->view->parent = $db->getParentCateIncome();
	}
	public function editAction()
	{
		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
		$db = new Incomeexpense_Model_DbTable_DbCateIncome();
		if($id==0){
			$this->_redirect('/incomeexpense/cateincome/index');
		}
		if($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost();
			$data["id"] = $id;
			$db = new Incomeexpense_Model_DbTable_DbCateIncome();
			$db->updateCateIncome($data);
			if($data['save_close']){
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS", '/incomeexpense/cateincome/index');
			}
		}
		$this->view->rs =  $db->getCateIncomeById($id);
		$db = new Incomeexpense_Model_DbTable_DbCateIncome();
		$this->view->parent = $db->getParentCateIncome();
	}
	function getexpensecateAction(){
		$post=$this->getRequest()->getPost();
		$db = new Application_Model_DbTable_DbGlobal();
		$result = $db->getAllExpense(null);
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		array_unshift($result, array('id'=>-1,'name'=>$tr->translate('ADD_NEW')));
		echo Zend_Json::encode($result);
		exit();
	}
}