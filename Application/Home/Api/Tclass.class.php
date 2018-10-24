<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/2/23
 * Time: 17:02
 */
namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
use Home\Model\TclassModel;
class Tclass extends Base{
    /**
     *小病百科分类
     */
    public function fenlei() {
        $fenlei=D('api_class')->select();
        //echo var_dump ($fenlei);
        if (empty($fenlei)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
       // $res = array();
        foreach($fenlei as $key=>$v)
        {
            //$res=$fenlei;
            $cid=$v['id'];
           $listInfo= D('api_troubleclass')->where(array('cid' =>$cid))->select();
            //echo var_dump ($listInfo);
                if(empty($listInfo)){
                    $fenlei[$key]['children']="暂无数据";
                }
            else
            {
                $fenlei[$key]['children']=$listInfo;
            }
        }
        return $fenlei;
    }
    /**
     *小病百科小分类详情
     */
    public function details($param) {
        //$param['id']=1;
        if (empty($param['id'])) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少id');
        }
        $listInfo = D('api_troubleclass')->where(array('id' => $param['id']))->select();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
    }

}