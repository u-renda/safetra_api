<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Media_model extends CI_Model {

    var $table = 'media';
	var $table_id = 'id_media';
    
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
        if (isset($param['id_media']) == TRUE)
        {
            $where += array('id_media' => $param['id_media']);
        }
        
        $this->db->select('id_media, '.$this->table.'.id_media_album, media_url,
						  '.$this->table.'.created_date, '.$this->table.'.updated_date, name, slug');
        $this->db->from($this->table);
		$this->db->join('media_album', $this->table.'.id_media_album = media_album.id_media_album', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['id_media_album']) == TRUE)
        {
            $where += array('id_media_album' => $param['id_media_album']);
        }
        
        $this->db->select('id_media, id_media_album, media_url, created_date, updated_date');
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
        if (isset($param['id_media_album']) == TRUE)
        {
            $where += array('id_media_album' => $param['id_media_album']);
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