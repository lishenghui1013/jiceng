<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/2/3
 * Time: 11:47
 */
namespace  Admin\Controller;
use Think\Upload;
use Think\Log;
class CliDepartController extends BaseController
{
    /**
     * 获取诊所菜单列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */

    public function index() {
        $list = D('api_hosdepartrelation as a')->join('api_hospital as h on a.hid=h.id')->join('api_department as d on d.id=a.did')->order("h.hospitalname")->field("a.*,h.hospitalname,d.departmentname")->select();
        $this->assign('list', $list);
        $this->display();
        Log::record('显示数据', Log::DEBUG);
    }

    /**
     * 添加诊所科室信息
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $data['time']= date("Y-m-d H:i:s");
            $res = D('api_hosdepartrelation')->add($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        } else {
            $hospitalList = D('api_hospital')->order("hospitalname")->select();
            $departmentList = D('api_department')->select();
            $this->assign('hospitalList', $hospitalList);
            $this->assign('departmentList', $departmentList);
            $this->display();

        }
    }
    /**
     * 编辑诊所科室信息
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        if (IS_GET) {

            $departmentList = D('api_department')->select();//根据排序字段升序，数字越大越靠后
             //$departmentList = D('api_hosdepartrelation as a')->join('api_department as d on d.id=a.did')->field("a.*,d.departmentname")->select();
            $hospitalList = D('api_hospital')->select();
            //$hospitalList = D('api_hosdepartrelation as a')->join('api_hospital as h on a.hid=h.id')->field("a.*,h.hospitalname")->select();
            $id = I('get.id');
            $typelist  = D('api_hosdepartrelation')->where(array('id' => $id))->find();
            $this->assign('detail', $typelist);
           $this->assign('hospitalList',  $hospitalList);
           $this->assign('departmentList', $departmentList);

            $this->display('add');
            //echo $id;
            //echo var_dump ($typelist);
        } elseif (IS_POST) {
            Log::record('编辑诊所科室', Log::DEBUG);
            $postData = I('post.');
            $res = D('api_hosdepartrelation')->where(array('id' => $postData['id']))->save($postData);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('编辑成功');
            }
        }
    }
    /**
     * 删除诊所科室信息
     */
    public function del() {
        $id = I('post.id');
        $res = D('api_hosdepartrelation')->where(array('id' => $id))->delete();
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('操作成功');
        }
    }

}