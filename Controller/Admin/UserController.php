<?php
class UserController extends AdminCommonController
{
    /**
    *列表页
    */
    public function index()
    {
        //引入数据库
        $users = new Model('shop_users');
        
        if(!empty($_GET['search_type']) && !empty($_GET['keywords'])){
            //接收要搜索的条件
            $search_type = $_GET['search_type'];
            //对用户输入的数据进行匹配
            $keywords = $_GET['keywords'];
            if($keywords == '男') {
                $keywords = 'm';
            } else if ($keywords == '女') {
                $keywords = 'w';
            } else if ($keywords == '保密') {
                $keywored = 'x';
            }
            $users -> where($search_type.' like "%'.$keywords.'%"');
        }
        
        
        /**
        *分页
        */
        //当前页码
        $page = empty($_GET['page']) ? 1 : $_GET['page'];
        //获取数据的总条数
        $count = $users -> count();
        //显示的条数
        $pageShow =15;  
        //计算最大的页码
        $maxPage = ceil($count/$pageShow);
        //计算下一页
        $nextPage = $page+1;
        if($nextPage > $maxPage){
            $nextPage = $maxPage;
        }
        //计算上一页
        $prevPage = $page-1;
        if($prevPage <= 1){
            $prevPage = 1;
        }
        //处理页码url 销毁变量
        if(isset($_GET['page'])){
            unset($_GET['page']);
        }
        $url = $_SERVER['SCRIPT_NAME'].'?'.http_build_query($_GET).'&page=';
        //计算limit页码参数
        $limitParam = ($page-1) * $pageShow;
        $data = $users -> where('resource = 1') -> limit($limitParam,$pageShow) -> select();
       
        //显示用户列表页面
        include_once('./View/Admin/User/index.html');
    }
    
    /**
    *列表页
    */
    public function add()
    {
        //显示用户添加页面
        include_once('./View/Admin/User/add.html');
    }
    
    /**
    *显示添加页面
    */
    public function insert()
    {
        //获取要添加的数据
        $data = $_POST;
        //var_dump($data);
        
        //判断用户名是否为空
        if(empty($data['uname'])) {
            echo '<script>alert("用户名不能为空");location.href="./index.php?m=admin&c=user&a=add";</script>';
            exit;
        }
        //判断用户名是否合法
        if(preg_match_all('/^[a-zA-Z]{1}[a-zA-Z0-9_]{5,17}$/',$data['uname']) == false){
            echo '<script>alert("用户名只允许6-18位并以字母开头");location.href="./index.php?m=admin&c=user&a=add";</script>';
            exit;
        }
        //检测密码长度是否合格
        if(strlen($data['upwd']) < 6 || strlen($data['upwd']) > 18 ) {
            echo '<script>alert("密码长度只允许6-18位");location.href="./index.php?m=admin&c=user&a=add";</script>';
            exit;
            //die('密码不符合要求');
        }
        //检测密码是否合格 是否一致
        if($data['upwd'] != $data['reupwd']) {
             echo '<script>alert("两次密码不一致");location.href="./index.php?m=admin&c=user&a=add";</script>';
            exit;
        }
        //检测手机号是否合法
        if(preg_match_all('/^1[3|4|5|7|8]\d{9}$/',$data['tel']) == false){
            echo '<script>alert("请输入正确的手机号");location.href="./index.php?m=admin&c=user&a=add";</script>';
            exit;
        }
        
        //检测数据库  用户是否唯一
        //添加数据
        //引入数据库
        $users = new Model('shop_users');
        $un_data = $users->where('uname="'.$data['uname'].'"')->select();
        //判断用户名是否存在
        if(!empty($un_data)) {
            echo '<script>alert("用户名已存在");location.href="./index.php?m=admin&c=user&a=add";</script>';
            exit;
        }
        
        //处理数据
        unset($data['reupwd']);
        $data['regtime'] = time();
        $data['upwd'] = md5($data['upwd']);
        
        //插入数据
        $res = $users->insert($data);
        //通过影响行数判断结果
        if($res > 0) {
            echo '<script>alert("添加成功");location.href="./index.php?m=admin&c=user&a=index";</script>';
        } else {
            echo '<script>alert("添加失败");location.href="./index.php?m=admin&c=user&a=index";</script>';
        }
        
    }
    
