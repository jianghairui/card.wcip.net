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
            $count = Db::table('mp_card')->where($whereCard)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_card')->where($whereCard)->limit(($curr_page-1)*$perpage,$perpage)->select();
            $card_type = Db::table('mp_card_type')->select();
            $card_camp = Db::table('mp_card_camp')->select();
            $card_attr = Db::table('mp_card_attr')->select();
            $card_ability = Db::table('mp_card_ability')->select();
            $card_version = Db::table('mp_card_version')->select();
            $type = [];
            $camp = [];
            $attr = [];
            $ability = [];
            $version = [];
            foreach ($card_type as $v) {$type[$v['id']] = $v['type_name'];}
            foreach ($card_camp as $v) {$camp[$v['id']] = $v['camp_name'];}
            foreach ($card_attr as $v) {$attr[$v['id']] = $v['attr_name'];}
            foreach ($card_ability as $v) {$ability[$v['id']] = $v['ability_name'];}
            foreach ($card_version as $v) {$version[$v['id']] = $v['version_name'];}
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('count',$count);
        $this->assign('list',$list);
        $this->assign('card_type',$card_type);
        $this->assign('card_camp',$card_camp);
        $this->assign('card_attr',$card_attr);
        $this->assign('card_version',$card_version);
        $this->assign('card_ability',$card_ability);
        $this->assign('type',$type);
        $this->assign('camp',$camp);
        $this->assign('attr',$attr);
        $this->assign('ability',$ability);
        $this->assign('version',$version);
        $this->assign('page',$page);
        return $this->fetch();

    }

    public function cardAdd() {
        if(request()->isPost()) {
            $val['card_name'] = input('post.card_name');
            $val['attr_id'] = input('post.attr_id');
            $val['resource'] = input('post.resource');
            $val['type_id'] = input('post.type_id');
            $val['camp_id'] = input('post.camp_id');
            $val['ability_id'] = input('post.ability_id');
            $val['version_id'] = input('post.version_id');
            checkInput($val);
            $val['desc'] = input('post.desc');
            $val['create_time'] = time();
            $val['update_time'] = $val['create_time'];

            try {
                if(isset($_FILES['file'])) {
                    $info = upload('file',$this->upload_base_path . 'card/');
                    if($info['error'] === 0) {
                        $val['pic'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }else {
                    return ajax('请上传封面图',-1);
                }
                Db::table('mp_card')->insert($val);
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        try {
            $card_type = Db::table('mp_card_type')->select();
            $card_camp = Db::table('mp_card_camp')->select();
            $card_attr = Db::table('mp_card_attr')->select();
            $card_version = Db::table('mp_card_version')->select();
            $card_ability = Db::table('mp_card_ability')->select();
            $card_resource = config('card.resource');
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('card_type',$card_type);
        $this->assign('card_camp',$card_camp);
        $this->assign('card_attr',$card_attr);
        $this->assign('card_version',$card_version);
        $this->assign('card_ability',$card_ability);
        $this->assign('card_resource',$card_resource);
        return $this->fetch();
    }

    public function cardDetail() {
        $val['id'] = input('param.id',0);
        try {
            $whereCard = [
                ['id','=',$val['id']]
            ];
            $card_exist = Db::table('mp_card')->where($whereCard)->find();
            if(!$card_exist) {
                die('非法操作');
            }
            $card_type = Db::table('mp_card_type')->select();
            $card_camp = Db::table('mp_card_camp')->select();
            $card_attr = Db::table('mp_card_attr')->select();
            $card_version = Db::table('mp_card_version')->select();
            $card_ability = Db::table('mp_card_ability')->select();
            $card_resource = config('card.resource');
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('info',$card_exist);
        $this->assign('card_type',$card_type);
        $this->assign('card_camp',$card_camp);
        $this->assign('card_attr',$card_attr);
        $this->assign('card_version',$card_version);
        $this->assign('card_ability',$card_ability);
        $this->assign('card_resource',$card_resource);
        return $this->fetch();
    }

    public function cardMod() {
        $val['card_name'] = input('post.card_name');
        $val['attr_id'] = input('post.attr_id');
        $val['resource'] = input('post.resource');
        $val['type_id'] = input('post.type_id');
        $val['camp_id'] = input('post.camp_id');
        $val['ability_id'] = input('post.ability_id');
        $val['version_id'] = input('post.version_id');
        $val['id'] = input('post.id');
        checkInput($val);
        $val['desc'] = input('post.desc');
        $val['create_time'] = time();
        $val['update_time'] = $val['create_time'];

        try {
            $whereCard = [
                ['id','=',$val['id']]
            ];
            $card_exist = Db::table('mp_card')->where($whereCard)->find();
            if(!$card_exist) {
                return ajax('非法参数',-1);
            }
            if(isset($_FILES['file'])) {
                $info = upload('file',$this->upload_base_path . 'card/');
                if($info['error'] === 0) {
                    $val['pic'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            Db::table('mp_card')->where($whereCard)->update($val);
        } catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(), -1);
        }
        if(isset($val['pic'])) {
            @unlink($card_exist['pic']);
        }
        return ajax();
    }

    public function cardHide() {

    }

    public function cardShow() {

    }

    public function cardDel() {

    }


    //卡牌属性
    public function attrList() {

    }

    //卡牌能力
    public function abilityList() {

    }

    //卡牌阵营
    public function campList() {

    }

    //卡牌类型
    public function typeList() {

    }

    //卡牌版本
    public function versionList() {

    }




}