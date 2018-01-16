<?php
if (! defined('BASEPATH'))
	exit('No direct script access allowed');

class Material_model extends MY_Model
{

	public function __construct ()
	{
		parent::__construct();
	}

	public function list_material($page=1){
		$data['limit'] = $this->limit;
		//获取总记录数
		$this->db->select('count(1) num')->from('material a');
		if($this->input->post('keyword')){
			$this->db->like('a.material_name',$this->input->post('keyword'));
		}
		if($this->input->post('flag')){
			$this->db->where("a.flag",$this->input->post('flag'));
		}
		$num = $this->db->get()->row();
		$data['total'] = $num->num;

		//搜索条件
		$data['flag'] = $this->input->post('flag')?$this->input->post('flag'):null;
		$data['keyword'] = $this->input->post('keyword')?$this->input->post('keyword'):null;
		//获取详细列
		$this->db->select('a.*')->from('material a');
		if($this->input->post('keyword')){
			$this->db->like('a.material_name',$this->input->post('keyword'));
		}
		if($this->input->post('flag')){
			$this->db->where("a.flag",$this->input->post('flag'));
		}
		$this->db->limit($this->limit, $offset = ($page - 1) * $this->limit);
		$this->db->order_by('a.id','desc');
		$data['items'] = $this->db->get()->result_array();

		return $data;
	}
	public function delete_material($id){
		$requset = $this->db->where('id',$id)->update('material',array('flag'=>-1));
		if($requset){
			return 1;
		}else{
			return 2;
		}
	}
	public function use_material($id){
		$requset = $this->db->where('id',$id)->update('material',array('flag'=>1));
		if($requset){
			return 1;
		}else{
			return 2;
		}
	}
	public function save_material(){
		$data = array(
			'material_name'=>$this->input->post('material_name'),
			//'flag'=>$this->input->post('flag')? 1 : 2,
			'create_date'=>date('Y-m-d H:i:s'),
			'modify_date'=>date('Y-m-d H:i:s'),
			'create_user'=>$this->session->userdata('user_id'),
			'modify_user'=>$this->session->userdata('user_id')
		);
		if(!$data['material_name']){
			return -1;
		}
		if(!$this->input->post('material_id')){
			$check = $this->db->select('*')->from('material')->where('material_name',$data['material_name'])->get()->row();
			if($check){
				return -2;
			}
			$rs = $this->db->insert('material',$data);
		}else{
			$check = $this->db->select('*')->from('material')->where(
				array(
					'material_name'=>$data['material_name'],
					'id <>'=>$this->input->post('material_id')
				)
			)->get()->row();
			if($check){
				return -2;
			}
			$rs = $this->db->where('id',$this->input->post('material_id'))->update('material',$data);
		}

		if($rs)
			return 1;
		else
			return -1;
	}

	public function get_material($id){
		$this->db->select()->from('material');
		$this->db->where('id',$id);
		return $this->db->get()->row_array();
	}


}