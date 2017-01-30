<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Preferences extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('preferences_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$name = filter(trim($this->post('name')));
		$content = filter(trim($this->post('content')));

		$data = array();
		if ($name == FALSE)
		{
			$data['name'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		if ($content == FALSE)
		{
			$data['content'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_preferences_name($name) == FALSE && $name == TRUE)
		{
			$data['name'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$url_title = url_title(strtolower($name));
			
			if (check_preferences_slug($url_title) == FALSE)
			{
				$counter = random_string('numeric',5);
				$slug = url_title(strtolower(''.$name.'-'.$counter.''));
			}
			else
			{
				$slug = $url_title;
			}
			
			$param = array();
			$param['name'] = $name;
			$param['slug'] = $slug;
			$param['content'] = $content;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->the_model->create($param);
			
			if ($query > 0)
			{
				$data['create'] = 'success';
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['create'] = 'failed';
				$validation = 'error';
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
	
	function delete_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
        $id = filter($this->post('id_preferences'));
        
		$data = array();
        if ($id == FALSE)
		{
			$data['id_preferences'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_preferences' => $id));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id);
				
				if ($delete > 0)
				{
					$data['delete'] = 'success';
					$validation = "ok";
					$code = 200;
				}
				else
				{
					$data['delete'] = 'failed';
					$validation = "error";
					$code = 400;
				}
			}
			else
			{
				$data['id_preferences'] = 'not found';
				$validation = "error";
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
	
	function info_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_preferences = filter($this->get('id_preferences'));
		$name = filter(trim($this->get('name')));
		
		$data = array();
		if ($id_preferences == FALSE && $name == FALSE)
		{
			$data['id_preferences'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_preferences != '')
			{
				$param['id_preferences'] = $id_preferences;
			}
			else
			{
				$param['name'] = $name;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_preferences' => $row->id_preferences,
					'name' => $row->name,
					'slug' => $row->slug,
					'content' => $row->content,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date
				);
				
                $validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_preferences'] = 'Not Found';
				$validation = 'error';
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
	
	function lists_get()
	{
		$this->benchmark->mark('code_start');
		
		$offset = filter(intval(trim($this->get('offset'))));
		$limit = filter(intval(trim($this->get('limit'))));
		$order = filter(trim($this->get('order')));
		$sort = filter(trim($this->get('sort')));
		
		if ($limit == TRUE && $limit < 20)
		{
			$limit = $limit;
		}
		elseif ($limit == TRUE && in_array($this->rest->key, $this->config->item('allow_api_key')))
		{
			$limit = $limit;
		}
		else
		{
			$limit = 20;
		}
		
		if ($offset == TRUE)
		{
			$offset = $offset;
		}
		else
		{
			$offset = 0;
		}
		
		if (in_array($order, $this->config->item('default_preferences_order')) && ($order == TRUE))
		{
			$order = $order;
		}
		else
		{
			$order = 'name';
		}
		
		if (in_array($sort, $this->config->item('default_sort')) && ($sort == TRUE))
		{
			$sort = $sort;
		}
		else
		{
			$sort = 'asc';
		}
		
		$param = array();
		$param2 = array();
		
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->the_model->lists($param);
		$total = $this->the_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_preferences' => $row->id_preferences,
					'name' => $row->name,
					'slug' => $row->slug,
					'content' => $row->content,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date
				);
			}
		}

		$rv = array();
		$rv['message'] = 'ok';
		$rv['code'] = 200;
		$rv['limit'] = intval($limit);
		$rv['offset'] = intval($offset);
		$rv['total'] = intval($total);
		$rv['count'] = count($data);
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $rv['code']);
	}
	
	function update_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_preferences = filter($this->post('id_preferences'));
		$name = filter(trim($this->post('name')));
		$content = filter(trim($this->post('content')));

		$data = array();
		if ($id_preferences == FALSE)
		{
			$data['id_preferences'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_preferences' => $id_preferences));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($name == TRUE)
				{
					$param['name'] = $name;
				}
				if ($content == TRUE)
				{
					$param['content'] = $content;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_preferences, $param);
					
					if ($update)
					{
						$data['update'] = 'success';
						$validation = 'ok';
						$code = 200;
					}
				}
				else
				{
					$data['update'] = 'failed';
					$validation = 'error';
					$code = 400;
				}
			}
			else
			{
				$data['id_preferences'] = 'not found';
				$validation = 'error';
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
}
