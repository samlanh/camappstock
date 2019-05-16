<?php
class Product_branchController extends Zend_Controller_Action
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
			$db = new Product_Model_DbTable_DbBranch();
			if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
			}else{
				$data = array(
					'branch_name'=>	'',
					'status'=>1
				);
			}
			$list = new Application_Form_Frmlist();
			$result = $db->getAllBranch($data);
			$this->view->resulr = $result;
			}catch (Exception $e){
				Application_Form_FrmMessage::messageError("INSERT_ERROR");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
			$formFilter = new Product_Form_FrmBranchFilter();
			$frmsearch = $formFilter->branchFilter();
			$this->view->formFilter = $frmsearch;
			Application_Model_Decorator::removeAllDecorator($formFilter);
	}
	public function addAction()
	{
		$session_stock = new Zend_Session_Namespace('stock');
		
			if($this->getRequest()->isPost()) {
				try{
					$data = $this->getRequest()->getPost();
					$db = new Product_Model_DbTable_DbBranch();
					$db->add($data);
					Application_Form_FrmMessage::message("INSER_SUCCESS");
				}catch (Exception $e){
					Application_Form_FrmMessage::messageError("INSERT_ERROR");
					Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				}
			}
			
		$formFilter = new Product_Form_FrmBranch();
		$formAdd = $formFilter->branch();
		$this->view->frmAdd = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
	}
	public function editAction()
	{
		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
		$db = new Product_Model_DbTable_DbBranch();
		
		if($id==0){
			$this->_redirect('/product/branch/index/add');
		}
		if($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost();
			$data["id"] = $id;
			$db->edit($data);
			Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS", '/product/branch/index');
		}
		$rs = $db->getBranchById($id);
		if(empty($rs)){
			Application_Form_FrmMessage::Sucessfull("NO_DATA", '/product/branch/index');
		}
		
		$formFilter = new Product_Form_FrmBranch();
		$formAdd = $formFilter->branch($rs);
		$this->view->frmAdd = $formAdd;
		Application_Model_Decorator::removeAllDecorator($formAdd);
	}
	//view category 27-8-2013
	
	public function addNewLocationAction(){
		$post=$this->getRequest()->getPost();
		$add_new_location = new Product_Model_DbTable_DbAddProduct();
		$location_id = $add_new_location->addStockLocation($post);
		$result = array("LocationId"=>$location_id);
		if(!$result){
			$result = array('LocationId'=>1);
		}
		echo Zend_Json::encode($result);
		exit();
	}
	
}

