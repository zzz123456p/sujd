<?php
class GoodsController extends AdminCommonController
{
    /**
    *获取类别的方法
    */
    public function getCate()
    {
        //实例化数据表
        $cate = new Model('shop_cate');
        $cate_data = $cate -> query("select *,concat(path,',',cid) as paths from shop_cate order by paths");
        //便利数据 处理字段名称
        foreach($cate_data as $k=>$v){
            //统计path中的逗号，
            $n = substr_count($v['path'],',');
            //处理cname
            $cate_data[$k]['cname'] = str_repeat ("|----",$n).$v['cname'];
        }
        return $cate_data;
    }
    /**
    *加载显示页面
    */
    public function index()
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
        
        
        //实例化数据表对商品进行查询
        //遍历商品表
        /*  foreach ($data as $k => $v){  
            //取到商品表中的cid
            $id = $v['cid'];
        } 
        //实例化分类表
        $good = new Model('shop_cate');
        //根据id取到查询相对应的数据
        $data_good = $good->query("select cname from shop_cate where cid = $id");
        foreach ($data_good as $k => $v) {
            $good = $v['cname'];
        } */
        /* echo '<pre>';
        var_dump($data_good);
        die; */
        
        /**
        *分页
        */
        //当前页码
        $page = empty($_GET['page']) ? 1 : $_GET['page'];
        //获取数据的总条数
        $count = $goods -> count();
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
        $data = $goods -> where('resource = 1') -> limit($limitParam,$pageShow) -> select();
        include_once('./View/Admin/Goods/index.html');
    }
    
    /**
    *加载添加商品页面
    */
    public function add()
    {
        $cate_data = $this->getCate();
        include_once('./View/Admin/Goods/add.html');
    } 
    
    /**
    *执行添加
    */
    public function insert()
    {   
        $data = $_POST;
        $a = new Model('shop_cate');
        $cate_id = $a -> find($data['cid']);
        $data['flid'] = $cate_id['cname'];
        
        //判断内容是否为空
        if(empty($_POST['gname']) || empty($_POST['price']) || empty($_POST['num'])){
            echo '<script>alert("内容不能为空，请重新填写");location.href="./index.php?m=admin&c=goods&a=add";</script>';
            die;
        } 
        //文件上传
        $up = new UP(UP_DIR,UP_TYPE,UP_SIZE);
        $up_res = $up->upload($_FILES['gpic']);
        if($up_res){
            // 接受添加的数据 并且处理
            $data['status'] = $_POST['status'];//上架   2下架
            $data['ctime'] = time();
            $data['gpic'] = $up_res;
        }else{
            echo '文件上传失败';
            exit;
        }
        //执行上传
        //实例化数据表
        $goods = new Model('shop_goods');
        $res = $goods -> insert($data);
        // 通过影响行数 判断结果
        if($res > 0){
            echo '<script>alert("添加成功");location.href="./index.php?m=admin&c=goods&a=index";</script>';
        }else{
            echo '<script>alert("添加失败");location.href="./index.php?m=admin&c=goods&a=add";</script>';
        }
    }
    /**
    *执行修改
    */
    public function delete()
    {
        //获取要删除的数据id
        $id = $_GET['gid'];
        //实例化数据表
        $goods = new Model('shop_goods');
        $data = $goods->find($id);
        $data['resource'] = 0;
        $res = $goods->update($data,$id);
        //通过影响行数判断结果
         if($res > 0){
            echo '<script>alert("删除成功");location.href="./index.php?m=admin&c=goods&a=index";</script>';
        }else{
            echo '<script>alert("删除失败");location.href="./index.php?m=admin&c=goods&a=index";</script>';
        }
    }
    
    /**
    *加载修改页面
    */
    public function edit()
    {
        //获取要修改的商品id
        $id = $_GET['gid'];
        //实例化数据表
        $cate_data = $this->getCate();
        $goods = new Model('shop_goods');
        $data = $goods -> find($id);
        //显示修改页面
        include_once('./View/Admin/Goods/edit.html');
    }
    /**
     * 执行修改
     * @return [type] [description]
     */
    public function update()
    {   
        $data = $_POST;
        $a = new Model('shop_cate');
        $cate_id = $a -> find($data['cid']);
        $data['flid'] = $cate_id['cname'];
        //实例化数据表
        $goods = new Model('shop_goods');
        //获取要修改的数据
        //接收要修改数据的id
        $id = $_GET['gid'];
        //文件上传
        if(empty($_FILES['gpic']['name'])){
            //实例化数据表
            $goods = new Model('shop_goods');
            //查询修改id的单条数据
            $goods_data = $goods -> find($id);
            $data['gpic'] = $goods_data['gpic'];
        }else{
            $up = new UP(UP_DIR,UP_TYPE,UP_SIZE);
            $up_res = $up->upload($_FILES['gpic']);

            if($up_res){
                // 接受添加的数据 并且处理
                $data = $_POST;
                $data['status'] = $_POST['status'];//上架   2下架
                $data['ctime'] = time();
                $data['gpic'] = $up_res;
            }else{
                echo '文件上传失败';
                exit;
            }
        }
        //对数据进行修改
        $res = $goods -> update($data,$id);
        //根据返回影响行数判断结果
        if($res > 0) {
            echo '<script>alert("修改成功");location.href="./index.php?m=admin&c=goods&a=index";</script>';
        } else {
            echo '<script>alert("修改失败");location.href="./index.php?m=admin&c=goods&a=index";</script>';
        }
    }
}