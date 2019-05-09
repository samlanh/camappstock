<?php

class Rsvacl_UseraccessController extends Zend_Controller_Action
{
    public function init()
    {
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());  
    }
    public function indexAction()
    {
    	try{
    	$tr = Application_Form_FrmLanguages::getCurrentlanguage();
        $db = new Rsvacl_Model_DbTable_DbUserType();        
        $userAccessQuery = "SELECT user_type_id, user_type, status from tb_acl_user_type";
        $rows = $db->getUserTypeInfo($userAccessQuery);
        if($rows){
        	$imgnone='<img src="'.BASE_URL.'/images/icon/none.png"/>';
        	$imgtick='<img src="'.BASE_URL.'/images/icon/tick.png"/>';
        	        	        	
        	foreach ($rows as $i =>$row){
        		if($row['status'] == 1){
        			$rows[$i]['status'] = $imgtick;
        		}
        		else{
        			$rows[$i]['status'] = $imgnone;
        		}
        	}
        	$link = array("rsvacl","useraccess","view");
        	$links = array('user_type'=>$link);
        	$list=new Application_Form_Frmlist();        	
        	$columns=array('USER_TYPE','STATUS');        	
        	$this->view->form=$list->getCheckList('radio', $columns, $rows,$links);
        	}else {$this->view->form = $tr->translate('NO_RECORD_FOUND');}
        }catch (Exception $e){
        	Application_Form_FrmMessage::messageError("INSERT_ERROR");
        	Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
        }
    }
    
