<?php
class AdminCommonController
{
	/**
	 * 检测用户是否登录
	 */
	public function __construct()
	{
		isset($_SESSION['login'])?$_SESSION['login'] : $_SESSION['login'] = false;
		//检测用户是否登录
		if (!$_SESSION['login']) {
			echo '<script>alert("请先登录");location.href="./index.php?m=admin&c=login&a=login"</script>';
			die;
		}
		
		isset($_SESSION['userinfo']['auth'])?$_SESSION['userinfo']['auth'] : $_SESSION['userinfo']['auth'] = false;
		//检测用户是否登录
		if ($_SESSION['userinfo']['auth']!=1) {
			echo '<script>alert("权限不够");location.href="./index.php?m=home&c=index&a=index"</script>';
			die;
		}
	}
}
