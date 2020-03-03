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

        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;

        $whereCard = [
            ['status','=',1]
        ];
        $order = ['sort' => 'ASC', 'id' => 'DESC'];
        if(is_array($post['attr_id']) && !empty($post['attr_id'])) { $whereCard[] = ['attr_id','in',$post['attr_id']]; }
        if(is_array($post['type_id']) && !empty($post['type_id'])) { $whereCard[] = ['type_id','in',$post['type_id']]; }
        if(is_array($post['camp_id']) && !empty($post['camp_id'])) { $whereCard[] = ['camp_id','in',$post['camp_id']]; }
        if(is_array($post['ability_id']) && !empty($post['ability_id'])) { $whereCard[] = ['ability_id','in',$post['ability_id']]; }
        if(is_array($post['version_id']) && !empty($post['version_id'])) { $whereCard[] = ['version_id','in',$post['version_id']]; }
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
            $list = Db::table('mp_card')->where($whereCard)->limit(($curr_page-1)*$perpage,$perpage)->order($order)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
       return ajax($list);

    }

    //卡牌详情
    public function cardDetail() {
        $val['id'] = input('post.id');
        checkPost($val);
        $post['attr_id'] = input('post.attr_id',[]);
        $post['resource'] = input('post.resource',[]);
        $post['type_id'] = input('post.type_id',[]);
        $post['camp_id'] = input('post.camp_id',[]);
        $post['ability_id'] = input('post.ability_id',[]);
        $post['version_id'] = input('post.version_id',[]);
        $post['search'] = input('post.search');
        try {
            //卡牌详情
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
            $info['camp'] = isset($camp[$info['camp_id']]) ? $camp[$info['camp_id']] : '未知';
            $info['attr'] = isset($attr[$info['attr_id']]) ? $attr[$info['attr_id']] : '未知';
            $info['ability'] = isset($ability[$info['ability_id']]) ? $ability[$info['ability_id']] : '未知';
            $info['version'] = isset($version[$info['version_id']]) ? $version[$info['version_id']] : '未知';
            switch ($info['resource']) {
                case -2:$info['resource'] = '资源-事件';break;
                case -1:$info['resource'] = 'X';break;
                default:;
            }
            unset($info['type_id']);unset($info['camp_id']);unset($info['attr_id']);unset($info['ability_id']);unset($info['version_id']);
            //卡牌在列表中位置
            $whereCardList = [
                ['status','=',1]
            ];
            $order = ['id'=>'DESC'];
            if(is_array($post['attr_id']) && !empty($post['attr_id'])) { $whereCardList[] = ['attr_id','in',$post['attr_id']]; }
            if(is_array($post['type_id']) && !empty($post['type_id'])) { $whereCardList[] = ['type_id','in',$post['type_id']]; }
            if(is_array($post['camp_id']) && !empty($post['camp_id'])) { $whereCardList[] = ['camp_id','in',$post['camp_id']]; }
            if(is_array($post['ability_id']) && !empty($post['ability_id'])) { $whereCardList[] = ['ability_id','in',$post['ability_id']]; }
            if(is_array($post['version_id']) && !empty($post['version_id'])) { $whereCardList[] = ['version_id','in',$post['version_id']]; }
            if(is_array($post['resource']) && !empty($post['resource'])) {
                $resource_arr = $post['resource'];
                if(in_array(7,$resource_arr)) {
                    $whereCardList[] = ['resource','in',array_merge($resource_arr,range(8,20))];
                }else {
                    $whereCardList[] = ['resource','in',$resource_arr];
                }
            }
            if($post['search']) { $whereCardList[] = ['card_name','like',"%{$post['search']}%"]; }
            $card_ids = Db::table('mp_card')->where($whereCardList)->order($order)->column('id');
            $offset = array_search($val['id'],$card_ids);
            if($offset !== false) {
                $info['prev_card_id'] = isset($card_ids[$offset-1]) ? $card_ids[$offset-1] : null;
                $info['next_card_id'] = isset($card_ids[$offset+1]) ? $card_ids[$offset+1] : null;
                $info['page'] = $offset + 1;

            }else {
                $info['page'] = null;
            }
            $info['total_count'] = count($card_ids);
            $info['card_ids'] = $card_ids;

            $whereCollection = [
                ['uid','=',$this->myinfo['id']],
                ['card_id','=',$val['id']]
            ];
            $collection_exist = Db::table('mp_card_collection')->where($whereCollection)->find();
            if($collection_exist) {
                $info['collect'] = true;
            }else {
                $info['collect'] = false;
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }

    //收藏卡牌
    public function cardCollect() {
        $val['card_id'] = input('post.card_id');
        checkPost($val);
        $val['uid'] = $this->myinfo['id'];
        $val['create_time'] = time();
        try {
            $card_exist = Db::table('mp_card')->where('id','=',$val['card_id'])->find();
            if(!$card_exist) {
                return ajax('invalid card_id',-4);
            }
            $whereCollection = [
                ['uid','=',$val['uid']],
                ['card_id','=',$val['card_id']]
            ];
            $collection_exist = Db::table('mp_card_collection')->where($whereCollection)->find();
            if($collection_exist) {
                Db::table('mp_card_collection')->where($whereCollection)->delete();
                return ajax(false);
            }
            Db::table('mp_card_collection')->insert($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax(true);
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


    //商品列表
    public function recommendGoodsList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',4);

        $where = [
            ['g.status','=',1],
            ['g.del','=',0]
        ];
        $order = ['g.id'=>'DESC'];
        try {
            $list = Db::table('mp_goods')->alias('g')
                ->join('mp_goods_cate c','g.cate_id=c.id','left')
                ->where($where)
                ->field("g.id,g.name,g.origin_price,g.price,g.sales,g.desc,g.pics,c.cate_name")
                ->order($order)
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        foreach ($list as &$v) {
            $v['cover'] = unserialize($v['pics'])[0];
            unset($v['pics']);
        }
        return ajax($list);
    }
    //游戏规则
    public function gameRule() {
        $where = [
            ['id','=',1]
        ];
        try {
            $info = Db::table('mp_game_rule')->Where($where)->find();
            if(!$info) {
                return ajax('not found',-4);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }






    //关于我们
    public function aboutUs() {
        try {
            $wherePlat = [
                ['id','=',1]
            ];
            $info = Db::table('mp_plat')->where($wherePlat)->find();
            if(!$info) {
                return ajax('data not exists',-1);
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