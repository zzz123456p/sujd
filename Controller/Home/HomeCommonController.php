<?php
class HomeCommonController
{
	/**
	 * 检测用户是否登录
	 */
	public function __construct()
	{
		isset($_SESSION['home_login']) ? $_SESSION['home_login'] : $_SESSION['home_login'] = false;
		//检测用户是否登录
	}
	/**
	 * 公共设置
	 * @param  integer $pid [description]
	 * @return [type]       [description]
	 */
	public function getCatePid($pid = 0)
	{
		//实例化数据表
		$cate = new Model('shop_cate');
		// 查询顶级分类
		$temp_data = $cate -> where('pid='.$pid) -> select();
		//定义一个空变量来接收数据
		$cate_data = [];
		//遍历查询出来的数据
		foreach ($temp_data as $k => $v) {
			// 根据cid查询数据，把结果塞回到$v['sub']
			$v['sub'] = $this->getCatePid($v['cid']);//$v['sub'] 定义一个新下标接收数据
			//赋值
			$cate_data[] = $v;
		}
		//返回
		return $cate_data;
		
	}
	/**
	 * 搜索
	 * @return [type] [description]
	 */
	public function search()
	{	
		$cate_data = $this->getCatePid(0);
		//引入数据库
		$goods = new Model('shop_goods');
		if(!empty($_GET['keywords'])){
			//接收数据
	        $keywords = $_GET['keywords'];
	        //对用户输入的数据进行匹配
	        $goods -> where('gname like "%'.$keywords.'%" or flid like "%'.$keywords.'%"');
   		}

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

        $data = $goods -> limit($limitParam,$pageShow) -> select();

        include_once('./View/Home/goods/index.html');
	}
}