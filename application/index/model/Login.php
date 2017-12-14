<?php
namespace app\index\model;
use think\Model;
use think\Db;

class Login extends Model
{
    /**
     * @Author:      fyd
     * @DateTime:    2017-12-14 20:07:49
     * @Description: 根据用户的输入判断
     */
    public function login($data,$table){//教职工登录
        $user = Db::name($table) -> where('username','=',$data['username']) -> find();
        if($user){
            //补充：密码的加密
            if($user['password'] == $data['password']){
                session(null);
                session('id',$user['id']);
                session('name',$user['name']);
                session('username',$user['username']);
                session('identity',$data['identity']);
                //dump($_SESSION);
                return 1;//信息正确
            }else{
                return 2;//密码错误
            }
        }else{
            return 3;//用户不存在
        }
    }
    /**
     * @Author:      fyd
     * @DateTime:    2017-12-14 20:10:41
     * @Description: 游客登录
     */
    public function see(){//以游客身份登陆
        session(null);
        session('name','游客');
        session('identity','visitor');
    }
}