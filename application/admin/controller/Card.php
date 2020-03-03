<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2019/12/10
 * Time: 13:45
 */
namespace app\admin\controller;

use think\Db;
class Card extends Base {

    //卡牌列表
    public function cardList() {

        $param['attr_id'] = input('param.attr_id','');
        $param['resource'] = input('param.resource','');
        $param['type_id'] = input('param.type_id','');
        $param['camp_id'] = input('param.camp_id','');
        $param['ability_id'] = input('param.ability_id','');
        $param['version_id'] = input('param.version_id','');
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $whereCard = [
            ['status','=',1]
        ];
        $order = ['sort' => 'ASC', 'id' => 'DESC'];
        if($param['attr_id'] !== '') { $whereCard[] = ['attr_id','in',explode(',',$param['attr_id'])]; }
        if($param['type_id'] !== '') { $whereCard[] = ['type_id','in',explode(',',$param['type_id'])]; }
        if($param['camp_id'] !== '') { $whereCard[] = ['camp_id','in',explode(',',$param['camp_id'])]; }
        if($param['ability_id'] !== '') { $whereCard[] = ['ability_id','in',explode(',',$param['ability_id'])]; }
        if($param['version_id'] !== '') { $whereCard[] = ['version_id','in',explode(',',$param['version_id'])]; }
        if($param['resource'] !== '') {
            $resource_arr = explode(',',$param['resource']);
            if(in_array(7,$resource_arr)) {
                $whereCard[] = ['resource','in',array_merge($resource_arr,range(8,20))];
            }else {
                $whereCard[] = ['resource','in',$resource_arr];
            }
        }
        if($param['search']) { $whereCard[] = ['card_name','like',"%{$param['search']}%"]; }

        try {
            $count = Db::table('mp_card')->where($whereCard)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_card')->where($whereCard)->limit(($curr_page-1)*$perpage,$perpage)->order($order)->select();
            $card_type = Db::table('mp_card_type')->select();
            $card_camp = Db::table('mp_card_camp')->select();
            $card_attr = Db::table('mp_card_attr')->select();
            $card_ability = Db::table('mp_card_ability')->select();
            $card_version = Db::table('mp_card_version')->select();
            $type = [];
            $camp = [];
            $attr = [];
            $ability = [];
            $version = [];
            foreach ($card_type as $v) {$type[$v['id']] = $v['type_name'];}
            foreach ($card_camp as $v) {$camp[$v['id']] = [
                'camp_name' => $v['camp_name'],
                'icon' => $v['icon']
            ];}
            foreach ($card_attr as $v) {$attr[$v['id']] = [
                'attr_name' => $v['attr_name'],
                'icon' => $v['icon']
            ];}
            foreach ($card_ability as $v) {$ability[$v['id']] = [
                'ability_name' => $v['ability_name'],
                'icon' => $v['icon']
            ];}
            foreach ($card_version as $v) {$version[$v['id']] = $v['version_name'];}
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('count',$count);
        $this->assign('list',$list);
        $this->assign('card_type',$card_type);
        $this->assign('card_camp',$card_camp);
        $this->assign('card_attr',$card_attr);
        $this->assign('card_version',$card_version);
        $this->assign('card_ability',$card_ability);
        $this->assign('type',$type);
        $this->assign('camp',$camp);
        $this->assign('attr',$attr);
        $this->assign('ability',$ability);
        $this->assign('version',$version);
        $this->assign('page',$page);
        $this->assign('param',$param);
        return $this->fetch();

    }

    public function cardAdd() {
        if(request()->isPost()) {
            $val['card_name'] = input('post.card_name');
            $val['attr_id'] = input('post.attr_id');
            $val['resource'] = input('post.resource');
            $val['type_id'] = input('post.type_id');
            $val['camp_id'] = input('post.camp_id');
            $val['ability_id'] = input('post.ability_id');
            $val['version_id'] = input('post.version_id');
            checkInput($val);
            $val['desc'] = input('post.desc');
            $val['origin'] = input('post.origin');
            $val['qa'] = input('post.qa');
            $val['wushuang'] = input('post.wushuang');
            $val['desc_show'] = input('post.desc_show');
            $val['origin_show'] = input('post.origin_show');
            $val['qa_show'] = input('post.qa_show');
            $val['create_time'] = time();
            $val['update_time'] = $val['create_time'];

            try {
                if(isset($_FILES['file'])) {
                    $info = upload('file',$this->upload_base_path . 'card/');
                    if($info['error'] === 0) {
                        $val['pic'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }else {
                    return ajax('请上传图一',-1);
                }
                if(isset($_FILES['file2'])) {
                    $info = upload('file2',$this->upload_base_path . 'card/');
                    if($info['error'] === 0) {
                        $val['cover'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }else {
                    return ajax('请上传图二',-1);
                }
                Db::table('mp_card')->insert($val);
            } catch (\Exception $e) {
                if(isset($val['pic'])) {
                    @unlink($val['pic']);
                }
                if(isset($val['cover'])) {
                    @unlink($val['cover']);
                }
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        try {
            $card_type = Db::table('mp_card_type')->select();
            $card_camp = Db::table('mp_card_camp')->select();
            $card_attr = Db::table('mp_card_attr')->select();
            $card_version = Db::table('mp_card_version')->select();
            $card_ability = Db::table('mp_card_ability')->select();
            $card_resource = config('card.resource');
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('card_type',$card_type);
        $this->assign('card_camp',$card_camp);
        $this->assign('card_attr',$card_attr);
        $this->assign('card_version',$card_version);
        $this->assign('card_ability',$card_ability);
        $this->assign('card_resource',$card_resource);
        return $this->fetch();
    }

    public function cardDetail() {
        $val['id'] = input('param.id',0);
        try {
            $whereCard = [
                ['id','=',$val['id']]
            ];
            $card_exist = Db::table('mp_card')->where($whereCard)->find();
            if(!$card_exist) {
                die('非法操作');
            }
            $card_type = Db::table('mp_card_type')->select();
            $card_camp = Db::table('mp_card_camp')->select();
            $card_attr = Db::table('mp_card_attr')->select();
            $card_version = Db::table('mp_card_version')->select();
            $card_ability = Db::table('mp_card_ability')->select();
            $card_resource = config('card.resource');
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('info',$card_exist);
        $this->assign('card_type',$card_type);
        $this->assign('card_camp',$card_camp);
        $this->assign('card_attr',$card_attr);
        $this->assign('card_version',$card_version);
        $this->assign('card_ability',$card_ability);
        $this->assign('card_resource',$card_resource);
        return $this->fetch();
    }

    public function cardMod() {
        $val['card_name'] = input('post.card_name');
        $val['attr_id'] = input('post.attr_id');
        $val['resource'] = input('post.resource');
        $val['type_id'] = input('post.type_id');
        $val['camp_id'] = input('post.camp_id');
        $val['ability_id'] = input('post.ability_id');
        $val['version_id'] = input('post.version_id');
        $val['id'] = input('post.id');
        checkInput($val);
        $val['desc'] = input('post.desc');
        $val['origin'] = input('post.origin');
        $val['qa'] = input('post.qa');
        $val['wushuang'] = input('post.wushuang');
        $val['desc_show'] = input('post.desc_show');
        $val['origin_show'] = input('post.origin_show');
        $val['qa_show'] = input('post.qa_show');
        $val['create_time'] = time();
        $val['update_time'] = $val['create_time'];

        try {
            $whereCard = [
                ['id','=',$val['id']]
            ];
            $card_exist = Db::table('mp_card')->where($whereCard)->find();
            if(!$card_exist) {
                return ajax('非法参数',-1);
            }
            if(isset($_FILES['file'])) {
                $info = upload('file',$this->upload_base_path . 'card/');
                if($info['error'] === 0) {
                    $val['pic'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            if(isset($_FILES['file2'])) {
                $info = upload('file2',$this->upload_base_path . 'card/');
                if($info['error'] === 0) {
                    $val['cover'] = $info['data'];
                }else {
                    return ajax($info['msg'],-1);
                }
            }
            Db::table('mp_card')->where($whereCard)->update($val);
        } catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            if(isset($val['cover'])) {
                @unlink($val['cover']);
            }
            return ajax($e->getMessage(), -1);
        }
        if(isset($val['pic'])) {
            @unlink($card_exist['pic']);
        }
        if(isset($val['cover'])) {
            @unlink($card_exist['cover']);
        }
        return ajax();
    }

    public function cardHide() {

    }

    public function cardShow() {

    }

    public function cardDel() {

    }

    /*------ 卡牌属性 ------*/

    //属性列表
    public function attrList() {
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $page['query'] = http_build_query(input('param.'));
        $whereAttr = [];
        try {
            $count = Db::table('mp_card_attr')->where($whereAttr)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_card_attr')->where($whereAttr)->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    //添加属性
    public function attrAdd() {
        if(request()->isPost()) {
            $val['attr_name'] = input('post.attr_name');
            checkInput($val);
            try {
                if(isset($_FILES['file'])) {
                    $info = upload('file',$this->upload_base_path . 'card/');
                    if($info['error'] === 0) {
                        $val['icon'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }else {
                    return ajax('请上传图片',-1);
                }
                Db::table('mp_card_attr')->insert($val);
            } catch (\Exception $e) {
                if(isset($val['icon'])) {
                    @unlink($val['icon']);
                }
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        return $this->fetch();
    }
    //属性详情
    public function attrDetail() {
        $val['id'] = input('param.id');
        checkInput($val);
        try {
            $whereAttr = [
                ['id','=',$val['id']]
            ];
            $attr_exist = Db::table('mp_card_attr')->where($whereAttr)->find();
            if(!$attr_exist) {
                die('非法参数');
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('info',$attr_exist);
        return $this->fetch();
    }
    //编辑属性
    public function attrMod() {
        if(request()->isPost()) {
            $val['attr_name'] = input('post.attr_name');
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $attr_exist = Db::table('mp_card_attr')->where($whereAttr)->find();
                if(!$attr_exist) {
                    return ajax('非法参数',-1);
                }
                if(isset($_FILES['file'])) {
                    $info = upload('file',$this->upload_base_path . 'card/');
                    if($info['error'] === 0) {
                        $val['icon'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }
                Db::table('mp_card_attr')->where($whereAttr)->update($val);
            } catch (\Exception $e) {
                if(isset($val['icon'])) {
                    @unlink($val['icon']);
                }
                return ajax($e->getMessage(), -1);
            }
            if(isset($val['icon'])) {
                @unlink($attr_exist['icon']);
            }
            return ajax();
        }
    }
    //删除属性
    public function attrDel() {
        if(request()->isPost()) {
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $attr_exist = Db::table('mp_card_attr')->where($whereAttr)->find();
                if(!$attr_exist) {
                    return ajax('非法参数',-1);
                }
                $whereCard = [
                    ['attr_id','=',$attr_exist['id']]
                ];
                $card_exist = Db::table('mp_card')->where($whereCard)->find();
                if($card_exist) {
                    return ajax('此属性下有卡牌,无法删除',-1);
                }
                Db::table('mp_card_attr')->where($whereAttr)->delete();
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            @unlink($attr_exist['icon']);
            return ajax();
        }
    }

    /*------ 卡牌能力 ------*/

    //能力列表
    public function abilityList() {
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $page['query'] = http_build_query(input('param.'));
        $whereAttr = [];
        try {
            $count = Db::table('mp_card_ability')->where($whereAttr)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_card_ability')->where($whereAttr)->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    //添加能力
    public function abilityAdd() {
        if(request()->isPost()) {
            $val['ability_name'] = input('post.ability_name');
            checkInput($val);
            try {
                if(isset($_FILES['file'])) {
                    $info = upload('file',$this->upload_base_path . 'card/');
                    if($info['error'] === 0) {
                        $val['icon'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }else {
                    return ajax('请上传图片',-1);
                }
                Db::table('mp_card_ability')->insert($val);
            } catch (\Exception $e) {
                if(isset($val['icon'])) {
                    @unlink($val['icon']);
                }
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        return $this->fetch();
    }
    //能力详情
    public function abilityDetail() {
        $val['id'] = input('param.id');
        checkInput($val);
        try {
            $whereAttr = [
                ['id','=',$val['id']]
            ];
            $ability_exist = Db::table('mp_card_ability')->where($whereAttr)->find();
            if(!$ability_exist) {
                die('非法参数');
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('info',$ability_exist);
        return $this->fetch();
    }
    //编辑能力
    public function abilityMod() {
        if(request()->isPost()) {
            $val['ability_name'] = input('post.ability_name');
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $ability_exist = Db::table('mp_card_ability')->where($whereAttr)->find();
                if(!$ability_exist) {
                    return ajax('非法参数',-1);
                }
                if(isset($_FILES['file'])) {
                    $info = upload('file',$this->upload_base_path . 'card/');
                    if($info['error'] === 0) {
                        $val['icon'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }
                Db::table('mp_card_ability')->where($whereAttr)->update($val);
            } catch (\Exception $e) {
                if(isset($val['icon'])) {
                    @unlink($val['icon']);
                }
                return ajax($e->getMessage(), -1);
            }
            if(isset($val['icon'])) {
                @unlink($ability_exist['icon']);
            }
            return ajax();
        }
    }
    //删除能力
    public function abilityDel() {
        if(request()->isPost()) {
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $ability_exist = Db::table('mp_card_ability')->where($whereAttr)->find();
                if(!$ability_exist) {
                    return ajax('非法参数',-1);
                }
                $whereCard = [
                    ['ability_id','=',$ability_exist['id']]
                ];
                $card_exist = Db::table('mp_card')->where($whereCard)->find();
                if($card_exist) {
                    return ajax('此能力下有卡牌,无法删除',-1);
                }
                Db::table('mp_card_ability')->where($whereAttr)->delete();
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            @unlink($ability_exist['icon']);
            return ajax();
        }
    }

    /*------ 卡牌阵营 ------*/

    //阵营列表
    public function campList() {
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $page['query'] = http_build_query(input('param.'));
        $whereAttr = [];
        try {
            $count = Db::table('mp_card_camp')->where($whereAttr)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_card_camp')->where($whereAttr)->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    //添加阵营
    public function campAdd() {
        if(request()->isPost()) {
            $val['camp_name'] = input('post.camp_name');
            checkInput($val);
            try {
                if(isset($_FILES['file'])) {
                    $info = upload('file',$this->upload_base_path . 'card/');
                    if($info['error'] === 0) {
                        $val['icon'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }else {
                    return ajax('请上传图片',-1);
                }
                Db::table('mp_card_camp')->insert($val);
            } catch (\Exception $e) {
                if(isset($val['icon'])) {
                    @unlink($val['icon']);
                }
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        return $this->fetch();
    }
    //阵营详情
    public function campDetail() {
        $val['id'] = input('param.id');
        checkInput($val);
        try {
            $whereAttr = [
                ['id','=',$val['id']]
            ];
            $camp_exist = Db::table('mp_card_camp')->where($whereAttr)->find();
            if(!$camp_exist) {
                die('非法参数');
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('info',$camp_exist);
        return $this->fetch();
    }
    //编辑阵营
    public function campMod() {
        if(request()->isPost()) {
            $val['camp_name'] = input('post.camp_name');
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $camp_exist = Db::table('mp_card_camp')->where($whereAttr)->find();
                if(!$camp_exist) {
                    return ajax('非法参数',-1);
                }
                if(isset($_FILES['file'])) {
                    $info = upload('file',$this->upload_base_path . 'card/');
                    if($info['error'] === 0) {
                        $val['icon'] = $info['data'];
                    }else {
                        return ajax($info['msg'],-1);
                    }
                }
                Db::table('mp_card_camp')->where($whereAttr)->update($val);
            } catch (\Exception $e) {
                if(isset($val['icon'])) {
                    @unlink($val['icon']);
                }
                return ajax($e->getMessage(), -1);
            }
            if(isset($val['icon'])) {
                @unlink($camp_exist['icon']);
            }
            return ajax();
        }
    }
    //删除阵营
    public function campDel() {
        if(request()->isPost()) {
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $camp_exist = Db::table('mp_card_camp')->where($whereAttr)->find();
                if(!$camp_exist) {
                    return ajax('非法参数',-1);
                }
                $whereCard = [
                    ['ability_id','=',$camp_exist['id']]
                ];
                $card_exist = Db::table('mp_card')->where($whereCard)->find();
                if($card_exist) {
                    return ajax('此阵营下有卡牌,无法删除',-1);
                }
                Db::table('mp_card_camp')->where($whereAttr)->delete();
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            @unlink($camp_exist['icon']);
            return ajax();
        }
    }


    /*------ 卡牌类型 ------*/

    //类型列表
    public function typeList() {
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $page['query'] = http_build_query(input('param.'));
        $whereAttr = [];
        try {
            $count = Db::table('mp_card_type')->where($whereAttr)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_card_type')->where($whereAttr)->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    //添加类型
    public function typeAdd() {
        if(request()->isPost()) {
            $val['type_name'] = input('post.type_name');
            checkInput($val);
            try {
                Db::table('mp_card_type')->insert($val);
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        return $this->fetch();
    }
    //类型详情
    public function typeDetail() {
        $val['id'] = input('param.id');
        checkInput($val);
        try {
            $whereAttr = [
                ['id','=',$val['id']]
            ];
            $type_exist = Db::table('mp_card_type')->where($whereAttr)->find();
            if(!$type_exist) {
                die('非法参数');
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('info',$type_exist);
        return $this->fetch();
    }
    //编辑类型
    public function typeMod() {
        if(request()->isPost()) {
            $val['type_name'] = input('post.type_name');
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $type_exist = Db::table('mp_card_type')->where($whereAttr)->find();
                if(!$type_exist) {
                    return ajax('非法参数',-1);
                }
                Db::table('mp_card_type')->where($whereAttr)->update($val);
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
    }
    //删除类型
    public function typeDel() {
        if(request()->isPost()) {
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $type_exist = Db::table('mp_card_type')->where($whereAttr)->find();
                if(!$type_exist) {
                    return ajax('非法参数',-1);
                }
                $whereCard = [
                    ['type_id','=',$val['id']]
                ];
                $card_exist = Db::table('mp_card')->where($whereCard)->find();
                if($card_exist) {
                    return ajax('此类型下有卡牌,无法删除',-1);
                }
                Db::table('mp_card_type')->where($whereAttr)->delete();
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
    }

    /*------ 卡牌版本 ------*/

    //版本列表
    public function versionList() {
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $page['query'] = http_build_query(input('param.'));
        $whereAttr = [];
        try {
            $count = Db::table('mp_card_version')->where($whereAttr)->count();
            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_card_version')->where($whereAttr)->limit(($curr_page-1)*$perpage,$perpage)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch();
    }
    //添加版本
    public function versionAdd() {
        if(request()->isPost()) {
            $val['version_name'] = input('post.version_name');
            checkInput($val);
            try {
                Db::table('mp_card_version')->insert($val);
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        return $this->fetch();
    }
    //版本详情
    public function versionDetail() {
        $val['id'] = input('param.id');
        checkInput($val);
        try {
            $whereAttr = [
                ['id','=',$val['id']]
            ];
            $version_exist = Db::table('mp_card_version')->where($whereAttr)->find();
            if(!$version_exist) {
                die('非法参数');
            }
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $this->assign('info',$version_exist);
        return $this->fetch();
    }
    //编辑版本
    public function versionMod() {
        if(request()->isPost()) {
            $val['version_name'] = input('post.version_name');
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $version_exist = Db::table('mp_card_version')->where($whereAttr)->find();
                if(!$version_exist) {
                    return ajax('非法参数',-1);
                }
                Db::table('mp_card_version')->where($whereAttr)->update($val);
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
    }
    //删除版本
    public function versionDel() {
        if(request()->isPost()) {
            $val['id'] = input('post.id');
            checkInput($val);
            try {
                $whereAttr = [
                    ['id','=',$val['id']]
                ];
                $version_exist = Db::table('mp_card_version')->where($whereAttr)->find();
                if(!$version_exist) {
                    return ajax('非法参数',-1);
                }
                $whereCard = [
                    ['version_id','=',$val['id']]
                ];
                $card_exist = Db::table('mp_card')->where($whereCard)->find();
                if($card_exist) {
                    return ajax('此版本下有卡牌,无法删除',-1);
                }
                Db::table('mp_card_version')->where($whereAttr)->delete();
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
    }

    //商品排序
    public function sortCard() {
        $val['id'] = input('post.id');
        $val['sort'] = input('post.sort');
        checkInput($val);
        try {
            Db::table('mp_card')->update($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val);
    }



}