    /**
    *删除操作
    */
    public function delete()
    {
        //接收要删除的数据id
        $id = $_GET['uid'];
        //连接数据库
        $users = new Model('shop_users');
        //执行删除
        $data = $users->find($id);
        $data['resource'] = 0;
        $res = $users->update($data,$id);
        //通过影响行数判断结果
        if($res > 0) {
            echo '<script>alert("删除成功");location.href="./index.php?m=admin&c=user&a=index";</script>';
        } else {
            echo '<script>alert("删除失败");location.href="./index.php?m=admin&c=user&a=index";</script>';
        }
    }
    
    /**
    *加载修改页面
    */
    public function edit()
    {
        //接收要修改的数据id
        $id = $_GET['uid'];
        //连接数据库
        $users = new Model('shop_users');
        $data = $users->find($id);
       
        //显示用户修改页面
        include_once('./View/Admin/User/edit.html');
    }
    
    /**
    *执行修改
    */
    public function update()
    {
        //实例化数据表
        $users = new Model('shop_users');
        //接收修改数据
        $data = $_POST;
        //检测手机号是否合法
        if(preg_match_all('/^1[3|4|5|7|8]\d{9}$/',$data['tel']) == false){
            echo '<script>alert("请输入正确的手机号");location.href="./index.php?m=admin&c=user&a=index";</script>';
            exit;
        }
        $id = $_GET['uid'];
        //执行修改
        $res = $users -> update($data,$id);
        //通过影响的行数 判断结果
        if($res > 0) {
            echo '<script>alert("修改成功");location.href="./index.php?m=admin&c=user&a=index";</script>';
        } else {
            echo '<script>alert("修改失败");location.href="./index.php?m=admin&c=user&a=index";</script>';
        }
    }
    /**
    *密码修改页面
    */
    public function updatepwd()
    {
        //接收要修改的数据id
        $id = $_GET['uid'];
        //连接数据库
        $users = new Model('shop_users');
        $data = $users->find($id);
        
        //显示用户修改页面
        include_once('./View/Admin/User/pwd.html');
    }
    
    /**
    *执行修改
    */
    public function newpwd()
    {
        //实例化数据表
        $users = new Model('shop_users');
        //接收修改数据
        $data = $_POST;
        //接收要修改数据的id
        $id = $_GET['uid'];
        //判断密码是否等于原密码
        $upwd = $users -> find($id);
        if(md5($data['upwd']) != $upwd['upwd'])
        {
            echo '<script>alert("原密码不正确");location.href="./index.php?m=admin&c=user&a=updatepwd&uid='.$id.'";</script>';
        }
        //检测密码长度是否合格
        if(strlen($data['npwd']) < 6 || strlen($data['npwd']) > 18 ) {
            echo '<script>alert("密码长度只允许6-18位");location.href="./index.php?m=admin&c=user&a=updatepwd&uid='.$id.'";</script>';
            exit;
            //die('密码不符合要求');
        }
        //检测密码是否合格 是否一致
        if($data['npwd'] != $data['renpwd']) {
             echo '<script>alert("两次密码不一致");location.href="./index.php?m=admin&c=user&a=updatepwd&uid='.$id.'";</script>';
            exit;
        }
        //给新密码加密
        $data['upwd'] = md5($data['npwd']);
        //删除重复密码
        unset($data['renpwd']);
        //执行修改数据
        $res = $users -> update($data,$id);
        //通过影响行数判断结果
        if($res > 0) {
            echo '<script>alert("修改密码成功");location.href="./index.php?m=admin&c=user&a=index";</script>';
        } else {
            echo '<script>alert("修改密码失败");location.href="./index.php?m=admin&c=user&a=updatepwd&uid='.$id.'";</script>';
        }
    }
    /**
    *详情页
    */
    public function details()
    {
        //接收要查看的数据id
        $id = $_GET['uid'];
        //连接数据库
        $users = new Model('shop_users');
        $data = $users->find($id);
        
        //显示用户详情页面
        include_once('./View/Admin/User/details.html');
    }
   
}