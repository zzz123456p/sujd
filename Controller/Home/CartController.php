<?php
class CartController extends HomeCommonController
{
	/**
	 * 加入购物车成功页面
	 */
	public function add()
	{
		$cate_data = $this->getCatePid(0);
		//获取加入购物车数据id
		$id = $_GET['gid'];
		//实例化数据表
		$goods = new Model('shop_goods');
		//根据id找到单条数据
		$data = $goods -> find($id);
		//定义商品件数
		$data['n'] = 1;
		$data['xj'] = ($data['price'] * $data['n']);
		//把所有的数据存到session
		$_SESSION['cart'][$id] = $data;
		//计算总计
		$sums = 0;
		$nums = 0;
		
		foreach ($_SESSION['cart'] as $key => $value) {
			$sums += $value['xj'];//计算总和
			$nums += $value['n'];
		}
		//将商品件数和价钱总和保存
		$_SESSION['order']['nums'] = $nums;
		$_SESSION['order']['sums'] = $sums;
		include_once('./View/Home/Cart/cart.html');
	}

	public function index()
	{
		//$cate_data = $this->getCatePid(0);
		if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
			$data = $_SESSION['cart'];
			foreach ($data as $key => $value) {
				$id = $value['gid'];
			}
			include_once('./View/Home/Cart/index.html');
		} else {
			include_once('./View/Home/Cart/none.html');
			exit;
		}
	}

	/**
	 * 商品加
	 */
	public function jia()
	{
		//通过id获得相应商品
		$id = $_GET['gid'];
		$data_one = $_SESSION['cart'][$id];
		//获得相应商品的数量
		$n = $data_one['n']+1;
		$data_one['n'] = $n;
		//计算价钱
		$xj = $data_one['price'] * $n;
		$data_one['xj'] = $xj;
		//赋值
		$_SESSION['cart'][$id] = $data_one;
		//计算总计
		$sums = 0;
		$nums = 0;

		foreach ($_SESSION['cart'] as $key => $value) {
			$sums += $value['xj'];//计算总和
			$nums += $value['n'];
		}
		//将商品件数和价钱总和保存
		$_SESSION['order']['nums'] = $nums;
		$_SESSION['order']['sums'] = $sums;
		//将数据输出
		header('location:./index.php?m=home&c=cart&a=index');
	}

	/**
	 * 商品减
	 */
	public function jian()
	{
		//通过id获得相应商品
		$id = $_GET['gid'];
		$data_one = $_SESSION['cart'][$id];
		//获得相应商品的数量
		$n = $data_one['n']-1;
		if ($n <= 0) {
			$n = 1;
		}
		$data_one['n'] = $n;
		//计算价钱
		$xj = $data_one['price'] * $n;
		$data_one['xj'] = $xj;
		//赋值
		$_SESSION['cart'][$id] = $data_one;
		//计算总计
		$sums = 0;
		$nums = 0;

		foreach ($_SESSION['cart'] as $key => $value) {
			$sums += $value['xj'];//计算总和
			$nums += $value['n'];
		}
		//将商品件数和价钱总和保存
		$_SESSION['order']['nums'] = $nums;
		$_SESSION['order']['sums'] = $sums;
		//将数据输出
		header('location:./index.php?m=home&c=cart&a=index');
	}
	/**
	 * 删除购物车
	 * @return [type] [description]
	 */
	public function delect()
	{
		$id = $_GET['gid'];
		unset($_SESSION['cart'][$id]);
		//计算总计
		$sums = 0;
		$nums = 0;

		foreach ($_SESSION['cart'] as $key => $value) {
			$sums += $value['xj'];//计算总和
			$nums += $value['n'];
		}
		//将商品件数和价钱总和保存
		$_SESSION['order']['nums'] = $nums;
		$_SESSION['order']['sums'] = $sums;
		//将数据输出
		header('location:./index.php?m=home&c=cart&a=index');
	}
	
}