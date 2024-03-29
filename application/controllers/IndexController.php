<?php
class IndexController extends Zend_Controller_Action
{ 
	public function init()
    {
        /* Initialize action controller here */
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }
    public function indexAction()
    {
    	$this->_helper->layout()->disableLayout();///sopharat disablelayout to display login
  		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		if($this->getRequest()->isPost()){
			$formdata=$this->getRequest()->getPost();
			$db_user=new Application_Model_DbTable_DbUsers();
			$username=$formdata['txt_email'];
			$password=$formdata['txt_password'];
// 			if($db_user->checkEmail($username)){
				if($db_user->userAuthenticate($username,$password)){
					$user_id=$db_user->getUserID($username);
					$user_info = $db_user->getUserInfo($user_id);
					$arr_acl=$db_user->getArrAcl($user_info['user_type_id']);
					//in case user have no right to access any module of the system
					//if(!$arr_acl) $this->view->msg = $tr->translate('LOGIN_FAIL_NO_MODULE');
					//else{
						$session_lang=new Zend_Session_Namespace('lang');
						$session_lang->lang_id=$formdata["lang"];//for creat session
						Application_Form_FrmLanguages::getCurrentlanguage($session_lang->lang_id);//for choose lang for when login
						
						$session_user=new Zend_Session_Namespace('auth');
						$session_user->unlock();
						$session_user->url_report=$db_user->getArrAclReport($user_info['user_type_id']);
						$session_user->user_id=$user_id;
						$session_user->fullname=$user_info['fullname'];
						$session_user->user_name=$user_info['username'];
						$session_user->level=$user_info['user_type_id'];
						$session_user->user_type=$user_info['user_type'];
						$session_user->location_id=$user_info['LocationId'];
						$session_user->email=$user_info['email'];
						$arr_module= array();
						$arr_actin = array();
						for($i=0; $i<count($arr_acl);$i++){
							$arr_module[$i]=$arr_acl[$i]['module'];
						}
						$arr_module=(array_unique($arr_module));
						$arr_actin=(array_unique($arr_actin));
						$arr_module=$this->sortMenu($arr_module);
						
						$session_user->arr_acl = $arr_acl;
						$session_user->arr_module = $arr_module;
						$session_user->arr_actin = $arr_actin;
												
						$session_user->lock();
						$_url=($arr_acl[0]!=='')? '/home/index':'/default/index' ;//after
						$this->_redirect($_url);
					//}
				}
				elseif (!$db_user->checkStatusByEmail($username)){
					$this->view->msg = $tr->translate('LOGIN_FAIL_COMFIRM');
				}
				else{
					$this->view->msg = $tr->translate('LOGIN_FAIL');
				}
// 			} else{
// 				$this->view->msg  = $tr->translate('EMAIL_NOT');
// 			}
		}
	}		
	protected function sortMenu($menus){
		$menus_order = Array ( 'home','product','purchase','sales','incomeexpense','report','rsvacl');
		$temp_menu = Array();
		$menus=array_unique($menus);
		foreach ($menus_order as $i => $val){
			foreach ($menus as $k => $v){
				if($val == $v){
					$temp_menu[] = $val;
					unset($menus[$k]);
					break;
				}
			}
		}
		return $temp_menu;
	} 
 	public function logoutAction()
    {
    	if($this->getRequest()->getParam('value')==1){
    		$aut=Zend_Auth::getInstance();
    		$aut->clearIdentity();
    		$session_user=new Zend_Session_Namespace('auth');
    		$session_user->unsetAll();
    		$session_stock=new Zend_Session_Namespace('stock');
    		$session_stock->unsetAll();
    		$this->_redirect('/');
    		exit();
    	}
    }
    public function webElementsAction(){	
    }
    public function homeAction(){
    	
    }
    public function mainpageAction(){
    	
    }
    
    public function dashboadAction(){

    }
    function changelangeAction(){
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		$session_lang=new Zend_Session_Namespace('lang');
    		$session_lang->lang_id=$data['lange'];
    		Application_Form_FrmLanguages::getCurrentlanguage($data['lange']);
    		print_r(Zend_Json::encode(2));
    		exit();
    	}
    }
   
  
}