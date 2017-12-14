<?php
namespace app\index\controller;
use think\Db;
use app\index\controller\Base;

class Index extends Base
{
    /**
     * @Author:      fyd
     * @DateTime:    2017-12-14 20:12:37
     * @Description: 显示实验室分类
     */
    public function index()
    {
        $data = Db::name('lab')->select();
        $this->assign('exp',$data);
        return $this->fetch('index');
    }

    /**
     * @Author:      fyd
     * @DateTime:    2017-12-14 20:13:51
     * @Description: 跳转到课程表页面，并传输数据
     */
    public function table(){
        $data = Db::name('lab')->select();
        $this->assign('exp',$data);

        $lab_id = input('Lab_ID');
        $labinfo = Db::name('lab')->where('id',($lab_id+1))->find();
        $this->assign('labinfo',$labinfo);
        // echo $getData;
        return $this->fetch('table');
    }

    /**
     * @Author:      fyd
     * @DateTime:    2017-12-14 20:12:57
     * @Description: 退出登录
     */
    public function logout(){
        session(null);
        $this->success('退出成功！','Login/index');
    }
}
