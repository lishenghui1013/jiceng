<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/9
 * Time: 15:33
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class Details extends Base
{
    /**
     *我的详情（医院列表）
     */
    public function particulars($param)
    {
        $uid = $param['userid'];
        $listInfo = D('api_spendmoney as s')->join('left join api_hospital as h on h.id=s.spended')->field('s.state,s.id as sid,s.spendtime,h.id,h.hospitalname,h.hos_pic,h.hos_address,h.hos_phone')->where(array('s.userid' => $uid))->select();

        //$listInfo = D('api_spendmoney as s')->join('api_hospital as h on h.id=s.spended')->field('h.id,h.hospitalname,h.hos_pic,h.hos_address,h.hos_phone')->where(array('userid'=>$uid))->select();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        foreach ($listInfo as $key => $val) {
            $arr = explode('||', $val['hos_pic']);
            $listInfo[$key]['hos_pic'] = array_filter($arr);
            $listInfo[$key]['hos_pic'] = $listInfo[$key]['hos_pic'][0];
        }
        return $listInfo;
    }

    /**
     *我的详情（医院列表详情）
     */
    public function particularsDetil($param)
    {
        $sid = $param['sid'];//消费记录id
        $listIn = D('api_spendmoney as s')->join('left join api_hospital as h on h.id=s.spended')->field('s.spendmoney,s.spendtime,s.state,s.id as sid,h.id,h.hospitalname,h.hos_pic,h.hos_address,h.hos_phone')->where(array('s.id' => $sid))->find();

        $arr = explode('||', $listIn['hos_pic']);
        $listIn['hos_pic'] = array_filter($arr);
        $listIn['hos_pic'] = $listIn['hos_pic'][0];
        if (empty($listIn)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }

        return $listIn;
    }

    public function liebiao($param)
    {
        $hid = $param['hid'];//医院编号
        $listInfo = D('api_hospital as h')->field('h.id,h.hospitalname,h.hos_pic,h.hos_address,h.hos_phone')->where(array('h.id' => $hid))->select();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
    }

    public function pingjia($param)
    {
        $uid = $param['userid'];//用户编号
        $hid = $param['hid'];//医院编号
        $comment = $param['comment'];//评价内容
        $star = $param['star'];//评分星级(1,一星；2，二星；3，三星；4，四星；5，五星)
        $id = $param['sid'];//消费记录id
        $user = D('api_users')->where(array('id' => $uid))->getField('username');//用户姓名
        $obj = array(
            "phone" => $uid,
            "comment" => $comment,
            "comtime" => date("Y-m-d H:i:s"),
            "beid" => $hid,
            "type" => 1,
            "userid" => $uid,
            "username" => $user,
            "star" => $star
        );
        $insert = M('api_hospitalcomment')->add($obj);
        if ($insert === false) {
            return array('bool' => "fail");
        } else {
            //$spendmoney=D('api_spendmoney')->where("userid='$uid'".'and '."spended='$hid'")->order('id desc')->select();
            // $spendmoneys=$spendmoney[0]['id'];//评价id
            $data['state'] = 1;
            D('api_spendmoney')->where(array('id' => $id))->data($data)->save();//修改评价状态
            //查询查询评价信息
            $estimate = D('api_hospitalcomment')->field('count(id) as num,sum(star) as total')->where(array('spended' => $hid, 'type' => 1))->find();
            $avg = 0.00 + $estimate['total'] / $estimate['num'];
            $hosStar = round($avg);
            $res = D('api_hospital')->where(array('id' => $hid))->data(array('star' => $hosStar))->save();//修改医院评价星级
            //把星级平均数修改到诊所表中
            return $res ? array('bool' => "success") : array('bool' => "fail");
        }
    }
}