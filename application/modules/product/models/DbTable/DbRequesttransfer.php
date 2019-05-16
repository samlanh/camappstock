<?php

class Product_Model_DbTable_DbRequesttransfer extends Zend_Db_Table_Abstract
{
	protected $_name = "tb_product_transfer";
	public function setName($name)
	{
		$this->_name=$name;
	}
	function getTitleReport($id){
		$db = $this->getAdapter();
		$sql ="SELECT s.`branch_code`,s.`logo`,s.`prefix`,name FROM `tb_sublocation` AS s WHERE s.`id`=$id";
		return $db->fetchRow($sql);
	}
	function getRequestTransfer($data){
		$start_date = date("Y-m-d",strtotime($data["start_date"]));
		$end_date = date("Y-m-d",strtotime($data["end_date"]));
		$db = $this->getAdapter();
		$sql = "SELECT
		rt.id,
		rt.`tran_no`,
		rt.date_request,
		rt.date_in,
		(SELECT s.`name` FROM `tb_sublocation` AS s WHERE s.`id`=rt.`cur_location` LIMIT 1) AS re_tranlocation ,
		(SELECT s.`name` FROM `tb_sublocation` AS s WHERE s.`id`=rt.`tran_location` LIMIT 1) AS to_tranlocation ,
			
		(SELECT v.name_en FROM `tb_view` AS v WHERE v.key_code = rt.`appr_status` AND v.type=7 LIMIT 1) AS appr_status,
		(SELECT v.name_en FROM `tb_view` AS v WHERE v.key_code = rt.`appr_pedding` AND v.type=13 LIMIT 1) AS appr_pedding,
		(SELECT u.`fullname` FROM  `tb_acl_user` AS u WHERE u.`user_id`=rt.`user_id` LIMIT 1) AS `user`,
		(SELECT name_en FROM `tb_view` WHERE type=5 AND key_code=status LIMIT 1) AS status
		FROM
		`tb_request_transfer` AS rt
		WHERE rt.`date_request` BETWEEN '".$start_date."' AND '".$end_date."'";
		$where = '';
		if($data["avd_search"]!=""){
			$s_where=array();
			$s_search = addslashes(trim($data['avd_search']));
			$s_where[]= " rt.tran_no LIKE '%{$s_search}%'";
			$s_where[]= " rt.remark LIKE '%{$s_search}%'";
			$where.=' AND ('.implode(' OR ', $s_where).')';
		}
		if($data["branch"]!=-1){
			$where.=' AND rt.`cur_location`='.$data["branch"];
		}
// 		if($data["check_stat"]>-1){
// 			$where.=' AND rt.`appr_pedding`='.$data["check_stat"];
// 		}
		$where.=" ORDER BY rt.id DESC ";
// 		echo $sql.$where;
		return $db->fetchAll($sql.$where);
	}
	public function addRequest($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		$date =new Zend_Date();
		try{
			$user_info = new Application_Model_DbTable_DbGetUserInfo();
			$result = $user_info->getUserInfo();
	
			$arr = array(
					'tran_no'		=>	$data["tran_num"],
					'cur_location'	=>	$data["from_loc"],
					'tran_location'	=>	$data["to_loc"],
					'date_request'	=>	$data["date_request"],
					'date_in'		=>	$data["date_in"],
					'remark'		=>	$data["remark"],
					'user_id'		=>	$result["user_id"],
					'appr_status'	=>	0,
					'appr_pedding'	=>	1
			);
			$this->_name="tb_request_transfer";
			$id = $this->insert($arr);
	
			if(!empty($data['identity'])){
				$identitys = explode(',',$data['identity']);
				foreach($identitys as $i)
				{
					$arr_ti = array(
							'tran_id'		=>	$id,
							'pro_id'		=>	$data["pro_id_".$i],
							'qty_unit'		=>	$data["qty_unit_".$i],
							'qty_per_unit'	=>	$data["qty_per_unit_".$i],
							'qty_measure'	=>	$data["qty_measure_".$i],
							'qty'			=>	$data["qty_tran_".$i],
							'remark'		=>	$data["remark_".$i],
					);
					$this->_name="tb_request_transfer_item";
					$this->insert($arr_ti);
				}
			}
			$db->commit();
		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e);
		}
	}
	public function editRequest($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$user_info = new Application_Model_DbTable_DbGetUserInfo();
			$result = $user_info->getUserInfo();
			$arr = array(
					'cur_location'	=>	$data["from_loc"],
					'tran_location'	=>	$data["to_loc"],
					'date_request'	=>	$data["date_request"],
					'date_in'		=>	$data["date_in"],
					'remark'		=>	$data["remark"],
					'user_id'		=>	$result["user_id"],
					'appr_status'	=>	0,
					'appr_pedding'	=>	1
			);
			$this->_name="tb_request_transfer";
			$where = "id=".$data["id"];
			$this->update($arr,$where);
	
			$sql ="DELETE FROM tb_request_transfer_item WHERE tran_id= "."'".$data["id"]."'";
			$db->query($sql);
	
			if(!empty($data['identity'])){
				$identitys = explode(',',$data['identity']);
				foreach($identitys as $i)
				{
					$arr_ti = array(
							'tran_id'		=>	$data["id"],
							'pro_id'		=>	$data["pro_id_".$i],
							'qty_unit'		=>	$data["qty_unit_".$i],
							'qty_per_unit'	=>	$data["qty_per_unit_".$i],
							'qty_measure'	=>	$data["qty_measure_".$i],
							'qty'			=>	$data["qty_tran_".$i],
							'remark'		=>	$data["remark_".$i],
					);
					$this->_name="tb_request_transfer_item";
					$this->insert($arr_ti);
				}
			}
			$db->commit();
		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e);
		}
	}
	function getReqTransferById($id){
		$db = $this->getAdapter();
		$sql = "SELECT
					t.id,
					t.`cur_location`,
					t.`tran_location`,
					(SELECT `name` FROM `tb_sublocation` AS s WHERE s.`id`=t.`cur_location` LIMIT 1) AS re_from,
					(SELECT `name` FROM `tb_sublocation` AS s WHERE s.`id`=t.`tran_location` LIMIT 1) AS re_to,
					t.`tran_no` AS re_no,
					t.date_request,
					t.appr_status,
					t.`date_in` AS date_in,
					t.`remark`,
					t.`status`
					FROM
					`tb_request_transfer` AS t
			WHERE t.`id` =$id";
		return $db->fetchRow($sql);
	}
	function getReqTransferDetail($id){
		$db = $this->getAdapter();
		$sql = "SELECT
			t.`pro_id`,
			(SELECT p.item_name FROM `tb_product` AS p WHERE p.id = t.`pro_id` LIMIT 1) AS item_name,
			(SELECT p.item_code FROM `tb_product` AS p WHERE p.id = t.`pro_id` LIMIT 1) AS item_code,
			(SELECT p.unit_label FROM `tb_product` AS p WHERE p.id = t.`pro_id` LIMIT 1) AS unit_label,
			(SELECT name FROM tb_measure as m where m.id=(SELECT p.measure_id FROM `tb_product` AS p WHERE p.id = t.`pro_id` LIMIT 1)) AS measure,
			t.`qty`,
			t.`qty_unit`,
			t.`qty_per_unit`,
			t.`qty_measure`,
			t.`remark`
			FROM
			`tb_request_transfer_item` AS t
		WHERE t.`tran_id` = $id";
	return $db->fetchAll($sql);
	}
	function getRequestPrint($id){
		$db = $this->getAdapter();
		$sql = "SELECT
				t.`pro_id`,
					(SELECT rt.`tran_no` FROM `tb_request_transfer` AS rt WHERE rt.id=t.`tran_id` ) AS req_no,
					(SELECT rt.`cur_location` FROM `tb_request_transfer` AS rt WHERE rt.id=t.`tran_id` ) AS cur_location,
					(SELECT rt.`date_request` FROM `tb_request_transfer` AS rt WHERE rt.id=t.`tran_id` ) AS date_request,
					(SELECT rt.`date_in` FROM `tb_request_transfer` AS rt WHERE rt.id=t.`tran_id` ) AS date_in,
					(SELECT s.name FROM `tb_sublocation`  AS s WHERE s.id=(SELECT rt.`cur_location` FROM `tb_request_transfer` AS rt WHERE rt.id=t.`tran_id` LIMIT 1 ) LIMIT 1) AS req_location,
					(SELECT s.name FROM `tb_sublocation`  AS s WHERE s.id=(SELECT rt.`tran_location` FROM `tb_request_transfer` AS rt WHERE rt.id=t.`tran_id` LIMIT 1 ) LIMIT 1) AS tran_location,
					(SELECT p.item_name FROM `tb_product` AS p WHERE p.id = t.`pro_id` LIMIT 1) AS item_name,
					(SELECT p.item_code FROM `tb_product` AS p WHERE p.id = t.`pro_id` LIMIT 1) AS item_code,
					(SELECT p.unit_label FROM `tb_product` AS p WHERE p.id = t.`pro_id` LIMIT 1) AS unit_label,
					(SELECT NAME FROM tb_measure AS m WHERE m.id=(SELECT p.measure_id FROM `tb_product` AS p WHERE p.id = t.`pro_id` LIMIT 1)) AS measure,
					t.`qty`,
					t.`qty_unit`,
					t.`qty_per_unit`,
					t.`qty_measure`,
					t.`remark`
		FROM
			`tb_request_transfer_item` AS t
		WHERE t.`tran_id` =$id";
		return $db->fetchAll($sql);
	}
	
	public function ApproveRequest($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$user_info = new Application_Model_DbTable_DbGetUserInfo();
			$result = $user_info->getUserInfo();
			if($data["approved_name"]==1){//approved
				$appr_status = 0;
				$appr_pedding = 2;
			}else{//reject
				$appr_status = 2;
				$appr_pedding = 1;
			}
			$arr = array(
					'appr_status'	=>	$appr_status,
					'appr_pedding'	=>	$appr_pedding,
					'approved_by'	=>	$result["user_id"],
			);
			$this->_name="tb_request_transfer";
			$where = "id=".$data["id"];
			$this->update($arr,$where);
			$db->commit();
		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e);
		}
	}
	public function makeTransfer($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$user_info = new Application_Model_DbTable_DbGetUserInfo();
			$result = $user_info->getUserInfo();
				
			$sql = "SELECT t.`id` FROM `tb_product_transfer` AS t WHERE t.`re_id`='".$data["id"]."'";
			$rs = $db->fetchOne($sql);
			if(empty($rs)){
				if(!empty($data['identity'])){
					$arr = array(
							'tran_no'		=>	$data["tran_num"],
							'cur_location'	=>	$data["from_loc"],
							'tran_location'	=>	$data["to_loc"],
							're_id'			=>	$data["id"],
							'date'			=>	$data["tran_date"],
							're_date'		=>	$data["re_date"],
							'date_mod'		=>	date('Y-m-d'),
							'remark'		=>	$data["remark"],
							'user_mod'		=>	$result["user_id"],
					);
					$id = $this->insert($arr);
						
					$arr_history = array(
							'tran_id'		=>	$data["id"],
							'tran_no'		=>	$data["tran_num"],
							'cur_location'	=>	$data["from_loc"],
							'tran_location'	=>	$data["to_loc"],
							'type'			=>	2,
							'date'			=>	date('Y-m-d'),
							'date_tran'		=>	date("Y-m-d",strtotime($data["tran_date"])),
							'remark'		=>	$data["remark"],
							'user_id'		=>	$result["user_id"],
							'action'		=>	1,
					);
					$this->_name="tb_transfer_history";
					$his_id = $this->insert($arr_history);
	
	
					$identitys = explode(',',$data['identity']);
					foreach($identitys as $i)
					{
						$arr_ti = array(
								'tran_id'		=>	$id,
								'pro_id'		=>	$data["pro_id_".$i],
								'qty_unit'		=>	$data["qty_unit_".$i],
								'qty_per_unit'	=>	$data["qty_per_unit_".$i],
								'qty_measure'	=>	$data["qty_measure_".$i],
								'qty'			=>	$data["qty_tran_".$i],
								'qty_request'	=>	$data["qty_request_".$i],
								'remark'		=>	$data["remark_".$i],
						);
						$this->_name="tb_transfer_item";
						$this->insert($arr_ti);
	
						$arr_ti_his = array(
								'his_id'		=>	$his_id,
								'tran_id'		=>	$id,
								'pro_id'		=>	$data["pro_id_".$i],
								'qty_unit'		=>	$data["qty_unit_".$i],
								'qty_per_unit'	=>	$data["qty_per_unit_".$i],
								'qty_measure'	=>	$data["qty_measure_".$i],
								'request_qty'	=>	$data["qty_request_".$i],
								'transfer_qty'	=>	$data["qty_tran_".$i],
								'old_qty'		=>	$data["qty_tran_".$i],
								'new_qty'		=>	$data["qty_tran_".$i],
								'remark'		=>	$data["remark_".$i],
						);
						$this->_name="tb_transfer_history_item";
						$this->insert($arr_ti_his);
	
						$rs_from = $this->getProductExist($data["pro_id_".$i],$data["to_loc"]);
	
						if(!empty($rs_from)){
							$arr_fo = array(
									'qty'	=>	$rs_from["qty"]-$data["qty_tran_".$i],
							);
							$this->_name="tb_prolocation";
							$where = array('pro_id=?'=>$data["pro_id_".$i],"location_id=?"=>$data["to_loc"]);
							$this->update($arr_fo, $where);
						}else{
							$arr_to = array(
									'pro_id'			=>	$data["pro_id_".$i],
									'location_id'		=>	$data["to_loc"],
									'qty'				=>	-$data["qty_tran_".$i],
									'qty_warning'		=>	0,
									'last_mod_userid'	=>	$result["user_id"],
									'last_mod_date'		=>	new Zend_Date(),
							);
							$this->_name="tb_prolocation";
							$this->insert($arr_to);
						}
						$arr = array(
								'appr_status'	=>	0,
								'appr_pedding'	=>	3,
								'is_transfer'	=>	$id,
						);
						$this->_name="tb_request_transfer";
						$where = "id=".$data["id"];
						$this->update($arr,$where);
					}
				}
			}
			$db->commit();
		}catch (Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e);
		}
	}
	function getProductExist($pro_id,$loc_id){
		$db = $this->getAdapter();
		$sql = "SELECT pl.`pro_id`,pl.`qty`,pl.`location_id` FROM `tb_prolocation` AS pl WHERE pl.`pro_id`=$pro_id AND pl.`location_id`=$loc_id";
		return $db->fetchRow($sql);
	}
}