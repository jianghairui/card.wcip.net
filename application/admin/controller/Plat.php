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

    public function home() {
        $whereHome = [
            ['id','=',1]
        ];
        if(request()->isPost()) {
            $pic['logo'] = input('post.logo');
            $pic['qrcode1'] = input('post.qrcode1');
            $pic['qrcode2'] = input('post.qrcode2');
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
            $val['qq'] = input('post.qq');
            $val['desc'] = input('post.desc');
            checkInput($val);
            $val['intro'] = input('post.intro');
            $val['honor'] = input('post.honor');
            $val['plan'] = input('post.plan');
            if(!is_email($val['email'])) {
                return ajax('无效的邮箱',-1);
            }
            if(!is_tel($val['tel'])) {
                return ajax('无效的手机号',-1);
            }
            try {
                $exist = Db::table('mp_home')->where($whereHome)->find();
                if(!$exist) {
                    return ajax('非法操作',-1);
                }
                if(!file_exists($pic['logo'])) {
                    return ajax('请上传logo',-1);
                }
                if(!file_exists($pic['qrcode1'])) {
                    return ajax('请上传公众号二维码',-1);
                }
                if(!file_exists($pic['qrcode2'])) {
                    return ajax('请上传微博二维码',-1);
                }

                foreach ($pic as $k=>$v) {
                    if ($v != $exist[$k]) {
                        $val[$k] = rename_file($v,$this->upload_base_path . 'plat/');
                    }else {
                        $val[$k] = $v;
                    }
                }
                Db::table('mp_home')->where($whereHome)->update($val);
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
            $exist = Db::table('mp_home')->where($whereHome)->find();
            if (!$exist) {
                die('NOTHING FOUND');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$exist);
        return $this->fetch();
    }


    public function shandong() {
        $whereHome = [
            ['id','=',2]
        ];
        if(request()->isPost()) {
            $pic['qrcode3'] = input('post.qrcode3');
            $pic['role0_icon'] = input('post.role0_icon');
            $pic['role1_icon'] = input('post.role1_icon');
            $pic['role2_icon'] = input('post.role2_icon');
            $pic['role3_icon'] = input('post.role3_icon');

            foreach ($pic as $v) {
                if(!$v) {
                    return ajax('请上传所有图片',-1);
                }
            }

            $val['role0'] = input('post.role0');
            $val['role1'] = input('post.role1');
            $val['role2'] = input('post.role2');
            $val['role3'] = input('post.role3');
            $val['video_url'] = input('post.video_url');
            $val['desc'] = input('post.desc');
            checkInput($val);
            $val['intro'] = input('post.intro');
            try {
                $exist = Db::table('mp_home')->where($whereHome)->find();
                if(!$exist) {
                    return ajax('非法操作',-1);
                }
                if(!file_exists($pic['qrcode3'])) {
                    return ajax('请上传小程序二维码',-1);
                }
                if(!file_exists($pic['role0_icon'])) {
                    return ajax('请上传普通用户图标',-1);
                }
                if(!file_exists($pic['role1_icon'])) {
                    return ajax('请上传博文图景图标',-1);
                }
                if(!file_exists($pic['role2_icon'])) {
                    return ajax('请上传设计师图标',-1);
                }
                if(!file_exists($pic['role3_icon'])) {
                    return ajax('请上传工厂图标',-1);
                }

                foreach ($pic as $k=>$v) {
                    if ($v != $exist[$k]) {
                        $val[$k] = rename_file($v,$this->upload_base_path . 'plat/');
                    }else {
                        $val[$k] = $v;
                    }
                }
                Db::table('mp_home')->where($whereHome)->update($val);
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
            $exist = Db::table('mp_home')->where($whereHome)->find();
            if (!$exist) {
                die('NOTHING FOUND');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$exist);
        return $this->fetch();
    }

    public function bwgsd() {
        $whereHome = [
            ['id','=',3]
        ];
        if(request()->isPost()) {
            $val['intro'] = input('post.intro');
            try {
                Db::table('mp_home')->where($whereHome)->update($val);
            }catch (\Exception $e) {
                return ajax($e->getMessage(),-1);
            }
            return ajax();
        }
        try {
            $exist = Db::table('mp_home')->where($whereHome)->find();
            if (!$exist) {
                die('NOTHING FOUNDED');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$exist);
        return $this->fetch();

    }


}