<?php
/**
* 注册页
*/
class RegController
{
	/**
	 * 加载注册页面
	 * @return [type] [description]
	 */
	public function reg()
	{
		include_once('./View/Home/Reg/reg.html');
	}

	/**
	 * 执行判断注册
	 * @return [type] [description]
	 */
	public function doreg()
	{
		//获取要添加的数据
        $data = $_POST;

        //判断用户名是否合法
        if(preg_match_all('/^[a-zA-Z]{1}[a-zA-Z0-9_]{5,17}$/',$data['uname']) == false){
            echo '<script>alert("用户名只允许6-18位并以字母开头");location.href="./index.php?m=home&c=reg&a=reg";</script>';
            exit;
        }
        //添加数据
        //引入数据库
        $users = new Model('shop_users');
        $un_data = $users->where('uname="'.$data['uname'].'"')->select();
        //判断用户名是否存在
        if(!empty($un_data)) {
            echo '<script>alert("用户名已存在");location.href="./index.php?m=home&c=reg&a=reg";</script>';
            exit;
        }
        //检测密码长度是否合格
        if(strlen($data['upwd']) < 6 || strlen($data['upwd']) > 18 ) {
            echo '<script>alert("密码长度只允许6-18位");location.href="./index.php?m=home&c=reg&a=reg";</script>';
            exit;
            //die('密码不符合要求');
        }
        //检测密码是否合格 是否一致
        if($data['upwd'] != $data['reupwd']) {
             echo '<script>alert("两次密码不一致");location.href="./index.php?m=home&c=reg&a=reg";</script>';
            exit;
        }
        //判断验证码是否正确
        $code1 = $_POST['code'];//图片上的验证码
		$code2 = $_SESSION['code'];//判断验证码是否正确
		if (strtoupper($code1) != strtoupper($code2)) {
			echo '<script>alert("验证码不正确");location.href="./index.php?m=home&c=reg&a=reg";</script>';
			die;
		}
        //处理数据
        unset($data['reupwd']);
        $data['regtime'] = time();
        $data['upwd'] = md5($data['upwd']);
        
        //插入数据
        $res = $users->insert($data);
        //通过影响行数判断结果
        if($res > 0) {
            echo '<script>alert("注册成功");location.href="./index.php?m=home&c=login&a=login";</script>';
        } else {
            echo '<script>alert("注册失败");location.href="./index.php?m=home&c=reg&a=reg";</script>';
        }
	}
}