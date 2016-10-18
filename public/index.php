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

ini_set('display_errors', 0);

define("APP_PATH", realpath(dirname(__FILE__))."/.." );
$app = new Yaf_Application(APP_PATH."/application/conf/application.ini");
$app->bootstrap()->run();

/*
echo "<pre>";
print_r($app->getDispatcher());
print_r($app->getConfig());
print_r($app->environ());
//print_r($app->geModules());
print_r($app->app());
*/