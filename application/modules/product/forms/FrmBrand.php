<?php 
class Product_Form_FrmBrand extends Zend_Form
{
	public function init()
    {
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	$request=Zend_Controller_Front::getInstance()->getRequest();
	}
	/////////////	Form Product		/////////////////
	public function Brand($data=null){
		$db = new Brand_Model_DbTable_DbBrand();
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$name = new Zend_Form_Element_Text('brand_name');
		$name->setAttribs(array(
			'dojoType'=>"dijit.form.ValidationTextBox",
			'class'=>'fullside',
			'required'=>"1"
		));
		 
		$parent = new Zend_Form_Element_Select("parent");
		$parent->setAttribs(array(
			'dojoType'=>"dijit.form.FilteringSelect",
			'autoComplete'=>"false",
			'queryExpr'=>'*${0}*',
			'class'=>'fullside',
		));
		$opt = array(''=>$tr->translate("SELECT_BRAND"));
		$row_brand = $db->getAllBrand();
		if(!empty($row_brand)){
			foreach ($row_brand as $rs){
				$opt[$rs["id"]] = $rs["name"];
			}
		}
		$parent->setMultiOptions($opt);
		
		$status = new Zend_Form_Element_Select("status");
		$status->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'required'=>'required'
		));
		$opt = array('1'=>$tr->translate("ACTIVE"),'0'=>$tr->translate("DEACTIVE"));
		$status->setMultiOptions($opt);
		
		$remark = new Zend_Form_Element_Text('remark');
		$remark->setAttribs(array(
			'dojoType'=>"dijit.form.TextBox",
			'class'=>'fullside',
		));
		
		if($data != null){
			$name->setValue($data["name"]);
			$parent->setValue($data["parent_id"]);
			$remark->setValue($data["remark"]);
			$status->setValue($data["status"]);
		}
			
		$this->addElements(array($parent,$name,$status,$remark));
		return $this;
	}
	
	public function BrandFilter(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Brand_Model_DbTable_DbBrand();
		$name = new Zend_Form_Element_Text('name');
		$name->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
		));
		$name->setValue($request->getParam("name"));
		
		$status = new Zend_Form_Element_Select("status");
		$status->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
			'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
				'required'=>'required'
		));
		$opt = array('1'=>$tr->translate("ACTIVE"),'0'=>$tr->translate("DEACTIVE"));
		$status->setMultiOptions($opt);
		$status->setValue($request->getParam("status"));
		
		$this->addElements(array($name,$status));
		return $this;
	}
}