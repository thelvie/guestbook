<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
		$this->load->model('m_apinya');
		$this->load->model('m_table');
	}

	public function index()
	{
		$this->load->dbforge();
		$this->load->dbutil();
		$this->config->load('hook');
		$this->config->load('basenya');
		$dbHost = $this->config->item('database_host');
		$dbUser = $this->config->item('database_user');
		$dbPass = $this->config->item('database_pass');
		$dbName = $this->config->item('database_name');
		$db = thisconect($dbHost, $dbUser, $dbPass, $dbName);
		if (!$db) {
			redirect('loadd');
		}
		$cekSerial = $this->m_user->getSerial();
		noEmail($cekSerial['email']);
		cekdimension();
		sedangLogin();
		$cekSeri = $this->m_apinya->getByKode($cekSerial['serial'], $cekSerial['email']);
		$this->m_table->modifyTabEvent();
		$this->db->set('link', base_url())->where('status', 2)->update('undangan');

		$allUser = $this->db->get_where('user', ['active' => 1])->result_array();
		foreach ($allUser as $key) {
			if ($key['expired'] < date('Y-m-d')) {
				$this->db->set('active', 0)->where('id', $key['id'])->update('user');
			}
		}

		$cekTbl = $this->db->table_exists('seting');
		if (!$cekTbl) {
			$this->m_table->creatTbSeting();
		}

		$cekLink = $this->db->get('seting')->num_rows();
		if ($cekLink <= 0) {
			$isi = [
				'bg_login' => 'batik.png',
				'logo_login' => 'logo.png',
				'url' => 'video.mp4'
			];
			$this->db->insert('seting', $isi);
		}

		$data['seting'] = $this->db->get('seting')->row_array();

		$data['judul'] = base_url();
		$this->load->view('auth/login', $data);
	}

	public function login()
	{
		$username = trim($this->input->post('username'));
		$password = trim($this->input->post('password'));

		$cekuser = $this->m_user->byUser($username);
		$cekemail = $this->m_user->byEmail($username);



		if ($cekuser) {
			if (passVerf($password, $cekuser['password'])) {
				if ($cekuser['active'] == '1') {

					$session = [
						'usernameGuestBook' => $cekuser['username'],

						'emailGuestBook' => $cekuser['email'],

						'idUserGuestBook' => $cekuser['id'],

					];

					$this->session->set_userdata($session);


					$json['kode'] = 1;
					$json['text'] = 'Success Login';
					$json['url_home'] = base_url('home');
				} else {
					$json['kode'] = 3;
					$json['text'] = 'Akun anda Dalam Status NON ACTIVE.!';
				}
			} else {
				$json['kode'] = 2;
				$json['text'] = 'Cek Kombinasi Username dan Password..!';
			}
		} elseif ($cekemail) {

			if (passVerf($password, $cekemail['password'])) {
				if ($cekemail['active'] == '1') {

					$session = [
						'usernameGuestBook' => $cekemail['username'],

						'emailGuestBook' => $cekemail['email'],

						'idUserGuestBook' => $cekemail['id'],

					];

					$this->session->set_userdata($session);



					$json['kode'] = 1;
					$json['text'] = 'Success Login';
					$json['url_home'] = base_url('home');
				} else {
					$json['kode'] = 3;
					$json['text'] = 'Akun anda Dalam Status NON ACTIVE.!';
				}
			} else {
				$json['kode'] = 2;
				$json['text'] = 'Cek Kombinasi Username dan Password..!';
			}
		} else {
			$json['kode'] = 2;
			$json['text'] = 'Cek Kombinasi Username dan Password..!';
		}

		echo json_encode($json);
	}


	public function logout()
	{
		$this->session->unset_userdata('usernameGuestBook');
		$this->session->unset_userdata('emailGuestBook');
		$this->session->unset_userdata('idUserGuestBook');
		$this->session->unset_userdata('sessionEventCek');
		$this->session->unset_userdata('filterHadirTamu');
		$this->session->unset_userdata('sesionSettingUndangan');
		redirect();
	}





	public function tutor()
	{
		sedangLogout();


		if (!$this->session->userdata('sessionEventCek')) {
			$this->session->set_flashdata('error', 'Select Event Active.!!');
			redirect('home');
		}


		$data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('emailGuestBook')])->row_array();

		$data['event'] = $this->m_event->byId($this->session->userdata('sessionEventCek'));
		$data['seting'] = $this->db->get('seting')->row_array();
		$data['judul'] = 'UserGuide';
		$this->load->view('temp/header', $data);
		$this->load->view('home/userguide', $data);
	}
}