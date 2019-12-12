<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/4/18
 * Time: 18:17
 */
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\exception\HttpResponseException;
use EasyWeChat\Factory;
class Base extends Controller {

    protected $cmd;
    protected $controller;
    protected $rename_base_path;
    protected $mp_config;
    protected $domain;
    protected $weburl;
    protected $myinfo;

    public function initialize() {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->cmd = request()->controller() . '/' . request()->action();
        $this->controller = request()->controller();
        $this->rename_base_path = 'upload/api/';
        $this->domain = config('domain');
        $this->weburl = config('weburl');
        $this->mp_config = [
            'app_id' => config('appid'),
            'secret' => config('app_secret'),
            'mch_id'             => config('mch_id'),
            'key'                => config('mch_key'),   // API 密钥
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          =>  config('cert_path'),
            'key_path'           =>  config('key_path'),
            // 下面为可选项,指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => APP_PATH . '/wechat.log',
            ],
        ];
        $this->checkSession();
    }

    private function checkSession() {
        $noneed = [
            'Test',
            'Login/login',
            'Pay/order_notify',
        ];
        if (in_array($this->controller,$noneed) || in_array($this->cmd, $noneed)) {
            return true;
        }else {
            $token = input('post.token');
            if(!$token) {
                throw new HttpResponseException(ajax('token is empty',-6));
            }
            try {
                $exist = Db::table('mp_user')->where([
                    ['token','=',$token]
                ])->find();
            }catch (\Exception $e) {
                throw new HttpResponseException(ajax($e->getMessage(),-1));
            }
            if($exist) {
                if(($exist['last_login_time'] + 3600*24*7) < time()) {
                    throw new HttpResponseException(ajax('invalid token',-3));
                }
                $this->myinfo = $exist;
                return true;
            }else {
                throw new HttpResponseException(ajax('invalid token',-3));
            }
        }

    }


    //微信日志
    protected function weixinlog($cmd = '',$msg = '') {
        $file= LOG_PATH . '/wechat.log';
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:".$cmd."\n".$msg."\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

    //xml数据日志
    protected function xmllog($cmd = '',$msg = '') {
        $file= LOG_PATH . '/xml.log';
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:".$cmd."\n".$msg."\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }

    //Exception日志
    protected function excep($cmd,$str) {
        $file= LOG_PATH . '/exception.log';
        create_dir($file);
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }else{
            echo '创建失败';
        }
    }




    //小程序验证文本内容是否违规
    protected function msgSecCheck($msg) {
        $content = $msg;
        $app = Factory::payment($this->mp_config);
        $access_token = $app->access_token;
        $token = $access_token->getToken();
        $url = 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token=' . $token['access_token'];
        $res = curl_post_data($url, '{ "content":"'.$content.'" }');

        $result = json_decode($res,true);
        try {
            $audit = true;
            if($result['errcode'] !== 0) {
                $this->weixinlog($this->cmd,$this->myinfo['id'] .' : '. $content .' : '. var_export($result,true));
                switch ($result['errcode']) {
                    case 87014: $audit = false;break;
                    case 40001:
                        $audit = false;break;
                    default:$audit = false;;
                }
            }
        } catch (\Exception $e) {
            throw new HttpResponseException(ajax($e->getMessage(),-1));
        }
        return $audit;
    }

    /*微信图片敏感内容检测*/
    public function imgSecCheck($image_path) {
        $audit = true;
        $img = @file_get_contents($image_path);
        if(!$img) {
            $this->weixinlog($this->cmd,'file_get_contents(): php_network_getaddresses: getaddrinfo failed: Name or service not known');
            return true;
        }
        $filePath = '/dev/shm/tmp1.png';
        file_put_contents($filePath, $img);
        $obj = new \CURLFile(realpath($filePath));
        $obj->setMimeType("image/jpeg");
        $file['media'] = $obj;
        $app = Factory::payment($this->mp_config);
        $access_token = $app->access_token;
        $token = $access_token->getToken();
        $url = 'https://api.weixin.qq.com/wxa/img_sec_check?access_token=' . $token['access_token'];
        $info = curl_post_data($url,$file);
        $result = json_decode($info,true);
        try {
            if($result['errcode'] !== 0) {
                $this->weixinlog($this->cmd,$this->myinfo['id'] .' : '. $image_path .' : '. var_export($result,true));
                switch ($result['errcode']) {
                    case 87014: $audit = false;break;
                    case 40001:
                        $audit = false;break;
                    default:$audit = false;;
                }
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return $audit;
    }





}