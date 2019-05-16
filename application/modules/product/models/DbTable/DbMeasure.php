<?php

class Product_Model_DbTable_DbMeasure extends Zend_Db_Table_Abstract
{
	protected $_name = "tb_measure";
	
	public function getUserId(){
		return Application_Model_DbTable_DbGlobal::GlobalgetUserId();
	}
	public function add($data){
		$db = $this->getAdapter();
		$arr = array(
				'name'			=>	$data["measure_name"],
				'date'			=>	new Zend_Date(),
				'status'		=>	1,
				'remark'		=>	$data["remark"],
		);
		$this->_name = "tb_measure";
		$this->insert($arr);
	}
	public function edit($data){
		$db = $this->getAdapter();
		$arr = array(
				'name'			=>	$data["measure_name"],
				'date'			=>	new Zend_Date(),
				'status'		=>	$data["status"],
				'remark'		=>	$data["remark"],
		);
		$this->_name = "tb_measure";
		$where = $db->quoteInto("id=?", $data["id"]);
		$this->update($arr, $where);
	}
	public function addNew($data){
		$db = $this->getAdapter();
		$arr = array(
				'name'			=>	$data["measure_name"],
// 				'parent_id'		=>	$data["parent"],
				'date'			=>	new Zend_Date(),
				'status'		=>	$data["status"],
				'remark'		=>	$data["remark"],
		);
		$this->_name = "tb_measure";
		return $this->insert($arr);
	}
	public function getAllMeasure($data=null){
		$db = $this->getAdapter();
		$sql = " SELECT m.id,m.`name`,m.`status`,m.`remark` FROM `tb_measure` AS m ";
		$where = ' WHERE 1 ';
		if($data["ad_search"]!="" AND !empty($data["ad_search"])){
			$string = str_replace(' ','',$data['ad_search']);
			$s_where=array();
			$s_search = addslashes(trim($string));
			$s_where[]=" REPLACE(m.name,' ','') LIKE '%{$s_search}%'";
			$where.=' AND ('.implode(' OR ', $s_where).')';
		}
		if($data["status"]!=""){
			$where.=' AND `status`='.$data["status"];
		}
		return $db->fetchAll($sql.$where);
	}
	
	public function getMeasure($id){
		$db = $this->getAdapter();
		$sql = "SELECT m.id,m.`name`,m.`status`,m.`remark` FROM `tb_measure` AS m  WHERE m.`id`= $id";
		return $db->fetchRow($sql);
	}
}