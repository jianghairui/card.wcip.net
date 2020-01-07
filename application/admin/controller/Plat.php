<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2018/10/8
 * Time: 18:21
 */
namespace app\admin\controller;
use think\Exception;
use think\Db;
class Plat extends Base {

    //轮播图列表
    public function slideList() {

        $param['type'] = input('param.type');
        $page['query'] = http_build_query(input('param.'));
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        try {
            $where = [];
            if($param['type']) {
                $where[] = ['type','=',$param['type']];
            }
            $count = Db::table('mp_slideshow')->where($where)->count();

            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_slideshow')->where($where)
                ->order(['sort'=>'ASC'])
                ->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('param',$param);
        return $this->fetch();

    }

    public function slideAdd() {
        return $this->fetch();
    }

    //添加轮播图POST
    public function slideAddPost() {
        $val['title'] = input('post.title');
        $val['status'] = input('post.status');
        checkInput($val);
        $val['url'] = input('post.url');
        if(isset($_FILES['file'])) {
            $info = upload('file',$this->upload_base_path . 'slide/');
            if($info['error'] === 0) {
                $val['pic'] = $info['data'];
            }else {
                return ajax($info['msg'],-1);
            }
        }else {
            return ajax('请上传图片',-1);
        }

        try {
            $res = Db::table('mp_slideshow')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        if($res) {
            return ajax([],1);
        }else {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax('添加失败',-1);
        }
    }

    //修改轮播图
    public function slideDetail() {
        $val['id'] = input('param.id');
        try {
            $exist = Db::table('mp_slideshow')->where('id','=',$val['id'])->find();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        if(!$exist) {
            $this->error('非法操作',url('Banner/slideshow'));
        }

        $this->assign('info',$exist);
        return $this->fetch();
    }

    //修改轮播图POST
    public function slideMod() {
        if(request()->isPost()) {
            $val['title'] = input('post.title');
            $val['id'] = input('post.slideid');
            $val['status'] = input('post.status');
            checkInput($val);
            $val['url'] = input('post.url');
            try {
                $exist = Db::table('mp_slideshow')->where('id',$val['id'])->find();
                if(!$exist) {
                    return ajax('非法操作',-1);
                }
                if(isset($_FILES['file'])) {
                    $info = upload('file',$this->upload_base_path . 'slide/');
                    if($info['error'] === 0) {
                        $val['pic'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }
                Db::table('mp_slideshow')->update($val);
            }catch (Exception $e) {
                if(isset($_FILES['file'])) {
                    @unlink($val['pic']);
                }
                return ajax($e->getMessage(),-1);
            }
            if(isset($_FILES['file'])) {
                @unlink($exist['pic']);
            }
            return ajax([],1);
        }
    }

    //删除轮播图
    public function slide_del() {
        $val['id'] = input('post.slideid');
        checkInput($val);
        try {
            $whereSlide = [
                ['id','=',$val['id']]
            ];
            $exist = Db::table('mp_slideshow')->where($whereSlide)->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_slideshow')->where($whereSlide)->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        @unlink($exist['pic']);
        return ajax([],1);
    }

    //轮播图排序
    public function sortSlide() {
        $val['id'] = input('post.id');
        $val['sort'] = input('post.sort');
        checkInput($val);
        try {
            Db::table('mp_slideshow')->update($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val);
    }

    //禁用轮播图
    public function slide_stop() {
        $val['id'] = input('post.slideid');
        checkInput($val);
        try {
            $exist = Db::table('mp_slideshow')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_slideshow')->where('id',$val['id'])->update(['status'=>0]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    //启用轮播图
    public function slide_start() {
        $val['id'] = input('post.slideid');
        checkInput($val);
        try {
            $exist = Db::table('mp_slideshow')->where('id',$val['id'])->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_slideshow')->where('id',$val['id'])->update(['status'=>1]);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax([],1);
    }

    public function aboutUs() {
        $whereHome = [
            ['id','=',1]
        ];
        if(request()->isPost()) {
            $pic['logo'] = input('post.logo');
            foreach ($pic as $v) {
                if(!$v) {
                    return ajax('请上传所有图片',-1);
                }
            }
            $val['name'] = input('post.name');
            $val['address'] = input('post.address');
            $val['linkman'] = input('post.linkman');
            $val['tel'] = input('post.tel');
            $val['email'] = input('post.email');
            $val['weixin'] = input('post.weixin');
            $val['desc'] = input('post.desc');
            checkInput($val);
            $val['intro'] = input('post.intro');
            if(!is_email($val['email'])) {
                return ajax('无效的邮箱',-1);
            }
            if(!is_tel($val['tel'])) {
                return ajax('无效的手机号',-1);
            }
            try {
                $exist = Db::table('mp_plat')->where($whereHome)->find();
                if(!$exist) {
                    return ajax('非法操作',-1);
                }
                if(!file_exists($pic['logo'])) {
                    return ajax('请上传logo',-1);
                }

                foreach ($pic as $k=>$v) {
                    if ($v != $exist[$k]) {
                        $val[$k] = rename_file($v,$this->upload_base_path . 'plat/');
                    }else {
                        $val[$k] = $v;
                    }
                }
                Db::table('mp_plat')->where($whereHome)->update($val);
            }catch (\Exception $e) {
                foreach ($pic as $k=>$v) {
                    if ($v != $exist[$k]) {
                        @unlink($v);
                    }
                }
                return ajax($e->getMessage(),-1);
            }
            foreach ($pic as $k=>$v) {
                if ($v != $exist[$k]) {
                    @unlink($exist[$k]);
                }
            }
            return ajax();
        }

        try {
            $exist = Db::table('mp_plat')->where($whereHome)->find();
            if (!$exist) {
                die('NOTHING FOUND');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$exist);
        return $this->fetch();
    }

    public function bloodMod() {
        $whereHome = [
            ['id','=',1]
        ];
        if(request()->isPost()) {
            $pic['blood1'] = input('post.blood1');
            $pic['blood2'] = input('post.blood2');
            $pic['card_bg'] = input('post.card_bg');
            try {
                $exist = Db::table('mp_plat')->where($whereHome)->find();
                if(!$exist) {
                    return ajax('非法操作',-1);
                }
                if(!file_exists($pic['blood1'])) {
                    return ajax('请上传计血器图一',-1);
                }
                if(!file_exists($pic['blood2'])) {
                    return ajax('请上传计血器图二',-1);
                }
                if(!file_exists($pic['card_bg'])) {
                    return ajax('请上传卡牌背景图',-1);
                }
                $val = [];
                foreach ($pic as $k=>$v) {
                    if ($v != $exist[$k]) {
                        $val[$k] = rename_file($v,$this->upload_base_path . 'plat/');
                    }else {
                        $val[$k] = $v;
                    }
                }
                Db::table('mp_plat')->where($whereHome)->update($val);
            }catch (\Exception $e) {
                foreach ($pic as $k=>$v) {
                    if ($v != $exist[$k]) {
                        @unlink($v);
                    }
                }
                return ajax($e->getMessage(),-1);
            }
            foreach ($pic as $k=>$v) {
                if ($v != $exist[$k]) {
                    @unlink($exist[$k]);
                }
            }
            return ajax();
        }

        try {
            $exist = Db::table('mp_plat')->where($whereHome)->find();
            if (!$exist) {
                die('NOTHING FOUND');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$exist);
        return $this->fetch();
    }


}