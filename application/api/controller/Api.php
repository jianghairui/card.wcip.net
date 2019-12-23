<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/8
 * Time: 11:11
 */

namespace app\api\controller;
use my\Sendsms;
use think\Db;
use think\Exception;

class Api extends Base
{
    //获取轮播图列表
    public function slideList() {
        $where = [
            ['status', '=', 1]
        ];
        try {
            $list = Db::table('mp_slideshow')->where($where)
                ->field('id,title,url,pic')
                ->order(['sort' => 'ASC'])->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }

    //获取卡牌筛选条件
    public function cardParams() {
        try {
            $data['card_type'] = Db::table('mp_card_type')->select();
            $data['card_camp'] = Db::table('mp_card_camp')->select();
            $data['card_attr'] = Db::table('mp_card_attr')->select();
            $data['card_ability'] = Db::table('mp_card_ability')->select();
            $data['card_version'] = Db::table('mp_card_version')->select();
            $data['resource'] = config('card.resource');
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($data);
    }
    //卡牌列表
    public function cardList() {

        $post['attr_id'] = input('post.attr_id',[]);
        $post['resource'] = input('post.resource',[]);
        $post['type_id'] = input('post.type_id',[]);
        $post['camp_id'] = input('post.camp_id',[]);
        $post['ability_id'] = input('post.ability_id',[]);
        $post['version_id'] = input('post.version_id',[]);
        $post['search'] = input('post.search');
        $page['query'] = http_build_query(input('post.'));

        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;

        $whereCard = [
            ['status','=',1],
            ['del','=',0]
        ];
        if(is_array($post['attr_id']) && !empty($post['attr_id'])) { $whereCard[] = ['attr_id','in',$post['attr_id']]; }
        if(is_array($post['type_id']) && !empty($post['attr_id'])) { $whereCard[] = ['type_id','in',$post['type_id']]; }
        if(is_array($post['camp_id']) && !empty($post['attr_id'])) { $whereCard[] = ['camp_id','in',$post['camp_id']]; }
        if(is_array($post['ability_id']) && !empty($post['attr_id'])) { $whereCard[] = ['ability_id','in',$post['ability_id']]; }
        if(is_array($post['version_id']) && !empty($post['attr_id'])) { $whereCard[] = ['version_id','in',$post['version_id']]; }
        if(is_array($post['resource']) && !empty($post['resource'])) {
            $resource_arr = $post['resource'];
            if(in_array(7,$resource_arr)) {
                $whereCard[] = ['resource','in',array_merge($resource_arr,range(8,20))];
            }else {
                $whereCard[] = ['resource','in',$resource_arr];
            }
        }
        if($post['search']) { $whereCard[] = ['card_name','like',"%{$post['search']}%"]; }

        try {
            $list = Db::table('mp_card')->where($whereCard)->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
       return ajax($list);

    }
    //卡牌详情
    public function cardDetail() {
        $val['id'] = input('post.id');
        checkPost($val);
        try {
            $whereCard = [
                ['id','=',$val['id']]
            ];
            $info = Db::table('mp_card')->where($whereCard)->find();
            if(!$info) {
                return ajax('invalid id',-4);
            }
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

            $info['type'] = isset($type[$info['type_id']]) ? $type[$info['type_id']] : '未知';
            $info['camp'] = isset($type[$info['camp_id']]) ? $type[$info['camp_id']] : '未知';
            $info['attr'] = isset($attr[$info['attr_id']]) ? $attr[$info['attr_id']] : '未知';
            $info['ability'] = isset($ability[$info['ability_id']]) ? $ability[$info['ability_id']] : '未知';
            $info['version'] = isset($version[$info['version_id']]) ? $version[$info['version_id']] : '未知';
            switch ($info['resource']) {
                case -2:$info['resource'] = '资源-事件';break;
                case -1:$info['resource'] = 'x';break;
                default:;
            }
            unset($info['type_id']);unset($info['camp_id']);unset($info['attr_id']);unset($info['ability_id']);unset($info['version_id']);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }

    //公告列表
    public function articleList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $curr_page = $curr_page ? $curr_page:1;
        $perpage = $perpage ? $perpage:10;
        $where = [
            ['status','=',1]
        ];
        $order = ['sort'=>'ASC','id'=>'DESC'];
        try {
            $list = Db::table('mp_article')
                ->where($where)
                ->order($order)
                ->field('id,title,desc,pic,create_time')
                ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        $ret['list'] = $list;
        return ajax($ret);
    }

    //公告详情
    public function articleDetail() {
        $val['id'] = input('post.id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $info = Db::table('mp_article')->where($where)->field('title,desc,content,pic,create_time')->find();
            if(!$info) {
                return ajax('invalid id',-4);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }






    //收集formid
    public function collectFormid() {
        $val['formid'] = input('post.formid');
        checkPost($val);
        if($val['formid'] == 'the formId is a mock one') {
            return ajax();
        }
        $val['uid'] = $this->myinfo['id'];
        $val['create_time'] = time();
        try {
            Db::table('mp_formid')->insert($val);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($val);
    }

}