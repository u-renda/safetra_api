<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_course_model extends CI_Model {

    var $table = 'member_course';
	var $table_id = 'id_member_course';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set($this->table_id, 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		return $query;
    }
    
    function delete($id)
    {
        $this->db->where($this->table_id, $id);
        $query = $this->db->delete($this->table);
        return $query;
    }
    
    function info($param)
    {
        $where = array();
        if (isset($param['id_member_course']) == TRUE)
        {
            $where += array('id_member_course' => $param['id_member_course']);
        }
        
        $this->db->select('id_member_course, '.$this->table.'.id_company, '.$this->table.'.name, email,
						  '.$this->table.'.phone_number, status, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, company.name AS company_name, pic_name,
						  company.phone_number AS company_phone_number, logo');
        $this->db->from($this->table);
		$this->db->join('company', $this->table.'.id_company = company.id_company', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        
        $this->db->select('id_member_course, id_member, course_type, course_name, id_promo_code,
						  created_date, updated_date');
        $this->db->from($this->table);
        $this->db->where($where);
        $this->db->order_by($param['order'], $param['sort']);
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
		return $query;
    }
    
    function lists_count($param)
    {
        $where = array();
        if (isset($param['id_company']) == TRUE)
        {
            $where += array('id_company' => $param['id_company']);
        }
        if (isset($param['status']) == TRUE)
        {
            $where += array('status' => $param['status']);
        }
        
        $this->db->select($this->table_id);
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->count_all_results();
        return $query;
    }
    
    function update($id, $param)
    {
        $this->db->where($this->table_id, $id);
        $query = $this->db->update($this->table, $param);
        return $query;
    }
}