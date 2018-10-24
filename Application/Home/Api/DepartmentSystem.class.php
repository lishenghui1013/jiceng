<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/2/22
 * Time: 14:33
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
use \Think\Model;

class DepartmentSystem extends Base
{
    /**
     *科室列表
     */
    public function depslist()
    {
        $listInfo = D('api_department')->order('hospital asc')->select();

        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
    }


    /**
     * 医生（医生列表）
     */
    public function doctor($param)
    {

        //$param['kid']=1;
        if (empty($param['kid'])) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少kid');
        }
        $listInfo = D('api_doctor  as dc ')->join('api_department as d on d.id=dc.department')->join('api_hosdepartrelation as a on a.did=d.id')->join('api_hospital as p on p.id=a.hid ')->field("d.departmentname,dc.id,dc.pic,dc.doctorname,dc.specialty,p.hospitalname,p.id as pid")->where(array('d.id' => $param['kid']))->select();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
    }

    /**
     * 问诊（诊所列表）
     */
    public function hospital($param)
    {

        //$param['kid']=5;
        if (empty($param['kid'])) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少kid');
        }
        //$page=$param['id']?$param['id']:1;
        $listInfo = D('api_hospital as h')->join('api_hosdepartrelation as a on h.id=a.hid')->join('api_department as d on d.id=a.did')->field("h.id,h.hospitalname,h.hos_phone,h.hos_address")->where(array('d.id' => $param['kid']))->select();;
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        //$count=D('api_hospital as h')->join('api_hosdepartrelation as a on h.id=a.hid')->join('api_department as d on d.id=a.did')->field("h.id,h.hospitalname,h.hos_phone,h.hos_address")->where(array('d.id' => $param['id']))->count();
        ApiLog::setAppInfo($listInfo);
        //$return['total'] = $count;
        //$return['list'] = $listInfo;
        return $listInfo;
    }

    /**
     * 省
     */
    public function sheng()
    {
        $listInfo = D('api_provinces as a')->select();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
    }

    /**
     * 河北省的城市
     */
    public function cities($param)
    {
        $shengid = $param['provinceid'];
        $listInfo = D('api_cities as a')->where(array('provinceid' => $shengid))->select();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
    }

    /**
     * 筛选下拉科室
     */
    public function keshi($param)
    {
        // $param['kid']='1';
        if (empty($param['kid'])) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少kid');
        }
        $listInfo = D('api_department')->where(array('id' => $param['kid']))->field('id,departmentname')->order('hospital asc')->select();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
    }

    /**
     * 市对应的县区
     */
    public function areas($param)
    {

        // $param['cityid']=130100;
        if (empty($param['cityid'])) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少cityid');
        }
        $listInfo = D('api_areas as a')->where(array('cityid' => $param['cityid']))->select();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listInfo;
    }

    /**
     * 医生（医生筛选列表）
     */
    public function doctorfilter($param)
    {

        $adress = $param['adress'];
        $kid = $param['kid'];
        $xid = $param['xid'];
        $price = $param['price'];

        //默认查询全部医生列表信息
        $sql = 'select d.departmentname,dc.jobtitle,dc.phone,dc.id,dc.pic,dc.doctorname,dc.specialty,p.hospitalname,p.id as pid,p.star from api_doctor  as dc left join api_department as d on d.id=dc.department left join api_hospital as p on p.id=dc.hospital where 1=1';
        $where = '';

        $order = '';
        //如果科室不为空则加科室为查询条件
        if ($kid != '') {
            $where .= ' and dc.department="' . $kid . '"';
        }
        //如果有地址则查询条件加地址为查询条件
        if ($adress != '') {
            $where .= ' and p.areasid="' . $adress . '"';

        }

        if ($price != '' && $xid != '') {
            $order = $price == 2 ? ' order by dc.paixu desc' : ($price == 1 ? ' order by dc.paixu asc' : '');
            $order .= $xid == 2 ? ',p.star desc' : ($xid == 1 ? ',p.star asc' : '');

        } elseif ($price != '' && $xid == '') {
            $order = $price == 2 ? ' order by dc.paixu desc' : ($price == 1 ? ' order by dc.paixu asc' : '');
        } elseif ($price == '' && $xid != '') {
            $order = $xid == 2 ? ' order by p.star desc' : ($xid == 1 ? ' order by p.star asc' : '');
        } else {
            $order = '';
        }

        $sql = $sql . $where . $order;//拼接sql语句
        // 实例化一个model对象 没有对应任何数据表
        $Model = new \Think\Model();
        $listIn = $Model->query($sql);//执行原生查询
        ApiLog::setApiInfo($listIn);


        if (empty($listIn)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listIn;
    }

    /**
     * 问诊（诊所筛选列表）
     */
    public function hosfilter($param)

    {
        $adress = $param['adress'];
        $kid = $param['kid'];
        $xid = $param['xid'];
        $price = $param['price'];
        //默认查询全部医院列表信息
        $sql = 'select p.id,p.hospitalname,hos_phone,hos_address,hos_pic from api_department as d left join api_hosdepartrelation as a on a.did=d.id left join api_hospital as p on p.id=a.hid where 1=1';
        $where = '';

        $order = '';
        //如果科室不为空则加科室为查询条件
        if ($kid != '') {
            $ids = D('api_hosdepartrelation')->field('hid')->where('did="' . $kid . '"')->select();
            $str = '';
            if ($ids) {
                foreach ($ids as $key => $value) {
                    $str .= $value['hid'] . ',';
                }
                unset($key, $value);
            }
            $str = substr($str, 0, -1);

            $where .= ' and a.hid in (' . $str . ')';
        }
        //如果有地址则查询条件加地址为查询条件
        if ($adress != '') {
            $where .= ' and p.areasid="' . $adress . '"';

        }


        if ($price != '' && $xid != '') {
            $order = $price == 2 ? ' order by p.paixu desc' : ($price == 1 ? ' order by p.paixu asc' : '');
            $order .= $xid == 2 ? ',p.star desc' : ($xid == 1 ? ',p.star asc' : '');

        } elseif ($price != '' && $xid == '') {
            $order = $price == 2 ? ' order by p.paixu desc' : ($price == 1 ? ' order by p.paixu asc' : '');
        } elseif ($price == '' && $xid != '') {
            $order = $xid == 2 ? ' order by p.star desc' : ($xid == 1 ? ' order by p.star asc' : '');
        } else {
            $order = '';
        }


        $sql = $sql . $where . ' group by a.hid' . $order;//拼接sql语句
        // 实例化一个model对象 没有对应任何数据表
        $Model = new \Think\Model();
        $listIn = $Model->query($sql);//执行原生查询
        ApiLog::setApiInfo($listIn);
        if (empty($listIn)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        foreach ($listIn as $key => $val) {
            $arr = explode('||', $val['hos_pic']);
            $listIn[$key]['hos_pic'] = array_filter($arr);
            $listIn[$key]['hos_pic'] = $listIn[$key]['hos_pic'][0];
        }
        return $listIn;
    }

    /**
     * 寻医问诊（搜索医生或者诊所）
     */
    public function sousuo($param)
    {
        $type = $param['type'];//当type=1时，搜索的是医生列表，当type=2时，搜索的是诊所列表
        $name = $param['name'];//用户搜索内容
        Response::debug($type . '+' . $name);
        if ($type == 1) {
            if ($name == '医生') {
                $listInfo = D('api_doctor  as dc ')->join('api_department as d on d.id=dc.department')->join('api_hospital as p on p.id=dc.hospital ')->field("d.departmentname,dc.id,dc.pic,dc.doctorname,dc.specialty,p.hospitalname,p.id as pid,dc.jobtitle")->select();
                ApiLog::setApiInfo($listInfo);
                if (empty($listInfo)) {
                    Response::error(ReturnCode::INVALID, '暂无数据');
                }
                return $listInfo;
            } else {

                //mb_substr($name,1,3,$charset='utf-8', $suffix=true);
                $data['doctorname'] = array('like', "%$name%");
                $listInfo = D('api_doctor  as dc ')->join('api_department as d on d.id=dc.department')->join('api_hospital as p on p.id=dc.hospital ')->field("d.departmentname,dc.id,dc.pic,dc.doctorname,dc.specialty,p.hospitalname,p.id as pid,dc.jobtitle")->where($data)->select();

                ApiLog::setApiInfo($listInfo);
                if (empty($listInfo)) {
                    //Response::error(ReturnCode::INVALID, '暂无数据');
                    $na = mb_substr($name, 0, 1, 'utf-8');
                    $data['doctorname'] = array('like', "%$na%");
                    $listInfo = D('api_doctor  as dc ')->join('api_department as d on d.id=dc.department')->join('api_hospital as p on p.id=dc.hospital ')->field("d.departmentname,dc.id,dc.pic,dc.doctorname,dc.specialty,p.hospitalname,p.id as pid,dc.jobtitle")->where($data)->select();

                }
                return $listInfo;
            }


        } elseif ($type == 2) {
            if ($name == '诊所' || $name == '医院') {
                $listInfo = D('api_department as d')->join('api_hosdepartrelation as a on a.did=d.id')->join('api_hospital as p on p.id=a.hid ')->field("p.id,p.hospitalname,hos_phone,hos_address,hos_pic")->select();
                ApiLog::setApiInfo($listInfo);
                if (empty($listInfo)) {
                    Response::error(ReturnCode::INVALID, '暂无数据');
                }
                ApiLog::setAppInfo($listInfo);
                foreach ($listInfo as $key => $val) {
                    $arr = explode('||', $val['hos_pic']);
                    $listInfo[$key]['hos_pic'] = array_filter($arr);
                    $listInfo[$key]['hos_pic'] = $listInfo[$key]['hos_pic'][0];
                }
                return $listInfo;
            } else {
                $data['hospitalname'] = array('like', "%$name%");
                $listInfo = D('api_department as d')->join('api_hosdepartrelation as a on a.did=d.id')->join('api_hospital as p on p.id=a.hid ')->field("p.id,p.hospitalname,hos_phone,hos_address,hos_pic")->where($data)->select();

                ApiLog::setApiInfo($listInfo);
                if (empty($listInfo)) {
                    //Response::error(ReturnCode::INVALID, '暂无数据');
                    $na = mb_substr($name, 0, 1, 'utf-8');
                    $data['hospitalname'] = array('like', "%$na%");
                    $listInfo = D('api_department as d')->join('api_hosdepartrelation as a on a.did=d.id')->join('api_hospital as p on p.id=a.hid ')->field("p.id,p.hospitalname,hos_phone,hos_address,hos_pic")->where($data)->select();

                }
                foreach ($listInfo as $key => $val) {
                    $arr = explode('||', $val['hos_pic']);
                    $listInfo[$key]['hos_pic'] = array_filter($arr);
                    $listInfo[$key]['hos_pic'] = $listInfo[$key]['hos_pic'][0];
                }
                ApiLog::setAppInfo($listInfo);
                return $listInfo;

            }

        }
    }

    /**
     * 收藏
     */
    public function shoucang($param)
    {
        $uid = $param['userid'];//用户编号
        $type = $param['type'];//type=1时，收藏的是医生；type=2时，收藏的是医院
        $id = $param['id'];//医生编号或者医院编号
        $obj = array(
            "userid" => $uid,
            "becollect" => $id,
            "type" => $type,
            "state" => 1
        );
        $insert = M('api_collection')->add($obj);
        if ($insert === false) {
            return array('state' => "fail");
        } else {
            return array('state' => "success");
        }

    }

    /**
     * 取消收藏
     */
    public function quxiao($param)
    {
        $uid = $param['userid'];//用户编号
        $type = $param['type'];//type=1时，收藏的是医生；type=2时，收藏的是医院
        $id = $param['id'];//医生编号或者医院编号
        $insert = M('api_collection')->where(array("becollect" => $id, "type" => $type, "userid" => $uid))->delete();

        if ($insert === false) {
            return array('state' => "fail");
        } else {
            return array('state' => "success");
        }

    }
}