<?php
class InfoController extends HomeCommonController
{
	/**
	 * 个人信息的添加
	 * @return [type] [description]
	 */
	public function info()
	{
		//接收前台传过来的id
		$id = $_GET['uid'];
		//实例化数据表
		$users = new Model('shop_users');
		//取到所有数据
		$data = $_POST;

        if(empty($_FILES['uface']['name'])){
            //查询修改id的单条数据
            $users_data = $users -> find($id);
            $data['uface'] = $users_data['uface'];
        }else{
            $up = new UP('./Public/uface/',UP_TYPE,UP_SIZE);
            $up_res = $up->upload($_FILES['uface']);
            if($up_res){
                // 接受添加的数据 并且处理
                $data = $_POST;
                $data['uface'] = $up_res;
            }else{
                echo '文件上传失败';
                exit;
            }
        }
         if(!empty($data['email']) && !empty($data['sex']) && !empty($data['uface'])) {
            $data['static'] = 1;
        }
        //执行上传
        //执行修改
        $res = $users -> update($data,$id);
        //通过影响的行数 判断结果
        if($res > 0) {
            echo '<script>alert("修改成功");location.href="./index.php?m=home&c=login&a=info";</script>';
        } else {
            echo '<script>alert("不能重复提交");location.href="./index.php?m=home&c=login&a=info";</script>';
        }
	}
    public function pwd()
    {
        if ($_SESSION['home_login'] == false) {
            echo '<script>alert("请先登录");location.href="./index.php?m=home&c=login&a=login"</script>';
        } else {
            //获取用户id
            $id = $_SESSION['home_userinfo']['uid'];
            //实例化数据表
            $users = new Model('shop_users');
            //查询单条数据
            $data = $users -> find($id);
            include_once('./View/Home/Info/pwd.html');
        }
        
    }
    public function upwd()
    {
        //获取用户id
        $id = $_SESSION['home_userinfo']['uid'];
        //实例化数据表
        $users = new Model('shop_users');
        //查询单条数据
        $data = $users -> find($id);
        //接收数据
        $pwd = $_POST;
        //判断原密码是否正确
        if (md5($pwd['upwd']) != $data['upwd']) {
            echo '<script>alert("原密码错误");location.href="./index.php?m=home&c=info&a=pwd";</script>';
            exit;
        }
        //检测密码长度是否合格
        if(strlen($pwd['npwd']) < 6 || strlen($pwd['npwd']) > 18 ) {
            echo '<script>alert("密码长度只允许6-18位");location.href="./index.php?m=home&c=info&a=pwd";</script>';
            exit;
        }
        //检测密码是否合格 是否一致
        if($pwd['npwd'] != $pwd['renpwd']) {
             echo '<script>alert("两次密码不一致");location.href="./index.php?m=home&c=info&a=pwd";</script>';
            exit;
        }
        //给新密码加密
        $pwd['upwd'] = md5($pwd['npwd']);

        //删除重复密码
        unset($pwd['renpwd']);
        //将修改后的密码插入数据库
        $res = $users -> update($pwd,$id);
        if($res > 0) {
            unset($_SESSION['home_login']);
            unset($_SESSION['home_userinfo']);
            echo '<script>alert("密码修改成功,请重新登录");location.href="./index.php?m=home&c=login&a=login";</script>';
        } else {
            echo '<script>alert("密码修改失败");location.href="./index.php?m=home&c=info&a=pwd";</script>';
            exit;
        }
    }
}