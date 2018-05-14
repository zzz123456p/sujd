<?php
/**
* 登录验证
*/
class LoginController
{	
	/**
	 * 加载登录页面
	 * @return [type] [description]
	 */
	public function login()
	{
		include_once('./View/Admin/Login/login.html');
	}
	public function dologin()
	{
		//获取登录信息
		$username = $_POST['uname'];
		$password = $_POST['upwd'];
		//检验验证码是否正确
		//用户输入的验证码
		$code1 = $_POST['code'];
		//图片上的验证码
		$code2 = $_SESSION['code'];
		//判断验证码是否正确
		if (strtoupper($code1) != strtoupper($code2)) {
			echo '<script>alert("验证码不正确");location.href="./index.php?m=admin&c=login&a=login"</script>';
			die;
		}
		
		//实例化数据表
		$users = new Model('shop_users');
		$temp_arr = $users -> where('uname="'.$username.'"') -> select();
		//判断用户名是否正确
		if (empty($temp_arr)) {
			echo '<script>alert("用户名或密码不正确");location.href="./index.php?m=admin&c=login&a=login"</script>';
			die;
		}
		
		//获取用户信息
		$userinfo = $temp_arr[0];
		//判断密码是否正确
		if (md5($password) != $userinfo['upwd']) {
			echo '<script>alert("用户名或密码不正确");location.href="./index.php?m=admin&c=login&a=login"</script>';
			die;
		}
		//判断权限
		if ($userinfo['auth'] != 1) {
			echo '<script>alert("权限不够，无法访问");location.href="./index.php?m=admin&c=login&a=login"</script>';
			die;
		} else {
			//用session做标识
			$_SESSION['login'] = true;//true  登录成功   false 登录失败
			$_SESSION['userinfo'] = $userinfo; //保留用户信息
			echo '<script>alert;location.href="./index.php?m=admin&c=user&a=index"</script>';
		}
	}


	/**
	 * 退出登录
	 */
	public function outlogin()
	{
			unset($_SESSION['login']);//销毁
			unset($_SESSION['userinfo']);//销毁用户信息
			echo '<script>alert;location.href="./index.php?m=admin&c=login&a=login"</script>';
	}
}