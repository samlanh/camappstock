<?php
class Product_MaketransferController extends Zend_Controller_Action
{
public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
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
		$id = $this->getRequest()->getParam("id");
		$db = new Product_Model_DbTable_DbTransfer();
			if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$post["id"] = $id;
					$db->makeTransfer($post);
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
					Application_Form_FrmMessage::redirectUrl('/product/maketransfer');
				}catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR");
    				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				}
			}
			$row = $db->getReqTransferById($id);
			$this->view->rs_detail = $db->getReqTransferDetail($id);
			$formProduct = new Product_Form_FrmTransfer();
			$formStockAdd = $formProduct->makeTransfers($row);
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
				$db->editTransfer($post);
				if(isset($post["save_close"]))
				{
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
					Application_Form_FrmMessage::redirectUrl('/product/transfer/transferlist');
				}else{
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
					Application_Form_FrmMessage::redirectUrl('/product/transfer/transferlist');
				}
			}catch (Exception $e){
				Application_Form_FrmMessage::messageError("INSERT_ERROR",$err = $e->getMessage());
			}
		}
		$rs = $db->getTransferById($id);
		$rs_detail = $db->getTransferDettail($id,$rs["cur_location"]);
		$this->view->rs_detail = $rs_detail;
		$formProduct = new Product_Form_FrmTransfer();
		$formStockAdd = $formProduct->editTransfers($rs);
		Application_Model_Decorator::removeAllDecorator($formStockAdd);
		$this->view->formFilter = $formStockAdd;
	}
}