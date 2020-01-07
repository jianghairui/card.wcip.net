<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/4
 * Time: 10:50
 */
namespace app\api\controller;
use EasyWeChat\Factory;
use think\Db;

class Pay extends Base {

    //直接支付、待支付订单支付、联合订单号支付
    public function orderSnPay() {

        $pay_order_sn = input('post.pay_order_sn');
        if(!$pay_order_sn) {
            return ajax('请选择要支付的订单',-2);
        }
        $whereOrder = [
            ['pay_order_sn','=',$pay_order_sn],
            ['status','=',0],
            ['uid','=',$this->myinfo['id']]
        ];
        try {
            $order_exist = Db::table('mp_order')->where($whereOrder)->find();
            if(!$order_exist) { return ajax('invalid pay_order_sn',-4); }
//            $order_exist['pay_price'] = 0.1;
            $app = Factory::payment($this->mp_config);
            $result = $app->order->unify([
                'body' => '掌控的历史',
                'out_trade_no' => $pay_order_sn,
                'total_fee' => floatval($order_exist['pay_price'])*100,
                'notify_url' => $this->weburl . 'api/pay/order_notify',
                'trade_type' => 'JSAPI',
                'openid' => $this->myinfo['openid']
            ]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        if($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            return ajax($result['err_code_des'],-1);
        }
        try {
            $sign['appId'] = $result['appid'];
            $sign['timeStamp'] = strval(time());
            $sign['nonceStr'] = $result['nonce_str'];
            $sign['signType'] = 'MD5';
            $sign['package'] = 'prepay_id=' . $result['prepay_id'];
            $sign['paySign'] = getSign($sign);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($sign);
    }

    //订单支付回调接口
    public function order_notify() {
        //将返回的XML格式的参数转换成php数组格式
        $xml = file_get_contents('php://input');
        $data = xml2array($xml);
        $this->paylog($this->cmd,var_export($data,true));
        if($data) {
            if($data['return_code'] == 'SUCCESS' && $data['result_code'] == 'SUCCESS') {
                $whereorder = [
                    ['pay_order_sn','=',$data['out_trade_no']],
                    ['status','=',0]
                ];
                try {
                    $order_exist = Db::table('mp_order')->where($whereorder)->find();
                    if($order_exist) {
                        //修改订单状态
                        $update_data = [
                            'status' => 1,
                            'trans_id' => $data['transaction_id'],
                            'pay_time' => time()
                        ];
                        Db::table('mp_order')->where($whereorder)->update($update_data);

                        //todo 库存销量修改
                        $whereDetail = [
                            ['order_id','=',$order_exist['id']]
                        ];
                        $order_detail = Db::table('mp_order_detail')->where($whereDetail)->field('goods_id,num')->select();
                        foreach ($order_detail as $v) {
                            Db::table('mp_goods')->where('id','=',$v['goods_id'])->setInc('sales',$v['num']);
                        }
                        //增加个人消费金额
                        $whereUser = [
                            ['id','=',$order_exist['uid']]
                        ];
                        Db::table('mp_user')->where($whereUser)->setInc('spend',$order_exist['total_price']);
                    }
                }catch (\Exception $e) {
                    $this->excep($this->cmd,$e->getMessage());
                }
            }
        }
        exit(array2xml(['return_code'=>'SUCCESS','return_msg'=>'OK']));
    }



}