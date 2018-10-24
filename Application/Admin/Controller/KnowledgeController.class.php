<?php

namespace Admin\Controller;

/**
 * 知识问答控制器
 * @since   2016-01-16
 * @author  zhaoxiang <zhaoxiang051405@outlook.com>
 */
class KnowledgeController extends BaseController {

    /**
     * 知识问答
     * 
     */
    public function index() {
        $list = D('api_questions')->select();
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 新增试题
     * 
     */
    public function add() {
        if (IS_POST) {
            $data = I('post.');
            $data['questioncreatetime'] = time();
            $res = D('api_questions')->add($data);
			$userid=I('session.uid');
			$username=D('api_user')->where("id='$userid'")->find();
			$data['questionusername']=$username['username'];
			$data['questionuserid']=$userid;
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('添加成功');
            }
        } else {
            //$typelist = D('api_healthtype')->select();
            $id = I('get.id');
			//print_r($typelist);
            //$this->assign('typelist', $typelist);
            $this->assign('id', $id);
            $this->display();
        }
    }

    /**
     * 试题上线
     *
     */
    public function open() {
        $id = I('post.id');
        $res = D('api_questions')->where(array('id' => $id))->save(array('questionstatus' => 0));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('添加成功');
        }
    }

    /**
     * 试题下线
     * 
     */
    public function close() {
        $id = I('post.id');
        $res = D('api_questions')->where(array('id' => $id))->save(array('questionstatus' => 1));
        if ($res === false) {
            $this->ajaxError('操作失败');
        } else {
            $this->ajaxSuccess('添加成功');
        }
    }

    /**
     * 编辑试题
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function edit() {
        if (IS_GET) {
             
            $id = I('get.id');
            $listInfo=D('api_questions')->where("id='$id'")->find();
            
            $this->assign('detail', $listInfo);
            
            $this->display('add');
        } elseif (IS_POST) {
            $postData = I('post.');
            $res = D('api_questions')->where(array('id' => $postData['id']))->save($postData);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('编辑成功');
            }
        }
    }
    /* 删除试题 */
    public function del() {
        $id = I('post.id');
        $childNum = D('api_questions')->where(array('id' => $id))->count();
        if ($childNum) {
			D('api_questions')->where(array('id' => $id))->delete();
            $this->ajaxSuccess('删除成功');
            
        } else {
            $this->ajaxError("当前试题不存在");
        }
    }
	/**
     * 知识问答结果列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function result() {
        $list = D('api_questions_result')->select();
        $this->assign('list', $list);
        $this->display();
    }
	/**
     * 新增答题结果
     */
    public function addtype() {
        if (IS_POST) {
            $data = I('post.');
            $data['ctime'] = time();
            $res = D('api_questions_result')->add($data);
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
     * 编辑答题结果
     */
    public function edittype() {
        if (IS_GET) {
            $id = I('get.id');
            $listInfo=D('api_questions_result')->where("id='$id'")->find();
            $this->assign('detail', $listInfo);
          
            $this->display('addtype');
        } elseif (IS_POST) {
            $postData = I('post.');
            $res = D('api_questions_result')->where(array('id' => $postData['id']))->save($postData);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('编辑成功');
            }
        }
    }
    /* 删除答题结果 */
    public function deltype() {
        $id = I('post.id');
        $childNum = D('api_questions_result')->where(array('id' => $id))->count();
        if ($childNum) {
			D('api_questions_result')->where(array('id' => $id))->delete();
            $this->ajaxSuccess('删除成功');
            
        } else {
            $this->ajaxError("类型不存在");
        }
    }

}