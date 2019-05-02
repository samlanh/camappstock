<?php
class Application_Form_Frmsearch extends Zend_Form
{
	public function init()
	{
		$session_user=new Zend_Session_Namespace('auth');
		$currentBranch = $session_user->location_id;
		$currentlevel = $session_user->level;
		
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
		
		$rs=$db->getGlobalDb('SELECT vendor_id, v_name FROM tb_vendor WHERE v_name!="" AND status=1 ');
		$options=array($tr->translate('SELECT_VENDOR'));
		$vendorValue = $request->getParam('suppliyer_id');
		if(!empty($rs)) foreach($rs as $read) $options[$read['vendor_id']]=$read['v_name'];
		$vendor_element=new Zend_Form_Element_Select('suppliyer_id');
		$vendor_element->setMultiOptions($options);
		$vendor_element->setAttribs(array(
				'id'=>'suppliyer_id',
				'dojoType'=>"dijit.form.FilteringSelect",
				'class'=>'fullside',
		));
		$vendor_element->setValue($vendorValue);
		$this->addElement($vendor_element);
		$_stutas = new Zend_Form_Element_Select('status');
		$_stutas ->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'class'=>'fullside',
		));
		$options= array(-1=>"ទាំងអស់",1=>"ACTIVE",0=>"DEACTIVE");
		$_stutas->setMultiOptions($options);
		$this->addElement($_stutas);
		
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
			'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		
		$this->addElement($startDateElement);
		$endDateElement = new Zend_Form_Element_Text('end_date');
		
		$endDateElement->setValue($endDateValue);
		$this->addElement($endDateElement);
		$endDateElement->setAttribs(array(
			'dojoType'=>"dijit.form.DateTextBox",
			'class'=>'fullside',
			'constraints'=>"{datePattern:'dd/MM/yyyy'}",
		));
		
		$options = $db->getAllLocation(1);
		$locationID = new Zend_Form_Element_Select('branch_id');
		$locationID ->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
			));
		$locationID->setMultiOptions($options);
		$locationID->setattribs(array());
		$locationID->setValue($request->getParam('branch_id'));
		$this->addElement($locationID);
		
		if($currentlevel!=1){
			$locationID->setValue($currentBranch);
			$locationID->setAttribs(array(
				'readonly'=>true
			));
		}
		
		$status_paid = new Zend_Form_Element_Select('status_paid');
		$status_paid ->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
		));
		$options= array(-1=>"ជ្រើសរើសការបង់",1=>"បង់ដាច់",2=>"នៅជំពាក់");
		$status_paid->setMultiOptions($options);
		$this->addElement($status_paid);
		$status_paid->setValue($request->getParam("status_paid"));
		
		$statusCOValue=4;
		$statusCOValue = $request->getParam('purchase_status');
		$optionsCOStatus=array(0=>$tr->translate('CHOOSE_STATUS'),2=>$tr->translate('OPEN'),3=>$tr->translate('IN_PROGRESS'),4=>$tr->translate('PAID'),5=>$tr->translate('RECEIVED'),6=>$tr->translate('MENU_CANCEL'));
		$statusCO=new Zend_Form_Element_Select('purchase_status');
		$statusCO->setMultiOptions($optionsCOStatus);
		$statusCO->setattribs(array(
				'id'=>'status',
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
		));
		
		$statusCO->setValue($statusCOValue);
		$this->addElement($statusCO);
		
		$type = new Zend_Form_Element_Select("type");
		$opt = array('0'=>$tr->translate("PRODUCT"),'1'=>$tr->translate("SERVICE"));
		$type->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
		));
		$type->setMultiOptions($opt);
		$type->setValue($request->getParam("type"));
		$this->addElement($type);
		
		$optexpense = $db->getAllExpense(1);
		$title = new Zend_Form_Element_Select('title');
		$title->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onchange'=>'showexpense();'
		));
		$title->setMultiOptions($optexpense);
		$valuetitle = $request->getParam('title');
		$title->setValue($valuetitle);
		$this->addElement($title);
	}
	function getHeaderAction(){
		$str='<div class="btn-group pull-right">
			 <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true" aria-expanded="false">
			   Actions <i class="fa fa-angle-down"></i>
			 </button>
				<ul class="dropdown-menu" role="menu">
					<li>
						<a href="" onclick="doPrint();"><i class="fa fa-print" aria-hidden="true"></i>&nbsp;&nbsp;Print</a>
					</li>
					<li>
						<a href="#" onClick="exports()"><i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;&nbsp;Export Excel </a>
					</li>
				</ul>
			</div>';
		return $str;
	}
	function getFooterAction(){
		$str='<table align="center" width="100%">
			   <tbody>
			   	<tr style="font-size: 14px;">
			        <td style="width:20%;text-align:center;font-family:Khmer MEF2">យល់ព្រមដោយ</td>
			        <td></td>
			        <td style="width:20%;text-align:center; font-family:Khmer MEF2">ត្រួតពិនិត្យដោយ</td>
			        <td></td>
			        <td style="width:20%;text-align:center;font-family:Khmer MEF2">រៀបចំដោយ</td>
			   </tr>
			</tbody>
		</table>';
		return $str;
	}
}