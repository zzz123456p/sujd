<?php
class ResourceController extends AdminCommonController
{
	/**
	 * 用户回收
	 * @return [type] [description]
	 */
	public function users()
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
        $data = $users -> where('resource = 0') -> select();
       
        //显示用户列表页面
        include_once('./View/Admin/Resource/resource_users.html');
	}
	/**
	 * 用户恢复
	 * @return [type] [description]
	 */
	public function recovery_user()
	{
		//接收要删除的数据id
        $id = $_GET['uid'];
        //连接数据库
        $users = new Model('shop_users');
        //执行删除
        $data = $users->find($id);
        $data['resource'] = 1;
        $res = $users->update($data,$id);
        //通过影响行数判断结果
        if($res > 0) {
            echo '<script>alert("恢复成功");location.href="./index.php?m=admin&c=Resource&a=users";</script>';
        } else {
            echo '<script>alert("恢复失败");location.href="./index.php?m=admin&c=Resource&a=users";</script>';
        }
	}
	/**
	 * 用户删除
	 * @return [type] [description]
	 */
	public function delete_user()
	{
		//接收要删除的数据id
        $id = $_GET['uid'];
        //连接数据库
        $users = new Model('shop_users');
        //执行删除
        $res = $users->delete($id);
        //通过影响行数判断结果
        if($res > 0) {
            echo '<script>alert("删除成功");location.href="./index.php?m=admin&c=Resource&a=users";</script>';
        } else {
            echo '<script>alert("删除失败");location.href="./index.php?m=admin&c=Resource&a=users";</script>';
        }
	}


	/**
	 * 商品回收
	 * @return [type] [description]
	 */
	public function goods()
	{
		//引入数据库
        $goods = new Model('shop_goods');
        
        if(!empty($_GET['search_type']) && !empty($_GET['keywords'])){
            //接收要搜索的条件
            $search_type = $_GET['search_type'];
            //对用户输入的数据进行匹配
            $keywords = $_GET['keywords'];
            
        $goods -> where($search_type.' like "%'.$keywords.'%"');
        }
        $data = $goods -> where('resource = 0') -> select();
        include_once('./View/Admin/Resource/resource_goods.html');
	}
	/**
	 * 商品恢复
	 * @return [type] [description]
	 */
	public function recovery_goods()
	{
		//接收要删除的数据id
        $id = $_GET['gid'];
        //连接数据库
        $goods = new Model('shop_goods');
        //执行删除
        $data = $goods->find($id);
        $data['resource'] = 1;
        $res = $goods->update($data,$id);
        //通过影响行数判断结果
        if($res > 0) {
            echo '<script>alert("恢复成功");location.href="./index.php?m=admin&c=Resource&a=goods";</script>';
        } else {
            echo '<script>alert("恢复失败");location.href="./index.php?m=admin&c=Resource&a=goods";</script>';
        }
	}
	/**
	 * 商品删除
	 * @return [type] [description]
	 */
	public function delete_goods()
	{
		//接收要删除的数据id
        $id = $_GET['gid'];
        //连接数据库
        $goods = new Model('shop_goods');
        //执行删除
        $res = $goods->delete($id);
        //通过影响行数判断结果
        if($res > 0) {
            echo '<script>alert("删除成功");location.href="./index.php?m=admin&c=Resource&a=goods";</script>';
        } else {
            echo '<script>alert("删除失败");location.href="./index.php?m=admin&c=Resource&a=goods";</script>';
        }
	}


	/**
	 * 订单回收
	 * @return [type] [description]
	 */
	public function orders()
	{
		$orders = new Model('shop_orders');
		if(!empty($_GET['search_type']) && !empty($_GET['keywords'])){
            //接收要搜索的条件
            $search_type = $_GET['search_type'];
            //对用户输入的数据进行匹配
            $keywords = $_GET['keywords'];
            
       	$orders -> where($search_type.' like "%'.$keywords.'%"');
        }
        $data = $orders -> where('resource = 0') -> select();
		include_once('./View/Admin/resource/resource_list.html');
	}
	/**
	 * 订单恢复
	 * @return [type] [description]
	 */
	public function recovery_orders()
	{
		//接收要删除的数据id
        $id = $_GET['oid'];
        //连接数据库
        $orders = new Model('shop_orders');
        //执行删除
        $data = $orders->find($id);
        $data['resource'] = 1;
        $res = $orders->update($data,$id);
        //通过影响行数判断结果
        if($res > 0) {
            echo '<script>alert("恢复成功");location.href="./index.php?m=admin&c=Resource&a=orders";</script>';
        } else {
            echo '<script>alert("恢复失败");location.href="./index.php?m=admin&c=Resource&a=orders";</script>';
        }
	}
	/**
	 * 订单删除
	 * @return [type] [description]
	 */
	public function delete_orders()
	{
		//接收要删除的数据id
        $id = $_GET['oid'];
        //连接数据库
        $orders = new Model('shop_orders');
        //执行删除
        $res = $orders->delete($id);
        //通过影响行数判断结果
        if($res > 0) {
            echo '<script>alert("删除成功");location.href="./index.php?m=admin&c=Resource&a=orders";</script>';
        } else {
            echo '<script>alert("删除失败");location.href="./index.php?m=admin&c=Resource&a=orders";</script>';
        }
	}
}