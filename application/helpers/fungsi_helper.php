<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('check_admin_email'))
{
    function check_admin_email($param)
	{
        $CI =& get_instance();
        $CI->load->model('admin_model', 'the_model');
        
		$query = $CI->the_model->info(array('email' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_admin_name'))
{
    function check_admin_name($param)
	{
        $CI =& get_instance();
        $CI->load->model('admin_model', 'the_model');
        
		$query = $CI->the_model->info(array('name' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_admin_username'))
{
    function check_admin_username($param)
	{
        $CI =& get_instance();
        $CI->load->model('admin_model', 'the_model');
        
		$query = $CI->the_model->info(array('username' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_article_slug'))
{
    function check_article_slug($param)
	{
        $CI =& get_instance();
        $CI->load->model('srticle_model', 'the_model');
        
		$query = $CI->the_model->info(array('slug' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_client_name'))
{
    function check_client_name($param)
	{
        $CI =& get_instance();
        $CI->load->model('client_model', 'the_model');
        
		$query = $CI->the_model->info(array('name' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_company_name'))
{
    function check_company_name($param)
	{
        $CI =& get_instance();
        $CI->load->model('company_model', 'the_model');
        
		$query = $CI->the_model->info(array('name' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_media_album_name'))
{
    function check_media_album_name($param)
	{
        $CI =& get_instance();
        $CI->load->model('media_album_model', 'the_model');
        
		$query = $CI->the_model->info(array('name' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_media_album_slug'))
{
    function check_media_album_slug($param)
	{
        $CI =& get_instance();
        $CI->load->model('media_album_model', 'the_model');
        
		$query = $CI->the_model->info(array('slug' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_preferences_name'))
{
    function check_preferences_name($param)
	{
        $CI =& get_instance();
        $CI->load->model('preferences_model', 'the_model');
        
		$query = $CI->the_model->info(array('name' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_preferences_slug'))
{
    function check_preferences_slug($param)
	{
        $CI =& get_instance();
        $CI->load->model('preferences_model', 'the_model');
        
		$query = $CI->the_model->info(array('slug' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_program_name'))
{
    function check_program_name($param)
	{
        $CI =& get_instance();
        $CI->load->model('program_model', 'the_model');
        
		$query = $CI->the_model->info(array('name' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_program_slug'))
{
    function check_program_slug($param)
	{
        $CI =& get_instance();
        $CI->load->model('program_model', 'the_model');
        
		$query = $CI->the_model->info(array('slug' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_program_sub_slug'))
{
    function check_program_sub_slug($param)
	{
        $CI =& get_instance();
        $CI->load->model('program_sub_model', 'the_model');
        
		$query = $CI->the_model->info(array('slug' => $param));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('filter'))
{
    function filter($param)
    {
        $CI =& get_instance();

        $result = $CI->db->escape_str($param);
        return $result;
    }
}

if ( ! function_exists('valid_email'))
{
	function valid_email($email)
	{
		if ( !preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i", $email) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}
