<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Material extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('material_model');
		$this->load->model('cust_model');
		$permission = $this->session->userdata('permission');
		if(!in_array('a',$permission)){
			redirect(site_url('index/index'));
		}
	}
//面料型号管理
	public function list_material($page=1){
		$data = $this->material_model->list_material($page);
		$base_url = "/material/list_material/";
		$pager = $this->pagination->getPageLink($base_url, $data['total'], $data['limit']);
		$this->assign('pager', $pager);
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->show('material/list_material');
	}

	public function get_material($master_id){
		$list = $this->material_model->get_material($master_id);
		echo json_encode($list);
		die;
	}

	public function delete_material($id){
		echo $this->material_model->delete_material($id);
	}

	public function use_material($id){
		echo $this->material_model->use_material($id);
	}

	public function save_material(){
		echo $this->material_model->save_material();
	}
//面料颜色
	public function list_material_color($page=1){
		$data = $this->material_model->list_material_color($page);
		$base_url = "/material/list_material_color/";
		$pager = $this->pagination->getPageLink($base_url, $data['total'], $data['limit']);
		$this->assign('pager', $pager);
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->show('material/list_material_color');
	}

	public function get_material_color($master_id){
		$list = $this->material_model->get_material_color($master_id);
		echo json_encode($list);
		die;
	}

	public function delete_material_color($id){
		echo $this->material_model->delete_material_color($id);
	}

	public function use_material_color($id){
		echo $this->material_model->use_material_color($id);
	}

	public function save_material_color(){
		echo $this->material_model->save_material_color();
	}
//面料入库单

	public function list_stock_in($page=1){
		$data = $this->material_model->list_stock_in($page);
		$cust = $this->cust_model->get_cust_all(1);
		$this->assign('cust', $cust);
		$base_url = "/material/list_stock_in/";
		$pager = $this->pagination->getPageLink($base_url, $data['total'], $data['limit']);
		$this->assign('pager', $pager);
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->show('material/list_stock_in');
	}

	public function add_stock($id=null,$page=1){
		$today = date('Y-m-d',time());
		$this->assign('today', $today);
		$this->assign('page', $page);
		$cust = $this->cust_model->get_cust_all(1);
		$material = $this->material_model->get_material_all(1);
		$color = $this->material_model->get_color_all(1);
		$this->assign('material', $material);
		$this->assign('color', $color);
		$this->assign('cust', $cust);
		$this->show('material/stock_in');
	}

	public function save_stock(){
		if(!trim($this->input->post('cust_id'))){
			$this->show_message('面料所属不能为空!');
		}
		$rs =$this->material_model->save_stock();
		if($rs == 1){
			$this->show_message('保存成功',site_url('material/list_stock_in'));
		}elseif($rs == -2){
			$this->show_message('面料详情有误!');
		}else{
			$this->show_message('保存失败!');
		}
	}

	public function view_in($id,$page=1){
		if(!$id)
			$this->show_message('未找到信息!');
		$this->assign('page', $page);
		$detail = $this->material_model->get_in_detail($id);
		if(!$detail){
			$this->show_message('未找到信息!');
		}
		$this->assign('detail', $detail);
		$this->show('material/stock_in_view');
	}

	//面料库存
	public function list_stock($page=1){
		$data = $this->material_model->list_stock($page);
		$material = $this->material_model->get_material_all(1);
		$cust = $this->cust_model->get_cust_all(1);
		$color = $this->material_model->get_color_all(1);
		$this->assign('material', $material);
		$this->assign('cust', $cust);
		$this->assign('color', $color);
		$base_url = "/material/list_stock/";
		$pager = $this->pagination->getPageLink($base_url, $data['total'], $data['limit']);
		$this->assign('pager', $pager);
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->show('material/list_stock');
	}

	public function down_excel_stock(){
		$data_res = $this->material_model->get_stock4excel();

		require_once (APPPATH . 'libraries/PHPExcel/PHPExcel.php');
		$excel  = new \PHPExcel ();


		$letter = array('A','B','C','D','E','F','G','H','I','J','K');
		$tableheader = array('面料型号','面料所属','面料颜色','缸号','原始米数','剩余米数','入库时间','','','','');
		for($i = 0;$i < count($tableheader);$i++) {
			$excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
			if($i == 4 || $i == 5)
				$excel->getActiveSheet()->getColumnDimension("$letter[$i]")->setWidth(15);
			else
				$excel->getActiveSheet()->getColumnDimension("$letter[$i]")->setWidth(25);
			$excel->getActiveSheet()->getStyle("$letter[$i]1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		$excel->getActiveSheet()->getStyle( 'A1:G1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$excel->getActiveSheet()->getStyle('A1:G1')->getFill()->getStartColor()->setARGB('FF66CD00');
		$data = array();

		foreach ($data_res as $k=>$v){
			$data[] = array($v['material_name'],$v['cust_name'],$v['color_name'],$v['ganghao'],$v['original_meters'],$v['turn_meters'],$v['in_date'],'','','','');
		}

		for ($i = 2;$i <= count($data) + 1;$i++) {
			$j = 0;
			foreach ($data[$i - 2] as $key=>$value) {
				$excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value",PHPExcel_Cell_DataType::TYPE_STRING);
				$j++;
			}
		}
		$excel->getActiveSheet()->setTitle('面料库存报表');
		$write = new \PHPExcel_Writer_Excel5 ($excel);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");;
		header('Content-Disposition:attachment;filename="'.'企业导出'.date('Y-m-d H:i:s',time()).'.xls"');
		header("Content-Transfer-Encoding:binary");
		$write->save('php://output');
	}
}