public function viewAction()
    {   
    	if($this->getRequest()->getParam('id')){    		
    		$id = $this->getRequest()->getParam('id');
    		$db = new Rsvacl_Model_DbTable_DbUserType();
    		$userAccessQuery = "SELECT user_type_id, user_type, status from tb_acl_user_type where user_type_id=".$id;
    		$rows = $db->getUserTypeInfo($userAccessQuery);
    		$this->view->rs=$rows;
    		$this->view->user_type = $id;
    		
    		$gc = new Application_Model_GlobalClass();
    		$sql = "SELECT DISTINCT acl.`module` FROM `tb_acl_acl` AS acl";
    		$this->view->optoin_mod =  $gc->getOptonsHtml($sql, "module", "module");
    		$sql = "SELECT DISTINCT acl.`controller` FROM `tb_acl_acl` AS acl WHERE acl.`status` = 1";
    		$this->view->optoin_con =  $gc->getOptonsHtml($sql, "controller", "controller");
    		$sql = "SELECT DISTINCT acl.`action` FROM `tb_acl_acl` AS acl WHERE acl.`status` = 1";
    		$this->view->optoin_act =  $gc->getOptonsHtml($sql, "action", "action");
    		$this->view->optoin_status =  $gc->getYesNoOption();
    		 
    		$where = " ";
    		$status = null;
    		 
    		if($this->getRequest()->isPost()){
    			$post = $this->getRequest()->getPost();
    		}else{
    			$post =array(
    					'fmod'=>'',
    					'fcon'=>'',
    					'fact'=>'',
    					'fstatus'=>'',
    			);
    		}
    		$this->view->data = $post;
    		 
    		$db_acl=new Application_Model_DbTable_DbGlobal();
    		 
    		$sqlNotParentId = "SELECT user_type_id FROM `tb_acl_user_type` WHERE `parent_id` =".$id;
    		$notParentId = $db_acl->getGlobalDb($sqlNotParentId);
    		$usernotparentid = $notParentId[0]['user_type_id'];
    		 
    		if($id == 1){
    			$sql = "select acl.acl_id,acl.label,CONCAT(acl.module,'/', acl.controller,'/', acl.action) AS user_access , acl.status, acl.module, acl.is_menu
    			from tb_acl_acl as acl
    			WHERE 1 " . $where;
    		}
    		else {
    			$sql="SELECT acl.acl_id,acl.label, CONCAT(acl.module,'/', acl.controller,'/', acl.action) AS user_access, acl.status, acl.module, acl.is_menu
    			FROM tb_acl_user_access AS ua
    			INNER JOIN tb_acl_user_type AS ut ON (ua.user_type_id = ut.parent_id)
    			INNER JOIN tb_acl_acl AS acl ON (acl.acl_id = ua.acl_id) WHERE ut.user_type_id =".$id . $where;
    		}
    		
    		$order = " order by acl.module ASC, acl.rank ASC,acl.controller ASC,acl.is_menu DESC ";
    		$acl=$db_acl->getGlobalDb($sql.$order);
    		$acl = (is_null($acl))? array(): $acl;
//     		$this->view->acl=$acl;
    		 
    		if(!$usernotparentid){
    			$sql_acl = "SELECT acl.acl_id,acl.label, CONCAT(acl.module,'/', acl.controller,'/', acl.action) AS user_access, acl.status, acl.status , acl.is_menu
    			FROM tb_acl_user_access AS ua
	    			INNER JOIN tb_acl_user_type AS ut ON (ua.user_type_id = ut.user_type_id)
	    			INNER JOIN tb_acl_acl AS acl ON (acl.acl_id = ua.acl_id) WHERE ua.user_type_id =".$id . $where;
    		}else{
    			$sql_acl = "SELECT acl.acl_id,acl.label, CONCAT(acl.module,'/', acl.controller,'/', acl.action) AS user_access, acl.status, acl.status , acl.is_menu
	    			FROM tb_acl_user_access AS ua
	    			INNER JOIN tb_acl_user_type AS ut ON (ua.user_type_id = ut.parent_id)
	    			INNER JOIN tb_acl_acl AS acl ON (acl.acl_id = ua.acl_id) WHERE ua.user_type_id =".$id . $where;
    		}
    		 
    		$acl_name = $db_acl->getGlobalDb($sql_acl.$order);
    		$acl_name = (is_null($acl_name))? array(): $acl_name;
    		 
    		$imgnone='<img src="'.BASE_URL.'/images/icon/none.png"/>';
    		$imgtick='<img src="'.BASE_URL.'/images/icon/tick.png"/>';
    		 
    		$rows= array();
    		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
    		foreach($acl as $com){
    			$img='<img src="'.BASE_URL.'/images/icon/none.png" id="img_'.$com['acl_id'].'" onclick="changeStatus('.$com['acl_id'].','.$id.');" class="pointer"/>';
    			$tmp_status = 0;
    			foreach($acl_name as $read){
    				if($read['acl_id']==$com['acl_id']){
    					$img='<img src="'.BASE_URL.'/images/icon/tick.png" id="img_'.$com['acl_id'].'" onclick="changeStatus('.$com['acl_id'].', '.$id.');" class="pointer"/>';
    					$tmp_status = 1;
    					break;
    				}
    					
    			}
    			if(!empty($status) || $status === 0){
    				if($tmp_status !== $status) continue;
    			}
    			$rows[] = array("acl_id"=>$com['acl_id'],"label"=>$tr->translate($com['label']), "url"=>$com['user_access'], "img"=>$img,"module"=>$com['module'] , "is_menu"=>$com['is_menu']) ;
    		}
    		
    		$this->view->rows = $rows;
//     		$list = new Application_Form_Frmlist();
//     		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
//     		$columns=array("Label",$tr->translate('URL'), $tr->translate('STATUS'));
//     		$this->view->list = $list->getCheckList('radio', $columns, $rows);
    	}  	 
    }
	public function addAction()
		{
			if($this->getRequest()->isPost())
			{
				$db=new Rsvacl_Model_DbTable_DbUserAccess();	
				$post=$this->getRequest()->getPost();			
				$id=$db->insertUserAccess($post);
			}
			$form=new Rsvacl_Form_FrmUserAccess();
			$db = new Application_Model_DbTable_DbGlobal();
			$this->view->form=$form;
		}
    
