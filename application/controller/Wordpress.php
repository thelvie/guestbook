<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wordpress extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('m_user');
    $this->load->model('m_event');
    cekColor();
  }

  public function index()
  {
    sedangLogout();
    sedangLOckAccess();


    if (!$this->session->userdata('sessionEventCek')) {
      $this->session->set_flashdata('error', 'Select Event Active.!!');
      redirect('home');
    }

    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('emailGuestBook')])->row_array();

    $data['event'] = $this->db->get('event')->result_array();
    $data['eventActive'] = $this->m_event->byId($this->session->userdata('sessionEventCek'));

    $data['judul'] = 'Settings Wordpress';
    $this->load->view('temp/header', $data);
    $this->load->view('setting/wp', $data);
  }



  public function editData()
  {
    $id = $this->input->post('id', true);
    $template = trim(htmlspecialchars($this->input->post('template')));
    $url = trim($this->input->post('url', true));

    $this->db->set(['template' => $template, 'urll' => $url])->where('id', $id)->update('event');
    $this->session->set_flashdata('success', 'Data UPDATED');
    redirect('wordpress');
  }
}