<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Member_certification extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('member_certification_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_member = filter($this->post('id_member'));
		$id_program_sub = filter($this->post('id_program_sub'));
		$certificate_url = filter($this->post('certificate_url'));
		$date = filter($this->post('date'));
		
		$data = array();
		if ($id_member == FALSE)
		{
			$data['id_member'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($id_program_sub == FALSE)
		{
			$data['id_program_sub'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($certificate_url == FALSE)
		{
			$data['certificate_url'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($date == FALSE)
		{
			$data['date'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['id_member'] = $id_member;
			$param['id_program_sub'] = $id_program_sub;
			$param['certificate_url'] = $certificate_url;
			$param['date'] = $date;
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
		
        $id = filter($this->post('id_member_certification'));
        
		$data = array();
        if ($id == FALSE)
		{
			$data['id_member_certification'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_member_certification' => $id));
			
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
				$data['id_member_certification'] = 'not found';
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
		
		$id_member_certification = filter($this->get('id_member_certification'));
		
		$data = array();
		if ($id_member_certification == FALSE)
		{
			$data['id_member_certification'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_member_certification != '')
			{
				$param['id_member_certification'] = $id_member_certification;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_member_certification' => $row->id_member_certification,
					'certificate_url' => $row->certificate_url,
					'date' => $row->date,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'member' => array(
						'id_member' => $row->id_member,
						'name' => $row->member_name,
						'email' => $row->email,
						'phone_number' => $row->phone_number,
						'status' => intval($row->status)
					),
					'program_sub' => array(
						'id_program_sub' => $row->id_program_sub,
						'name' => $row->program_sub_name,
						'slug' => $row->slug
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_member_certification'] = 'Not Found';
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
		
		$offset = filter(trim(intval($this->get('offset'))));
		$limit = filter(trim(intval($this->get('limit'))));
		$order = filter(trim(strtolower($this->get('order'))));
		$sort = filter(trim(strtolower($this->get('sort'))));
		$id_member = filter($this->get('id_member'));
		$id_program_sub = filter($this->get('id_program_sub'));
		
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
		
		if (in_array($order, $this->config->item('default_member_certification_order')) && ($order == TRUE))
		{
			$order = $order;
		}
		else
		{
			$order = 'created_date';
		}
		
		if (in_array($sort, $this->config->item('default_sort')) && ($sort == TRUE))
		{
			$sort = $sort;
		}
		else
		{
			$sort = 'desc';
		}
		
		$param = array();
		$param2 = array();
		if ($id_member != '')
        {
            $param['id_member'] = $id_member;
            $param2['id_member'] = $id_member;
        }
		if ($id_program_sub != '')
        {
            $param['id_program_sub'] = $id_program_sub;
            $param2['id_program_sub'] = $id_program_sub;
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
					'id_member_certification' => $row->id_member_certification,
					'id_member' => $row->id_member,
					'id_program_sub' => $row->id_program_sub,
					'certificate_url' => $row->certificate_url,
					'date' => $row->date,
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
		
		$id_member_certification = filter($this->post('id_member_certification'));
		$id_member = filter($this->post('id_member'));
		$id_program_sub = filter($this->post('id_program_sub'));
		$certificate_url = filter($this->post('certificate_url'));
		$date = filter($this->post('date'));
		
		$data = array();
		if ($id_member_certification == FALSE)
		{
			$data['id_member_certification'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_member_certification' => $id_member_certification));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_member == TRUE)
				{
					$param['id_member'] = $id_member;
				}
				
				if ($id_program_sub == TRUE)
				{
					$param['id_program_sub'] = $id_program_sub;
				}
				
				if ($certificate_url == TRUE)
				{
					$param['certificate_url'] = $certificate_url;
				}
				
				if ($date == TRUE)
				{
					$param['date'] = $date;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_member_certification, $param);
					
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
				$data['id_member_certification'] = 'not found';
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
