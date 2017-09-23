<?php

/**
 * 前台首页控制器
 * User: find35.com
 * Date: 15/12/24
 * Time: 下午5:44
 */
class Home extends Controller
{
    public function index($data = array()){
    	$list = array('a'=>1,'b'=>2,'c'=>3);
    	 $db = Model::getStringleton();
    	 //查询某个表
		$result = $db->select('users');
		var_dump($result);die;
        //加载首页页面
        $this->show('index/index',[$data,$list]);


       
    }
}





