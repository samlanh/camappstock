<?php 
class Sales_Form_FrmCustomer extends Zend_Form
{
	public function init()
    {	
	}
	/////////////	Form vendor		/////////////////
public function Formcustomer($data=null) {
$db=new Application_Model_DbTable_DbGlobal();
		$db_cu= new Sales_Model_DbTable_DbCustomer();
		
		$code = $db_cu->getCustomerCode(1);
		
		$nameElement = new Zend_Form_Element_Text('txt_name');
		$nameElement->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>"1",
				'placeholder'=>'Enter Name'));
    	$this->addElement($nameElement);
    	
    	
    	$rowsStock = $db->getGlobalDb('SELECT id,name FROM tb_sublocation WHERE name!=""  ORDER BY id DESC ');
    	$optionsStock = array('1'=>'Default Location','-1'=>'Add New Location');
    	if(count($rowsStock) > 0) {
    		foreach($rowsStock as $readStock) $optionsStock[$readStock['id']]=$readStock['name'];
    	}
    	$mainStockElement = new Zend_Form_Element_Select('branch_id');
    	$mainStockElement->setAttribs(array(
    			"onChange"=>"getCustomerCode()",
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',));
    	$mainStockElement->setMultiOptions($optionsStock);
    	$this->addElement($mainStockElement);
    	
    	$rows= $db->getGlobalDb('SELECT id,name FROM `tb_price_type` WHERE name!="" AND status=1');
    	$opt= array();
    	if(count($rows) > 0) {
    		foreach($rows as $readStock) $opt[$readStock['id']]=$readStock['name'];
    	}
    	$customerlevel = new Zend_Form_Element_Select('customer_level');
    	$customerlevel->setAttribs(array('OnChange'=>'AddLocation()',
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',));
    	$customerlevel->setMultiOptions($opt);
    	$this->addElement($customerlevel);
		
		$province = new Zend_Form_Element_Select('province');
    	$province->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onchange'=>'getDistrict();'));
    	$opt = $db->getAllProvince(1);
		$province->setMultiOptions($opt);
    	$this->addElement($province);
    	
    	$district = new Zend_Form_Element_Select('district');
    	$district->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onchange'=>'getCommune();'));
    	$this->addElement($district);
    	
    	$commune = new Zend_Form_Element_Select('commune');
    	$commune->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',));
    	$this->addElement($commune);
    	
    	$contactElement = new Zend_Form_Element_Text('txt_contact_name');
    	$contactElement->setAttribs(array('placeholder'=>'Enter Contact Name',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
    			));
    	$this->addElement($contactElement);

    	$phoneElement = new Zend_Form_Element_Text('txt_phone');
    	$phoneElement->setAttribs(array(
    			'placeholder'=>'Enter Contact Number',
    			'dojoType'=>"dijit.form.ValidationTextBox",
    			'required'=>true,
				'class'=>'fullside',	));
    	$this->addElement($phoneElement);
    	
    	$contact_phone = new Zend_Form_Element_Text('contact_phone');
    	$contact_phone->setAttribs(array('placeholder'=>'Enter Contact Number',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	));
    	$this->addElement($contact_phone);
    	
    	$faxElement = new Zend_Form_Element_Text('txt_fax');
    	$faxElement->setAttribs(array('placeholder'=>'Enter Fax Number',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	));
    	$this->addElement($faxElement);
    	
    	$emailElement = new Zend_Form_Element_Text('txt_mail');
    	$emailElement->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside','placeholder'=>'Enter Email Address'));
    	$this->addElement($emailElement);
    	
    	$websiteElement = new Zend_Form_Element_Text('txt_website');
    	$websiteElement->setAttribs(array('placeholder'=>'Enter Website Address',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',));
    	$this->addElement($websiteElement);
    	
    	///update 
    	$remarkElement = new Zend_Form_Element_Textarea('remark');
    	$remarkElement->setAttribs(array('placeholder'=>'Remark Here...',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',"rows"=>3));
    	$this->addElement($remarkElement);
    	         
    	$addressElement = new Zend_Form_Element_Textarea('txt_address');
    	$addressElement->setAttribs(array('placeholder'=>'Enter Adress',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',"rows"=>3));
    	$this->addElement($addressElement);
    	
    	$balancelement = new Zend_Form_Element_Text('txt_balance');
    	$balancelement->setValue("0.00");
    	$balancelement->setAttribs(array('readonly'=>'readonly',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',));
    	$this->addElement($balancelement); 

    	$credit_limit = new Zend_Form_Element_Text("credit_limit");
    	$credit_limit->setValue("0.00");
    	$credit_limit->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',));
    	$this->addElement($credit_limit);
    	
    	$credit_tearm = new Zend_Form_Element_Text("credit_tearm");
    	$credit_tearm->setValue("0");
    	$credit_tearm->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',));
    	$this->addElement($credit_tearm);
    	
    	$rows= $db->getGlobalDb('SELECT v.key_code,v.`name_en`,v.`name_kh` FROM `tb_view` AS v WHERE v.`status`=1 AND v.`name_en`!="" AND v.`type`=6');
    	$opt= array();
    	if(count($rows) > 0) {
    		foreach($rows as $readStock) $opt[$readStock['key_code']]=$readStock['name_en'];
    	}
    	$customer_type = new Zend_Form_Element_Select('customer_type');
    	$customer_type->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onchange'=>'getCustomerLimit();'));
    	$customer_type->setMultiOptions($opt);
    	$this->addElement($customer_type);
    	
    	$cus_code = new Zend_Form_Element_Text("cu_code");
    	$cus_code->setValue($code);
    	$cus_code->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',"readOnly"=>"readOnly"));
    	$this->addElement($cus_code);
		
		$user_info = new Application_Model_DbTable_DbGetUserInfo();
    	$result = $user_info->getUserInfo();
    	$status = new Zend_Form_Element_Select('status');
    	$status->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',));
    	$opt = array(1=>"Active");
    	if($result['level']==1){
    		$opt[0]="Deactive";
    	}
    	$status->setMultiOptions($opt);
    	$this->addElement($status);
    	
    	if($data != null) {
			$id = new Zend_Form_Element_Hidden("id");
			$id->setAttribs(array("class"=>"form-control","readOnly"=>"readOnly"));
			$this->addElement($id);
		
		    $id->setValue($data['id']);
    	    $nameElement->setValue($data['cust_name']);
    		$contactElement->setValue($data['contact_name']);
    		$addressElement->setValue($data["address"]);
    		$faxElement->setValue($data['fax']);
    		$emailElement->setValue($data['email']);
    		$websiteElement->setValue($data['website']);
    		$remarkElement->setValue($data['remark']);
    		$contact_phone->setValue($data['contact_phone']);
    		$cus_code->setValue($data["cu_code"]);
    		$customer_type->setValue($data["cu_type"]);
    		$customerlevel->setValue($data["customer_level"]);
    		$credit_limit->setValue($data["credit_limit"]);
    		$credit_tearm->setValue($data["credit_team"]);
    		$phoneElement->setValue($data["phone"]);
    		$mainStockElement->setValue($data["branch_id"]);
			$province->setValue($data["province_id"]);
			$status->setValue($data["status"]);
    	}
    	return $this;
	}
}