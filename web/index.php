<?php
/**
 * 项目后台入口文件
 * User: find35.com
 * Date: 15/12/27
 * Time: 下午3:12
 */

echo "<meta charset='utf-8'>";
require_once '../core/App.class.php';

//注册一个
define('APP','web');

spl_autoload_register(array('App','myAutoloader'));

try{
    App::run();
    throw new MyException();
}catch(MyException $e){
    $e->showError(($e->getMessage()));
}