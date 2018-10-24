<?php

namespace Admin\Controller;

/**
 * 体质检测管理控制器
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class HealthController extends BaseController {

    /**
     * 体质检测列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {
        $list = D('api_health as a')->join('api_healthtype as b on a.healthtype=b.id')->field("a.*,b.healthname")->select();
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 新增文章
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $data['addtime'] = time();
            $res = D('api_health')->add($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        } else {
            $typelist = D('api_healthtype')->select();
            $id = I('get.id');
			//print_r($typelist);
            $this->assign('typelist', $typelist);
            $this->assign('id', $id);
            $this->display();
        }
    }

    /**
     * 文章上线
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function open() {
        $id = I('post.id');
        $res = D('api_health')->where(array('id' => $id))->save(array('ifshow' => 0));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('添加成功');
        }
    }

    /**
     * 文章下线
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function close() {
        $id = I('post.id');
        $res = D('api_article')->where(array('id' => $id))->save(array('ifshow' => 1));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('添加成功');
        }
    }

    /**
     * 编辑文章
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        if (IS_GET) {
             $typelist = D('api_healthtype')->select();
            $id = I('get.id');
            $listInfo=D('api_health')->where("id='$id'")->find();
            
            $this->assign('detail', $listInfo);
            $this->assign('typelist', $typelist);
            $this->display('add');
        } elseif (IS_POST) {
            $postData = I('post.');
            $res = D('api_health')->where(array('id' => $postData['id']))->save($postData);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('编辑成功');
            }
        }
    }

    public function del() {
        $id = I('post.id');
        $childNum = D('api_health')->where(array('id' => $id))->count();
        if ($childNum) {
			D('api_health')->where(array('id' => $id))->delete();
            $this->ajaxSuccess('删除成功');
            
        } else {
            $this->ajaxError("当前文章不存在");
        }
    }
	/**
     * 检测结果列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function typelist() {
        $list = D('api_healthtype')->select();
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
     * 编辑检测结果
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edittype() {
        if (IS_GET) {
            $id = I('get.id');
            $listInfo=D('api_healthtype')->where("id='$id'")->find();
            $this->assign('detail', $listInfo);
          
            $this->display('addtype');
        } elseif (IS_POST) {
            $postData = I('post.');
            $res = D('api_healthtype')->where(array('id' => $postData['id']))->save($postData);
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
				$info= $images['photo']['savepath'].$images['photo']['savename']; 
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