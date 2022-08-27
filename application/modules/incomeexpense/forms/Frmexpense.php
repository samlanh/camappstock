<?php 
Class Incomeexpense_Form_Frmexpense extends Zend_Form {
	protected $tr;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
	}
	public function FrmAddExpense($data=null){
		
		$db = new Application_Model_DbTable_DbGlobal();
		$optexpense = $db->getAllExpense(1);
		
		$dbexpense = new Incomeexpense_Model_DbTable_Dbexpensetitle();
		$optexpense = $dbexpense->getParentCateExpense('',0,'','',1);
		
// 		$options_exp=array(0=>"Select Expense",-1=>"ADD_NEW");
// 		if(!empty($optexpense)){
// 			foreach($optexpense AS $read){
// 				$options_exp[$read['id']]=$read['name'];
// 			}
// 		}
		
		$title = new Zend_Form_Element_Select('title');
		$title->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onchange'=>'showexpense();'
				));
		$title->setMultiOptions($optexpense);
		
		$expense_title = new Zend_Form_Element_Text('expense_title');
		$expense_title->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>true,
		));
		$title->setMultiOptions($optexpense);
		
		$for_date = new Zend_Form_Element_Select('for_date');
		$for_date->setAttribs(array(
				'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',	
		));
		$options= array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5",6=>"6",7=>"7",8=>"8",9=>"9",10=>"10",11=>"11",12=>"12");
		$for_date->setMultiOptions($options);
		
		$_Date = new Zend_Form_Element_Text('Date');
		$_Date->setAttribs(array(
				'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				'data-date-format'=>"dd-mm-yyyy"
		));
		$_Date->setValue(date('Y-m-d'));
		
		$_branch_id = new Zend_Form_Element_Select('branch_id');
		$_branch_id->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onchange'=>'filterClient();'
		));
		
		$opt = $db->getAllLocation(1);
		$_branch_id->setMultiOptions($opt);
		
		$_stutas = new Zend_Form_Element_Select('Stutas');
		$_stutas ->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
		));
		$options= array(1=>"ប្រើប្រាស់",0=>"មិនប្រើប្រាស់");
		$_stutas->setMultiOptions($options);
		
		$_Description = new Zend_Form_Element_Textarea('Description');
		$_Description ->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
				'rows'=>"3",
		));
		$total_amount=new Zend_Form_Element_Text('total_amount');
		$total_amount->setAttribs(array(
				'dojoType'=>"dijit.form.NumberTextBox",
				'class'=>'fullside',
				'readonly'=>'readonly',
				'required'=>true,
		));
		
		$dollar=new Zend_Form_Element_Text('dollar');
		$dollar->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
				'readonly'=>'readonly',
				'onkeyup'=>'CalculateReceived();'
		));
		$dollar->setValue(1);
		
		$exchangetobath=new Zend_Form_Element_Text('exchangetobath');
		$exchangetobath->setAttribs(array(
				'dojoType'=>"dijit.form.NumberTextBox",
				'class'=>'fullside',	
				'onkeyup'=>'CalculateReceived();'
		));
		$exchangetobath->setValue(1);
		
		$convert_to_dollar=new Zend_Form_Element_Text('convert_to_dollar');
		$convert_to_dollar->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
		));
		
		$received_dollar=new Zend_Form_Element_Text('received_dollar');
		$received_dollar->setAttribs(array(
				'dojoType'=>"dijit.form.NumberTextBox",
				'class'=>'fullside',	
				'onkeyup'=>'CalculateReceived();',
		));
		
		$received_bath=new Zend_Form_Element_Text('received_bath');
		$received_bath->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
				'onkeyup'=>'CalculateReceived();',
				'placeholder'=>'amount in bath'
		));
		
		$invoice=new Zend_Form_Element_Text('invoice');
		$invoice->setAttribs(array(
			'dojoType'=>"dijit.form.TextBox",
			'class'=>'fullside',	
		));
		$invoiceNO = $db->getExpenseCode();
		$invoice->setValue($invoiceNO);
		
		$id = new Zend_Form_Element_Hidden("id");
		$_currency_type = new Zend_Form_Element_Select('currency_type');
		$_currency_type->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
		));
		$opt = $db->getAllCurrency(1);
		$_currency_type->setMultiOptions($opt);
		
		if($data!=null){
			$_branch_id->setValue($data['branch_id']);
			$expense_title->setValue($data['expense_title']);
			$exchangetobath->setValue($data['exchangetobath']);
			$received_bath->setValue($data['received_bath']);
			$received_dollar->setValue($data['received_dollar']);
			$_currency_type->setValue($data['curr_type']);
			$title->setValue($data['title']);
			$total_amount->setValue($data['total_amount']);
			
			$originalDate = $data['for_date'];
			$newDate = date("Y/m/d", strtotime($originalDate));
			
			$_Date->setValue($originalDate);
			
			$for_date->setValue($data['for_date']);
			$_Description->setValue($data['desc']);
			
			$_stutas->setValue($data['status']);
			$invoice->setValue($data['invoice']);
			$id->setValue($data['id']);
		}
		$this->addElements(array($expense_title,$received_bath,$received_dollar,$exchangetobath,$dollar,$_branch_id,$invoice,$_currency_type,$title,$_Date ,$_stutas,$_Description,
				$total_amount,$convert_to_dollar,$for_date,$id,));
		return $this;
	}	
}