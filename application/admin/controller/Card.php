<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2019/12/10
 * Time: 13:45
 */
namespace app\admin\controller;

use think\Db;
class Card extends Base {

    //卡牌列表
    public function cardList() {

        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $whereCard = [
            ['status','=',1],
            ['del','=',0]
        ];

        if($param['search']) {
            $whereCard[] = ['card_name','like',"%{$param['search']}%"];
        }

        if($param['datemin']) {
            $whereCard[] = ['create_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['datemin'])))];
        }
        if($param['datemax']) {
            $whereCard[] = ['create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])))];
        }

        try {
            $count = Db::table('mp_card')->where($whereCard)->select();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_card')->where($whereCard)->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        $this->assign('count',$count);
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function attrList() {

    }

    public function abilityList() {

    }

    public function campList() {

    }

    public function typeList() {

    }

    public function versionList() {

    }




}