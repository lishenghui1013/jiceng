<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/9
 * Time: 17:56
 */
namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
use Home\Model\TclassModel;
class Notify_url extends Base{
    /**
     *支付宝支付回调
     */
    public function huidiao()
    {
		 vendor('alipay.AopSdk');// 加载类库
        $aop = new \AopClient;
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAheyVuwYyZy3UaLSUsgX5ZpcqgG4D9M6YvyBq/RlnZpWYUmIXFGKjUEgX7VkttbDgWB0q3hyPQydJED4WkcVx96FXYq/RALIbJ21+fd35ibBaeUsKQcKxEwBUK2Bs2bwR/G3x4T19yTLkELU0Zgi4eUe/+6ELu/wQEet7Aj5DrI1pbtSKi64LZDxoXAHePK3CtzFcDoNcCE1FH0C5GvyQO5oYna2VSZFX55wDw559EMpHXxRGPs8TR9VRqCuxWMZrBt/tYj57zOU81i3U9IwvijwxuVo45kr4fN/3MU5qEUEQwRcdtAuIHsfJ0dR1bOOk8OeWZ9pg/da/oR6iUUAkzQIDAQAB';
        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
        if ($flag) {
            if ($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS')
            {
                $out_trade_no = $_POST['out_trade_no'];    //商户订单号
                $trade_status = $_POST['trade_status'];    //商户订单号
                ApiLog::setApiInfo("订单号：".$out_trade_no);
                ApiLog::setApiInfo("支付状态：".$trade_status);
                if($trade_status != 'TRADE_FINISHED' && $trade_status != 'TRADE_SUCCESS')
                {
                    $data['paystate']=1;
                    $res = D('api_charge')->where(array('order' => $out_trade_no))->save($data);
                    if( $res === false ){
                        echo 'fail';
                    }else{
                        echo 'succeess';
                    }
                }
            }
        }

    }
}


