<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/2/23
 * Time: 16:33
 */
namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
class TclassImg extends Base{
    /**
     *小病百科头部顶图
     */
    public function img($param) {
        $type=$param['type'];
        if (empty($type)) {
            Response::error(ReturnCode::INVALID, '缺少type');
        }
        $listInfo = D('api_tclassimg')->where('use_state='.$type)->select();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
    }
}