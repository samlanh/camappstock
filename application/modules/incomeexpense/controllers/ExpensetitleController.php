<?php
class Incomeexpense_ExpensetitleController extends Zend_Controller_Action
{
	public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }
    public function indexAction()
    {
    	try{
			$db = new Incomeexpense_Model_DbTable_Dbexpensetitle();
			$rows = $db->getAllTerm();
			$glClass = new Application_Model_GlobalClass();
			$rows = $glClass->getImgStatus($rows, BASE_URL, true);
			$list = new Application_Form_Frmlist();
			$columns=array("TITLE","NAME_ENTITLE","PARENT","STATUS");
			$link=array(
					'module'=>'incomeexpense','controller'=>'expensetitle','action'=>'edit',
			);
			$this->view->list=$list->getCheckList(0, $columns, $rows, array('title'=>$link));
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
			$db = new Incomeexpense_Model_DbTable_Dbexpensetitle();
			$db->add($data);
			Application_Form_FrmMessage::message("INSERT_SUCCESS");
		}
		$db = new Incomeexpense_Model_DbTable_Dbexpensetitle();
		$this->view->parent = $db->getParentCateExpense();
	}
	public function editAction()
	{
		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
		$db = new Incomeexpense_Model_DbTable_Dbexpensetitle();
		if($id==0){
			$this->_redirect('/incomeexpense/expensetitle/index');
		}
		if($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost();
			$data["id"] = $id;
			$db = new Incomeexpense_Model_DbTable_Dbexpensetitle();
			$db->edit($data);
			if($data['save_close']){
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS", '/incomeexpense/expensetitle/index');
			}
		}
		$this->view->rs =  $db->getTermById($id);
		$db = new Incomeexpense_Model_DbTable_Dbexpensetitle();
		$this->view->parent = $db->getParentCateExpense();
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