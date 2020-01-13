<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/3/20
 * Time: 15:45
 */
namespace app\api\controller;
use think\Db;

class Shop extends Base {

    //商品分类
    public function cateList() {
        try {
            $map = [
                ['del','=',0],
                ['status','=',1],
                ['pid','=',0]
            ];
            $list = Db::table('mp_goods_cate')->where($map)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }
    //商品版本
    public function versionList() {
        try {
            $map = [];
            $list = Db::table('mp_goods_version')->where($map)->select();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($list);
    }
    //商品列表
    public function goodsList() {
        $cate_id = input('post.cate_id',0);
        $version_id = input('post.version_id',0);
        $curr_page = input('post.page',1);
        $perpage = input('post.perpage',10);
        $search = input('post.search','');

        $where = [
            ['g.status','=',1],
            ['g.del','=',0]
        ];
        $order = ['g.id'=>'DESC'];
        if($cate_id) {
            $where[] = ['g.cate_id','=',$cate_id];
        }
        if($version_id) {
            $where[] = ['g.version_id','=',$version_id];
        }
        if($search) {
            $where[] = ['g.name','like',"%{$search}%"];
        }
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
    //商品详情
    public function goodsDetail() {
        $val['id'] = input('post.id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['id']]
            ];
            $info = Db::table('mp_goods')
                ->where($where)
                ->field("id,name,detail,origin_price,price,pics,carriage,stock,sales,use_attr,attr,hot,limit")
                ->find();
            if(!$info) {
                return ajax($val['id'],-4);
            }
            if($info['use_attr']) {
                $whereAttr = [
                    ['goods_id','=',$val['id']],
                    ['del','=',0]
                ];
                $attr_list = Db::table('mp_goods_attr')->where($whereAttr)->select();
            }else {
                $attr_list = [];
            }
            $info['attr_list'] = $attr_list;
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $info['pics'] = unserialize($info['pics']);
        return ajax($info);
    }
    //商品评论
    public function goodsCommentList() {
        $val['goods_id'] = input('post.goods_id');
        checkPost($val);
        try {
            $whereGoods = [
                ['id','=',$val['goods_id']]
            ];
            $goods_exist = Db::table('mp_goods')->where($whereGoods)->find();
            if(!$goods_exist) {
                return ajax('invalid goods_id',-4);
            }
            $curr_page = input('post.page',1);
            $perpage = input('post.perpage',10);
            $curr_page = $curr_page ? $curr_page : 1;
            $perpage = $perpage ? $perpage : 10;
            $whereComment = [
                ['c.goods_id','=',$val['goods_id']],
                ['c.status','=',1]
            ];
            $list = Db::table('mp_goods_comment')->alias('c')
                ->join('mp_user u','c.uid=u.id','left')
                ->join('mp_order_detail d','c.order_detail_id=d.id','left')
                ->where($whereComment)
                ->field('c.comment,c.create_time,u.nickname,u.avatar,d.attr')
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->order(['c.id'=>'DESC'])
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax($list);
    }

    //购物车列表
    public function cartList() {
        try {
            $where = [
                ['uid','=',$this->myinfo['id']]
            ];
            $list = Db::table('mp_cart')->alias('c')
                ->join("mp_goods g","c.goods_id=g.id","left")
                ->join("mp_goods_attr a","c.attr_id=a.id","left")
                ->field("c.id,c.uid,c.goods_id,c.num,c.use_attr,c.attr,c.attr_id,g.name,g.pics,g.price,g.carriage,g.stock,g.limit")
                ->where($where)->select();
            foreach ($list as &$v) {
                $v['cover'] = unserialize($v['pics'])[0];
                unset($v['pics']);
                if($v['use_attr']) {
                    $map_attr = [
                        ['id','=',$v['attr_id']],
                        ['goods_id','=',$v['goods_id']]
                    ];
                    $attr_exist = Db::table('mp_goods_attr')->where($map_attr)->find();

                    $price = $attr_exist['price'];
                }else {
                    $price = $v['price'];
                }
                $v['price'] = $price;
                $v['total_price'] = $price * $v['num'];
                $v['total_price'] = sprintf ( "%1\$.2f",$v['total_price']);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        return ajax($list);
    }

    //加入购物车
    public function cartAdd() {
        $val['goods_id'] = input('post.goods_id');
        $val['num'] = input('post.num');
        checkPost($val);
        $val['attr_id'] = input('post.attr_id',0);
        $val['use_attr'] = 0;
        $val['uid'] = $this->myinfo['id'];
        if(!if_int($val['num'])) {
            return ajax('num:' . $val['num'],-4);
        }
        try {
            //判断当前购物车内商品件数
            $whereCartCount = [
                ['uid','=',$this->myinfo['id']]
            ];
            $count = Db::table('mp_cart')->where($whereCartCount)->count();
            if($count >= 10) { return ajax('购物车已经满啦',14); }
            //判断商品是否存在
            $whereGoods = [
                ['id','=',$val['goods_id']]
            ];
            $goods_exist = Db::table('mp_goods')->where($whereGoods)->find();
            if(!$goods_exist) { return ajax('goods_id:' . $val['goods_id'],-4); }
            $whereCart = [
                ['goods_id','=',$val['goods_id']],
                ['uid','=',$this->myinfo['id']]
            ];
            if($val['num'] > $goods_exist['limit']) {
                return ajax('超出单笔限购数量',16);
            }
            if($val['attr_id']) {
                $val['use_attr'] = 1;
                $map_attr = [
                    ['id','=',$val['attr_id']],
                    ['goods_id','=',$val['goods_id']]
                ];
                $attr_exist = Db::table('mp_goods_attr')->where($map_attr)->find();
                if(!$attr_exist) {
                    return ajax('attr_id:' . $val['attr_id'],-4);
                }
                if($val['num'] > $attr_exist['stock']) {
                    return ajax('库存不足',15);
                }

                $val['attr'] = $attr_exist['value'];
                $whereCart[] = ['attr_id','=',$val['attr_id']];
                $cart_exist = Db::table('mp_cart')->where($whereCart)->find();//购物车是否已经存在此商品
                if($cart_exist) {
                    if(($val['num'] + $cart_exist['num']) > $attr_exist['stock']) {
                        return ajax('商品+购件数(含购物车)超出库存',17);
                    }
                    if(($val['num'] + $cart_exist['num']) > $goods_exist['limit']) {
                        return ajax('超出单笔限购数量',16);
                    }
                    Db::table('mp_cart')->where($whereCart)->setInc('num',$val['num']);
                }else {
                    $val['create_time'] = time();
                    Db::table('mp_cart')->insert($val);
                }
            }else {
                if($val['num'] > $goods_exist['stock']) {
                    return ajax('库存不足',15);
                }
                $cart_exist = Db::table('mp_cart')->where($whereCart)->find();//购物车是否已经存在此商品
                if($cart_exist) {
                    if(($val['num'] + $cart_exist['num']) > $goods_exist['stock']) {
                        return ajax('商品+购件数(含购物车)超出库存',17);
                    }
                    if(($val['num'] + $cart_exist['num']) > $goods_exist['limit']) {
                        return ajax('超出单笔限购数量',16);
                    }
                    Db::table('mp_cart')->where($whereCart)->setInc('num',$val['num']);
                }else {
                    $val['create_time'] = time();
                    Db::table('mp_cart')->insert($val);
                }
            }

        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    //购物车+++
    public function cartInc() {
        $val['cart_id'] = input('post.cart_id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['cart_id']],
                ['uid','=',$this->myinfo['id']]
            ];
            $cart_exist = Db::table('mp_cart')->where($where)->find();
            if(!$cart_exist) {
                return ajax($val['cart_id'],-4);
            }
            $cart_exist['num'] += 1;
            $where_goods = [
                ['id','=',$cart_exist['goods_id']]
            ];
            $goods_exist = Db::table('mp_goods')->where($where_goods)->find();
            if(($cart_exist['num']) > $goods_exist['limit']) {
                return ajax('超出单笔限购数量',16);
            }
            if($cart_exist['use_attr']) {
                $map_attr = [
                    ['id','=',$cart_exist['attr_id']],
                    ['goods_id','=',$cart_exist['goods_id']]
                ];
                $attr_exist = Db::table('mp_goods_attr')->where($map_attr)->find();
                if($cart_exist['num'] > $attr_exist['stock']) {
                    return ajax('此规格库存不足',30);
                }
                $price = $attr_exist['price'];
            }else {
                if($cart_exist['num'] > $goods_exist['stock']) {
                    return ajax('库存不足',15);
                }
                $price = $goods_exist['price'];
            }
            Db::table('mp_cart')->where($where)->setInc('num',1);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $data['price'] = $price;
        $data['num'] = $cart_exist['num'];
        $data['total_price'] = $price * $cart_exist['num'];
        $data['total_price'] = sprintf ( "%1\$.2f",$data['total_price']);
        return ajax($data);
    }
    //购物车---
    public function cartDec() {
        $val['cart_id'] = input('post.cart_id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['cart_id']],
                ['uid','=',$this->myinfo['id']]
            ];
            $cart_exist = Db::table('mp_cart')->where($where)->find();
            if(!$cart_exist) {
                return ajax($val['cart_id'],-4);
            }
            $cart_exist['num'] -= 1;
            if($cart_exist['num'] <= 0) {
                return ajax('不能再减了',18);
            }
            $where_goods = [
                ['id','=',$cart_exist['goods_id']]
            ];
            $goods_exist = Db::table('mp_goods')->where($where_goods)->find();
            if($cart_exist['use_attr']) {
                $map_attr = [
                    ['id','=',$cart_exist['attr_id']],
                    ['goods_id','=',$cart_exist['goods_id']]
                ];
                $attr_exist = Db::table('mp_goods_attr')->where($map_attr)->find();
                $price = $attr_exist['price'];
            }else {
                $price = $goods_exist['price'];
            }
            Db::table('mp_cart')->where($where)->setDec('num',1);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $data['price'] = $price;
        $data['num'] = $cart_exist['num'];
        $data['total_price'] = $price * $cart_exist['num'];
        $data['total_price'] = sprintf ( "%1\$.2f",$data['total_price']);
        return ajax($data);
    }
    //删除购物车
    public function cartDel() {
        $val['cart_id'] = input('post.cart_id');
        checkPost($val);
        try {
            $where = [
                ['id','=',$val['cart_id']],
                ['uid','=',$this->myinfo['id']]
            ];
            $cart_exist = Db::table('mp_cart')->where($where)->find();
            if(!$cart_exist) {
                return ajax($val['cart_id'],-4);
            }
            Db::table('mp_cart')->where($where)->delete();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //购买下单
    public function purchase() {
        $data['goods_id'] = input('post.goods_id');
        $data['num'] = input('post.num');
        $data['receiver'] = input('post.receiver');
        $data['tel'] = input('post.tel');
        $data['address'] = input('post.address');
        $data['attr_id'] = input('post.attr_id',0);
        checkPost($data);
        if(!if_int($data['num'])) {
            return ajax($data['num'],-4);
        }
        try {
            $time = time();
            $pay_order_sn = create_unique_number('');
            $whereGoods = [
                ['id', '=',$data['goods_id']]
            ];
            $goods_exist = Db::table('mp_goods')->where($whereGoods)->find();
            if (!$goods_exist) { return ajax('invalid goods_id', -4); }
            if($data['num'] > $goods_exist['stock']) { return ajax('库存不足',15); }
            if($data['num'] > $goods_exist['limit']) { return ajax('超出单笔限购数量',16); }

            if($data['attr_id']) {
                $where_attr = [
                    ['id','=',$data['attr_id']],
                    ['goods_id','=',$data['goods_id']],
                ];
                $attr_exist = Db::table('mp_goods_attr')->where($where_attr)->find();
                if(!$attr_exist) {
                    return ajax('invalid attr_id',-4);
                }
                if($data['num'] > $attr_exist['stock']) {
                    return ajax('库存不足',39);
                }
                $unit_price = $attr_exist['price'];
                $order_detail['use_attr'] = 1;
                $order_detail['attr_id'] = $data['attr_id'];
                $order_detail['attr'] = $attr_exist['value'];
            }else {
                $unit_price = $goods_exist['price'];
                $order_detail['use_attr'] = 0;
                $order_detail['attr_id'] = 0;
                $order_detail['attr'] = '默认';
            }

            $total_carriage = $goods_exist['carriage'];
            if($data['num'] > 1) {
                $total_carriage = 0;
            }

            $total_price = $unit_price * $data['num'] + $total_carriage;
            $insert_data = [
                'uid' => $this->myinfo['id'],
                'pay_order_sn' => $pay_order_sn,
                'total_price' => $total_price,
                'pay_price' => $total_price,
                'carriage' => $goods_exist['carriage'] * $data['num'],
                'receiver' => $data['receiver'],
                'tel' => $data['tel'],
                'address' => $data['address'],
                'create_time' => $time,
            ];

            Db::startTrans();
            $order_id = Db::table('mp_order')->insertGetId($insert_data);//创建支付订单

            $order_detail['uid'] = $this->myinfo['id'];
            $order_detail['order_id'] = $order_id;
            $order_detail['goods_id'] = $goods_exist['id'];
            $order_detail['goods_name'] = $goods_exist['name'];
            $order_detail['num'] = $data['num'];
            $order_detail['unit_price'] = $unit_price;
            $order_detail['total_price'] = $unit_price * $data['num'] + $goods_exist['carriage'];
            $order_detail['carriage'] = $goods_exist['carriage'] * $data['num'];
            $order_detail['create_time'] = $time;


            Db::table('mp_order_detail')->insert($order_detail);//创建订单详情
            Db::table('mp_goods')->where('id', $data['goods_id'])->setDec('stock',$data['num']);
            if($data['attr_id']) {
                Db::table('mp_goods_attr')->where($where_attr)->setDec('stock',$data['num']);
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(), -1);
        }
        return ajax($pay_order_sn);
    }

    //购物车去支付
    public function cartToPurchase() {
        $cart_ids = input('post.cart_ids',[]);
        $val['receiver'] = input('post.receiver');
        $val['tel'] = input('post.tel');
        $val['address'] = input('post.address');
        checkPost($val);
        if(!is_array($cart_ids) || empty($cart_ids)) {  return ajax('请选择要结算的商品',19);}
        if(array_unique($cart_ids) !== $cart_ids) { return ajax($cart_ids,-4);}
        try {
            $time = time();
            $whereCart = [
                ['c.id','IN',$cart_ids],
                ['c.uid','=',$this->myinfo['id']]
            ];
            $cart_list = Db::table('mp_cart')->alias('c')
                ->join("mp_goods g","c.goods_id=g.id","left")
                ->where($whereCart)
                ->field("c.*,g.price,g.name,g.carriage,g.weight,g.stock AS total_stock")
                ->select();

            if(count($cart_ids) != count($cart_list)) { return ajax($cart_ids,-4);}

            $pay_order_sn = create_unique_number('');

            $card_delete_ids = [];//库存不足的商品,从购物车中删除
            $total_order_price = 0;//订单总金额
            $insert_detail_all = [];//商品详情表数据

            foreach ($cart_list as $v) {
                //商品带规格
                if($v['use_attr']) {
                    $where_attr = [
                        ['id','=',$v['attr_id']],
                        ['goods_id','=',$v['goods_id']],
                    ];
                    $attr_exist = Db::table('mp_goods_attr')->where($where_attr)->find();
                    if(!$attr_exist) {  return ajax('invalid attr_id',-4);}
                    if($v['num'] > $attr_exist['stock']) {$card_delete_ids[] = $v['id'];}

                    $unit_price = $attr_exist['price'];
                    $insert_detail['use_attr'] = 1;
                    $insert_detail['attr_id'] = $v['attr_id'];
                    $insert_detail['attr'] = $attr_exist['value'];
                }else {
                    //商品不带规格
                    if($v['num'] > $v['total_stock']) {$card_delete_ids[] = $v['id'];}
                    $unit_price = $v['price'];
                    $insert_detail['use_attr'] = 0;
                    $insert_detail['attr_id'] = 0;
                    $insert_detail['attr'] = '默认';
                }

                $carriage = $v['carriage'];

                if($v['num'] > 1) {
                    $carriage = 0;
                }

                $total_order_price += $unit_price * $v['num'];

                $insert_detail['uid'] = $this->myinfo['id'];
                $insert_detail['goods_id'] = $v['goods_id'];
                $insert_detail['goods_name'] = $v['name'];
                $insert_detail['num'] = $v['num'];
                $insert_detail['unit_price'] = $unit_price;
                $insert_detail['total_price'] = $unit_price * $v['num'] + $carriage;
                $insert_detail['carriage'] = $v['carriage'] * $v['num'];
                $insert_detail['create_time'] = $time;
                $insert_detail_all[] = $insert_detail;
            }

            $total_carriage = $carriage;
            if(count($cart_ids) > 1) {
                $total_carriage = 0;
            }

            $order_data = [
                'uid' => $this->myinfo['id'],
                'pay_order_sn' => $pay_order_sn,
                'total_price' => $total_order_price + $total_carriage,
                'pay_price' => $total_order_price + $total_carriage,
                'carriage' => $total_carriage,
                'receiver' => $val['receiver'],
                'tel' => $val['tel'],
                'address' => $val['address'],
                'create_time' => $time,
            ];

            //有库存不足的商品清除购物车库存不足部分
            if(!empty($card_delete_ids)) {
                $whereDelete = [
                    ['id','in',$card_delete_ids],
                    ['uid','=',$this->myinfo['id']]
                ];
                Db::table('mp_cart')->where($whereDelete)->delete();
                return ajax('部分商品库存不足,请重新结算',20);
            }

            Db::startTrans();

            $order_id = Db::table('mp_order')->insertGetId($order_data);
            foreach ($insert_detail_all as $k=>&$v) {
                $v['order_id'] = $order_id;
            }
            Db::table('mp_order_detail')->insertAll($insert_detail_all);
            foreach ($cart_list as $v) {
                Db::table('mp_goods')->where('id','=',$v['goods_id'])->setDec('stock',$v['num']);
                if($v['use_attr']) {
                    $where_attr = [
                        ['id','=',$v['attr_id']],
                        ['goods_id','=',$v['goods_id']],
                    ];
                    Db::table('mp_goods_attr')->where($where_attr)->setDec('stock',$v['num']);
                }
            }
            //清除购物车已结算商品
            $whereDelete = [
                ['id','in',$cart_ids],
                ['uid','=',$this->myinfo['id']]
            ];
            Db::table('mp_cart')->where($whereDelete)->delete();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return ajax($e->getMessage(), -1);
        }
        return ajax($pay_order_sn);

    }





}