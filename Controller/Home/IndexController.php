<?php
class IndexController extends HomeCommonController
{
	
	/**
	 * 加载主页
	 * @return [type] [description]
	 */
	public function index()
	{
		// 获取顶级分类
		$cate_data = $this->getCatePid(0);
		/*echo '<pre>';
		var_dump($cate_data);
		die;*/

		
		//加载主页
		include_once('./View/Home/Index/index.html');
	}
	
}