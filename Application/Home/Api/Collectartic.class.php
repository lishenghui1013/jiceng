<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/6/6
 * Time: 16:53
 */
namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class Collectartic extends Base
{
    /**
     *收藏列表
     */
    public function shoucanglist($param)
    {
        $docid=$param['docid'];//医生编号
        $listInfo= D('api_article as a')->join('api_articletype as t on t.id=a.type')->join('api_collectartic as c on c.articid=a.id')->field('a.id,a.title,a.pic,a.publishtime,t.typename')->where(array('c.docid' =>$docid,'ifshow'=>0))->select();
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        foreach($listInfo as $key=>$val){
            $arr=explode('||',$val['pic']);
            $listInfo[$key]['pic']=array_filter($arr);
            $listInfo[$key]['pic']=$listInfo[$key]['pic'][0];
        }
        return $listInfo;
    }
    /**
     *收藏列表
     */
    public function shoucangdetiles($param)
    {
        $id=$param['id'];//医生编号
        $listInfo= D('api_article as a')->join('api_articletype as t on t.id=a.type')->field('a.*,t.typename')->where(array('a.id' =>$id,'ifshow'=>0))->find();
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }

            $arr=explode('||',$listInfo['pic']);
            $listInfo['pic']=array_filter($arr);
            $listInfo['pic']=$listInfo['pic'][0];

        return $listInfo;
    }
    /**
     *收藏
     */
    public function shoucang($param)
    {
        $docid=$param['docid'];//医生编号
        $atrid=$param['atrid'];//文章编号
        $obj = array(
            "docid" => $docid,
            "articid" =>$atrid,
        );
        $insert=M('api_collectartic')->add($obj);
        if( $insert === false ){
            return array('state' => "fail");
        }else{
            return array('state' => "success");
        }
    }
    /**
     *取消收藏
     */
    public function reset($param)
    {
        $docid=$param['docid'];//医生编号
        $atrid=$param['atrid'];//文章编号
        $insert=M('api_collectartic')->where(array("articid"=>$atrid,"docid"=>$docid))->delete();
        if(!$insert){
            return array('state' => "fail");
        }else{
            return array('state' => "success");
        }
    }

}