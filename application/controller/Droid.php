<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Droid extends CI_Controller
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

    $random = uniqid();

    $cekRandom = $this->db->get_where('konek', ['kode' => $random])->row_array();
    if ($cekRandom) {
      redirect('droid');
    }

    $ipConfig = '192.168.1.9';

    $url = str_replace('localhost', $ipConfig, base_url());


    if (!$this->session->userdata('sessionEventCek')) {
      $this->session->set_flashdata('error', 'Select Event Active.!!');
      redirect('home');
    }

    $cekKode = $this->db->get_where('konek', ['event_id' => $this->session->userdata('sessionEventCek')])->row_array();
    
    if (!$cekKode) {
      $dataK = [
        'event_id' => $this->session->userdata('sessionEventCek'),
        'kode' => $random,
        'url' => $url
      ];
      $this->db->insert('konek', $dataK);
      // $this->creatBarcode('url=' . $url . $random, $random);
    } else {
      $this->db->set('url', $url)->where('event_id', $this->session->userdata('sessionEventCek'))->update('konek');
    }

    $cekKodbarcode = $this->db->get_where('konek', ['event_id' => $this->session->userdata('sessionEventCek')])->row_array();
    $this->creatBarcode('url='.$cekKodbarcode['url'] . '&kode=' . $cekKodbarcode['kode'], $cekKodbarcode['kode']);

    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('emailGuestBook')])->row_array();

    $data['event'] = $this->m_event->allByAdminId($data['user']['id']);
    $data['eventActive'] = $this->m_event->byId($this->session->userdata('sessionEventCek'));

    $data['konek'] = $this->db->get_where('konek', ['event_id' => $this->session->userdata('sessionEventCek')])->row_array();

    $data['judul'] = 'Android Connect';
    $this->load->view('temp/header', $data);
    $this->load->view('setting/android', $data);
  }







  public function creatBarcode($barcode, $name)
  {
    $this->load->library('ciqrcode'); //pemanggilan library QR CODE
    $config['cacheable']    = true; //boolean, the default is true
    $config['cachedir']     = './guestbook/assets/images/'; //string, the default is application/cache/
    $config['errorlog']     = './guestbook/assets/images/'; //string, the default is application/logs/
    $config['imagedir']     = './guestbook/assets/images/qr/'; //direktori penyimpanan qr code
    $config['quality']      = true; //boolean, the default is true
    $config['size']         = '1024'; //interger, the default is 1024
    $config['black']        = array(255, 255, 255); // array, default is array(255,255,255)
    $config['white']        = array(0, 0, 0); // array, default is array(0,0,0)
    $this->ciqrcode->initialize($config);

    $qrName = $name . '.png'; //buat name dari qr code sesuai dengan nik

    $params['data'] = $barcode; //data yang akan di jadikan QR CODE
    $params['level'] = 'H'; //H=High
    $params['size'] = 10;
    $params['savename'] = FCPATH . $config['imagedir'] . $qrName; //simpan image QR CODE ke folder assets/images/
    return $this->ciqrcode->generate($params); // fungsi untuk generate QR CODE
  }






  // KONEKSI
  public function androidkonek($kode)
  {
    $kodeKonek = $this->db->get_where('konek',['kode' => $kode])->row_array();
    if ($kodeKonek) {
      $event = $this->m_event->byId($kodeKonek['event_id']);
      $respon['kode'] = "1";
      $respon['event'] = $event['nama'];
      $respon['poto'] = base_url('guestbook/assets/images/event/') . $event['poto'];
      echo json_encode($respon);
    } else {
      $respon['kode'] = "no200";
      echo json_encode($respon);
    }
  }


  public function cekDataTamu()
  {
    $konek = $this->input->post('konek');
    $barcode = $this->input->post('barcode');

    $time = time();
    $timer = $time + (10);
    
    $konesi = $this->db->get_where('konek',['kode' => $konek])->row_array();
    $idEvent = $konesi['event_id'];

    $this->db->set('sapa', 0)->where(['event_id' => $idEvent])->update('tamu');

    $cekTamu = $this->db->get_where('tamu',['nama' => $barcode, 'event_id' => $idEvent])->row_array();
    if ($cekTamu == true) {
      if ($cekTamu['hadir'] == 0) {
        $this->db->set(['hadir'=> $time, 'sapa' => 1, 'timer' => $timer])->where('id', $cekTamu['id'])->update('tamu');
        $respon['kode'] = '1';
        $respon['pesan'] = 'Terimakasih, SELAMAT DATANG ';
        $respon['detail'] = 'Name: ' . $cekTamu['nama'] . '<br> Address: ' . $cekTamu['alamat'];
        $respon['chekin'] = 'Checkin: ' . date('d/m/Y H:i:s', time());
        $respon['idT'] = $cekTamu['id'];
        $respon['nama'] = $cekTamu['nama'];
        $respon['waktu'] = date('d/m/Y H:i:s', time());
        echo json_encode($respon);
        return false;
      } else {
        $this->db->set(['sapa' => 1, 'timer' => $timer])->where('id', $cekTamu['id'])->update('tamu');
        $respon['kode'] = '2';
        $respon['pesan'] = 'Terimakasih,' . $cekTamu['nama'];
        $respon['detail'] = 'Anda Sudah Checkin Sebelumnya,';
        $respon['chekin'] = 'Pada: '. date('d/m/Y H:i:s', $cekTamu['hadir']);
        $respon['idT'] = $cekTamu['id'];
        $respon['nama'] = $cekTamu['nama'];
        $respon['waktu'] = date('d/m/Y H:i:s', $cekTamu['hadir']);
        echo json_encode($respon);
        return false;
      }
    } else {
      $respon['kode'] = '0';
      $respon['pesan'] = 'Barcode tidak DIKENAL';
      echo json_encode($respon);
      return false;
    }
  }



  public function upoadImge($konek, $id)
  {
    $konesi = $this->db->get_where('konek',['kode' => $konek])->row_array();
    $idEvent = $konesi['event_id'];

    $tamu = $this->m_event->getTamubyIdByEvent($id, $idEvent);

    $img = file_get_contents('php://input');

    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $img = base64_decode($img);
    $nImg = uniqid() . time() . '.png';

    if (file_put_contents('./guestbook/assets/images/guest/' . $nImg, $img) == true) {
      if ($tamu['poto'] !== 'tamu.jpg') {
        unlink(FCPATH . 'guestbook/assets/images/guest/' . $tamu['poto']);
      }

      $this->db->set('poto', $nImg)->where('id', $id)->update('tamu');
    }

  }






  public function checkin($barcode)
  {
    // $barcode = trim($this->input->post('barcode'));
    $array = explode('_', $barcode);
    $index1 = $array[0];
    $id = str_replace(' ', '', $index1);
    $idEvent = $this->session->userdata('sessionEventCek');
    $cekTamu = $this->m_event->getTamubyIdByEvent($id, $idEvent);
    if ($cekTamu == true) {
      if ($cekTamu['hadir'] == 0) {
        $this->db->set('hadir', time())->where('id', $id)->update('tamu');
        $respon = 'Terimakasih, SELAMAT DATANG ' . $cekTamu['nama'];
        echo $respon;
        return false;
      } else {
        $respon = 'Terimakasih,';
        echo $respon;
        return false;
      }
    } else {
      $respon = 'Barcode tidak dikenal, SILAHKAN DIULANG.!';
      echo $respon;
      return false;
    }
  }

  public function checkin2()
  {
    $konek = trim($this->input->post('konek'));
    $barcode = trim($this->input->post('barcode'));
    $array = explode('_', $barcode);
    $index1 = $array[0];
    $id = str_replace(' ', '', $index1);
    
    $konesi = $this->db->get_where('konek',['kode' => $konek])->row_array();
    $idEvent = $konesi['event_id'];

    $cekTamu = $this->m_event->getTamubyIdByEvent($id, $idEvent);
    if ($cekTamu == true) {
      if ($cekTamu['hadir'] == 0) {
        $this->db->set('hadir', time())->where('id', $id)->update('tamu');
        $respon['kode'] = '1';
        $respon['pesan'] = 'Terimakasih, SELAMAT DATANG ';
        $respon['detail'] = 'Name: ' . $cekTamu['nama'] . '<br> Address: ' . $cekTamu['alamat'];
        $respon['chekin'] = 'Checkin: ' . date('d/m/Y H:i:s', time());
        echo json_encode($respon);
        return false;
      } else {
        $respon['kode'] = '2';
        $respon['pesan'] = 'Terimakasih,' . $cekTamu['nama'];
        $respon['detail'] = 'Anda Sudah Checkin Sebelumnya,';
        $respon['chekin'] = 'Pada: '. date('d/m/Y H:i:s', $cekTamu['hadir']);
        echo json_encode($respon);
        return false;
      }
    } else {
      $respon['kode'] = '0';
      $respon['pesan'] = 'Barcode tidak DIKENAL';
      echo json_encode($respon);
      return false;
    }
  }





  public function cekIp2()
  {
    if (isset($_SERVER)) {
 
      if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
          return $_SERVER["HTTP_X_FORWARDED_FOR"];

      if (isset($_SERVER["HTTP_CLIENT_IP"]))
          return $_SERVER["HTTP_CLIENT_IP"];

      return $_SERVER["REMOTE_ADDR"];
      }

      if (getenv('HTTP_X_FORWARDED_FOR'))
          return getenv('HTTP_X_FORWARDED_FOR');

      if (getenv('HTTP_CLIENT_IP'))
          return getenv('HTTP_CLIENT_IP');

      return getenv('REMOTE_ADDR');

    }
    
    
    public function cekIp()
    {
      $ip = $this->cekIp2();
      echo "<h2 align=\"center\">Your IP Address ". $ip;
  }
}