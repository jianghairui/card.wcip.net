<?php
namespace app\api\controller;

use think\Db;
class Plan extends Base {


    public function index() {
        halt($_SERVER);
    }

    //取消订单
    public function orderCancel() {
        if($_SERVER['REMOTE_ADDR'] === '47.105.113.239') {
            try {
                $whereOrder = [
                    ['status','=',0],
                    ['del','=',0],
                    ['create_time','<',time()-3600]
                ];
                $order_ids = Db::table('mp_order')->where($whereOrder)->column('id');

                if(!empty($order_ids)) {
                    $detail_list = Db::table('mp_order_detail')->where('order_id','in',$order_ids)->field('id,goods_id,use_attr,attr_id,num')->select();

                    foreach ($detail_list as $vv) {
                        if($vv['use_attr'] == 1) {
                            Db::table('mp_goods_attr')->where('id','=',$vv['attr_id'])->setInc('stock',$vv['num']);
                        }
                        Db::table('mp_goods')->where('id','=',$vv['goods_id'])->setInc('stock',$vv['num']);
                    }
                    $res = Db::table('mp_order')->where('id','in',$order_ids)->update(['del'=>1]);

                    $this->runlog($this->cmd,'订单ID:[ ' . implode($order_ids,',') . ' ] 共 ' . $res . '条数据更新',LOG_PATH . '/plan.log');
                }else {
                    $res = 0;
                    $this->runlog($this->cmd,$res . '条数据更新',LOG_PATH . '/plan.log');
                }

            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            exit($res . '条数据更新');
        }else {
            exit('无权访问');
        }
    }

}