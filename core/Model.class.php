<?php

/**
 * 数据库操作类
 * User: find35.com
 * Date: 15/12/27
 * Time: 下午4:52
 */
class Model
{
    protected static $_instance;
    protected static $_link;
    protected $whereStr = '';//用来存储where条件

    /**
     * 单例模式 不允许在类外对类进行实例化
     */
    private function __construct(){}

    /**
     * 获得类的实例
     * @return mixed|Model
     */
    public static function getStringleton(){
        //判断我们类的实例是否存在，没有则创建之
        if(!isset(self::$_instance)){
            
            self::$_instance = new self();
        }
        
        //连接数据库
        self::connect(HOST,UNAME,UPASS,DBNAME);
        return self::$_instance;
    }

    /**
     * 连接数据库方法
     * @param $host 服务器地址
     * @param $username 数据库账号
     * @param $userpass 数据库密码
     * @param $dbname 操作的数据库名
     */
    protected static function connect($host,$username,$userpass,$dbname){
        try{
            self::$_link = new PDO("mysql:host={$host};dbname={$dbname}",$username,$userpass);
            //设置返回数据的类型
            self::$_link->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
            //设置操作数据库的报错模式
//            self::$_link->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
//            if(self::$_link){
//                echo '连接数据库成功';
//            }
        }catch (PDOException $e){
            MyException::showError($e->getMessage());
        }

    }

    /**
     * 直接执行sql语句查询数据库的方法
     * @param $sql mysql语句
     * @param array $where 条件数据
     * @return mixed 成功数组
     */
    public function queryString($sql,$where=array()){
        try{
            //使用预处理语句来执行sql
            $stmt = self::$_link->prepare($sql);

            //判断是否有条件数组
            if(empty($where)){
                $stmt->execute();
            }else{
                $stmt->execute($where);
            }

            //判断执行是否成功
            if($stmt->rowCount() > 0){
                return $stmt->fetchAll();
            }else {
                //得到错误信息
                $err = $stmt->errorInfo();
                throw new PDOException($err[2]);
            }
        }catch(PDOException $e){
            MyException::showError($e->getMessage());
        }
    }

    /**
     * 内部sql处理好的查询方法
     * @param $table 表名
     * @param array $where 查询条件
     * @return mixed 成功返回数组
     */
    public function select($table,$where = array()){
        $sql = "select * from {$table} ";
        if(!empty($this->whereStr)){
            $sql .= $this->whereStr;
        }
        try{
            //执行sql语句
            $stmt = self::$_link->prepare($sql);
            if(empty($where)){
                $stmt->execute();
            }else{
                $stmt->execute($where);
            }

            //判断是否成功，如果不成功爆出异常
            if($stmt->rowCount() > 0){
                return $stmt->fetchAll();
            }else{
                $err = $stmt->errorInfo();
                return $err[2];
            }
        }catch(PDOException $e){
            MyException::showError($e->getMessage());
        }
    }

    /**
     * where条件方法
     * @param string $whereStr
     * @return $this
     */
    public function where($whereStr = ''){
        $this->whereStr = $whereStr;
        return $this;//返回当前对象
    }

    /**
     * 查询单条数据的方法
     * @param $table 表名
     * @param array $where 查询的条件，:key=value
     * @return mixed 成功返回数组
     */
    public function find($table,$where = array()){
        $sql = "select * from {$table} ";

        if(!empty($this->whereStr)){
            $sql .= $this->whereStr;
        }

        try{
            //执行sql
            $stmt = self::$_link->prepare($sql);
            if(empty($where)){
                $stmt->execute();
            }else{
                $stmt->execute($where);
            }

            //判断是否成功
            if($stmt->rowCount() > 0){
                $result = $stmt->fetchAll();
                return $result[0];
            }else{
                $err = $stmt->errorInfo();
                throw new PDOException($err[2]);
            }
        }catch(PDOException $e){
            MyException::showError($e->getMessage());
        }
    }

    /**
     * 添加单条数据的方法
     * @param $table 表名
     * @param array(':username'=>'zhang6',':userpass'=>md5(123456),':create_time'=>time()) $data
     * @return int 成功返回1
     */
    public function insert($table,array $data){
        $sql = "insert into {$table} ";
        $fields = "";
        $values = "";
        foreach($data as $k => $v){
            $fields .= ltrim($k,":").",";
            $values .= "'".ltrim($v,":")."',";
        }
        $sql .= "(".rtrim($fields,",").") values (".rtrim($values,",").")";

        try{
            //开启事务
            self::$_link->beginTransaction();

            $stmt = self::$_link->prepare($sql);
            $stmt->execute($data);

            if($stmt->rowCount() > 0){
                self::$_link->commit();
                return 1;
            }else{
                self::$_link->rollback();
                $err = $stmt->errorInfo();
                throw new PDOException($err[2]);
            }

        }catch(PDOException $e){
            MyException::showError($e->getMessage());
        }
    }

    /**
     * 更新数据
     * @param $table 表名
     * @param array $data array(':username'=>'lisi',':userpass'=>md5(456789));
     * @param array $where array(':id'=>9);
     * @return int
     */
    public function update($table,array $data,array $where){
        $sql = "update {$table} set ";
        $set_str = '';
        foreach($data as $k => $v){
            $set_str .= ltrim($k,":")."=$k,";
        }

        $sql .= rtrim($set_str,',').' '.$this->whereStr;

        try{
            self::$_link->beginTransaction();
            $stmt = self::$_link->prepare($sql);
            $data2 = array_merge($data,$where);
            $stmt->execute($data2);

            if($stmt->rowCount() > 0){
                self::$_link->commit();
                return 1;
            }else{
                self::$_link->rollback();
                $err = $stmt->errorInfo();
                throw new PDOException($err[2]);
            }
        }catch(PDOException $e){
            MyException::showError($e->getMessage());
        }
    }

    /**
     * 删除数据方法
     * @param $table 表名
     * @param array $where
     * @return int
     */
    public function delete($table,array $where){
        $sql = "delete from {$table} ".$this->whereStr;
        try{
            self::$_link->beginTransaction();
            $stmt = self::$_link->prepare($sql);
            $stmt->execute($where);

            if($stmt->rowCount() > 0){
                self::$_link->commit();
                return 1;
            }else{
                self::$_link->rollback();
                $err = $stmt->errorInfo();
                throw new PDOException($err[2]);
            }
        }catch(PDOException $e){
            MyException::showError($e->getMessage());
        }
    }

    /**
     * 析构方法
     * 销毁对象
     */
    public function __destruct(){
        self::$_link = null;
    }
}