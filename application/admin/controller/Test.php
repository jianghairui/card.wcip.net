<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2019/11/4
 * Time: 16:35
 */
namespace app\admin\controller;

class Test extends Base {


    public function index() {
        $arr = range('a','e');
        $this->excep($this->cmd,var_export($arr,true));
//        halt($arr);
        echo 'SUCCESS' . date('Y-m-d H:i:s');

    }




}