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

class Health extends Base {
    /* 
    健康知识列表
	*/
    public function arlist($param) {
		//$param['type']=2;
        if (empty($param['type'])) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少type');
        }
		$type=$param['type'];
		//日志打印
		Log::record($type, Log::DEBUG);
		$page=$param['page']?$param['page']:1;
        $list = D('api_article')->where(array('ifshow' => 0,'type'=>$type))->order("publishtime desc")->page("$page,10")->select();
        if (empty($list)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
		$count=D('api_article')->where(array('ifshow' => 0))->count();
		foreach($list as $key=>$val){
			$arr=explode('||',$val['pic']);
			$list[$key]['pic']=array_filter($arr);
			$list[$key]['pic']=$list[$key]['pic'][0];
			$list[$key]['publishtime']=date('Y-m-d',$val['publishtime']);
		}
        ApiLog::setAppInfo($list);
        $return['total'] = $count;
        $return['list'] = $list;
        

        return $return;
    }
	//详细页
	public function artinfo($param){
	   
		$aid=$param['aid'];
		if (empty($param['aid'])) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少文章id');
        } 
		//日志打印
		Log::record($aid, Log::DEBUG);
		//$page=$param['page']?$param['page']:1;
        $list = D('api_article')->where(array('ifshow' => 0,'id'=>$aid))->order("publishtime desc")->find();
        if (empty($list)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
		$list['publishtime']=date('Y-m-d',$list['publishtime']);
		$list['content']=strip_tags(htmlspecialchars_decode($list['content']));
	    $list['pic']=explode('||',$list['pic']);
		$list['pic']=array_filter($list['pic']); 
		//unset($list['pic']);
		$list['commentslist']=D('api_articlecomment as a')->join('left join api_users as u on u.id=a.userid')->field('a.id,a.comment,a.comtime,a.beid,a.userid,a.username,u.headphoto')->where("a.beid='$aid'")->select();
		
		$list['commentsnum']=count($list['commentslist']);
		//$count=D('api_article')->where(array('ifshow' => 0))->count();
        ApiLog::setAppInfo($list);
        //$return['total'] = $count;
        $return['list'] = $list;
        

        return $return;
	}
	/* 
	体质检测列表 
	*/
	public function healthlist($param){
		$page=$param['page']?$param['page']:1;
		$list=D('api_health')->where("status=1")->field('id,title,healthtype,ifcontrary')->select();
		$total=D('api_health')->count();
		if (empty($list)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
		//print_r($list);
		$result['total']=$total;
		$result['list']=$list;
		
		return $result;
	}
	/* 
	获取检测结果
	*/
	public function getresult($param){
		$score=$param['result'];
		//日志打印
		Log::record($score, Log::DEBUG);
		//$score='12.1-12.1-12.1-12.1-12.1-12.1-11.1-11.1-11.1-11.1';
		$arr=explode('-',$score);
		$arr2=array();
		//print_r($arr);
		foreach($arr as $key=>$val){
			
			$arr1=explode('.',$val);
			$num=$arr1[0];
			$arr2[$num]['original']+=$arr1[1];
			$arr2[$num]['number']=substr_count($score,"$num");
		}
		//print_r($arr2);
        $health = '';
		foreach($arr2 as $key=>$val){
			$a=(int)$val['original'];
			
			$c=(int)$val['number'];
			
			$b=(($a-$c)/($c*4))*100;
			//echo $b;
			$arr2[$key]['original']=$b;
			if($key==12){      //平和体质
				if($b>=60){
					$flag1=true;   //转化分大于60
				}else{
					$flag1=false;   //转化分小于60
				}
			}else{     //偏颇体质
			$healthtype=D('api_healthtype')->where("id='$key'")->find();
				if($b>=40){         //转化分大于40
					$flag2=true;
					$health.='兼'.$healthtype['healthname'];
					$result[]=$healthtype;
				}elseif($b>=30&&$b<=39){ //转化分30到39
					$flag3=true;
					$health.='倾向'.$healthtype['healthname'];
					$result[]=$healthtype;
				}else{
					$flag4=true;  //转化分小于30
					
				}
			}
		}
		//echo $flag1.$flag2.$flag3;
		if($flag1&&!$flag2&&!$flag3){
			$health.='平和体质';
			$result[]=D('api_healthtype')->where("id=4")->find();
		}elseif($flag1&&!$flag2){
			$health.='基本平和体质';
			$result[]=D('api_healthtype')->where("id=4")->find();
		}else{
			$health.='平和体质';
			$result[]=D('api_healthtype')->where("id=4")->find();
		}
		if (empty($result)) {
            Response::error(ReturnCode::INVALID, '提交错误');
        }
		$result1['healthtype']=$health;
		$result1['health']=$result;
		
		//echo $a;
		//$b=(($a-6)/(6*4))*100;
		//echo $b;
		//print_r($arr2);
		//print_r($result1);
		return $result1;
	}
	/* 健康知识评论 */
	public function comment($param){
		$data['userid']=$param['userid'];
		$data['beid']=$param['articleid'];
		$data['comment']=$param['content'];
		$data['comtime']=time();
		$data['username']=$param['username'];
		$res=D('api_articlecomment')->add($data);
		if (!$res) {
            Response::error(ReturnCode::INVALID, '评论失败');
        }
		return array('msg'=>'评论成功');
		
	}

}