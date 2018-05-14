<?php
class OrderController extends HomeCommonController
{
	/**
	 * 订单确认
	 * @return [type] [description]
	 */
	public function index()
	{
		if ($_SESSION['cart']) {
			//判断用户是否登录
			if(!isset($_SESSION['home_login']) || empty($_SESSION['home_login'])){
				echo '<script>alert("请先登录");location.href="./index.php?m=home&c=login&a=login"</script>';
			}
			//判断用户是否填写收货地址
			$id = $_SESSION['home_userinfo']['uid'];
			$users = new Model('shop_users');
			$addr = $users -> find($id);
			if((!isset($addr['name']) || empty($addr['name'])) || (!isset($addr['tel']) || empty($addr['tel'])) ||(!isset($addr['addr']) || empty($addr['addr']))) {
				echo '<script>alert("请添加收货地址");location.href="./index.php?m=home&c=order&a=addr"</script>';
			}
			//用户地址信息
			
			//购物车商品信息
			$cart = $_SESSION['cart'];
			include_once('./View/Home/Cart/qry.html');
		} else {
			header('location:./index.php?m=home&c=cart&a=index');
		}
	}
	/**
	 * 用户地址页
	 */
	public function addr()
	{
		//获取id
		$id = $_SESSION['home_userinfo']['uid'];
		//实例化数据表
		$users = new Model('shop_users');
		//查询数据表单条信息
		$data = $users -> find($id);
		include_once('./View/Home/Cart/addr.html');
	}
	/**
	 * 收货地址添加
	 */
	public function doaddr()
	{
		$id = $_SESSION['home_userinfo']['uid'];
		//实例化数据表
		$users = new Model('shop_users');
		//取到所有数据												
		$data = $_POST;
		//检测手机号是否合法
        if(preg_match_all('/^1[3|4|5|7|8]\d{9}$/',$data['tel']) == false){
            echo '<script>alert("请输入正确的手机号");location.href="./index.php?m=home&c=order&a=addr";</script>';
            exit;
        }
        //用户地址信息
        $_SESSION['order']['addr'] = $data;
        //执行插入
        $res = $users -> update($_SESSION['order']['addr'],$id);
        //通过影响的行数 判断结果addr
        if($res > 0) {
            echo '<script>alert("添加成功");location.href="./index.php?m=home&c=order&a=index";</script>';
        } else {
            echo '<script>alert("添加失败");location.href="./index.php?m=home&c=order&a=addr";</script>';
        }
	}
	/**
	 * 成功页面
	 */
	public function success()
	{
		if ($_SESSION['home_login']==true) {
			//修改商品表信息
			$cart = $_SESSION['cart'];
			//实例化商品表
			$goods = new Model('shop_goods');
			foreach ($cart as $key => $value) {
				$sql = 'update shop_goods set num = num - '.$value['n'].',salecnt = salecnt + '.$value['n'].' where gid='.$value['gid'];
				$cart_res = $goods -> exec($sql);
				if($cart_res < 1){
					exit('下单失败');
				}
			}
			//订单地址信息
			$addrs = new Model('shop_users');
			$addr = $addrs->find($_SESSION['home_userinfo']['uid']);
			//数据处理
			//金额
			$data['ormb'] = $_SESSION['order']['sums'];
			//商品总数量
			$data['ocnt'] = $_SESSION['order']['nums'];
			//下单人
			$data['uid'] = $_SESSION['home_userinfo']['uname'];
			//收货人
			$data['rec'] = $addr['name'];
			//收货地址
			$data['addr'] = $addr['addr'];
			//联系电话
			$data['tel'] = $addr['tel'];
			//number_format
			$data['num']=$addr['num'];
			//状态
			$data['status'] = 1;
			//下单时间
			$data['otime'] = time();
			//订单号
			$data['orders'] = time() . rand(1000,9999);
			//实例化数据表
			//判断购物车是否有数据
			if ($_SESSION['cart']) {
				$shop_orders = new MOdel('shop_orders');
				//插入数据
				$res = $shop_orders -> insert($data);
				if ($res > 0) {
					//销毁购物车
					unset($_SESSION['cart']);
					//销毁总计
					unset($_SESSION['order']['sums']);
					//删除数量
					unset($_SESSION['order']['nums']);
					include_once('./View/Home/Cart/success.html');	
				} else {
					echo '<script>alert("下单失败");location.href="./index.php?m=home&c=order&a=index";</script>';
				}
			} else {
				header('location:./index.php?m=home&c=cart&a=index');
			} 闭包不Vv
		} else {
			echo '<script>alert("请先登录");location.href="./index.php?m=home&c=login&a=login";</script>';
		}
	}
	/**
	 * 订单页
	 * @return [type] [description]
	 */
	public function list()
	{	
		if ($_SESSION['home_login']==true) {
			$orders = new Model('shop_orders');
			$uid = $_SESSION['home_userinfo']['uname'];
			$data = $orders -> where('uid="'.$uid.'"') -> select();
			/**
	        *分页
	        */
	        //当前页码
	        $page = empty($_GET['page']) ? 1 : $_GET['page'];
	        //获取数据的总条数
	        $count = $orders -> count();
	        //显示的条数
	        $pageShow = 5;  
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
	        $data = $orders -> where('resource = 1') -> limit($limitParam,$pageShow) -> select();
			include_once('./View/Home/Cart/list.html');
		} else {
			echo '<script>alert("请先登录");location.href="./index.php?m=home&c=login&a=login";</script>';
		}
	}
	/**
	 * 删除订单
	 */
	public function delect()
	{
		$id = $_GET['oid'];
		$orders = new Model('shop_orders');
		$data = $orders->find($id);
        $data['resource'] = 0;
        $res = $orders->update($data,$id);
		if($res > 0) {
            echo '<script>alert("删除成功");location.href="./index.php?m=home&c=order&a=list";</script>';
        } else {
            echo '<script>alert("删除失败");location.href="./index.php?m=home&c=order&a=list";</script>';
        }
	}
}