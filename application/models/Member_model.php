<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_model extends CI_Model {

    var $table = 'member';
	var $table_id = 'id_member';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set($this->table_id, 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		$id = $this->db->insert_id();
		return $id;
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
        if (isset($param['id_member']) == TRUE)
        {
            $where += array('id_member' => $param['id_member']);
        }
        
        $this->db->select('id_member, '.$this->table.'.id_company, '.$this->table.'.name, email,
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
        if (isset($param['id_company']) == TRUE)
        {
            $where += array('id_company' => $param['id_company']);
        }
        if (isset($param['status']) == TRUE)
        {
            $where += array('status' => $param['status']);
        }
        
        $this->db->select('id_member, id_company, name, email, phone_number, status, created_date,
						  updated_date');
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