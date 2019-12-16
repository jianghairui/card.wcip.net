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

        echo randname(10);
//        halt($arr);
//        echo 'SUCCESS' . date('Y-m-d H:i:s');

    }




}