public function editAction()
    {	
    	$id=$this->getRequest()->getParam('id');
    	if(!$id)$id=0;    	
		$session=new Zend_Session_Namespace('auth');
		$session->user_type_id=$id;
    	$session->lock();	
    	
   		$form = new RsvAcl_Form_FrmUserAccess();
   		//echo "it works"; exit;
   		
    	$db = new RsvAcl_Model_DbTable_DbUserAccess();
    	$sql = "select user_type_id, user_type  from tb_acl_user_type where user_type_id=".$id;
        $rs = $db->getUserAccessInfo($sql);
        //print_r($rs); exit;
               
	
		//Sophen add here
			//to assign project list in view
			$db_acl=new Application_Model_DbTable_DbGlobal();
			
			$sqlNotParentId = "SELECT user_type_id FROM `tb_acl_user_type` WHERE `parent_id` =".$id;
			$notParentId = $db_acl->getGlobalDb($sqlNotParentId);
			$usernotparentid = $notParentId[0]['user_type_id'];			    	
	    	//print $usernotparentid; exit;
	    	
		if($id == 1){
				$sql_acl = "select acl.acl_id,CONCAT(acl.module,'/', acl.controller,'/', acl.action) AS user_access from tb_acl_acl as acl";
		}if(!$usernotparentid){
				$sql_acl = "SELECT acl.acl_id, CONCAT(acl.module,'/', acl.controller,'/', acl.action) AS user_access, acl.status FROM tb_acl_user_access AS ua 
					INNER JOIN tb_acl_user_type AS ut ON (ua.user_type_id = ut.user_type_id)
					INNER JOIN tb_acl_acl AS acl ON (acl.acl_id = ua.acl_id) WHERE ua.user_type_id =".$id;
		}else {
    			$sql_acl = "SELECT acl.acl_id, CONCAT(acl.module,'/', acl.controller,'/', acl.action) AS user_access, acl.status FROM tb_acl_user_access AS ua 
					INNER JOIN tb_acl_user_type AS ut ON (ua.user_type_id = ut.parent_id)
					INNER JOIN tb_acl_acl AS acl ON (acl.acl_id = ua.acl_id) WHERE ua.user_type_id =".$id;
		}
	    //print $sql_acl; exit;
		$acl_name=$db_acl->getGlobalDb($sql_acl);
		
		//print_r($acl_name); exit;
		
		if($acl_name!=''){ $form->setAcl($acl_name);}
				
		Application_Model_Decorator::setForm($form, $rs);
		
		$this->view->form = $form;
		
		$rows= array();
		for($i=1;$i<=$form->getPlus();$i++){
			$rows[] = array($i, $form->getElement('acl_id_'.$i)->getLabel(), $form->getElement('acl_id_'.$i)) ;
		}
		
		$list=new Application_Form_Frmlist();
		$tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$columns=array($tr->translate('URL'), $tr->translate('STATUS'));
		$this->view->form_layout = $list->getCheckList('radio', $columns, $rows);
		
    	$this->view->id = $id;
    	
    	if($this->getRequest()->isPost())
		{
			$post=$this->getRequest()->getPost();
			//if($rs[0]['']==$post['username']){	
					$db_user = new RsvAcl_Model_DbTable_DbUserType();

					print_r($post); exit;
					//print $rs[0]['user_type_id']; exit;
					$db_user->updateUserTypeAccess($post['user_type'],$rs[0]['user_type_id']);								
					
					$db->assignAcl($post,$rs[0]['user_type_id'], $form->getPlus());
					 		//write log file 
				             $userLog= new Application_Model_Log();
				    		 $userLog->writeUserLog($id);
				     	  //End write log file
					//Application_Form_FrmMessage::message('One row affected!');
					Application_Form_FrmMessage::redirector('/rsvacl/user-access/index');																																				
				
			/*}else{
				if(!$db->isUserExist($post['username'])){
					$db->updateUser($post,$rs[0]['user_id']);
					 //write log file 
				        //$userLog= new RsvLogging_Model_RsvLogging();
				    	//$userLog->writeUserLog($user_id);
				     //End write log file
					Application_Form_FrmMessage::message('One row affected!');
					Application_Form_FrmMessage::redirector('/rsvAcl/user/index');						
				}else {
					Application_Form_FrmMessage::message('User had existed already');
				}
			}*/
		}
    }

    public function updateStatusAction(){
    	if($this->getRequest()->isPost()){
    		try{
    		$post=$this->getRequest()->getPost();
    		
    		$db = new Rsvacl_Model_DbTable_DbUserAccess();
    		$user_type_id =  $post['user_type_id'];
    		$acl_id = $post['acl_id'];
    		$status = $post['status'];
    		
    		$data=array('acl_id'=>$acl_id, 'user_type_id'=>$user_type_id);
    		
    		if($status == "yes"){
    			$where="user_type_id='".$user_type_id."' AND acl_id='". $acl_id . "'";
    			$db->delete($where);    		
    			echo "no";	
    		}
    		elseif($status == "no"){
    			$id = $db->insert($data); 
    			echo "yes";
    		}
    		//write log file
    		}catch(Exception $e){
    		$err =$e->getMessage();
    		Application_Model_DbTable_DbUserLog::writeMessageError($err);
    		//$userLog= new Application_Model_Log();
    		//$userLog->writeUserLog($acl_id);
    		}
    	}
    	exit();
    }
}

