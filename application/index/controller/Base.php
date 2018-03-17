<?php
namespace app\index\controller;
use think\Db;
use think\Controller;

class Base extends Controller
{
    //初始化方法
    /**
     * @Author:      fyd
     * @DateTime:    2017-12-14 20:04:44
     * @Description: 初始化方法，判断用户是否登录
     */
    public function _initialize(){ 
        if(!session('ems_identity')){
            $this->error("请先登录系统！",'Login/index');
        }else{
            $exp_datas = Db::name('lab')->where('isdelete',0)->select();
            $this->assign('exp',$exp_datas);
        }
    }
}