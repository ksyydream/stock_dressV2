<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cutorder extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('cutorder_model');
		$this->load->model('cust_model');
		$this->load->model('style_model');
		$this->load->model('material_model');
	}



	public function order_do($id){
		$detail = $this->cutorder_model->get_detail4write($id);
		if(!$detail)
			redirect(site_url('cutorder/add_cut'));
		if($detail['status']!=1)
			redirect(site_url('cutorder/add_cut'));
		$material = $this->material_model->get_material_all(1);
		$color = $this->material_model->get_color_all(1);
		$style= $this->style_model->get_style_all(1);
		$this->assign('style', $style);
		$this->assign('material', $material);
		$this->assign('color', $color);
		$this->assign('detail', $detail);
		$this->show('style/test_cut_in_1');
	}

	public function add_cut(){
		$cust = $this->cust_model->get_cust_all(1);
		$today = date('Y-m-d',time());
		$this->assign('today', $today);
		$this->assign('cust', $cust);
		$this->show('style/test_cut_in');
	}

	public function save_1(){
		$rs = $this->cutorder_model->save_1();
		if($rs > 0){
			$this->show_message('操作成功',site_url('cutorder/order_do').'/'.$rs);
		}elseif ($rs == -2){
			$this->show_message('客户已停用或为找到');
		}else{
			$this->show_message('操作失败');
		}
	}

	public function get_stock(){
		$cust_id = $this->input->post('cust_id');
		$material_id = $this->input->post('material_id');
		$color_id = $this->input->post('color_id');
		$ganghao = $this->input->post('ganghao');
		$cut_order_id = $this->input->post('cut_order_id');
		if(!$cust_id){
			//return -1;
		}
		$list= $this->material_model->get_m_list4cut($cust_id,$material_id,$color_id,$ganghao,$cut_order_id);
		$this->assign('list', $list);
		$this->display('style/chose_m_list.html');
	}

	public function get_m_list(){
		$cust_id = $this->input->post('cust_id');
		$material_id = $this->input->post('material_id');
		$color_id = $this->input->post('color_id');
		$ganghao = $this->input->post('ganghao');
		$cut_order_id = $this->input->post('cut_order_id');
		if(!$cust_id){
			//return -1;
		}
		$list= $this->material_model->get_m_list4cut($cust_id,$material_id,$color_id,$ganghao,$cut_order_id);
		//die(var_dump($view_p));
		$this->assign('list', $list);
		$this->display('style/chose_m_list.html');
	}

	public function cut_in_mstock(){
		$data =  $this->cutorder_model->cut_in_mstock();
		if($data==-1 || $data ==-2){
			echo $data;
		}
		$this->assign('list', $data);
		$this->display('style/show_m_list4cut.html');
	}

	public function delete_show(){
		echo $this->cutorder_model->delete_show();
	}

	public function save_cut(){
		$rs = $this->cutorder_model->save_cut();
		if($rs > 0){
			$this->show_message('操作成功',site_url('cutorder/cut_list_2'));
		}elseif ($rs == -2){
			$this->show_message('信息不全,或已不可编辑');
		}else{
			$this->show_message('操作失败');
		}
	}

	public function cut_list_2($page=1){
		$data = $this->cutorder_model->cut_list($page,2);
		$base_url = "/cutorder/cut_list/";
		$pager = $this->pagination->getPageLink($base_url, $data['total'], $data['limit']);
		$cust = $this->cust_model->get_cust_all();
		$material = $this->material_model->get_material_all();
		$color = $this->material_model->get_color_all();
		$style= $this->style_model->get_style_all();
		$this->assign('style', $style);
		$this->assign('material', $material);
		$this->assign('color', $color);
		$this->assign('cust', $cust);
		$this->assign('pager', $pager);
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->show('style/cut_list');
	}


}