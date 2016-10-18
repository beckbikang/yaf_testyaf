<?php
/*
cusor:
user:kang
date:2016年2月26日
project-name:project_name
package_name:package_name
*/
class Db_MysqlHandle
{
	private $host,$user,$password,$db;
	private $port = 3306;
	private $charset = 'utf8';
	private $timeout = 5;

	private $resource_arr = array();
	private $current_connect = null;
	private $db_config = null;

	//retry times
	private $retry_times = 3;
	private $sleep_time = 1;
	private $error ;

	private $slow_log = '/tmp/query_slow.log';
	
	public $num_rows = 0;
	
	public function __construct($db_config=array()){
		if(empty($db_config))
		{
			throw new Exception("please check the db config!");
		}
		if(!isset($db_config["host"]) || !isset($db_config["user"]) || !isset($db_config["password"]) || !isset($db_config["db"]))
		{
			throw new Exception("the config file like this array(host,user,password)");
		}

		$this->host = $db_config["host"];
		$this->user = $db_config["user"];
		$this->password = $db_config["password"];
		$this->db = $db_config["db"];
		if(isset($db_config["port"])) $this->port = $db_config["port"];
		if(isset($db_config["charsert"]))$this->charsert = $db_config["charsert"];
		if(isset($db_config["timeout"])) $this->timeout = $db_config["timeout"];
		$this->processConnect();
	}

	public  function processConnect(){
		$db_key = md5($this->host.$this->user.$this->password.$this->db);
		if(!is_array($this->resource_arr) || !array_key_exists($db_key, $this->resource_arr) || !is_resource($this->resource_arr[$db_key])){
			$this->getConnect();
			if(!$this->current_connect){
				throw new Exception("connect db faild ".mysql_error());
			}
			$this->resource_arr[$db_key] = $this->current_connect;
		}else{
			$this->current_connect = $this->resource_arr[$db_key];
		}
	}

	public  function getConnect()
	{
		$mysqli = mysqli_init();
		if(!$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT,$this->timeout)){
			throw new Exception("set timeout faild!");
		}
		if(!$mysqli->real_connect($this->host,$this->user,$this->password,$this->db,$this->port)){
			throw new Exception(" connect db faild ".$mysqli->connect_error);
		}
		$mysqli->set_charset($this->charset);
		$this->current_connect = $mysqli;
	}

	public function reConnect(){
		if(!$this->current_connect->ping()){
			$this->current_connect->close();
			$try_time = $this->retry_times;
			do{
				try{
					$this->getConnect();
					if(!empty($this->current_connect) && $this->current_connect->ping()){
						break;
					}
				}catch(Exception $e){
					--$try_time;
					sleep(1);
				}
			}while($try_time  > 0);
		}
	}

	public function __destruct(){
		$this->current_connect->close();
		unset($this->current_connect);
		if(!empty($this->resource_arr)){
			foreach($this->resource_arr as $resouce){
				if(is_resource($resouce) && !empty($resouce)){
					$resouce->close();
				}
			}
		}
		unset($this->resource_arr);
	}

	public function checkConnect(){
		if(!$this->current_connect->ping()){
			$this->reConnect();
		}
	}
	
	
	public function query($sql){
		$this->checkConnect();
		$ret = $this->current_connect->query($sql);
		if(!$ret){
			$this->error = $this->current_connect->error;
		}else{
			if(is_object($ret) && stripos(strtolower($sql), "select") === false){
				$this->num_rows = $ret->num_rows;
			}
		}
		return $ret;
	}
	
	public function insert($sql){
		$this->checkConnect();
		$this->query($sql);
		return  $this->current_connect->insert_id;
	}

	public function fetchRow($sql){
		$this->checkConnect();
		$ret = array();
		$result = $this->current_connect->query($sql);
		if($result){
			$ret =$result->fetch_array();
			$result->free();
		}
		return $ret;
	}

	public function fetchRows($sql,$resultType = MYSQLI_ASSOC){
		$this->checkConnect();
		$ret = array();
		$result = $this->current_connect->query($sql);
		if($result){
			while($row = $result->fetch_array($resultType)){
				$ret[] = $row;
			}
			$result->free();
		}
		return $ret;
	}

	public function getQueryError(){
		return $this->error;
	}

	public function startTrans(){
		$this->checkConnect();
		$this->current_connect->query('SET AUTOCOMMIT=0');
		$this->current_connect->query('START TRANSACTION');
		return true;
	}


	public function transCommit(){
		$this->checkConnect();
		$this->current_connect->query('COMMIT');
		$this->current_connect->query('SET AUTOCOMMIT=1');
		return TRUE;
	}

	public function transRollback(){
		$this->checkConnect();
		$this->current_connect->query('ROLLBACK');
		$this->current_connect->query('SET AUTOCOMMIT=1');
		return TRUE;
	}
}


