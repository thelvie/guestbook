<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class M_user extends CI_Model
{

  public function byUser($username)
  {
    $this->db->where('username', $username);
    $query = $this->db->get('user')->row_array();
    return $query;
  }

  public function byId($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('user')->row_array();
    return $query;
  }


  public function byEmail($email)
  {
    $this->db->where('email', $email);
    $query = $this->db->get('user')->row_array();
    return $query;
  }

  public function getSerial()
  {
    $query = $this->db->get('serial')->row_array();
    return $query;
  }
}