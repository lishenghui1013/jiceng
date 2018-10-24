<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/1
 * Time: 14:03
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class Login extends Base
{
    /**
     *登陆
     */
    public function denglu($param)
    {
        $tel = $param['tel'];
        $pwsd = md5($param['pswd']);
        Response::debug($tel . '+' . $param['pswd'] . '+' . $pwsd);
        $user1 = M();
        $sel = $user1->query("select * from api_users where phone='$tel' ");
        $select = $user1->query("select * from api_users where phone='$tel' and pwd='$pwsd'");

        if (!$sel) {
            return array('cstate' => 'no');//此用户需要注册账号
        } elseif (!$select) {
            return array('cstate' => 'uperror');//账号或密码错误
        } else {
            $listInfo = D('api_users')->field('id,phone,account,headphoto,username')->where(array('phone' => $tel))->select();
            $listInfo['cstate'] = $listInfo ? 'success' : 'fail';//success:登录成功;fail:登录失败
            //Response::debug($listInfo);
            return $listInfo;
        }
    }

    public function yanzhengLogin($param)
    {
        $tel = $param['tel'];
        $yanzhengma = $param['identifyingcode'];//用户输入的验证码
        $yz = $param['codes'];//发送的验证码
        Response::debug($tel . '+' . $yanzhengma . '+' . $yz);
        if (empty($yanzhengma) && empty($yz)) {
            return array('codestate' => 'null');//请填写验证码
        } elseif ($yanzhengma == $yz) {
            $user1 = M();
            $sel = $user1->query("select * from api_users where phone='$tel'");
            if (!$sel) {
                return array('codestate' => 'nohave');//此用户需要注册账号
            } else {
                $listInfo = D('api_users')->field('id,phone,account,headphoto,username')->where(array('phone' => $tel))->select();

                Response::debug($listInfo);

                $listInfo['codestate'] = $listInfo ? 'success' : 'fail';//success:登录成功;fail:登录失败
                return $listInfo;
            }
        } elseif ($yanzhengma != $yz) {
            return array('codestate' => 'error');//验证码输入不正确
        }

    }

    /**
     *用户忘记密码
     */
    public function forget($param)
    {

        $tel = $param['tel'];//手机号
        $yanzhengma = $param['identifyingcode'];//用户输入的验证码
        $yz = $param['codes'];//发送的验证码
        $pwsd = md5($param['pwsd']); //密码
        $preg = '/^1\d{10}$/';
        $preg_pass = '/^[\da-zA-Z]{6,20}$/';
        if ($tel == '' ||! preg_match($preg, $tel)) {
            return array('cstate' => 'pherror');//手机号格式不正确
        }
        if ($yanzhengma == '' || $yanzhengma != $yz) {
            return array('cstate' => 'cerror');//验证码不正确
        }
        if ($param['pwsd'] == '' || !preg_match($preg_pass, $param['pwsd'])) {
            return array('cstate' => 'pserror');//密码格式不正确
        }

        Response::debug($tel . '+' . $param['pwsd'] . '+' . $pwsd);
        $user1 = M();
        $select = $user1->query("select id from api_users where phone='$tel'");

        if (!$select) {
            return array('cstate' => 'no');//此用户不存在
        }
        $data['pwd'] = $pwsd;
        $res = D('api_users')->where(array('phone' => $tel))->save($data);
        if ($res == false) {
            return array('cstate' => "fail");//修改失败
        } else {
            $listInfo = D('api_users')->field('id,phone,account,headphoto,username')->where(array('phone' => $tel))->select();
            $listInfo['cstate'] = 'success';//修改成功
            Response::debug($listInfo);
            return $listInfo;
        }


    }
}