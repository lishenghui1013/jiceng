<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/21
 * Time: 15:02
 */
namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
class DocDetails extends Base{
    /**
     *医生我的详情（诊所列表）
     */
    public function poslist($param) {
        $did=$param['did'];//医生编号
        $listInfo = D('api_doctor as d')->join('api_hospital as h  on h.id=d.hospital')->field('h.id,h.hospitalname,h.hos_pic,h.hos_address,h.hos_phone')->where('d.id='.$did)->select();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
    }
    /**
     *医生我的详情（诊所列表详情页）
     */
    public function poslistDetil($param) {
        $id=$param['id'];//医院编号
        $docid=$param['docid'];//医生编号
        $listIn = D('api_doctor as d')->join('api_hospital as h  on h.id=d.hospital')->field('h.id,h.hospitalname,h.hos_pic,h.hos_address,h.hos_phone')->where('h.id='.$id.' and d.id='.$docid)->select();
        foreach($listIn as $key=>$v)
        {
            $listInfo = D('api_spendmoney as s')->join('api_users as u on u.id=s.userid')->field('u.username,spendmoney,spendtime')->where("spended='$id'")->select();
            ApiLog::setApiInfo($listInfo);
            if (empty($listInfo)) {
                Response::error(ReturnCode::INVALID, '暂无数据');
            }
            else
            {
                $listIn[$key]['children']=$listInfo;
            }
        }
        return $listIn;
    }
}