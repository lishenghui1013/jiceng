<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/21
 * Time: 10:06
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class UpdateMy extends Base
{
    /**
     *修改用户头像
     */
    public function UpdateImg($param)
    {

        $uid = $param['userid'];
        Response::debug($uid);
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
                $info = $url . preg_replace('/^..\//', '', $images['img']['savepath']) . $images['img']['savename'];//拼接图片地址
                $data['headphoto'] = $info;
                Response::debug($info);
                $res = D('api_users')->where(array('id' => $uid))->save($data);
                Response::debug($res);
                if ($res === false) {
                    return array('state' => "fail");
                } else {
                    return array('state' => "succeess",'url'=>$info);

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
     *修改用户昵称
     */
    public function UpdateName($param)
    {
        $uid = $param['userid'];
        $name = $param['name'];
        if($name==''){
            return array('cstate' => "nameno");//昵称不能为空
        }
        $data['username'] = $name;
        $res = D('api_users')->where(array('id' => $uid))->save($data);
        if ($res === false) {
            return array('cstate' => "fail");//修改失败
        } else {
            return array('cstate' => "success");//修改成功
        }
    }

    /**
     *修改用户密码
     */
    public function UpdatePwsd($param)
    {
        $uid = $param['userid'];//用户编号
        $yuan = md5($param['yuan']);//原密码
        $Pwsd = md5($param['Pwsd']);//新密码
        $queren = md5($param['quren']);//确认密码
        $preg_pass = '/^[\da-zA-Z]{6,20}$/';
        if ($uid == '') {
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
        $select = $user1->query("select id from api_users where id='$uid' and pwd='$yuan'");
        if (!$select) {
            return array('cstate' => 'olderror');//原密码输入错误
        }
        $data['pwd'] = $Pwsd;
        $res = D('api_users')->where(array('id' => $uid))->save($data);
        if ($res === false) {
            return array('cstate' => "fail");//修改失败
        } else {
            return array('cstate' => "success");//修改成功
        }

    }

    /**
     *充值记录
     */
    public function charge($param)
    {
        $uid = $param['userid'];
        $listInfo = D('api_charge')->where('userid="' . $uid . '" and ' . 'paystate=1')->select();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
    }

    /**
     * 查询我余额信息
     * User: 李胜辉
     * Date: 2018/10/08
     * Time: 18:52
     */
    public function MyAccount($param)
    {
        $uid = $param['userid'];//用户id
        $account = D('api_users')->where('id="' . $uid . '"')->getField('account');//查询我的余额
        ApiLog::setApiInfo($account);

        return $account;
    }
}