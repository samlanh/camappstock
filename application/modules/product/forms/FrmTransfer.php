<?php 
class Product_Form_FrmTransfer extends Zend_Form
{
	public function init()
    {
	}
	protected function GetuserInfo(){
		$user_info = new Application_Model_DbTable_DbGetUserInfo();
		$result = $user_info->getUserInfo();
		return $result;
	}
	public function add($data=null) {
		$db=new Product_Model_DbTable_DbTransfer();
		$db_stock = new Product_Model_DbTable_DbAdjustStock();
		$db_global = new Application_Model_DbTable_DbGlobal();
		$rs_loc = $db->getLocation(2);
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
	
		$tran_num = new Zend_Form_Element_Text('tran_num');
		$tran_num->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>"1",
				'readOnly'=>true));
		$tran_num->setValue($db->getTransferNo(1));
    	
    	$tran_date = new Zend_Form_Element_Text('tran_date');
    	$tran_date->setValue(date('Y-m-d'));
    	$tran_date->setAttribs(array(
    			'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
    			'required'=>'required',
    		));
    	
    	$remark = new Zend_Form_Element_Textarea("remark");
    	$remark->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',	
    		));
		
		$rs_from_loc = $db_global -> getAllLocation();
    	$from_loc = new Zend_Form_Element_Select("from_loc");
    	$from_loc->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    	));
		
		$opt = array(''=>$tr->translate("SELECT_BRANCH"));
		if(!empty($rs_from_loc)){
    		foreach ($rs_from_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
    	$from_loc->setMultiOptions($opt);
		
    	
    	$opt = array(''=>$tr->translate("SELECT_BRANCH"));
    	$to_loc = new Zend_Form_Element_Select("to_loc");
    	$to_loc->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    	));
    	if(!empty($rs_loc)){
    		foreach ($rs_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
    	$to_loc->setMultiOptions($opt);
    	
    	$pro_name =new Zend_Form_Element_Select("pro_name");
    	$pro_name->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onChange'=>'addNew();'
    	));
    	$opt= array(''=>$tr->translate("SELECT_PRODUCT"));
		$row_product = $db_stock->getProductName();
    	if(!empty($row_product)){
    		foreach ($row_product as $rs){
    			$opt[$rs["id"]] = $rs["item_name"]." ".$rs["model"]." ".$rs["size"]." ".$rs["color"];
    		}
    	}
    	$pro_name->setMultiOptions($opt);
    	
    	$type =new Zend_Form_Element_Select("type");
    	$type->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onChange'=>'transferType()'
    	));
    	$opt= array(''=>$tr->translate("SELECT_TRANSFER_TYPE"),1=>$tr->translate("TRANSFER_IN"),2=>$tr->translate("TRANSFER_OUT"));
    	$type->setMultiOptions($opt);
    	
    	
    	$status =new Zend_Form_Element_Select("status");
    	$status->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    	));
    	$opt= array(''=>$tr->translate("SELECT_STATUS"),1=>$tr->translate("ACTIVE"),0=>$tr->translate("DEACTIVE"));
    	$status->setMultiOptions($opt);
    	
    	$id =new Zend_Form_Element_Hidden("id");
    	if($data != null) {
    		$id->setValue($data["id"]);
    		$tran_num->setValue($data["tran_no"]);
    		$tran_date->setValue($data["date"]);
    		$remark->setValue($data["remark"]);
    		
    		$from_loc->setValue($data["cur_location"]);
    		$to_loc->setValue($data["tran_location"]);
    		$status->setValue($data["status"]);
    		$type->setValue($data["type"]);
    	}
    	$this->addElements(array($id,$status,$type,$pro_name,$tran_num,$tran_date,$remark,$from_loc,$to_loc));
    	return $this;
	}
	
	public function addRequest($data=null) {
		$db=new Product_Model_DbTable_DbTransfer();
		$db_stock = new Product_Model_DbTable_DbAdjustStock();
		$db_global = new Application_Model_DbTable_DbGlobal();
		
		$user_info = new Application_Model_DbTable_DbGetUserInfo();
		$result = $user_info->getUserInfo();
			
		$rs_loc = $db->getLocation();
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
	
		$tran_num = new Zend_Form_Element_Text('tran_num');
		$tran_num->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',		
				'required'=>'required','readOnly'=>true));
		$tran_num->setValue($db->getRequestTransferNo($result["branch_id"]));
    	
    	$date =date("Y-m-d");
    	$tran_date = new Zend_Form_Element_Text('date_request');
    	$tran_date->setValue($date);
    	$tran_date->setAttribs(array(
    			'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
    			'required'=>'true'));
    	
    	$date_in = new Zend_Form_Element_Text('date_in');
    	$date_in->setValue($date);
    	$date_in->setAttribs(array(
    			'dojoType'=>"dijit.form.DateTextBox",
    			'class'=>'fullside',
    			'constraints'=>"{datePattern:'dd/MM/yyyy'}",
    			'required'=>'true'));
    	
    	$remark = new Zend_Form_Element_Textarea("remark");
    	$remark->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',		
    			'style'=>'width: 100%;height:35px'));
    	
    	$rs_from_loc = $db_global -> getAllLocation();
    	$from_loc = new Zend_Form_Element_Select("from_loc");
    	$from_loc->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onChange'=>'getRequestNo()'
    	));
		
		$opt = array(''=>$tr->translate("SELECT_BRANCH"));
		if(!empty($rs_from_loc)){
    		foreach ($rs_from_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
    	$from_loc->setMultiOptions($opt);
		$from_loc->setValue($result["branch_id"]);
    	
    	$opt = array(''=>$tr->translate("SELECT_BRANCH"));
    	$to_loc = new Zend_Form_Element_Select("to_loc");
    	$to_loc->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside'
    	));
    	if(!empty($rs_loc)){
    		foreach ($rs_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
    	$to_loc->setMultiOptions($opt);
    	
    	$pro_name =new Zend_Form_Element_Select("pro_name");
    	$pro_name->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onChange'=>'addNew();'
    	));
    	$opt= array(''=>$tr->translate("SELECT_PRODUCT"));
		$row_product=$db_stock->getProductName();
    	if(!empty($row_product)){
    		foreach ($row_product as $rs){
    			$opt[$rs["id"]] = $rs["item_name"].','.$rs["item_code"];
    		}
    	}
    	$pro_name->setMultiOptions($opt);
    	
    	$type =new Zend_Form_Element_Select("type");
    	$type->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onChange'=>'transferType()'
    	));
    	$opt= array(''=>$tr->translate("SELECT_TRANSFER_TYPE"),1=>$tr->translate("TRANSFER_IN"),2=>$tr->translate("TRANSFER_OUT"));
    	$type->setMultiOptions($opt);
    	
    	
    	$status =new Zend_Form_Element_Select("status");
    	$status->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside'
    	));
    	$opt= array(''=>$tr->translate("SELECT_STATUS"),1=>$tr->translate("ACTIVE"),0=>$tr->translate("DEACTIVE"));
    	$status->setMultiOptions($opt);
    	
    	if($data != null) {
    		$tran_num->setValue($data["re_no"]);
    		$tran_date->setValue($data["date_request"]);
    		$date_in->setValue($data["date_in"]);
    		$remark->setValue($data["remark"]);
			$from_loc->setValue($data["cur_location"]);
    		$to_loc->setValue($data["tran_location"]);
    		$status->setValue($data["status"]);
    	}
    	$this->addElements(array($date_in,$status,$type,$pro_name,$tran_num,$tran_date,$remark,$from_loc,$to_loc));
    	return $this;
	}
	
	public function makeTransfers($data=null) {
		$db=new Product_Model_DbTable_DbTransfer();
		$db_stock = new Product_Model_DbTable_DbAdjustStock();
		$rs_loc = $db->getLocation(2);
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		
		$db_global = new Application_Model_DbTable_DbGlobal();
		
		$user_info = new Application_Model_DbTable_DbGetUserInfo();
		$result = $user_info->getUserInfo();
		
		$date =date("Y-m-d");
	
		$tran_num = new Zend_Form_Element_Text('tran_num');
		$tran_num->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				 'required'=>'true',
				'readOnly'=>true));
		$tran_num->setValue($db->getTransferNo(1));
		
		$re_num = new Zend_Form_Element_Text('re_num');
		$re_num->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>"1",
				'readOnly'=>true));
    	
    	$date =date("Y-m-d");
    	$tran_date = new Zend_Form_Element_Text('tran_date');
    	$tran_date->setValue($date);
    	$tran_date->setAttribs(array(
    			'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
    			'required'=>'true'
    			));
		
    	$re_date = new Zend_Form_Element_Text('re_date');
    	$re_date->setValue($date);
    	$re_date->setAttribs(array(
    			'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
    			'required'=>'true'
    			));
    	
    	$remark = new Zend_Form_Element_Textarea("remark");
    	$remark->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
    			'class'=>'fullside',
    			'style'=>'height:35px'));
    	
    	$rs_from_loc = $db_global -> getAllLocation();
    	$from_loc = new Zend_Form_Element_Select("from_loc");
    	$from_loc->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onChange'=>'gettransferNo()',
    			'readonly'=>'readonly'
    	));
		
		$opt = array(''=>$tr->translate("SELECT_BRANCH"));
		if(!empty($rs_from_loc)){
    		foreach ($rs_from_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
    	$from_loc->setMultiOptions($opt);
		$from_loc->setValue($result["branch_id"]);
		
		$opt = array(''=>$tr->translate("SELECT_BRANCH"));
		if(!empty($rs_loc)){
    		foreach ($rs_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
		$from_loc->setMultiOptions($opt);
    	
    	$opt = array(''=>$tr->translate("SELECT_BRANCH"));
    	$to_loc = new Zend_Form_Element_Select("to_loc");
    	$to_loc->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'readonly'=>'readonly'
    	));
    	if(!empty($rs_loc)){
    		foreach ($rs_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
    	$to_loc->setMultiOptions($opt);
    	
    	$pro_name =new Zend_Form_Element_Select("pro_name");
    	$pro_name->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onChange'=>'addNew();'
    	));
    	$opt= array(''=>$tr->translate("SELECT_PRODUCT"));
		$row_product=$db_stock->getProductName();
    	if(!empty($row_product)){
    		foreach ($row_product as $rs){
    			$opt[$rs["id"]] = $rs["item_name"]." ".$rs["model"]." ".$rs["size"]." ".$rs["color"];
    		}
    	}
    	$pro_name->setMultiOptions($opt);
    	
    	$type =new Zend_Form_Element_Select("type");
    	$type->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onChange'=>'transferType()'
    	));
    	$opt= array(''=>$tr->translate("SELECT_TRANSFER_TYPE"),1=>$tr->translate("TRANSFER_IN"),2=>$tr->translate("TRANSFER_OUT"));
    	$type->setMultiOptions($opt);
    	
    	
    	$status =new Zend_Form_Element_Select("status");
    	$status->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    	));
    	$opt= array(''=>$tr->translate("SELECT_STATUS"),1=>$tr->translate("ACTIVE"),0=>$tr->translate("DEACTIVE"));
    	$status->setMultiOptions($opt);
    	if($data != null) {
    		$re_num->setValue($data["re_no"]);
			$re_id = new Zend_Form_Element_Hidden("re_id");
			if(!empty($data["re_id"])){
				$re_id->setValue($data["re_id"]);
			}
			
			$this->addElement($re_id);
			if(@$data["date_tran"]!=""){
				$tran_date->setValue(date("Y-m-d",strtotime($data["date_tran"])));
			}else{
				$tran_date->setValue($date);
			}
			if(@$data["tran_no"]!=""){
				$tran_num->setValue($data["tran_no"]);
			}else{
    		$tran_num->setValue($db->getTransferNo($data["tran_location"]));
			}
			$re_date->setValue(date("Y-m-d",strtotime($data["date_request"])));
			
    		$remark->setValue($data["remark"]);
    		$to_loc->setValue($data["tran_location"]);
			$from_loc->setValue($data["cur_location"]);
    		$status->setValue($data["status"]);
    	}
    	$this->addElements(array($re_date,$re_num,$status,$type,$pro_name,$tran_num,$tran_date,$remark,$from_loc,$to_loc));
    	return $this;
	}
	
	public function editTransfers($data=null) {
		$db=new Product_Model_DbTable_DbTransfer();
		$db_stock = new Product_Model_DbTable_DbAdjustStock();
		$rs_loc = $db->getLocation(2);
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		
		$db_global = new Application_Model_DbTable_DbGlobal();
		
		$user_info = new Application_Model_DbTable_DbGetUserInfo();
		$result = $user_info->getUserInfo();
		
		$date =date("Y-m-d");
	
		$tran_num = new Zend_Form_Element_Text('tran_num');
		$tran_num->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>"1",'required','readOnly'=>true));
		$tran_num->setValue($db->getTransferNo(1));
		
		$re_num = new Zend_Form_Element_Text('re_num');
		$re_num->setAttribs(array('class'=>'form-control', 'required'=>'required','readOnly'=>true));
    	
    	$date =date("Y-m-d");
    	$tran_date = new Zend_Form_Element_Text('tran_date');
    	$tran_date->setValue($date);
    	$tran_date->setAttribs(array(
    			'dojoType'=>"dijit.form.DateTextBox",
    			'class'=>'fullside',
    			'constraints'=>"{datePattern:'dd/MM/yyyy'}",
    			'required'=>'required'));
		
    	$re_date = new Zend_Form_Element_Text('re_date');
    	$re_date->setValue($date);
    	$re_date->setAttribs(array(
    			'required'=>'required',
    			'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				));
    	
    	$remark = new Zend_Form_Element_Textarea("remark");
    	$remark->setAttribs(array(
    			'dojoType'=>"dijit.form.ValidationTextBox",
    			'class'=>'fullside',
    			'required'=>"1",
    			'style'=>'width: 100%;height:35px'));
    	
    	$rs_from_loc = $db_global -> getAllLocation();
    	$from_loc = new Zend_Form_Element_Select("from_loc");
    	$from_loc->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onChange'=>'gettransferNo()'
    	));
		
		$opt = array(''=>$tr->translate("SELECT_BRANCH"));
		if(!empty($rs_from_loc)){
    		foreach ($rs_from_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
    	$from_loc->setMultiOptions($opt);
		$from_loc->setValue($result["branch_id"]);
		
		$opt = array(''=>$tr->translate("SELECT_BRANCH"));
		if(!empty($rs_loc)){
    		foreach ($rs_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
		$from_loc->setMultiOptions($opt);
    	
    	$opt = array(''=>$tr->translate("SELECT_BRANCH"));
    	$to_loc = new Zend_Form_Element_Select("to_loc");
    	$to_loc->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    	));
    	if(!empty($rs_loc)){
    		foreach ($rs_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
    	$to_loc->setMultiOptions($opt);
    	
    	$pro_name =new Zend_Form_Element_Select("pro_name");
    	$pro_name->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onChange'=>'addNew();'
    	));
    	$opt= array(''=>$tr->translate("SELECT_PRODUCT"));
		$row_product=$db_stock->getProductName();
    	if(!empty($row_product)){
    		foreach ($row_product as $rs){
    			$opt[$rs["id"]] = $rs["item_name"]." ".$rs["model"]." ".$rs["size"]." ".$rs["color"];
    		}
    	}
    	$pro_name->setMultiOptions($opt);
    	
    	$type =new Zend_Form_Element_Select("type");
    	$type->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onChange'=>'transferType()'
    	));
    	$opt= array(''=>$tr->translate("SELECT_TRANSFER_TYPE"),1=>$tr->translate("TRANSFER_IN"),2=>$tr->translate("TRANSFER_OUT"));
    	$type->setMultiOptions($opt);
    	
    	
    	$status =new Zend_Form_Element_Select("status");
    	$status->setAttribs(array(
    		'dojoType'=>"dijit.form.FilteringSelect",
			'autoComplete'=>"false",
			'queryExpr'=>'*${0}*',
			'class'=>'fullside',
    	));
    	$opt= array(''=>$tr->translate("SELECT_STATUS"),1=>$tr->translate("ACTIVE"),0=>$tr->translate("DEACTIVE"));
    	$status->setMultiOptions($opt);
    	if($data != null) {
    		$re_num->setValue($data["re_no"]);
			$re_id = new Zend_Form_Element_Hidden("re_id");
			if(!empty($data["re_id"])){
				$re_id->setValue($data["re_id"]);
			}
			
			$this->addElement($re_id);
			if(@$data["date_tran"]!=""){
				$tran_date->setValue(date("Y-m-d",strtotime($data["date_tran"])));
			}else{
				$tran_date->setValue($date);
			}
			if(@$data["tran_no"]!=""){
				$tran_num->setValue($data["tran_no"]);
			}else{
    		$tran_num->setValue($db->getTransferNo($data["tran_location"]));
			}
			$re_date->setValue(date("Y-m-d",strtotime($data["re_date"])));
    		$remark->setValue($data["remark"]);
    		$to_loc->setValue($data["tran_location"]);
			$from_loc->setValue($data["cur_location"]);
    		$status->setValue($data["status"]);
    		//$type->setValue($data["type"]);
    	}
    	$this->addElements(array($re_date,$re_num,$status,$type,$pro_name,$tran_num,$tran_date,$remark,$from_loc,$to_loc));
    	return $this;
	}
	
	public function receiveTransfer($data=null,$type=1) {
		$db=new Product_Model_DbTable_DbTransfer();
		$db_stock = new Product_Model_DbTable_DbAdjustStock();
		$rs_loc = $db->getLocation(2);
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		
		$db_global = new Application_Model_DbTable_DbGlobal();
		
		$user_info = new Application_Model_DbTable_DbGetUserInfo();
		$result = $user_info->getUserInfo();
		
		$date =date("Y-m-d");
	
		$tran_num = new Zend_Form_Element_Text('tran_num');
		$tran_num->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>"1",
				'required'=>'required','readOnly'=>true));
		$tran_num->setValue($db->getTransferNo(1));
		
		$re_num = new Zend_Form_Element_Text('re_num');
		$re_num->setAttribs(array('class'=>'form-control', 'required'=>'required','readOnly'=>true));
    	
    	$date =date("Y-m-d");
    	$tran_date = new Zend_Form_Element_Text('tran_date');
    	$tran_date->setValue($date);
    	$tran_date->setAttribs(array(
    			'class'=>'form-control date-picker',
    			'required'=>'required',
		'data-date-format'=>"dd-mm-yyyy"));
		
    	$re_date = new Zend_Form_Element_Text('re_date');
    	$re_date->setValue($date);
    	$re_date->setAttribs(array('class'=>'form-control date-picker', 'required'=>'required',));
    	
    	$remark = new Zend_Form_Element_Textarea("remark");
    	$remark->setAttribs(array(
    			'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
				'required'=>"1",
    			'style'=>'width: 100%;height:35px'));
    	
    	$rs_from_loc = $db_global -> getAllLocation();
    	$from_loc = new Zend_Form_Element_Select("from_loc");
    	$from_loc->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onChange'=>'gettransferNo()'
    	));
		
		$opt = array(''=>$tr->translate("SELECT_BRANCH"));
		if(!empty($rs_from_loc)){
    		foreach ($rs_from_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
    	$from_loc->setMultiOptions($opt);
		$from_loc->setValue($result["branch_id"]);
		
		$opt = array(''=>$tr->translate("SELECT_BRANCH"));
		if(!empty($rs_loc)){
    		foreach ($rs_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
		$from_loc->setMultiOptions($opt);
    	
    	$opt = array(''=>$tr->translate("SELECT_BRANCH"));
    	$to_loc = new Zend_Form_Element_Select("to_loc");
    	$to_loc->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    	));
    	if(!empty($rs_loc)){
    		foreach ($rs_loc as $rs){
    			$opt[$rs["id"]] = $rs["name"];
    		}
    	}
    	$to_loc->setMultiOptions($opt);
    	
    	$pro_name =new Zend_Form_Element_Select("pro_name");
    	$pro_name->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onChange'=>'addNew();'
    	));
    	$opt= array(''=>$tr->translate("SELECT_PRODUCT"));
		$row_product=$db_stock->getProductName();
    	if(!empty($row_product)){
    		foreach ($row_product as $rs){
    			$opt[$rs["id"]] = $rs["item_name"]." ".$rs["model"]." ".$rs["size"]." ".$rs["color"];
    		}
    	}
    	$pro_name->setMultiOptions($opt);
    	
    	$type =new Zend_Form_Element_Select("type");
    	$type->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			'onChange'=>'transferType()'
    	));
    	$opt= array(''=>$tr->translate("SELECT_TRANSFER_TYPE"),1=>$tr->translate("TRANSFER_IN"),2=>$tr->translate("TRANSFER_OUT"));
    	$type->setMultiOptions($opt);
    	
    	
    	$status =new Zend_Form_Element_Select("status");
    	$status->setAttribs(array(
    			'class'=>'form-control select2me',
    	));
    	$opt= array(''=>$tr->translate("SELECT_STATUS"),1=>$tr->translate("ACTIVE"),0=>$tr->translate("DEACTIVE"));
    	$status->setMultiOptions($opt);
    	//set value when edit
    	if($data != null) {
    		$re_num->setValue($data["re_no"]);
			$re_id = new Zend_Form_Element_Hidden("re_id");
			if(!empty($data["re_id"])){
				$re_id->setValue($data["re_id"]);
			}
			
			$this->addElement($re_id);
			if(@$data["date_tran"]!=""){
				$tran_date->setValue(date("Y-m-d",strtotime($data["date_tran"])));
			}else{
				$tran_date->setValue($date);
			}
			if(@$data["tran_no"]!=""){
				$tran_num->setValue($data["tran_no"]);
			}else{
    		$tran_num->setValue($db->getTransferNo($data["tran_location"]));
			}
			$re_date->setValue(date("Y-m-d",strtotime($data["re_date"])));
    		$remark->setValue($data["remark"]);
    		$to_loc->setValue($data["cur_location"]);
			$from_loc->setValue($data["tran_location"]);
    		$status->setValue($data["status"]);
    		//$type->setValue($data["type"]);
    	}
    	$this->addElements(array($re_date,$re_num,$status,$type,$pro_name,$tran_num,$tran_date,$remark,$from_loc,$to_loc));
    	return $this;
	}
	function frmFilter(){
		$db=new Product_Model_DbTable_DbTransfer();
		$db_global = new Application_Model_DbTable_DbGlobal();
		$db_stock = new Product_Model_DbTable_DbAdjustStock();
		$rs_loc = $db_global->getAllLocation();
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$tran_num = new Zend_Form_Element_Text('avd_search');
		$tran_num->setAttribs(array(
			'dojoType'=>"dijit.form.TextBox",
			'class'=>'fullside',
		));
		$tran_num->setValue($request->getParam("avd_search"));
		 
		$date = date("Y-m-d");
		$start_date = new Zend_Form_Element_Text('start_date');
		$start_date->setValue($date);
		$start_date->setAttribs(array(
				'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				));
		if($request->getParam("start_date") !=""){
			$date = $request->getParam("start_date");
		}else{
			$date = date("Y-m-d");
		}
		$start_date->setValue($date);
		
		$end_date = new Zend_Form_Element_Text('end_date');
		$end_date->setValue($date);
		$end_date->setAttribs(array(
				'dojoType'=>"dijit.form.DateTextBox",
				'class'=>'fullside',
				'constraints'=>"{datePattern:'dd/MM/yyyy'}",
				));
		if($request->getParam("end_date") !=""){
			$date = $request->getParam("end_date");
		}else{
			$date = date("Y-m-d");
		}
		$end_date->setValue($date);
		
		$type =new Zend_Form_Element_Select("type");
		$type->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
				'onChange'=>'transferType()'
		));
		$opt= array(''=>$tr->translate("SELECT_TRANSFER_TYPE"),1=>$tr->translate("TRANSFER_IN"),2=>$tr->translate("TRANSFER_OUT"));
		$type->setMultiOptions($opt);
		$type->setValue($request->getParam("type"));
		 
		$status =new Zend_Form_Element_Select("status");
		$status->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
		));
		$opt= array(1=>$tr->translate("ACTIVE"),0=>$tr->translate("DEACTIVE"));
		$status->setMultiOptions($opt);
		$status->setValue($request->getParam("status"));
		
		$opt = array('-1'=>$tr->translate("SELECT_BRANCH"));
		$to_loc = new Zend_Form_Element_Select("branch");
		$to_loc->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
		));
		$opt = $db_global->getAllLocation(1);
		$to_loc->setMultiOptions($opt);
		$to_loc->setValue($request->getParam("branch"));
		
		$opt= array('-1'=>$tr->translate("SELECT_STATUS"),1=>$tr->translate("Wait check"),2=>$tr->translate("Reject"),3=>$tr->translate("Checked"));
		$check_stat = new Zend_Form_Element_Select("check_stat");
		$check_stat->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
		));
		$check_stat->setValue($request->getParam("check_stat"));
		$check_stat->setMultiOptions($opt);
		
		$this->addElements(array($status,$type,$tran_num,$start_date,$to_loc,$end_date,$check_stat));
		return $this;
	}
}