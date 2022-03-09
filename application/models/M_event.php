<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class M_event extends CI_Model
{

  public function allByAdminId($id)
  {
    $this->db->where('admin_id', $id);
    $query = $this->db->get('event')->result_array();
    return $query;
  }

  public function byId($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('event')->row_array();
    return $query;
  }

  public function jmlEventByAdmin($id)
  {
    $this->db->where('admin_id', $id);
    $query = $this->db->get('event')->num_rows();
    return $query;
  }



  public function getAllTamuByEvent($id)
  {
    $this->db->where('event_id', $id);
    $query = $this->db->get('tamu')->result_array();
    return $query;
  }


  public function getAllTamuHadirByEvent($id)
  {
    $this->db->where('event_id', $id);
    $this->db->where('hadir >', 0);
    // $this->db->order_by('hadir', 'DESC');
    $query = $this->db->get('tamu')->result_array();
    return $query;
  }

  public function getTamubyId($id)
  {
    $this->db->where('id', $id);
    $query = $this->db->get('tamu')->row_array();
    return $query;
  }

  public function getTamubyIdByEvent($id, $event)
  {
    $this->db->where('id', $id);
    $this->db->where('event_id', $event);
    $query = $this->db->get('tamu')->row_array();
    return $query;
  }

  public function jmlTamuByEvent($id)
  {
    $this->db->where('event_id', $id);
    $query = $this->db->get('tamu')->num_rows();
    return $query;
  }
}