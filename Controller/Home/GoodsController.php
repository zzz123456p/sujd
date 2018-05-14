<?php
class GoodsController extends HomeCommonController
{
	/**
	 * 列表页
	 * @return [type] [description]
	 */
	public function index()
	{
		$cate_data = $this->getCatePid(0);
		$id = $_GET['cid'];
		$goods = new Model('shop_goods');
		$data = $goods -> where('cid='.$id .' and status=1') -> select();
		/**
        *分页
        */
        //当前页码
        $page = empty($_GET['page']) ? 1 : $_GET['page'];
        //获取数据的总条数
        $count = $goods -> count();
        //显示的条数
        $pageShow = 8;  
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
        $data = $goods -> where('resource = 1 and num > 0') -> limit($limitParam,$pageShow) -> select();

		include_once('./View/Home/Goods/index.html');
	}
	/**
	 * 详情页
	 */
	public function show()
	{	
		$cate_data = $this -> getCatePid(0);
		//获取要显示数据的id
		$id = $_GET['gid'];
		//实例化数据表
		$goods = new Model('shop_goods');
		//根据id查询单条数据
		$data = $goods -> find($id);

		include_once('./View/Home/Goods/show.html');
	}
	
}