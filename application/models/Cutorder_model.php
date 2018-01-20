<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

class Cutorder_model extends MY_Model
{
    
    public function __construct ()
    {
    	parent::__construct();
    }

    public function save_1(){
        $data = array(
            'cust_id'=>$this->input->post('cust_id'),
            'plan_date'=>$this->input->post('plan_date'),
            'status'=>1,
            'remark'=>$this->input->post('remark'),
            'create_date'=>date('Y-m-d H:i:s'),
            'modify_date'=>date('Y-m-d H:i:s'),
            'create_user'=>$this->session->userdata('user_id'),
            'modify_user'=>$this->session->userdata('user_id')
        );
        if(!$data['cust_id'])
            return -2;
        if($data['cust_id']!=-1){
            $cust_info = $this->db->select()->from('cust')->where(array(
                'id'=>$data['cust_id'],
                'flag'=>1
            ))->get()->row();
            if(!$cust_info)
                return -2;
        }
        $this->db->insert('cut_order',$data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function get_detail4write($id){
        $this->db->select('a.*,IFNULL(b.name,\'公共库存\') cust_name')->from('cut_order a');
        $this->db->join('cust b','a.cust_id = b.id','left');
        $this->db->where('a.id',$id);
        return $this->db->get()->row_array();
    }

    public function cut_in_mstock(){
        $re = 1;
        //die(var_dump($this->input->post()));
        $data=array(
            'cut_order_id'=>$this->input->post('cut_order_id'),
            'p_xs'=>$this->input->post('use_XS2')?$this->input->post('use_XS2'):0,
            'p_s'=>$this->input->post('use_S2')?$this->input->post('use_S2'):0,
            'p_m'=>$this->input->post('use_M2')?$this->input->post('use_M2'):0,
            'p_l'=>$this->input->post('use_L2')?$this->input->post('use_L2'):0,
            'p_xl'=>$this->input->post('use_XL2')?$this->input->post('use_XL2'):0,
            'p_2xl'=>$this->input->post('use_2XL2')?$this->input->post('use_2XL2'):0,
            'p_3xl'=>$this->input->post('use_3XL2')?$this->input->post('use_3XL2'):0,
            'p_4xl'=>$this->input->post('use_4XL2')?$this->input->post('use_4XL2'):0,
            'style_id'=>$this->input->post('style_id'),
            'one_use'=>$this->input->post('use_meters4one'),
            'cut_cust_id'=>$this->input->post('cut_cust_id'),
            'create_date'=>date('Y-m-d H:i:s'),
            'modify_date'=>date('Y-m-d H:i:s'),
            'create_user'=>$this->session->userdata('user_id'),
            'modify_user'=>$this->session->userdata('user_id')
        );

        if(!$data['cut_order_id'])
            return -2;
        if($data['one_use']<=0)
            return -2;
        if($data['style_id']<=0)
            return -2;
        if($data['p_xs']==0 && $data['p_s']==0 && $data['p_m']==0 && $data['p_l']==0 && $data['p_xl']==0 && $data['p_2xl']==0 && $data['p_3xl']==0 && $data['p_4xl']==0)
            return -2;
        $stock_ids = $this->input->post('stock_ids');
        if(!$stock_ids)
            return -2;
        if(!is_array($stock_ids))
            return -2;
        $insert_arr = array();
        foreach($stock_ids as $v_){
            if($v_>0){
                $m_stock = $this->db->select('*')->from('material_stock')->where(array(
                    'flag'=>1,
                    'status'=>1,
                    'id'=>$v_
                ))->where_in('cust_id',array('-1',$data['cut_cust_id']))->get()->row_array();
                if($m_stock){//检查是否可以添加此匹布
                    $check_ = $this->db->select('*')->from('cut_order_detail')->where(array(
                        'material_stock_id'=>$m_stock['id'],
                        'cut_order_id'=>$data['cut_order_id'],
                    ))->get()->row_array();
                    if(!$check_){//坚持是否存在同一匹布 重复添加
                        $insert_1=$data;
                        $insert_1['cust_id']=$m_stock['cust_id'];
                        $insert_1['material_id']=$m_stock['material_id'];
                        $insert_1['ganghao']=$m_stock['ganghao'];
                        $insert_1['material_stock_id']=$m_stock['id'];
                        $insert_arr[]=$insert_1;
                    }
                }else{
                    $re = -3;
                }
            }
        }
        if(!$insert_arr)
            return -2;
        $this->db->insert_batch('cut_order_detail',$insert_arr);
        return $re;

    }

}