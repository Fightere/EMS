<?php
namespace app\index\controller;
use think\Db;
use app\index\controller\Base;

class Reservations extends Base
{

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-02-21 09:32:42
	 * @Description: 获得所有预约
	 */
	public function index(){
		return $this->fetch('reservations');
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-02-21 09:32:42
	 * @Description: 获得本人预约
	 */
	public function index_user(){
		return $this->fetch('reservation_user');
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-03-31 09:48:40
	 * @Description: 获取本人预约的数据
	 */
	public function getmyre(){

		$page = input('page');
        $limit = input('limit');
        $user = session('ems_name');

        $count = Db::name('exper')
        ->where('exp_user',$user)
        -> count();
		
		$data = Db::name('exper')
				->where('exp_user',$user)
				->limit(($page-1)*$limit,$limit)
				->select();
		$counts = count($data);

		for($i=1;$i<=$counts;$i++){
            $data[$i-1]['kid'] = $i;
        }
		$datas = [];
		for ($i=0; $i<$counts; $i++) { 
			$datas[$i]['id'] = $data[$i]['id'];
			$datas[$i]['kid'] = $data[$i]['kid'];
			$datas[$i]['exp_zdt'] = $data[$i]['exp_zdt'];
			$datas[$i]['lab_name'] = $data[$i]['exp_name'];
			$datas[$i]['ifsubmit'] = ($data[$i]['exp_apply'] == 1)? '是':'否'; 
			$datas[$i]['ifpass'] = ($data[$i]['exp_isallow'] == 1)? '是':'否'; 
			$datas[$i]['sub_time'] = $data[$i]['exp_time'];
		}
		echo json(['code'=>0,'count'=>$count,'msg'=>'','data'=>$datas])->getcontent();
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-03-18 18:16:36
	 * @Description: 获取预约数据
	 */
	public function getdata(){
		$page = input('page');
        $limit = input('limit');

        $count = Db::name('exper')
        -> count();

		$user = session('ems_name');
		$data = Db::name('exper')
				-> limit(($page-1)*$limit,$limit)
				->select();
		$counts = count($data);

		for($i=1;$i<=$counts;$i++){
            $data[$i-1]['kid'] = $i;
        }
		$datas = [];
		for ($i=0; $i<$counts; $i++) { 
			$datas[$i]['id'] = $data[$i]['id'];
			$datas[$i]['kid'] = $data[$i]['kid'];
			$datas[$i]['exp_zdt'] = $data[$i]['exp_zdt'];
			$datas[$i]['lab_name'] = $data[$i]['exp_name'];
			$datas[$i]['ifsubmit'] = ($data[$i]['exp_apply'] == 1)? true:false; 
			$datas[$i]['ifpass'] = ($data[$i]['exp_isallow'] == 1)? true:false; 
			$datas[$i]['sub_time'] = $data[$i]['exp_time'];
		}
		echo json(['code'=>0,'count'=>$count,'msg'=>'','data'=>$datas])->getcontent();
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-03-18 18:37:42
	 * @Description: 私有函数
	 */
	private function ex($name,$id){
		$data = Db::name('exper')
				-> where('id',$id)
				-> find();
		if($data){
			$sub = $data[$name];
			
			$res = Db::name('exper')
				-> where('id',$id)
				-> update([$name=>!$sub]);

			if($res){
				echo json(['code'=>0])->getcontent();
			}else{
				echo json(['code'=>1])->getcontent();
			}
		}else{
			echo json(['code'=>1])->getcontent();
		}
		
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-03-18 18:36:57
	 * @Description: 是否提交申请书
	 */
	public function exsub(){
		$id = input('id');
		$this->ex('exp_apply',$id);
	}
	/**
	 * @Author:      fyd
	 * @DateTime:    2018-03-18 18:36:57
	 * @Description: 是否通过申请
	 */
	public function exall(){
		$id = input('id');
		$this->ex('exp_isallow',$id);
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-02-21 15:40:40
	 * @Description: add
	 */
	public function add(){
		$posts = input('post.');
		$lab_id = $posts['Lab_ID'];
		$equipid = $posts['equip_name'];
		$equipdata = Db::name('equip')->where('id',$equipid)->find();
		$equipnum = $equipdata['equip_num'];
		$user = session('ems_name');
		$posts['exp_user'] = $user;
		$zdt = $posts['teacher'];

		// 获取课程号以及学期,获取接口后可以获取
		$exp_id = "1122334";
		$xq = Db::name('set')->where('setname','xq')->find();
		$exp_xq = $xq['setvalue'];

		$name = $posts['class_name']; //课程名称
		$xs = $posts['classes']; //学时
		$snum = $posts['sum_peo'];
		$pnum = $posts['group_peo'];
		$cycle = $posts['cycle_peo'];
		$class = $posts['major_class'];

		$a1=preg_match('/['.chr(0xa1).'-'.chr(0xff).']/', $name);
		$b1=preg_match('/[0-9]/', $name);
		$c1=preg_match('/[a-zA-Z]/', $name);
		$a2=preg_match('/['.chr(0xa1).'-'.chr(0xff).']/', $class);
		$b2=preg_match('/[0-9]/', $class);
		$c2=preg_match('/[a-zA-Z]/', $class);
		$state = true;
		if(!is_numeric($xs) | !is_numeric($snum) | !is_numeric($pnum) | !is_numeric($cycle)){
			// echo "这些数据必须全部是数字<br>";
			echo json(['code'=>1,'msg'=>'这些数据必须全部是数字'])->getcontent();  
			$state = false;
		}elseif(!($a1 && !$b1 && !$c1)){
			echo json(['code'=>1,'msg'=>'这个数据必须是中文'])->getcontent();
			// echo "这个数据必须是中文";
			$state = false;
		}elseif(!($a2 && $b2 && !$c2)){
			echo json(['code'=>1,'msg'=>'这个数据必须有中文和数字'])->getcontent();
			// echo "这个数据必须有中文和数字";
			$state = false;
		}

		$warr = ['mon','thus','wed','thur','fri','sat'];
		$termweek = $posts['week'];
		$wday = array_search($posts['day'],$warr)+1;

		//进行设备的处理
		$applynum = (int)($snum/$pnum);
		$isapply = Db::name('exper')->where(['equip_id'=>$equipid,'exp_date'=>$termweek,'exp_week'=>$wday,'exp_sec'=>$posts['period']])->find();
		$esum = Db::name('equip')->where('id',$equipid)->find();
		$sum = $esum['equip_num'];
		if($isapply){
			$ago = (int)($isapply['exp_snum']/$isapply['exp_pnum']);
			if($applynum > ($sum-$ago)){
				echo json(['code'=>1,'msg'=>'预约设备过多'])->getcontent();
				// echo "预约设备过多";
				$state = false;
			}
		}else{
			if($applynum > $sum){
				// echo "预约设备过多";
				echo json(['code'=>1,'msg'=>'预约设备过多'])->getcontent();
				$state = false;
			}
		}

		if($state){
			$termweek = $posts['week'];
			$insertdata = array(
				'exp_user'	=>	$user,
				'exp_xq' 	=>	$exp_xq,
				'exp_name'	=>	$name,
				'exp_id'	=>	$exp_id,
				'exp_jys'	=>	$posts['office'],
				'exp_zdt'	=>	$zdt,
				'exp_xs'	=>	$xs,
				'elab_id'	=>	$lab_id,
				'equip_id'	=>	$equipid,
				'exp_snum'	=>	$snum,
				'exp_pnum'	=>	$pnum,
				'exp_cycle'	=>	$cycle,
				'exp_bz'	=>	$posts['desc'],
				'exp_class'	=>	$class,
				'exp_type'	=>	$posts['type'],
				'exp_date'	=>	$termweek,
				'exp_week'	=>	$wday,
				'exp_sec'	=>	$posts['period'],
				'exp_apply'	=>	0,
				'exp_isallow'=>	0
			);

			$res = Db::name('exper')->insert($insertdata);
			// dump($res);
			if($res==1){
				// echo "添加成功！";
				echo json(['code'=>0,'msg'=>'添加成功'])->getcontent();
			}
		}
	}
}