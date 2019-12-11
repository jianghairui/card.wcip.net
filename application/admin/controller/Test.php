<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2019/11/4
 * Time: 16:35
 */
namespace app\admin\controller;

use think\Controller;

class Test extends Controller {


    public function index() {
        $arr = range('');
        echo 'SUCCESS' . date('Y-m-d H:i:s');

    }




}