<?php

class Model
{
    protected $pdo = null;//声明pdo的连接方式
    protected $tableName = null;//声明数据表名称
    protected $pk = 'id';//主键
    protected $fields = [];//当前数据表所有的字段
    protected $where = [];
    protected $order = '';
    protected $limit = '';
    /**
    *初始化信息
    *   数据连接信息
    *   数据表名称
    */
    public function __construct($tableName)
    {
        //初始化表名
        $this->tableName = $tableName;
        //1.连接数据库
        $this->pdo = new PDO(DB_DSN.':host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET.';port='.DB_PORT,DB_USER,DB_PASS);
        //初始化字段
        $this->getFields();
    }
    /**
    *加载数据的主键和所有字段
    *@return [type] [description]
    */
    public function getFields()
    {
        //1.准备sql
        $sql = 'desc '. $this->tableName;
        //2.发送
        $stmt = $this->pdo -> query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($data as $k=>$v){
            //获取所有的字段
            $this->fields[] = $v['Field'];
            //获取主键
            if($v['Key'] == 'PRI') {
                $this->pk = $v['Field'];
            }
        }
    }
    /**
    *[select 查询数据表中所有的数据]
    *@return [type] [返回结果]
    */
    public function select()
    {
        //2.准备sql语句
        $sql = 'select * from '.$this->tableName;
        
        //拼装where
        if(count($this->where) > 0){
            $sql .= ' where '.implode(' and ',$this->where);
        }
        
        //拼接order
        if(!empty($this->order)) {
            $sql .= ' order by '.$this->order;
        }
        
        //拼装limit
        if(!empty($this->limit)) {
            $sql .= ' limit '.$this->limit;
        }

        //3.执行发生sql
        $stmt = $this->pdo -> query($sql);
        //清空条件
        /* $this->where = [];
        $this->order = '';
        $this->limit = ''; */
        
        //4.处理返回结果
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
    *[find 单条查询]
    *@param [type] $id [查询id]
    *@return [type]    [返回结果]
    */
    public function find($id = 0)
    {
        //判断请求参数的类型
        if(intval($id) <= 0) {
            die('参数非法，请输入正确的参数...');
        }
        //2.准备sql语句
        $sql = 'select * from '.$this->tableName.' where '.$this->pk.'='.$id;
        //3.执行发送sql
        $stmt = $this->pdo -> query($sql);
        //4.处理返回结果
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
    *删除
    *@param integer $id [要删除的id]
    *@return [type]     [受影响的行数]
    */
    public function delete ($id = 0)
    {
        //判断请求参数的类型
        if(intval($id) <=0 ) {
            die('参数非法，请输入正确的参数...');
        }
        //1.准备sql语句
        $sql = 'delete from '.$this->tableName.' where '.$this->pk.' = '.$id;
        //2.发送 并且返回影响行数
        return $this->pdo -> exec($sql);
    }
    
    public function insert($data)
    {
    
        //检测参数
        if(count($data) <= 0) {
            return [];
            exit;
        }
        $fieldsKey = '';
        $fieldsVal = '';
        //遍历表单的值 目的为了拼接sql参数
        foreach ($data as $k=>$v) {
            //过滤非法字段
            if(in_array($k,$this->fields) && ($k != $this->pk)) {
                $fieldsKey .= ','.$k;
                $fieldsVal .= ','."'$v'";
            }
        }
        //清除左侧的，逗号
        $fieldsKey = ltrim($fieldsKey,',');
        $fieldsVal = ltrim($fieldsVal,',');
        
        $sql = 'insert into '.$this->tableName.'('.$fieldsKey.') values('.$fieldsVal.')';
        //发送 返回形象行数
        return $this->pdo -> exec($sql);
    }
    
    /**
    *修改
    *@param  [type] $data [要修改的数据]
    *@param  [type] $id   [修改的id]
    *@return [type]       [受影响的行数]
    */
    public function update($data,$id)
    {
        //检测参数
        if(count($data) <= 0 || (int)$id <= 0) {
            return [];
            exit;
        }
        
        $str = '';
        foreach($data as $k=>$v){
            //排除非法字段
            if(in_array($k,$this->fields) && ($k != $this->pk)) {
                $str .=','.$k.'='."'$v'";
            }
        }
        $str = ltrim ($str,',');
        //1.准备sql
        $sql = 'update '.$this->tableName.' set '.$str.' where '.$this->pk.' = '.$id;
        //发送sql
        return $this->pdo->exec($sql);//返回影响行数
    }
    
    /**
    *发送原生的sql
    *@param  [type] $sql [sql语句]
    *@return [type]      [结果]
    */
    public function query($sql)
    {
        //查询
        $stmt = $this->pdo->query($sql);
        return $stmt -> fetchAll(PDO::FETCH_ASSOC);
    }

    public function exec($sql)
    {
        //查询
        return $this->pdo->exec($sql);
    }
    
    /**
    *where 条件
    *@param  [type] $apram [需要传入的where条件]
    *@return [type]        [对象]
    */
    public function where($param)
    {
        $this->where[] = $param;
        return $this;
    }
    
    /**
    *排序
    *@return [type] [本对象]
    */
    public function order($param)
    {
        $this->order = $param;
        return $this;
    }
    
    /**
    *limit 方法
    *@param  [type] $m [跳过几条]
    *@param  [type] $n [显示几条]
    *@return [type]    [本对象]
    */
    public function limit($m,$n)
    {
        $this->limit = $m.','.$n;
        return $this;
    }
    
    /**
    *统计总条数
    *@return [type] [总条数]
    */
    public function count()
    {
        $sql = 'select count(*) as count from '.$this->tableName;
        //拼装where条件
        //拼装where
        if(count($this->where) > 0) {
            $sql .= ' where '.implode(' and ',$this->where);
        }
        $stmt = $this->pdo -> query($sql);
        //清空参数
        //$this->where = [];
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        return $data;
    }
}