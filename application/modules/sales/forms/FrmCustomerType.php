<?php 
class Sales_Form_FrmCustomerType extends Zend_Form
{
	public function init()
    {
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	$request=Zend_Controller_Front::getInstance()->getRequest();
	}
	/////////////	Form Product		/////////////////
	public function add($data=null){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$db = new Product_Model_DbTable_DbOther();		
		$_title = new Zend_Form_Element_Text('adv_search');
		$_title->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
		));
		$status_search=  new Zend_Form_Element_Select('status_search');
		$status_search->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'required'=>'required'));
		$_status_opt = array(
				-1=>$tr->translate("ALL"),
				1=>$tr->translate("ACTIVE"),
				0=>$tr->translate("DACTIVE"));
		$status_search->setMultiOptions($_status_opt);
		
		
		
		$name_en = new Zend_Form_Element_Text('title_en');
		$name_en->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>"1"
		));
		
		$name_kh = new Zend_Form_Element_Text('title_kh');
		$name_kh->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>"1",
		));
		$key_code = new Zend_Form_Element_Text('key_code');
		$key_code->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',		
				
				));
		$_arr = array(1=>$tr->translate("ACTIVE"),0=>$tr->translate("DACTIVE"));
		$_status = new Zend_Form_Element_Select("status");
		$_status->setMultiOptions($_arr);
		$_status->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',));
		
		$opt = array(''=>$tr->translate("SELECT_TYPE"),2=>$tr->translate("MODEL"),3=>$tr->translate("SIZE"),4=>$tr->translate("COLOR"),6=>$tr->translate("Customer Type"));
		$type = new Zend_Form_Element_Select("type");
		$type->setAttribs(array(
				'class'=>'form-control',
				'required'=>'required'));
		$type->setMultiOptions($opt);
		
		
		$_display=  new Zend_Form_Element_Select('display_by');
		$_display->setAttribs(array(
				'class'=>'form-control',
				'required'=>'required'));
		$_display_opt = array(
				1=>$tr->translate("NAME_KHMER"),
				2=>$tr->translate("NAME_ENGLISH"));
		$_display->setMultiOptions($_display_opt);
		
		$credit_term = new Zend_Form_Element_Text('credit_term');
		$credit_term->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>"1"
				));
				
		$credit_limit= new Zend_Form_Element_Text('credit_limit');
		$credit_limit->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>"1"));
		
		if($data!=null){
			$name_en->setValue($data['name_en']);
			$type->setValue($data["type"]);
			$_status->setValue($data['status']);
			$credit_term->setValue($data['credit_term']);
			$credit_limit->setValue($data['credit_limit']);
		}
		$this->addElements(array($credit_limit,$credit_term,$status_search,$_title,$name_en,$name_kh,$key_code,$_display,$_status,$type));
		return $this;
	}
	function search(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$_title = new Zend_Form_Element_Text('adv_search');
		$_title->setAttribs(array(
				'class'=>'form-control',
		));
		$_title->setValue($request->getParam("adv_search"));
		
		$status_search=  new Zend_Form_Element_Select('status_search');
		$status_search->setAttribs(array(
				'class'=>'form-control',));
		$_status_opt = array(
				''=>$tr->translate("ALL"),
				1=>$tr->translate("ACTIVE"),
				0=>$tr->translate("DACTIVE"));
		$status_search->setMultiOptions($_status_opt);
		$status_search->setValue($request->getParam("status_search"));
		
		$opt = array(''=>$tr->translate("SELECT_TYPE"),2=>$tr->translate("MODEL"),3=>$tr->translate("SIZE"),4=>$tr->translate("COLOR"));
		$type = new Zend_Form_Element_Select("type");
		$type->setAttribs(array(
				'class'=>'form-control',));
		$type->setMultiOptions($opt);
		$type->setValue($request->getParam("type"));
		
		$this->addElements(array($_title,$status_search,$type));
		return $this;
	}
	
}