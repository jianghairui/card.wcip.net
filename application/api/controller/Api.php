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
            $order = [
                'sort' => 'ASC',
                'id' => 'DESC'
            ];
            $data['card_type'] = Db::table('mp_card_type')->order($order)->select();
            $data['card_camp'] = Db::table('mp_card_camp')->select();
            $data['card_attr'] = Db::table('mp_card_attr')->select();
            $data['card_ability'] = Db::table('mp_card_ability')->order($order)->select();
            $data['card_version'] = Db::table('mp_card_version')->select();
            $data['resource'] = config('card.resource');
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($data);

    }

    //卡牌列表
    public function cardList() {

        $post['camp_id'] = input('post.camp_id',[]);
        $post['type_id'] = input('post.type_id',[]);
        $post['attr_id'] = input('post.attr_id',[]);
        $post['ability_id'] = input('post.ability_id',[]);
        $post['version_id'] = input('post.version_id',[]);
        $post['resource'] = input('post.resource',[]);
        $post['search'] = input('post.search');

        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;

        $whereCard = " `status`=1 ";

        $order = " `sort` ASC,`id` DESC ";

        if(is_array($post['camp_id']) && !empty($post['camp_id'])) {
            $camp_ids = $post['camp_id'];
            $whereCard .= " AND (0 ";
            foreach ($camp_ids as $v) {
                $whereCard .= " OR FIND_IN_SET(".$v.",camp_id) ";
            }
            $whereCard .= ") ";
        }

        if(is_array($post['type_id']) && !empty($post['type_id'])) {
            $type_ids = $post['type_id'];
            $whereCard .= " AND (0 ";
            foreach ($type_ids as $v) {
                $whereCard .= " OR FIND_IN_SET(".$v.",type_id) ";
            }
            $whereCard .= ") ";
        }

        if(is_array($post['attr_id']) && !empty($post['attr_id'])) {
            $attr_ids = $post['attr_id'];
            $whereCard .= " AND (0 ";
            foreach ($attr_ids as $v) {
                $whereCard .= " OR FIND_IN_SET(".$v.",attr_id) ";
            }
            $whereCard .= ") ";
        }

        if(is_array($post['ability_id']) && !empty($post['ability_id'])) {
            $ability_ids = $post['ability_id'];
            $whereCard .= " AND (0 ";
            foreach ($ability_ids as $v) {
                $whereCard .= " OR FIND_IN_SET(".$v.",ability_id) ";
            }
            $whereCard .= ") ";
        }

        if(is_array($post['version_id']) && !empty($post['version_id'])) {
            $whereCard .= " AND `version_id` IN (" . implode(',',$post['version_id']) . ")";
        }

        if(is_array($post['resource']) && !empty($post['resource'])) {
            $resource_arr = $post['resource'];
            if(in_array(7,$resource_arr)) {
                $resource_arr = array_merge($resource_arr,range(8,20));
            }
            $whereCard .= " AND `resource` IN (" . implode(',',$resource_arr) . " ) ";
        }

        if($post['search']) {
//            $whereCard .= " AND card_name LIKE \"%".$post['search']."%\"";
            $whereCard .= " AND (`card_name` LIKE \"%".$post['search']."%\" OR `desc` LIKE \"%".$post['search']."%\") ";
        }

        try {
            $query_sql = "SELECT * FROM mp_card WHERE " . $whereCard . " ORDER BY " . $order . " LIMIT " . (($curr_page-1)*$perpage) . "," . $perpage;
            $list = Db::query($query_sql);
        } catch (\Exception $e) {
            $this->excep($this->cmd,$query_sql);
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

            $info['type'] = [];
            $type_ids = explode(',',$info['type_id']);
            foreach ($type_ids as $v) {
                if(isset($type[$v])) {
                    $info['type'][] = $type[$v];
                }
            }
            $info['camp'] = [];
            $camp_ids = explode(',',$info['camp_id']);
            foreach ($camp_ids as $v) {
                if(isset($camp[$v])) {
                    $info['camp'][] = $camp[$v];
                }
            }

            $info['attr'] = [];
            $attr_ids = explode(',',$info['attr_id']);
            foreach ($attr_ids as $v) {
                if(isset($attr[$v])) {
                    $info['attr'][] = $attr[$v];
                }
            }

            $info['ability'] = [];
            $ability_ids = explode(',',$info['ability_id']);
            foreach ($ability_ids as $v) {
                if(isset($ability[$v])) {
                    $info['ability'][] = $ability[$v];
                }
            }

            $info['version'] = isset($version[$info['version_id']]) ? $version[$info['version_id']] : '未知';
            switch ($info['resource']) {
                case -2:$info['resource'] = '无';break;
                case -1:$info['resource'] = 'X';break;
                default:;
            }
            unset($info['type_id']);unset($info['camp_id']);unset($info['attr_id']);unset($info['ability_id']);unset($info['version_id']);

            //卡牌在列表中位置
            $whereCard = " `status`=1 ";

            $order = " `sort` ASC,`id` DESC ";

            if(is_array($post['camp_id']) && !empty($post['camp_id'])) {
                $camp_ids = $post['camp_id'];
                $whereCard .= " AND (0 ";
                foreach ($camp_ids as $v) {
                    $whereCard .= " OR FIND_IN_SET(".$v.",camp_id) ";
                }
                $whereCard .= ") ";
            }

            if(is_array($post['type_id']) && !empty($post['type_id'])) {
                $type_ids = $post['type_id'];
                $whereCard .= " AND (0 ";
                foreach ($type_ids as $v) {
                    $whereCard .= " OR FIND_IN_SET(".$v.",type_id) ";
                }
                $whereCard .= ") ";
            }

            if(is_array($post['attr_id']) && !empty($post['attr_id'])) {
                $attr_ids = $post['attr_id'];
                $whereCard .= " AND (0 ";
                foreach ($attr_ids as $v) {
                    $whereCard .= " OR FIND_IN_SET(".$v.",attr_id) ";
                }
                $whereCard .= ") ";
            }

            if(is_array($post['ability_id']) && !empty($post['ability_id'])) {
                $ability_ids = $post['ability_id'];
                $whereCard .= " AND (0 ";
                foreach ($ability_ids as $v) {
                    $whereCard .= " OR FIND_IN_SET(".$v.",ability_id) ";
                }
                $whereCard .= ") ";
            }

            if(is_array($post['version_id']) && !empty($post['version_id'])) {
                $whereCard .= " AND version_id IN (" . implode(',',$post['version_id']) . ")";
            }

            if(is_array($post['resource']) && !empty($post['resource'])) {
                $resource_arr = $post['resource'];
                if(in_array(7,$resource_arr)) {
                    $resource_arr = array_merge($resource_arr,range(8,20));
                }
                $whereCard .= " AND resource IN (" . implode(',',$resource_arr) . " ) ";
            }

            if($post['search']) {
                $whereCard .= " AND card_name LIKE \"%".$post['search']."%\"";
            }

            $query_sql = "SELECT id FROM mp_card WHERE " . $whereCard . " ORDER BY " . $order;
            $result = Db::query($query_sql);
            $card_ids = [];
            foreach ($result as $v) {
                $card_ids[] = $v['id'];
            }
            $offset = array_search(intval($val['id']),$card_ids);
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
        $order = ['g.sort'=>'ASC','g.id'=>'DESC'];
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

    public function ruleList() {
        $whereRule = [
            ['status','=',1]
        ];
        $order = ['sort'=>'ASC'];
        try {
            $list = Db::table('mp_game_rule')->where($whereRule)->field('id,title,create_time')->order($order)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }

    public function ruleDetail() {
        $val['id'] = input('post.id');
        checkPost($val);
        $where = [
            ['id','=',$val['id']]
        ];
        try {
            $info = Db::table('mp_game_rule')->where($where)->find();
            if(!$info) {
                return ajax('invalid id',-4);
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