<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Admin extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('admin_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$name = filter(trim(strtolower($this->post('name'))));
		$username = filter(trim(strtolower($this->post('username'))));
		$password = filter(trim($this->post('password')));
		$email = filter(trim(strtolower($this->post('email'))));
		$photo = filter($this->post('photo'));
		$status = filter($this->post('status'));
		$role = filter($this->post('role'));
		$job_title = filter(trim($this->post('job_title')));
		
		$data = array();
		if ($name == FALSE)
		{
			$data['name'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($username == FALSE)
		{
			$data['username'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($password == FALSE)
		{
			$data['password'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($email == FALSE)
		{
			$data['email'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($status == FALSE)
		{
			$data['status'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($role == FALSE)
		{
			$data['role'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($job_title == FALSE)
		{
			$data['job_title'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_admin_name($name) == FALSE && $name == TRUE)
		{
			$data['name'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_admin_username($username) == FALSE && $username == TRUE)
		{
			$data['username'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_admin_email($email) == FALSE && $email == TRUE)
		{
			$data['email'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if (valid_email($email) == FALSE && $email == TRUE)
		{
			$data['email'] = 'wrong format';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($role, $this->config->item('default_admin_role')) == FALSE && $role == TRUE)
		{
			$data['role'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_admin_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['name'] = $name;
			$param['username'] = $username;
			$param['password'] = md5($password);
			$param['email'] = $email;
			$param['photo'] = $photo;
			$param['status'] = $status;
			$param['role'] = $role;
			$param['job_title'] = $job_title;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->the_model->create($param);
			
			if ($query > 0)
			{
				// bisa tambahin pengiriman email ke admin
				
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
		
        $id = filter($this->post('id_admin'));
        
		$data = array();
        if ($id == FALSE)
		{
			$data['id_admin'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_admin' => $id));
			
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
				$data['id_admin'] = 'not found';
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
		
		$id_admin = filter($this->get('id_admin'));
		$username = filter(trim($this->get('username')));
		
		$data = array();
		if ($id_admin == FALSE && $username == FALSE)
		{
			$data['id_admin'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_admin != '')
			{
				$param['id_admin'] = $id_admin;
			}
			else
			{
				$param['username'] = $username;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_admin' => $row->id_admin,
					'name' => $row->name,
					'username' => $row->username,
					'email' => $row->email,
					'photo' => $row->photo,
					'status' => intval($row->status),
					'role' => intval($row->role),
					'job_title' => $row->job_title,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_admin'] = 'Not Found';
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
		$role = filter(trim($this->get('role')));
		$status = filter(trim($this->get('status')));
		
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
		
		if (in_array($order, $this->config->item('default_admin_order')) && ($order == TRUE))
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
		
		if (in_array($role, $this->config->item('default_admin_role')) && ($role == TRUE))
		{
			$role = $role;
		}
		
		if (in_array($status, $this->config->item('default_admin_status')) && ($status == TRUE))
		{
			$status = $status;
		}
		
		$param = array();
		$param2 = array();
		if ($role == TRUE)
		{
			$param['role'] = intval($role);
			$param2['role'] = intval($role);
		}
		
		if ($status == TRUE)
		{
			$param['status'] = intval($status);
			$param2['status'] = intval($status);
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
					'id_admin' => $row->id_admin,
					'name' => $row->name,
					'username' => $row->username,
					'email' => $row->email,
					'photo' => $row->photo,
					'status' => intval($row->status),
					'role' => intval($row->role),
					'job_title' => $row->job_title,
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
		
		$id_admin = filter($this->post('id_admin'));
		$name = filter(trim(strtolower($this->post('name'))));
		$username = filter(trim(strtolower($this->post('username'))));
		$password = filter(trim($this->post('password')));
		$email = filter(trim(strtolower($this->post('email'))));
		$photo = filter($this->post('photo'));
		$status = filter($this->post('status'));
		$role = filter($this->post('role'));
		$job_title = filter(trim($this->post('job_title')));
		
		$data = array();
		if ($id_admin == FALSE)
		{
			$data['id_admin'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (valid_email($email) == FALSE && $email == TRUE)
		{
			$data['email'] = 'wrong format';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($role, $this->config->item('default_admin_role')) == FALSE && $role == TRUE)
		{
			$data['role'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_admin_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_admin' => $id_admin));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($name == TRUE)
				{
					$param['name'] = $name;
				}
				
				if ($username == TRUE)
				{
					$param['username'] = $username;
				}
				
				if ($password == TRUE)
				{
					$param['password'] = md5($password);
					
					// bisa tambahin kirim email karena ganti password
				}
				
				if ($email == TRUE)
				{
					$param['email'] = $email;
					
					// bisa tambahin kirim email konfirmasi karena ganti email
				}
				
				if ($photo == TRUE)
				{
					$param['photo'] = $photo;
				}
				
				if ($status == TRUE)
				{
					$param['status'] = $status;
				}
				
				if ($role == TRUE)
				{
					$param['role'] = $role;
				}
				
				if ($job_title == TRUE)
				{
					$param['job_title'] = $job_title;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_admin, $param);
					
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
				$data['id_admin'] = 'not found';
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
	
	// Dipakai untuk login karena butuh username & password (required) 
	function valid_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$username = filter(trim(strtolower($this->post('username'))));
		$password = filter(trim($this->post('password')));
		
		$data = array();
		if ($username == FALSE)
		{
			$data['username'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($password == FALSE)
		{
			$data['password'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('username' => $username));
			
			if ($query->num_rows() > 0)
			{
				$check_pass = $query->row()->password;
				$pass = md5($password);
				
				if ($check_pass == $pass)
				{
					$data['valid'] = 'yes!';
					$validation = 'ok';
					$code = 200;
				}
				else
				{
					$data['valid'] = 'no!';
					$validation = 'error';
					$code = 400;
				}
			}
			else
			{
				$data['username'] = 'not found';
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
