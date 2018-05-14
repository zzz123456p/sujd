<?php
//声明常量
define('DB_DSN','mysql');
define('DB_HOST','localhost');
define('DB_NAME','jdshop');
define('DB_CHARSET','utf8');
define('DB_PORT','3306');
define('DB_USER','root');
define('DB_PASS','');

// 网站应用的配置 常量
date_default_timezone_set('PRC'); 

//声明上传类
define('UP_DIR','./Public/uploads/');
define('UP_TYPE',['image/jpeg','image/jpg','image/png','image/gif']);
define('UP_SIZE','2048000');