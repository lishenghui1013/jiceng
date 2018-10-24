<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/8
 * Time: 9:35
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class Mytiwen extends Base
{
    /**
     *我的咨询
     */
    public function zixun($param)
    {
        $uid = $param['userid'];
        Response::debug($uid);
        $listInfo = D('api_tiwen as t')->join('api_doctor as d on d.id=t.doctorid')->join('api_hospital as h on h.id=d.hospital')->field('t.id,d.pic,d.doctorname,h.hospitalname,t.content,pubtime')->where(array('userid' => $uid))->group('t.doctorid')->select();
        $listIn = D('api_tiwen as t')->join('api_doctor as d on d.id=t.doctorid')->join('api_hospital as h on h.id=d.hospital')->field('t.id,d.pic,d.doctorname,h.hospitalname,t.content,pubtime')->where(array('userid' => $uid))->order('t.id desc')->find();

        $list = array();

        foreach ($listInfo as $key => $v) {
            $lis = D('api_tiwen as t')->join('api_tiwen_reply as d on d.tiwenid=t.id')->field('d.state,d.id')->where(array("d.tiwenid" => $listIn['id']))->order('d.id desc')->getField('d.state');

            if ($lis) {
                $list[$key] = $listInfo[$key];
                $list[$key]['state'] = $lis;
            } else {
                $list[$key] = $listInfo[$key];
                $list[$key]['state'] = 'false';
            }


        }
        ApiLog::setApiInfo($list);
        if (empty($list)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $list;
    }

    /**
     *我的咨询详情页
     */

    public function zixunDetil($param)
    {
        $id = $param['id'];
        $listIn = D('api_tiwen as t')->join('api_doctor as d on d.id=t.doctorid')->join('api_hospital as h on h.id=d.hospital')->join('api_department as p on p.id=d.department')->field('t.id,d.id as did,d.pic,d.doctorname,h.hospitalname,departmentname,jobtitle,workyears')->where(array('t.id' => $id))->find();
        ApiLog::setApiInfo($listIn);
        //$li = D('api_tiwen as t')->field('t.id as tid,t.content,t.pubtime')->order('tid desc')->where("t.id_id=".$id)->find();
        $listInfo = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id as tid,t.content,t.pubtime,userid,u.username,u.headphoto')->where("t.id_id='$id'")->select();

        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            return $listIn;
        } else {
            foreach ($listInfo as $key => $u) {
                $list = D('api_tiwen_reply as t')->join('left join api_doctor as d on d.id=t.doctorid')->field('t.content as rcontent,t.pubtime as rpubtime,d.doctorname,d.pic as doctorpic')->where("t.tiwenid=" . $u['tid'] . ' and t.doctorid=' . $listIn['did'])->select();

                ApiLog::setApiInfo($list);
                $listInfo[$key]['reply'] = $list;
                $data['state'] = 'false';
                $res = D('api_tiwen_reply')->where(array('tiwenid' => $u['tid']))->save($data);
            }
            $listIn['children'] = $listInfo;
        }

        return $listIn;
    }

}