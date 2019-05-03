<?php
class Product_ApprequestController extends Zend_Controller_Action
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
	    	$formFilter = new Product_Form_FrmTransfer();
	    	$this->view->formFilter = $formFilter->frmFilter();
	    	Application_Model_Decorator::removeAllDecorator($formFilter);
    	}catch (Exception $e){
    		Application_Form_FrmMessage::messageError("INSERT_ERROR");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
}
	function addAction(){
		$id = $this->getRequest()->getParam("id");
		$db = new Product_Model_DbTable_DbTransfer();
			if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$post["id"] = $id;
					$db->ApproveRequest($post);
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
					Application_Form_FrmMessage::redirectUrl('/product/apprequest/');
					
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR");
    				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				  }
			}
			$this->view->rs= $db->getReqTransferById($id);
			$this->view->rs_detail = $db->getReqTransferDetail($id);
	}
}