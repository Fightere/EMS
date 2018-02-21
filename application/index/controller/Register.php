<?php
namespace app\index\controller;
use think\Db;
use app\index\controller\Base;

class Register extends Base
{
	/**
	 * @Author:      fyd
	 * @DateTime:    2018-02-21 09:32:42
	 * @Description: 获得本人的预约
	 */
	public function index(){
		$user = session('username');
		$data = Db::name('exper')
				->where('exp_user',$user)
				->find();
		$apply = $data['exp_sec']; //是否递交申请书
		$isallow = $data['exp_isallow']; //是否通过了申请

		if($apply == 1){
			echo "已经递交了申请书";
			if($isallow == 1){
				echo "<br>";
				echo "已经通过了申请";
			}else{
				echo "还未通过申请！";
			}
		}else{
			echo "还未递交申请书";
		}



		dump($data);
		$this->fetch('register');
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-02-21 15:40:40
	 * @Description: add
	 */
	public function add(){
		// 此时得到的是可以填充到表单的内容
		$posts = input('post.');
		dump($posts);
	}

	
}