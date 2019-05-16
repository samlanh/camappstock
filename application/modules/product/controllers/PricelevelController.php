<?php
class Product_PricelevelController extends Zend_Controller_Action
{
	public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    } 
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
		
			$columns=array("PRICE_LEVEL","NOTE","ACTIVE",);
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
}