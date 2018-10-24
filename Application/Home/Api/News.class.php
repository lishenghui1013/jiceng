<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/29
 * Time: 14:10
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;
use Home\Model\TclassModel;

class News extends Base
{
    /**
     *热点新闻
     */
    public function lists($param)
    {
        $type = $param['type'];//type=6头条新闻;8技能提升;9前沿新闻;10患者关注
        Response::debug($type);
        $listInfo = D('api_article as a')->join('api_articletype as t on t.id=a.type')->field('a.id,a.title,a.pic,a.publishtime,t.typename')->where(array('a.type' => $type, 'ifshow' => 0))->select();
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        foreach ($listInfo as $key => $val) {
            $arr = explode('||', $val['pic']);
            $listInfo[$key]['pic'] = array_filter($arr);
            $listInfo[$key]['pic'] = $listInfo[$key]['pic'][0];
            $listInfo[$key]['publishtime'] = date('Y-m-d', $val['publishtime']);
        }
        return $listInfo;
    }

    /**
     *热点新闻详情
     */
    public function details($param)
    {
        $id = $param['id'];//文章编号
        $docid = $param['docid'] ? $param['docid'] : 0;//医生id
        if (empty($param['id'])) {
            Response::error(ReturnCode::EMPTY_PARAMS, '缺少id');
        }
        $listInfo = D('api_article')->where(array('id' => $id, 'ifshow' => 0))->find();
        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }

        $arr = explode('||', $listInfo['pic']);
        $listInfo['pic'] = array_filter($arr);
        $listInfo['pic'] = $listInfo['pic'];
        $listInfo['publishtime'] = date('Y-m-d', $listInfo['publishtime']);

        $list = D('api_awenazhang as a')->join('api_doctor as d on d.id=a.userid')->field('pic,doctorname,contment')->where(array('artid' => $id))->select();
        $listInfo['pinlunlist'] = $list;

        $content = htmlspecialchars_decode($listInfo['content'], ENT_QUOTES);
        $content = strip_tags($content);
        $listInfo['content'] = $content;
        $have = D('api_collectartic as c')->join('left join api_doctor as d on d.id=c.docid')->join('left join api_article as a on a.id=c.articid')->field('c.id')->where(array('c.docid' => $docid, 'c.articid' => $id))->select();
        $listInfo['state'] = $have ? 1 : 0;

