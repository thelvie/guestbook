<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcomee extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('m_user');
    $this->load->model('m_event');
    $this->load->model('m_table');
  }

  public function index()
  {
    sedangLogout();


    if (!$this->session->userdata('sessionEventCek')) {
      $this->session->set_flashdata('error', 'Select Event Active.!!');
      redirect('home');
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

    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('emailGuestBook')])->row_array();

    $data['event'] = $this->m_event->byId($this->session->userdata('sessionEventCek'));
    $data['seting'] = $this->db->get('seting')->row_array();
    $data['judul'] = 'Welcome Screen';
    $this->load->view('temp/header', $data);
    $this->load->view('home/setwelcome', $data);
  }


  public function setingWel()
  {
    $val = trim($this->input->post('isi', true));
    $id = $this->input->post('id');
    $name = $this->input->post('name');

    $this->db->set($name, $val)->where('id', $id)->update('event');
  }

  public function bgImage()
  {
    $poto = $_FILES['poto']['name'];

    $event = $this->m_event->byId($this->session->userdata('sessionEventCek'));
    $id = $event['id'];

    if ($poto) {
      $config['allowed_types'] = 'jpg|png|jpeg';
      $config['max_size']     = '10000';
      $config['upload_path'] = './guestbook/assets/images/auth/';
      $config['encrypt_name'] = true;
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('poto')) {

        if (file_exists('guestbook/assets/images/auth/' . $event['warna_bg'])) {
          unlink(FCPATH . 'guestbook/assets/images/auth/' . $event['warna_bg']);
        }
        $new_image = $this->upload->data('file_name');

        $this->db->set('warna_bg', $new_image)->where('id', $id)->update('event');
        $this->session->set_flashdata('success', 'Data UPDATED.!');
        redirect('welcomee');
      } else {
        $this->session->set_flashdata('error', 'Harus format gambar, dan ukuran max. 2 MB.!');
        redirect('welcomee');
      }
    }
  }



  public function live()
  {
    sedangLogout();


    if (!$this->session->userdata('sessionEventCek')) {
      $this->session->set_flashdata('error', 'Select Event Active.!!');
      redirect('home');
    }

    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('emailGuestBook')])->row_array();

    $data['event'] = $this->m_event->byId($this->session->userdata('sessionEventCek'));
    $data['judul'] = 'Welcome Screen';
    $this->load->view('home/live', $data);
  }


  public function loginLive()
  {
    redirect('auth/logout');
  }

  public function video()
  {
    $seting = $this->db->get('seting')->row_array();
    $file = $_FILES['url']['name'];
    if ($file) {
      $config['allowed_types'] = 'mp4';
      $config['max_size']     = '150000';
      $config['upload_path'] = './guestbook/assets/images/auth/';
      $config['encrypt_name'] = true;
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('url')) {
        if (file_exists('guestbook/assets/images/auth/' . $seting['url'])) {
          if ($seting['url'] !== 'video.mp4') {
            # code...
            unlink(FCPATH . 'guestbook/assets/images/auth/' . $seting['url']);
          }
        }
        $new = $this->upload->data('file_name');
        $this->db->set('url', $new);
        $this->db->update('seting');
        redirect('welcomee');
      } else {
        $this->session->set_flashdata('gagal', 'Harus format video, dan ukuran max. 50 MB.!');
        redirect('welcomee');
      }
    } else {
      redirect('welcomee');
    }
  }



  public function bgLogin()
  {
    $bg = $_FILES['bg']['name'];
    $icon = $_FILES['icon']['name'];
    $seting = $this->db->get('seting')->row_array();
    if ($bg) {
      $config['allowed_types'] = 'jpg|png|jpeg';
      $config['max_size']     = '10000';
      $config['upload_path'] = './guestbook/assets/images/auth/';
      $config['encrypt_name'] = true;
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('bg')) {
        if (file_exists('guestbook/assets/images/auth/' . $seting['bg_login'])) {
          if ($seting['bg_login'] !== 'batik.png') {
            # code...
            unlink(FCPATH . 'guestbook/assets/images/auth/' . $seting['bg_login']);
          }
        }
        $new = $this->upload->data('file_name');
        $this->db->set('bg_login', $new);
        $this->db->update('seting');
      } else {
        $this->session->set_flashdata('gagal', 'Background Harus format gambar, dan ukuran max. 10 MB.!');
        redirect('welcomee');
      }
    }
    if ($icon) {
      $config['allowed_types'] = 'jpg|png|jpeg';
      $config['max_size']     = '5000';
      $config['upload_path'] = './guestbook/assets/images/auth/';
      $config['encrypt_name'] = true;
      $this->load->library('upload', $config);

      if ($this->upload->do_upload('icon')) {
        if (file_exists('guestbook/assets/images/auth/' . $seting['logo_login'])) {
          if ($seting['logo_login'] !== 'icon.png') {
            # code...
            unlink(FCPATH . 'guestbook/assets/images/auth/' . $seting['logo_login']);
          }
        }
        $new = $this->upload->data('file_name');
        $this->db->set('logo_login', $new);
        $this->db->update('seting');
      } else {
        $this->session->set_flashdata('gagal', 'LOGO Harus format gambar, dan ukuran max. 5 MB.!');
        redirect('welcomee');
      }
    }

    redirect('welcomee');
  }






  public function autoLoadPage()
  {
    $event = $this->m_event->byId($this->session->userdata('sessionEventCek'));

    if (file_exists('guestbook/assets/images/auth/' . $event['warna_bg'])) {
      $data = '<div style="width: 100vw;height: 100vh; background: url(' . base_url('guestbook/assets/images/auth/' . $event['warna_bg']) . '); background-size: cover;background-position: center;text-align: center;padding-top: 25vh;color:' . $event['warna'] . ';" id="bgImg">';
    } else {
      $data = '<div style="width: 100vw;height: 100vh; background: ' . $event['warna_bg'] . ';text-align: center;padding-top: 25vh;color:' . $event['warna'] . ';" id="bgColor">';
    }

    $data .= '<div class="row">';
    $data .= '<div class="col-sm-12"><h5 style="font-size: 5vw;">' . $event['welcome'] . '</h5></div>';
    $data .= '<div class="col-sm-12 mt-4"><h5 style="font-size: 6vw;">Nama Tamu Undangan</h5></div>';
    $data .= '<div class="col-sm-12 mt-4"><h6 style="font-size: 3vw;">Alamat</h6>';
    $data .= '<h6 style="font-size: 2vw;">' . date('d/m/Y H:i:s') . '</h6>';
    $data .= '</div></div>';
    echo $data;
  }
}