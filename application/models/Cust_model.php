<?php
if (! defined('BASEPATH'))
	exit('No direct script access allowed');

class Cust_model extends MY_Model
{

	public function __construct ()
	{
		parent::__construct();
	}

	public function list_cust($page=1,$flag=null,$keyword=null){
		$data['limit'] = $this->limit;
		//获取总记录数
		$this->db->select('count(1) num')->from('cust a');
		if($keyword){
			$this->db->like('a.name',$keyword);
		}
		if($flag){
			$this->db->where("a.flag",$flag);
		}
		$num = $this->db->get()->row();
		$data['total'] = $num->num;

		//搜索条件
		$data['flag'] = $flag?$flag:null;
		$data['keyword'] = $keyword?$keyword:null;
		//获取详细列
		$this->db->select('a.*')->from('cust a');
		if($keyword){
			$this->db->like('a.name',$keyword);
		}
		if($flag){
			$this->db->where("a.flag",$flag);
		}
		$this->db->limit($data['limit'], $offset = ($page - 1) * $data['limit']);
		$this->db->order_by('a.id','desc');
		$data['items'] = $this->db->get()->result_array();

		return $data;
	}
	public function delete_cust($id){
		$requset = $this->db->where('id',$id)->update('cust',array('flag'=>-1));
		if($requset){
			return 1;
		}else{
			return 2;
		}
	}
	public function use_cust($id){
		$requset = $this->db->where('id',$id)->update('cust',array('flag'=>1));
		if($requset){
			return 1;
		}else{
			return 2;
		}
	}
	public function save_cust(){
		$data = array(
			'name'=>$this->input->post('name'),
			'phone'=>$this->input->post('phone'),
			'remark'=>$this->input->post('remark'),
			'flag'=>$this->input->post('flag')?$this->input->post('flag'):1,
			'create_date'=>date('Y-m-d H:i:s'),
			'modify_date'=>date('Y-m-d H:i:s'),
			'create_user'=>$this->session->userdata('user_id'),
			'modify_user'=>$this->session->userdata('user_id')
		);
		if(!$data['name']){
			return -1;
		}
		if(!$this->input->post('cust_id')){
			$check = $this->db->select('*')->from('cust')->where('name',$data['name'])->get()->row();
			if($check){
				return -2;
			}
			$rs = $this->db->insert('cust',$data);
		}else{
			$check = $this->db->select('*')->from('cust')->where(
				array(
					'name' => $data['name'],
					'id <>'=>$this->input->post('cust_id')
				)
			)->get()->row();
			if($check){
				return -2;
			}
			$rs = $this->db->where('id',$this->input->post('cust_id'))->update('cust',$data);
		}

		if($rs)
			return 1;
		else
			return -1;
	}

	public function get_cust($id){
		$this->db->select()->from('cust');
		$this->db->where('id',$id);
		return $this->db->get()->row_array();
	}

	public function get_cust_all($flag){
		//获取详细列
		$this->db->select('a.name,a.id')->from('cust a');
		if($flag){
			$this->db->where_in('a.flag',$flag);
		}
		$this->db->order_by('a.id','asc');
		$data = $this->db->get()->result_array();

		return $data;
	}


}