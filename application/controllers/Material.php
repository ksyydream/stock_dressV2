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
//面料库存

	public function list_stock_in($page=1){
		$data = $this->material_model->list_stock_in($page);
		$types = $this->material_model->list_material();
		$base_url = "/material/list_stock_in/";
		$pager = $this->pagination->getPageLink($base_url, $data['total'], $data['limit']);
		$this->assign('pager', $pager);
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->assign('types', $types);
		$this->show('material/list_stock_in');
	}

	public function add_stock($id=null,$page=1){
		$this->assign('page', $page);
		$cust = $this->cust_model->get_cust_all(1);
		$material = $this->material_model->get_material_all(1);
		$color = $this->material_model->get_color_all(1);
		$this->assign('material', $material);
		$this->assign('color', $color);
		$this->assign('cust', $cust);
		if($id){
			$detail = $this->material_model->get_new_detail($id);
			$this->assign('detail', $detail);
		}
		$this->show('material/stock_in');
	}
}