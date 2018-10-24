<?php

/**
 * 知识问答相关的全部接口
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

class Knowledge extends Base
{
    /* 
    知识问答题干列表
	*/
    public function questions($param)
    {

        $userid = $param['userid'];
        //查询答题状态(24小时内只能答题一次)
        $time = D('api_questions_result')->field('ctime')->where(array('cuser'=>$userid))->order('id desc')->limit(1)->find();//答题人最后一次答题时间
        if(0){
            /*$limit_time = time()-$time['ctime'];
            if($limit_time<86400){
                $return['state'] = 2;//今天不能再答题
            }else{
                $return['state'] = 1;//今天可以答题
            }*/
        }else{
            $return['state'] = 1;//今天可以答题
        }


        $list = D('api_questions')->where(array('questionstatus' => 0))->select();
        if (empty($list)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        foreach ($list as $key => $val) {
            $list[$key]['questionselect'] = explode('|', $val['questionselect']);
        }
        $count = D('api_questions')->where(array('questionstatus' => 0))->count();

        $return['total'] = $count;
        $return['list'] = $list;

        return $return;
    }

    /*
     知识答题结果
    */
    public function questionresult($param)
    {
        $score = $param['result'];
        $userid = $param['userid'];
        //日志打印
        Log::record($score, Log::DEBUG);
        $time = D('api_questions_result')->field('ctime')->where(array('cuser'=>$userid))->order('id desc')->limit(1)->find();//答题人最后一次答题时间
        /*if($time){
            $limit_time = time()-$time['ctime'];
            if($limit_time<86400){
                exit;
            }
        }*/
        $arr = explode('-', $score);
        $i = 0;//答对题的个数
        $jifen1 = 0.0;//答对题所得总积分
        foreach ($arr as $k => $v) {
            $a = explode('.', $v);
            $id = $a[0];
            $answer = D('api_questions')->where("id='$id'")->field('id,questionanswer')->find();//正确答案信息
            $jifen = D('api_questions')->where("id='$id'")->field('jifen')->find();//答对题所得积分
            $answer = array_values($answer);//数组转换
            $answer = implode('.', $answer);
            if ($v == $answer) {
                $i++;
                $jifen1 += $jifen['jifen'];
            }
        }

        $money = D('api_users')->where("id='$userid'")->find();
        $money['account'] += $jifen1;
        $questions_num = D('api_questions')->field('count(id) as num')->where(array('questionstatus'=>0))->find();//问题总个数
        $data = array();
        $data['answer'] = $i.'|'.$questions_num['num'];
        $data['jifen'] = $jifen1;//答题结果所得总积分
        $data['ctime'] = time();//答题时间
        $data['cuser'] = $userid;//答题用户id

        $insert = D('api_questions_result')->add($data);
        if($insert){
            $res = D('api_users')->where("id='$userid'")->save($money);
                $result = '恭喜您答对' . $i . '道题,获得' . $jifen1 . '积分!';

        }else{

                $result = '提交失败!';

        }

        return $result;
    }

}