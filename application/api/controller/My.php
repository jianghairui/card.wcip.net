<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/11
 * Time: 16:00
 */
namespace app\api\controller;
use my\Kuaidiniao;
use Qiniu\Tests\DownloadTest;
use think\Db;
use EasyWeChat\Factory;

class My extends Base {

    //获取个人信息
    public function mydetail() {
        return ajax($this->myinfo);
    }
    //修改头像
    public function modMyInfo() {
        $user = $this->myinfo;
        $val['nickname'] = input('post.nickname');
        $val['realname'] = input('post.realname');
        $val['sex'] = input('post.sex');
        $val['tel'] = input('post.tel');
        checkPost($val);
        $val['id'] = $this->myinfo['id'];
        $avatar = input('post.avatar');

        if(!$this->msgSecCheck($val['nickname'])) {
            return ajax('昵称包含敏感词',21);
        }
        if(!$this->msgSecCheck($val['realname'])) {
            return ajax('姓名包含敏感词',22);
        }
        if(!is_tel($val['tel'])) { return ajax('无效的手机号',6); }
        try {
            if($avatar) {
                if (substr($avatar,0,4) == 'http') {
                    $val['avatar'] = $avatar;
                }else {
                    if(!file_exists($avatar)) {
                        return ajax('avatar not exist',23);
                    }
                    $val['avatar'] = rename_file($avatar,$this->rename_base_path . 'avatar/');
                }
            }else {
                return ajax('请传入图片',3);
            }
            $whereUser = [['id','=',$val['id']]];
            Db::table('mp_user')->where($whereUser)->update($val);
        } catch (\Exception $e) {
            if ($val['avatar'] != $user['avatar']) {
                @unlink($val['avatar']);
            }
            return ajax($e->getMessage(), -1);
        }
        if ($val['avatar'] != $user['avatar']) {
            @unlink($user['avatar']);
        }
        return ajax();

    }

    //卡牌列表
    public function myCollectCardList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $curr_page = $curr_page ? $curr_page : 1;
        $perpage = $perpage ? $perpage : 10;

        $whereCard = [
            ['c.status','=',1],
            ['co.uid','=',$this->myinfo['id']]
        ];
        $order = ['co.create_time'=>'DESC'];
        try {
            $list = Db::table('mp_card_collection')->alias('co')
                ->join('mp_card c','co.card_id=c.id','left')
                ->where($whereCard)
                ->order($order)
                ->field('c.*')
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }

