<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/4/18
 * Time: 15:49
 * 公众号场景码
 */
namespace app\admin\controller;

use think\Db;
use wx\Jssdk;

class Qrcode extends Base {

    //用户列表
    public function userList() {
        $param['subscribe'] = input('param.subscribe','');
        $param['scene_id'] = input('param.scene_id','');
        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $param['search'] = input('param.search','');
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $page['query'] = http_build_query(input('param.'));
        $whereUser = [];

        if($param['subscribe'] !== '') {
            $whereUser[] = ['u.subscribe','=',$param['subscribe']];
        }
        if($param['scene_id'] !== '') {
            $whereUser[] = ['u.scene_id','=',$param['scene_id']];
        }
        if($param['datemin']) {
            $whereUser[] = ['u.sub_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['datemin'])))];
        }
        if($param['datemax']) {
            $whereUser[] = ['u.sub_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])))];
        }
        if($param['search']) {
            $whereUser[] = ['u.nickname','like',"%{$param['search']}%"];
        }

        try {
            $count = Db::table('mp_scene_user')->alias('u')->where($whereUser)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_scene_user')->alias('u')
                ->join('mp_scene c','u.scene_id=c.id','left')
                ->where($whereUser)
                ->field('u.*,c.scene_name')
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->order(['u.id'=>'DESC'])
                ->select();
            $scene_list = Db::table('mp_scene')->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('param',$param);
        $this->assign('scene_list',$scene_list);
        return $this->fetch();
    }

    //场景列表
    public function sceneList() {
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $page['query'] = http_build_query(input('param.'));
        $whereScene = [];
        try {
            $count = Db::table('mp_scene')->where($whereScene)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_scene')->where($whereScene)->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }

    //添加场景
    public function sceneAdd() {
        if(request()->isPost()) {
            $val['scene_name'] = input('post.scene_name');
            checkInput($val);
            $val['desc'] = input('post.desc');
            try {
                $scene_id = Db::table('mp_scene')->insertGetId($val);

                $post_data = [
                    'action_name' => 'QR_LIMIT_SCENE',
                    'action_info' => [
                        'scene' => [
                            'scene_id' => $scene_id
                        ]
                    ]
                ];
                $jssdk = new Jssdk($this->config['appid'], $this->config['app_secret']);
                $access_token = $jssdk->getAccessToken();

                $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
                $result = curl_post_data($url,json_encode($post_data,JSON_UNESCAPED_UNICODE));
                $obj = json_decode($result,true);
                if(isset($obj['errcode'])) {
                    return ajax($obj['errmsg'],-1);
                }
                $update['qrcode_url'] = $obj['url'];
                $update['qrcode'] = $this->genQrcode($obj['url'],$scene_id);
                $whereScene = [['id','=',$scene_id]];
                Db::table('mp_scene')->where($whereScene)->update($update);
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        return $this->fetch();
    }

    //场景详情
    public function sceneDetail() {
        $val['id'] = input('param.id');
        checkInput($val);
        try {
            $whereAttr = [
                ['id','=',$val['id']]
            ];
            $scene_exist = Db::table('mp_scene')->where($whereAttr)->find();
            if(!$scene_exist) {
                die('非法参数');
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('info',$scene_exist);
        return $this->fetch();
    }

    //场景修改
    public function sceneMod() {
        if(request()->isPost()) {
            $val['scene_name'] = input('post.scene_name');
            $val['id'] = input('post.id');
            checkInput($val);
            $val['desc'] = input('post.desc');
            try {
                $whereScene = [
                    ['id','=',$val['id']]
                ];
                $scene_exist = Db::table('mp_scene')->where($whereScene)->find();
                if(!$scene_exist) {
                    return ajax('非法参数',-1);
                }
                Db::table('mp_scene')->where($whereScene)->update($val);
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
    }


    //刷新场景码
    public function refreshQrode() {
        if(request()->isPost()) {
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereScene = [
                    ['id','=',$val['id']]
                ];
                $scene_exist = Db::table('mp_scene')->where($whereScene)->find();
                if(!$scene_exist) {
                    return ajax('非法参数',-1);
                }
                $new_qrcode = $this->genQrcode($scene_exist['qrcode_url'],$scene_exist['id']);
                Db::table('mp_scene')->where($whereScene)->update(['qrcode'=>$new_qrcode]);
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax($new_qrcode . '?' . mt_rand(1,1000));
        }
    }



    //生成二维码
    private function genQrcode($str = '',$scene_id = 1) {
        include ROOT_PATH . '/extend/phpqrcode/phpqrcode.php';
        $errorCorrectionLevel = "M"; // 纠错级别：L、M、Q、H
        $matrixPointSize = "6"; // 点的大小：1到10
        header('Content-Type:image/png');
        $file_path = 'sceneqrcode/'. md5($scene_id . config('qrcode_key')) .'.png';
        \QRcode::png($str, $file_path, $errorCorrectionLevel, $matrixPointSize);
//        exit;
        return $file_path;
    }


    public function test() {

        $where = [];

        $arr = [1,2,3];
        $where[] = ['id','in',$arr];
    }






}