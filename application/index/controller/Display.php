<?php
namespace app\index\controller;
use think\Db;
use app\index\controller\Base;

class Display extends Base
{

	/**
	 * @Author:      name
	 * @DateTime:    2018-03-10 10:51:10
	 * @Description: 获取当前学期和周数
	 */
	private function getinfo($now){
		// 这里应当是通过表单输入然后把这个信息存储起来
		// 学期
		$xq = '2017 - 2018 - 2';
		// 新学期第一天
		$fday = "2018-03-05";
		//转化成时间戳
		$fday_str = strtotime($fday);
		$now_str = strtotime($now);

		//获取当前是第几周
		$diff = ($now_str - $fday_str) / 86400;
		$termweek = $diff / 7 + 1;

		$ndate = getdate($now_str);

		$wday = $ndate['wday'];

		if($wday == 0){
			$wday = 7;
		}


		$info = [
				'xq'		=>	$xq,
				'fday'		=>	$fday,
				'termweek'	=>	(int)$termweek,
				'wday'		=>	$wday,
			];

		return $info;
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-03-10 11:10:24
	 * @Description: 除去节假日
	 */
	private function isholiday(){
		$now = date("Y-m-d",time());
		$holiday = [
			'2018-04-05',
			'2018-05-01',
			'2018-06-18',
			];
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-02-23 16:37:50
	 * @Description: 展示当前周数
	 */
	public function index(){
		//当前是哪一天
		$now = date("Y-m-d",time());
		$info = $this->getinfo($now);
		$lab_id = input('ids'); 

		$labid = Db::name('equip') 
		-> where('elab_id',$lab_id) 
		-> field('id')
		-> find();

		if(input('weeks')){
			$dweek = input('weeks');
		}else{
			$dweek = $info['termweek']; //获取当前周数
		}

		$data = [];

		if(isset($dweek)){
			$data = Db::name('exper')
					->where('exp_date',$dweek)
					->where('equip_id','in',$labid)
					->select();
		}else{
			$data = Db::name('exper')
					->where('exp_date',18)
					->where('equip_id','in',$labid)
					->select();
		}

		// $data = Db::name('exper')
		// 				->where('exp_date',18)
		// 				->select();
		$count = count($data);

		$arr = [];

		for($js=0;$js<5;$js++){
			$arr[$js] = [
				[[]],
				[[]],
				[[]],
				[[]],
				[[]],
				[[]]
			];
		}

		// 对于$data的处理 获取到对应实验的实验设备数目 并且进行对比 然后把数据添加过去
		for($n=0;$n<$count;$n++){
			$oper_data = $data[$n];
			$equipid = $oper_data['equip_id'];
			$edata = Db::name('equip')->where('id',$equipid)->find();
			$equipnum = $edata['equip_num']; // 实验室中存在的实验设备数目
			$applynum = $oper_data['exp_snum']/$oper_data['exp_pnum'];// 申请的数目

			$remain = $equipnum - $applynum;
			$data[$n]['equip_num'] = $equipnum;
			$data[$n]['remain_num'] = $remain;

			if($remain>0){
				$data[$n]['remain_state'] = True;
			}else{
				$data[$n]['remain_state'] = False;
			}
		}

		$formdata = array();
		$num = array();
		$jdata = array();
		for($i=0;$i<5;$i++){
			for($j=0;$j<6;$j++){
				$formdata[$i][$j] = ' ';
				$num[$i][$j] = 0;
			}
		}

		for($j=0;$j<$count;$j++){
			$row = $data[$j]['exp_sec']-1;
			$col = $data[$j]['exp_week']-1;
			$formdata[$row][$col] = $data[$j]['exp_name'];
			$num[$row][$col] = $data[$j]['id'];
			$jdata[$j] = [
				"实验课程名称"	=>	$data[$j]['exp_name'],
				"专业班级"		=>	$data[$j]['exp_class'],
				"人数"			=>	$data[$j]['exp_snum'],
				"指导教师"		=>	$data[$j]['exp_zdt'],
				"可用设备数"	=>	$data[$j]['remain_num'],
				"总实验设备数"	=>	$data[$j]['equip_num'],
			];
			$arr[$row][$col][0] = $jdata[$j];
		}

		/*$jc = array(
			'第一大节','第二大节','第三大节','第四大节','第五大节'
		);
*/
		$jsondata = [];

		for($js=0;$js<5;$js++){
			$jsondata[$js] = [
				"mon"=>$arr[$js][0],
				"tues"=>$arr[$js][1],
				"wed"=>$arr[$js][2],
				"thur"=>$arr[$js][3],
				"fri"=>$arr[$js][4],
				"sat"=>$arr[$js][5]
			];
		}

		echo json(['code'=>0,'fday'=>$info['fday'],'termweek'=>$dweek,'msg'=>'','data'=>$jsondata])->getcontent();
	}
}