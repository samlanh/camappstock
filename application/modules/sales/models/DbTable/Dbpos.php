<?php

class Sales_Model_DbTable_Dbpos extends Zend_Db_Table_Abstract
{
	protected $_name="tb_invoice";
	function getAllProductName(){
		$sql="SELECT id,CONCAT(item_name,',',item_code) AS name FROM `tb_product` WHERE item_name!='' AND status=1 ";
		//$sql="SELECT id,CONCAT(item_name,' ',barcode) AS name FROM `tb_product` WHERE item_name!='' AND status=1 ";
		return $this->getAdapter()->fetchAll($sql);
	}
	function getAllCustomerName(){
		$sql="SELECT id,cust_name AS name FROM `tb_customer` WHERE status=1 AND cust_name!='' ORDER BY id DESC ";
		return $this->getAdapter()->fetchAll($sql);
	}
	function getProductById($product_id,$branch_id,$agreement_id=null,$customer_id=null){
		$result= array();
		if($agreement_id>0){
			$sql="
				SELECT ag.pro_id ,
					p.price AS cost_price,
					ag.price,
					p.qty_perunit,
					(SELECT tb_measure.name FROM `tb_measure` WHERE tb_measure.id=p.measure_id) as measue_name,
					(SELECT qty FROM `tb_prolocation` WHERE pro_id=ag.pro_id AND location_id=$branch_id LIMIT 1) AS qty
			FROM `tb_agreement` AS a,
				`tb_agreement_detail`  AS ag,
				`tb_product` AS p
			WHERE
				a.id=ag.agreement_id
				AND p.id=ag.pro_id
				AND a.id=$agreement_id
				AND ag.pro_id=$product_id";
			$result = $this->getAdapter()->fetchRow($sql);	
		}
		
		if(!empty($result)){
			return $result;
		}else{
			if($customer_id!=null){
				$sql="	SELECT *,price as cost_price,
				(SELECT price FROM `tb_product_price` WHERE type_id=(SELECT customer_level FROM `tb_customer` WHERE id=$customer_id) AND pro_id=$product_id LIMIT 1) as price,
				(SELECT qty FROM `tb_prolocation` WHERE pro_id=$product_id AND location_id=$branch_id LIMIT 1) AS qty,
				(SELECT tb_measure.name FROM `tb_measure` WHERE tb_measure.id=measure_id) as measue_name
				FROM tb_product WHERE id=$product_id LIMIT 1";
				return $this->getAdapter()->fetchRow($sql);
			}else{
				$sql="	SELECT *,price as cost_price,
				(SELECT price FROM `tb_product_price` WHERE type_id=1 AND pro_id=$product_id LIMIT 1) as price,
				(SELECT qty FROM `tb_prolocation` WHERE pro_id=$product_id AND location_id=$branch_id LIMIT 1) AS qty,
				(SELECT tb_measure.name FROM `tb_measure` WHERE tb_measure.id=measure_id) as measue_name
				FROM tb_product WHERE id=$product_id LIMIT 1";
				return $this->getAdapter()->fetchRow($sql);
				//join with above sql
			}
			
		}
		
	}
	function getProductByProductId($product_id,$location){
		$sql=" SELECT * FROM tb_prolocation WHERE pro_id = $product_id AND location_id = $location ";
		return $this->getAdapter()->fetchRow($sql);
	 }
	public function addSaleOrder($data)
	{
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$arr = array(
					'key_value'=>$data['exchange_rate']
					);
			$this->_name='tb_setting';		    
			$where ="code='exchange_rate'";
			$this->update($arr, $where);
			
			$db_global = new Application_Model_DbTable_DbGlobal();
			$session_user=new Zend_Session_Namespace('auth');
			$userName=$session_user->user_name;
			$GetUserId= $session_user->user_id;
			$dbc=new Application_Model_DbTable_DbGlobal();
			$so = $dbc->getSalesNumber($data["branch_id"]);
	
			$info_purchase_order=array(
					"customer_id"   => $data['customer_id'],
					"branch_id"     => $data["branch_id"],
					"sale_no"       => $so,//$data['txt_order'],
					"date_sold"     => $data['sale_date'],
					"all_total"     => $data['total_dollar'],
					'discount_value' =>empty($data['discount'])?0:$data['discount'],
					//"discount_type"  => 	$data['discount_type'],
					//"saleagent_id"  => 	$data['saleagent_id'],
					//"tax"			 =>     $data["total_tax"],
					//"remark"       => 	$data['remark'],
					//"currency_id"    => 1,//$data['currency'],
					"jcnumber"    => $data['cjnumber'],
					"mileage"    => $data['mileage'],
					'paid_dollar'=>$data['total_paid'],
					"paid"           => $data['total_paid'],
					"balance"        => $data['balance'],
					'all_totalafter'=> $data['balance'],
					"net_total"      => $data['sub_total'],
					"user_mod"       => $GetUserId,
					'term_condition' => $data['term_condition'],
					'pending_status' =>3,
					"date"           => date("Y-m-d"),
					'agreement_id'   => 0,
			);
			
			$this->_name="tb_sales_order";
			$sale_id = $this->insert($info_purchase_order);
			
			if($data['total_paid']>0){
				$data['receipt'] = $db_global->getReceiptNumber(1);
				$info_purchase_order=array(
						"branch_id"   	=> 	$data["branch_id"],
						"customer_id"   => 	$data["customer_id"],
						"payment_type"  => 	1,
						"payment_id"    => 	1,
						"receipt_no"    => 	$data['receipt'],
						"receipt_date"  =>  date("Y-m-d"),
						"date_input"    =>  date("Y-m-d"),
						"total"         => 	$data['total_dollar'],
						"paid"          => 	$data["total_paid"],
						"paid_dollar"   => 	$data['total_paid'],
						"paid_riel"     => 	0,
						"balance"       => 	$data['balance'],
						"user_id"       => 	$GetUserId,
						'status'        =>1,
						"bank_name"     => 	'',
						"cheque_number" => 	'',
						"exchange_rate" => 	$data['exchange_rate'],						
				);
				$this->_name="tb_receipt";
				$reciept_id = $this->insert($info_purchase_order);
				
				$data_item= array(
						'receipt_id'  => $reciept_id,
						'invoice_id'  => $sale_id,
						'total'		  => $data['total_dollar'],
						'paid'	      => $data["total_paid"],
						'balance'	  => $data['balance'],
						'is_completed'=> ($data['balance']==0)?1:0,
						'status'      => 1,
						'date_input'  => date("Y-m-d"),
				);
				$this->_name='tb_receipt_detail';
				$this->insert($data_item);
			}
	
			$ids=explode(',',$data['identity']);
			foreach ($ids as $i)
			{
				$rs = $this->getProductByProductId($data['product_id'.$i], $data["branch_id"]);
				if(!empty($rs)){
					$this->_name='tb_prolocation';
					$arr = array(
							'qty'=>$rs['qty']-$data['qty_sold'.$i]
							);
					$where=" id =".$rs['id'];
					
					$this->update($arr, $where);
				}
				$data_item= array(
						'saleorder_id'=> $sale_id,
						'pro_id'	  => $data['product_id'.$i],
						'qty_unit'	  => $data['qty_'.$i],
						'qty_detail'  => empty($data['qtydetail_'.$i])?0:$data['qtydetail_'.$i],
						'qty_order'	  => $data['qty_sold'.$i],
						'price'		  => $data['price_'.$i],
						'old_price'   => $data['price_'.$i],
 						'cost_price'  => $data['cost_price'.$i],
						'sub_total'	  => $data['sub_total'.$i],
						'note'	  	  => $data['note'.$i],
						'staff_id'	  => $data['staff_'.$i],
						'disc_value'  => empty($data['discount_'.$i])?0:$data['discount_'.$i],
				);
				$this->_name='tb_salesorder_item';
				$this->insert($data_item);
			}
			$db->commit();
			return $sale_id;
		}catch(Exception $e){
			$db->rollBack();
			Application_Form_FrmMessage::message('INSERT_FAIL');
			$err =$e->getMessage();
			Application_Model_DbTable_DbUserLog::writeMessageError($err);
		}
	}
	function getCountReceiptbySale($saleId){
		$sql="SELECT COUNT(id) 
			FROM tb_receipt_detail 
				WHERE invoice_id=$saleId ";
		return $this->getAdapter()->fetchOne($sql);
	}
	function editSale($data){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$sale_id= $data['sale_id'];
			$rsdetail = $this->getInvoiceDetailById($sale_id);
			if(!empty($rsdetail)){
				foreach($rsdetail as $row){
					$rs = $this->getProductByProductId($row['pro_id'], 1);
					if(!empty($rs)){
						$this->_name='tb_prolocation';
						$arr = array(
								'qty'=>$rs['qty']+$row['qty_order']
						);
						$where=" id =".$rs['id'];
						$this->update($arr, $where);
					}
				}
			}
			$session_user=new Zend_Session_Namespace('auth');
			$userName=$session_user->user_name;
			$GetUserId= $session_user->user_id;
				
			$info_purchase_order=array(
					"customer_id"   => $data['customer_id'],
					"branch_id"     => $data["branch_id"],
					"date_sold"     => $data['sale_date'],
					"net_total"     => $data['total_dollar'],
					'discount_value' =>empty($data['discount'])?0:$data['discount'],
					"all_total"     => $data['total_dollar'],
			        'paid_dollar'	=> $data['receive_dollar'],
					"paid"          => $data['total_paid'],
					"balance"       => $data['balance'],
					"user_mod"      => $GetUserId,
					'term_condition'=> $data['term_condition'],
					'pending_status'=>3,
					"date"          => date("Y-m-d"),
					'agreement_id'  => 0,
					"jcnumber"    => $data['cjnumber'],
					"mileage"    => $data['mileage'],
					//"currency_id"   => 1,//$data['currency'],
					//"discount_value"=> $data['dis_value'],
					//"discount_type" => $data['discount_type'],
					//"saleagent_id"  => $data['saleagent_id'],
					//"tax"			  => $data["total_tax"],
					//"remark"        => $data['remark'],
					//"sale_no"       => $so,//$data['txt_order'],
			);
			$this->_name="tb_sales_order";
			$where=" id = ".$sale_id;
			$this->update($info_purchase_order, $where);
				
			$this->_name='tb_salesorder_item';
			$where=" saleorder_id = ".$sale_id;
			$this->delete($where);
			
			$rsreceipt = $this->getReceiptDetailbysaleid($sale_id);
			if(!empty($rsreceipt)){
				$this->_name='tb_receipt';
				$where=" id =".$rsreceipt['receipt_id'];
				$this->delete($where);
			}
			$this->_name='tb_receipt_detail';
			$where=" invoice_id=".$sale_id;
			$this->delete($where);
			
			if($data['total_paid']>0){
				$db_global = new Application_Model_DbTable_DbGlobal();
				$data['receipt'] = $db_global->getReceiptNumber(1);
				$info_purchase_order=array(
						"branch_id"   	=> 	$data["branch_id"],
						"customer_id"   => 	$data["customer_id"],
						"payment_type"  => 	1,//payment by customer/invoice
						"payment_id"    => 	1,	//payment by cash/paypal/cheque
						"receipt_no"    => 	$data['receipt'],
						"receipt_date"  =>  date("Y-m-d"),
						"date_input"    =>  date("Y-m-d"),
						"total"         => 	$data['total_dollar'],
						"paid"          => 	$data["total_paid"],
						"paid_dollar"   => 	$data['receive_dollar'],
						"paid_riel"     => 	$data['receive_riel'],
						"balance"       => 	$data['balance'],
						"user_id"       => 	$GetUserId,
						'status'        =>1,
						"bank_name"     => 	'',
						"cheque_number" => 	'',
						"exchange_rate" => 	$data['exchange_rate'],
				);
				$this->_name="tb_receipt";
				$reciept_id = $this->insert($info_purchase_order);
					
				$data_item= array(
						'receipt_id'  => $reciept_id,
						'invoice_id'  => $sale_id,
						'total'		  => $data['total_dollar'],
						'paid'	      => $data["total_paid"],
						'balance'	  => $data['balance'],
						'is_completed'=> ($data['balance']==0)?1:0,
						'status'      => 1,
						'date_input'  => date("Y-m-d"),
				);
				$this->_name='tb_receipt_detail';
				$this->insert($data_item);
			}
				
			$ids=explode(',',$data['identity']);
			foreach ($ids as $i)
			{
				$rs = $this->getProductByProductId($data['product_id'.$i], $data["branch_id"]);
				if(!empty($rs)){
					$this->_name='tb_prolocation';
					$arr = array(
							'qty'=>$rs['qty']-$data['qty_sold'.$i]
					);
					$where=" id =".$rs['id'];
					$this->update($arr, $where);
				}
					$data_item= array(
						'saleorder_id'=> $sale_id,
						'pro_id'	  => $data['product_id'.$i],
						'qty_unit'	  => $data['qty_'.$i],
						'qty_detail'  => $data['qtydetail_'.$i],
						'qty_order'	  => $data['qty_sold'.$i],
						'price'		  => $data['price_'.$i],
						'old_price'   => $data['price_'.$i],
						//'cost_price'  => $data['cost_price'.$i],
						'sub_total'	  => $data['sub_total'.$i],
						'note'	  	  => $data['note'.$i],
						'staff_id'	  => $data['staff_'.$i],
					);
				$this->_name='tb_salesorder_item';
				$this->insert($data_item);
			}
	
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	function getInvoiceById($id){
		$sql=" SELECT s.*,
				(net_total+transport_fee) AS net_total,
			(SELECT CONCAT(cust_name,' ',contact_name) FROM `tb_customer` WHERE tb_customer.id=s.customer_id LIMIT 1 ) AS customer_name,
			(SELECT contact_name FROM `tb_customer` WHERE tb_customer.id=s.customer_id LIMIT 1 ) AS contact_name,	
			(SELECT address FROM `tb_customer` WHERE tb_customer.id=s.customer_id LIMIT 1 ) AS address,	
			(SELECT contact_phone FROM `tb_customer` WHERE tb_customer.id=s.customer_id LIMIT 1 ) AS contact_phone,	
			(SELECT u.fullname FROM tb_acl_user AS u WHERE u.user_id =s.user_mod LIMIT 1) AS user_name
		FROM tb_sales_order AS s WHERE s.id= ".$id;
		return $this->getAdapter()->fetchRow($sql);
	}
	function getInvoiceDetailById($id){
		$sql=" SELECT si.*,
			(SELECT item_name FROM `tb_product` WHERE id=si.pro_id) As pro_name
		FROM tb_salesorder_item as si WHERE si.saleorder_id= ".$id;
		return $this->getAdapter()->fetchAll($sql);
	}
	function deleteSale($sale_id){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
		$rsdetail = $this->getInvoiceDetailById($sale_id);
			if(!empty($rsdetail)){
				foreach($rsdetail as $row){
					$rs = $this->getProductByProductId($row['pro_id'], 1);
					if(!empty($rs)){
						$this->_name='tb_prolocation';
						$arr = array(
								'qty'=>$rs['qty']+$row['qty_order']
						);
						$where=" id =".$rs['id'];
						$this->update($arr, $where);
					}
				}
			}
		
			$this->_name='tb_sales_order';
			$where=" id = ".$sale_id;
			$this->delete($where);
			
			$rsreceipt = $this->getReceiptDetailbysaleid($sale_id);
			if(!empty($rsreceipt)){
				$this->_name='tb_receipt';
				$where=" id =".$rsreceipt['receipt_id'];
				$this->delete($where);
			}
			$this->_name='tb_receipt_detail';
			$where=" invoice_id=".$sale_id;
			$this->delete($where);
			
			$this->_name='tb_salesorder_item';
			$where=" saleorder_id = ".$sale_id;
			$this->delete($where);
			$db->commit();
		
		}catch(Exception $e){
			$db->rollBack();
		}
	}
	function getReceiptDetailbysaleid($sale_id){
// 		$data_item= array(
// 				'receipt_id'  => $reciept_id,
// 				'invoice_id'  => $sale_id,
// 				'total'		  => $data['total_dollar'],
// 				'paid'	      => $data["total_paid"],
// 				'balance'	  => $data['balance'],
// 				'is_completed'=> ($data['balance']==0)?1:0,
// 				'status'      => 1,
// 				'date_input'  => date("Y-m-d"),
// 		);
// 		$this->_name='tb_receipt_detail';
// 		$this->insert($data_item);
		
		$sql=" SELECT  receipt_id,invoice_id FROM tb_receipt_detail WHERE invoice_id = $sale_id LIMIT 1 ";
		return $this->getAdapter()->fetchRow($sql);				
	}
}