<?php

    session_start();
//index.php?m=admin&c=user&a=index
//访问:Controller/admin模板  下面的user控制器 里面的index方法

    /*
        index.php 入口文件
            m 模块 home(前台) admin(后台)
            c 控制器的名称  user => userController
            a 方法名称
    */
    include('./Model/Model.php');
    include('./Public/configs.php');
    include('./Public/UP.php');
    include('./Controller/Admin/AdminCommonController.php');
    include('./Controller/Home/HomeCommonController.php');
    $m = empty($_GET['m']) ? 'home' : $_GET['m'];
    $c = empty($_GET['c']) ? 'index'  : $_GET['c'];
    $a = empty($_GET['a']) ? 'index' : $_GET['a'];
    //echo $m.'---'.$c.'---'.$a;
    //拼接文件的路径 ./Controller/Admin/UserController.php
    $class_dir = './Controller/'.ucfirst($m).'/'.ucfirst($c).'Controller.php';
    //检测文件或者路径是否存在
    if(!file_exists($class_dir)){
        die('参数错误，文件或者类不存在...');
    }
    //引入类文件
    include_once($class_dir);
    //拼装类名
    $class_name = ucfirst($c).'Controller';
    //实例化
    $user = new $class_name;
    //检测方法是否存在
    if(!method_exists($user,$a)){
        die('方法不存在...');
    }
    $user->$a();