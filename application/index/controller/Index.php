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
        /*$data = Db::name('lab')->select();
        $this->assign('exp',$data);*/
        return $this->fetch('index');
    }

    /**
     * @Author:      fyd
     * @DateTime:    2017-12-14 20:13:51
     * @Description: 跳转到课程表页面，并传输数据
     */
    public function table(){
        /*$data = Db::name('lab')->select();
        $this->assign('exp',$data);*/

        $lab_id = input('Lab_ID');
        $labinfo = Db::name('lab')->where('id',($lab_id))->find();
        $this->assign('labinfo',$labinfo);
        // echo $getData;
        return $this->fetch('table');
    }

    /**
     * @Author:      fyd
     * @DateTime:    2018-02-20 11:59:40
     * @Description: 跳转到输入课程信息界面，并传输数据
     */
    public function form(){
        // $data = Db::name('lab')->select();
        $type = Db::name('type')->select();
        $jys = Db::name('jys')->select();
        $this->assign('jys',$jys);
        $this->assign('type',$type);
        // $this->assign('exp',$data);
        $lab_id = input('Lab_ID');
        $labinfo = Db::name('lab')->where('id',($lab_id+1))->find();
        $this->assign('labinfo',$labinfo);
        // 得到点击的位置的信息
        $gets = input('get.');
        $this->assign('gets',$gets);

        // dump($gets);
        //dump($gets);
        return $this->fetch('form');
    }

    /**
     * @Author:      fyd
     * @DateTime:    2018-02-20 18:01:07
     * @Description: 修改用户密码
     */
    public function ex_pass(){
        $username = session('ems_username');
        $identity = session('ems_identity');
        if($identity == 'staff'){
            $identity = 'user';
        }
        $info = Db::name($identity)->where('username',$username)->find();
        $password = $info['password'];

        if(request() -> isPost()){
            $ex_data = input('post.');
            // dump($ex_data);
            $old_pass = md5($ex_data['old_pass']);
            $new_pass = md5($ex_data['new_pass']);

            if($old_pass == $password){
                if($new_pass == $password){
                    echo json(['code'=>2,'msg'=>'您输入的密码与原密码相同！'])->getcontent();  
                }else{
                    $result = Db::name($identity)
                    ->where('username',$username)
                    ->update([
                        'password' => $new_pass,
                    ]);

                    if($result){
                        session(null);
                        echo json(['code'=>0])->getcontent();  
                    }else{
                        echo json(['code'=>3])->getcontent();  
                    }
                }
            }else{
                echo json(['code'=>1,'msg'=>'原密码错误'])->getcontent();  
            }
        }
        // dump($info);
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
