<?php

class Sales_Form_FrmQuoatation extends Zend_Form
{
    protected function GetuserInfo(){
    	$user_info = new Application_Model_DbTable_DbGetUserInfo();
    	$result = $user_info->getUserInfo();
    	return $result;
    }
    public function SaleOrder($data=null)
    {
    	$user_info = new Application_Model_DbTable_DbGetUserInfo();
    	$result = $user_info->getUserInfo();
    	
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	$request=Zend_Controller_Front::getInstance()->getRequest();
    	$db=new Application_Model_DbTable_DbGlobal();
    	
    	$customerid=new Zend_Form_Element_Select('customer_id');
    	$customerid ->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'Onchange'=>'getCustomerInfo()',
				'id'=>'customer_id'
    			));
    	$options = $db->getAllCustomer(1);
    	$customerid->setMultiOptions($options);
    	$this->addElement($customerid);
    	
    	$roder_element= new Zend_Form_Element_Text("txt_order");
    	$roder_element->setAttribs(array(
    			'placeholder' =>'Optional',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
    			"onblur"=>"CheckPOInvoice();","readOnly"=>true));
    	$qo = $db->getQuoationNumber(1);
    	$roder_element->setValue($qo);
    	$this->addElement($roder_element);
    	
    	$roder_element= new Zend_Form_Element_Text("so_number");
    	$roder_element->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
    			'style'=>'background:yellow !important;',
    			"readOnly"=>true));
    	$qo = $db->getQuoationNumber(1);
    	$roder_element->setValue($qo);
    	$this->addElement($roder_element);
    	
    	
    	$user= $this->GetuserInfo();
    	$options="";
		
		$locationID = new Zend_Form_Element_Select('branch_id');
    	$locationID ->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
    			'autoComplete'=>"false",
    			'queryExpr'=>'*${0}*',
    			'class'=>'fullside',
    			));
		$options = $db->getAllLocation(1);
    	$locationID->setMultiOptions($options);
    	$locationID->setattribs(array(
    			'Onchange'=>'getsaleOrderNumber()',));
    	$this->addElement($locationID);
    	    	
    	
    	$rowsPayment = $db->getGlobalDb('SELECT id, description,symbal FROM tb_currency WHERE status = 1 ');
    	if($rowsPayment) {
    		foreach($rowsPayment as $readPayment) $options_cur[$readPayment['id']]=$readPayment['description'].$readPayment['symbal'];
    	}	 
    	$currencyElement = new Zend_Form_Element_Select('currency');
    	$currencyElement->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
    			'autoComplete'=>"false",
    			'queryExpr'=>'*${0}*',
    			'class'=>'fullside',
    			));
    	$currencyElement->setMultiOptions($options_cur);
    	$this->addElement($currencyElement);
    	
    	$saleagent_id = new Zend_Form_Element_Select('saleagent_id');
    	$saleagent_id->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    	));
    	$opt = $db->getSaleAgent(1);
    	$saleagent_id->setMultiOptions($opt);
    	$this->addElement($saleagent_id);
    	
    	$status = new Zend_Form_Element_Select('status');
    	$status->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			));
    	$opt = array(1=>"Active");
    	if($result['level']==1){
    		$opt[0]="Deactive";
    	}
    	$status->setMultiOptions($opt);
    	$this->addElement($status);
    	
    	$descriptionElement = new Zend_Form_Element_Textarea('remark');
    	$descriptionElement->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',"rows"=>3));
    	$this->addElement($descriptionElement);
    	
    	$allTotalElement = new Zend_Form_Element_Text('all_total');
    	$allTotalElement->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
    			'readonly'=>'readonly','style'=>''));
    	$this->addElement($allTotalElement);
    	
    	$netTotalElement = new Zend_Form_Element_Text('net_total');
    	$netTotalElement->setAttribs(array(
    			'readonly'=>'readonly',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',));
    	$this->addElement($netTotalElement);
    	
    	$discountValueElement = new Zend_Form_Element_Text('discount_value');
    	$discountValueElement->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
    			'onblur'=>'doTotal()',));
    	$this->addElement($discountValueElement);
    	
    	$discountRealElement = new Zend_Form_Element_Text('discount_real');
    	$discountRealElement->setAttribs(array(
    			'readonly'=>'readonly',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',));
    	$this->addElement($discountRealElement);
    	
    	$globalRealElement = new Zend_Form_Element_Text('global_disc');
    	$globalRealElement->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',));
    	$this->addElement($globalRealElement);
    	
    	$discountValueElement = new Zend_Form_Element_Text('discount_value');
    	$discountValueElement->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
    			'onblur'=>'doTotal();','style'=>''));
    	$this->addElement($discountValueElement);
    	
    	$dis_valueElement = new Zend_Form_Element_Text('dis_value');
    	$dis_valueElement->setAttribs(array(
    			'placeholder' => 'Discount Value',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',));
    	$dis_valueElement->setValue(0);
    	$dis_valueElement->setAttribs(array(
    			"onkeyup"=>"calculateDiscount();",
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',));
    	$this->addElement($dis_valueElement);
    	
    	$totalAmountElement = new Zend_Form_Element_Text('totalAmoun');
    	$totalAmountElement->setAttribs(array(
    			'readonly'=>'readonly',
    			'dojoType'=>"dijit.form.TextBox",
    			'class'=>'fullside',
    	));
    	$this->addElement($totalAmountElement);
    	
    	$date =new Zend_Date();
    	$dateOrderElement = new Zend_Form_Element_Text('order_date');
    	$dateOrderElement ->setAttribs(array(
    			'dojoType'=>"dijit.form.DateTextBox",
    			'class'=>'fullside',
    			'constraints'=>"{datePattern:'dd/MM/yyyy'}",
    			));
    	$dateOrderElement ->setValue(date("Y-m-d"));
    	$this->addElement($dateOrderElement);
		
		$credit_term = new Zend_Form_Element_Text('credit_term');
    	$credit_term->setAttribs(array(
    			'readonly'=>'readonly',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
    	));
    	$this->addElement($credit_term);
    	
    	$credit_limit = new Zend_Form_Element_Text('credit_limit');
    	$credit_limit->setAttribs(array(
    			'readonly'=>'readonly',
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
    	));
    	$this->addElement($credit_limit);
    	
    	$totalElement = new Zend_Form_Element_Text('total');
    	$this->addElement($totalElement);
    	
    		if($data != null) {
    			$idElement = new Zend_Form_Element_Hidden('id');
    			$this->addElement($idElement);
    			$idElement->setValue($data["id"]);
    			$customerid->setValue($data["customer_id"]);
    			$locationID->setValue($data['branch_id']);
    			$currencyElement->setValue($data['currency_id']);
    			$saleagent_id->setValue($data['saleagent_id']);
    			$descriptionElement->setValue($data['remark']);
    			$dateOrderElement->setValue(date("m/d/Y",strtotime($data['date_order'])));
    			$roder_element->setValue($data['quoat_number']);
    			$totalAmountElement->setValue($data['all_total']);
    			$dis_valueElement->setValue($data['discount_value']);
    			$allTotalElement->setValue($data['net_total']);
    		} else {
    	}
     	return $this;
    }
}