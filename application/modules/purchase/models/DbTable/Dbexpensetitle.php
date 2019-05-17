<?php

class Purchase_Model_DbTable_Dbexpensetitle extends Zend_Db_Table_Abstract
{
	protected $_name = "tb_expensetitle";
	
	public function getUserId(){
		return Application_Model_DbTable_DbGlobal::GlobalgetUserId();
	}
	public function add($data){
		$db = $this->getAdapter();
		$arr = array(
				'title'		=> $data["title"],
				'title_en'	=> $data["name_en"],
				'parent_id'	=> $data["parent"],
				'status'	=> 1,
				'date'		=> date("Y-m-d"),
				'user_id'	=> $this->getUserId()
		);
		$this->insert($arr);
	}
	public function edit($data){
		$db = $this->getAdapter();
		$arr = array(
				'title'	=>	$data["title"],
				'title_en'	=>	$data["name_en"],
				'parent_id'	=> $data["parent"],
				'status'		=>	$data["status"],
				'date'		=>	date("Y-m-d"),
				'user_id'=>$this->getUserId()
		);
		$where = $db->quoteInto("id=?", $data["id"]);
		$this->update($arr, $where);
	}
	public function getAllTerm(){
		$db = $this->getAdapter();
		$sql = "SELECT 
				  t.id,
				  t.title,
				  t.title_en,
				  (SELECT title FROM tb_expensetitle WHERE tb_expensetitle.id = t.parent_id LIMIT 1) AS parent_name,
				  t.status	  
				FROM
				  tb_expensetitle AS t
			WHERE title!='' OR title_en!=''	ORDER BY t.parent_id ";
		return $db->fetchAll($sql);
	}
	public function getParentCateExpense($cate_id='',$parent = 0, $spacing = '', $cate_tree_array = '',$option = null){
		$db=$this->getAdapter();
		if (!is_array($cate_tree_array)){
			$cate_tree_array = array();
		}
		$sql = " SELECT id , title as name from tb_expensetitle where status=1 AND `parent_id` = $parent ";
		if (!empty($cate_id)){
			$sql.=" AND id != $cate_id";
		}
		$query = $db->fetchAll($sql);
		$rowCount = count($query);
	
		$id='';
		if ($rowCount > 0) {
			foreach ($query as $row){
				$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
				$cate_tree_array = $this->getParentCateExpense($cate_id,$row['id'], $spacing . ' - ', $cate_tree_array,$option);
			}
		}
	
		return $cate_tree_array;
	
	}
	public function getTermById($id){
		$db = $this->getAdapter();
		$sql = "SELECT t.* FROM tb_expensetitle AS t WHERE t.id= $id";
		return $db->fetchRow($sql);
	}
	public function getTermcondictionType(){
		$db = $this->getAdapter();
		$sql = "SELECT v.`key_code`,v.`name_en` FROM tb_view AS v WHERE v.`type`=10";
		return $db->fetchAll($sql);
	}
}