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


    public function test() {
        $where[] = ['id1','in',[1,3]];
        $where[] = ['id2','in',[3,5]];
        $where[] = ['id3','in',[]];
        $where[] = ['id4','in',[]];
    }




}