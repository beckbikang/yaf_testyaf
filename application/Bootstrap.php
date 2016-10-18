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

class Bootstrap extends Yaf_Bootstrap_Abstract{
	
	//把配置保存起来
	public function _initConfig(Yaf_Dispatcher $dispatcher) {
         Yaf_Registry::set('config', Yaf_Application::app()->getConfig());
	}
    
	//注册插件
	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		$user = new UserPlugin();
		$dispatcher->registerPlugin($user);
	}
	
	public function _initRoute(Yaf_Dispatcher $dispatcher){
		//注册路由变量
		$router =  Yaf_Dispatcher::getInstance()->getRouter();
		if(Yaf_Registry::get("config")->routes){
			$router->addConfig(Yaf_Registry::get("config")->routes);
		}
		
		//rewrite
		$route = new Yaf_Route_Rewrite("/index/book",array('controller' => 'index','action' => 'book'));
		$router->addRoute('product',$route);
		
		//rewrite
		
		$route = new Yaf_Route_Rewrite("/msg/del/:id",array('controller' => 'msg','action' => 'del'));
		$router->addRoute('product',$route);
		
	}
	
	
	public function _initDb(Yaf_Dispatcher $dispatcher){
		$model = new BaseModel();
		$this->_db = $model->getMysqlDb();
		Yaf_Registry::set('_db', $this->_db);
	}
	
	
	public function _inittest(Yaf_Dispatcher $dispatcher){
		//echo "##test##";
	}
	
}