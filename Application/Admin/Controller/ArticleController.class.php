<?php

namespace Admin\Controller;

use Think\log;

/**
 * 健康知识管理控制器
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class ArticleController extends BaseController
{

    /**
     * 健康知识列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index()
    {
        $list = D('api_article as a')->join('api_articletype as b on a.type=b.id')->field("a.*,b.typename")->select();
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 新增文章
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add()
    {
        if (IS_POST) {
            $data = I('post.');
            //print_r($_POST['img']);die;
            $data['publishtime'] = time();
            $data['pic'] = implode('||', $data['file']);

            $res = D('api_article')->add($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        } else {
            $typelist = D('api_articletype')->order('sort asc')->select();
            $fid = '';
            $id = I('get.id');
            if (!empty($id)) {
                $fid = $id;
            }
            //print_r($typelist);
            $this->assign('typelist', $typelist);
            $this->assign('fid', $fid);
            $this->display();
        }
    }

    /**
     * 文章上线
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function open()
    {
        $id = I('post.id');
        $res = D('api_article')->where(array('id' => $id))->save(array('ifshow' => 0));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('添加成功');
        }
    }

    /**
     * 文章下线
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function close()
    {
        $id = I('post.id');
        $res = D('api_article')->where(array('id' => $id))->save(array('ifshow' => 1));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('添加成功');
        }
    }

    /**
     * 编辑文章
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit()
    {
        //获取网站根目录$url
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $str = substr($PHP_SELF, 1);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($str, 0, strpos($str, '/') + 1);
        //拼接图片url地址

        if (IS_GET) {
            $typelist = D('api_articletype')->order('sort asc')->select();
            $id = I('get.id');
            $listInfo = D('api_article')->where("id='$id'")->find();

            $img = explode('||', $listInfo['pic']);
            $img = array_filter($img);
            //print_r($img);
            $this->assign('detail', $listInfo);
            $this->assign('img', $img);
            $this->assign('typelist', $typelist);
            $this->display('add');
        } elseif (IS_POST) {
            $postData = I('post.');
            $postData['pic'] = implode('||', $postData['file']);
            //print_r($postData);die();
            $res = D('api_article')->where(array('id' => $postData['id']))->save($postData);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('编辑成功');
            }
        }
    }

    public function del()
    {
        $id = I('post.id');
        $childNum = D('api_article')->where(array('id' => $id))->count();
        if ($childNum) {
            D('api_article')->where(array('id' => $id))->delete();
            $this->ajaxSuccess('删除成功');

        } else {
            $this->ajaxError("当前文章不存在");
        }
    }

    /**
     * 文章类型列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function typelist()
    {
        $list = D('api_articletype')->order('sort desc')->select();
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 新增类型
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function addtype()
    {
        if (IS_POST) {
            $data = I('post.');
            $data['addtime'] = time();
            $res = D('api_articletype')->add($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        } else {

            $this->display();
        }
    }

    /**
     * 编辑类型
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edittype()
    {
        if (IS_GET) {
            $id = I('get.id');
            $listInfo = D('api_articletype')->where("id='$id'")->find();
            $this->assign('detail', $listInfo);

            $this->display('addtype');
        } elseif (IS_POST) {
            $postData = I('post.');
            $res = D('api_articletype')->where(array('id' => $postData['id']))->save($postData);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('编辑成功');
            }
        }
    }

    /* 删除类型 */
    public function deltype()
    {
        $id = I('post.id');
        $childNum = D('api_articletype')->where(array('id' => $id))->count();
        if ($childNum) {
            D('api_articletype')->where(array('id' => $id))->delete();
            $this->ajaxSuccess('删除成功');

        } else {
            $this->ajaxError("类型不存在");
        }
    }

    //图片上传
    public function upload()
    {
        //获取网站根目录地址$url
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $str = substr($PHP_SELF, 1);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($str, 0, strpos($str, '/') + 1);
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();   // 实例化上传类
            $upload->maxSize = 3145728;    // 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->rootPath = THINK_PATH;          // 设置附件上传根目录
            $upload->savePath = '../Public/';    // 设置附件上传（子）目录
            $upload->subName = 'articleimage/lunbo/';  //子文件夹
            $upload->saveName = date('Ymdhis');     //文件名
            $upload->replace = true;  //同名文件是否覆盖
            // 上传文件
            $images = $upload->upload();
            //return $images;
            //Log::record('$images', Log::DEBUG);die;
            //判断是否有图
            if ($images) {
                $info['url'] = $url . substr($images['file']['savepath'],3) . $images['file']['savename'];//拼接图片地址
                $info['imgid'] = rand(0, 1000);
                echo json_encode($info);
            } else {
                $a = $upload->getError();//获取失败信息
                echo json_encode($a);
            }
        } else {
            echo json_encode(2);
        }
    }

}