<?php
class Product_indexController extends Zend_Controller_Action
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
	function updatecodeAction(){
		$db = new Product_Model_DbTable_DbProduct();
		$db->getProductCoded();
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
	    			'model'		=>	'',
	    			'color'		=>	'',
	    			'size'		=>	'',
	    			'status'	=>	1
	    		);
	    	}
			$rows = $db->getAllProductForAdmin($data);
			$columns=array("ITEM_CODE","ITEM_NAME","TYPE",
					"PRODUCT_CATEGORY","MEASURE","QTY","SOLD_PRICE","COST_PRICE","BY_USER","STATUS");
			$link=array(
				'module'=>'product','controller'=>'index','action'=>'edit',
			);
		
			$list = new Application_Form_Frmlist();
			$this->view->list=$list->getCheckList(0, $columns, $rows,array('item_name'=>$link,'item_code'=>$link,'barcode'=>$link,'branch'=>$link));
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
					if(isset($post["save_close"]))
					{
						Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", '/product/index');
					}else{
						Application_Form_FrmMessage::message("INSERT_SUCCESS");
					}
				  }catch (Exception $e){
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR");
				  	Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
				  }
			}
			$rs_branch = $db->getBranch();
			$this->view->branch = $rs_branch;
			$this->view->price_type = $db->getPriceType();
			$formProduct = new Product_Form_FrmProduct();
			$formStockAdd = $formProduct->add(null);
			Application_Model_Decorator::removeAllDecorator($formStockAdd);
			$this->view->form = $formStockAdd;
			
			$formBrand = new Product_Form_FrmBrand();
			$frmBrand = $formBrand->Brand();
			$this->view->frmBrand = $frmBrand;
			Application_Model_Decorator::removeAllDecorator($frmBrand);
			
			$formCat = new Product_Form_FrmCategory();
			$frmCat = $formCat->cat();
			$this->view->frmCat = $frmCat;
			Application_Model_Decorator::removeAllDecorator($frmCat);
			
			$formMeasure = new Product_Form_FrmMeasure();
			$frmMesure = $formMeasure->measure();
			$this->view->frmMesure = $frmMesure;
			Application_Model_Decorator::removeAllDecorator($frmMesure);
			
			$fmOther = new Product_Form_FrmOther();
			$frmOther = $fmOther->add();
			Application_Model_Decorator::removeAllDecorator($frmOther);
			$this->view->frmOther = $frmOther;
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
				  	Application_Form_FrmMessage::messageError("INSERT_ERROR",$err = $e->getMessage());
				  }
		}
		$this->view->rs_location = $db->getProductLocation($id);
		$this->view->rs_price = $db->getProductPrcie($id);
		$rs = $db->getProductById($id);
		$this->view->rs = $rs;
		$formProduct = new Product_Form_FrmProduct();
		$formStockAdd = $formProduct->add($rs);
		Application_Model_Decorator::removeAllDecorator($formStockAdd);
		$this->view->form = $formStockAdd;
		
		$formBrand = new Product_Form_FrmBrand();
		$frmBrand = $formBrand->Brand();
		$this->view->frmBrand = $frmBrand;
		Application_Model_Decorator::removeAllDecorator($frmBrand);
		
		$formCat = new Product_Form_FrmCategory();
		$frmCat = $formCat->cat();
		$this->view->frmCat = $frmCat;
		Application_Model_Decorator::removeAllDecorator($frmCat);
		
		$formMeasure = new Product_Form_FrmMeasure();
		$frmMesure = $formMeasure->measure();
		$this->view->frmMesure = $frmMesure;
		Application_Model_Decorator::removeAllDecorator($frmMesure);
		
		$fmOther = new Product_Form_FrmOther();
		$frmOther = $fmOther->add();
		Application_Model_Decorator::removeAllDecorator($frmOther);
		$this->view->frmOther = $frmOther;
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
	
	public function addOtherAction(){
		if($this->getRequest()->isPost()){
			try {
				$post=$this->getRequest()->getPost();
				$db = new Product_Model_DbTable_DbOther();
				$other_id =$db->addNew($post);
				$result = array('other_id'=>$other_id);
				echo Zend_Json::encode($result);
				exit();
			}catch (Exception $e){
				$result = array('err'=>$e->getMessage());
				echo Zend_Json::encode($result);
				exit();
			}
		}
	}
	public function addNewproudctAction(){
		if($this->getRequest()->isPost()){
			try {
				$post=$this->getRequest()->getPost();
				$db = new Product_Model_DbTable_DbProduct();
				$pro_id =$db->addAjaxProduct($post);
				$result = array('pro_id'=>$pro_id);
				echo Zend_Json::encode($result);
				exit();
			}catch (Exception $e){
				$result = array('err'=>$e->getMessage());
				echo Zend_Json::encode($result);
				exit();
			}
		}
	}
	
	function outstockAction(){
		$db = new Product_Model_DbTable_DbProduct();
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    			'ad_search'	=>	'',
    			'branch'	=>	'',
    			'brand'		=>	'',
    			'category'	=>	'',
    			'model'		=>	'',
    			'color'		=>	'',
    			'size'		=>	'',
    			'status'	=>	1
    		);
    	}
    	$this->view->product = $db->getAllProductOutStock($data);
    	$formFilter = new Product_Form_FrmProduct();
    	$this->view->formFilter = $formFilter->productFilter();
    	Application_Model_Decorator::removeAllDecorator($formFilter);
	}
	
	function lowstockAction(){
		$db = new Product_Model_DbTable_DbProduct();
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    	}else{
    		$data = array(
    			'ad_search'	=>	'',
    			'branch'	=>	'',
    			'brand'		=>	'',
    			'category'	=>	'',
    			'model'		=>	'',
    			'color'		=>	'',
    			'size'		=>	'',
    			'status'	=>	1
    		);
    	}
    	$this->view->product = $db->getAllProductLowStock($data);
    	$formFilter = new Product_Form_FrmProduct();
    	$this->view->formFilter = $formFilter->productFilter();
    	Application_Model_Decorator::removeAllDecorator($formFilter);
	}
	
}

