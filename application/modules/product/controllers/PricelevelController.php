<?php
class Product_PricelevelController extends Zend_Controller_Action
{
	public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    } 
   	//view transfer index
//     protected function GetuserInfoAction(){
//     	$user_info = new Application_Model_DbTable_DbGetUserInfo();
//     	$result = $user_info->getUserInfo();
//     	return $result;
//     }
   	public function indexAction()
   	{
   		try{
			$list = new Application_Form_Frmlist();
			$db = new Application_Model_DbTable_DbGlobal();
			$request = $this->getRequest();
			$id = $request->getParam("id", NULL);
		
			$sql = "SELECT id,name,pt.desc,status
			FROM tb_price_type AS pt
			WHERE name !='' ";
			if($this->getRequest()->isPost()){
				$post = $this->getRequest()->getPost();
				if($post['price_type'] !=''){
					$sql .= " AND name LIKE '%".trim($post['price_type'])."%'";
				}
				if($post['status'] !=''){
					$sql .= " AND status =".trim($post['status']);
				}
			}
			$rows=$db->getGlobalDb($sql);
			$glClass = new Application_Model_GlobalClass();
			$rows = $glClass->getImgStatus($rows, BASE_URL, true);
		
			$columns=array("TYPE_PRICE","DESC_CAP","ACTIVE",);
			$link=array(
					'module'=>'product','controller'=>'pricelevel','action'=>'add',
			);
			$this->view->list=$list->getCheckList(0, $columns, $rows, array('desc'=>$link,'name'=>$link));
		}catch (Exception $e){
			Application_Form_FrmMessage::messageError("INSERT_ERROR");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$formFilter = new Product_Form_FrmItemPrice();
		$frmsearch=$formFilter->searchPriceType(null);
		Application_Model_Decorator::removeAllDecorator($frmsearch);
		$this->view->formFilter = $frmsearch;
		Application_Model_Decorator::removeAllDecorator($formFilter);
   	}	
	public function addAction(){		
		$dbprice= new Product_Model_DbTable_DbPrice();
		$formFilter = new Product_Form_FrmItemPrice();
		
		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
		$frm= new Product_Form_FrmItemPrice(null);
		if($id){
			$row=$dbprice->getTypePrice($id);	
			$frmsearch=$formFilter->add($row);
			Application_Model_Decorator::removeAllDecorator($frmsearch);
			$this->view->form_agent = $frmsearch;
			if($this->getRequest()->isPost()) {
				$data = $this->getRequest()->getPost();
				$data["id"]= $id;
				$dbprice->addPriceType($data);
				if(isset($data["saveclose"])){
					Application_Form_FrmMessage::Sucessfull("Price type has been saved! ", "/product/pricelevel");
				}
			}
		}
		else{
			$frmsearch=$formFilter->add(null);
			Application_Model_Decorator::removeAllDecorator($frmsearch);
			$this->view->form_agent = $frmsearch;
			if($this->getRequest()->isPost()) {
				$data = $this->getRequest()->getPost();
				$dbprice->addPriceType($data);
				if(isset($data["saveclose"])){
					Application_Form_FrmMessage::Sucessfull("Price type has been saved! ", "/product/pricelevel");
				}					
			}
		}
		Application_Model_Decorator::removeAllDecorator($frmsearch);
		$this->view->form_agent = $frmsearch;
	}
	/// Ajax Section
	public function getProductAction(){
		if($this->getRequest()->isPost()) {
			$db = new Product_Model_DbTable_DbAdjustStock();
			$data = $this->getRequest()->getPost();
			$rs = $db->getProductById($data["id"]);
			echo Zend_Json::encode($rs);
			exit();
		}
	}
	//add class of price
	public function addNewPriceTypeAction(){
		if($this->getRequest()->isPost()) {
			$dbprice= new Product_Model_DbTable_DbPrice();
			$data = $this->getRequest()->getPost();
			$type_id = $dbprice->addPriceType($data);
			$rs = array("type_id"=>$type_id);
			echo Zend_Json::encode($rs);
			exit();
		}
	}
	public function updateTypePriceAction()//for update class of price name
	{
		$db = new Application_Model_DbTable_DbGlobal();
		if($this->getRequest()->isPost())
		{
			try{
				$post=$this->getRequest()->getPost();
				$dbprice= new Product_Model_DbTable_DbPrice();
				$dbprice->updatePricetype($post);
				$this->_redirect("product/adjust-stock/type-price");
			}catch (Exception $e){
			}
			
		}
	}
		
// 	// not yet done
// 	public function transferStockAction()
// 	{
// 		if($this->getRequest()->isPost()){
// 			$post=$this->getRequest()->getPost();
// 			$transfer_stock = new Product_Model_DbTable_DbAdjustStock();
// 			$db_result = $transfer_stock->TransferStockTransaction($post);
// 			if($post["transfer"]=="Transfer New"){
// 				Application_Form_FrmMessage::Sucessfull("Transfer stock has been successed !", '/product/adjust-stock/transfer-stock');
// 			}
// 			else{
// 				$this->_redirect('/product/adjust-stock/index');
// 			}
// 		}
// 		$db = new Application_Model_DbTable_DbGlobal();
		 
// 		$frm = new Product_Form_FrmTransfer();
// 		 $frm_transfer=$frm->transferItem(null);
// 		 Application_Model_Decorator::removeAllDecorator($frm_transfer);
// 		 $this->view->form_transfer = $frm_transfer;
		 
// 		///view on select location form table
// 		$getOption = new Application_Model_GlobalClass();
// 		$locationRows = $getOption->getLocationOption();
// 		$this->view->locationOption = $locationRows;
// 		///view on select location form table
// 		$toLocationRows = $getOption->tolocationOption();
// 		$this->view->tolocationOption = $toLocationRows;
		
// 		$itemRows = $getOption->selectProductOption();
// 		$this->view->productOption = $itemRows;
		
// 		//for add product;
// 		$formpopup = new Application_Form_FrmPopup(null);
// 		$formproduct = $formpopup->popuProduct(null);
// 		Application_Model_Decorator::removeAllDecorator($formproduct);
// 		$this->view->form = $formproduct;
		
// 		//for add location
// 		$formAdd = $formpopup->popuLocation(null);
// 		Application_Model_Decorator::removeAllDecorator($formAdd);
// 		$this->view->form_addstock = $formAdd;
// 	}
// 	public function transferUpdateAction(){
// 		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
// 		$transfer = new Product_Model_DbTable_DbUpdateTransfer();
// 		$exist=$transfer->transferExist($id);
// 		if($exist==""){
// 			//redirect if no transfer this id
// 			$this->_redirect("product/adjust-stock/index");
// 		}
// 		if($this->getRequest()->getPost()){
// 			$post = $this->getRequest()->getPost();
// 			$update = new Product_Model_DbTable_DbUpdateTransfer();
// 			$update->updateTransferStockTransaction($post);
// 			$this->_redirect("product/adjust-stock/index");
// 		}
// 		$db = new Application_Model_DbTable_DbGlobal();
// 		$productinfo = new Product_Model_DbTable_DbProduct();
// 		$rows = $productinfo->getTransferInfo($id);

// 		$row_item = $productinfo->getTransferItem($id);
// 		$this->view->transfer_item = $row_item;
		
// 		$frm = new Product_Form_FrmTransfer();
// 		$frm_transfer=$frm->transferItem($rows);
// 		Application_Model_Decorator::removeAllDecorator($frm_transfer);
// 		$this->view->form_transfer = $frm_transfer;
			
// 		///view on select location form table
// 		$getOption = new Application_Model_GlobalClass();
// 		$locationRows = $getOption->getLocationOption();
// 		$this->view->locationOption = $locationRows;
// 		///view on select location form table
// 		$toLocationRows = $getOption->tolocationOption();
// 		$this->view->tolocationOption = $toLocationRows;
		
// 		$itemRows = $getOption->selectProductOption();
// 		$this->view->productOption = $itemRows;
		
// 		//for add product;
// 		$formpopup = new Application_Form_FrmPopup(null);
// 		$formproduct = $formpopup->popuProduct(null);
// 		Application_Model_Decorator::removeAllDecorator($formproduct);
// 		$this->view->form = $formproduct;
		
// 		//for add location
// 		$formAdd = $formpopup->popuLocation(null);
// 		Application_Model_Decorator::removeAllDecorator($formAdd);
// 		$this->view->form_addstock = $formAdd;
		
// 	}
	
// 	public function viewTransferAction(){
// 		$id = ($this->getRequest()->getParam('id'))? $this->getRequest()->getParam('id'): '0';
// 		$transfer = new Product_Model_DbTable_DbUpdateTransfer();
// 		$exist=$transfer->transferExist($id);
// 		$user = $this->GetuserInfoAction();
// 		if($exist==""){
// 			//redirect if no transfer this id
// 			$this->_redirect("product/adjust-stock/index");
// 		}
// 		else{
// 			$user_exist=$transfer->transferUserExist($id,$user["location_id"]);
// 			if($user_exist==""){
// 				//redirect if no transfer this id
// 				$this->_redirect("product/adjust-stock/index");
// 			}
			
// 		}
// 		if($this->getRequest()->getPost()){
// // 			$post= $this->getRequest()->getPost();
// // 			print_r($post);exit();
// 			Application_Form_FrmMessage::message("You Haven't Permission To Change !");
// 			Application_Form_FrmMessage::redirectUrl("/product/adjust-stock/index");
// // 			$post = $this->getRequest()->getPost();
// // 			$update = new Product_Model_DbTable_DbUpdateTransfer();
// // 			$update->updateTransferStockTransaction($post);
// // 			$this->_redirect("product/adjust-stock/index");
// 		}
// 		$db = new Application_Model_DbTable_DbGlobal();
// 		$productinfo = new Product_Model_DbTable_DbProduct();
// 		$rows = $productinfo->getTransferInfo($id);
	
// 		$row_item = $productinfo->getTransferItem($id);
// 		$this->view->transfer_item = $row_item;
	
// 		$frm = new Product_Form_FrmTransfer();
// 		$frm_transfer=$frm->transferItem($rows);
// 		Application_Model_Decorator::removeAllDecorator($frm_transfer);
// 		$this->view->form_transfer = $frm_transfer;
			
// 		///view on select location form table
// 		$getOption = new Application_Model_GlobalClass();
// 		$locationRows = $getOption->getLocationOption();
// 		$this->view->locationOption = $locationRows;
// 		///view on select location form table
// 		$toLocationRows = $getOption->tolocationOption();
// 		$this->view->tolocationOption = $toLocationRows;
	
// 		$itemRows = $getOption->getProductOption();
// 		$this->view->productOption = $itemRows;
	
// 		//for add product;
// 		$formpopup = new Application_Form_FrmPopup(null);
// 		$formproduct = $formpopup->popuProduct(null);
// 		Application_Model_Decorator::removeAllDecorator($formproduct);
// 		$this->view->form = $formproduct;
	
// 		//for add location
// 		$formAdd = $formpopup->popuLocation(null);
// 		Application_Model_Decorator::removeAllDecorator($formAdd);
// 		$this->view->form_addstock = $formAdd;
	
// 	}
	
// 	// not yet done
// 	public function adjustPriceAction()
// 	{
// 		$db = new Application_Model_DbTable_DbGlobal();
// 		if($this->getRequest()->isPost())
// 		{	
// 			$post=$this->getRequest()->getPost();
// 			if($post['save']!=="")
// 			{
// 				$adjust_price = new Product_Model_DbTable_DbAdjustStock();
// 				$db_result = $adjust_price->adjustPricing($post);
// 			}
// 			$this->_redirect("product/index/index");
// 		}	
// 		$session_stock=new Zend_Session_Namespace('stock');
// 		///view on select location form table
// 		$getOption = new Application_Model_GlobalClass();
// 		$locationRows = $getOption->getLocationOption($session_stock->stockID);
// 		$this->view->locationOption = $locationRows;
// 		///view on select location form table
// 		$itemRows = $getOption->getProductOption($session_stock->stockID);
// 		$this->view->productOption = $itemRows;
		
// 		//for add product;
// 		$formpopup = new Application_Form_FrmPopup(null);
// 		$formproduct = $formpopup->popuProduct(null, $session_stock->stockID);
// 		Application_Model_Decorator::removeAllDecorator($formproduct);
// 		$this->view->form_product = $formproduct;
			
// 	}
// 	public function getCurrentQuantityAction(){
		
// 		$post=$this->getRequest()->getPost();
//  		$get_item = new Product_Model_DbTable_DbAdjustStock();
//  		$result = $get_item->getCurrentItem($post);    
//  		 if(!$result){
//  		 	$result = array('qty'=>0);
//  		 }		
// 		echo Zend_Json::encode($result);
// 		exit();
// 	}
}