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


class  BaseModel {
	
	public function __get($key) {
		switch ($key){
			case "db":
				return $this->getMysqlDb();
				break;
			case "config":
				return $this->getMysqlDbConfig();
				break;
				
		}
	}
	
	
	public function buildInsert($table,$arr){
		return Db_SqlBuilder::buildInsertSql($table, $arr);
	}
	
	public function getMysqlDb(){
		$tmodel = new BaseModel();
		$config = $tmodel->getMysqlDbConfig();
		try{
			$handle = new Db_MysqlHandle($config);
			return  $handle;
		}catch (Exception $e){
			echo $e->getMessage();
			return "";
		}
	}
	
	public function getMysqlDbConfig(){
		$config =  Yaf_Application::app()->getConfig()->db->mysql;
		$ret = array();
		$ret["host"] = $config->host;
		$ret["port"] = $config->port;
		$ret["user"] = $config->user;
		$ret["password"] = $config->pass;
		$ret["db"] = $config->database;
		$ret["charset"] = $config->charset;
		return $ret;
	}
	
	
}