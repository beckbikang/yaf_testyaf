<?php

/**
 *
* Copyright(c) 201x,
* All rights reserved.
*
* 功 能：
* @author bikang@book.sina.com
* date:2016年10月18日
* 版 本：1.0
 */

class Db_SqlBuilder{
	/**
	 * 格式化数组
	 * @param type $values
	 * @return string
	 */
	public static  function clearValues($values){
		if(empty($values) || !is_array($values)) return '';
		foreach($values as &$v){
			if(is_int($v)){
				$v = intval($v);
			}else{
				$v = "'". mysql_real_escape_string($v)."'";
			}
		}
		return $values;
	}
	/**
	 * 根据数组构建插入语句
	 * @param type $tableName
	 * @param type $keyValueArr
	 * @return string
	 */
	public static function buildInsertSql($tableName,$keyValueArr){
		if(empty($tableName) || empty($keyValueArr)) return '';
		$columns = array_keys($keyValueArr);
		$values  = array_values($keyValueArr);
		$values = self::clearValues($values);
		$sql = "insert into `{$tableName}`(";
		$sql .= implode(",",$columns).")values(";
		$sql .= implode(",",$values).")";
		return $sql;
	}

	public static function buildUpdateSql($tableName, $columnsAndValues, $filters = array())
	{
		 
	}


}