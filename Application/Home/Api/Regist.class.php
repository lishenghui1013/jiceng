<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/1
 * Time: 14:05
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
use Home\Api\CCPRestSDK;

include_once("CCPRestSDK.class.php"); //说明：需要包含接口声明文件，可将该文件拷贝到自己的程序组织目录下。

class Regist extends Base
{

    /**
     *注册
     * 修改:李胜辉
     * time:2018/10/11 09:45
     */
    public function zhuce($param)
    {
        //$tel='18704215258';//手机号
        //$yanzhengma='1233';//用户输入的验证码
        //  $yz='1234';//发送的验证码
        // $pwsd=md5($param['pwsd']='123');//密码
        // $invite='';//邀请手机号

        // Response::debug("手机号：".$tel."+用户输入的验证码:".$yanzhengma."+发送的验证码:".$yz."+密码：".$pwsd."+邀请人：".$invite);
        $tel = $param['tel'];//手机号
        $yanzhengma = $param['identifyingcode'];//用户输入的验证码
        $yz = $param['codes'];//发送的验证码
        $pwsd = md5($param['pwsd']);//密码
        $invite = $param['invitetel'] ? $param['invitetel'] : '';//邀请手机号
        //生成随机昵称
        $arr_rand = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'g', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $nickname = '';
        for ($i = 0; $i < 10; $i++) {
            $index = mt_rand(0, 61);
            $nickname .= $arr_rand[$index];

        }
        //获取网站根目录地址$url
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $str = substr($PHP_SELF, 1);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($str, 0, strpos($str, '/') + 1);
        $preg = '/^1\d{10}$/';
        $preg_pass = '/^[\da-zA-Z]{6,20}$/';
        if (empty($tel) || !preg_match($preg, $tel)) {
            return array('cstate' => "phno");//手机号格式不正确
        }
        if (empty($param['pwsd']) || !preg_match($preg_pass, $param['pwsd'])) {
            return array('cstate' => "psno");//密码格式不正确
        }
        if ($invite != '') {
            if (!preg_match($preg, $invite)) {
                return array('cstate' => "plerror");//邀请手机号格式不正确
            }
        }


        if ($invite == $tel) {
            return array('cstate' => "plserror");//邀请手机号不能与注册号相同
        }
        //判断用户是否已经注册了
        $user1 = M();
        //$sel = $user1->query("select * from api_users where phone='$tel'");
        $select = $user1->query("select id from api_users where phone='$tel'");
        if ($select) {//如果存在该用户
            return array('cstate' => "have");//此用户已存在
        }

        $arr_phone = D('api_users')->getField('phone', true);//查询所有用户的手机号
        if ($arr_phone) {
            if ($invite != '' && !in_array($invite, $arr_phone)) {
                return array('cstate' => "plno");//邀请手机号还不是会员
            }
        }

        $obj = array(
            "phone" => $tel,
            "pwd" => $pwsd,
            "tuijianTel" => $invite,
            'account' => 0.00,
            'username' => $nickname,
            "headphoto" => $url . 'Public/uploads/user/mrtx.jpg'
        );
        if ($yanzhengma != '' && $yz != '') {
            if ($yanzhengma == $yz) {
                $insert = M('api_users')->add($obj);
                if ($insert === false) {
                    return array('cstate' => "fail");//注册失败
                } else {
                    return array('cstate' => 'success', 'phone' => $tel);//注册成功
                }
            }else{
                return array('cstate' => "coderror");//验证码不正确
            }

        } elseif ($yanzhengma == '' && $yz != '') {
            return array('cstate' => "codeno");//验证码不能为空
        } else{
            return array('cstate' => "getcode");//请获取验证码
        }

    }


    /**
     * 验证码d
     */
    public function verificationcode($param)
    {
        $tel = $param['tel'];
        Response::debug($tel);
        //随机生成一个4位数的数字验证码
        $num = "";
        for ($i = 0; $i < 4; $i++) {
            $num .= rand(0, 9);
        }
        Response::debug('1：' . $num);
        //随机生成一个4位数的数字验证码
        vendor('CCPRestSmsSDK', '', 'CCPRestSmsSDK.class.php');
        // 初始化REST SDK
        // global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
        $accountSid = '8a216da857f4d3ec0157fffec9bd08a1'; //说明：主账号，登陆云通讯网站后，可在控制台首页看到开发者主账号ACCOUNT SID。
        $accountToken = '80e8a6492b91449db80e9428fb7827b7'; //说明：主账号Token，登陆云通讯网站后，可在控制台首页看到开发者主账号AUTH TOKEN。
        $appId = '8a216da861d4f5d90161e040497b0589';  //说明：请使用管理控制台中已创建应用的APPID。
        $serverIP = 'app.cloopen.com'; //说明：生产环境请求地址：app.cloopen.com。
        $serverPort = '8883';  //说明：请求端口 ，无论生产环境还是沙盒环境都为8883.
        $softVersion = '2013-12-26';//说明：REST API版本号保持不变。
        $rest = new \CCPRestSDK($serverIP, $serverPort, $softVersion);
        $rest->setAccount($accountSid, $accountToken);
        $rest->setAppId($appId);


        // 发送模板短信
        //echo "Sending TemplateSMS to $tel";
        $result = $rest->sendTemplateSMS($tel, array($num, '1分钟'), "236465");

        if ($result == NULL) {
            echo "result error!";

        }
        if ($result->statusCode != 0) {
            //echo "模板短信发送失败!";
            //echo "error code :" . $result->statusCode . "";
            // echo "error msg :" . $result->statusMsg . "";
            Response::debug("error code :" . $result->statusCode . "++++++++" . "error msg :" . $result->statusMsg . "");
            //下面可以自己添加错误处理逻辑
        } else {
            // echo "模板短信发送成功!";
            // 获取返回信息
            // $smsmessage = $result->TemplateSMS;
            // echo "dateCreated:".$smsmessage->dateCreated."";
            // echo "smsMessageSid:".$smsmessage->smsMessageSid."";
            //下面可以自己添加成功处理逻辑
            Response::debug('2：' . $num);
            return array('yanzhengma' => $num);
        }

    }
}