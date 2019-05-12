<?php

class Sales_Form_FrmSale extends Zend_Form
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
    			));
    	$options = $db->getAllCustomer(1);
    	$customerid->setMultiOptions($options);
    	$this->addElement($customerid);
    	
    	$roder_element= new Zend_Form_Element_Text("txt_order");
    	$roder_element->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
    			"readonly"=>true,
    			"onblur"=>"CheckPOInvoice();"));
    	$this->addElement($roder_element);
    	$qo = $db->getSalesNumber(1);
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
    	    	
    	$rowspayment= $db->getGlobalDb('SELECT * FROM tb_paymentmethod');
    	if($rowspayment) {
    		foreach($rowspayment as $readCategory) $options_cg[$readCategory['payment_typeId']]=$readCategory['payment_name'];
    	}
    	$paymentmethodElement = new Zend_Form_Element_Select('payment_name');
    	$paymentmethodElement->setMultiOptions($options_cg);
    	$this->addElement($paymentmethodElement);
    	$paymentmethodElement->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
    			'autoComplete'=>"false",
    			'queryExpr'=>'*${0}*',
    			'class'=>'fullside',
    		));
    	$rowsPayment = $db->getGlobalDb('SELECT id, description,symbal FROM tb_currency WHERE status = 1 ');
    	if($rowsPayment) {
    		foreach($rowsPayment as $readPayment) $options_cur[$readPayment['id']]=$readPayment['description'].$readPayment['symbal'];
    	}	 
    	$currencyElement = new Zend_Form_Element_Select('currency');
    	$currencyElement->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',));
    	$currencyElement->setMultiOptions($options_cur);
    	$this->addElement($currencyElement);
    	
    	$descriptionElement = new Zend_Form_Element_Textarea('remark');
    	$descriptionElement->setAttribs(array(
    			"class"=>'form-control',
    			"rows"=>3));
    	$this->addElement($descriptionElement);
    	
    	$allTotalElement = new Zend_Form_Element_Text('all_total');
    	$allTotalElement->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
    			'readonly'=>'readonly','style'=>'text-align:right'));
    	$this->addElement($allTotalElement);
    	
    	$netTotalElement = new Zend_Form_Element_Text('net_total');
    	$netTotalElement->setAttribs(array('readonly'=>'readonly',));
    	$this->addElement($netTotalElement);
    	
    	$opt=array();
    	$rows = $db->getGlobalDb('SELECT id ,name FROM `tb_sale_agent` WHERE name!="" AND status=1');
    	if(!empty($rows)) {
    		foreach($rows as $rs) $opt[$rs['id']]=$rs['name'];
    	}
    	$saleagent_id = new Zend_Form_Element_Select('saleagent_id');
    	$saleagent_id->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			));
    	$saleagent_id->setMultiOptions($opt);
    	$this->addElement($saleagent_id);
    	
    	
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
				'class'=>'fullside'));
    	$this->addElement($discountRealElement);
    	
    	$globalRealElement = new Zend_Form_Element_Hidden('global_disc');
    	$globalRealElement->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
    			));
    	$this->addElement($globalRealElement);
    	
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
    	
    	$discountValueElement = new Zend_Form_Element_Text('discount_value');
    	$discountValueElement->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
    			'onblur'=>'doTotal();','style'=>'text-align:right'));
    	$this->addElement($discountValueElement);
    	
    	$dis_valueElement = new Zend_Form_Element_Text('dis_value');
    	$dis_valueElement->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
    			"onkeyup"=>"calculateDiscount();",
    			'class'=>'fullside',
    			));
    	$dis_valueElement->setValue(0);
    	$this->addElement($dis_valueElement);
    	
    	$totalAmountElement = new Zend_Form_Element_Text('totalAmoun');
    	$totalAmountElement->setAttribs(array('readonly'=>'readonly',
    			'dojoType'=>"dijit.form.NumberTextBox",
				'class'=>'fullside',	
    	));
    	$this->addElement($totalAmountElement);
    	
    	$remainlElement = new Zend_Form_Element_Text('remain');
    	$remainlElement->setAttribs(array('readonly'=>'readonly',
    			'dojoType'=>"dijit.form.NumberTextBox",
				'class'=>'fullside',
    			));
    	$this->addElement($remainlElement);
    	
    	$balancelElement = new Zend_Form_Element_Text('balance');
    	$balancelElement->setAttribs(array(
    			'readonly'=>'readonly',
    			'dojoType'=>"dijit.form.NumberTextBox",
    			'class'=>'fullside',
    			"class"=>"form-control"));
    	$this->addElement($balancelElement);
    	
    	$date_inElement = new Zend_Form_Element_Text('date_in');
    	$date =new Zend_Date();
    	$date_inElement ->setAttribs(array(
    			'dojoType'=>"dijit.form.DateTextBox",
    			'class'=>'fullside',
    			'constraints'=>"{datePattern:'dd/MM/yyyy'}",
    			));
    	$date_inElement ->setValue(date("Y-m-d"));
    	$this->addElement($date_inElement);
    	
    	$dateOrderElement = new Zend_Form_Element_Text('order_date');
    	$dateOrderElement ->setAttribs(array(
    			'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}"
    		));
    	$dateOrderElement ->setValue(date("Y-m-d"));
    	$this->addElement($dateOrderElement);
    	
    	$dateElement = new Zend_Form_Element_Text('date');
    	$this->addElement($dateElement);
    	 
    	$totalElement = new Zend_Form_Element_Text('total');
    	$this->addElement($totalElement);
    	
    	$totaTaxElement = new Zend_Form_Element_Text('total_tax');
    	$totaTaxElement->setAttribs(array(
    			'dojoType'=>"dijit.form.NumberTextBox",
    			'class'=>'fullside',
    			'style'=>'text-align:right'
    		));
    	$this->addElement($totaTaxElement);
    	
    	$paidElement = new Zend_Form_Element_Text('paid');
    	$paidElement->setAttribs(array(
    			'dojoType'=>"dijit.form.NumberTextBox",
    			'class'=>'fullside',
    			'onkeyup'=>'doRemain();',
    			'style'=>'text-align:right'));
    	$this->addElement($paidElement);
    	
    		if($data != null) {
    			$idElement = new Zend_Form_Element_Hidden('id');
    			$this->addElement($idElement);
    			$idElement ->setValue($data["id"]);
    			$customerid->setValue($data["customer_id"]);
    			$locationID->setValue($data['branch_id']);
    			$currencyElement->setValue($data['currency_id']);
    			$saleagent_id->setValue($data['saleagent_id']);
    			$descriptionElement->setValue($data['remark']);
    			$dateOrderElement->setValue(date("m/d/Y",strtotime($data['date_sold'])));
    			$roder_element->setValue($data['sale_no']);
    			$totalAmountElement->setValue($data['all_total']);
    			if($data['discount_type']==1){$data['discount_value']=$data['discount_value']."%";}
    			$dis_valueElement->setValue($data['discount_value']);
    			$allTotalElement->setValue($data['net_total']);
    		} else {
    	}
     	return $this;
    }

}

