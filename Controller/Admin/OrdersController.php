<?php
class OrdersController extends AdminCommonController
{
	public function index()
	{
		$orders = new Model('shop_orders');
		if(!empty($_GET['search_type']) && !empty($_GET['keywords'])){
            //接收要搜索的条件
            $search_type = $_GET['search_type'];
            //对用户输入的数据进行匹配
            $keywords = $_GET['keywords'];
            
       	$orders -> where($search_type.' like "%'.$keywords.'%"');
        }

		/**
        *分页
        */
        //当前页码
        $page = empty($_GET['page']) ? 1 : $_GET['page'];
        //获取数据的总条数
        $count = $orders -> count();
        //显示的条数
        $pageShow = 10;  
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
		include_once('./View/Admin/Orders/index.html');
	}
	/**
	 * 显示修改页面
	 * @return [type] [description]
	 */
	public function edit()
	{
		//获取id
		$id = $_GET['oid'];
		//实例化数据表
		$orders = new Model('shop_orders');
		//查询单条信息
		$data = $orders -> find($id);
		include_once('./View/Admin/Orders/edit.html');
	}
	/**
	 * 执行修改
	 * @return [type] [description]
	 */
	public function update()
	{
		//获取id
		$id = $_GET['oid'];
		//接收全部数据
		$data = $_POST;
		//实例化数据表
		$orders = new Model('shop_orders');
		//执行修改
		$res = $orders -> update($data,$id);
		if ($res > 0) {
			echo '<script>alert("修改成功");location.href="./index.php?m=admin&c=orders&a=index";</script>';
		} else {
			echo '<script>alert("添加失败");location.href="./index.php?m=admin&c=orders&a=edit&oid='.$id.'";</script>';
		}
	}
}