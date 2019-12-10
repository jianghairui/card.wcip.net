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
        $path = ROOT_PATH . '/log/aaaa.jpg';
        $position =  strripos($path,'/');
        echo substr($path,0,$position);
//        var_dump($place);
//        echo strrchr($path,'/');

    }




}