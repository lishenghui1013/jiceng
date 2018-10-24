<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/4
 * Time: 14:30
 */

namespace Home\Api;

use Admin\Model\ApiAppModel;
use Home\ORG\ApiLog;
use Home\ORG\Crypt;
use Home\ORG\Response;
use Home\ORG\ReturnCode;

class Consulting extends Base
{
    /**
     *提问
     */
    public function upload($param)
    {
        //获取网站根目录地址$url
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $str = substr($PHP_SELF, 1);
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . substr($str, 0, strpos($str, '/') + 1);
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();   // 实例化上传类
            $upload->maxSize = 3145728;    // 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->rootPath = THINK_PATH;          // 设置附件上传根目录
            $upload->savePath = '../Public/';    // 设置附件上传（子）目录
            $upload->subName = 'uploads/tiwen/';  //子文件夹
            /*$upload->saveName = date('Ymdhis');*/     //文件名
            $upload->replace = true;  //同名文件是否覆盖
            // 上传文件
            $images = $upload->upload();
            //判断是否有图
            if ($images) {
                /*$info = $images['img']['savepath'] . $images['img']['savename'];*/
                $info = '';
                foreach ($images as $key => $tepimg) {
                    $info .= $url . preg_replace('/^..\//','',$tepimg['savepath']) . $tepimg['savename'] . ';';//拼接图片地址
                }
                unset($key, $tepimg);
                $info = substr($info, 0, -1);
                $title = $param['content'];
                $userid = $param['userid'];
                $neirong = $param['neirong'];
                Response::debug($title . '+' . $userid . '+' . $info);
                $list = D('api_users')->field('id')->where(array('id' => $userid))->getField("username");
                // $user1 = M("select * from api_tiwen where userid='$userid' order by id DESC ");
                // $select = $user1->getField('id');


                $username = $list;
                $time = date("Y-m-d H:i:s", time());
                $obj = array(
                    "content" => $title,
                    "photo" => $info,
                    "userid" => $userid,
                    "username" => $username,
                    "pubtime" => $time,
                    "doctorid" => 'all',
                    "neirong" => $neirong
                );

                $num = M('api_tiwen')->where(array('userid' => $userid))->count();
                if ($num >= 1) {
                    $insert = M('api_tiwen')->add($obj);
                    $last_id = M('api_tiwen')->where(array('userid' => $userid))->order('id desc')->limit(1)->find();

                    if ($insert === false) {
                        return array('phone' => "fail");
                    } else {
                        $data['state'] = 'true';
                        $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);

                        $oneid = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => 'all'))->order('id asc')->limit(1)->find();
                        $id_id['id_id'] = $oneid['id'];
                        $sta = M('api_tiwen')->where(array('id' => $last_id['id']))->save($id_id);


                        return array('phone' => "succeess", 'tid' => $last_id['id']);
                    }
                } else {
                    $insert = M('api_tiwen')->add($obj);
                    $last_id = M('api_tiwen')->where(array('userid' => $userid))->order('id desc')->limit(1)->find();

                    if ($insert === false) {
                        return array('phone' => "fail");
                    } else {


                        $data['state'] = 'true';
                        $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);

                        /* $oneid =M('api_tiwen')->where(array('userid'=>$userid,'doctorid'=>'all'))->order('id asc')->limit(1)->find();*/
                        $id_id['id_id'] = $last_id['id'];
                        $sta = M('api_tiwen')->where(array('id' => $last_id['id']))->save($id_id);


                        return array('phone' => "succeess", 'tid' => $last_id['id']);
                    }

                }

                //echo json_encode($info);
            } else {
                $a = $upload->getError();//获取失败信息
                echo json_encode($a);
            }
        } else {
            $title = $param['content'];
            $userid = $param['userid'];
            $neirong = $param['neirong'];
            Response::debug($title . '+' . $userid);
            $list = D('api_users')->field('id')->where(array('id' => $userid))->getField("username");
            $username = $list;
            $time = date("Y-m-d H:i:s", time());


            // $user1 = M("select * from api_tiwen where userid='$userid' order by id DESC ")->getField('id');


            $obj = array(
                "content" => $title,
                "userid" => $userid,
                "username" => $username,
                "pubtime" => $time,
                "doctorid" => 'all',
                "neirong" => $neirong
            );
            $num = M('api_tiwen')->where(array('userid' => $userid))->count();
            if ($num >= 1) {
                $insert = M('api_tiwen')->add($obj);
                $last_id = M('api_tiwen')->where(array('userid' => $userid))->order('id desc')->limit(1)->find();
                if ($insert === false) {
                    return array('phone' => "fail");
                } else {
                    $data['state'] = 'true';
                    $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);
                    $oneid = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => 'all'))->order('id asc')->limit(1)->find();
                    $id_id['id_id'] = $oneid['id'];
                    $sta = M('api_tiwen')->where(array('id' => $last_id['id']))->save($id_id);
                    Response::debug(111111);
                    return array('phone' => "succeess", 'tid' => $last_id['id']);

                }
            } else {
                $insert = M('api_tiwen')->add($obj);
                $last_id = M('api_tiwen')->where(array('userid' => $userid))->order('id desc')->limit(1)->find();
                if ($insert === false) {
                    return array('phone' => "fail");
                } else {
                    $data['state'] = 'true';
                    $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);
                    $id_id['id_id'] = $last_id['id'];
                    $sta = M('api_tiwen')->where(array('id' => $last_id['id']))->save($id_id);
                    Response::debug(22222222222222);
                    return array('phone' => "succeess", 'tid' => $last_id['id']);
                }
            }

        }

    }

    /**
     *首页提问列表
     */
    public function tiwenIndex($param)
    {
        $uid = $param['userid'];
        $listIn = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id,u.username,u.headphoto,content,pubtime')->where("userid='$uid'" . 'and ' . "t.doctorid='all'")->order('t.id desc')->select();

        foreach ($listIn as $key => $u) {
            $tid = $u['id'];
            $li = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id,u.username,u.headphoto,content,pubtime,t.state')->where(array('t.id_id' => $tid))->order('t.id desc')->getField('t.id');
            $list = D('api_tiwen_reply as t')->field('state')->where(array('tiwenid' => $li))->order('id desc')->find();
            if ($list['state'])
                $listIn[$key]['state'] = $list['state'];
            else

                $listIn[$key]['state'] = 'false';
        }
        ApiLog::setApiInfo($listIn);
        if (empty($listIn)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listIn;
    }

    /**
     *首页提问医生回答列表
     */
    public function tiwenIndexDetile($param)
    {
        $id = $param['id'];
        //$li = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id,u.username,u.headphoto,content,pubtime,t.state')->where(array('t.id_id'=>$id))->order('t.id desc')->getField('t.id');

        $listIn = D('api_tiwen_reply as r')->join('api_doctor as d on d.id=r.doctorid')->field('d.id as did,d.pic,d.doctorname,r.content,r.pubtime')->where(array('r.tiwenid' => $id))->group('doctorid')->select();

        foreach ($listIn as $key => $u) {
            $listIn[$key]['id'] = $id;
        }
        ApiLog::setApiInfo($listIn);
        if (empty($listIn)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        }
        return $listIn;
    }

    /**
     *首页提问列表详情
     */
    public function tiwenDetiles($param)
    {
        $id = $param['id'];
        $did = $param['did'];
        $listIn = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id,u.username,u.headphoto,t.content,t.pubtime')->where(array('t.id' => $id))->find();
        $listInfo = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id as tid,t.content,t.pubtime')->where("t.id_id=" . $id)->select();

        $list = D('api_tiwen as t')->join('api_users as u on u.id=t.userid')->field('t.id as tid,t.content,t.pubtime')->where(array('t.id_id' => $id))->order('t.id desc')->getField('t.id');
        $data['state'] = 'false';
        $res = D('api_tiwen_reply')->where(array('tiwenid' => $list))->save($data);
        if (empty($listInfo)) {
            Response::error(ReturnCode::INVALID, '暂无数据');
        } else {
            foreach ($listInfo as $key => $u) {
                $list = D('api_tiwen_reply')->field('content as rcontent,pubtime as rpubtime')->where("tiwenid=" . $u['tid'] . ' and  doctorid=' . $did)->select();

                $listInfo[$key]['reply'] = $list;
                $listIn['children'] = $listInfo;
            }

        }
//         echo "<pre>";
//        var_dump($listIn);die();
        return $listIn;
    }

    /**
     * 在医生列表的咨询
     */
    public function tiwenDoc($param)
    {
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();   // 实例化上传类
            $upload->maxSize = 3145728;    // 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->rootPath = THINK_PATH;          // 设置附件上传根目录
            $upload->savePath = '../Public/';    // 设置附件上传（子）目录
            $upload->subName = 'uploads/tiwen/';  //子文件夹
            $upload->saveName = date('Ymdhis');     //文件名
            $upload->replace = true;  //同名文件是否覆盖
            // 上传文件
            $images = $upload->upload();
            //return $images;
            //判断是否有图
            if ($images) {
                $info = $images['img']['savepath'] . $images['img']['savename'];
                $title = $param['content'];
                $userid = $param['userid'];
                $docid = $param['docid'];
                Response::debug($title . '+' . $userid . '+' . $info);
                $list = D('api_users')->field('id')->where(array('id' => $userid))->getField("username");
                // $user1 = M("select * from api_tiwen where userid='$userid' order by id DESC ");
                // $select = $user1->getField('id');


                $username = $list;
                $time = date("Y-m-d H:i:s", time());
                $obj = array(
                    "content" => $title,
                    "photo" => $info,
                    "userid" => $userid,
                    "username" => $username,
                    "pubtime" => $time,
                    "doctorid" => $docid,

                );

                $num = M('api_tiwen')->where(array('userid' => $userid))->count();
                if ($num > 0) {
                    $insert = M('api_tiwen')->add($obj);
                    $last_id = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => $docid))->order('id desc')->limit(1)->find();

                    if ($insert === false) {
                        return array('phone' => "fail");
                    } else {
                        $data['state'] = 'true';
                        $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);

                        $oneid = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => $docid))->order('id asc')->limit(1)->find();
                        $id_id['id_id'] = $oneid['id'];
                        $sta = M('api_tiwen')->where(array('id' => $oneid['id']))->save($id_id);


                        return array('phone' => "succeess", 'tid' => $last_id['id']);
                    }
                } else {
                    $insert = M('api_tiwen')->add($obj);
                    $last_id = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => $docid))->order('id desc')->limit(1)->find();

                    if ($insert === false) {
                        return array('phone' => "fail");
                    } else {
                        $data['state'] = 'true';
                        $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);
                        $oneid = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => $docid))->order('id asc')->limit(1)->find();
                        $id_id['id_id'] = $oneid['id'];
                        $sta = M('api_tiwen')->where(array('id' => $last_id['id']))->save($id_id);


                        return array('phone' => "succeess", 'tid' => $last_id['id']);
                    }

                }

                //echo json_encode($info);
            } else {
                $a = $upload->getError();//获取失败信息
                echo json_encode($a);
            }
        } else {
            $title = $param['content'];
            $userid = $param['userid'];
            $docid = $param['docid'];
            Response::debug($title . '+' . $userid);
            $list = D('api_users')->field('id')->where(array('id' => $userid))->getField("username");
            $username = $list;
            $time = date("Y-m-d H:i:s", time());


            // $user1 = M("select * from api_tiwen where userid='$userid' order by id DESC ")->getField('id');


            $obj = array(
                "content" => $title,
                "userid" => $userid,
                "username" => $username,
                "pubtime" => $time,
                "doctorid" => $docid,

            );
            $num = M('api_tiwen')->where(array('userid' => $userid))->count();
            if ($num > 0) {
                $insert = M('api_tiwen')->add($obj);
                $last_id = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => $docid))->order('id desc')->limit(1)->find();
                if ($insert === false) {
                    return array('phone' => "fail");
                } else {
                    $data['state'] = 'true';
                    $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);
                    $oneid = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => $docid))->order('id asc')->limit(1)->find();
                    $id_id['id_id'] = $oneid['id'];
                    $sta = M('api_tiwen')->where(array('id' => $last_id['id']))->save($id_id);
                    return array('phone' => "succeess", 'tid' => $last_id['id']);
                }
            } else {
                $insert = M('api_tiwen')->add($obj);
                $last_id = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => $docid))->order('id desc')->limit(1)->find();
                $oneid = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => $docid))->order('id asc')->limit(1)->find();
                if ($insert === false) {
                    return array('phone' => "fail");
                } else {
                    $data['state'] = 'true';
                    $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);
                    $id_id['id_id'] = $oneid['id'];
                    $sta = M('api_tiwen')->where(array('id' => $last_id['id']))->save($id_id);

                    return array('phone' => "succeess", 'tid' => $last_id['id']);
                }
            }

        }
    }

    /**
     * 继续提问（咨询）
     */
    public function tiwen($param)
    {
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();   // 实例化上传类
            $upload->maxSize = 3145728;    // 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->rootPath = THINK_PATH;          // 设置附件上传根目录
            $upload->savePath = '../Public/';    // 设置附件上传（子）目录
            $upload->subName = 'uploads/tiwen/';  //子文件夹
            $upload->saveName = date('Ymdhis');     //文件名
            $upload->replace = true;  //同名文件是否覆盖
            // 上传文件
            $images = $upload->upload();
            //return $images;
            //判断是否有图
            if ($images) {
                $info = $images['img']['savepath'] . $images['img']['savename'];
                $title = $param['content'];//咨询内容
                $userid = $param['userid'];//用户编号
                $did = $param['docid'];//医生编号
                $tid = $param['tid'];//上一个问题的提问编号
                $neirong = $param['neirong'];
                Response::debug($title . '+' . $userid . '+' . $info);
                $list = D('api_users')->field('id')->where(array('id' => $userid))->getField("username");
                $username = $list;
                $time = date("Y-m-d H:i:s", time());
                $obj = array(
                    "content" => $title,
                    "photo" => $info,
                    "userid" => $userid,
                    "username" => $username,
                    "pubtime" => $time,
                    "doctorid" => 'all',
                    "id_id" => $tid,
                    "neirong" => $neirong
                );
                $insert = M('api_tiwen')->add($obj);

                $last_id = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => 'all'))->order('id desc')->limit(1)->find();
                $data['state'] = 'true';
                $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);
                if ($insert === false) {
                    return array('state' => "fail");
                } else {
                    return array('state' => "succeess");
                }
                //echo json_encode($info);
            } else {
                $a = $upload->getError();//获取失败信息
                echo json_encode($a);
            }
        } else {
            $title = $param['content'];//咨询内容
            $userid = $param['userid'];//用户编号
            $did = $param['docid'];//医生编号
            $tid = $param['tid'];//上一个问题的提问编号
            $neirong = $param['neirong'];
            Response::debug($title . '+' . $userid);
            $list = D('api_users')->field('id')->where(array('id' => $userid))->getField("username");
            $username = $list;
            $time = date("Y-m-d H:i:s", time());
            $obj = array(
                "content" => $title,
                "userid" => $userid,
                "username" => $username,
                "pubtime" => $time,
                "doctorid" => 'all',
                "id_id" => $tid,
                "neirong" => $neirong
            );
            $insert = M('api_tiwen')->add($obj);

            $last_id = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => 'all'))->order('id desc')->limit(1)->find();
            $data['state'] = 'true';
            $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);
            if ($insert === false) {
                return array('state' => "fail");
            } else {
                return array('state' => "succeess");
            }
        }
    }

    /**
     * 继续提问（咨询）
     */
    public function zixuntiwen($param)
    {
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();   // 实例化上传类
            $upload->maxSize = 3145728;    // 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->rootPath = THINK_PATH;          // 设置附件上传根目录
            $upload->savePath = '../Public/';    // 设置附件上传（子）目录
            $upload->subName = 'uploads/tiwen/';  //子文件夹
            $upload->saveName = date('Ymdhis');     //文件名
            $upload->replace = true;  //同名文件是否覆盖
            // 上传文件
            $images = $upload->upload();
            //return $images;
            //判断是否有图
            if ($images) {
                $info = $images['img']['savepath'] . $images['img']['savename'];
                $title = $param['content'];//咨询内容
                $userid = $param['userid'];//用户编号
                $did = $param['docid'];//医生编号
                $tid = $param['tid'];//上一个问题的提问编号
                $neirong = $param['neirong'];
                Response::debug($title . '+' . $userid . '+' . $info);
                $list = D('api_users')->field('id')->where(array('id' => $userid))->getField("username");
                $username = $list;
                $time = date("Y-m-d H:i:s", time());
                $obj = array(
                    "content" => $title,
                    "photo" => $info,
                    "userid" => $userid,
                    "username" => $username,
                    "pubtime" => $time,
                    "doctorid" => $did,
                    "id_id" => $tid,
                    "neirong" => $neirong
                );
                $insert = M('api_tiwen')->add($obj);

                $last_id = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => $did))->order('id desc')->limit(1)->find();
                $data['state'] = 'true';
                $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);
                if ($insert === false) {
                    return array('state' => "fail");
                } else {
                    return array('state' => "succeess");
                }
                //echo json_encode($info);
            } else {
                $a = $upload->getError();//获取失败信息
                echo json_encode($a);
            }
        } else {
            $title = $param['content'];//咨询内容
            $userid = $param['userid'];//用户编号
            $did = $param['docid'];//医生编号
            $tid = $param['tid'];//上一个问题的提问编号
            $neirong = $param['neirong'];
            Response::debug($title . '+' . $userid);
            $list = D('api_users')->field('id')->where(array('id' => $userid))->getField("username");
            $username = $list;
            $time = date("Y-m-d H:i:s", time());
            $obj = array(
                "content" => $title,
                "userid" => $userid,
                "username" => $username,
                "pubtime" => $time,
                "doctorid" => $did,
                "id_id" => $tid,
                "neirong" => $neirong
            );
            $insert = M('api_tiwen')->add($obj);

            $last_id = M('api_tiwen')->where(array('userid' => $userid, 'doctorid' => $did))->order('id desc')->limit(1)->find();
            $data['state'] = 'true';
            $res = D('api_tiwen')->where(array('id' => $last_id['id']))->save($data);
            if ($insert === false) {
                return array('state' => "fail");
            } else {
                return array('state' => "succeess");
            }
        }
    }


}