<?php

namespace Admin\Controller;
use Home\ORG\ApiLog;
/**
 * 提问控制器
 */
class AskController extends BaseController {

    /**
     * 提问列表
     * 
     */
    public function index() {
        $list = D('api_tiwen as a')->join("left join api_tiwen_reply as r on a.id=r.tiwenid")->field("a.id,a.content,a.pubtime,a.userid,a.username")->group("a.id")->select();
        
		$this->assign('list', $list);
        $this->display();
    }

    /**
     * 查看提问 ，回答问题
     */
    public function edit() {
        if (IS_GET) {
            
            $id = I('get.id');
            $listInfo=D('api_tiwen')->where("id='$id'")->find();
            $img=unserialize($listInfo['pic']);
			$reply=D('api_tiwen_reply')->where("tiwenid='$id'")->select();
			$this->assign('reply',$reply);
            $this->assign('detail', $listInfo);
			$this->assign('img',$img);
            //print_r($_SESSION);
            $this->display('add');
        } elseif (IS_POST) {
            $postData = I('post.');
			//print_r($postData);die;
			$postData['tiwenid']=$postData['id'];
			unset($postData['id']);
			$postData['pubtime']=time();
			$postData['doctorid']=$_SESSION['uid'];
			//$aa=D('api_doctor')->where()//医生姓名
			//$postData['doctorid']=$dname;
			//print_r($postData);die;
            $res = D('api_tiwen_reply')->add($postData);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('回复成功');
            }
        }
    }

    public function del() {
        $id = I('post.id');
        $childNum = D('api_tiwen')->where(array('id' => $id))->count();
        if ($childNum) {
			D('api_tiwen')->where(array('id' => $id))->delete();
            $this->ajaxSuccess('删除成功');
            
        } else {
            $this->ajaxError("当前文章不存在");
        }
    }
	/**
     * 文章类型列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function typelist() {
        $list = D('api_articletype')->order('sort desc')->select();
        $this->assign('list', $list);
        $this->display();
    }
	/**
     * 新增类型
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function addtype() {
        if (IS_POST) {
            $data = I('post.');
            $data['addtime'] = time();
            $res = D('api_articletype')->add($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        } else {
           
            $this->display();
        }
    }
	/**
     * 编辑类型
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edittype() {
        if (IS_GET) {
            $id = I('get.id');
            $listInfo=D('api_articletype')->where("id='$id'")->find();
            $this->assign('detail', $listInfo);
          
            $this->display('addtype');
        } elseif (IS_POST) {
            $postData = I('post.');
            $res = D('api_articletype')->where(array('id' => $postData['id']))->save($postData);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('编辑成功');
            }
        }
    }
    /* 删除类型 */
    public function deltype() {
        $id = I('post.id');
        $childNum = D('api_articletype')->where(array('id' => $id))->count();
        if ($childNum) {
			D('api_articletype')->where(array('id' => $id))->delete();
            $this->ajaxSuccess('删除成功');
            
        } else {
            $this->ajaxError("类型不存在");
        }
    }
	//图片上传
	public function upload(){
		 if (!empty($_FILES)) {
            $upload = new \Think\Upload();   // 实例化上传类
			$upload->maxSize   =     3145728 ;    // 设置附件上传大小
			$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
			$upload->rootPath  =     THINK_PATH;          // 设置附件上传根目录
			$upload->savePath  =     '../Public/';    // 设置附件上传（子）目录
			$upload->subName   =     'articleimage/lunbo/';  //子文件夹
			$upload->saveName  =     date('Ymdhis');     //文件名
			$upload->replace   =     true;  //同名文件是否覆盖
			// 上传文件 
			$images   =   $upload->upload();
			//return $images;
            //判断是否有图
            if($images){
				$info= '/myjiceng/public/articleimage/lunbo/'.$images['photo']['savename']; 
				echo json_encode($info);  
            }
            else{
                $a=$upload->getError();//获取失败信息
				echo json_encode($a);
            }
        }else{
			return 2;
		}
	}

}