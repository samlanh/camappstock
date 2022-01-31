<?php 
class Sales_Form_FrmSearch extends Zend_Form
{
public function init()
	{
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db=new Application_Model_DbTable_DbGlobal();
		
		$tr=Application_Form_FrmLanguages::getCurrentlanguage();
		$nameValue = $request->getParam('text_search');
		$nameElement = new Zend_Form_Element_Text('text_search');
		$nameElement->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
				));
		$nameElement->setValue($nameValue);
		$this->addElement($nameElement);
		
		$rs=$db->getGlobalDb('SELECT id,cust_name,`phone`,`contact_phone` FROM tb_customer WHERE cust_name!="" AND status=1 ');
		$options=array($tr->translate('SELECT_CLIENT'));
		$vendorValue = $request->getParam('customer_id');
		if(!empty($rs)) foreach($rs as $read) $options[$read['id']]=$read['cust_name']."-".$read['contact_phone'];
		$vendor_element=new Zend_Form_Element_Select('customer_id');
		$vendor_element->setMultiOptions($options);
		$vendor_element->setAttribs(array(
				'id'=>'customer_id',
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
		));
		$vendor_element->setValue($vendorValue);
		$this->addElement($vendor_element);
		
		$startDateValue = $request->getParam('start_date');
		$endDateValue = $request->getParam('end_date');
		
		if($endDateValue==""){
			$endDateValue=date("Y-m-d");
		}
		
		$startDateElement = new Zend_Form_Element_Text('start_date');
		$startDateElement->setValue($startDateValue);
		$startDateElement->setAttribs(array(
				'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'placeHolder'=>$tr->translate('START_DATE'),
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		$this->addElement($startDateElement);
		
		$options="";
		$options = $db->getAllLocation(1);
		$locationID = new Zend_Form_Element_Select('branch_id');
		$locationID ->setAttribs(array(
			'dojoType'=>"dijit.form.FilteringSelect",
			'autoComplete'=>"false",
			'queryExpr'=>'*${0}*',
			'class'=>'fullside',
				));
		$locationID->setMultiOptions($options);
		$locationID->setattribs(array(
				'Onchange'=>'AddLocation()',));
	    $locationID->setValue($request->getParam("branch_id"));
		$this->addElement($locationID);
		
		$endDateElement = new Zend_Form_Element_Text('end_date');
		$endDateElement->setValue($endDateValue);
		$this->addElement($endDateElement);
		$endDateElement->setAttribs(array(
			'dojoType'=>"dijit.form.DateTextBox",
			'class'=>'fullside',
			'required'=>true,
			'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		
		$opt=array(-1=>"Choose Sale Person");
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
		$saleagent_id->setValue($request->getParam("saleagent_id"));
		$this->addElement($saleagent_id);
		
		$rows= $db->getGlobalDb('SELECT v.key_code,v.`name_en`,v.`name_kh` FROM `tb_view` AS v WHERE v.`status`=1 AND v.`name_en`!="" AND v.`type`=6');
		$opt= array(0=>"Choose Customer Type");
		if(count($rows) > 0) {
			foreach($rows as $readStock) $opt[$readStock['key_code']]=$readStock['name_en'];
		}
		$customer_type = new Zend_Form_Element_Select('customer_type');
		$customer_type->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
			));
		$customer_type->setMultiOptions($opt);
		$this->addElement($customer_type);
		
		$salestatus = new Zend_Form_Element_Select('sale_status');
		$salestatus->setAttribs(array(
			'dojoType'=>"dijit.form.FilteringSelect",
			'autoComplete'=>"false",
			'queryExpr'=>'*${0}*',
			'class'=>'fullside',
		));
 		$opt = array(-1=>'ជ្រើសរើសប្រភេទលក់',1=>'បានបង់ដាច់',2=>'មិនទាន់ដាច់');
		$salestatus->setMultiOptions($opt);
		$salestatus->setValue($request->getParam("sale_status"));
		$this->addElement($salestatus);
	}
}