##YAF使用的总结


听说yaf是基于php的扩展写的php框架，不胜向往，一直没有时间去使用它

[yaf的参考资料](http://www.laruence.com/manual/index.html)

最近总算使用yaf写了个简单的应用[地址](https://github.com/beckbikang/yaf_testyaf)






和常用的php框架一样，yaf提供了单入口

入口文件的格式
```
define("APP_PATH", realpath(dirname(__FILE__))."/.." );
$app = new Yaf_Application(APP_PATH."/application/conf/application.ini");
$app->bootstrap()->run();
```

yaf必须配置项目的路径

	application.directory = APP_PATH  "/application"

yaf的路由，你只需要将你nginx配置到指定public路径，yaf的默认路由会自动解析到对应的路径,后面详细说下yaf的路由


我的nginx配置,可以看到所有的请求都被重新定向到index.php 这个入口文件去了哦。

 ```
 server {
          listen 80; 
          server_name  framework.myyaf.yaf.kang.com;
          access_log  /Users/kang/Documents/var/log/access.log;
          root /Users/kang/Documents/phpProject/studyphp/framework/yaf/yaf_testyaf/public;
          index  index.php index.html index.htm;

          location / { 
              try_files $uri $uri/ /index.php;        
          }   

          location ~ \.php$ {
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include        fastcgi_params;
            include         fastcgi.conf;
          }   
          if (!-e $request_filename) {
                rewrite ^/(.*)  /index.php/$1 last;
          }   
    }  
 ```
 如果按照上面的步骤配置好，yaf就可以正常运行了。
 
 
 摘自鸟哥的说明
 	
	+ public
	  |- index.php //入口文件
	  |- .htaccess //重写规则    
	  |+ css
	  |+ img
	  |+ js
	+ conf
	  |- application.ini //配置文件   
	+ application
	  |+ controllers
	     |- Index.php //默认控制器
	  |+ views    
	     |+ index   //控制器
	        |- index.phtml //默认视图
	  |+ modules //其他模块
	  |+ library //本地类库
	  |+ models  //model目录
	  |+ plugins //插件目录
	 
 
 
 我的例子里面是没有使用module的。简单的例子没必要那么多层级
 
 说说yaf的路由，yaf有一个路由器，可以定义5种路由规则
 
 
Yaf_Route_Simple 简单路由，是什么？

定义了通过传递参数的路由规则
我们可以通过 index.php?m=test&c=c&a=a访问moudle test 下的controller c的action a

		
Yaf_Route_Supervar 简单路由可以获取uri扩展
http://domain.com/index.php?r=/test/c/a
效果同上



Yaf_Route_Static  默认路由协议

	http://domain.com/test/c/a 也能达到上面的效果

Yaf_Route_Map map路由
	
	我没有试过，大家可以参考这个说明事实，简单的说就是通过/切分重写的参数
	
	Yaf_Route_Map议是一种简单的路由协议, 它将REQUEST_URI中以'/'分割的节, 组合在一起, 形成一个分层的控制器或者动作的路由结果. Yaf_Route_Map的构造函数接受俩个参数, 第一个参数表示路由结果是作为动作的路由结果,还是控制器的路由结果. 默认的是动作路由结果. 第二个参数是一个字符串, 表示一个分隔符, 如果设置了这个分隔符, 那么在REQUEST_URI中, 分隔符之前的作为路由信息载体, 而之后的作为请求参数.

Yaf_Route_Rewrite  重写

	这个真的很方便
	

Yaf_Route_Regex 正则路由
	
	http://framework.myyaf.yaf.kang.com/msg/listmsg/16
	我可以配置为
	
		"/msg/del/:id",array('controller' => 'msg','action' => 'del')
		
		controller是msg
		action是listmsg
		第三个参数id=16
		
	我们可以通过$id = $this->getRequest()->getParam('id',0);获取参数
	
	如果不配置就是
	http://framework.myyaf.yaf.kang.com/msg/listmsg/id/16了

我没有看到yaf的get函数，需要摸索，只能通过这种方式去配置get参数
		

Yaf_Route_Regex 也挺有意思的，大家可以试试，

例如

http://framework.myyaf.yaf.kang.com/msg/16 
映射到msg controller的list方法，接受参数



```
$route = new Yaf_Route_Regex(
   　　'msg/([a-zA-Z-_0-9]+)',
   　　array(
　　　　　　'controller' => 'msg',
　　　　　　'action' => 'list'
   　　)
   );
   $router->addRoute('product', $route);
   
获取参数

$id = $this->getRequest()->getParam(1);
```




添加路由可以在两个地方

	1 配置文件
	2 booststrap.php文件	,这个文件可以指定具体位置


通过配置文件添加

```
;simple
routes.simple.type="simple"
routes.simple.module="m"
routes.simple.controller="c"
routes.simple.action="a"

;supervar
routes.supervar.type="supervar"
routes.supervar.varname=r

;rewrite
;routes.rewrite.type="rewrite"
;routes.rewrite.match="/msg/del/:id"
;routes.simple.controller="msg"
;routes.simple.action="del"

```



通过booststrap.php文件添加

```
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
```


yaf没有提供多余的部件


大致有

	入口的对象
	配置管理
	加载器
	分发器
	请求和响应
	路由
	自动加载
	控制器
	异常
	
剩余的部分需要我们自由填充了哦。大家可以使用composer，很方便的工具


yaf的使用就说到这里，其实说完路由，大致就没啥可说的了，其他的都是在项目中逐步熟悉。


入门可以看看我的入门项目哦


[地址](https://github.com/beckbikang/yaf_testyaf)


	

	

 	
 	
 
 
 
 
 
 
 
 
 
 
 














