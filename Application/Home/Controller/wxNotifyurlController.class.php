<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/4/8
 * Time: 10:10
 */
namespace Home\Controller;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Think\Controller;


class wxNotifyurlController extends BaseController  {
    /**
     *微信支付回调
     */
    public function index()
    {
        $xml = file_get_contents('php://input');
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		 ApiLog::setApiInfo($this->$arr);

        //用户http_build_query()将数据转成URL键值对形式
        $sign = http_build_query($arr);
        //md5处理
        $sign = md5($sign);
        //转大写
        $sign = strtoupper($sign);
        //验签名。默认支持MD5
        if ( $sign === $arr['sign']) {
//            校验返回的订单金额是否与商户侧的订单金额一致。修改订单表中的支付状态。
            $out_trade_no = $arr['out_trade_no'];    //商户订单号
            ApiLog::setApiInfo($this->$out_trade_no);
            $data['paystate']=1;
            $res = D('api_charge')->where(array('order' => $out_trade_no))->save($data);
            if( $res === false ){
                echo 'fail';
            }else{
                echo 'success';
            }
        }
       // $return = ['return_code'=>'SUCCESS','return_msg'=>'OK'];
       // $xml = '<xml>';
       // foreach($return as $k=>$v){
       //     $xml.='<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
       // }
       // $xml.='</xml>';

       // echo $xml;

    }

}


