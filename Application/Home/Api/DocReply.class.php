<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/20
 * Time: 19:16
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class DocReply extends Base
{

    /**
     *患者留言
     */
    public function liuyan()
    {
        $listInfo = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id,u.username,u.headphoto,content,pubtime,userid')->where("t.doctorid='all'")->group('userid')->select();;
        $list = array();
        foreach ($listInfo as $key => $v) {
            $lis = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id,u.username,u.headphoto,content,pubtime,userid')->where(array("t.id_id" => $v['id']))->order('t.id desc')->getField('t.state');
            $list[$key] = $listInfo[$key];
            $list[$key]['state'] = $lis;
        }
        ApiLog::setApiInfo($list);
        if (empty($list)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }

        ApiLog::setApiInfo($list);
        if (empty($list)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $list;
    }

    /**
     *患者留言详情
     */
    public function liuyanDteil($param)
    {
        $id = $param['id'];//提问编号
        $did = $param['docid'];//医生编号
        Response::debug($id . '+' . $did);

        $listIn = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id,u.username,u.headphoto,content,pubtime,t.userid')->where("t.id='$id'")->find();
        $li = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id as tid,t.content,t.pubtime')->order('tid desc')->where("t.id_id=" . $id)->find();

        $data['state'] = 'false';
        $res = D('api_tiwen')->where(array('id' => $li['tid']))->save($data);
        $listInfo = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id as tid,t.content,t.pubtime,userid,u.username,u.headphoto')->where("t.id_id='$id'")->select();

        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        } else {
            foreach ($listInfo as $key => $u) {
                $list = D('api_tiwen_reply as t')->join('left join api_doctor as d on d.id=t.doctorid')->field('t.content as rcontent,t.pubtime as rpubtime,d.doctorname,d.pic as doctorpic')->where("t.tiwenid=" . $u['tid'] . ' and t.doctorid=' . $did)->select();

                $listInfo[$key]['reply'] = $list;

            }
            $listIn['children'] = $listInfo;
        }
        return $listIn;
    }

    /**
     * 免费咨询
     */
    public function zixun($param)
    {
        $id = $param['docid'];//医生编号
        $listInfo = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->join('api_doctor as d on d.id=t.doctorid')->field('t.id,u.username,u.headphoto,content,pubtime,userid')->where("t.doctorid='$id'")->group('userid')->order('t.id asc')->select();

        $list = array();

        foreach ($listInfo as $key => $v) {
            $lis = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->join('api_doctor as d on d.id=t.doctorid')->field('t.id,u.username,u.headphoto,content,pubtime,userid')->where(array("t.id_id" => $v['id']))->order('t.id desc')->getField('t.state');
            $list[$key] = $listInfo[$key];
            $list[$key]['state'] = $lis;
        }


        ApiLog::setApiInfo($list);
        if (empty($list)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $list;
    }

    /**
     *免费咨询详情
     */
    public function zixunDetile($param)
    {
        $id = $param['id'];//提问编号
        $did = $param['docid'];//医生编号
        Response::debug($id . '+' . $did);
        $listIn = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id,u.username,u.headphoto,content,pubtime,userid')->where("t.id='$id'")->select();

        $listInfo = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id as tid,t.content,t.pubtime,userid,u.username,u.headphoto')->where("t.id_id='$id'")->select();
        ApiLog::setApiInfo($listInfo);

        $li = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id as tid,t.content,t.pubtime,userid')->order('tid desc')->where("t.id_id='$id'")->find();

        $data['state'] = 'false';
        $res = D('api_tiwen')->where(array('id' => $li['tid']))->save($data);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        } else {
            foreach ($listInfo as $key => $u) {

                $list = D('api_tiwen_reply as t')->join('left join api_doctor as d on d.id=t.doctorid')->field('t.content as rcontent,t.pubtime as rpubtime,d.doctorname,d.pic as doctorpic')->where("t.tiwenid=" . $u['tid'] . ' and t.doctorid=' . $did)->select();

                $listInfo[$key]['reply'] = $list;
            }
            $listIn['children'] = $listInfo;
        }
        return $listIn;
    }

    /**
     * 继续回答
     */
    public function Reply($param)
    {
        $docid = $param['docid'];//医生编号
        $tid = $param['tid'];//提问编号
        $content = $param['content'];
        $date = date("Y-m-d H:i:s");
        $obj = array(
            "tiwenid" => $tid,
            "doctorid" => $docid,
            "content" => $content,
            "pubtime" => $date
        );

        $insert = M('api_tiwen_reply')->add($obj);
        $list = D('api_tiwen_reply')->where(array('tiwenid' => $tid, 'doctorid' => $docid))->order('id desc')->find();
        $data['state'] = 'true';
        $res = D('api_tiwen_reply')->where(array('id' => $list['id'], 'doctorid' => $docid))->save($data);

        if ($insert === false) {
            return array('state' => "fail");
        } else {
            return array('state' => "succeess");
        }

    }
}