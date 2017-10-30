<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Member_course extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('member_course_model', 'the_model');
		$this->load->model('member_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_member = filter($this->post('id_member'));
		$id_promo_code = filter($this->post('id_promo_code'));
		$course_type = filter(trim($this->post('course_type')));
		$course_name = filter(trim($this->post('course_name')));
		
		$data = array();
		if ($id_member == FALSE)
		{
			$data['id_member'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($course_type == FALSE)
		{
			$data['course_type'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($course_name == FALSE)
		{
			$data['course_name'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['id_member'] = $id_member;
			$param['course_type'] = $course_type;
			$param['course_name'] = $course_name;
			$param['id_promo_code'] = $id_promo_code;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			
			$query = $this->the_model->create($param);
			
			if ($query > 0)
			{
				// send email
				$query2 = $this->member_model->info(array('id_member' => $id_member));
				
				if ($query2->num_rows() > 0)
				{
					$param2 = array();
					$param2['name'] = $query2->row()->name;
					$param2['email'] = $query2->row()->email;
					$send_email = email_member_create($param2);
					
					if ($send_email)
					{
						$data['send_email'] = 'success';
					}
					else
					{
						$data['send_email'] = 'failed';
					}
				}
				
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
		
        $id = filter($this->post('id_member_course'));
        
		$data = array();
        if ($id == FALSE)
		{
			$data['id_member_course'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_member_course' => $id));
			
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
				$data['id_member_course'] = 'not found';
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
		
		$id_member_course = filter($this->get('id_member_course'));
		
		$data = array();
		if ($id_member_course == FALSE)
		{
			$data['id_member_course'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_member_course != '')
			{
				$param['id_member_course'] = $id_member_course;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_member_course' => $row->id_member_course,
					'name' => $row->name,
					'email' => $row->email,
					'phone_number' => $row->phone_number,
					'status' => intval($row->status),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'company' => array(
						'id_company' => $row->id_company,
						'name' => $row->company_name,
						'pic_name' => $row->pic_name,
						'phone_number' => $row->company_phone_number,
						'logo' => $row->logo
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_member_course'] = 'Not Found';
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
		
		if (in_array($order, $this->config->item('default_member_course_order')) && ($order == TRUE))
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
					'id_member_course' => $row->id_member_course,
					'id_member' => $row->id_member,
					'course_type' => intval($row->course_type),
					'course_name' => $row->course_name,
					'id_promo_code' => $row->id_promo_code,
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
		
		$id_member_course = filter($this->post('id_member_course'));
		$id_company = filter($this->post('id_company'));
		$name = filter(trim($this->post('name')));
		$email = filter(trim(strtolower($this->post('email'))));
		$password = filter(trim($this->post('password')));
		$phone_number = filter(trim($this->post('phone_number')));
		$status = filter(trim(intval($this->post('status'))));
		
		$data = array();
		if ($id_member_course == FALSE)
		{
			$data['id_member_course'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_member_course_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_member_course' => $id_member_course));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				
				if ($id_company == TRUE)
				{
					$param['id_company'] = $id_company;
				}
				
				if ($name == TRUE)
				{
					$param['name'] = $name;
				}
				
				if ($email == TRUE)
				{
					$param['email'] = $email;
				}
				
				if ($password == TRUE)
				{
					$param['password'] = md5($password);
				}
				
				if ($phone_number == TRUE)
				{
					$param['phone_number'] = $phone_number;
				}
				
				if ($status == TRUE)
				{
					$param['status'] = $status;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_member_course, $param);
					
					if ($update == TRUE)
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
				$data['id_member_course'] = 'not found';
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
