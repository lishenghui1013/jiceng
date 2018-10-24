<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/21
 * Time: 14:35
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class UpdateDoc extends Base
{
    /**
     *修改头像
     */


    public function UpdateImg($param)
    {

        $did = $param['did'] = '1';//医生编号
        Response::debug($did);
        /*         if (!empty($_FILES)) {
                    $upload = new \Think\Upload();   // 实例化上传类
                    $upload->maxSize   =     3145728 ;    // 设置附件上传大小
                    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
                    $upload->rootPath  =     THINK_PATH;          // 设置附件上传根目录
                    $upload->savePath  =     '../Public/';    // 设置附件上传（子）目录
                    $upload->subName   =     'uploads/updateimg/';  //子文件夹
                    $upload->saveName  =     date('Ymdhis');     //文件名
                    $upload->replace   =     true;  //同名文件是否覆盖
                    // 上传文件
                    $images   =   $upload->upload();
                    //return $images;
                    //判断是否有图
                    if($images){
                        $info= $images['img']['savepath'].$images['img']['savename'];
                        $data['pic']=$info;
                        $res = D('api_doctor')->where(array('id' => $did))->save($data);
                        if( $res === false ){
                            return array('state' => "fail");
                        }else{
                            return array('state' => "succeess");
                        }
                    }
                    else{
                        $a=$upload->getError();//获取失败信息
                        echo json_encode($a);
                    }
                }
                else
                {
                    return array('state' => "fail");
                } */
    }

    public function img($param)
    {

        $did = $param['did'];//医生编号
        Response::debug($did);
        //获取网站根目录地址$url
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $str = substr($PHP_SELF, 1);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($str, 0, strpos($str, '/') + 1);
        Response::debug($url);
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();   // 实例化上传类
            $upload->maxSize = 3145728;    // 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->rootPath = THINK_PATH;          // 设置附件上传根目录
            $upload->savePath = '../Public/';    // 设置附件上传（子）目录
            $upload->subName = 'uploads/updateimg/';  //子文件夹
            $upload->saveName = date('Ymdhis');     //文件名
            $upload->replace = true;  //同名文件是否覆盖

            // 上传文件
            $images = $upload->upload();
            //return $images;
            //判断是否有图
            if ($images) {
                $info = $url . preg_replace('/^..\//', '', $images['img']['savepath']) . $images['img']['savename'];//拼接图片url地址
                $data['pic'] = $info;
                Response::debug($info);
                $res = D('api_doctor')->where(array('id' => $did))->save($data);
                if ($res === false) {
                    return array('state' => "fail");
                } else {
                    return array('state' => "succeess");
                }
            } else {
                $a = $upload->getError();//获取失败信息
                echo json_encode($a);
            }
        } else {
            return array('state' => "fail");
        }
    }

    /**
     *修改医生名称
     */
    public function UpdateName($param)
    {
        $did = $param['userid'];
        $name = $param['name'];
        if($name==''){
            return array('cstate' => "nameno");//昵称不能为空
        }
        $data['doctorname'] = $name;
        $res = D('api_doctor')->where(array('id' => $did))->save($data);
        if ($res === false) {
            return array('cstate' => "fail");//修改成功
        } else {
            return array('cstate' => "success");//修改失败
        }
    }

    /**
     *修改医生密码
     */
    public function UpdatePwsd($param)
    {
        $did = $param['did'];//医生编号
        $yuan = md5($param['yuan']);//原密码
        $Pwsd = md5($param['Pwsd']);//新密码
        $queren = md5($param['quren']);//确认密码
        $preg_pass = '/^[\da-zA-Z]{6,20}$/';
        if ($did == '') {
            return array('cstate' => 'uno');//请登录
        }
        if ($yuan == '') {
            return array('cstate' => 'oldno');//原密码不能为空
        }
        if ($param['Pwsd'] == '' || !preg_match($preg_pass, $param['Pwsd'])) {
            return array('cstate' => 'pserror');//新密码格式不正确
        }
        if ($Pwsd != $queren) {
            return array('cstate' => 'diff');//两次输入密码不一致
        }
        $user1 = M();
        $select = $user1->query("select * from api_doctor where id='$did' and pasd='$yuan'");
        if (!$select) {
            return array('cstate' => 'olderror');//原密码输入错误
        }
        $data['pasd'] = $Pwsd;
        $res = D('api_doctor')->where(array('id' => $did))->save($data);
        if ($res === false) {
            return array('cstate' => "fail");//修改失败
        } else {
            return array('cstate' => "succeess");//修改成功
        }


    }

    /**
     *医生忘记密码
     */
    public function forget($param)
    {

        $tel = $param['tel'];//手机号
        $yanzhengma = $param['identifyingcode'];//用户输入的验证码
        $yz = $param['codes'];//发送的验证码
        $pwsd = md5($param['pswd']); //密码
        $preg = '/^1\d{10}$/';
        $preg_pass = '/^[\da-zA-Z]{6,20}$/';
        if ($tel == '' || !preg_match($preg, $tel)) {
            return array('cstate' => 'pherror');//手机号格式不正确
        }
        if ($yanzhengma == '' || $yanzhengma != $yz) {
            return array('cstate' => 'cerror');//验证码不正确
        }
        if ($param['pswd'] == '' || !preg_match($preg_pass, $param['pswd'])) {
            return array('cstate' => 'pserror');//密码格式不正确
        }

        $user1 = M();
        $select = $user1->query("select id from api_doctor where phone='$tel'");
        if (!$select) {
            return array('cstate' => 'no');//此用户不存在
        }

        $data['pasd'] = $pwsd;
        $res = D('api_doctor')->where(array('phone' => $tel))->save($data);
        if ($res === false) {
            return array('cstate' => "fail");//修改失败
        } else {
            $listInfo = D('api_doctor')->field('id,pic,doctorname')->where(array('phone' => $tel))->select();
            Response::debug($listInfo);
            $listInfo['cstate'] = 'success';//修改成功
            return $listInfo;
        }


    }

}