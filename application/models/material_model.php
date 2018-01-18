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
			'remark'=>$this->input->post('remark'),
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
			unset($data['create_date']);
			unset($data['create_user']);
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

	public function list_material_color($page=1){
		$data['limit'] = $this->limit;
		//获取总记录数
		$this->db->select('count(1) num')->from('material_color a');
		if($this->input->post('keyword')){
			$this->db->like('a.color_name',$this->input->post('keyword'));
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
		$this->db->select('a.*')->from('material_color a');
		if($this->input->post('keyword')){
			$this->db->like('a.color_name',$this->input->post('keyword'));
		}
		if($this->input->post('flag')){
			$this->db->where("a.flag",$this->input->post('flag'));
		}
		$this->db->limit($data['limit'], $offset = ($page - 1) * $data['limit']);
		$this->db->order_by('a.id','desc');
		$data['items'] = $this->db->get()->result_array();

		return $data;
	}
	public function delete_material_color($id){
		$requset = $this->db->where('id',$id)->update('material_color',array('flag'=>-1));
		if($requset){
			return 1;
		}else{
			return 2;
		}
	}
	public function use_material_color($id){
		$requset = $this->db->where('id',$id)->update('material_color',array('flag'=>1));
		if($requset){
			return 1;
		}else{
			return 2;
		}
	}
	public function save_material_color(){
		$data = array(
			'color_name'=>$this->input->post('color_name'),
			//'flag'=>$this->input->post('flag')? 1 : 2,
			'create_date'=>date('Y-m-d H:i:s'),
			'modify_date'=>date('Y-m-d H:i:s'),
			'create_user'=>$this->session->userdata('user_id'),
			'modify_user'=>$this->session->userdata('user_id')
		);
		if(!$data['color_name']){
			return -1;
		}
		if(!$this->input->post('color_id')){
			$check = $this->db->select('*')->from('material_color')->where('color_name',$data['color_name'])->get()->row();
			if($check){
				return -2;
			}
			$rs = $this->db->insert('material_color',$data);
		}else{
			unset($data['create_date']);
			unset($data['create_user']);
			$check = $this->db->select('*')->from('material_color')->where(
				array(
					'color_name'=>$data['color_name'],
					'id <>'=>$this->input->post('color_id')
				)
			)->get()->row();
			if($check){
				return -2;
			}
			$rs = $this->db->where('id',$this->input->post('color_id'))->update('material_color',$data);
		}

		if($rs)
			return 1;
		else
			return -1;
	}

	public function get_material_color($id){
		$this->db->select()->from('material_color');
		$this->db->where('id',$id);
		return $this->db->get()->row_array();
	}

	public function get_material_all($flag){
		//获取详细列
		$this->db->select('a.material_name,a.id')->from('material a');
		if($flag){
			$this->db->where_in('a.flag',$flag);
		}
		$this->db->order_by('a.id','asc');
		$data = $this->db->get()->result_array();

		return $data;
	}

	public function get_color_all($flag){
		//获取详细列
		$this->db->select('a.color_name,a.id')->from('material_color a');
		if($flag){
			$this->db->where_in('a.flag',$flag);
		}
		$this->db->order_by('a.id','asc');
		$data = $this->db->get()->result_array();

		return $data;
	}

	//面料入库

	public function list_stock_in($page){
		$data['limit'] = $this->limit;
		//获取总记录数
		$this->db->select('count(distinct(a.id)) as num')->from('material_in_form a');
		$this->db->join('cust b','a.cust_id = b.id','left');
		$this->db->join('material_in_form_detail c','c.form_id = a.id','left');
		$this->db->join('material d','c.material_id = d.id','left');
		$this->db->join('material_color e','c.color_id = e.id','left');
		if($this->input->post('keyword')){
			$this->db->like('a.form_name',$this->input->post('keyword'));
		}
		if($this->input->post('cust_id')){
			$this->db->where('a.cust_id',$this->input->post('cust_id'));
		}
		if($this->input->post('flag')){
			$this->db->where("a.flag",$this->input->post('flag'));
		}

		$num = $this->db->get()->row();
		$data['total'] = $num->num;

		//搜索条件
		$data['flag'] = $this->input->post('flag')?$this->input->post('flag'):null;
		$data['cust_id'] = $this->input->post('cust_id')?$this->input->post('cust_id'):null;
		$data['keyword'] = $this->input->post('keyword')?$this->input->post('keyword'):null;
		//获取详细列
		$this->db->select("a.*,IFNULL(b.name,'公共库存') cust_name,group_concat(distinct d.material_name ORDER BY d.material_name) m_list,count(c.id) as count_all")->from('material_in_form a');
		$this->db->join('cust b','a.cust_id = b.id','left');
		$this->db->join('material_in_form_detail c','c.form_id = a.id','left');
		$this->db->join('material d','c.material_id = d.id','left');
		$this->db->join('material_color e','c.color_id = e.id','left');
		if($this->input->post('keyword')){
			$this->db->like('a.form_name',$this->input->post('keyword'));
		}
		if($this->input->post('cust_id')){
			$this->db->where('a.cust_id',$this->input->post('cust_id'));
		}
		if($this->input->post('flag')){
			$this->db->where("a.flag",$this->input->post('flag'));
		}
		$this->db->group_by('a.id');
		$this->db->limit($data['limit'], $offset = ($page - 1) * $data['limit']);
		$this->db->order_by('a.id','desc');
		$data['items'] = $this->db->get()->result_array();


		return $data;
	}

	public function save_stock(){
		$material_ids = $this->input->post('material_id');
		$color_ids = $this->input->post('color_id');
		$ganghaos = $this->input->post('ganghao');
		$meters_list= $this->input->post('meters');
		$cust_id = $this->input->post('cust_id');
		$remark = $this->input->post('remark');
		$stock_list = array();
		if(!is_array($material_ids)){
			return -2;
		}
		if($material_ids){
			foreach($material_ids as $idx => $material_id) {
				$stock_ = array(
					'material_id' => $material_id,
					'color_id' => $color_ids[$idx],
					'ganghao' => trim($ganghaos[$idx]),
					'original_meters' => trim($meters_list[$idx]),
					'turn_meters' => trim($meters_list[$idx]),
					'cust_id' => $cust_id,
					'original_cust_id' => $cust_id,
					'create_date'=>date('Y-m-d H:i:s'),
					'modify_date'=>date('Y-m-d H:i:s'),
					'create_user'=>$this->session->userdata('user_id'),
					'modify_user'=>$this->session->userdata('user_id'),
					'flag'=>1
				);
				if($stock_['material_id']=="" || $stock_['color_id']=="" || $stock_['ganghao']=="" || $stock_['turn_meters']==""){
					return -2;
				}
				$stock_list[] = $stock_;
			}
		}else{
			return -2;
		}
		if(!$stock_list){
			return -2;
		}
		$this->db->trans_start();//--------开始事务
		$data_form = array(
			'cust_id'=>	$cust_id,
			'remark'=>	$remark,
			'create_date'=>date('Y-m-d H:i:s'),
			'modify_date'=>date('Y-m-d H:i:s'),
			'create_user'=>$this->session->userdata('user_id'),
			'modify_user'=>$this->session->userdata('user_id'),
			'flag'=>1
		);
		$this->db->insert('material_in_form',$data_form);
		$form_id = $this->db->insert_id();
		$newStr=str_pad($form_id,4,"0",STR_PAD_LEFT);
		$this->db->where('id',$form_id)->update('material_in_form',array('form_name'=>'MI'.date('ymd', time()).$newStr));
		$stock_ins=array();
		$form_details = array();
		foreach($stock_list as $id_ => $s_){
			$detail_ = $s_;
			$detail_['form_id'] = $form_id;
			$stock_ins[] = $detail_;
			$form_details[] = $detail_;
		}
		$this->db->insert_batch('material_in_form_detail',$stock_ins);
		$this->db->insert_batch('material_stock',$form_details);
		$this->db->trans_complete();//------结束事务
		if ($this->db->trans_status() === FALSE) {
			return -1;
		} else {
			return 1;
		}

	}

}