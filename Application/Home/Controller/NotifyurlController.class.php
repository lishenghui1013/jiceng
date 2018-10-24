<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/9
 * Time: 17:56
 */

namespace Home\Controller;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Think\Controller;
use Home\Api\Wxpay;


class NotifyurlController extends BaseController
{
    /**
     *支付宝支付回调
     */
    public function index()
    {

        vendor('alipay.AopSdk');// 加载类库
        $aop = new \AopClient;
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAheyVuwYyZy3UaLSUsgX5ZpcqgG4D9M6YvyBq/RlnZpWYUmIXFGKjUEgX7VkttbDgWB0q3hyPQydJED4WkcVx96FXYq/RALIbJ21+fd35ibBaeUsKQcKxEwBUK2Bs2bwR/G3x4T19yTLkELU0Zgi4eUe/+6ELu/wQEet7Aj5DrI1pbtSKi64LZDxoXAHePK3CtzFcDoNcCE1FH0C5GvyQO5oYna2VSZFX55wDw559EMpHXxRGPs8TR9VRqCuxWMZrBt/tYj57zOU81i3U9IwvijwxuVo45kr4fN/3MU5qEUEQwRcdtAuIHsfJ0dR1bOOk8OeWZ9pg/da/oR6iUUAkzQIDAQAB';
        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
        if ($flag) {
            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $out_trade_no = $_POST['out_trade_no'];    //商户订单号
                $trade_status = $_POST['trade_status'];    //支付状态
                $data['paystate'] = 1;
                $have = D('api_charge')->where(array('order' => $out_trade_no, 'paystate' => 0))->find();
                $res = D('api_charge')->where(array('order' => $out_trade_no))->save($data);


                if ($res) {
                    if($have){
                        $list = D('api_charge')->where(array('order' => $out_trade_no, 'paystate' => 1))->find();
                        $user = D('api_users')->where(array('id' => $list['userid']))->find();
                        $data1['account'] = floatval(round($user['account'], 2) + round($list['charge'], 2));
                        $res = D('api_users')->where(array('id' => $list['userid']))->save($data1);//修改用户账号金币
                        echo $res?'success':'fail';
                    }else{
                        echo 'fail';
                    }

                } else {

                    echo 'fail';
                }

            }
        } else {
            echo 'fail';
        }


    }

    /**
     *微信支付回调
     */
    public function notify()
    {
        //接受微信返回的数据,返回的xml格式
        $xml = file_get_contents('php://input');
        //将xml格式转为数组
        $wxpay = new Wxpay();
        $arr = $wxpay->xmlToArray($xml);
        ApiLog::setApiInfo($arr);
        //为了防止假数据,验证签名是否和返回的一样.
        //记录一下,返回回来的签名,生成签名的时候,必须剔除sign字段
        $sign = $arr['sign'];
        unset($arr['sign']);

        if ($sign == $wxpay->getSign($arr)) {
            //根据返回的订单号做业务逻辑
            if ($arr['result_code'] == 'SUCCESS') {

//            校验返回的订单金额是否与商户侧的订单金额一致。修改订单表中的支付状态。
                $out_trade_no = $arr['out_trade_no'];    //商户订单号
                ApiLog::setApiInfo($out_trade_no);
                $data['paystate'] = 1;
                $have = D('api_charge')->where(array('order' => $out_trade_no, 'paystate' => 0))->find();
                $res = D('api_charge')->where(array('order' => $out_trade_no,'paystate'=>0))->save($data);

                if ($res) {
                    if($have){
                        $list = D('api_charge')->where(array('order' => $out_trade_no, 'paystate' => 1))->find();
                        $user = D('api_users')->where(array('id' => $list['userid']))->find();
                        $data1['account'] = floatval(round($user['account'], 2) + round($list['charge'], 2));
                        $ress = D('api_users')->where(array('id' => $list['userid']))->save($data1);//修改用户账号金币

                        echo $ress ? 'success' : 'fail';
                    }else{
                        echo 'fail';
                    }

                } else {

                    echo 'fail';
                }
            }else{
                echo 'fail';
            }
        } else {
            echo 'fail';
        }

    }

    // 接收post数据
    /*
    *  微信是用$GLOBALS['HTTP_RAW_POST_DATA'];这个函数接收post数据的
    */
    public function postdata()
    {
        $receipt = $_REQUEST;
        if ($receipt == null) {
            $receipt = file_get_contents("php://input");
            if ($receipt == null) {
                $receipt = $GLOBALS['HTTP_RAW_POST_DATA'];
            }
        }
        return $receipt;
    }

    //把对象转成数组
    public function object2array($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = object2array($value);
            }
        }
        return $array;
    }





}


