<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/8/6
 * Time: 10:00
 */
namespace app\admin\controller;
use think\Db;
class Article extends Base {

    //新闻列表
    public function articleList()
    {
        $param['search'] = input('param.search');
        $page['query'] = http_build_query(input('param.'));

        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);
        $where = [
            ['del','=',0]
        ];
        if($param['search']) {
            $where[] = ['title','like',"%{$param['search']}%"];
        }
        $order = ['sort'=>'ASC','id'=>'DESC'];

        try {
            $count = Db::table('mp_article')->where($where)->count();

            $page['count'] = $count;
            $page['curr'] = $curr_page;
            $page['totalPage'] = ceil($count/$perpage);
            $list = Db::table('mp_article')
                ->where($where)
                ->order($order)
                ->limit(($curr_page - 1)*$perpage,$perpage)->select();
        }catch (\Exception $e) {
            die('SQL错误: ' . $e->getMessage());
        }
        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('param',$param);
        return $this->fetch();
    }

    //添加新闻页面
    public function articleAdd() {
        return $this->fetch();
    }
    //添加新闻提交
    public function articleAddPost() {
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        checkInput($val);
        $val['content'] = input('post.content');
        $val['create_time'] = input('post.create_time',date('Y-m-d H:i:s'));
        $val['admin_id'] = session('admin_id');

        try {
//            if(isset($_FILES['file'])) {
//                $info = upload('file',$this->upload_base_path . 'article/');
//                if($info['error'] === 0) {
//                    $val['pic'] = $info['data'];
//                }else {
//                    return ajax($info['msg'],-1);
//                }
//            }else {
//                return ajax('请上传封面图',-1);
//            }
            Db::table('mp_article')->insert($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        return ajax([]);

    }
    //修改新闻页面
    public function articleDetail() {
        $article_id = input('param.id');
        try {
            $exist = Db::table('mp_article')->where('id','=',$article_id)->find();
            if(!$exist) {
                die('非法操作');
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$exist);
        return $this->fetch();
    }

    //修改资讯提交
    public function articleMod() {
        $val['title'] = input('post.title');
        $val['desc'] = input('post.desc');
        $val['id'] = input('post.id');
        checkInput($val);
        $val['content'] = input('post.content');
        $val['create_time'] = input('post.create_time',date('Y-m-d H:i:s'));

        $where = [
            ['id','=',$val['id']]
        ];
        try {
            $exist = Db::table('mp_article')->where($where)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
//            if(isset($_FILES['file'])) {
//                $info = upload('file',$this->upload_base_path . 'article/');
//                if($info['error'] === 0) {
//                    $val['pic'] = $info['data'];
//                }else {
//                    return ajax($info['msg'],-1);
//                }
//            }

            Db::table('mp_article')->where($where)->update($val);
        }catch (\Exception $e) {
            if(isset($val['pic'])) {
                @unlink($val['pic']);
            }
            return ajax($e->getMessage(),-1);
        }
        if(isset($val['pic'])) {
            @unlink($exist['pic']);
        }
        return ajax([]);
    }
    //删除资讯
    public function articleDel() {
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $exist = Db::table('mp_article')->where('id','=',$val['id'])->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_article')->where('id','=',$val['id'])->delete();
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        @unlink($exist['pic']);
        return ajax([],1);
    }
    //停用新闻
    public function articleHide()
    {
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $exist = Db::table('mp_article')->where('id','=',$val['id'])->find();
            if (!$exist) {
                return ajax('非法操作', -1);
            }
            Db::table('mp_article')->where('id','=',$val['id'])->update(['status' => 0]);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }
    //启用新闻
    public function articleShow() {
        $val['id'] = input('post.id');
        checkInput($val);
        try {
            $exist = Db::table('mp_article')->where('id','=',$val['id'])->find();
            if(!$exist) {
                return ajax('非法操作',-1);
            }
            Db::table('mp_article')->where('id','=',$val['id'])->update(['status'=>1]);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        return ajax();
    }

    public function recommend() {
        $id = input('post.id');
        try {
            $where = [
                ['id','=',$id]
            ];
            $exist = Db::table('mp_article')->where($where)->find();
            if(!$exist) {
                return ajax('非法参数',-1);
            }
            if($exist['recommend'] == 1) {
                Db::table('mp_article')->where($where)->update(['recommend'=>0]);
                return ajax(false);
            }else {
                Db::table('mp_article')->where($where)->update(['recommend'=>1]);
                return ajax(true);
            }
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
    }

    //文章排序
    public function sortArticle() {
        $val['id'] = input('post.id');
        $val['sort'] = input('post.sort');
        checkInput($val);
        try {
            Db::table('mp_article')->update($val);
        }catch (\Exception $e) {
            return ajax($e->getMessage(),-1);
        }
        return ajax($val);
    }

    public function gameRule() {
        if(request()->isPost()) {
            $val['content'] = input('post.content');
            checkInput($val);
            try {
                $whereRule = [
                    ['id','=',1]
                ];
                $info = Db::table('mp_game_rule')->where($whereRule)->find();
                if(!$info) {return ajax('非法操作',-1);}
                Db::table('mp_game_rule')->where($whereRule)->update($val);
            } catch (\Exception $e) {
                return ajax($e->getMessage(), -1);
            }
            return ajax();
        }
        try {
            $whereRule = [
                ['id','=',1]
            ];
            $info = Db::table('mp_game_rule')->where($whereRule)->find();
            if(!$info) {die('非法操作');}
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        $this->assign('info',$info);
        return $this->fetch();
    }






}