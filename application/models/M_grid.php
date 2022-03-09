<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_grid extends CI_Model
{

  // var $table = 'quotation';
  // var $column_order = array(null, 'kode', 'customer', 'model', 'engine', 'tgl', 'unit', 'status', 'user_id');
  // var $column_search = array('kode', 'customer', 'model', 'engine', 'tgl', 'unit', 'status', 'user_id'); 
  // var $order = array('id' => 'desc');

  // var $where = [
  //   'user' => 'user_id',
  //   'status' => 'status'
  // ];

  public function __construct()
  {
    parent::__construct();
  }



  public function getTable($table, $col_order, $order, $search)
  {
    $this->db->from($table);

    $i = 0;

    $cari = $_POST['search']['value'];

    foreach ($search as $item) {

      if ($cari) {

        if ($i === 0) {

          $this->db->group_start();

          $this->db->like($item, $cari);
        } else {

          $this->db->or_like($item, $cari);
        }

        if (count($search) - 1 == $i) {

          $this->db->group_end();
        }
      }
      $i++;
    }

    if (isset($_POST['order'])) {
      $this->db->order_by($col_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
    } else if (isset($order)) {
      $this->db->order_by(key($order), $order[key($order)]);
    }
  }



  public function get_datatables($table, $col_order, $order, $search, $where, $val)
  {
    $this->getTable($table, $col_order, $order, $search);
    if ($_POST['length'] != -1)
      $this->db->limit($_POST['length'], $_POST['start']);

    foreach ($where as $key) {

      foreach ($val as $vl) {

        if ($vl !== false) {
          $this->db->where($key, $vl);
        }
      }
    }


    $query = $this->db->get()->result_array();
    return $query;
  }



  public function count_filter($table, $col_order, $order, $search, $where, $val)
  {
    $this->getTable($table, $col_order, $order, $search);

    foreach ($where as $key) {

      foreach ($val as $vl) {

        if ($vl !== false) {
          $this->db->where($key, $vl);
        }
      }
    }

    $query = $this->db->get()->num_rows();
    return $query;
  }



  public function count_all($table, $where, $val)
  {
    $this->db->from($table);

    foreach ($where as $key) {

      foreach ($val as $vl) {

        if ($vl !== false) {
          $this->db->where($key, $vl);
        }
      }
    }

    return $this->db->count_all_results();
  }
}