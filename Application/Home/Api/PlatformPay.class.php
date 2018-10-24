<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/21
 * Time: 16:46
 */
namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
class PlatformPay extends Base{
    /**
     *平台支付
     */
    public function pay($param) {
        $uid=$param['userid'];//用户编号
        $hid=$param['hid'];//诊所编号
        $money=$param['money'];//支付金额
        $date =time();//支付时间
        Response::debug($uid.'+'.$hid.'+'.$money);
        //获取用户账户余额
        $account = D('api_users')->where('id='.$uid)->getField('account');
        if(intval($money*100)<=0)
        {
            return array('state' => "您输入的金额不合理");
        }
        if(intval($account*100)==0)
        {
            return array('state' => "您的金币不足，请先充值");
        }
        else
        {
            if(intval($account*100)>=intval($money*100))
            {
                $data['account']=$account-$money;
                $res = D('api_users')->where(array('id' => $uid))->save($data);//修改用户账号金币

                $obj = array(
                    "userid" => $uid,//用户编号
                    "spendmoney" =>$money,//支付金额
                    'spendtime'=>$date,//支付时间
                    'spended'=>$hid,//消费对象（诊所编号）
					'state'=>0//消费对象（诊所编号）
                );
                $insert=M('api_spendmoney')->add($obj);//向api_spendmoney消费记录表中添加记录

                $objs = array(
                    "userid" => $uid,//用户编号
                    "hospitalid" =>$hid//医院编号
                );
                $inserts=M('api_u_h_relation')->add($objs);//向api_u_h_relation用户、医生消费关系表中添加记录

                if( $res === false &&$insert==false&&$inserts==false){
                    return array('state' => "fail");
                }else{

                    return array('state' => "succeess");
                }
            }
            else
            {
                return array('state' => "您的金额不足，请先充值");
            }

        }

    }
}