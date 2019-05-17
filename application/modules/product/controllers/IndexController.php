<?php
class Product_indexController extends Zend_Controller_Action
{
	public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }
    public function indexAction()
    {
    	try{
	    	$db = new Product_Model_DbTable_DbProduct();
	    	if($this->getRequest()->isPost()){
	    		$data = $this->getRequest()->getPost();
	    	}else{
	    		$data = array(
	    			'ad_search'	=>	'',
	    			'branch'	=>	'',
	    			'brand'		=>	'',
	    			'category'	=>	'',
	    			'type'		=>	-1,
	    			'color'		=>	'',
	    			'size'		=>	'',
	    			'status'	=>	1
	    		);
	    	}
			$rows = $db->getAllProductForAdmin($data);
			$columns=array("TYPE","ITEM_CODE","ITEM_NAME",
				"PRODUCT_CATEGORY","MEASURE","QTY","SOLD_PRICE","COST_PRICE","BY_USER","STATUS");
			$link=array(
				'module'=>'product','controller'=>'index','action'=>'edit',
			);
			$list = new Application_Form_Frmlist();
			$this->view->list=$list->getCheckList(0, $columns, $rows,array('is_service'=>$link,'item_name'=>$link,'item_code'=>$link,'barcode'=>$link,'branch'=>$link));
    	}catch (Exception $e){
    		Application_Form_FrmMessage::messageError("INSERT_ERROR");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    	}
    	$formFilter = new Product_Form_FrmProduct();
    	$this->view->formFilter = $formFilter->productFilter();
    	Application_Model_Decorator::removeAllDecorator($formFilter);
	}
	public function addAction()
	{
		$db = new Product_Model_DbTable_DbProduct();
			if($this->getRequest()->isPost()){ 
				try{
					$post = $this->getRequest()->getPost();
					$db->add($post);
					Application_Form_FrmMessage::message("INSERT_SUCCESS");
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR");
				  	Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				  }
			}
			$rs_branch = $db->getBranch();
			$this->view->branch = $rs_branch;
			$formProduct = new Product_Form_FrmProduct();
			$formStockAdd = $formProduct->add(null);
			Application_Model_Decorator::removeAllDecorator($formStockAdd);
			$this->view->form = $formStockAdd;
	}
	public function editAction()
	{
		$id = $this->getRequest()->getParam("id"); 
		$db = new Product_Model_DbTable_DbProduct();
		if($this->getRequest()->isPost()){ 
			try{
				$post = $this->getRequest()->getPost();
				$post["id"] = $id;
				$db->edit($post);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS", '/product/index');
			  }catch (Exception $e){
			  	Application_Form_FrmMessage::messageError("INSERT_ERROR");
			  	Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			  }
		}
		$this->view->rs_location = $db->getProductLocation($id);
		$this->view->rs_price = $db->getProductPrcie($id);
		
		$rs = $db->getProductById($id);
		if(empty($rs)){
			Application_Form_FrmMessage::Sucessfull("NO_DATA", '/product/index');
		}
		$this->view->rs = $rs;
		$formProduct = new Product_Form_FrmProduct();
		$formStockAdd = $formProduct->add($rs);
		Application_Model_Decorator::removeAllDecorator($formStockAdd);
		$this->view->form = $formStockAdd;
	}
	public function getBrandAction(){
		if($this->getRequest()->isPost()){
			try {
				$post=$this->getRequest()->getPost();
				$db = new Product_Model_DbTable_DbBrand();
				$result =$db->getAllBrand($post);
				array_unshift($result, array ( 'id' =>-1,'name' =>"ADD_NEW"));
				print_r(Zend_Json::encode($result));
				exit();
			}catch (Exception $e){
				$result = array('err'=>$e->getMessage());
				echo Zend_Json::encode($result);
				exit();
			}
		}
	}
	public function getCategoryAction(){
		if($this->getRequest()->isPost()){
			try {
				$post=$this->getRequest()->getPost();
				$db = new Product_Model_DbTable_DbCategory();
				$result =$db->getAllCategory($post);
				$tr = Application_Form_FrmLanguages::getCurrentlanguage();
				array_unshift($result, array ( 'id' =>-1,'name' =>$tr->translate("ADD_NEW")));
				echo Zend_Json::encode($result);
				exit();
			}catch (Exception $e){
				$result = array('err'=>$e->getMessage());
				echo Zend_Json::encode($result);
				exit();
			}
		}
	}
	public function getMeasureAction(){
		if($this->getRequest()->isPost()){
			try {
				$post=$this->getRequest()->getPost();
				$db = new Product_Model_DbTable_DbMeasure();
				$result =$db->getAllMeasure($post);
				echo Zend_Json::encode($result);
				exit();
			}catch (Exception $e){
				$result = array('err'=>$e->getMessage());
				echo Zend_Json::encode($result);
				exit();
			}
		}
	}
	public function getOtherAction(){
		if($this->getRequest()->isPost()){
			try {
				$tr = Application_Form_FrmLanguages::getCurrentlanguage();
				$post=$this->getRequest()->getPost();
				$db = new Application_Model_DbTable_DbGlobal();
				$result =$db->getColor($post);
				
				array_unshift($result, array ( 'id' =>-1,'name' =>$tr->translate("ADD_NEW")));
				echo Zend_Json::encode($result);
				exit();
			}catch (Exception $e){
				$result = array('err'=>$e->getMessage());
				echo Zend_Json::encode($result);
				exit();
			}
		}
	}
	public function getAllproductAction(){
		if($this->getRequest()->isPost()){
			try {
				$post=$this->getRequest()->getPost();
				$db = new Application_Model_DbTable_DbGlobal();
				$rs= $db->getAllProduct();
				echo Zend_Json::encode($rs);
				exit();
			}catch (Exception $e){
				$result = array('err'=>$e->getMessage());
				echo Zend_Json::encode($result);
				exit();
			}
		}
	}
}