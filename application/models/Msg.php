<?php

/**
 *
* Copyright(c) 201x,
* All rights reserved.
*
* 功 能：
* @author bikang@book.sina.com
* date:2016年10月17日
* 版 本：1.0
 */

class MsgModel extends BaseModel{
	
	private $table = "message";
	
	public function getList(){
		return $this->db->fetchRows("select * from ".$this->table);
	}
	
	
	
	public function  add($arr){
		$insert_sql = $this->buildInsert($this->table, $arr);
		return $this->db->query($insert_sql);
	}
	
	public function delte($id) {
		$id = intval($id);
		return $this->db->query("delete from ".$this->table." where id=".$id);
	}
	
}