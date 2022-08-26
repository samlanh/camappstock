<?php

class Incomeexpense_ExpenseController extends Zend_Controller_Action
{
	const REDIRECT_URL = '/incomeexpense/expense';
    public function init()
    {
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }

    public function indexAction()
    {
    	try{
    		$db = new Incomeexpense_Model_DbTable_DbExpense();
    		if($this->getRequest()->isPost()){
    			$formdata=$this->getRequest()->getPost();
    			$formdata['start_date']=date("Y-m-d",strtotime($formdata['start_date']));
    			$formdata['end_date']=date("Y-m-d",strtotime($formdata['end_date']));
    		}
    		else{
    			$formdata = array(
    					"adv_search"=>'',
    					"branch_id"=>-1,
    					'title'=>-1,
    					"status"=>-1,
    					'start_date'=> date('Y-m-d'),
    					'end_date'=>date('Y-m-d'),
    			);
    		}
			$rs_rows= $db->getAllExpense($formdata);//call frome model
    		$glClass = new Application_Model_GlobalClass();
    		$list = new Application_Form_Frmlist();
    		$collumns = array("BRANCH_NAME","INVOICE_NO","TITLE","EXPENSE_TITLE","CURRENCY_TYPE","TOTAL_EXPENSE","NOTE","DATE","BY_USER","STATUS");
    		$link=array(
    				'module'=>'incomeexpense','controller'=>'expense','action'=>'edit',
    		);
    		$this->view->list=$list->getCheckList(0, $collumns,$rs_rows,array('branch_name'=>$link,'expense_title'=>$link,'category'=>$link,'invoice'=>$link,'total_amount'=>$link));
    	}catch (Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
		$formFilter = new Application_Form_Frmsearch();
		$this->view->formFilter = $formFilter;
		Application_Model_Decorator::removeAllDecorator($formFilter);
    }
    public function addAction()
    {
    	if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();	
			$db = new Incomeexpense_Model_DbTable_DbExpense();				
			try {
				$db->addexpense($data);
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			} catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
    	$pructis=new Incomeexpense_Form_Frmexpense();
    	$frm = $pructis->FrmAddExpense();
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
    	
    	$db = new Incomeexpense_Model_DbTable_Dbexpensetitle();
    	$result = $db->getParentCateExpense();
    	
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	
    	array_unshift($result, array ( 'id' => -1,'name' => $tr->translate("ADD_NEW")));
    	$this->view->all_category = $result;
    	
    }
    public function editAction()
    {
    	$id = $this->getRequest()->getParam('id');
    	if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();	
			$data['id'] = $id;
			$db = new Incomeexpense_Model_DbTable_DbExpense();				
			try {
				$db->updateExpense($data);				
				Application_Form_FrmMessage::Sucessfull('EDIT_SUCCESS', self::REDIRECT_URL);		
			} catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$id = $this->getRequest()->getParam('id');
		$db = new Incomeexpense_Model_DbTable_DbExpense();
		$row  = $db->getexpensebyid($id);
		$this->view->row = $row;
		
    	$pructis=new Incomeexpense_Form_Frmexpense();
    	$frm = $pructis->FrmAddExpense($row);
    	Application_Model_Decorator::removeAllDecorator($frm);
    	$this->view->frm_expense=$frm;
    	
    	$db = new Incomeexpense_Model_DbTable_Dbexpensetitle();
    	$result = $db->getParentCateExpense();
    	 
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	 
    	array_unshift($result, array ( 'id' => -1,'name' => $tr->translate("ADD_NEW")));
    	$this->view->all_category = $result;
    }
}