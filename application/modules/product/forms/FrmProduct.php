<?php 
class Product_Form_FrmProduct extends Zend_Form
{
	public function init()
    {
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	$request=Zend_Controller_Front::getInstance()->getRequest();
	}
	/////////////	Form Product		/////////////////
	public function add($data=null){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Product_Model_DbTable_DbProduct();
		$p_code = $db->getProductCode();
		$name = new Zend_Form_Element_Text("name");
		$name->setAttribs(array(
				'dojoType'=>"dijit.form.ValidationTextBox",
				'class'=>'fullside',
				'required'=>"1"
		));
		
		$type = new Zend_Form_Element_Select("type");
		$opt = array('0'=>$tr->translate("PRODUCT"),'1'=>$tr->translate("SERVICE"));
		$type->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
				
		));
		$type->setMultiOptions($opt);
		$type->setValue($request->getParam("type"));
		
		$pro_code = new Zend_Form_Element_Text("pro_code");
		$pro_code->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside'
		));
		$pro_code->setValue($p_code);
		 
		$serial = new Zend_Form_Element_Text("serial");
		$serial->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside'
		));
		 
		$barcode = new Zend_Form_Element_Text("barcode");
		$barcode->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside'
		));
		$barcodevalue = $db->getProductbarcode();
		$barcode->setValue($barcodevalue);
		
		$opt = array(''=>$tr->translate("SELECT_BRAND"),-1=>$tr->translate("ADD_NEW_BRAND"));
		$brand = new Zend_Form_Element_Select("brand");
		$brand->setAttribs(array(
				'onChange'=>'getPopupBrand();',
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
		));
		$row_brand= $db->getBrand();
		if(!empty($row_brand)){
			foreach ($row_brand as $rs){
				$opt[$rs["id"]] = $rs["name"];
			}
		}
		$brand->setMultiOptions($opt);
		 
		$opt = array(''=>$tr->translate("SELECT_MODEL"),-1=>$tr->translate("ADD_NEW_MODEL"));
		$model = new Zend_Form_Element_Select("model");
		$model->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
				'onChange'=>'getPopupModel()',
		));
		$row_model = $db->getModel();
		if(!empty($row_model)){
			foreach ($row_model as $rs){
				$opt[$rs["key_code"]] = $rs["name"];
			}
		}
		$model->setMultiOptions($opt);
		 
		$opt = array(''=>$tr->translate("SELECT_CATEGORY"),-1=>$tr->translate("ADD_NEW_CATEGORY"));
		$category = new Zend_Form_Element_Select("category");
		$category->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
				'onChange'=>'getPopupCategory()',
		));
		$row_cat = $db->getCategory();
		if(!empty($row_cat)){
			foreach ($row_cat as $rs){
				$opt[$rs["id"]] = $rs["name"];
			}
		}
		$category->setMultiOptions($opt);
		
		$opt = array(''=>$tr->translate("SELECT_COLOR"),-1=>$tr->translate("ADD_NEW_COLOR"));
		$color = new Zend_Form_Element_Select("color");
		$color->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
				'onChange'=>'getPopupColor()',
		));
		$row_color = $db->getColor();
		if(!empty($row_color)){
			foreach ($row_color as $rs){
				$opt[$rs["key_code"]] = $rs["name"];
			}
		}
		$color->setMultiOptions($opt);
		 
		$opt = array(''=>$tr->translate("SELECT_SIZE"),-1=>$tr->translate("ADD_NEW_SIZE"));
		$size = new Zend_Form_Element_Select("size");
		$size->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
				'onChange'=>'getPopupSize()',
		));
		$row_size = $db->getSize();
		if(!empty($row_size)){
			foreach ($row_size as $rs){
				$opt[$rs["key_code"]] = $rs["name"];
			}
		}
		$size->setMultiOptions($opt);
		 
		$unit = new Zend_Form_Element_Text("unit");
		$unit->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
				'required'=>'required',
				'readOnly'=>'readOnly'
		));
		$unit->setValue(1);
		 
		$qty_per_unit = new Zend_Form_Element_Text("qty_unit");
		$qty_per_unit->setAttribs(array(
				'dojoType'=>"dijit.form.NumberTextBox",
				'class'=>'fullside',
				'required'=>'required',
				'onKeyup'=>'doTotalQty()'
		));
		$qty_per_unit->setValue(1);
		 
		$opt = array(''=>$tr->translate("SELECT_MEASURE"),-1=>$tr->translate("ADD_NEW"));
		$measure = new Zend_Form_Element_Select("measure");
		$measure->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false",
				 'queryExpr'=>'*${0}*','class'=>'fullside',
				'Onchange'	=>	'getMeasureLabel();getPopupMeasure();'
		));
		$row_measure= $db->getMeasure();
		if(!empty($row_measure)){
			foreach ($row_measure as $rs){
				$opt[$rs["id"]] = $rs["name"];
			}
		}
		$measure->setMultiOptions($opt);
		 
		$label = new Zend_Form_Element_Text("label");
		$label->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",'class'=>'fullside'
		));
		 
		$description = new Zend_Form_Element_Text("description");
		$description->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",'class'=>'fullside'
				//'required'=>'required'
		));
		
		$price = new Zend_Form_Element_Text("price");
		$price->setAttribs(array(
				'dojoType'=>"dijit.form.NumberTextBox",
				'class'=>'fullside',
				'required'=>'required'
		));
		
		$status = new Zend_Form_Element_Select("status");
		$opt = array('1'=>$tr->translate("ACTIVE"),'0'=>$tr->translate("DEACTIVE"));
		$status->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
				'required'=>'required',
		));
		$status->setMultiOptions($opt);
		
		$branch = new Zend_Form_Element_Select("branch");
		$opt = array(''=>$tr->translate("SELECT_BRANCH"));
		$row_branch = $db->getBranch();
		if(!empty($row_branch)){
			foreach ($row_branch as $rs){
				$opt[$rs["id"]] = $rs["name"];
			}
		}
		$branch->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
				'required'=>'false',
				'Onchange'	=>	'addNewProLocation()'
		));
		$branch->setMultiOptions($opt);
		
		$price_type = new Zend_Form_Element_Select("price_type");
		$opt = array(-1=>$tr->translate("PRICE_LEVEL"));
		$row_price = $db->getPriceType();
		if(!empty($row_price)){
			foreach ($row_price as $rs){
				$opt[$rs["id"]] = $rs["name"];
			}
		}
		$price_type->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false",
				'queryExpr'=>'*${0}*','class'=>'fullside',
				'Onchange'	=>	'addNewPriceType()',
				'required'=>'false',
		));
		$price_type->setMultiOptions($opt);
		
		if($data!=null){
			$name->setValue($data["item_name"]);
			$pro_code->setValue($data["item_code"]);
			$barcode->setValue($data["barcode"]);
			$serial->setValue($data["serial_number"]);
			$brand->setValue($data["brand_id"]);
			$category->setValue($data["cate_id"]);
			$model->setValue($data["model_id"]);
			$color->setValue($data["color_id"]);
			$size->setValue($data["size_id"]);
			$measure->setValue($data["measure_id"]);
			$label->setValue($data["unit_label"]);
			$description->setValue($data["note"]);
			$qty_per_unit->setValue($data["qty_perunit"]);
			$status->setValue($data["status"]);
			$price->setValue($data["price"]);
			$type->setValue($data["is_service"]);
		}
		
		$this->addElements(array($type,$price,$price_type,$branch,$status,$pro_code,$name,$serial,$brand,$model,$barcode,$category,$size,$color,$measure,$qty_per_unit,$unit,$label,$description));
		return $this;
	}
	function productFilter(){
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$request=Zend_Controller_Front::getInstance()->getRequest();
		$db = new Product_Model_DbTable_DbProduct();
		$ad_search = new Zend_Form_Element_Text("ad_search");
		$ad_search->setAttribs(array(
				'dojoType'=>"dijit.form.TextBox",'class'=>'fullside'
		));
		$ad_search->setValue($request->getParam("ad_search"));
		
		$branch = new Zend_Form_Element_Select("branch");
		$opt = array(''=>$tr->translate("SELECT_BRANCH"));
		$row_branch = $db->getBranch();
		if(!empty($row_branch)){
			foreach ($row_branch as $rs){
				$opt[$rs["id"]] = $rs["name"];
			}
		}
		$branch->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
		));
		$branch->setMultiOptions($opt);
		$branch->setValue($request->getParam("branch"));
		
		$status = new Zend_Form_Element_Select("status");
		$opt = array('-1'=>$tr->translate("STATUS"),'1'=>$tr->translate("ACTIVE"),'0'=>$tr->translate("DEACTIVE"));
		$status->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
		));
		$status->setMultiOptions($opt);
		$status->setValue($request->getParam("status"));
		
		$opt = array(''=>$tr->translate("SELECT_BRAND"));
		$brand = new Zend_Form_Element_Select("brand");
		$brand->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
		));
		$row_brand = $db->getBrand();
		if(!empty($row_brand)){
			foreach ($row_brand as $rs){
				$opt[$rs["id"]] = $rs["name"];
			}
		}
		$brand->setMultiOptions($opt);
		$brand->setValue($request->getParam("brand"));
			
		$opt = array(''=>$tr->translate("SELECT_MODEL"));
		$model = new Zend_Form_Element_Select("model");
		$model->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
		));
		$row_model = $db->getModel();
		if(!empty($row_model)){
			foreach ($row_model as $rss){
				$opt[$rss["key_code"]] = $rss["name"];
			}
		}
		$model->setMultiOptions($opt);
		$model->setValue($request->getParam("model"));
			
		$opt = array(''=>$tr->translate("SELECT_CATEGORY"));
		$category = new Zend_Form_Element_Select("category");
		$category->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
		));
		$row_cat = $db->getCategory();
		if(!empty($row_cat)){
			foreach ($row_cat as $rs){
				$opt[$rs["id"]] = $rs["name"];
			}
		}
		$category->setMultiOptions($opt);
		$category->setValue($request->getParam("category"));
		
		$opt = array(''=>$tr->translate("SELECT_COLOR"));
		$color = new Zend_Form_Element_Select("color");
		$color->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
		));
		$row_color = $db->getColor();
		if(!empty($row_color)){
			foreach ($row_color as $rs){
				$opt[$rs["key_code"]] = $rs["name"];
			}
		}
		$color->setMultiOptions($opt);
		$color->setValue($request->getParam("color"));
			
		$opt = array(''=>$tr->translate("SELECT_SIZE"));
		$size = new Zend_Form_Element_Select("size");
		$size->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
		));
		$row_size = $db->getSize();
		if(!empty($row_size)){
			foreach ($row_size as $rs){
				$opt[$rs["key_code"]] = $rs["name"];
			}
		}
		$size->setMultiOptions($opt);
		$size->setValue($request->getParam("size"));
		
		$status_qty = new Zend_Form_Element_Select("status_qty");
		$opt = array(-1=>$tr->translate("ទាំងអស់"),1=>$tr->translate("ផលិតផលមានស្តុក"),0=>$tr->translate("ផលិតផលអស់ពីស្តុក"));
		$status_qty->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
		));
		$status_qty->setMultiOptions($opt);
		$status_qty->setValue($request->getParam("status_qty"));
		
		$type = new Zend_Form_Element_Select("type");
		$opt = array(-1=>$tr->translate("SELECT_TYPE"),'0'=>$tr->translate("PRODUCT"),'1'=>$tr->translate("SERVICE"));
		$type->setAttribs(array(
				'dojoType'=>"dijit.form.FilteringSelect",'autoComplete'=>"false", 'queryExpr'=>'*${0}*','class'=>'fullside',
		));
		$type->setMultiOptions($opt);
		$type->setValue($request->getParam("type"));
		
		$this->addElements(array($type,$status_qty,$ad_search,$branch,$brand,$model,$category,$color,$size,$status));
		return $this;
	}
}