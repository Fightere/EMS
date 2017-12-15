<?php
namespace app\index\controller;
use think\Db;
use PHPExcel_IOFactory;
use PHPExcel;
use app\index\controller\Base;

class Excel extends Base
{
	/**
	 * @Author:      fyd
	 * @DateTime:    2017-12-14 21:50:38
	 * @Description: 合并单元格
	 */
	public function mergeExcel($phpsheet,$mergestr){
		$phpsheet->mergeCells($mergestr);
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2017-12-15 16:56:00
	 * @Description: 设置字体
	 */
	public  function setFont($phpsheet,$cell,$n,$size){
		for($i=0;$i<$n;$i++){
			$phpsheet->getStyle($cell[$i])->getFont()->setSize($size[$i]);
		}
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2017-12-14 21:48:16
	 * @Description: 设置宽度
	 */
	public function setWidth($phpsheet,$cell,$n,$width){
		for($i=0;$i<$n;$i++){
			$phpsheet->getColumnDimension($cell[$i])->setWidth($width[$i]);
		}
	}

	/**
	 * @Author:      居中
	 * @DateTime:    2017-12-15 16:47:43
	 * @Description: Description
	 */
	public function setCenter($phpsheet,$cell,$n){
		for($i=0;$i<$n;$i++){
			$phpsheet->getStyle($cell[$i])->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$phpsheet->getStyle($cell[$i])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		}
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2017-12-14 21:49:47
	 * @Description: 设置高度
	 */
	public function setHeight($phpsheet,$cell,$n,$height){
		for($i=0;$i<$n;$i++){
			$phpsheet->getRowDimension($cell[$i])->setRowHeight($height[$i]);
		}
	}

	/**
	 * @Author:      fyd
	 * @DateTime:    2017-12-14 21:44:20
	 * @Description: 利用PHPExcel生成实验申请表
	 */
    public function index()
    {
        $path = dirname(__FILE__);
        $PHPExcel = new PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle('实验申请表');

        //设置excel表格默认属性
        $PHPSheet->getDefaultStyle()->getFont()->setSize(14);

       	//设置其他属性
       	//合并单元格
       	$this->mergeExcel($PHPSheet,'A1:H2');
       	$this->mergeExcel($PHPSheet,'A3:H3');
       	$this->mergeExcel($PHPSheet,'D4:F4');
       	$this->mergeExcel($PHPSheet,'C12:D12');
       	$this->mergeExcel($PHPSheet,'G12:H12');

       	//设置字体大小
       	$fcell = ['A1'];
       	$size = ['20'];
       	$this->setFont($PHPSheet,$fcell,1,$size);

       	//设置宽高
       	$wcell = ['A','B','C','D','E','F','G','H'];
       	$width = [30,30,10,10,10,10,10,10];
       	$this->setWidth($PHPSheet,$wcell,8,$width);
       	$this->setCenter($PHPSheet,$wcell,8);
       	$wce = ['A1','A3','D4','C12','G12'];
       	$this->setCenter($PHPSheet,$wce,5);
       	$hcell = [6,7,8,9,10,11];
       	$height = [26,26,26,26,26,26];
       	$this->setHeight($PHPSheet,$hcell,6,$height);
       	//填写数据
       	$PHPSheet->setCellValue('A1','计算机系实验中心计划外实验申请表');
       	$PHPSheet->setCellValue('A3','2017 - 2018学年度 第一学期');
       	$PHPSheet->setCellValue('A4','实验课程');
       	$PHPSheet->setCellValue('B4','申请人');
       	$PHPSheet->setCellValue('D4','实验室名称');
       	$PHPSheet->setCellValue('A5','实验/授课名称');
       	$PHPSheet->setCellValue('B5','实验/授课内容');
       	$PHPSheet->setCellValue('C5','实验学时');
       	$PHPSheet->setCellValue('D5','班级');
       	$PHPSheet->setCellValue('E5','人数');
       	$PHPSheet->setCellValue('F5','周次');
       	$PHPSheet->setCellValue('G5','星期');
       	$PHPSheet->setCellValue('H5','节次');
       	$PHPSheet->setCellValue('A11','备注:');
       	$PHPSheet->setCellValue('A12','教研室主任签字:');
       	$PHPSheet->setCellValue('B12','实验中心主任签字:');
       	$PHPSheet->setCellValue('C12','系教学主任签字:');
       	$PHPSheet->setCellValue('G12',date('Y年m月d日',time()));

        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel,'Excel2007');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="实验申请表.xlsx"');
        header('Cache-Control: max-age=0');
        $PHPWriter->save("php://output");
    }
}
