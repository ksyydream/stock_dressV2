<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cust extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('cust_model');
		$permission = $this->session->userdata('permission');
		if(!in_array('d',$permission)){
			redirect(site_url('index/index'));
		}
	}
//面料型号 管理
	public function list_cust($page=1){
		$flag = $this->input->post('flag');
		$keyword = $this->input->post('keyword');
		$data = $this->cust_model->list_cust($page,$flag,$keyword);
		$base_url = "/cust/list_cust/";
		$pager = $this->pagination->getPageLink($base_url, $data['total'], $data['limit']);
		$this->assign('pager', $pager);
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->show('cust/list_cust');
	}

	public function get_cust($master_id){
		$list = $this->cust_model->get_cust($master_id);
		echo json_encode($list);
		die;
	}

	public function delete_cust($id){
		echo $this->cust_model->delete_cust($id);
	}

	public function use_cust($id){
		echo $this->cust_model->use_cust($id);
	}

	public function save_cust(){
		echo $this->cust_model->save_cust();
	}



}