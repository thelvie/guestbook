<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Resetakun extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
		$this->load->model('m_apinya');
	}

	public function index()
	{
		sedangLogin();

		$seri = $this->m_user->getSerial();
		$data['judul'] = 'Reset Akun';
		$this->load->view('temp/reset', $data);
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





	public function reset()
	{
		$email = trim($this->input->post('email'));
		$serial = trim($this->input->post('serial'));

		$cekKode = $this->db->get_where('serial', ['email' => $email])->row_array();
		if ($cekKode) {
			if ($serial == $cekKode['serial']) {
				$pass = passHash('12345678');
				$this->db->set(['username' => 'admin', 'password' => $pass, 'email' => 'admin@gmail.com'])->where('role', '1');
				$this->db->update('user');
				$this->session->unset_userdata('usernameGuestBook');
				$this->session->unset_userdata('emailGuestBook');
				$this->session->unset_userdata('idUserGuestBook');
				$this->session->unset_userdata('sessionEventCek');
				$this->session->unset_userdata('filterHadirTamu');
				$this->session->unset_userdata('sesionSettingUndangan');
				$this->session->set_flashdata('berhasil', 'Password dan username kembali ke default');
				redirect('auth');
			} else {
				$this->session->set_flashdata('gagal', 'Kombinasi kode dan email Tidak dikenal');
				redirect('resetakun');
			}
		} else {
			$this->session->set_flashdata('gagal', 'Kombinasi kode dan email Tidak dikenal');
			redirect('resetakun');
		}
	}
}