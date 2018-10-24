<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/28
 * Time: 10:39
 */
namespace  Admin\Controller;
use Think\log;
class AppversionController extends BaseController{
    /**
     * 获取菜单列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {

        $listInfo = D('api_appversion')->select();
        $this->assign('list', $listInfo);
        $this->display();
    }

    /**
     * 添加科室
     */
    public function add() {
        if( IS_POST ){
            $data = I('post.');
            $data['createtime'] =date("Y-m-d H:i:s").time();
            $data['app_type'] = 2;
            $res = D('api_appversion')->add($data);
            if( $res === false ){
                $this->ajaxError('操作失败');
            }else{
                $this->ajaxSuccess('添加成功');
            }
        }else{
            $this->display();
        }
    }

    /**
     * 编辑科室
     */
    public function edit(){
        Log::record('编辑科室', Log::DEBUG);
        if( IS_GET ){
            $id = I('get.id');
            $details=D('api_appversion')->where("id='$id'")->find();
            $this->assign('detail', $details);
            $this->display('add');
            //echo var_dump ($details);
        }elseif( IS_POST ){

            $data = I('post.');
            $res = D('api_appversion')->where(array('id' => $data['id']))->save($data);
            if( $res === false ){
                $this->ajaxError('操作失败');
            }else{
                $this->ajaxSuccess('添加成功');
            }
        }
    }
    /**
     * 删除科室
     */
    public function del() {
        $id = I('post.id');
        $res = D('api_appversion')->where(array('id' => $id))->delete();
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('操作成功');
        }
    }
    //图片上传
    public function upload(){
        //获取网站根目录$url
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $str = substr($PHP_SELF, 1);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($str, 0, strpos($str, '/') + 1);
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();   // 实例化上传类
            $upload->maxSize   =     0 ;    // 设置附件上传大小
            $upload->exts      =     array("apk"); // 设置附件上传类型
            $upload->rootPath  =     THINK_PATH;          // 设置附件上传根目录
            $upload->savePath  =     '../Public/';    // 设置附件上传（子）目录
            $upload->subName   =     'uploads/apk/';  //子文件夹
            $upload->saveName  =     date('Ymdhis');     //文件名
            $upload->replace   =     true;  //同名文件是否覆盖
            // 上传文件
            $images   =   $upload->upload();
            //return $images;
            //判断是否有图
            if($images){
                $info= $url . preg_replace('/^..\//','',$images['pho']['savepath']) . $images['pho']['savename'];//拼接图片地址
                echo json_encode($info);
            }
            else{
                $a=$upload->getError();//获取失败信息
                echo json_encode($a);
            }
        }else{
            return 2;
        }
    }


}