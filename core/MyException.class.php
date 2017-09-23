<?php

/**
 * 用户自定义的错误异常类
 * User: find35.com
 * Date: 15/12/26
 * Time: 下午7:48
 */
//PHP具有很多异常处理类，其中Exception是所有异常处理的基类。
// message 异常消息内容
// code 异常代码
// file 抛出异常的文件名
// line 抛出异常在该文件的行数

// 其中常用的方法有：

// getTrace 获取异常追踪信息
// getTraceAsString 获取异常追踪信息的字符串
// getMessage 获取出错信息
class MyException extends Exception
{
    /**
     * 错误页面加载
     * @param $msg
     */
    public static function showError($msg){
        $err_dir = 'app/views/error/error.php';
        //判断错误页面是否存在
        if(file_exists($err_dir)){
            require $err_dir;
        }
    }
}