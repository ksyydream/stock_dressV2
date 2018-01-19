<?php
if (! defined('BASEPATH'))
	exit('No direct script access allowed');

class Outsource_model extends MY_Model
{

	public function __construct ()
	{
		parent::__construct();
	}

	public function list_outsource($page=1,$flag=null,$keyword=null){
		$data['limit'] = $this->limit;
		//获取总记录数
		$this->db->select('count(1) num')->from('outsource a');
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
		$this->db->select('a.*')->from('outsource a');
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
	public function delete_outsource($id){
		$requset = $this->db->where('id',$id)->update('outsource',array('flag'=>-1));
		if($requset){
			return 1;
		}else{
			return 2;
		}
	}
	public function use_outsource($id){
		$requset = $this->db->where('id',$id)->update('outsource',array('flag'=>1));
		if($requset){
			return 1;
		}else{
			return 2;
		}
	}
	public function save_outsource(){
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
		if(!$this->input->post('outsource_id')){
			$check = $this->db->select('*')->from('outsource')->where('name',$data['name'])->get()->row();
			if($check){
				return -2;
			}
			$rs = $this->db->insert('outsource',$data);
		}else{
			$check = $this->db->select('*')->from('outsource')->where(
				array(
					'name' => $data['name'],
					'id <>'=>$this->input->post('outsource_id')
				)
			)->get()->row();
			if($check){
				return -2;
			}
			$rs = $this->db->where('id',$this->input->post('outsource_id'))->update('outsource',$data);
		}

		if($rs)
			return 1;
		else
			return -1;
	}

	public function get_outsource($id){
		$this->db->select()->from('outsource');
		$this->db->where('id',$id);
		return $this->db->get()->row_array();
	}

	public function get_outsource_all($flag=null){
		//获取详细列
		$this->db->select('a.name,a.id')->from('outsource a');
		if($flag){
			$this->db->where_in('a.flag',$flag);
		}
		$this->db->order_by('a.id','asc');
		$data = $this->db->get()->result_array();

		return $data;
	}


}