    /*------ 商品订单管理 START------*/
    //我的订单列表
    public function orderList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $status = input('post.status','');
        $where = "del=0 AND uid=".$this->myinfo['id'];
        $where .= " AND `status` IN ('0','1','2','3') AND `del`=0 AND `refund_apply`=0";
        $order = " ORDER BY `id` DESC";
        $orderby = " ORDER BY `d`.`id` DESC";
        if($status !== '') {
            $where .= " AND status=" . $status;
        }
        try {
            $list = Db::query("SELECT 
`o`.`id`,`o`.`pay_order_sn`,`o`.`pay_price`,`o`.`total_price`,`o`.`carriage`,`o`.`create_time`,`o`.`refund_apply`,`o`.`status`,`o`.`refund_apply`,`d`.`order_id`,`d`.`goods_id`,`d`.`num`,`d`.`unit_price`,`d`.`goods_name`,`d`.`attr`,`d`.`evaluate`,`g`.`pics` 
FROM (SELECT * FROM mp_order WHERE " . $where . $order ." LIMIT ".($curr_page-1)*$perpage.",".$perpage.") `o` 
LEFT JOIN `mp_order_detail` `d` ON `o`.`id`=`d`.`order_id`
LEFT JOIN `mp_goods` `g` ON `d`.`goods_id`=`g`.`id`
" . $orderby);

            $order_id = [];
            $newlist = [];
            foreach ($list as $v) {
                $order_id[] = $v['id'];
            }
            $uniq_order_id = array_unique($order_id);
            foreach ($uniq_order_id as $v) {
                $child = [];
                foreach ($list as $li) {
                    if($li['order_id'] == $v) {
                        $data['id'] = $li['id'];
                        $data['pay_order_sn'] = $li['pay_order_sn'];
                        $data['total_price'] = $li['total_price'];
                        $data['carriage'] = $li['carriage'];
                        $data['status'] = $li['status'];
                        $data['refund_apply'] = $li['refund_apply'];
                        $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
                        $data_child['goods_id'] = $li['goods_id'];
                        $data_child['cover'] = unserialize($li['pics'])[0];
                        $data_child['goods_name'] = $li['goods_name'];
                        $data_child['num'] = $li['num'];
                        $data_child['unit_price'] = $li['unit_price'];
                        $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                        $data_child['attr'] = $li['attr'];
                        $data_child['evaluate'] = $li['evaluate'];
                        $child[] = $data_child;
                    }
                }
                $data['child'] = $child;
                $newlist[] = $data;
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        return ajax($newlist);
    }
    //我的售后列表
    public function refundList() {
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $type = input('post.type',1);
        if(!in_array($type,[0,1,2,3])) {
            return ajax($type,-4);
        }
        $where = "del=0 AND uid=".$this->myinfo['id'];
        $order = " ORDER BY `id` DESC";
        $orderby = " ORDER BY `d`.`id` DESC";
        if($type == 1) {
            $where .= " AND refund_apply=1";
        }else if($type == 2){
            $where .= " AND refund_apply=2";
        }else {
            $where .= " AND refund_apply IN (1,2)";
        }
        try {
            $list = Db::query("SELECT 
`o`.`id`,`o`.`pay_order_sn`,`o`.`pay_price`,`o`.`total_price`,`o`.`carriage`,`o`.`create_time`,`o`.`refund_apply`,`o`.`status`,`o`.`refund_apply`,`d`.`order_id`,`d`.`goods_id`,`d`.`num`,`d`.`unit_price`,`d`.`goods_name`,`d`.`attr`,`g`.`pics` 
FROM (SELECT * FROM mp_order WHERE " . $where . $order . " LIMIT ".($curr_page-1)*$perpage.",".$perpage.") `o` 
LEFT JOIN `mp_order_detail` `d` ON `o`.`id`=`d`.`order_id`
LEFT JOIN `mp_goods` `g` ON `d`.`goods_id`=`g`.`id`
".$orderby);

            $order_id = [];
            $newlist = [];
            foreach ($list as $v) {
                $order_id[] = $v['id'];
            }
            $uniq_order_id = array_unique($order_id);
            foreach ($uniq_order_id as $v) {
                $child = [];
                foreach ($list as $li) {
                    if($li['order_id'] == $v) {
                        $data['id'] = $li['id'];
                        $data['pay_order_sn'] = $li['pay_order_sn'];
                        $data['total_price'] = $li['total_price'];
                        $data['carriage'] = $li['carriage'];
                        $data['status'] = $li['status'];
                        $data['refund_apply'] = $li['refund_apply'];
                        $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
                        $data_child['goods_id'] = $li['goods_id'];
                        $data_child['cover'] = unserialize($li['pics'])[0];
                        $data_child['goods_name'] = $li['goods_name'];
                        $data_child['num'] = $li['num'];
                        $data_child['unit_price'] = $li['unit_price'];
                        $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                        $data_child['attr'] = $li['attr'];
                        $child[] = $data_child;
                    }
                }
                $data['child'] = $child;
                $newlist[] = $data;
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        return ajax($newlist);
    }
    //查看订单详情
    public function orderDetail() {
        $val['order_id'] = input('post.order_id');
        checkPost($val);
        $where = [
            ['o.id','=',$val['order_id']],
            ['o.uid','=',$this->myinfo['id']],
            ['o.del','=',0]
        ];
        try {
            $list = Db::table('mp_order')->alias('o')
                ->join("mp_order_detail d","o.id=d.order_id","left")
                ->join("mp_goods g","d.goods_id=g.id","left")
                ->where($where)
                ->field("o.id,o.pay_order_sn,o.pay_price,o.total_price,o.carriage,o.receiver,o.tel,o.address,o.create_time,o.refund_apply,o.status,o.pay_time,o.tracking_name,o.tracking_num,d.goods_id AS goods_id,d.id AS order_detail_id,d.order_id,d.num,d.unit_price,d.goods_name,d.attr,d.evaluate,g.pics")->select();
            if(!$list) {
                return ajax('invalid order_id',24);
            }

            $data = [];
            $child = [];
            foreach ($list as $li) {
                $data['pay_order_sn'] = $li['pay_order_sn'];
                $data['receiver'] = $li['receiver'];
                $data['tel'] = $li['tel'];
                $data['address'] = $li['address'];
                $data['total_price'] = $li['total_price'];
                $data['carriage'] = $li['carriage'];
                $data['amount'] = $li['total_price'] - $data['carriage'];
                $data['create_time'] = date('Y-m-d H:i',$li['create_time']);
                $data['refund_apply'] = $li['refund_apply'];
                $data['status'] = $li['status'];
                $data['pay_time'] = $li['pay_time'];
                $data['tracking_name'] = $li['tracking_name'];
                $data['tracking_num'] = $li['tracking_num'];
                $data_child['goods_id'] = $li['goods_id'];
                $data_child['order_detail_id'] = $li['order_detail_id'];
                $data_child['cover'] = unserialize($li['pics'])[0];
                $data_child['goods_name'] = $li['goods_name'];
                $data_child['num'] = $li['num'];
                $data_child['unit_price'] = $li['unit_price'];
                $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                $data_child['attr'] = $li['attr'];
                $data_child['evaluate'] = $li['evaluate'];
                $data_child['comment'] = Db::table('mp_goods_comment')->where('order_detail_id','=',$li['order_detail_id'])->value('comment');
                $child[] = $data_child;
            }
            $data['child'] = $child;
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($data);

    }
    //申请退款
    public function refundApply() {
        $val['order_id'] = input('post.order_id');
        $val['reason'] = input('post.reason');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['order_id']],
                ['uid','=',$this->myinfo['id']],
                ['status','in',[1,2,3]],
                ['del','=',0]
            ];
            $exist = Db::table('mp_order')->where($where)->find();
            if(!$exist) {
                return ajax( 'invalid order_id',24);
            }
            $update_data = [
                'refund_apply' => 1,
                'reason' => $val['reason'],
                'refund_apply_time' => time()
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //确认收货
    public function orderConfirm() {
        $val['order_id'] = input('post.order_id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['order_id']],
                ['uid','=',$this->myinfo['id']],
                ['status','=',2],
                ['del','=',0]
            ];
            $exist = Db::table('mp_order')->alias('o')->where($where)->find();
            if(!$exist) {
                return ajax( 'invalid order_id',24);
            }
            $update_data = [
                'status' => 3,
                'finish_time' => time()
            ];
            Db::table('mp_order')->where($where)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //取消订单
    public function orderCancel() {
        $val['order_id'] = input('post.order_id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['order_id']],
                ['uid','=',$this->myinfo['id']],
                ['status','=',0],
                ['del','=',0]
            ];
            $exist = Db::table('mp_order')->alias('o')->where($where)->find();
            if(!$exist) {
                return ajax( 'invalid order_id',24);
            }
            $update_data = [
                'del' => 1
            ];
            Db::table('mp_order')->where($where)->update($update_data);
            $detail_list = Db::table('mp_order_detail')->where('order_id','=',$exist['id'])->select();
            foreach ($detail_list as $v) {
                if($v['use_attr'] == 1) {
                    Db::table('mp_goods_attr')->where('id','=',$v['attr_id'])->setInc('stock',$v['num']);
                }
                Db::table('mp_goods')->where('id','=',$v['goods_id'])->setInc('stock',$v['num']);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();

    }
    //获取快递信息
    public function getKdTrace() {
        $data['order_id'] = input('post.order_id');
        checkPost($data);
        try {
            $whereOrder = [
                ['status','IN',[2,3]],
                ['id','=',$data['order_id']]
            ];
            $order_exist = Db::table('mp_order')->where($whereOrder)->find();
            if(!$order_exist) {
                return ajax('订单不存在或状态已改变',24);
            }
            $whereTracking = [
                ['name','=',$order_exist['tracking_name']]
            ];
            $tracking_exist = Db::table('mp_tracking')->where($whereTracking)->find();
            if(!$tracking_exist) {
                return ajax('物流不存在',-4);
            }
            $tracking_code = $tracking_exist['code'];
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $kuaidi = new Kuaidiniao();
        $result = $kuaidi->getOrderTracesByJson($tracking_code,$order_exist['tracking_num']);
        $result['tracking_name'] = $order_exist['tracking_name'];
        return ajax($result);
    }

    //订单评价
    public function orderEvaluate() {
        $val['order_detail_id'] = input('post.order_detail_id');
        $val['comment'] = input('post.comment');
        checkPost($val);
        $val['uid'] = $this->myinfo['id'];
        $val['create_time'] = time();
        try {
            $where_order_detail = [
                ['uid','=',$this->myinfo['id']],
                ['id','=',$val['order_detail_id']]
            ];
            $order_detail_exist = Db::table('mp_order_detail')->where($where_order_detail)->find();
            if(!$order_detail_exist) {
                return ajax('invalid order_detail_id',-4);
            }
            if($order_detail_exist['evaluate']) { return ajax('不可重复评价',25); }
            $where_order = [
                ['id','=',$order_detail_exist['order_id']]
            ];
            $order_exist = Db::table('mp_order')->where($where_order)->find();
            if(!$order_exist) {
                return ajax('invalid order_id',-4);
            }
            if($order_exist['status'] != 3) { return ajax('订单未完成,无法评价',26); }

            $val['order_id'] = $order_exist['id'];
            $val['goods_id'] = $order_detail_exist['goods_id'];
            Db::table('mp_goods_comment')->insert($val);
            Db::table('mp_order_detail')->where($where_order_detail)->update(['evaluate'=>1]);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }


    /*------ 商品订单结束 END------*/


    /*------收货地址管理 START------*/
    //我的地址列表
    public function addressList() {
        $uid = $this->myinfo['id'];
        try {
            $where = [
                ['uid','=',$uid]
            ];
            $list = Db::table('mp_address')->where($where)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }
    //添加收货地址
    public function addressAdd() {
        $val['uid'] = $this->myinfo['id'];
        $val['provincename'] = input('post.provincename');
        $val['cityname'] = input('post.cityname');
        $val['countyname'] = input('post.countyname');
        $val['detail'] = input('post.detail');
//        $val['postalcode'] = input('post.postalcode');
        $val['tel'] = input('post.tel');
        $val['username'] = input('post.username');
        $val['default'] = input('post.default',0);
        checkPost($val);
        if(!is_tel($val['tel'])) {
            return ajax('',6);
        }
        try {
            $id = Db::table('mp_address')->insertGetId($val);
            if($val['default']) {
                $whereDefault = [
                    ['id','<>',$id],
                    ['uid','=',$val['uid']]
                ];
                Db::table('mp_address')->where($whereDefault)->update(['default'=>0]);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //收货地址详情
    public function addressDetail() {
        $val['id'] = input('post.id');
        checkPost($val);
        $uid = $this->myinfo['id'];
        $where = [
            ['id','=',$val['id']],
            ['uid','=',$uid]
        ];
        try {
            $info = Db::table('mp_address')->where($where)->find();
            if(!$info) {
                return ajax('',-4);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }
    //修改收货地址
    public function addressMod() {
        $val['id'] = input('post.id');
        $uid = $this->myinfo['id'];
        $val['provincename'] = input('post.provincename');
        $val['cityname'] = input('post.cityname');
        $val['countyname'] = input('post.countyname');
        $val['detail'] = input('post.detail');
//        $val['postalcode'] = input('post.postalcode');
        $val['tel'] = input('post.tel');
        $val['username'] = input('post.username');
        $val['default'] = input('post.default',0);
        checkPost($val);
        if(!is_tel($val['tel'])) {
            return ajax('',6);
        }
        $where = [
            ['id','=',$val['id']],
            ['uid','=',$uid]
        ];
        try {
            $info = Db::table('mp_address')->where($where)->find();
            if(!$info) {
                return ajax('',-4);
            }
            Db::table('mp_address')->where($where)->update($val);
            if($val['default']) {
                $whereDefault = [
                    ['id','<>',$val['id']],
                    ['uid','=',$uid]
                ];
                Db::table('mp_address')->where($whereDefault)->update(['default'=>0]);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //删除收货地址
    public function addressDel() {
        $val['id'] = input('post.id');
        checkPost($val);
        try {
            $uid = $this->myinfo['id'];
            $where = [
                ['id','=',$val['id']],
                ['uid','=',$uid]
            ];
            $info = Db::table('mp_address')->where($where)->find();
            if(!$info) {
                return ajax('',-4);
            }
            Db::table('mp_address')->where($where)->delete();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //获取我的默认收货地址
    public function getDefaultAddress() {
        $uid = $this->myinfo['id'];
        $where = [
            ['default','=',1],
            ['uid','=',$uid]
        ];
        try {
            $info = Db::table('mp_address')->where($where)->find();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }
    //设置默认收货地址
    public function setDetaultAddress() {
        $val['id'] = input('post.id');
        $uid = $this->myinfo['id'];
        checkPost($val);
        $where = [
            ['id','=',$val['id']],
            ['uid','=',$uid]
        ];
        try {
            $info = Db::table('mp_address')->where($where)->find();
            if(!$info) {
                return ajax('invalid id',-4);
            }
            Db::table('mp_address')->where($where)->update(['default'=>1]);
            $whereDefault = [
                ['id','<>',$val['id']],
                ['uid','=',$uid]
            ];
            Db::table('mp_address')->where($whereDefault)->update(['default'=>0]);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    /*------收货地址管理 END------*/


    //获取我的套牌列表
    public function myComboDir() {
        $uid = $this->myinfo['id'];
        try {
            $whereDir = [
                ['uid','=',$uid]
            ];
            $list = Db::table('mp_combo_dir')->where($whereDir)->order(['id'=>'DESC'])->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }
    //获取套牌详情
    public function myComboDetail() {
        $val['dir_id'] = input('post.dir_id');
        checkPost($val);
        try {
            $whereDir = [
                ['id','=',$val['dir_id']]
            ];
            $info = Db::table('mp_combo_dir')->where($whereDir)->find();
            if(!$info) { return ajax('非法参数' . $val['dir_id'],-4); }
            $whereCard = [
                ['dir_id','=',$val['dir_id']]
            ];
            $list = Db::table('mp_card_combo')->alias('c')
                ->join('mp_card ca','c.card_id=ca.id','left')
                ->where($whereCard)
                ->field('c.*,ca.card_name,ca.cover,ca.type_id,ca.wushuang,ca.resource,ca.camp_id')
                ->select();
            $card_type = Db::table('mp_card_type')->select();
            $card_camp = Db::table('mp_card_camp')->select();
            $type = [];
            $camp = [];
            foreach ($card_type as $v) {$type[$v['id']] = $v['type_name'];}
            foreach ($card_camp as $v) {$camp[$v['id']] = $v['camp_name'];}

            foreach ($list as &$value) {
                $value['type'] = isset($type[$value['type_id']]) ? $type[$value['type_id']] : '未知';
                $value['camp'] = isset($camp[$value['camp_id']]) ? $camp[$value['camp_id']] : '未知';
                switch ($value['resource']) {
                    case -2:$value['resource'] = '资源-事件';break;
                    case -1:$value['resource'] = 'X';break;
                    default:;
                }
            }
            $info['list'] = $list;
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($info);
    }
    //创建套牌组合
    public function createCardCombo() {
        $dir_data['dir_name'] = input('post.dir_name');
        checkPost($dir_data);
        $combo = input('post.combo','');

        try {
            $combo = json_decode($combo,true);
            if(!is_array($combo) || empty($combo)) {
                return ajax('invalid combo',-4);
            }
            $time = time();
            $dir_data['uid'] = $this->myinfo['id'];
            $dir_data['total_num'] = 0;
            $dir_data['main_num'] = 0;
            $dir_data['spare_num'] = 0;
            $dir_data['create_time'] = $time;
            $dir_data['cover'] = '';
            $time_count = 0;
            foreach ($combo as $k=>$num) {//$k的格式 c_$card_id_$main
                $v = explode('_',$k);
                $card_id = $v[1];
                $main = $v[2];
                $whereCard = [
                    ['id','=',$card_id]
                ];
                $card_exist = Db::table('mp_card')->where($whereCard)->find();
                if(!$card_exist) { return ajax('非法参数card_id ' . $card_id,-4); }
                if($time_count === 0) { $dir_data['cover'] = $card_exist['cover']; }
                $dir_data['total_num'] += $num;
                if($main == 1) {
                    $dir_data['main_num'] += $num;
                }else {
                    $dir_data['spare_num'] += $num;
                }
                $time_count++;
            }

            Db::startTrans();
            $dir_id = Db::table('mp_combo_dir')->insertGetId($dir_data);
            $combo_data_all = [];
            foreach ($combo as $k=>$num) {
                $v = explode('_',$k);
                $card_id = $v[1];
                $main = $v[2];
                $combo_data['dir_id'] = $dir_id;
                $combo_data['uid'] = $dir_data['uid'];
                $combo_data['card_id'] = $card_id;
                $combo_data['main'] = $main;//1.主牌 2.副牌
                $combo_data['num'] = $num;
                $combo_data['combo_key'] = 'c_' . $card_id . '_' . $main;
                $combo_data['create_time'] = $time;
                $combo_data_all[] = $combo_data;
            }
            Db::table('mp_card_combo')->insertAll($combo_data_all);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(), -1);
        }
        return ajax($dir_id);
    }

    //修改套餐目录名
    public function dirnameModify() {
        $val['dir_name'] = input('post.dir_name');
        $val['dir_id'] = input('post.dir_id');
        checkPost($val);
        try {
            $whereDir = [
                ['id','=',$val['dir_id']],
                ['uid','=',$this->myinfo['id']]
            ];
            $dir_exist = Db::table('mp_combo_dir')->where($whereDir)->find();
            if(!$dir_exist) {
                return ajax('invalid dir_id',-4);
            }
            $update_data = [
                'dir_name'=>$val['dir_name']
            ];
            Db::table('mp_combo_dir')->where($whereDir)->update($update_data);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    //套牌修改
    public function cardComboModify() {

        $val['dir_id'] = input('post.dir_id');
        $val['dir_name'] = input('post.dir_name');
        checkPost($val);
        $combo = input('post.combo','');
        $uid = $this->myinfo['id'];
        try {
            $combo = json_decode($combo,true);
            if(!is_array($combo) || empty($combo)) {
                return ajax('invalid combo',-4);
            }

            $whereDir = [
                ['id','=',$val['dir_id']],
                ['uid','=',$uid]
            ];
            $dir_exist = Db::table('mp_combo_dir')->where($whereDir)->find();//判断目录是否存在
            if(!$dir_exist) {
                return ajax('invalid dir_id',-4);
            }

            $time = time();
            $dir_data['dir_name'] = $val['dir_name'];
            $dir_data['total_num'] = 0;
            $dir_data['main_num'] = 0;
            $dir_data['spare_num'] = 0;
            $dir_data['cover'] = '';
            $time_count = 0;
            foreach ($combo as $k=>$num) {//$k的格式 c_$card_id_$main
                $v = explode('_',$k);
                $card_id = $v[1];
                $main = $v[2];
                $whereCard = [
                    ['id','=',$card_id]
                ];
                $card_exist = Db::table('mp_card')->where($whereCard)->find();
                if(!$card_exist) { return ajax('非法参数card_id ' . $card_id,-4); }//判断每一张卡牌是否存在
                if($time_count === 0) { $dir_data['cover'] = $card_exist['cover']; }
                $dir_data['total_num'] += $num;
                if($main == 1) {
                    $dir_data['main_num'] += $num;
                }else {
                    $dir_data['spare_num'] += $num;
                }
                $time_count++;
            }
            $combo_data_all = [];
            foreach ($combo as $k=>$num) {
                $v = explode('_',$k);
                $card_id = $v[1];
                $main = $v[2];
                $combo_data['dir_id'] = $val['dir_id'];
                $combo_data['uid'] = $uid;
                $combo_data['card_id'] = $card_id;
                $combo_data['main'] = $main;//1.主牌 2.副牌
                $combo_data['num'] = $num;
                $combo_data['combo_key'] = 'c_' . $card_id . '_' . $main;
                $combo_data['create_time'] = $time;
                $combo_data_all[] = $combo_data;
            }
            Db::startTrans();
            $whereCombo = [
                ['dir_id','=',$val['dir_id']]
            ];
            Db::table('mp_card_combo')->where($whereCombo)->delete();//删除老的牌组
            Db::table('mp_card_combo')->insertAll($combo_data_all);//添加新的牌组
            Db::table('mp_combo_dir')->where($whereDir)->update($dir_data);//更新套牌目录信息
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(), -1);
        }
        return ajax();

    }

    public function cardComboCopy() {
        $val['dir_id'] = input('post.dir_id');
        $val['dir_name'] = input('post.dir_name');
        checkPost($val);
        $uid = $this->myinfo['id'];
        try {
            $whereDir = [
                ['id','=',$val['dir_id']],
                ['uid','=',$uid]
            ];
            $dir_exist = Db::table('mp_combo_dir')->where($whereDir)->find();//判断目录是否存在
            if(!$dir_exist) {
                return ajax('invalid dir_id',-4);
            }

            $time = time();
            $dir_data['uid'] = $uid;
            $dir_data['total_num'] = $dir_exist['total_num'];
            $dir_data['main_num'] = $dir_exist['main_num'];
            $dir_data['spare_num'] = $dir_exist['spare_num'];
            $dir_data['cover'] = $dir_exist['cover'];
            $dir_data['dir_name'] = $val['dir_name'];
            $dir_data['create_time'] = $time;

            $whereCombo = [
                ['dir_id','=',$val['dir_id']]
            ];
            $list = Db::table('mp_card_combo')->where($whereCombo)->select();

            Db::startTrans();
            $dir_id = Db::table('mp_combo_dir')->insertGetId($dir_data);
            $inser_data_all = [];
            foreach ($list as $v) {
                $insert_data['uid'] = $uid;
                $insert_data['dir_id'] = $dir_id;
                $insert_data['card_id'] = $v['card_id'];
                $insert_data['main'] = $v['main'];
                $insert_data['num'] = $v['num'];
                $insert_data['create_time'] = $time;
                $insert_data['combo_key'] = $v['combo_key'];
                $inser_data_all[] = $insert_data;
            }
            Db::table('mp_card_combo')->insertAll($inser_data_all);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(), -1);
        }
        return ajax($dir_id);
    }

    //套牌删除
    public function cardComboDel() {
        $val['dir_id'] = input('post.dir_id');
        checkPost($val);
        try {
            $whereDir = [
                ['id','=',$val['dir_id']],
                ['uid','=',$this->myinfo['id']]
            ];
            $dir_exist = Db::table('mp_combo_dir')->where($whereDir)->find();
            if(!$dir_exist) {
                return ajax('invalid dir_id',-4);
            }

            Db::startTrans();
            $whereCombo = [
                ['dir_id','=',$val['dir_id']]
            ];
            Db::table('mp_card_combo')->where($whereCombo)->delete();
            Db::table('mp_combo_dir')->where($whereDir)->delete();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    /*------ 裂变二维码 ------*/

    public function getShareQrcode() {
        $uid = $this->myinfo['id'];
//        $uid = 1;
        $app = Factory::miniProgram($this->mp_config);
        $response = $app->app_code->getUnlimit($uid, [
//            'page' => 'pages/index/index',
            'width' => '450'
        ]);
        $png = $uid . '.png';
        $save_path = 'shareqrcode/';
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->saveAs($save_path, $png);
        } else {
            return ajax($response, -1);
        }
        return ajax($save_path . $png);
    }

    //获取我邀请的人
    public function getInviteList() {
        $uid = input('post.uid','');
        try {
            if($uid) {
                $where = [
                    ['i.inviter_id','=',$uid]
                ];
            }else {
                $where = [
                    ['i.inviter_id','=',$this->myinfo['id']]
                ];
            }
            $list = Db::table('mp_invite')->alias('i')
                ->join("mp_user u", "i.to_uid=u.id", "left")
                ->where($where)
                ->field("i.create_time,i.to_uid AS uid,u.nickname,u.avatar,u.spend")
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $rule = "商品详情生成分享二维码，用户扫码进入才会被计算为一个有效好友。";
        $data['list'] = $list;
        $data['rule'] = $rule;
        return ajax($data);
    }






}