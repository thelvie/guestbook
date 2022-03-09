<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends CI_Controller
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

    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('emailGuestBook')])->row_array();

    if ($data['user']['role'] == "3") {
      redirect('home');
    }

    $data['event'] = $this->db->get('event')->result_array();
    $data['eventActive'] = $this->m_event->byId($this->session->userdata('sessionEventCek'));

    $data['lokasiApk'] = $this->db->get_where('undangan', ['status' => 2])->row_array();

    $data['judul'] = 'Settings';
    $this->load->view('temp/header', $data);
    $this->load->view('setting/index', $data);
  }



  public function addData()
  {
    $wa = trim($this->input->post('wa', true));
    $nama = htmlspecialchars(trim($this->input->post('event', true)));
    $tgl = $this->input->post('tgl');
    $poto = $_FILES['img']['name'];

    $nUser = htmlspecialchars(trim($this->input->post('nama', true)));
    $username = htmlspecialchars(trim($this->input->post('username', true)));
    $email = trim($this->input->post('email', true));
    $pass = trim($this->input->post('pass', true));
    $passs = passHash($pass);

    $cekEmail = $this->m_user->byEmail($email);
    $cekUsername = $this->m_user->byUser($username);

    if ($cekEmail) {
      if ($cekEmail['role'] === '3') {
        $this->session->set_flashdata('error', 'Gunakan EMAIL Lain');
        redirect('setting');
      }
      if ($cekEmail['role'] === '1' || $cekEmail['role'] === '2') {
        $idUser = $cekEmail['id'];
      } else {
        $idUser = 0;
      }
    } else {
      $idUser = 0;
    }

    if ($cekUsername) {
      if ($cekUsername['role'] === '3') {
        $this->session->set_flashdata('error', 'Gunakan USERNAME Lain');
        redirect('setting');
      }
      if ($cekUsername['role'] === '1' || $cekUsername['role'] === '2') {
        $idUser = $cekUsername['id'];
      } else {
        $idUser = 0;
      }
    } else {
      $idUser = 0;
    }






    if ($poto) {
      $config['allowed_types'] = 'jpg|png|jpeg';
      $config['max_size']     = '9000';
      $config['upload_path'] = './guestbook/assets/images/event/';
      $config['encrypt_name'] = true;
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('img')) {

        $new_image = $this->upload->data('file_name');
        $data = [
          'nama' => $nama,
          'tgl' => $tgl,
          'poto' => $new_image,
          'admin_id' => $idUser,
          'wa' => $wa,
        ];

        $this->db->insert('event', $data);

        if ($cekEmail['role'] !== '1') {
          $data = [
            'nama' => $nUser,
            'username' => $username,
            'email' => $email,
            'password' => $passs,
            'poto' => 'user.jpg',
            'active' => 1,
            'role' => 3,
            'member' => 'Gold',
            'register' => date('Y-m-d'),
            'expired' => '2100-12-30',
            'kunci' => 0,
            'event_id' => 0,
          ];

          $this->db->insert('user', $data);
        }




        $cekUser = $this->m_user->byEmail($email);
        if ($cekUser['role'] === '3') {
          $this->db->set('admin_id', $cekUser['id'])->where('admin_id', 0)->update('event');
        }

        $cekEvent = $this->db->get_where('event', ['admin_id' => $cekUser['id']])->row_array();
        if ($cekUser['role'] === '3') {
          $this->db->set('event_id', $cekEvent['id'])->where('id', $cekUser['id'])->update('user');
        }

        $this->session->set_flashdata('success', 'Data berhasil ditambahkan.!');
        redirect('setting');
      } else {
        $this->session->set_flashdata('error', 'Harus format gambar, dan ukuran max. 8 MB.!');
        redirect('setting');
      }
    } else {
      $this->session->set_flashdata('error', 'Harus disertakan Gambar.!');
      redirect('setting');
    }
  }


  public function setActive($id)
  {
    $user = $this->db->get_where('user', ['email' => $this->session->userdata('emailGuestBook')])->row_array();
    $event = $this->m_event->byId($id);
    if ($user['id'] == $event['admin_id']) {
      # code...
      $this->session->unset_userdata('sessionEventCek');
      $this->session->set_userdata('sessionEventCek', $id);
      echo 1;
    } else {
      echo 3;
    }
  }




  public function delData($id)
  {
    $event = $this->m_event->byId($id);
    $poto = $event['poto'];
    $tamu = $this->m_event->getAllTamuByEvent($id);
    $jmlTamu = $this->m_event->jmlTamuByEvent($id);
    if ($jmlTamu > 0) {
      foreach ($tamu as $key) {
        $potoTamu = $key['poto'];
        if ($potoTamu !== 'tamu.jpg') {
          unlink(FCPATH . 'guestbook/assets/images/guest/' . $potoTamu);
        }

        $kode = $key['id'] . '_' . $key['nama'];
        if (file_exists('guestbook/assets/images/qr/' . $kode . '.png')) {
          unlink(FCPATH . 'guestbook/assets/images/qr/' . $kode . '.png');
        }
        $this->db->where('id', $key['id'])->delete('tamu');
      }
    }

    if ($poto !== 'wedding.jpg') {
      unlink(FCPATH . 'guestbook/assets/images/event/' . $poto);
    }
    if (file_exists('guestbook/assets/images/auth/' . $event['warna_bg'])) {
      unlink(FCPATH . 'guestbook/assets/images/auth/' . $event['warna_bg']);
    }


    $koneksi = $this->db->get('konek', ['event_id' => $event['id']])->result_array();
    $member = $this->m_user->byId($event['admin_id']);



    if ($koneksi) {
      foreach ($koneksi as $kkon) {
        unlink(FCPATH . 'guestbook/assets/images/qr/' . $kkon['kode'] . '.png');
      }
      $this->db->where('event_id', $event['id'])->delete('konek');
    }

    if ($member['role'] == "3") {
      if ($member['poto'] !== 'user.jpg') {
        unlink(FCPATH . 'guestbook/assets/images/faces/' . $member['poto']);
      }
      $this->db->where('id', $member['id'])->delete('user');
    }
    $this->db->where('id', $id)->delete('event');
  }


  public function editData()
  {
    $id = $this->input->post('id');
    $nama = htmlspecialchars(trim($this->input->post('event', true)));
    $tgl = htmlspecialchars(trim($this->input->post('tgl', true)));

    $this->db->set([
      'nama' => $nama,
      'tgl' => $tgl
    ]);
    $this->db->where('id', $id)->update('event');
    $this->session->set_flashdata('success', 'Data berhasil di update.!');
    redirect('setting');
  }


  public function editDataWa()
  {
    $id = $this->input->post('id');
    $wa = trim($this->input->post('wa', true));

    $this->db->set([
      'wa' => $wa
    ]);
    $this->db->where('id', $id)->update('event');
    $this->session->set_flashdata('success', 'Data berhasil di update.!');
    redirect('setting');
  }


  public function updatePoto()
  {
    $id = $this->input->post('id');
    $poto = $_FILES['poto']['name'];

    $event = $this->m_event->byId($id);

    if ($poto) {
      $config['allowed_types'] = 'jpg|png|jpeg';
      $config['max_size']     = '9000';
      $config['upload_path'] = './guestbook/assets/images/event/';
      $config['encrypt_name'] = true;
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('poto')) {

        if ($event['poto'] !== 'wedding.jpg') {
          unlink(FCPATH . 'guestbook/assets/images/event/' . $event['poto']);
        }
        $new_image = $this->upload->data('file_name');

        $this->db->set('poto', $new_image)->where('id', $id)->update('event');
        $this->session->set_flashdata('success', 'Data UPDATED.!');
        redirect('setting');
      } else {
        $this->session->set_flashdata('error', 'Harus format gambar, dan ukuran max. 8 MB.!');
        redirect('setting');
      }
    }
  }

  public function editBgWelcome()
  {
    $warna = $this->input->post('warna');
    $wel = $this->input->post('wel');
    $id = $this->input->post('id');
    $poto = $_FILES['poto']['name'];

    $event = $this->m_event->byId($id);

    if ($poto) {
      $config['allowed_types'] = 'jpg|png|jpeg';
      $config['max_size']     = '9000';
      $config['upload_path'] = './guestbook/assets/images/auth/';
      $config['encrypt_name'] = true;
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('poto')) {

        if (file_exists('guestbook/assets/images/auth/' . $event['warna_bg'])) {
          unlink(FCPATH . 'guestbook/assets/images/auth/' . $event['warna_bg']);
        }

        $new_image = $this->upload->data('file_name');

        $this->db->set('warna_bg', $new_image);
      } else {
        $this->session->set_flashdata('error', 'Harus format gambar, dan ukuran max. 8 MB.!');
        redirect('setting');
      }
    }
    $this->db->set(['warna' => $warna, 'welcome' => $wel]);
    $this->db->where('id', $id)->update('event');
    $this->session->set_flashdata('success', 'Data UPDATED.!');
    redirect('setting');
  }
}