<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Loadd extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
		$this->load->model('m_table');
		$this->load->model('m_apinya');
	}

	public function index()
	{
		$this->load->dbforge();
		$this->load->dbutil();
		$this->config->load('basenya');
		$this->config->load('basenya');
		$dbHost = $this->config->item('database_host');
		$dbUser = $this->config->item('database_user');
		$dbPass = $this->config->item('database_pass');
		$dbName = $this->config->item('database_name');
		$db = thisconect($dbHost, $dbUser, $dbPass, $dbName);
		if ($db) {
			redirect('loadd/register');
		}

		sedangLogin();

		$data['judul'] = 'Register';
		$this->load->view('auth/instal', $data);
	}

	public function register()
	{
		$this->load->dbforge();
		$this->load->dbutil();
		$this->config->load('basenya');
		$dbHost = $this->config->item('database_host');
		$dbUser = $this->config->item('database_user');
		$dbPass = $this->config->item('database_pass');
		$dbName = $this->config->item('database_name');
		$db = thisconect($dbHost, $dbUser, $dbPass, $dbName);
		if (!$db) {
			$this->session->set_flashdata('gagal', 'Pastikan memasukan input database dengan benar');
			redirect('loadd');
		}

		sedangLogin();

		$data['judul'] = 'Register';
		$this->load->view('auth/register', $data);
	}


	public function creatDb()
	{
		$host = trim($this->input->post('host'));
		$user = trim($this->input->post('user'));
		$pass = trim($this->input->post('pass'));
		$base = trim($this->input->post('base'));

		$isi = '<?php defined(\'BASEPATH\') or exit(\'No direct script access allowed\');';
		$isi .= '$config[\'database_host\']=' . '\'' . $host . '\'' . ';';
		$isi .= '$config[\'database_user\']=' . '\'' . $user . '\'' . ';';
		$isi .= '$config[\'database_pass\']=' . '\'' . $pass . '\'' . ';';
		$isi .= '$config[\'database_name\']=' . '\'' . $base . '\'' . ';';
		$filename = "application/config/basenya.php";

		$file = fopen($filename, 'w+');
		fwrite($file, $isi);
		fclose($file);

		redirect('loadd/creatTbl');
	}

	public function creatDb2()
	{
		$this->config->load('basenya');
		$dbName = $this->config->item('database_name');
		$name = $dbName;
		$this->load->dbforge();
		// $this->load->dbutil();
		// $cekDb =	$this->dbutil->database_exists($name);
		$ql =	$this->dbforge->create_database($name, true);
		if ($ql === true) {
			redirect('loadd/creatTbl');
		} else {
			redirect('loadd');
		}
	}



	public function creatTbl()
	{
		$this->load->dbforge();
		$this->load->dbutil();
		$this->config->load('basenya');
		$dbHost = $this->config->item('database_host');
		$dbUser = $this->config->item('database_user');
		$dbPass = $this->config->item('database_pass');
		$dbName = $this->config->item('database_name');
		$db = thisconect($dbHost, $dbUser, $dbPass, $dbName);
		if (!$db) {
			$this->session->set_flashdata('gagal', 'Pastikan memasukan input database dengan benar');
			redirect('loadd');
		} else {
			$this->m_table->creatTbEvent();
			$this->m_table->creatTbKonek();
			$this->m_table->creatTbSerial();
			$this->m_table->creatTbTamu();
			$this->m_table->creatTbUndangan();
			$this->m_table->creatTbUser();
			$this->session->set_flashdata('berhasil', 'Database Created');
			redirect('loadd/register');
		}
	}


	public function dropTable()
	{
		$drop = $this->m_table->drop();
		if ($drop) {
			redirect('loadd');
		}
	}




	public function registered()
	{
		$this->db->delete('serial');
		$this->config->load('hook');
		$email = trim($this->input->post('email'));
		$serial = trim($this->input->post('serial'));
		$this->db->empty_table('serial');
		$cekSeri = $this->m_apinya->getByKode($serial, $email);
		$hg = $email;
		$wd = $serial;
		$df = $this->config->item('def_dftd');
		$gr = $this->config->item('def_grtd');
		$cek = lostdim($wd, $hg, $df, $gr);
		$lost = json_decode($cek, true);
		if ($lost['status'] === false) {
			$this->session->set_flashdata('gagal', 'Kombinasi Email dan Kode Tidak dikenal');
			redirect('loadd/register');
		} else {
			if ($lost['lg'] === null || $lost['lg'] === "") {
				$putdim = putdim($wd, $hg, dataLengt(), $df, $gr);
				$putdim = json_decode($putdim, true);
				$data = ['serial' => $serial, 'email' => $email, 'active' => 1, 'status' => 1];
				$this->db->insert('serial', $data);
				creatdimensi($hg, $wd, dataLengt());
				$cekTblEvent = $this->db->get('event')->num_rows();
				$cekTblundangan = $this->db->get('undangan')->num_rows();
				$cekTbluser = $this->db->get('user')->num_rows();
				if ($cekTblEvent <= 0) {
					$this->m_table->isiTblEvent();
				}
				if ($cekTblundangan <= 0) {
					$this->m_table->isiTblUndangan();
				}
				if ($cekTbluser <= 0) {
					$this->m_table->isiTblUser($email);
				}
				$this->session->set_flashdata('berhasil', 'Success Registrasi');
				redirect('auth');
			}
			if ($lost['lg'] !== dataLengt()) {
				$this->session->set_flashdata('gagal', 'Akun Base belum terdaftar');
				redirect('loadd/register');
			}
			if ($lost['lg'] === dataLengt()) {
				creatdimensi($hg, $wd, dataLengt());
				$cekTblEvent = $this->db->get('event')->num_rows();
				$cekTblundangan = $this->db->get('undangan')->num_rows();
				$cekTbluser = $this->db->get('user')->num_rows();
				if ($cekTblEvent <= 0) {
					$this->m_table->isiTblEvent();
				}
				if ($cekTblundangan <= 0) {
					$this->m_table->isiTblUndangan();
				}
				if ($cekTbluser <= 0) {
					$this->m_table->isiTblUser($email);
				}
				$data = ['serial' => $serial, 'email' => $email, 'active' => 1, 'status' => 1];
				$this->db->insert('serial', $data);
				$this->session->set_flashdata('berhasil', 'Success Registrasi');
				redirect('auth');
			}
		}
	}
}