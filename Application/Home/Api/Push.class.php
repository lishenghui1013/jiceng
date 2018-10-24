<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/5/5
 * Time: 11:26
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
class Push extends Base
{
    //用户发消息给医生
    //给医生推送
    public function Utuisong($param)
    {
        $tid=$param["tid"];//提问编号
        $listInfo = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id,t.content,t.pubtime,u.username,photo,t.doctorid')->where(array('t.id'=>$tid))->find();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        $list=array();
     if($listInfo['doctorid']=='all')
       {
            $doc=D('api_doctor')->field('id')->select();
           foreach($doc as $key=>$val)
           {
               if(isset($listInfo['photo']))
               {
                   $result['img']='图片';
               }
               $result['username'] = $listInfo['username'];
               $result['content'] = $listInfo['content'];
               $result['doctorid']=$val['id'];
               $list[]=$result;
           }
           return $list;
       }
        else {
            $list['doctorid'] = $listInfo['doctorid'];
            if (isset($listInfo['photo'])) {
                $list['img'] = '图片';
            }
            $list['username'] = $listInfo['username'];
            $list['content'] = $listInfo['content'];
            return $list;

        }



    }
      //医生回复用户
    //给客户推送
    public  function  dtuisong($param)
    {
        $huifuid=$param["huifuid"];//回复编号
        $tid=$param["tid"];//提问编号
        $listInfo = D('api_tiwen_reply as t')->join('api_doctor as d on d.id=t.doctorid')->field('t.content,t.pubtime,d.doctorname')->where(array('t.id'=>$huifuid))->find();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        $list=array();
        $u=D('api_users as u')->join('api_tiwen as t on t.userid=u.id')->field('u.id')->where(array('t.id'=>$tid))->select();
        foreach($u as $key=>$val)
        {
            /*if(isset($listInfo['photo']))
            {
                $result['img']='图片';
            }*/
            $result['doctorname'] = $listInfo['doctorname'];
            $result['content'] = $listInfo['content'];
            $result['uid']=$val['id'];
            $result['tid']=$tid;
            $list[]=$result;
        }
        return $list;
    }
}

