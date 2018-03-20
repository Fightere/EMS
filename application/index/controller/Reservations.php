<?php
namespace app\index\controller;
use think\Db;
use app\index\controller\Base;

class Reservations extends Base
{

	/**
	 * @Author:      fyd
	 * @DateTime:    2018-02-21 09:32:42
	 * @Description: 获得本人的预约
	 */
	public function index(){
		return $this->fetch('reservations');
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
				->where('exp_user',$user)
				-> limit(($page-1)*$limit,$limit)
				->select();
		$counts = count($data);

		for($i=1;$i<=$counts;$i++){
            $data[$i-1]['kid'] = $i;
        }
		$datas = [];
		for ($i=0; $i<$counts; $i++) { 
			$datas[$i]['id'] = $data[$i]['id'];
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
		// 此时得到的是可以填充到表单的内容
		$posts = input('post.');
		/*
		  'Lab_ID' => string '1' (length=1) 实验室id
		  'day' => string '2' (length=1) 实验对应的星期
		  'period' => string '1' (length=1) 节次
		  'term' => string 'sfds' (length=4) 学期
		  'class_name' => string 'dsad' (length=4) 课程名称
		  'office' => string '2' (length=1) 教研室id
		  'classes' => string 'dasd' (length=4) 实验学时
		  'sum_peo' => string 'das' (length=3) 实验人数
		  'group_peo' => string 'das' (length=3) 每组人数
		  'cycle_peo' => string 'dsa' (length=3) 循环人数
		  'type' => string '2' (length=1) 实验类型
		  'major_class' => string 'das' (length=3) 专业班级
		  'desc' => string 'das' (length=3) 备注
		*/

		// 首先要查看数据库中是否有当前位置的数据，如果有要进行判断

		// 根据实验室id获取实验设备数目
		$lab_id = $posts['Lab_ID'];
		$equipdata = Db::name('equip')->where('elab_id',$lab_id)->find();
		$equipnum = $equipdata['equip_num'];
		$equipid = $equipdata['id'];
		$posts['equip_id'] = $equipid; //获取id

		// 获取exp_user
		$user = session('ems_name');
		$posts['exp_user'] = $user;

		// 获取课程号以及学期,获取接口后可以获取
		$exp_id = "1122334";
		$exp_xq = '2017-2018';

		// 获取得到周数、星期以及节次（周数应该从上一个界面传过来）

		/*
			对于数据的判断
			学期获取 不用判断
			课程名称只能是汉字
			学时只能是数字
			人数只能是数字
			专业班级只能是数字加上汉字
			备注无所谓
		*/
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

		/*if(!is_numeric($xs) | !is_numeric($snum) | !is_numeric($pnum) | !is_numeric($cycle)){
			echo "这些数据必须全部是数字<br>";
			$state = false;
		}elseif(!($a1 && !$b1 && !$c1)){
			echo "这个数据必须是中文";
			$state = false;
		}elseif(!($a2 && $b2 && !$c2)){
			echo "这个数据必须有中文和数字";
			$state = false;

		}*/


		if($state){
			$insertdata = array(
				'exp_user'	=>	$user,
				'exp_xq' 	=>	$exp_xq,
				'exp_name'	=>	$name,
				'exp_id'	=>	$exp_id,
				'exp_jys'	=>	$posts['office'],
				'exp_xs'	=>	$xs,
				'equip_id'	=>	$equipid,
				'exp_snum'	=>	$snum,
				'exp_pnum'	=>	$pnum,
				'exp_cycle'	=>	$cycle,
				'exp_bz'	=>	$posts['desc'],
				'exp_class'	=>	$class,
				'exp_type'	=>	$posts['type'],
				'exp_date'	=>	'18',
				'exp_week'	=>	$posts['day'],
				'exp_sec'	=>	$posts['period'],
				'exp_apply'	=>	0,
				'exp_isallow'=>	0	
			);

			$res = Db::name('exper')->insert($insertdata);
			dump($res);
			if($res==1){
				echo "添加成功！";
			}

			dump($posts);
			dump($insertdata);
		}else{
			echo "添加失败！";
		}
		

		
	}
}