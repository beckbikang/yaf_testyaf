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


class MsgController extends Yaf_Controller_Abstract{
	

	public function  indexAction(){
		$this->getView()->assign("content","hi yaf");
	}
	

	//列表
	public function listmsgAction(){
		$model = new MsgModel();
		$list = $model->getList();
		$this->getView()->assign("list",$list);
		$this->getView()->assign("content","mydata");
		return true;
	}
	
	
	public function listapiAction(){
		Yaf_Dispatcher::getInstance()->disableView();
		$model = new MsgModel();
		$list = $model->getList();
		$ret["data"] = $list;
		$ret["error_code"] = 0;
		exit(json_encode($ret));
	}
	
	
	//添加
	public function addAction(){
		Yaf_Dispatcher::getInstance()->disableView();
		if($this->getRequest()->isPost()){
			$posts = $this->getRequest()->getPost();
			$model = new MsgModel();
			$ret = $model->add($posts);
		}
		$this->getResponse()->setRedirect("http://framework.myyaf.yaf.kang.com/msg/listmsg");
	}
	//删除
	public function delAction() {
		Yaf_Dispatcher::getInstance()->disableView();
		$id = $this->getRequest()->getParam('id',0);
		if($id > 0){
			$model = new MsgModel();
			$ret = $model->delte($id);
		}
		$this->getResponse()->setRedirect("http://framework.myyaf.yaf.kang.com/msg/listmsg");
	}
	
	

}