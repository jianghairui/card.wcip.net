<?php
/**
 * Created by PhpStorm.
 * User=>JHR
 * Date=>2019/1/30
 * Time=>14:15
 */
namespace  app\admin\controller;
use think\Db;
use wx\Jssdk;
class Api extends Base {

    public function index() {
        //验证第三方服务器
        $param = input('param.');
        if(isset($param['echostr'])){
            $this->weixinlog($this->cmd,var_export($param,true));
//            $this->xmllog($this->cmd,var_export($param,true));
            $token = $this->token;
            $tmpArr = array($token,$param["timestamp"], $param["nonce"]);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode('',$tmpArr);
            $tmpStr = sha1( $tmpStr );
            if($tmpStr === $param["signature"]) {
                //第一次接入微信API
                exit($param['echostr']);
            }
        }else{
            $xml = file_get_contents('php://input');
            $data = xml2array($xml);
            switch ($data['MsgType']) {
                case 'event':
                    //匹配各种事件
                    switch ($data['Event']) {
                        //扫码事件
                        case 'SCAN':
                            $this->weixinlog($this->cmd . '.SCAN',var_export($data,true));
                            $scene_id = $data['EventKey'];
                            break;
                        //关注事件
                        case 'subscribe':
                            $this->weixinlog($this->cmd . '.subscribe',var_export($data,true));

                            //带参数关注
                            if($data['EventKey']) {
                                try {
                                    $scene_id = explode('_',$data['EventKey'])[1];
                                    $scene_exist = Db::table('mp_scene')->where('id','=',$scene_id)->find();
                                    if(!$scene_exist) {
                                        $this->weixinlog($this->cmd . '.subscribe','scene_id not exist' . $scene_id);
                                        exit('success');
                                    }
                                    //根据OPENID获取用户信息
                                    $jssdk = new Jssdk($this->config['appid'], $this->config['app_secret']);
                                    $access_token = $jssdk->getAccessToken();
                                    $openid = $data['FromUserName'];
                                    $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
                                    $result = curl_get_data($url);
                                    $obj = json_decode($result,true);
                                    $insert_data = [
                                        'openid' => $obj['openid'],
                                        'nickname' => $obj['nickname'],
                                        'sex' => $obj['sex'],
                                        'avatar' => $obj['headimgurl'],
                                        'sub_time' => time(),
                                        'subscribe' => 1
                                    ];
                                    $whereUser = [['openid','=',$obj['openid']]];
                                    $user_exist = Db::table('mp_scene_user')->where($whereUser)->find();
                                    if($user_exist) {
                                        if($user_exist['subscribe'] == 0) {
                                            $scene_id = $user_exist['scene_id'];
                                            Db::table('mp_scene_user')->where($whereUser)->update($insert_data);
                                            Db::table('mp_scene')->where('id','=',$scene_id)->setInc('subscribe',1);
                                            Db::table('mp_scene')->where('id','=',$scene_id)->setDec('unsubscribe',1);
                                        }
                                    }else {
                                        $insert_data['scene_id'] = $scene_id;
                                        $insert_data['create_time'] = time();
                                        Db::table('mp_scene_user')->insert($insert_data);
                                        Db::table('mp_scene')->where('id','=',$scene_id)->setInc('total_num',1);
                                        Db::table('mp_scene')->where('id','=',$scene_id)->setInc('subscribe',1);
                                    }
                                } catch (\Exception $e) {
                                    $this->excep($this->cmd . '.subscribe',$e->getMessage());
                                    exit('success');
                                }
                            }else {//不带参数的关注
                                $whereUser = [
                                    ['openid','=',$data['FromUserName']],
                                    ['subscribe','=',0]
                                ];
                                try {
                                    $user_exist = Db::table('mp_scene_user')->where($whereUser)->find();
                                    if($user_exist) {
                                        //根据OPENID获取用户信息
                                        $jssdk = new Jssdk($this->config['appid'], $this->config['app_secret']);
                                        $access_token = $jssdk->getAccessToken();
                                        $openid = $data['FromUserName'];
                                        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
                                        $result = curl_get_data($url);
                                        $obj = json_decode($result,true);
                                        $update_data = [
                                            'openid' => $obj['openid'],
                                            'nickname' => $obj['nickname'],
                                            'sex' => $obj['sex'],
                                            'avatar' => $obj['headimgurl'],
                                            'sub_time' => time(),
                                            'subscribe' => 1
                                        ];
                                        //修改用户关注状态,场景关注值+1 取关值-1
                                        Db::table('mp_scene_user')->where($whereUser)->update($update_data);
                                        Db::table('mp_scene')->where('id','=',$user_exist['scene_id'])->setInc('subscribe',1);
                                        Db::table('mp_scene')->where('id','=',$user_exist['scene_id'])->setDec('unsubscribe',1);
                                    }
                                } catch (\Exception $e) {
                                    $this->excep($this->cmd . '.subscribe',$e->getMessage());
                                    return ajax($e->getMessage(), -1);
                                }

                            }
                            break;
                        //取关事件
                        case 'unsubscribe':
                            //TODO
                            $this->weixinlog($this->cmd . '.unsubscribe',var_export($data,true));
                            try {
                                $whereUser = [
                                    ['openid','=',$data['FromUserName']],
                                    ['subscribe','=',1]
                                ];
                                $user_exist = Db::table('mp_scene_user')->where($whereUser)->find();
                                if($user_exist) {
                                    //修改用户关注状态,场景关注值-1 取关值+1
                                    Db::table('mp_scene_user')->where($whereUser)->update(['subscribe'=>0,'unsub_time'=>time()]);
                                    Db::table('mp_scene')->where('id','=',$user_exist['scene_id'])->setInc('unsubscribe',1);
                                    Db::table('mp_scene')->where('id','=',$user_exist['scene_id'])->setDec('subscribe',1);
                                }
                            } catch (\Exception $e) {
                                $this->excep($this->cmd,$e->getMessage());
                                exit('success');
                            }
                            break;


                    };break;


                default:;
            }
            exit('success');
        }

    }





//7 查询授权完成状态
    public function cardinfo() {
        $jssdk = new Jssdk($this->config['appid'], $this->config['app_secret']);
        $access_token = $jssdk->getAccessToken();
        $s_pappid = 'd3gxZGM2NGFjYzliZDllYjA5X_i4jn6NAgX0Dl14GNauU5bkCCn9w7W5w7xmCvvUMvV1';

        $url = 'https://api.weixin.qq.com/card/invoice/getauthdata?access_token=' . $access_token;
        $data = [
            "s_pappid" => $s_pappid,
            "order_id" => "102",
        ];
        $result = curl_post_data($url,json_encode($data));
        $obj = json_decode($result);
        halt($obj);
    }
//8 拒绝开票
    public function rejectInvoice() {
        $jssdk = new Jssdk($this->config['appid'], $this->config['app_secret']);
        $access_token = $jssdk->getAccessToken();
        $s_pappid = 'd3gxZGM2NGFjYzliZDllYjA5X_i4jn6NAgX0Dl14GNauU5bkCCn9w7W5w7xmCvvUMvV1';

        $url = 'https://api.weixin.qq.com/card/invoice/rejectinsert?access_token=' . $access_token;
        $data = [
            "s_pappid" => $s_pappid,
            "order_id" => "102",
            "reason" => 'invalid order_sn',
            "url"   => 'http://www.baidu.com'
        ];
        $result = curl_post_data($url,json_encode($data));
        $obj = json_decode($result);
        halt($obj);
    }
//11 关联商户号与开票平台
    public function set_pay_mch() {
        $jssdk = new Jssdk($this->config['appid'], $this->config['app_secret']);
        $access_token = $jssdk->getAccessToken();
        $s_pappid = 'd3gxZGM2NGFjYzliZDllYjA5X_i4jn6NAgX0Dl14GNauU5bkCCn9w7W5w7xmCvvUMvV1';

        $url = 'https://api.weixin.qq.com/card/invoice/setbizattr?action=set_pay_mch&access_token=' . $access_token;
        $data['paymch_info'] = [
            "s_pappid" => $s_pappid,
            "mchid" => $this->config['mch_id'],
        ];
        $result = curl_post_data($url,json_encode($data));
        $obj = json_decode($result);
        halt($obj);
    }
//12 查询商户号与开票平台关联情况
    public function get_pay_mch() {
        $jssdk = new Jssdk($this->config['appid'], $this->config['app_secret']);
        $access_token = $jssdk->getAccessToken();
        $url = 'https://api.weixin.qq.com/card/invoice/setbizattr?action=get_pay_mch&access_token=' . $access_token;
        $result = curl_post_data($url,json_encode([]));
        $obj = json_decode($result);
        halt($obj);
    }

