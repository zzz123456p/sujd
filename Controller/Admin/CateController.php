<?php
class CateController extends AdminCommonController
{
    /**
    *获取类别方法
    */
    public function getCate()
    {
        //实例化数据表
        $cate = new Model('shop_cate');
        $cate_data = $cate -> query("select *,concat(path,',',cid) as paths from shop_cate order by paths");
        foreach($cate_data as $k => $v){
            //统计path中的逗号，
            $n = substr_count($v['path'],',');
            //处理cname
            $cate_data[$k]['cname'] = str_repeat ('|----',$n).$v['cname'];
        }
        return $cate_data;
    }
    
    /**
    *分类显示
    */
    public function index()
    {
        $cate_data = $this->getCate();
        // 显示分类主页面
        include_once('./View/Admin/Cate/index.html');
    }
    /**
    *分类添加
    */
    public function add()
    {
        //实例化数据表
        $cate_data = $this->getCate();
        // 显示分类添加页面
        include_once('./View/Admin/Cate/add.html');
    }
    
    /**
    *插入分类
    */
    public function insert()
    {
        // 实例化数据表
        $cate = new Model('shop_cate');
        //接收数据
        $data = $_POST;
        
        //判断内容不能为空
        if(empty($data['cname'])){
            echo '<script>alert("内容不能为空，请重新填写");location.href="./index.php?m=admin&c=cate&a=add";</script>';
            exit;
        }
        
        //判断是否是顶级分类
        if($data['pid'] == '0'){
            //处理数据
            $data ['path'] = 0;
        } else {
            //通过父级的path和父级的id 使用，拼接
            $parent_data = $cate->find($data['pid']);
            //拼接数据
            $data['path'] = $parent_data['path'].','.$parent_data['cid'];
        }
        //执行添加
		$res = $cate->insert($data);
        // 通过影响行数 判断结果
        if($res > 0){
            echo '<script>alert("添加成功");location.href="./index.php?m=admin&c=cate&a=index";</script>';
        }else{
            echo '<script>alert("添加失败");location.href="./index.php?m=admin&c=cate&a=index";</script>';
        }
        include_once('./View/Admin/Cate/add.html');
    }
    
    /**
    *删除操作
    */
    public function delete()
    {
        //获取要删除的id
        $id = $_GET['cid'];
        //实例化数据表
        $cate = new Model('shop_cate');
        //获取当前要删除的分类下面是否有子分类
        $data = $cate->where('pid='.$id)->select();
        if(!empty($data)){
            echo '<script>alert("当前类别下面有子分类不允许删除....");location.href="./index.php?m=admin&c=cate&a=index";</script>';
            exit;
        }
        //返回受影响的行数
        $res = $cate -> delete($id);
        //通过影响行数判断结果
         if($res > 0){
            echo '<script>alert("删除成功");location.href="./index.php?m=admin&c=cate&a=index";</script>';
        }else{
            echo '<script>alert("删除失败");location.href="./index.php?m=admin&c=cate&a=index";</script>';
        }
    }
    
    /**
    *进入修改页面
    */
    public function edit()
    {
        $cate_data = $this->getCate();
        //接收要修改的数据的id
        $id = $_GET['cid'];
        //实例化数据表
        //通过id查询数据
        $cate = new Model('shop_cate');
        $data = $cate -> find($id);
        include_once('./View/Admin/Cate/edit.html');
    }
    
    /**
    *执行修改操作
    */
    public function update()
    {
        // 实例化数据表
        $cate = new Model('shop_cate');
        //接收数据
        $id = $_GET['cid'];
        $data = $_POST;
        //判断内容不能为空
        if(empty($data['cname'])){
            echo '<script>alert("内容不能为空，请重新填写");location.href="./index.php?m=admin&c=cate&a=edit&cid='.$id.'";</script>';
            exit;
        }
        //判断是否是顶级分类
        if($data['pid'] == '0'){
            //处理数据
            $data ['path'] = 0;
        } else {
            //通过父级的path和父级的id 使用，拼接
            $parent_data = $cate->find($data['pid']);
            //拼接数据
            $data['path'] = $parent_data['path'].','.$parent_data['cid'];
        }
        //判断当前类别下面是否有子类
        //获取当前要修改的分类下面是否有子分类
        $child_data = $cate -> where('pid='.$id) -> select();
        if(!empty($child_data)){
            echo '<script>alert("当前类别下有子分类不能修改");location.href="./index.php?m=admin&c=cate&a=edit&cid='.$id.'";</script>';
			exit;
        }
        //执行添加
		$res = $cate->update($data,$id);
        // 通过影响行数 判断结果
        if($res > 0){
            echo '<script>alert("修改成功");location.href="./index.php?m=admin&c=cate&a=index";</script>';
        }else{
            echo '<script>alert("修改失败");location.href="./index.php?m=admin&c=cate&a=edit&cid='.$id.'";</script>';
        }
    }
    /**
     * 加载添加子类页面
     * @return [type] [description]
     */
    public function readd()
    {
        $cate_data = $this->getCate();
        //接收要添加子类的数据的id
        $id = $_GET['cid'];
        //实例化数据表
        //通过id查询数据
        $cate = new Model('shop_cate');
        $data = $cate -> find($id);
        include_once('./View/Admin/Cate/readd.html');
    }
    /**
     * 执行添加子类
     * @return [type] [description]
     */
    public function reinsert()
    {
        // 实例化数据表
        $cate = new Model('shop_cate');
        //接收数据
        $id = $_GET['cid'];
        $data = $_POST;
        //判断内容不能为空
        if(empty($data['cname'])){
            echo '<script>alert("内容不能为空，请重新填写");location.href="./index.php?m=admin&c=cate&a=readd&cid='.$id.'";</script>';
            exit;
        }
        //判断是否是顶级分类
        if($data['pid'] == '0'){
            //处理数据
            $data ['path'] = 0;
        } else {
            //通过父级的path和父级的id 使用，拼接
            $parent_data = $cate->find($data['pid']);
            //拼接数据
            $data['path'] = $parent_data['path'].','.$parent_data['cid'];
        }
        //执行添加
		$res = $cate->insert($data,$id);
        // 通过影响行数 判断结果
        if($res > 0){
            echo '<script>alert("添加成功");location.href="./index.php?m=admin&c=cate&a=index";</script>';
        }else{
            echo '<script>alert("添加失败");location.href="./index.php?m=admin&c=cate&a=readd&cid='.$id.'";</script>';
        }
    }
}