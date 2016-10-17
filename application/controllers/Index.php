<?php

/**
 *
* Copyright(c) 201x,
* All rights reserved.
*
* 功 能：
* @author bikang@book.sina.com
* date:2016年10月13日
* 版 本：1.0
 */

class IndexController extends Yaf_Controller_Abstract{
	
	
	public function indexAction(){
		
		//测试加载本地类库
		$loader = Yaf_Loader::getInstance();
		$loader->import(APP_PATH."/library/Test/TestData.php");
		echo Test_TestData::$a;
		
		//测试registerLocalNamespace
		$loader->registerLocalNamespace(array("Test", "TestDataMore"));
		echo Test_TestDataMore::$a;
		
		//获取当前的路由规则
		$router = Yaf_Dispatcher::getInstance()->getRouter();
		echo "<pre>";print_r($router);
		
		var_dump(ini_get('yaf.environ'));
		
		
		$this->getView()->assign("content","hi yaf");
	}
	
	public function  bookAction() {
		Yaf_Dispatcher::getInstance()->disableView();
		echo "go";

	}
	
}