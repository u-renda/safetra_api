<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_certification_model extends CI_Model {

    var $table = 'member_certification';
    var $table_id = 'id_member_certification';
    
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
        if (isset($param['id_member_certification']) == TRUE)
        {
            $where += array('id_member_certification' => $param['id_member_certification']);
        }
        
        $this->db->select('id_member_certification, '.$this->table.'.id_member,
						  '.$this->table.'.id_program_sub, certificate_url, date,
						  '.$this->table.'.created_date, '.$this->table.'.updated_date,
						  member.name AS member_name, email, phone_number, status,
						  program_sub.name AS program_sub_name, slug');
        $this->db->from($this->table);
        $this->db->join('member', $this->table.'.id_member = member.id_member', 'left');
        $this->db->join('program_sub', $this->table.'.id_program_sub = program_sub.id_program_sub', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['id_member']) == TRUE)
        {
            $where += array('id_member' => $param['id_member']);
        }
        if (isset($param['id_program_sub']) == TRUE)
        {
            $where += array('id_program_sub' => $param['id_program_sub']);
        }
        
        $this->db->select('id_member_certification, id_member, id_program_sub, certificate_url, date,
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
        if (isset($param['id_member']) == TRUE)
        {
            $where += array('id_member' => $param['id_member']);
        }
        if (isset($param['id_program_sub']) == TRUE)
        {
            $where += array('id_program_sub' => $param['id_program_sub']);
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