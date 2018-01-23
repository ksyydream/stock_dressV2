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
        $show_stock_ids=array();
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
                        $show_stock_ids[]=$m_stock['id'];
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
        $this->db->select("a.*,d.material_name,e.color_name,c.style_name,b.turn_meters,b.ganghao,b.cust_id")->from('material_stock b');
        $this->db->join('cut_order_detail a','a.material_stock_id = b.id','inner');
        $this->db->join('style c','a.style_id = c.id','left');
        $this->db->join('material d','b.material_id = d.id','left');
        $this->db->join('material_color e','b.color_id = e.id','left');
        $this->db->where(array(
           'b.flag'=>1,
            'b.status'=>1,
            'a.cut_order_id'=>$data['cut_order_id']
        ));
        $this->db->where_in('b.id',$show_stock_ids);
        $show_data = $this->db->get()->result_array();
        foreach($show_data as $key_=>$show_){
            $p_all = $show_['p_xs'] + $show_['p_s'] + $show_['p_m'] + $show_['p_l'] + $show_['p_xl'] + $show_['p_2xl'] + $show_['p_3xl'] + $show_['p_4xl'];
            $turn_meters_ = $show_['turn_meters'];
            $one_use_ = $show_['one_use'];
            $use_p_ = floor($turn_meters_/($one_use_*$p_all));
            $show_data[$key_]['ls_xs'] = round($show_['p_xs']*$use_p_);
            $show_data[$key_]['ls_s'] = round($show_['p_s']*$use_p_);
            $show_data[$key_]['ls_m'] = round($show_['p_m']*$use_p_);
            $show_data[$key_]['ls_l'] = round($show_['p_l']*$use_p_);
            $show_data[$key_]['ls_xl'] = round($show_['p_xl']*$use_p_);
            $show_data[$key_]['ls_2xl'] = round($show_['p_2xl']*$use_p_);
            $show_data[$key_]['ls_3xl'] = round($show_['p_3xl']*$use_p_);
            $show_data[$key_]['ls_4xl'] = round($show_['p_4xl']*$use_p_);
            $show_data[$key_]['ls_all'] = $p_all*$use_p_;
            $show_data[$key_]['ls_leave_m'] = round(($turn_meters_ - $one_use_*$p_all*$use_p_),2);
        }
        $re_show['item'] = $show_data;
        $re_show['res'] = $re;
        return $re_show;

    }

    public function delete_show(){
        $check = $this->db->select()->from('cut_order')->where(array(
            'id'=>$this->input->post('cut_order_id'),
            'flag'=>1
        ))->get()->row();
        if(!$check)
            return -1;
        $res = $this->db->delete('cut_order_detail',array(
            'id'=>$this->input->post('cut_order_detail_id'),
            'cut_order_id'=>$this->input->post('cut_order_id'),
        ));
        return $res;
    }

    public function save_cut(){
        $cut_order_id = $this->input->post('cut_order_id');
        if(!$cut_order_id)
            return -2;
        $order = $this->db->select()->from('cut_order')->where(array('id'=>$cut_order_id,'status'=>1))->get()->row_array();
        if(!$order)
            return -2;
        $detail_ids = $this->input->post('cut_order_detail_id');
        $xs_arr=$this->input->post('work_XS');
        $s_arr=$this->input->post('work_S');
        $m_arr=$this->input->post('work_M');
        $l_arr=$this->input->post('work_L');
        $xl_arr=$this->input->post('work_XL');
        $xxl_arr=$this->input->post('work_2XL');
        $xxxl_arr=$this->input->post('work_3XL');
        $xxxxl_arr=$this->input->post('work_4XL');
        $remark_arr=$this->input->post('remark');
        if(!is_array($detail_ids))
            return -2;
        if(!$detail_ids)
            return -2;
        $this->db->trans_start();//--------开始事务
        $this->db->where('cut_order_id',$cut_order_id)->where_not_in('id',$detail_ids)->delete('cut_order_detail');
        foreach($detail_ids as $key_ => $v){
            $check_ = $this->db->select('a.id,a.one_use,b.material_id,b.color_id,b.ganghao,b.turn_meters')->from('cut_order_detail a')
                ->join('material_stock b','a.material_stock_id = b.id','inner')
                ->where(array(
                    'a.cut_order_id'=>$cut_order_id,
                    'a.id'=>$v,
                    'b.flag'=>1,
                    'b.status'=>1,
                ))->get()->row_array();
            if($check_){
                $update_data = array(
                    'xs'=>$xs_arr[$key_],
                    's'=>$s_arr[$key_],
                    'm'=>$m_arr[$key_],
                    'l'=>$l_arr[$key_],
                    'xl'=>$xl_arr[$key_],
                    '2xl'=>$xxl_arr[$key_],
                    '3xl'=>$xxxl_arr[$key_],
                    '4xl'=>$xxxxl_arr[$key_],
                    'remark'=>$remark_arr[$key_],
                    'color_id'=>$check_['color_id'],
                    'material_id'=>$check_['material_id'],
                    'ganghao'=>$check_['ganghao']
                );
                //$one_use_ = $check_['one_use'];
                $all_work = $update_data['xs'] + $update_data['s'] +$update_data['m'] +$update_data['l'] +$update_data['xl'] +$update_data['2xl'] +$update_data['3xl'] +$update_data['4xl'] ;
                $max_work = floor($check_['turn_meters']/$check_['one_use']);
                if($all_work > $max_work){
                    $p_all = $check_['p_xs'] + $check_['p_s'] + $check_['p_m'] + $check_['p_l'] + $check_['p_xl'] + $check_['p_2xl'] + $check_['p_3xl'] + $check_['p_4xl'];
                    $turn_meters_ = $check_['turn_meters'];
                    $one_use_ = $check_['one_use'];
                    $use_p_ = floor($turn_meters_/($one_use_*$p_all));
                    if($use_p_ >= 1){
                        $update_data['xs'] = round($check_['p_xs']*$use_p_);
                        $update_data['s'] = round($check_['p_s']*$use_p_);
                        $update_data['m'] = round($check_['p_m']*$use_p_);
                        $update_data['l'] = round($check_['p_l']*$use_p_);
                        $update_data['xl'] = round($check_['p_xl']*$use_p_);
                        $update_data['2xl'] = round($check_['p_2xl']*$use_p_);
                        $update_data['3xl'] = round($check_['p_3xl']*$use_p_);
                        $update_data['4xl'] = round($check_['p_4xl']*$use_p_);
                        $all_work = $update_data['xs'] + $update_data['s'] +$update_data['m'] +$update_data['l'] +$update_data['xl'] +$update_data['2xl'] +$update_data['3xl'] +$update_data['4xl'] ;
                    }else{
                        continue;
                    }
                }
                $update_data['all_use'] = $all_work * $check_['one_use'];
                $update_data['leave_meters'] = $check_['turn_meters'] - $all_work * $check_['one_use'];
                $this->db->update('cut_order_detail',$update_data);
            }
        }
        $this->db->where(array('id'=>$cut_order_id,'status'=>1))->update('cut_order',array('status'=>2));
        $this->db->trans_complete();//------结束事务
        if ($this->db->trans_status() === FALSE) {
            return -1;
        } else {
            return 1;
        }
    }

    public function cut_list($page,$status=100){
        $data['limit'] = $this->limit;
        //获取总记录数
        $this->db->select('count(distinct(a.id)) as num')->from('cut_order a');
        $this->db->join('cust b','a.cust_id = b.id','left');
        $this->db->join('cut_order_detail c','c.cut_order_id = a.id','left');
        $this->db->join('material d','c.material_id = d.id','left');
        $this->db->join('material_color e','c.color_id = e.id','left');
        $this->db->join('style f','c.style_id = f.id','left');
        if($this->input->post('material_id') && $this->input->post('material_id')!='-2'){
            $this->db->where('a.material_id',$this->input->post('material_id'));
        }
        if($this->input->post('cust_id') && $this->input->post('cust_id')!='-2'){
            $this->db->where('a.cust_id',$this->input->post('cust_id'));
        }
        if($this->input->post('color_id') && $this->input->post('color_id')!='-2'){
            $this->db->where('a.color_id',$this->input->post('color_id'));
        }
        $this->db->where('a.status',$status);
        if($this->input->post('ganghao')){
            $this->db->like("a.ganghao",$this->input->post('ganghao'));
        }
        if($this->input->POST('Istart_date')) {
            $this->db->where('DATE_FORMAT(a.in_date,\'%Y-%m-%d\') >=', $this->input->POST('Istart_date'));
        }
        if($this->input->POST('Iend_date')) {
            $this->db->where('DATE_FORMAT(a.in_date,\'%Y-%m-%d\') <=', $this->input->POST('Iend_date'));
        }
        $num = $this->db->get()->row();
        $data['total'] = $num->num;

        //搜索条件
        $data['flag'] = $this->input->post('flag')?$this->input->post('flag'):null;
        $data['ganghao'] = $this->input->post('ganghao')?$this->input->post('ganghao'):null;
        $data['cust_id'] = $this->input->post('cust_id')?$this->input->post('cust_id'):null;
        $data['color_id'] = $this->input->post('color_id')?$this->input->post('color_id'):null;
        $data['material_id'] = $this->input->post('material_id')?$this->input->post('material_id'):null;
        $data['Istart_date'] = $this->input->post('Istart_date')?$this->input->post('Istart_date'):null;
        $data['Iend_date'] = $this->input->post('Iend_date')?$this->input->post('Iend_date'):null;
        //获取详细列
        $this->db->select("a.*,IFNULL(b.name,'公共库存') cust_name,group_concat(d.material_name) m_list,group_concat(f.style_name) s_list")->from('cut_order a');
        $this->db->join('cust b','a.cust_id = b.id','left');
        $this->db->join('cut_order_detail c','c.cut_order_id = a.id','left');
        $this->db->join('material d','c.material_id = d.id','left');
        $this->db->join('material_color e','c.color_id = e.id','left');
        $this->db->join('style f','c.style_id = f.id','left');
        if($this->input->post('material_id') && $this->input->post('material_id')!='-2'){
            $this->db->where('a.material_id',$this->input->post('material_id'));
        }
        if($this->input->post('cust_id') && $this->input->post('cust_id')!='-2'){
            $this->db->where('a.cust_id',$this->input->post('cust_id'));
        }
        if($this->input->post('color_id') && $this->input->post('color_id')!='-2'){
            $this->db->where('a.color_id',$this->input->post('color_id'));
        }
        $this->db->where('a.status',$status);
        if($this->input->post('ganghao')){
            $this->db->like("a.ganghao",$this->input->post('ganghao'));
        }
        if($this->input->POST('Istart_date')) {
            $this->db->where('DATE_FORMAT(a.in_date,\'%Y-%m-%d\') >=', $this->input->POST('Istart_date'));
        }
        if($this->input->POST('Iend_date')) {
            $this->db->where('DATE_FORMAT(a.in_date,\'%Y-%m-%d\') <=', $this->input->POST('Iend_date'));
        }
        $this->db->group_by('a.id');
        $this->db->limit($data['limit'], $offset = ($page - 1) * $data['limit']);
        $this->db->order_by('a.create_date','desc');
        $data['items'] = $this->db->get()->result_array();


        return $data;
    }

}