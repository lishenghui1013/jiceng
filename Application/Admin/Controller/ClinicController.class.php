<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/1/31
 * Time: 14:14
 */

namespace Admin\Controller;

use Think\Upload;
use Think\Log;

class ClinicController extends BaseController
{
    /**
     * 获取诊所菜单列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index()
    {
        $listInfo = D('api_hospital')->select();
        $this->assign('list', $listInfo);
        $this->display();
        Log::record('显示数据', Log::DEBUG);
    }

    /**
     * 添加诊所
     */
    public function add()
    {
        //获取网站根目录$url
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $str = substr($PHP_SELF, 1);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($str, 0, strpos($str, '/') + 1);
        if (IS_POST) {
            $data = I('post.');

            // $data['hos_pic']=I('hos_pic');
            $data['Ctime'] = date("Y-m-d H:i:s");
            $data['star'] = 0;


            $data['hos_pic'] = implode('||', $data['file']);
            if ($data['hos_pic'] == '') {
                $data['hos_pic'] = $url . 'Public/uploads/default/zanwu.png';
            }
            $res = D('api_hospital')->add($data);

            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                Log::record('添加成功', Log::DEBUG);
                $this->ajaxSuccess('添加成功');

            }
        } else {
            $city = D('api_cities')->where("provinceid='130000'")->select();
            //print_r($city);
            $this->assign('city', $city);
            $this->display();
        }
    }

    /**
     * 编辑诊所
     */
    public function edit()
    {
        if (IS_GET) {
            $id = I('get.id');
            if ($id) {
                //$details = D('api_hospital')->where(array('id' => $id))->select();
                $details = D('api_hospital')->where(array('id' => $id))->find();
                //$img=unserialize($details['hos_pic']);

                $img = explode('||', $details['hos_pic']);
                $img = array_filter($img);

                $city = D('api_cities')->where("provinceid='130000'")->select();
                $cityid = $details['cityid'];
                $area = D('api_areas')->where("cityid='$cityid'")->select();
                $this->assign('city', $city);
                $this->assign('area', $area);
                $this->assign('detail', $details);
                $this->assign('img', $img);

                $this->display('add');

                // echo var_dump ($details);
                //echo $id;

            }
        } else if (IS_POST) {
            $data = I('post.');
            //$data['hos_pic']=serialize($data['file']);
            $data['hos_pic'] = implode('||', $data['file']);
            $res = D('api_hospital')->where(array('id' => $data['id']))->save($data);
            if ($res === false) {

                $this->ajaxError('操作失败');
            } else {
                Log::record('修改成功', Log::DEBUG);
                $this->ajaxSuccess('添加成功');
            }
        }
    }

    /*
     *城市联动
    */
    public function ajaxcity()
    {
        $cityid = I('post.cityid');
        //echo $cityid;die();
        //Log::record('$cityid', Log::DEBUG);
        $area = D('api_areas')->where("cityid='$cityid'")->select();
        echo json_encode($area);
        //print_r($cityid);
    }

    /**
     * 删除诊所
     */
    public function del()
    {
        $id = I('post.id');
        $res = D('api_hospital')->where(array('id' => $id))->delete();
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            Log::record('删除成功', Log::DEBUG);
            $this->ajaxSuccess('操作成功');
        }
    }

    //上传图片
    public function upload()
    {
        //获取网站根目录$url
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $str = substr($PHP_SELF, 1);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($str, 0, strpos($str, '/') + 1);

        Log::record('上传图片', Log::DEBUG);
        $upload = new \Think\Upload();   // 实例化上传类
        $upload->maxSize = 3145728;    // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath = THINK_PATH;          // 设置附件上传根目录
        $upload->savePath = '../Public/';    // 设置附件上传（子）目录
        $upload->subName = 'uploads/hos/';  //子文件夹
        $upload->saveName = date('Ymdhis');     //文件名
        $upload->replace = true;  //同名文件是否覆盖
        // 上传文件
        $images = $upload->upload();
        if (!$images) {// 上传错误提示错误信息
            $a = $upload->getError();
            echo json_encode($a);
        } else {// 上传成功
            // $info['url']= 'Public/uploads/hos/'.$images['file']['savename'];
            $info['url'] = $url . preg_replace('/^..\//', '', $images['file']['savepath']) . $images['file']['savename'];//拼接图片地址
            $info['imgid'] = rand(0, 1000);
            Log::record($info, Log::DEBUG);
            echo json_encode($info);

        }

    }

    /**
     * 上传视频
     */
    public function uploads()
    {
        //获取网站根目录$url
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $str = substr($PHP_SELF, 1);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($str, 0, strpos($str, '/') + 1);
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();   // 实例化上传类
            $upload->maxSize = 0;    // 设置附件上传大小
            $upload->exts = array("mp4"); // 设置附件上传类型
            $upload->rootPath = THINK_PATH;          // 设置附件上传根目录
            $upload->savePath = '../Public/';    // 设置附件上传（子）目录
            $upload->subName = 'uploads/video/';  //子文件夹
            $upload->saveName = date('Ymdhis');     //文件名
            $upload->replace = true;  //同名文件是否覆盖
            // 上传文件
            $images = $upload->upload();
            //return $images;
            //判断是否有图
            if ($images) {
                $info = $url . preg_replace('/^..\//', '', $images['pho']['savepath']) . $images['pho']['savename'];//拼接图片地址
                echo json_encode($info);
            } else {
                $a = $upload->getError();//获取失败信息
                echo json_encode($a);
            }
        } else {
            return 2;
        }
    }

    /*
     * 获取地图坐标
     * author:李胜辉
     * time:2018/10/12 11:56
     */
    public function mapcontent()
    {
        $this->display();
        Log::record('显示数据', Log::DEBUG);
    }

}