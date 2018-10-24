<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/5
 * Time: 10:03
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class Collection extends Base
{
    /**
     *我的收藏
     */
    public function shoucang($param)
    {
        $uid = $param['userid'];
        $type = $param['type'];//类型(1医生,2医院)
        Response::debug($uid . '+' . $type);
        if ($type == 1)//收藏的医生列表
        {
            $listInfo = D('api_doctor  as dc')->join('left join api_department as d on d.id=dc.department')->join('left join api_hospital as p on p.id=dc.hospital')->join('left join api_collection as c on c.becollect=dc.id')->field("d.departmentname,dc.id,dc.pic,dc.doctorname,dc.specialty,p.hospitalname,p.id as pid,dc.jobtitle")->where(array('c.userid' => $uid, 'c.type' => $type))->select();

            if (empty($listInfo)) {
                Response::error(ReturnCode::INVALID, '暂无数据');
            }
            return $listInfo;
        } elseif ($type == 2)//收藏的医院列表
        {
            $listInfo = D('api_hospital as dc')->join('api_collection as c on c.becollect=dc.id')->field('dc.id,dc.hospitalname,hos_phone,hos_address,hos_pic')->where(array('userid' => $uid, 'c.type' => $type))->select();
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


    }

    /**
     * 医生详情页
     */
    public function docDetil($param)
    {
        $id = $param['did'];
        $uid = $param['userid'];

        $listInfo = D('api_doctor  as dc ')->join('api_department as d on d.id=dc.department')->join('api_hospital as p on p.id=dc.hospital ')->field("dc.id,d.departmentname,dc.doctorname,dc.phone,dc.jobtitle,dc.workyears,dc.pic,dc.specialty,p.hospitalname,dc.visittime,dc.doctorintro,dc.specialty,p.id as pid,p.hos_address,p.lng,p.lat")->where(array('dc.id' => $id))->select();

        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }

        if (isset($uid)) {
            foreach ($listInfo as $key => $v) {
                $user1 = M();
                $pinglun = $user1->query("select u.username,u.headphoto,t.content,t.pubtime from api_tiwen as t  INNER  join api_users as u on u.id=t.userid  where t.doctorid=$id and u.id=$uid  limit 1 ");
                //$pinglun=D('api_tiwen as t')->join('api_user as u on u.id=t.userid ')->field('*')->where('t.doctorid='.$id.' and  t.userid='.$uid)->select();//评论表
                $listInfo[$key]['children'] = $pinglun;

            }
        } else {
            foreach ($listInfo as $key => $v) {

                $pinglun = array();
                //$pinglun=D('api_tiwen as t')->join('api_user as u on u.id=t.userid ')->field('*')->where('t.doctorid='.$id.' and  t.userid='.$uid)->select();//评论表
                $listInfo[$key]['children'] = $pinglun;

            }

        }
        if (isset($uid)) {
            foreach ($listInfo as $key => $v) {
                $shoucang = D('api_doctor as p')->join('api_collection as c on c.becollect=p.id')->field("c.state")->where(array('c.becollect' => $id, 'type' => 1, "c.userid" => $uid))->select();
                if (empty($shoucang)) {
                    $listInfo[$key]['state'] = "0";
                } else {
                    $listInfo[$key]['state'] = '1';
                }
            }

        } else {
            foreach ($listInfo as $key => $v) {
                $listInfo[$key]['state'] = "0";
            }
        }

        return $listInfo;
    }

    /**
     * 医院详情页
     */
    public function posDetil($param)
    {
        $id = $param['pid'];
        $uid = $param['userid'];
        $listInfo = D('api_hospital as p')->field("p.id,p.hos_pic,p.hos_phone,p.hospitalname,p.star,p.hos_address,p.hos_intro,p.mainwork,p.equipment,p.test,p.charge,p.ifyibao,p.ifvisits,p.video,p.lng,p.lat")->where(array('p.id' => $id))->select();

        foreach ($listInfo as $key => $val) {
            $arr = explode('||', $val['hos_pic']);
            $listInfo[$key]['hos_pic'] = array_filter($arr);
            $listInfo[$key]['hos_pic'] = $listInfo[$key]['hos_pic'];
        }


        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        foreach ($listInfo as $key => $v) {
            $pinglun = D('api_doctor as dc')->join('api_department as d on d.id=dc.department')->join('api_hospital as p on p.id=dc.hospital')->field('dc.id,dc.pic,dc.doctorname,dc.jobtitle,d.departmentname,dc.specialty,p.hospitalname')->where('dc.hospital=' . $id)->select();//评论表
            if (empty($pinglun)) {
                $listInfo[$key]['children1'] = $pinglun;
            }
            $listInfo[$key]['children1'] = $pinglun;

        }
        foreach ($listInfo as $key => $v) {
            $pingluns = D('api_hospitalcomment as h')->join('api_users as u on u.id=h.userid')->field('u.username,u.headphoto,h.comment,h.comtime')->where(array('beid' => $id))->select();//评论表

            if (empty($pingluns)) {
                $listInfo[$key]['children2'] = $pingluns;
            }
            $listInfo[$key]['children2'] = $pingluns;


        }
        if ($uid != '') {
            foreach ($listInfo as $key => $v) {

                $shoucang = D('api_hospital as p')->join('api_collection as c on c.becollect=p.id')->field("c.state")->where(array('c.becollect' => $id, 'type' => 2, "c.userid" => $uid))->select();
                if (empty($shoucang)) {
                    $listInfo[$key]['state'] = "0";
                } else {
                    $listInfo[$key]['state'] = '1';

                }


            }
        } else {
            foreach ($listInfo as $key => $v) {
                $listInfo[$key]['state'] = "0";
            }
        }

        return $listInfo;
    }

    /**
     *我的收藏
     */
    public function Wenzhangshoucang($param)
    {
        $uid = $param['docid'];
        $type = $param['type'];//类型(1医生,2医院,3文章)
        if ($type == 1)//收藏的医生列表
        {
            $listInfo = D('api_doctor  as dc ')->join('api_department as d on d.id=dc.department')->join('api_hosdepartrelation as a on a.did=d.id')->join('api_hospital as p on p.id=a.hid ')->join('api_collection as c on c.becollect=dc.id')->field("d.departmentname,dc.id,dc.pic,dc.doctorname,dc.specialty,p.hospitalname,p.id as pid")->where(array('c.userid' => $uid))->select();
            if (empty($listInfo)) {
                Response::error(ReturnCode::INVALID, '暂无数据');
            }
            return $listInfo;
        } elseif ($type == 2)//收藏的医院列表
        {
            $listInfo = D('api_hospital as dc')->join('api_collection as c on c.becollect=dc.id')->field('dc.id,dc.hospitalname,hos_phone,hos_address,hos_pic')->where(array('userid' => $uid))->select();
            if (empty($listInfo)) {
                Response::error(ReturnCode::INVALID, '暂无数据');
            }
            return $listInfo;
        }


    }
}