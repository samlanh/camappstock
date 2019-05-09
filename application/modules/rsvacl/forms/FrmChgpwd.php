<?php
class RsvAcl_Form_FrmChgpwd extends Zend_Form
{
	public function init()
	{
		$current_password=new Zend_Form_Element_Password('current_password');
    	$current_password->setAttribs(array(
    		'id'=>'current_password',
    		'dojoType'=>"dijit.form.ValidationTextBox",
    		'class'=>'fullside',
    		'required'=>"1"
    	));	
    	$this->addElement($current_password);
    	$password=new Zend_Form_Element_Password('password');
    	$password->setAttribs(array(
    		'id'=>'password',
    		'dojoType'=>"dijit.form.ValidationTextBox",
    		'class'=>'fullside',
    		'required'=>"1"
    	));
    	$this->addElement($password);
    	
    	$confirm_password=new Zend_Form_Element_Password('confirm_password');
    	$confirm_password->setAttribs(array(
    		'id'=>'confirm_password',
    		'dojoType'=>"dijit.form.ValidationTextBox",
    		'class'=>'fullside',
    		'required'=>"1"
    	));
    	$this->addElement($confirm_password);
	}
}