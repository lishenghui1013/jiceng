<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/5/8
 * Time: 19:14
 */
namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
use Think\Model;

class Hot extends Base
{
    /**
     *热点问题列表
     */
    public function tiwenIndex()
    {
        $sql = 'select t.id,t.pubtime,t.content,a.num,a.tiwenid from api_tiwen as t left join (select count(tiwenid) as num,tiwenid from api_aapinglun group by tiwenid order by num) as a on a.tiwenid=t.id order by num desc limit 6';
        $tiwen = new Model();
        $listInfo = $tiwen->query($sql);
/* 		
		        $list = array();
        foreach ($listInfo as $key => $v) {
            $where['id_id'] = $v['id'];
            $listIn = D('api_tiwen as t')->field('id,content,pubtime')->where($where)->limit(1)->select();
            if ($listIn) {
                foreach ($listIn as $key => $v) {
                    $list[] = $listIn[$key];
                }
            }

        } */
		
		
		
		
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
		
		
		
		
		
    }

    /**
     * 热点问题评论列表
     */
       /**
     * 热点问题评论列表
     */
    public function  pinglun($param)
    {
/*         $id = $param['id']='292';//提问编号
        $listInfo = D('api_tiwen as t')->join('api_tiwen_reply as r on r.tiwenid=t.id')->where("tiwenid=" .$id)->field('r.doctorid,r.tiwenid')->select();
		
		//dump( $listInfo);die();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        $arr=array();
        foreach ($listInfo as $key => $u)
        {
            $did=$u['doctorid'];
           // $c= D('api_tiwen_reply as r')->join('api_doctor as d on d.id=r.doctorid')->field('count(*) as count,content,pubtime,d.doctorname,d.pic')->where("tiwenid=".$id.' and  doctorid='.$did)->select();
                $count= D('api_tiwen_reply as r')->join('api_doctor as d on d.id=r.doctorid')->field('tiwenid,r.doctorid,content,pubtime,d.doctorname,d.pic')->where("tiwenid=".$id.' and  doctorid='.$did)->count();
            if($count>=1)
            {
                $list= D('api_tiwen_reply as r')->join('api_doctor as d on d.id=r.doctorid')->field('tiwenid,r.doctorid,content,pubtime,d.doctorname,d.pic')->where("tiwenid=".$id.' and  doctorid='.$did)->limit(1)->select();
                foreach ($list as $key => $u)
                {
                    $arr[] = $list[$key];
                }
            }
        }
        $newarr = array();
        foreach($arr as $_arr){
            if(!isset($newarr[$_arr['doctorid']])){
				 $newarr[] = $_arr;
            }
        }

        $lis = D('api_tiwen as t')->where("id=" .$id)->select();
        foreach ($lis as $key => $u) {
          // $lis[]['chil'] = $newarr;
            $lis[$key]['pinglun']=$newarr;
        }
        return $lis; */
   
		 $id = $param['id'];//提问编号
        $userid = $param['userid'];//用户编号
        $cotment=$param['cotment'];//评论内容
        $obj = array(
            "tiwenid" => $id,
            "contment" =>$cotment,
            "userid"=>$userid,
        );
        $res=M('api_aapinglun')->add($obj);
        if($res){
            return array('state' => 'success');
        }
        else{
            return array('state' => 'fail');
        }

   }
    /**
     *热点问题详情页
     */
    public function tiwenDetiles($param)
    {
        $id= $param['id'];
/*         $did=$param['did'];

        $listIn = D('api_doctor as d ')->join('api_department as de on de.id=d.department')->join('api_hospital as p on p.id=d.hospital ')->field('d.id,d.doctorname,d.pic,d.jobtitle,d.workyears,de.departmentname,p.hospitalname')->where(array('d.id'=>$did))->select();
        foreach($listIn as $key=>$v)
        {
            $listInfo = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id as tid,t.content,t.pubtime')->where("t.id_id=".$id)->select();
            ApiLog::setApiInfo($listInfo);
            if (empty($listInfo))
            {
                Response::error(ReturnCode::INVALID, '暂无数据');
            }
            else
            {


                foreach($listInfo as $key=>$u)
                {
                    $list= D('api_tiwen_reply')->field('content as rcontent,pubtime as rpubtime')->where("tiwenid=".$u['tid'].' and  doctorid='.$did)->select();

                    $listInfo[$key]['reply']=$list;
                }
                $listIn[]['children']=$listInfo;
            }
        }
        return $listIn; */
   
        $listInfo = D('api_tiwen as t')->where(array("id"=>$id))->find();
        $lis = D('api_aapinglun as p')->join('api_users as d on d.id=p.userid')->field('d.headphoto,d.username,p.contment')->where(array("tiwenid"=>$id))->select();
        $listInfo['pinglun']=$lis;
        return $listInfo;
   }

}