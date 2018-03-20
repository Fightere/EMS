<?php
namespace app\index\controller;
use think\Db;
use app\index\controller\Base;
use think\Request;

class Term extends Base
{
	/**
	 * @Author:      fyd
	 * @DateTime:    2018-03-19 21:47:20
	 * @Description: 打开设置页面
	 */
	public function index(){
		return $this->fetch('term/term');
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-03-20 07:39:56
	 * @Description: 接收数据
	 */
	public function sets(){
		$fday = $_POST['fday'];
		$res1 = Db::name('set') 
				-> where('setname','fday')
				-> find();

		if($res1){
			$res2 = Db::name('set')
					-> where('setname','fday')
					-> update(['setvalue'=>$fday]);
		}else{
			$data = [
				'setname'	=>	'fday',
				'setvalue'	=>	$fday
			];
			$res2 = Db::name('set')
					-> insert($data);
		}

		if($res2){
			echo json(['code'=>0])->getcontent();
		}else{
			echo json(['code'=>1])->getcontent();
		}
	}
}