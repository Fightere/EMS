<?php
namespace app\index\controller;
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
        if(!session('identity')){
            $this->error("请先登录系统！",'Login/index');
        }
    }
}