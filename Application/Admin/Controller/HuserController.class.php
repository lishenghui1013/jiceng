<?php
/**
 * Created by PhpStorm.
 * User: wrl
 * Date: 2018/3/31
 * Time: 20:00
 */
namespace  Admin\Controller;
use Think\log;
use Think\Think;
use Think\Upload;
class HuserController extends BaseController{
    /**
     * 获取菜单列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function index() {

        $listInfo = D('api_users')->select();
        $this->assign('list', $listInfo);
        $this->display();
    }

}