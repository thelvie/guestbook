<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('m_user');
    $this->load->model('m_event');
  }

  public function index()
  {
    sedangLogout();
    sedangLOckAccess();

    $allUser = $this->db->get_where('user', ['active' => 1])->result_array();
    foreach ($allUser as $key) {
      if ($key['expired'] < date('Y-m-d')) {
        $this->db->set('active', 0)->where('id', $key['id'])->update('user');
      }
    }
    cekColor();
    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('emailGuestBook')])->row_array();

    $data['allUser'] = $this->db->get('user')->result_array();

    $data['lokasiApk'] = $this->db->get_where('undangan', ['status' => 2])->row_array();

    $data['judul'] = 'My Profile';
    $this->load->view('temp/header', $data);
    $this->load->view('profile/index', $data);
  }



  public function update()
  {
    $nama = trim($this->input->post('nama', true));
    $username = trim($this->input->post('username', true));
    $email = trim($this->input->post('email', true));
    $id = trim($this->input->post('id', true));

    $cekUser = $this->m_user->byId($id);
    $cekEmail = $this->m_user->byEmail($email);
    $cekUsername = $this->m_user->byUser($username);

    if ($cekEmail) {
      if ($cekUser) {
        $this->db->set('email', $email);
      } else {
        $this->session->set_flashdata('error', 'Gunakan email Lain');
        redirect('profile');
      }
    } else {
      $this->db->set('email', $email);
    }

    if ($cekUsername) {
      if ($cekUser) {
        $this->db->set('username', $username);
      } else {
        $this->session->set_flashdata('error', 'Gunakan USERNAME Lain');
        redirect('profile');
      }
    } else {
      $this->db->set('username', $username);
    }

    $this->db->set('nama', $nama);
    $this->db->where('id', $id)->update('user');

    $session = [
      'usernameGuestBook' => $username,

      'emailGuestBook' => $email,

      'idUserGuestBook' => $id,

    ];

    $this->session->set_userdata($session);

    $this->session->set_flashdata('success', 'Data UPDATED');
    redirect('profile');
  }


  public function updatePoto()
  {
    $id = trim($this->input->post('id', true));
    $poto = $_FILES['poto']['name'];

    $user = $this->m_user->byId($id);

    if ($poto) {
      $config['allowed_types'] = 'jpg|png|jpeg';
      $config['max_size']     = '3000';
      $config['upload_path'] = './guestbook/assets/images/faces/';
      $config['encrypt_name'] = true;
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('poto')) {

        if ($user['poto'] !== 'user.jpg') {
          unlink(FCPATH . 'guestbook/assets/images/faces/' . $user['poto']);
        }
        $new_image = $this->upload->data('file_name');

        $this->db->set('poto', $new_image)->where('id', $id)->update('user');
        $this->session->set_flashdata('success', 'Data UPDATED.!');
        redirect('profile');
      } else {
        $this->session->set_flashdata('error', 'Harus format gambar, dan ukuran max. 2 MB.!');
        redirect('profile');
      }
    }
  }




  public function password()
  {
    sedangLogout();
    sedangLOckAccess();

    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('emailGuestBook')])->row_array();

    $data['judul'] = 'Change Password';
    $this->load->view('temp/header', $data);
    $this->load->view('profile/password', $data);
  }



  public function updatePass()
  {
    $pass = trim($this->input->post('pass'));
    $pass1 = trim($this->input->post('pass1'));
    $pass2 = trim($this->input->post('pass2'));
    $id = trim($this->input->post('id'));

    $jmlKrk = strlen($pass1);

    $user = $this->m_user->byId($id);

    if (passVerf($pass, $user['password'])) {
      if ($jmlKrk >= 8) {
        if ($pass1 === $pass2) {
          $password = passHash($pass1);

          $this->db->set('password', $password)->where('id', $id)->update('user');

          $this->session->unset_userdata('usernameGuestBook');
          $this->session->unset_userdata('emailGuestBook');
          $this->session->unset_userdata('idUserGuestBook');
          $this->session->unset_userdata('sessionEventCek');
          $this->session->unset_userdata('filterHadirTamu');

          $this->session->set_flashdata('success', 'Data UPDATED');
          redirect('auth');
        } else {
          $this->session->set_flashdata('error', 'Konfirmasi Password Tidak sama.!');
          redirect('profile/password');
        }
      } else {
        $this->session->set_flashdata('error', 'Password Baru Minimal 8 Karakter');
        redirect('profile/password');
      }
    } else {
      $this->session->set_flashdata('error', 'Password TIDAK DIKENAL..');
      redirect('profile/password');
    }
  }




  // ANDROID
  public function uploadApk()
  {
    $id = $this->input->post('id');
    $apk = $_FILES['apk']['name'];

    $lokasi = $this->db->get_where('undangan', ['id' => $id])->row_array();

    if ($apk) {
      $config['allowed_types'] = 'rar|zip';
      $config['upload_path'] = './guestbook/assets/apk/';
      $config['encrypt_name'] = true;
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('apk')) {
        unlink(FCPATH . 'guestbook/assets/apk/' . $lokasi['apk']);
        $new_image = $this->upload->data('file_name');

        $this->db->set('apk', $new_image)->where('id', $id)->update('undangan');
        $this->session->set_flashdata('success', 'Data UPLOAD.!');
        redirect('setting');
      } else {
        $this->session->set_flashdata('error', 'Harus format ZIP/RAR');
        redirect('setting');
      }
    }
  }







  // MEMEBER
  public function member()
  {
    $nama = trim($this->input->post('nama'));
    $username = trim($this->input->post('username'));
    $email = trim($this->input->post('email'));
    $memb = trim($this->input->post('memb'));
    $pass = trim($this->input->post('pass'));
    $passs = passHash($pass);

    if ($memb == "gold") {
      # code...
      $time = strtotime('+12 month');
    } elseif ($memb == "silver") {
      # code...
      $time = strtotime('+6 month');
    } else {
      # code...
      $time = strtotime('+3 day');
    }

    $expired = date('Y-m-d', $time);


    $cekEmail = $this->m_user->byEmail($email);
    $cekUsername = $this->m_user->byUser($username);

    if ($cekEmail) {
      $this->session->set_flashdata('error', 'Gunakan EMAIL Lain');
      redirect('profile');
    }

    if ($cekUsername) {
      $this->session->set_flashdata('error', 'Gunakan USERNAME Lain');
      redirect('profile');
    }

    $data = [
      'nama' => $nama,
      'username' => $username,
      'email' => $email,
      'password' => $passs,
      'poto' => 'user.jpg',
      'active' => 1,
      'role' => 2,
      'member' => $memb,
      'register' => date('Y-m-d'),
      'expired' => $expired,
      'kunci' => 0
    ];

    $this->db->insert('user', $data);
    $this->session->set_flashdata('success', 'Added DATA');
    redirect('profile');
  }


  public function activasiMember($id)
  {
    $akun = $this->m_user->byId($id);
    if ($akun['active'] == "1") {
      $this->db->set('active', 0)->where('id', $id)->update('user');
    } else {
      $this->db->set('active', 1)->where('id', $id)->update('user');
    }
    redirect('profile');
  }

  public function delMember($id)
  {
    $akun = $this->m_user->byId($id);
    $jmlEvent = $this->db->get_where('event', ['admin_id' => $akun['id']])->num_rows();

    if ($jmlEvent >= 1) {
      $this->session->set_flashdata('error', 'Silahkan DELETE Dulu Event MEMBER');
      redirect('profile');
    }

    $this->db->where('id', $id)->delete('user');

    redirect('profile');
  }

  public function resetPass($id)
  {
    $akun = $this->m_user->byId($id);
    $pass = passHash($akun['username']);

    $this->db->set('password', $pass);

    $this->db->where('id', $id)->update('user');
    $this->session->set_flashdata('success', 'RESET PASSWORD Ok');
    redirect('profile');
  }

  public function memberUpgrade()
  {
    $id = trim($this->input->post('id'));
    $memb = trim($this->input->post('memb'));
    $expired = $this->input->post('expired');

    $this->db->set([
      'active' => 1,
      'member' => $memb,
      'expired' => $expired,
    ]);

    $this->db->where('id', $id)->update('user');
    $this->session->set_flashdata('success', 'Update DATA');
    redirect('profile');
  }
}