    public function makeoutinvoice() {
        $data['invoiceinfo'] = [
            "wxopenid"=>"oNEu_s8TWzpK6p6-kUFnFHaS1GiI",
            "ddh" =>"155032941148455400978",
            "fpqqlsh"=>"155032941148455400978",
            "nsrsbh"=>"91120116MA05MAH15R",
            "nsrmc"=>"山海文化有限公司",
            "nsrdz"=>"天津滨海高新区华科三路华鼎智地2号楼613室",
            "nsrdh"=>"022-87797523",
            "nsrbank"=>"天津银行融源支行",
            "nsrbankid"=>"106601201090299967",
            "ghfmc"=>"天津文博艺术品销售有限公司",
            "kpr"=>"姜海蕤",
            "jshj"=> "1.17",
            "hjje"=> "1",
            "hjse"=> "0.17",
            "hylx"=>"0",
            "invoicedetail_list"=>[
                "fphxz"=>"0",
                "spbm"=>"1060500000000000000",
                "xmmc"=>"文创产品",
                "xmsl"=>"1",
                "xmdj"=>"1",
                "xmje"=>"1",
                "sl"=>"0.17",
                "se"=>"0.17"
            ],
        ];
        $jssdk = new Jssdk($this->config['appid'], $this->config['app_secret']);
        $access_token = $jssdk->getAccessToken();
        $url = 'https://api.weixin.qq.com/card/invoice/makeoutinvoice?access_token=' . $access_token;
        $result = curl_post_data($url,json_encode($data));
        $obj = json_decode($result);
        halt($obj);
    }

}