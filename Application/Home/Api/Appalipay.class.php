<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/9
 * Time: 17:49
 */
namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class Appalipay extends Base{
    /**
     *支付宝支付
     */
    public function pay($param) {
        vendor('alipay.AopSdk');// 加载类库
        $config = array(
            'appid' =>'2018032002414339',//商户密钥
            'rsaPrivateKey' =>'MIIEpQIBAAKCAQEAyvY7nagGZke/TE1Vj+/6WiePz2E02soBv4V8rzwSWz8NXZem1cqcueILPSgDBbB5OqUa+um9iUeqhOzjItdMFWD3ItVXLLVcTdX/PIaeZO3CBfyKEHIwwMLasJOb7P8ctmCPJVwbxQuXdeKaoJT/MAZY/pLNqUwRTp7AFb0VW5t71Tm7vLHrDkvQBL9f2hlUAt7GMJPPLOV3EZ2jWmBQk4dNxK+72D1+0DQq3D3smk93BOl4STaIxddLrSz13rvzON/SFGi+IBlHRtErrBcVeVyJ/nVvdWSFo4DWkXmlNKzyJmXKa6HhMrtYs1RjeLX0TBAtgAJVKJc4fbw65L9xowIDAQABAoIBAQCTTeT0xrdmUlpf+ZzI6+Yqup3gaG+g/44HP82/rPyxpvB/ZgqbDgNz0cBQcZLH9U33J0+OnkiGszHqabdsWRAKUCbt7CLp+vL0NwWZGycon3r7N0/JIeeKb6GjGG7JslpXb3tVJSWufHw//eg19g3EAEdk9I05e1DwW72TyXkP010JpNz8zQhzUrGBh4IFNxyUD13sSBgTPpcrHIr7Ch85Mh3Wi9erik9iv+T1mkPluil6WgarwD0Gc2RdGr8rB3ml6NdAhDI5bVB9uZ6DD9ui67EfgomjJeoA172z3J5SZtsqApxc9VW7Y4EOG9HNdciOZk7gRe6H+6GJrmAfFXthAoGBAO0godHlBi9MsihnpzntviNGFV2cf3ZUAKyFjCdtfHHFlOSgq59oJ80x6Rcjh2ZytiZyQwN2XAU3w7jKpKfi4/UbXu0ZTxs5ptSxn+iyXS/sMGgX00WdOqRXkM+gqU2zM5CNXtTttze03en461PcEqPcGVEhbnqavEV5m9J5W7rxAoGBANsdfcIlVaUuiIegZ3z8WeOW4X+5MGQsvO3YOKeSNNe9zz0Kn/9QLKCcTOXTW8zvUV74/QGDXv7mAHD9t4U4t6BZ+WeFoLkoQE7tFOhLR32mqmmgj2qx8pmAJ/zChqYkxZE8S276qM7qOiGvc/4WEkFqN4lAGucUKNd8JejSyC3TAoGBAK2e0/zr9LBTNqrw4fc/RJVtLh2hhY4tCWVITwtbVd+zrEYOAjswNtw+LA2eHPh9CzNxO/HgCpZjczHtZcvrC7+eb82oV0x21NksyQnG9wYsqHC+6RkyewpzdvsfBnd2u02exQ2glCL8kfNLJ3r6SsehUwQdN1gbzbgMx3O/GdPxAoGANi0YhwRMJMdYopAahmCuqQMJRlc3i80z+WrYtzYDMsSPlPwniyz7m8qJiNm1fPo/GEhf5hvhRQ0BVu6kjZ/0ZwVYESyNDLrTC7C61qSmTH8E65DzZOISpbl2KZy/Rh0ZbEuIKyx2yJJJRULoXw59/QaSLLFY9YGah4z+oo5bSFkCgYEAuPDv/VvEA4hqK0ZgLC76r2by1m8sNjnAbcOnCTHV4Q8BjjvjH3RMYXoPcLIPQi0KRLBrvSSv7J3HaiZ/AzFOoz3H6v7OW9VujluY/RV2iZ8s41b0stnkZEtZdCij674NcSbY862KGJt6AzAgHVRRT9OUm88cmYFyj0fi1xbZI4o=',//私钥
            'alipayrsaPublicKey'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyvY7nagGZke/TE1Vj+/6WiePz2E02soBv4V8rzwSWz8NXZem1cqcueILPSgDBbB5OqUa+um9iUeqhOzjItdMFWD3ItVXLLVcTdX/PIaeZO3CBfyKEHIwwMLasJOb7P8ctmCPJVwbxQuXdeKaoJT/MAZY/pLNqUwRTp7AFb0VW5t71Tm7vLHrDkvQBL9f2hlUAt7GMJPPLOV3EZ2jWmBQk4dNxK+72D1+0DQq3D3smk93BOl4STaIxddLrSz13rvzON/SFGi+IBlHRtErrBcVeVyJ/nVvdWSFo4DWkXmlNKzyJmXKa6HhMrtYs1RjeLX0TBAtgAJVKJc4fbw65L9xowIDAQAB',//公钥
            'charset'=>strtolower('utf-8'),//编码
            'notify_url' =>'http://60.205.111.111:8087/jiceng/Notifyurl/index',//回调地址(支付宝支付成功后回调修改订单状态的地址)
            'payment_type' =>1,//(固定值)
            'seller_id' =>'2088031565381518',//收款商家账号5abd9e4d92fef
            'charset'    => 'utf-8',//编码
            'sign_type' => 'RSA2',//签名方式
            'timestamp' =>date("Y-m-d H:i:s"),
            'version'   =>"1.0",//固定值
            'url'       => 'https://openapi.alipay.com/gateway.do',//固定值
            'method'    => 'alipay.trade.app.pay',//固定值
        );
//构造业务请求参数的集合(订单信息)
        $order=date("YmdHis").time().mt_rand(1000,9999);//订单号
        $price=$param['price'];//价格
        $uid=$param['userid'];//用户编号
        Response::debug($price.'+'.$uid);
        //添加账单
        $obj = array(
            "userid" => $uid,
            "charge" =>$price,
            'paytime'=>time(),
            "paytype"=>'支付宝',
            "paystate"=>0,
            "order"=>$order
        );
        $insert=M('api_charge')->add($obj);
        $content = array();
        $content['body'] = '';
        $content['subject'] = '充值';//商品的标题/交易标题/订单标题/订单关键字等
        $content['out_trade_no'] = $order;//商户网站唯一订单号
        $content['timeout_express'] = '1d';//该笔订单允许的最晚付款时间
        $content['total_amount'] = floatval($price);//订单总金额(必须定义成浮点型)
        $content['seller_id'] =  $config['seller_id'];//收款人账号
        $content['product_code'] = 'QUICK_MSECURITY_PAY';//销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
        $content['store_id'] = '001';//商户门店编号
        $con = json_encode($content);//$content是biz_content的值,将之转化成字符串
//公共参数
        $param = array();
        $Client = new \AopClient();//实例化支付宝sdk里面的AopClient类,下单时需要的操作,都在这个类里面
        $param['app_id'] = $config['appid'];//支付宝分配给开发者的应用ID
        $param['method'] = $config['method'];//接口名称
        $param['charset'] = $config['charset'];//请求使用的编码格式
        $param['sign_type'] = $config['sign_type'];//商户生成签名字符串所使用的签名算法类型
        $param['timestamp'] = $config['timestamp'];//发送请求的时间
        $param['version'] = $config['version'];//调用的接口版本，固定为：1.0
        $param['notify_url'] = $config['notify_url'];//支付宝服务器主动通知地址
        $param['biz_content'] = $con;//业务请求参数的集合,长度不限,json格式

//生成签名

        $paramStr = $Client->getSignContent($param);
        $sign = $Client->alonersaSign($paramStr,$config['rsaPrivateKey'],'RSA2');
        $param['sign'] = $sign;
        $str = $Client->getSignContentUrlencode($param);



        return array('url'=>$str);
    }
}