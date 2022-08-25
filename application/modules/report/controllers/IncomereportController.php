<?php
class Report_IncomereportController extends Zend_Controller_Action
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
   
   
    public function rptIncomeexpenseAction(){
    	try{
    		if($this->getRequest()->isPost()){
    			$search=$this->getRequest()->getPost();
    		}
    		else{
    			$search = array(
    					"adv_search"=>'',
    					"branch_id"=>-1,
    					'cate_income'=>-1,
    					'start_date'=> date('Y-m-d'),
    					'end_date'=>date('Y-m-d'),
    			);
    		}
    		$db = new report_Model_DbQuery();
    		$this->view->income = $db->getAllIncomeExpanse($search);
    		//$this->view->expense_po = $db->getAllIncomeExpanseSum($search);
    		$this->view->search = $search;
    
    	}catch(Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$form = new Application_Form_FrmSearchGb();
    	$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
    
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->getHeader = $formFilter->getHeaderAction();
    	$this->view->getFooter = $formFilter->getFooterAction();
    
    }

	public function rptSummaryincomeAction(){
    	try{
    		if($this->getRequest()->isPost()){
    			$search=$this->getRequest()->getPost();
    		}
    		else{
    			$search = array(
    					"adv_search"=>'',
    					'cate_income'=>'-1',
    					'start_date'=> date('Y-m-d'),
    					'end_date'=>date('Y-m-d'),
    			);
    		}
    		$db = new report_Model_DbQuery();
    		$this->view->incomebycate = $db->getAllIncomebycate($search);
    		//$this->view->expense_po = $db->getAllIncomeExpanseSum($search);
    		$this->view->search = $search;
    
    	}catch(Exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			echo ($e);
    	}
    	$form = new Application_Form_FrmSearchGb();
    	$form->FrmSearchRegister();
		Application_Model_Decorator::removeAllDecorator($form);
		$this->view->form_search=$form;
    
    	$formFilter = new Application_Form_Frmsearch();
    	$this->view->getHeader = $formFilter->getHeaderAction();
    	$this->view->getFooter = $formFilter->getFooterAction();
    
    }
    
   
}