        return $listInfo;
    }

    /**
     *热点列表
     */
    public function liebiao($param)
    {
        $type = $param['type'];//type=6头条新闻;8技能提升;9前沿新闻;10患者关注
        $page = $param['page'] ? $param['page'] : 1;
        $list = D('api_article')->where(array('ifshow' => 0, 'type' => $type))->order("publishtime desc")->page("$page,10")->select();
        if (empty($list)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        foreach ($list as $key => $val) {
            $list[$key]['publishtime'] = date('Y-m-d', $val['publishtime']);
        }
        $count = D('api_article')->where(array('ifshow' => 0))->count();
        ApiLog::setAppInfo($list);
        $return['total'] = $count;
        $return['list'] = $list;
        return $return;
    }

    /**
     *头条新闻
     */
    public function toutiao()
    {
        $listInfo = D('api_article as a')->join('api_articletype as t on t.id=a.type')->field('a.id,a.title,a.pic,a.publishtime,t.typename')->where(array('ifshow' => 0))->limit(1, 3)->select();
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        foreach ($listInfo as $key => $val) {
            $arr = explode('||', $val['pic']);
            $listInfo[$key]['pic'] = array_filter($arr);
            $listInfo[$key]['pic'] = $listInfo[$key]['pic'];
            $listInfo[$key]['publishtime'] = date('Y-m-d', $val['publishtime']);
        }
        return $listInfo;
    }

    /**
     * 轮播图
     */
    public function lunbo($param)
    {
        $type = $param['type'] = "10";//type=6头条新闻;8技能提升;9前沿新闻;10患者关注
        $listInfo = D('api_article as a')->join('api_articletype as t on t.id=a.type')->field('a.id,a.pic')->where(array('a.type' => $type, 'ifshow' => 0))->select();
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        foreach ($listInfo as $key => $val) {
            $arr = explode('||', $val['pic']);
            $listInfo[$key]['pic'] = array_filter($arr);
            $listInfo[$key]['pic'] = $listInfo[$key]['pic'][0];
        }
        return $listInfo;
    }

    /**
     * 轮播新闻
     */
    public function xinwenLB($param)
    {
        $type = $param['type'] = '11';//type=6头条新闻;8技能提升;9前沿新闻;10患者关注
        $listInfo = D('api_article as a')->join('api_articletype as t on t.id=a.type')->field('a.id,a.title,a.pic')->where(array('a.type' => $type, 'ifshow' => 0))->select();
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        foreach ($listInfo as $key => $val) {
            $arr = explode('||', $val['pic']);
            $listInfo[$key]['pic'] = array_filter($arr);
            $listInfo[$key]['pic'] = $listInfo[$key]['pic'][0];
        }
        return $listInfo;
    }

    /*
     * 医生角色首页轮播图
     */
    public function lunbotu()
    {
        //$type=$param['type'];//type=6头条新闻;8技能提升;9前沿新闻;10患者关注
        $listInfo = D('api_article as a')->join('api_articletype as t on t.id=a.type')->field('a.id,a.pic')->where(array('ifshow' => 0))->limit(2, 3)->select();
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        foreach ($listInfo as $key => $val) {
            $arr = explode('||', $val['pic']);
            $listInfo[$key]['pic'] = array_filter($arr);
            $listInfo[$key]['pic'] = $listInfo[$key]['pic'][0];
        }
        return $listInfo;
    }

    /**
     * 搜索
     */
    public function sousuo($param)
    {

        $name = $param['title'];//用户搜索内容
        $data['title'] = array('like', "%$name%");
        $data['ifshow'] = 0;
        $listInfo = D('api_article as a')->join('api_articletype as t on t.id=a.type')->field('a.id,a.title,a.pic,a.publishtime,t.typename')->where($data)->select();

        ApiLog::setApiInfo($listInfo);
        if (empty($listInfo)) {
            //Response::error(ReturnCode::INVALID, '暂无数据');
            $na = mb_substr($name, 0, 1, 'utf-8');
            $data['title'] = array('like', "%$na%");
            $data['ifshow'] = 0;
            $listInfo = D('api_article as a')->join('api_articletype as t on t.id=a.type')->field('a.id,a.title,a.pic,a.publishtime,t.typename')->where($data)->select();
        }
        foreach ($listInfo as $key => $val) {
            $arr = explode('||', $val['pic']);
            $listInfo[$key]['pic'] = array_filter($arr);
            $listInfo[$key]['pic'] = $listInfo[$key]['pic'][0];
            $listInfo[$key]['publishtime'] = date('Y-m-d', $val['publishtime']);
        }
        ApiLog::setAppInfo($listInfo);
        return $listInfo;
    }

    /**
     *评论
     */

    public function pinglun($param)
    {
        $id = $param['id'];//文章编号
        $userid = $param['docid'];//用户编号
        $cotment = $param['cotment'];//评论内容
        $obj = array(
            "artid" => $id,
            "contment" => $cotment,
            "userid" => $userid,
        );
        $res = M('api_awenazhang')->add($obj);
        if ($res) {
            return array('state' => 'success');
        } else {
            return array('state' => 'fail');
        }

    }
    /**
     * 医生角色搜索文章.
     * 作者: 李胜辉
     * 时间:2018年10月08日
     */
    public function search($param){
        $title = $param['title'];//文章标题
        Response::debug($title);
        //如果搜索内容为空,查询所有的文章
        if($title!==''){
            $listInfo = D('api_article as a')->join('api_articletype as t on t.id=a.type')->field('a.id,a.title,a.pic,a.publishtime,t.typename')->where('ifshow=0 and a.title like "%'.$title.'%"')->select();
        }else{ //否则按照搜索内容进行模糊查询
            $listInfo = D('api_article as a')->join('api_articletype as t on t.id=a.type')->field('a.id,a.title,a.pic,a.publishtime,t.typename')->where(array('ifshow' => 0))->select();

        }
        //如果查不到数据
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }

        foreach ($listInfo as $key => $val) {
            $arr = explode('||', $val['pic']);
            $listInfo[$key]['pic'] = array_filter($arr);
            $listInfo[$key]['pic'] = $listInfo[$key]['pic'][0];//得到一张图片地址
            $listInfo[$key]['publishtime'] = date('Y-m-d', $val['publishtime']);//发布时间格式化
        }
        ApiLog::setAppInfo($listInfo);
        return $listInfo;

    }

}