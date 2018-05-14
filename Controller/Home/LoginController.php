<?php
class LoginController
{
	/**
	 * 加载登录页面
	 * @return [type] [description]
	 */
	public function login()
	{
		include_once('./View/Home/Login/login.html');
	}
	/**
	 * 执行登录判断
	 * @return [type] [description]
	 */
	public function dologin()
	{
		//获取登录信息
		$username = $_POST['uname'];
		$password = $_POST['upwd'];
		//实例化数据表
		$users = new Model('shop_users');
		$temp_arr = $users -> where('uname="'.$username.'"') -> select();
		//判断用户名是否正确
		if (empty($temp_arr)) {
			echo '<script>alert("用户名不正确");location.href="./index.php?m=home&c=login&a=login"</script>';
			die;
		}
		//获取用户信息
		$userinfo = $temp_arr[0];
		//判断密码是否正确
		if (md5($password) != $userinfo['upwd']) {
			echo '<script>alert("密码不正确");location.href="./index.php?m=home&c=login&a=login"</script>';
			die;
		} else {
			//用session做标识
			$_SESSION['home_login'] = true;//true  登录成功   false 登录失败
			$_SESSION['home_userinfo'] = $userinfo; //保留用户信息
			echo '<script>alert;location.href="./index.php?m=home&c=index&a=index"</script>';
		}
	}
	/**
	 * 退出登录
	 */
	public function outlogin()
	{
		unset($_SESSION['home_login']);//销毁
		unset($_SESSION['home_userinfo']);//销毁用户信息
		echo '<script>alert;location.href="./index.php?m=home&c=index&a=index"</script>';
	}
	/**
	 * 个人中心判断
	 * @return [type] [description]
	 */
	public function info()
	{
		//判断用户是否登录
		if ($_SESSION['home_login'] == false) {
			echo '<script>alert("请先登录");location.href="./index.php?m=home&c=login&a=login"</script>';
		} else {
			//获取用户id
			$id = $_SESSION['home_userinfo']['uid'];
			//实例化数据表
			$users = new Model('shop_users');
			//查询单条数据
			$data = $users -> find($id);
			include_once('./View/Home/Info/info.html');
		}
		
	}

}