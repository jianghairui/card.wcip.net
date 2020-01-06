<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2020/1/3
 * Time: 17:08
 */
namespace app\admin\controller;

use think\Db;
class User extends Base {


    public function userList() {
        $param['share_auth'] = input('param.share_auth','');
        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $where = [];

        if($param['share_auth'] !== '') {
            $where[] = ['share_auth','=',$param['share_auth']];
        }

        if($param['datemin']) {
            $where[] = ['create_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['datemin'])))];
        }

        if($param['datemax']) {
            $where[] = ['create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])))];
        }

        if($param['search']) {
            $where[] = ['nickname|tel','like',"%{$param['search']}%"];
        }

        $order = ['id'=>'DESC'];
        try {
            $count = Db::table('mp_user')
                ->where($where)
                ->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_user')->where($where)
                ->order($order)
                ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('param',$param);
        return $this->fetch();
    }

    public function userDetail() {
        $param['id'] = input('param.id');
        try {
            $where = [
                ['id','=',$param['id']]
            ];
            $info = Db::table('mp_user')->where($where)->find();
            if(!$info) {die('非法参数');}
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function shareAuthMod() {
        $val['id'] = input('post.id');
        try {
            $whereUser = [
                ['id','=',$val['id']]
            ];
            $user_exist = Db::table('mp_user')->where($whereUser)->find();
            if(!$user_exist) {return ajax('非法参数',-1);}
            if($user_exist['share_auth'] == 1) {
                Db::table('mp_user')->where($whereUser)->update(['share_auth'=>0]);
                return ajax(false);
            }else {
                Db::table('mp_user')->where($whereUser)->update(['share_auth'=>1]);
                return ajax(true);
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

    }



}