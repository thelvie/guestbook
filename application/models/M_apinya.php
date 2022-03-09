<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');


class M_apinya extends CI_Model
{

  public function getByKode($kode, $email)
  {
    $dtEmail1 = [

      'email' => 'fiturbukutamu@gmail.com',
      'kode' => '9515a317'

    ];
    $dtEmail2 = [


      'email' => 'undanganwedding80@gmail.com',
      'kode' => '991af321'

    ];
    $dtEmail3 = [

      'email' => 'diamengundang@gmail.com',
      'kode' => '9ea61750'

    ];
    $dtEmail4 = [

      'email' => 'asepnugrah4@gmail.com',
      'kode' => 'a5bd1143'

    ];
    $dtEmail5 = [

      'email' => 'inivitationid@gmail.com',
      'kode' => 'a9c9a348'

    ];
    $dtEmail6 = [

      'email' => 'admin@gmail.com',
      'kode' => '12345678'

    ];

    if ($email == $dtEmail1['email'] && $kode == $dtEmail1['kode']) {
      return true;
    } elseif ($email == $dtEmail2['email'] && $kode == $dtEmail2['kode']) {
      return true;
    } elseif ($email == $dtEmail3['email'] && $kode == $dtEmail3['kode']) {
      return true;
    } elseif ($email == $dtEmail4['email'] && $kode == $dtEmail4['kode']) {
      return true;
    } elseif ($email == $dtEmail5['email'] && $kode == $dtEmail5['kode']) {
      return true;
    } elseif ($email == $dtEmail6['email'] && $kode == $dtEmail6['kode']) {
      return true;
    } else {
      return false;
    }
  }


  public function updateCheck($id)
  {
    $set = date('Y-m-d', strtotime('+2 day'));
    $this->db->set('checkin', $set)->where('id', $id);
    return $this->db->update('user');
  }
}