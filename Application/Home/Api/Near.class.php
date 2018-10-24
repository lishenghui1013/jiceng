<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/12
 * Time: 17:15
 */
namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
class Near extends Base{
    /**
     *附近
     */
    public function nearlist($param)
    {
        $lng= $param['lng'];//我的经度
       $lat=$param['lat'];//我的纬度
        //诊所的经纬度
        /*$listInfo = D('api_hospital as h')->join('left join api_hosdepartrelation as a on h.id=a.hid')->field("lng,lat,h.id,h.hospitalname,h.hos_phone,h.hos_address")->select();*/
        $Model = new \Think\Model();
        $sql = 'select lng,lat,h.id,h.hospitalname,h.hos_phone,h.hos_address from api_hospital as h right join api_hosdepartrelation as a on h.id=a.hid group by a.hid';
        $listInfo = $Model->query($sql);
        //$listIn = D('api_hospital as h')->join('api_hosdepartrelation as a on h.id=a.hid')->join('api_department as d on d.id=a.did');

        $distance='1000';
        $result=array();
        foreach ($listInfo as $key => $value)
        {
            if ($value['lng'] >0 && $value['lat'] >0 ) {
                $dis = $this->getDistance($value['lng'],$value['lat'],$lng,$lat);
                //echo $dis;
                if ($dis > $distance) {
                   continue;
                }
                else{
                    $value['distance']=$dis;
                    //unset($listInfo[$key]);
                    $result[]=$value;

                  // if(in_array('distance',$value));
                       //unset($listInfo[$key]);
                }

            }
        }

     return $result;

    }
    function GetDistance($lng1,$lat1,$lng2,$lat2){
        //将角度转为狐度
        $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
        $radLat2=deg2rad($lat2);
        $radLng1=deg2rad($lng1);
        $radLng2=deg2rad($lng2);
        $a=$radLat1-$radLat2;
        $b=$radLng1-$radLng2;
        //L=α（弧度）× r(半径) （弧度制）  L是弧长
        $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
        return floor($s);
    }
}