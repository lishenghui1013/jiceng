<?php

/**
 * 和Token相关的全部接口
 * @since   2017/03/02 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
use Think\log;

class Version extends Base
{
    //获取最新版本
    public function upgrade($param)
    {
        $version = trim($param['version']) * 100;
        settype($version,"int");
        $type = (int)$param['type'];//【1：IOS,2:Android】
        if (!$version) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少版本号');
        }
        if (!$type) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少客户端类型');
        }
        /*$data = D('api_appversion')->where('app_version > \''.$version.' \' and app_type = '.$type)->field('app_version,app_url')->order('createtime desc')->find();*/
        $data = D('api_appversion')->field('(CAST(app_version*100 AS SIGNED)) as num,app_version,app_url')->where('app_type = ' . $type)->having('num>'.$version)->order('num desc')->limit(1)->select();
        if ($data[0]) {
            return $data[0];
        } else {
            return array('msg' => '当前已是最新版本');
        }
    }
}