<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Program_sub extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('program_sub_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_program = filter($this->post('id_program'));
		$name = filter(trim($this->post('name')));
		$program_objective = $this->post('program_objective');
		$training_purpose = $this->post('training_purpose');
		$requirements_of_participant = $this->post('requirements_of_participant');
		$training_material = $this->post('training_material');
		$others = filter(trim($this->post('others')));
		
		$data = array();
		if ($id_program == FALSE)
		{
			$data['id_program'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($name == FALSE)
		{
			$data['name'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($program_objective == FALSE)
		{
			$data['program_objective'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($training_purpose == FALSE)
		{
			$data['training_purpose'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($requirements_of_participant == FALSE)
		{
			$data['requirements_of_participant'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($training_material == FALSE)
		{
			$data['training_material'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$url_title = url_title(strtolower($name));
			
			if (check_program_sub_slug($url_title) == FALSE)
			{
				$counter = random_string('numeric',5);
				$slug = url_title(strtolower(''.$name.'-'.$counter.''));
			}
			else
			{
				$slug = $url_title;
			}
			
			$param = array();
			$param['id_program'] = $id_program;
			$param['name'] = $name;
			$param['slug'] = $slug;
			$param['program_objective'] = $program_objective;
			$param['training_purpose'] = $training_purpose;
			$param['requirements_of_participant'] = $requirements_of_participant;
			$param['training_material'] = $training_material;
			$param['others'] = $others;
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
		
        $id = filter($this->post('id_program_sub'));
        
		$data = array();
        if ($id == FALSE)
		{
			$data['id_program_sub'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_program_sub' => $id));
			
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
				$data['id_program_sub'] = 'not found';
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
		
		$id_program_sub = filter($this->get('id_program_sub'));
		$slug = filter(trim($this->get('slug')));
		
		$data = array();
		if ($id_program_sub == FALSE && $slug == FALSE)
		{
			$data['id_program_sub'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_program_sub != '')
			{
				$param['id_program_sub'] = $id_program_sub;
			}
			else
			{
				$param['slug'] = $slug;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_program_sub' => $row->id_program_sub,
					'name' => $row->name,
					'slug' => $row->slug,
					'introduction' => $row->introduction,
					'training_purpose' => $row->training_purpose,
					'target_participant' => $row->target_participant,
					'course_content' => $row->course_content,
					'others' => $row->others,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'program' => array(
						'id_program' => $row->id_program,
						'name' => $row->program_name,
						'slug' => $row->program_slug
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_program_sub'] = 'Not Found';
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
		$id_program = filter($this->get('id_program'));
		
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
		
		if (in_array($order, $this->config->item('default_program_sub_order')) && ($order == TRUE))
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
        if ($id_program != '')
        {
            $param['id_program'] = $id_program;
            $param2['id_program'] = $id_program;
        }
		
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
					'id_program_sub' => $row->id_program_sub,
					'id_program' => $row->id_program,
					'name' => $row->name,
					'slug' => $row->slug,
					'introduction' => $row->introduction,
					'training_purpose' => $row->training_purpose,
					'target_participant' => $row->target_participant,
					'course_content' => $row->course_content,
					'others' => $row->others,
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
		
		$id_program_sub = filter($this->post('id_program_sub'));
		$id_program = filter($this->post('id_program'));
		$name = filter(trim($this->post('name')));
		$program_objective = $this->post('program_objective');
		$training_purpose = $this->post('training_purpose');
		$requirements_of_participant = $this->post('requirements_of_participant');
		$training_material = $this->post('training_material');
		$others = filter(trim($this->post('others')));
		
		$data = array();
		if ($id_program_sub == FALSE)
		{
			$data['id_program_sub'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_program_sub' => $id_program_sub));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_program == TRUE)
				{
					$param['id_program'] = $id_program;
				}
				
				if ($name == TRUE)
				{
					$param['name'] = $name;
				}
				
				if ($program_objective == TRUE)
				{
					$param['program_objective'] = $program_objective;
				}
				
				if ($training_purpose == TRUE)
				{
					$param['training_purpose'] = $training_purpose;
				}
				
				if ($requirements_of_participant == TRUE)
				{
					$param['requirements_of_participant'] = $requirements_of_participant;
				}
				
				if ($training_material == TRUE)
				{
					$param['training_material'] = $training_material;
				}
				
				if ($others == TRUE)
				{
					$param['others'] = $others;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_program_sub, $param);
					
					if ($update > 0)
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
				$data['id_program_sub'] = 'not found';
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
