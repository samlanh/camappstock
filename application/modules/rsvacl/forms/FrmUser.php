<?php 
class Rsvacl_Form_FrmUser extends Zend_Form
{
	public function init($data=null)
    {
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	$request=Zend_Controller_Front::getInstance()->getRequest();	
    	$db=new Application_Model_DbTable_DbGlobal();

    	//user typefilter
		$sql = 'SELECT user_type_id,user_type FROM tb_acl_user_type';
		$rs=$db->getGlobalDb($sql);
		$options=array('All User Type');
		$usertype = $request->getParam('user_type_filter');
		foreach($rs as $read) $options[$read['user_type_id']]=$read['user_type'];
		$user_type_filter=new Zend_Form_Element_Select('user_type_filter');
    	$user_type_filter->setMultiOptions($options);
    	$user_type_filter->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
    			'autoComplete'=>"false",
    			'queryExpr'=>'*${0}*',
    			'class'=>'fullside',
    		'onchange'=>'this.form.submit()',
    	));
    	$user_type_filter->setValue($usertype);
    	$this->addElement($user_type_filter);
		
		$options=array('All Location');
		$location_r = $request->getParam('location');
		$location=new Zend_Form_Element_Select('location');
		$options = $db->getAllLocation(1);
    	$location->setMultiOptions($options);
    	$location->setAttribs(array(
    		'dojoType'=>"dijit.form.FilteringSelect",
    		'autoComplete'=>"false",
    		'queryExpr'=>'*${0}*',
    		'class'=>'fullside',
    		'onchange'=>'this.form.submit()',
    	));
    	$location->setValue($location_r);
    	$this->addElement($location);
		
		$options=array(''=>$tr->translate('SELECT_STATUS'),1=>$tr->translate('ACTIVE'),'0'=>$tr->translate('DEACTIVE'));
		$status_r = $request->getParam('status_se');
		$status_se=new Zend_Form_Element_Select('status_se');
    	$status_se->setMultiOptions($options);
    	$status_se->setAttribs(array(
    		'dojoType'=>"dijit.form.FilteringSelect",
    		'autoComplete'=>"false",
    		'queryExpr'=>'*${0}*',
    		'class'=>'fullside',
    		'onchange'=>'this.form.submit()',
    	));
    	$status_se->setValue($status_r);
    	$this->addElement($status_se);
		
		$ad_search=new Zend_Form_Element_Text('ad_search');
    	$ad_search->setAttribs(array(
    		'id'=>'username',
    		'dojoType'=>"dijit.form.ValidationTextBox",
    		'class'=>'fullside',
    		'required'=>true,
    	));
    	$ad_search->setValue($request->getParam('ad_search'));
    	$this->addElement($ad_search);

    	//uer title
    	$user_title = new Zend_Form_Element_Select("title");
    	$user_title->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
    			'autoComplete'=>"false",
    			'queryExpr'=>'*${0}*',
    			'class'=>'fullside',
    			));
    	$user_title->setMultiOptions(array("Mr"=>"Mr","Ms"=>"Ms"));
    	$this->addElement($user_title);

    	$user_fullname = new Zend_Form_Element_Text("fullname");
    	$user_fullname->setAttribs(array(
    		'id'=>'fullname',
    		'dojoType'=>"dijit.form.ValidationTextBox",
    		'class'=>'fullside',
    		'required'=>true,
    	));
    	$this->addElement($user_fullname);
    	
    	//user name
    	$user_name=new Zend_Form_Element_Text('username');
    	$user_name->setAttribs(array(
    		'id'=>'username',
    		'dojoType'=>"dijit.form.TextBox",
			'class'=>'fullside',
    	));
    	$this->addElement($user_name);
    	
    	//email
    	$email=new Zend_Form_Element_Text('email');
    	$email->setAttribs(array(
    		'id'=>'email',
    		'dojoType'=>"dijit.form.TextBox",
				'class'=>'fullside',
    	));
    	$this->addElement($email);
//password    	
    	$password=new Zend_Form_Element_Password('password');
    	$password->setAttribs(array(
    		'id'=>'password',
    		'dojoType'=>"dijit.form.ValidationTextBox",
    		'class'=>'fullside',
    		'required'=>true,
    	));
    	$this->addElement($password);
    	$confirm_password=new Zend_Form_Element_Password('confirm_password');
    	$confirm_password->setAttribs(array(
    		'id'=>'confirm_password',
    		'dojoType'=>"dijit.form.ValidationTextBox",
    		'class'=>'fullside',
    		'required'=>true,
    	));
    	$this->addElement($confirm_password);
    	
    	//user type
		$sql = 'SELECT user_type_id,user_type FROM tb_acl_user_type';
		$rs=$db->getGlobalDb($sql);
		$options=array(''=>$tr->translate('USER_TYPE'));
		foreach($rs as $read) $options[$read['user_type_id']]=$read['user_type'];
		$user_type_id=new Zend_Form_Element_Select('user_type_id');		
    	$user_type_id->setMultiOptions($options);
    	$user_type_id->setAttribs(array(
    		'id'=>'user_type_id',
    			'dojoType'=>"dijit.form.FilteringSelect",
    			'autoComplete'=>"false",
    			'queryExpr'=>'*${0}*',
    			'class'=>'fullside',
    	));
    	$this->addElement($user_type_id);
		
		$status = new Zend_Form_Element_Select("status");
    	$status->setAttribs(array(
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			));
    	$status->setMultiOptions(array("1"=>"Active","0"=>"Deactive"));
    	$this->addElement($status);
    	//location 
    	$option = $db->getAllLocation(1);
    	
    	$locationID= new Zend_Form_Element_Select('LocationId');
    	$locationID->setMultiOptions($option);
    	$locationID->setattribs(array('id'=>'LocationId','Onchange'=>'AddLocation()',
    			'dojoType'=>"dijit.form.FilteringSelect",
				'autoComplete'=>"false",
				'queryExpr'=>'*${0}*',
				'class'=>'fullside',
    			));
    	$this->addElement($locationID);
    	
    	if($data!=null){
			$user_title->setValue($data["title"]);
			$user_fullname->setValue($data["fullname"]);
			$user_name->setValue($data["username"]);
			$email->setValue($data["email"]);
			$user_type_id->setValue($data["user_type_id"]);
			$status->setValue($data["status"]);
			$locationID->setValue($data["LocationId"]);
		}
    	return $this;
    }
}
?>