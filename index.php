<?php
/**
 * 项目前台入口文件
 * User: find35.com
 * Date: 15/12/24
 * Time: 下午5:15
 */
header("content-type:text/html;charset=utf-8");//默认字符集
require_once 'core/App.class.php';//引入类
require_once 'config/constants.php';//引入数据库配置文件

//注册一个
define('APP','app');//所有页面都可以使用的 常量
spl_autoload_register(array('App','myAutoloader'));
try{
	//如果执行的过程中发生异常的话直接去catch语句
    App::run();
}catch(MyException $e){
	//有$e来获取异常信息,使用catch来处理抛出的异常
	//抛出异常
    $e->showError($e->getMessage());
}

// $db = Model::getStringleton();
// var_dump($db);
//直接执行sql语句
//$result = $db->queryString('select * from users where username=:username',array(':username'=>'zhang'));

//查询某个表
//$result = $db->select('users');

//查询某个表，并增加where条件
//$result = $db->where('where username=:username')->select('users',array(':username'=>'zhang'));

//查询单条数据
//$result = $db->where('where id=:id')->find('users',array(':id'=>'3'));

//插入数据
//$data = array(':username'=>'zhang6',':userpass'=>md5(123456),':create_time2'=>time());
//$result = $db->insert('users',$data);

//更新数据
//$data = array(':username'=>'lisi',':userpass'=>md5(456789));
//$where = array(':id'=>9);
//$result = $db->where('where id=:id')->update('users',$data,$where);

//删除数据
// $result = $db->where('where id=:id')->delete('users',array('id'=>9));

// echo "<pre>";
// print_r($result);