<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Copyright (C) 2016
 * File: application/helpers/email_helper.php
 * Summary: email_helper
 * First writter:  renda <renda [dot] innovation [at] gmail [dot] com>
 */

if ( ! function_exists('email_member_create'))
{
	function email_member_create($param)
	{
		$CI =& get_instance();
		$CI->load->model('preferences_model');
		$param += requirement();
		
		$param['subject'] = 'Safetra - Registrasi Berhasil';
		
		// content email
		$query = $CI->preferences_model->info(array('slug' => 'member-create-success'));
		
		$email_content = '';
		if ($query->num_rows() > 0)
		{
			$email_content = $query->row()->content;
		}
		
		$send = send_email($param, $email_content);
		return $send;
	}
}

if( ! function_exists('requirement'))
{
	function requirement()
	{
		$CI =& get_instance();
		$CI->load->library('email');
		
		$config['useragent'] = 'safetra.co.id';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$CI->email->initialize($config);
		
		$param = array();
		
		return $param;
	}
}

if ( ! function_exists('send_email'))
{
	function send_email($param, $email_content)
	{
		$CI =& get_instance();
		
		foreach ($param as $key => $value)
		{
			$k = "{" . $key . "}";
			$email_content = str_replace($k, $value, $email_content);
		}
		
		$CI->email->from('marketing@safetra.co.id', 'Safetra Indonesia');
		$CI->email->to($param['email']);
		$CI->email->subject($param['subject']);
		$CI->email->message($email_content);
		
		$send = $CI->email->send();
		return $send;
	}
}

/* End of file email_helper.php */
/* Location: ./application/helpers/email_helper.php */