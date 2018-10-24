<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/26
 * Time: 14:47
 */
namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
use Home\Model\TclassModel;
class Lunbo extends Base{
    /**
     *首页轮播图
     */
    public function img() {
        $fenlei=D('api_lunbo as l')->join('api_hospital as h on  h.id=l.hosid')->field('l.img,l.hosid,h.lng,h.lat')->select();
        //echo var_dump ($fenlei);
        if (empty($fenlei)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $fenlei;
    }

    /**
     *首页轮播文字
     */
    public function wenzi() {
        $list=D('api_hospital')->field('hospitalname')->select();
        if (empty($list)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $list;
    }
}