<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mysecurity
{
	protected $CI;
	protected $encryption_key;

	private $secret;

	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('encryption');

		$this->secret = $this->CI->encryption;
	}

	public function set_key($key)
	{
		$this->encryption_key = $key;
	}

	public function encrypt_url($data, $key = '')
	{
		$key = !empty($key) ? $key : $this->encryption_key;

		$this->secret->initialize(array('key' => $key));
		return str_replace(array('+', '/', '='), array('-', '_', '~'), $this->secret->encrypt($data));
	}

	public function decrypt_url($data, $key = '')
	{
		$key = !empty($key) ? $key : $this->encryption_key;

		$this->secret->initialize(array('key' => $key));
		return $this->secret->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $data));
	}
}
