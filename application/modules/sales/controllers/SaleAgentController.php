<?php

class Sales_SaleagentController extends Zend_Controller_Action
{

public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }
    public function indexAction()
    {
    	try{
	    	if($this->getRequest()->isPost()){
				$search = $this->getRequest()->getPost();
				$search['start_date']=date("Y-m-d",strtotime($search['start_date']));
				$search['end_date']=date("Y-m-d",strtotime($search['end_date']));
			}
			else{
				$search =array(
						'text_search'=>'',
						'start_date'=>1,
						'end_date'=>date("Y-m-d"),
						'branch_id'=>-1,
						'status'=>-1,
						);
			}
			$db = new Sales_Model_DbTable_DbSalesAgent();
			$rows= $db->getAllSaleAgent($search);
	        $list = new Application_Form_Frmlist();
	    	$columns=array("BRANCH_NAME","AGENT_CODE","SALE_AGENT","CONTACT_NUM","EMAIL","ADDRESS","POSTION","START_WORKING_DATE","DESC_CAP","STATUS");
	    	$link=array(
	    		'module'=>'sales','controller'=>'saleagent','action'=>'edit',
	    	);
	    	$urlEdit = BASE_URL . "/sales/saleagent/edit";
	    	$glClass = new Application_Model_GlobalClass();
	    	$this->view->list=$list->getCheckList(0, $columns, $rows, array('branch_name'=>$link,'name'=>$link,'phone'=>$link));
    	}catch (Exception $e){
    		Application_Form_FrmMessage::messageError("INSERT_ERROR");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
	    	$formFilter = new Sales_Form_FrmSearchStaff();
	    	$this->view->formFilter = $formFilter;
	    	Application_Model_Decorator::removeAllDecorator($formFilter);
	}
	public function addAction() {
		if($this->getRequest()->isPost()) {
			$post = $this->getRequest()->getPost();
			try{
				$add_agent = new Sales_Model_DbTable_DbSalesAgent();
				$add_agent->addSalesAgent($post);
				Application_Form_FrmMessage::message("INSERT_SUCCESS");
			}catch(Exception $e){
				Application_Form_FrmMessage::message('INSERT_FAIL');
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$formAgent = new Sales_Form_FrmStock(null);
		$formShowAgent = $formAgent->showSaleAgentForm(null);
		Application_Model_Decorator::removeAllDecorator($formShowAgent);
		$this->view->form_agent = $formShowAgent;
	}	
	public function editAction() {
		$session_stock=new Zend_Session_Namespace('stock');
		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
    	$db = new Application_Model_DbTable_DbGlobal();
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$data["id"] = $id;
    		$update_agent = new Sales_Model_DbTable_DbSalesAgent();
    		$update_agent ->editSalesAgent($data);
    		Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/sales/saleagent");
    	}
		$this->view->id = $id;
    	$sql="SELECT * FROM tb_sale_agent where id=".$id;
    	$rows= $db->getGlobalDbRow($sql);
		$this->view->user_id = $rows["acl_user"];
    	$formAgent = new Sales_Form_FrmStock(null);
		$formShowAgent = $formAgent->showSaleAgentForm($rows);
		Application_Model_Decorator::removeAllDecorator($formShowAgent);
		$this->view->form_agent = $formShowAgent;
		$this->view->row = $rows;
		
// 		$formpopup = new Application_Form_FrmPopup(null);
// 		$formAdd = $formpopup->popuLocation(null);
// 		Application_Model_Decorator::removeAllDecorator($formAdd);
// 		$this->view->form_addstock = $formAdd;
	}
	
	
	//for get current price getCurrentPrice
	public function addAgentAction(){
		if($this->getRequest()->isPost()){
			$post=$this->getRequest()->getPost();
			$update_agent = new sales_Model_DbTable_DbSalesAgent();
			$agent_id = $update_agent ->addNewAgent($post);
			$result = array("agent_id"=>$agent_id);
			echo Zend_Json::encode($result);
			exit();
		}
		
	}
	
	public function getSaleAgentCodeAction(){//dynamic by customer
	
		$post=$this->getRequest()->getPost();
		$get_code = new Sales_Model_DbTable_DbSalesAgent();
		$result = $get_code->getSaleAgentCode($post["id"]);
		echo Zend_Json::encode($result);
		exit();
	}
}

