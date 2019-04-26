<?php 
class Product_Form_FrmBranch extends Zend_Form
{
	public function init()
    {
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	$request=Zend_Controller_Front::getInstance()->getRequest();
	}
	/////////////	Form Product		/////////////////
	public function branch($data=null){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$branch_name = new Zend_Form_Element_Text('branch_name');
		$branch_name->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>"1"
		));
		
		$code = new Zend_Form_Element_Text("code");
		$code->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
		));
		
		$prefix = new Zend_Form_Element_Text("prefix");
		$prefix->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
		));
		
		$addres = new Zend_Form_Element_Textarea("address");
		$addres->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
				'style'=>'height:59px',
		));
		 
		$contact_name = new Zend_Form_Element_Text("contact");
		$contact_name->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
		));
		 
		$contact_num = new Zend_Form_Element_Text("contact_num");
		$contact_num->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
		));
		
		$email = new Zend_Form_Element_Text("email");
		$email->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
		));
		
		$fax = new Zend_Form_Element_Text("fax");
		$fax->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
				
		));
		
		$office_num = new Zend_Form_Element_Text("office_num");
		$office_num->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
		));
		
		$status = new Zend_Form_Element_Select("status");
		$status->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
		));
		$opt = array('1'=>$tr->translate("ACTIVE"),'0'=>$tr->translate("DEACTIVE"));
		$status->setMultiOptions($opt);
		
		$remark = new Zend_Form_Element_Text("remark");
		$remark->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
		));
		
		$show_by = new Zend_Form_Element_Select("show_by");
		$show_by->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
		));
		$opt_show = array('1'=>$tr->translate("SHOW_BY_TEXT"),'2'=>$tr->translate("SHOW_BY_LOGO"),'3'=>$tr->translate("SHOW_BY_ALL"));
		$show_by->setMultiOptions($opt_show);
		
		$logo = new Zend_Form_Element_File("logo");
    	$this->addElement($logo);
		$old_logo = new Zend_Form_Element_Hidden("old_logo");
    	$this->addElement($old_logo);
		
		if($data != null){
			$branch_name->setValue($data["name"]);
			$contact_name->setValue($data["contact"]);
			$contact_num->setValue($data["phone"]);
			$email->setValue($data["email"]);
			$fax->setValue($data["fax"]);
			$office_num->setValue($data["office_tel"]);
			$status->setValue($data["status"]);
			$remark->setValue($data["remark"]);
			$addres->setValue($data["address"]);
			$code->setValue($data["code"]);
			$prefix->setValue($data["prefix"]);
			$show_by->setValue($data["show_by"]);
			$old_logo->setValue($data["logo"]);
		}
		$this->addElements(array($show_by,$code,$prefix,$branch_name,$addres,$contact_name,$contact_num,$email,$fax,$office_num,$status,$remark));
		return $this;
	}
}