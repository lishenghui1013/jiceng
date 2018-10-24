<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/24
 * Time: 9:25
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class DocLogin extends Base
{
    /**
     *登陆
     */
    public function denglu($param)
    {
        $tel = $param['tel'];
        $pwsd = md5($param['pswd']);
        // Response::debug($tel.$pwsd);
        $user1 = M();
        $sel = $user1->query("select id from api_doctor where phone='$tel' ");
        $select = $user1->query("select id from api_doctor where phone='$tel' and pasd='$pwsd'");
        if (!$sel) {
            return array('cstate' => 'no');//没有此账号
        }
        if (!$select) {
            return array('cstate' => 'uperror');//账号或密码错误
        }

        $listInfo = D('api_doctor')->field('id,pic,doctorname')->where(array('phone' => $tel))->select();
        // Response::debug($listInfo);
        $listInfo['cstate'] = $listInfo ? 'success' : 'fail';//success:登录成功;fail:登录失败
        return $listInfo;

    }

    public function yanzhengLogin($param)
    {
        $tel = $param['tel'];
        $yanzhengma = $param['identifyingcode'];//用户输入的验证码
        $yz = $param['codes'];//发送的验证码
        // Response::debug($tel.'+'.$yanzhengma.'+'.$yz);
        if (empty($yanzhengma) && empty($yz)) {
            return array('cstate' => 'null');//请填写验证码
        } elseif ($yanzhengma == $yz) {
            $user1 = M();
            $select = $user1->query("select * from api_doctor where phone='$tel'");
            if (!$select) {
                return array('cstate' => 'nohave');//此用户需要注册账号
            } else {
                $listInfo = D('api_doctor')->field('id,pic,doctorname')->where(array('phone' => $tel))->select();
                // Response::debug($listInfo);
                $listInfo['cstate'] = $listInfo?'success':'fail';//success:登录成功;fail:登录失败
                return $listInfo;
            }
        } elseif ($yanzhengma != $yz) {
            return array('cstate' => 'error');//验证码输入不正确
        }

    }
}