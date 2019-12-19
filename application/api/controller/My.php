<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/11
 * Time: 16:00
 */
namespace app\api\controller;
use my\Sendsms;
use my\Kuaidiniao;
use think\Db;
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
                return ajax('',3);
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
`o`.`id`,`o`.`pay_order_sn`,`o`.`pay_price`,`o`.`total_price`,`o`.`carriage`,`o`.`create_time`,`o`.`refund_apply`,`o`.`status`,`o`.`refund_apply`,`d`.`order_id`,`d`.`goods_id`,`d`.`num`,`d`.`unit_price`,`d`.`goods_name`,`d`.`attr`,`g`.`pics` 
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
        if(!in_array($type,[1,2,3])) {
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
                ->field("o.id,o.pay_order_sn,o.pay_price,o.total_price,o.carriage,o.receiver,o.tel,o.address,o.create_time,o.refund_apply,o.status,d.order_id,d.num,d.unit_price,d.goods_name,d.attr,g.pics")->select();
            if(!$list) {
                return ajax('invalid order_id',4);
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
                $data_child['cover'] = unserialize($li['pics'])[0];
                $data_child['goods_name'] = $li['goods_name'];
                $data_child['num'] = $li['num'];
                $data_child['unit_price'] = $li['unit_price'];
                $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                $data_child['attr'] = $li['attr'];
                $data_child['cover'] = unserialize($li['pics'])[0];
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
                return ajax( 'invalid order_id',4);
            }
            $update_data = [
                'refund_apply' => 1,
                'reason' => $val['reason']
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
                return ajax( 'invalid order_id',44);
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
                return ajax( 'invalid order_id',44);
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
        $val['postalcode'] = input('post.postalcode');
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
        $val['postalcode'] = input('post.postalcode');
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
    /*------收货地址管理 END------*/



    //获取快递信息
    public function getKdTrace() {
        $data['order_id'] = input('post.order_id');
        checkPost($data);
        try {
            $whereOrder = [
                ['status','=',2],
                ['id','=',$data['order_id']]
            ];
            $order_exist = Db::table('mp_order')->where($whereOrder)->find();
            if(!$order_exist) {
                return ajax('订单不存在或状态已改变',4);
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









}