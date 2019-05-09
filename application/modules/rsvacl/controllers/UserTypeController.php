<?php
class Rsvacl_UsertypeController extends Zend_Controller_Action
{
    public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
    }
    public function indexAction()
    {
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
        $getUser = new Rsvacl_Model_DbTable_DbUserType();
        $userQuery = "SELECT u.user_type_id,u.user_type,(SELECT u1.user_type FROM `tb_acl_user_type` u1 WHERE u1.user_type_id = u.parent_id LIMIT 1) parent_id FROM `tb_acl_user_type` u";
        $rows = $getUser->getUserTypeInfo($userQuery);
        if($rows){
        	$link = array("rsvacl","usertype","edit");
        	$links = array('user_type'=>$link);
        	$list=new Application_Form_Frmlist();
        	$columns=array($tr->translate('USER_TYPE'), $tr->translate('PARENT'));
        	$this->view->form=$list->getCheckList('radio', $columns, $rows, $links);
        }else $this->view->form = $tr->translate('NO_RECORD_FOUND');
    }
	public function addAction()
	{
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		if($this->getRequest()->isPost())
		{
			$db=new Rsvacl_Model_DbTable_DbUserType();	
			$post=$this->getRequest()->getPost();
			if(!$db->isUserTypeExist($post['user_type'])){
				$id=$db->insertUserType($post);
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS", '/rsvacl/usertype/index');
			}else {
				Application_Form_FrmMessage::message('User type had existed already');
			}
		}
		$form=new Rsvacl_Form_FrmUserType();
		$this->view->form=$form;
	}
    public function editAction()
    {	
    	$user_type_id=$this->getRequest()->getParam('id');
    	if(!$user_type_id)$user_type_id=0;
   		$form = new Rsvacl_Form_FrmUserType();
    	$db = new Rsvacl_Model_DbTable_DbUserType();
        $rs = $db->getUserTypeInfo('SELECT * FROM tb_acl_user_type where user_type_id='.$user_type_id);
		Application_Model_Decorator::setForm($form, $rs);
		Application_Model_Decorator::removeAllDecorator($form);

    	$this->view->form = $form;
    	$this->view->user_id = $user_type_id;
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    	if($this->getRequest()->isPost())
		{
			$post=$this->getRequest()->getPost();
			if($rs[0]['user_type']==$post['user_type']){
				$db->updateUserType($post,$rs[0]['user_type_id']);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS", '/rsvacl/usertype/index');
			}else{
				if(!$db->isUserTypeExist($post['user_type'])){
					$db->updateUserType($post,$rs[0]['user_type_id']);
					Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS", '/rsvacl/usertype/index');
				}else {
					Application_Form_FrmMessage::message('User had existed already');
				}
			}
		}
    }
}