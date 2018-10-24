<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/13
 * Time: 14:38
 */
namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
class Wxpay extends Base{
    /*
           一、配置参数
     */
    private    $config = array(
        'appid' => "wxdcb394ff6b8781e6",    /*微信开放平台上的应用id*/
        'mch_id' => "1501526171",   /*微信申请成功之后邮件中的商户id*/
        'api_key' => "oskeql73avbzxkwtost51vqg5hpv6qku",    /*在微信商户平台上自己设定的api密钥 32位*/
        'notify_url' => 'http://60.205.111.111:8087/jiceng/Notifyurl/notify' /*自定义的回调程序地址*/
    );


    //下单
    public function getPrePayOrder($param)
    {
        $order=date("YmdHis").time().mt_rand(1000,9999);//订单号
        $price=$param['price'];//价格
        $uid=$param['userid'];//用户编号
        //Response::debug($price.'+'.$uid);
        //添加账单
        $obj = array(
            "userid" => $uid,
            "charge" =>$price,
            'paytime'=>time(),
            "paytype"=>'微信',
            "paystate"=>0,
            "order"=>$order
        );
        $insert=M('api_charge')->add($obj);
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $data["appid"] = $this->config["appid"];//APPID
        $data["body"] = 'buy';                    //付款商品
        $data["mch_id"] = $this->config['mch_id'];//商户号
        $data["nonce_str"] = $this->createNoncestr();//随机字符串
        $data["notify_url"] = $this->config["notify_url"];//回调地址
        $data["out_trade_no"] = $order;              //订单号
        $data["spbill_create_ip"] = $this->get_client_ip();//IP地址

        //echo $this->get_client_ip();
        $data["total_fee"] = intval($price* 100);                 //价格
        $data["trade_type"] = "APP";                //交易类型
        $sign = $this->getSign($data);

        $data["sign"] = $sign;

        $xml = $this->arrayToXml($data);
        $response = $this->postXmlCurl($xml, $url);
        //echo $xml;
        //将微信返回的结果xml转成数组
        $response = $this->xmlToArray($response);
        //返回数据
        $prepay_id= $response['prepay_id'];


        $sign_array= array();
        $sign_array['appid']=  $this->config["appid"]; //注意 $sign_array['appid'] 里的参数名必须是appid

        $sign_array['partnerid'] = $this->config['mch_id'];//注意 $sign_array['partnerid'] 里的参数名必须是partnerid

        $sign_array['prepayid']= $prepay_id;//注意 $sign_array['prepayid'] 里的参数名必须是prepayid

        $sign_array['package']= 'Sign=WXPay';//注意 $sign_array['package'] 里的参数名必须是package

        $sign_array['noncestr']= $this->createNoncestr();//注意 $sign_array['noncestr'] 里的参数名必须是noncestr

        $sign_array['timestamp'] = time();//注意 $sign_array['timestamp'] 里的参数名必须是timestamp

        $sign_two = $this->getSign($sign_array);//调用wechatAppPay类里的MakeSign()函数生成sign
        $sign_array['sign'] = $sign_two;


        $xml2 = $this->arrayToXml($sign_array);

//将微信返回的结果xml转成数组

        $response3 = $this->xmlToArray($xml2);
        return $response3;


    }

    /**
     * 生成签名
     */
    public function getSign($Obj){

        foreach ($Obj as $k => $v){
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo $String;
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".$this->config['api_key'];
        // echo $String;
        //签名步骤三：MD5加密
        $String = md5($String);
        // echo $String;
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo $result_;
        return $result_;
    }


    /**
     *  作用：产生随机字符串，不长于32位
     */
    public function createNoncestr( $length = 32 ){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }


    //数组转xml
    public function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }


    /**
     *  作用：将xml转为array
     */
    public function xmlToArray($xml){
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }


    /**
     *  作用：以post方式提交xml到对应的接口url
     */
    public function postXmlCurl($xml,$url,$second=30)
    {
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果

        if($data){
            curl_close($ch);
            return $data;
        }else{
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            curl_close($ch);
            return false;
        }
    }


    //微信支付 - 获取当前服务器的IP
    public function get_client_ip(){
        if ($_SERVER['REMOTE_ADDR']) {
            $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
            $cip = getenv("REMOTE_ADDR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $cip = getenv("HTTP_CLIENT_IP");
        } else {
            $cip = "unknown";
        }
        return $cip;
    }


    /**
     *  作用：格式化参数，签名过程需要使用
     */
    public function formatBizQueryParaMap($paraMap, $urlencode){
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v){
            if($urlencode){
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0){
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }
}