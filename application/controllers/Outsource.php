<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outsource extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('outsource_model');
		$permission = $this->session->userdata('permission');
		if(!in_array('e',$permission)){
			redirect(site_url('index/index'));
		}
	}
//面料型号 管理
	public function list_outsource($page=1){
		$flag = $this->input->post('flag');
		$keyword = $this->input->post('keyword');
		$data = $this->outsource_model->list_outsource($page,$flag,$keyword);
		$base_url = "/outsource/list_outsource/";
		$pager = $this->pagination->getPageLink($base_url, $data['total'], $data['limit']);
		$this->assign('pager', $pager);
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->show('outsource/list_outsource');
	}

	public function get_outsource($master_id){
		$list = $this->outsource_model->get_outsource($master_id);
		echo json_encode($list);
		die;
	}

	public function delete_outsource($id){
		echo $this->outsource_model->delete_outsource($id);
	}

	public function use_outsource($id){
		echo $this->outsource_model->use_outsource($id);
	}

	public function save_outsource(){
		echo $this->outsource_model->save_outsource();
	}



}