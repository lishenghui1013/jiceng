<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/2/7
 * Time: 14:33
 */

namespace Admin\Controller;

use Think\log;

class DoctorController extends BaseController
{
    /**
     * 获取菜单列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index()
    {

        $listInfo = D('api_doctor as d')->join('api_hospital as y on y.id=d.hospital')->join('api_hosdepartrelation    as g  on g.hid=y.id')->field('d.*,hospitalname,dname')->order('d.paixu asc')->select();//根据排序字段升序，数字越大越靠后
        $this->assign('list', $listInfo);
        $this->display();
    }

    /**
     * 添加医生
     */
    public function add()
    {
        //获取网站根目录$url
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $str = substr($PHP_SELF, 1);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($str, 0, strpos($str, '/') + 1);
        if (IS_POST) {
            $data = I('post.');
            //如果没有上传图片,给一个默认图片url地址
            if ($data['pic'] == '') {
                $data['pic'] = $url . 'Public/uploads/doctor/mrtx.jpg';
            }
            $data['pasd'] = md5('123456');
            $res = D('api_doctor')->add($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        } else {
            $data = I('post.');
            $hospitalList = D('api_hospital')->order("hospitalname")->select();
            //$departmentList = D('api_department')->select();
            $departmentList = D('api_hosdepartrelation as a')->join('api_department as d on d.id=a.did')->field("a.*,d.departmentname")->select();

            $this->assign('hospitalList', $hospitalList);
            $this->assign('departmentList', $departmentList);
            $this->display();

        }
    }

    /*
     科室ajax
    */
    public function ajaxdepartment()
    {
        $hospital = I('post.hospital');
        $dp = D('api_hosdepartrelation')->where("hid='$hospital'")->select();
        echo json_encode($dp);
    }

    /**
     * 编辑医生
     */
    public function edit()
    {
        if (IS_GET) {
            $hospitalList = D('api_hospital')->select();
            $id = I('get.id');
            $typelist = D('api_doctor')->where(array('id' => $id))->find();
            $hid = $typelist['hospital'];
            $dp = D('api_hosdepartrelation')->where("hid='$hid'")->select();
            $this->assign('dp', $dp);
            $this->assign('detail', $typelist);
            $this->assign('hospitalList', $hospitalList);
            $this->display('add');
        } elseif (IS_POST) {
            $postData = I('post.');
            $res = D('api_doctor')->where(array('id' => $postData['id']))->save($postData);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('编辑成功');
            }
        }
    }

    /**
     * 删除科室
     */
    public function del()
    {
        $id = I('post.id');
        $res = D('api_doctor')->where(array('id' => $id))->delete();
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('操作成功');
        }
    }

    /**
     * 根据诊所传过来的id值查出科室
     */

    public function hospit()
    {
        //$id = I('post.hosid');
        $id = I('request.hosid');
        $departmentList = D('api_hosdepartrelation as a')->join('api_department as d on d.id=a.did')->field("a.*,d.departmentname")->where(array('hid' => $id))->select();
        echo json_encode($departmentList);

    }

    //图片上传
    public function upload()
    {
        //获取网站根目录$url
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $str = substr($PHP_SELF, 1);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($str, 0, strpos($str, '/') + 1);
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();   // 实例化上传类
            $upload->maxSize = 3145728;    // 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->rootPath = THINK_PATH;          // 设置附件上传根目录
            $upload->savePath = '../Public/';    // 设置附件上传（子）目录
            $upload->subName = 'uploads/doctor/';  //子文件夹
            $upload->saveName = date('Ymdhis');     //文件名
            $upload->replace = true;  //同名文件是否覆盖
            // 上传文件
            $images = $upload->upload();

            //return $images;
            //判断是否有图
            if ($images) {
                $info = $url . preg_replace('/^..\//','',$images['img']['savepath']) . $images['img']['savename'];//拼接图片地址
                echo json_encode($info);
            } else {
                $a = $upload->getError();//获取失败信息
                echo json_encode($a);
            }
        } else {
            return 2;
        }
    }
}
