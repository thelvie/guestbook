<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master extends CI_Controller
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
    sedangLOckAccess();

    if (!$this->session->userdata('sessionEventCek')) {
      redirect('master/setSesiEvent');
    }

    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('emailGuestBook')])->row_array();
    $event = $this->m_event->byId($this->session->userdata('sessionEventCek'));

    $jmlEvent = $this->m_event->jmlEventByAdmin($data['user']['id']);
    if ($jmlEvent < 1) {
      redirect('settings');
    }

    $data['event'] = $event['nama'];
    $data['judul'] = 'Master';
    $this->load->view('temp/header', $data);
    $this->load->view('master/index', $data);
  }


  public function setSesiEvent()
  {
    $idUser = $this->session->userdata('idUserGuestBook');

    $jmlEvent = $this->m_event->jmlEventByAdmin($idUser);
    if ($jmlEvent < 1) {
      $this->session->set_flashdata('error', 'CREAT EVENT BARU..!');
      redirect('setting');
    }

    $event = $this->db->get_where('event', ['admin_id' => $idUser])->row_array();
    if ($event) {
      $this->session->set_userdata('sessionEventCek', $event['id']);
    } else {
      $data = [
        'nama' => 'Nama Event',
        'tgl' => date('Y-m-d'),
        'poto' => 'wedding.jpg',
        'admin_id' => $idUser
      ];

      $this->db->insert('event', $data);
    }

    redirect('master');
  }




  public function datatable()
  {
    $event = $this->session->userdata('sessionEventCek');
    $this->load->model('m_grid');
    $table = 'tamu';
    $col_order = [null, 'nama', 'alamat', 'telp'];
    $order = ['id' => 'desc'];
    $search = ['nama', 'alamat', 'telp'];

    $where = ['event_id'];
    $valu = [$event];
    $list = $this->m_grid->get_datatables($table, $col_order, $order, $search, $where, $valu);

    $data = array();
    $no = $_POST['start'];

    foreach ($list as $key) {

      $no++;
      $row = array();
      $row[] = $no;
      $row[] = $key['nama'];
      $row[] = $key['alamat'];
      // $row[] = $key['telp'];
      $row[] = '<div class="btn-group btn-group-sm"><button type="button" data-id="' . $key['id'] . '" data-nama="' . $key['nama'] . '" data-alamat="' . $key['alamat'] . '" data-telp="' . $key['telp'] . '" class="btn btn-sm btn-info btnEdit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="mdi mdi-pencil"></i></button><button type="button" data-id="' . $key['id'] . '" class="btn btn-sm btn-danger btnDelet" data-toggle="tooltip" data-placement="top" title="Delete"><i class="mdi mdi-delete"></i></button><button type="button" data-id="' . $key['id'] . '" class="btn btn-sm btn-info btnLinkWp" data-toggle="tooltip" data-placement="top" title="Undangan WP"><i class="mdi mdi-wordpress"></i></button><button type="button" data-id="' . $key['id'] . '" class="btn btn-sm btn-success btnWA" data-toggle="tooltip" data-placement="top" title="Share WhatsApp"><i class="mdi mdi-whatsapp"></i></button><button type="button" data-id="' . $key['id'] . '" class="btn btn-sm btn-secondary btnCopy" data-toggle="tooltip" data-placement="top" title="Copy Link"><i class="mdi mdi-content-copy"></i></button><a href="' . base_url('master/qrDownload/' . $key['id']) . '" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title=" QRcode"><i class="mdi mdi-qrcode-scan"></i></a></div>';

      $data[] = $row;
    }

    $output = array(
      "draw" => $_POST['draw'],
      "recordsTotal" => $this->m_grid->count_all($table, $where, $valu),
      "recordsFiltered" => $this->m_grid->count_filter($table, $col_order, $order, $search, $where, $valu),
      "data" => $data,
    );
    //output to json format
    echo json_encode($output);
  }


  public function newData()
  {
    // $nama = htmlspecialchars(trim($this->input->post('nama', true)));
    // $alamat = htmlspecialchars(trim($this->input->post('alamat', true)));
    // $telp = htmlspecialchars(trim($this->input->post('telp', true)));

    $nama = trim($this->input->post('nama', true));
    $alamat = trim($this->input->post('alamat', true));

    $nama = str_replace('&', 'dan', $nama);
    $nama = str_replace('&amp;', 'dan', $nama);

    if ($alamat == "") {
      $alamat = '-';
    }

    $event = trim($this->input->post('event'));


    $cekTamu = $this->db->get_where('tamu', ['nama' => $nama, 'event_id' => $event])->row_array();
    if ($cekTamu) {
      $json['kode'] = 3;
      $json['text'] = 'NAMA Yang sama sudah terdaftar, Silahkan gunakan NAMA LAIN Atau tambahkan HURUP/Karakter';
      echo json_encode($json);
    } else {
      $data = [
        'nama' => $nama,
        'alamat' => $alamat,
        'telp' => 0,
        'event_id' => $event,
        'poto' => 'tamu.jpg',
        'qr' => time() . uniqid(),
      ];
      $insert = $this->db->insert('tamu', $data);
      if ($this->db->affected_rows($insert) >= 1) {
        $json['kode'] = 1;
        $json['text'] = 'Berhasil Save Data';
      } else {
        $json['kode'] = 2;
        $json['text'] = 'GAGAL Save data Silahkan diulang..!';
      }
      echo json_encode($json);
    }
  }


  public function editData()
  {
    // $nama = htmlspecialchars(trim($this->input->post('nama1', true)));
    // $alamat = htmlspecialchars(trim($this->input->post('alamat1', true)));

    $nama = trim($this->input->post('nama1', true));
    $alamat = trim($this->input->post('alamat1', true));

    $nama = str_replace('&', 'dan', $nama);
    $nama = str_replace('&amp;', 'dan', $nama);

    if ($alamat == "") {
      $alamat = '-';
    }

    $id = trim($this->input->post('id'));

    $this->db->set([
      'nama' => $nama,
      'alamat' => $alamat,
    ]);
    $insert = $this->db->where('id', $id)->update('tamu');
    if ($insert) {
      $json['kode'] = 1;
      $json['text'] = 'Berhasil Update Data';
    } else {
      $json['kode'] = 2;
      $json['text'] = 'GAGAL Update data Silahkan diulang..!';
    }
    echo json_encode($json);
  }


  public function deleteData($id)
  {
    $tamu = $this->db->get_where('tamu', ['id' => $id])->row_array();
    $qr = $tamu['qr'];
    $poto = $tamu['poto'];
    if ($poto !== 'tamu.jpg') {
      unlink(FCPATH . 'guestbook/assets/images/guest/' . $poto);
    }
    if (file_exists('guestbook/assets/images/qr/' . $qr . '.png')) {
      unlink(FCPATH . 'guestbook/assets/images/qr/' . $qr . '.png');
    }

    $this->db->where('id', $id)->delete('tamu');
  }






  public function cekDataWp($id)
  {
    $url = $this->db->get_where('undangan', ['status' => '3'])->row_array();
    $tamu = $this->db->get_where('tamu', ['id' => $id])->row_array();
    $event = $this->db->get_where('event', ['id' => $tamu['event_id']])->row_array();

    if ($event['urll'] == null) {
      echo 1;
      return false;
    }

    $nama = $tamu['nama'];
    $nama = urlencode($nama);

    $link = $event['urll'] . '?to=' . $nama;

    $kode = $tamu['nama'];
    if (!file_exists('guestbook/assets/images/qr/' . $tamu['qr'] . '.png')) {
      $this->creatBarcode($kode, $tamu['qr']);
    }

    echo $link;
  }






  public function shareWp($id)
  {
    $url = $this->db->get_where('undangan', ['status' => '3'])->row_array();
    $tamu = $this->db->get_where('tamu', ['id' => $id])->row_array();
    $event = $this->db->get_where('event', ['id' => $tamu['event_id']])->row_array();

    if ($event['urll'] == null) {
      echo 1;
      return false;
    }

    $nama = $tamu['nama'];
    $namakode = urlencode($nama);

    $link = $event['urll'] . '?to=' . $namakode;
    $kode = $tamu['nama'];
    if (!file_exists('guestbook/assets/images/qr/' . $tamu['qr'] . '.png')) {
      $this->creatBarcode($kode, $tamu['qr']);
    }

    $crop = substr($tamu['telp'], 0, 1);
    if ($crop == "0") {
      $telpon = substr($tamu['telp'], 1);
      $telpon = '+62' . $telpon;
    } else {
      $telpon = $tamu['telp'];
    }

    $gantiLink = str_replace('[LINK-UNDANGAN]', $link, $event['wa']);
    $gantiLink2 = str_replace('[NAMA-TAMU]', $nama, $gantiLink);

    $link = urlencode($gantiLink2);
    $wa = 'https://api.whatsapp.com/send?phone=&text=' . $link;
    echo $wa;
  }



  public function copyWa($id)
  {
    // $url = $this->db->get_where('undangan', ['status' => '3'])->row_array();
    $tamu = $this->db->get_where('tamu', ['id' => $id])->row_array();
    $event = $this->db->get_where('event', ['id' => $tamu['event_id']])->row_array();

    if ($event['urll'] == null) {
      echo 1;
      return false;
    }

    $nama = $tamu['nama'];
    $namakode = urlencode($nama);

    $link = $event['urll'] . '?to=' . $namakode;
    $kode = $tamu['nama'];
    if (!file_exists('guestbook/assets/images/qr/' . $tamu['qr'] . '.png')) {
      $this->creatBarcode($kode, $tamu['qr']);
    }

    $crop = substr($tamu['telp'], 0, 1);
    if ($crop == "0") {
      $telpon = substr($tamu['telp'], 1);
      $telpon = '+62' . $telpon;
    } else {
      $telpon = $tamu['telp'];
    }

    $gantiLink = str_replace('[LINK-UNDANGAN]', $link, $event['wa']);
    $gantiLink2 = str_replace('[NAMA-TAMU]', $nama, $gantiLink);

    $link = urlencode($gantiLink2);
    // $wa = 'https://api.whatsapp.com/send?phone=&text=' . $link;
    echo $gantiLink2;
  }








  public function printQr($id = 'not')
  {
    $tamu = $this->db->get_where('tamu', ['id' => $id])->row_array();
    if (!$tamu) {
      redirect('master');
    }

    $kode = $tamu['id'] . '_' . $tamu['nama'];
    if (!file_exists('guestbook/assets/images/qr/' . $tamu['qr'] . '.png')) {
      $this->creatBarcode($kode, $tamu['qr']);
    }

    $data['qrNya'] = $tamu['qr'] . '.png';
    $data['tamu'] = $tamu;
    $data['event'] = $this->m_event->byId($tamu['event_id']);
    $data['judul'] = 'Print Qrcode';
    $this->load->view('master/print', $data);
  }

  public function printQrAll()
  {
    $idEvent = $this->session->userdata('sessionEventCek');
    $tamu = $this->db->get_where('tamu', ['event_id' => $idEvent])->result_array();
    if (!$tamu) {
      redirect('master');
    }

    foreach ($tamu as $key) {
      $kode = $key['id'] . '_' . $key['nama'];
      if (!file_exists('guestbook/assets/images/qr/' . $key['qr'] . '.png')) {
        $this->creatBarcode($kode, $key['qr']);
      }
    }


    $data['tamu'] = $tamu;
    $data['event'] = $this->m_event->byId($idEvent);
    $data['judul'] = 'Print Qrcode';
    $this->load->view('master/printall', $data);
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



  public function apkDownload()
  {
    $undangan = $this->db->get_where('undangan', ['status' => 2])->row_array();
    $path = $undangan['link'] . 'guestbook/assets/apk/';
    $apk = $path . $undangan['apk'];

    force_download('Checkin_Geustbook.zip', file_get_contents($apk));
  }


  public function qrDownload($id)
  {
    $tamu = $this->db->get_where('tamu', ['id' => $id])->row_array();
    if (!$tamu) {
      redirect('master');
    }

    $kode = $tamu['nama'];
    // if (!file_exists('guestbook/assets/images/qr/' . $tamu['qr'] . '.png')) {
    //   $this->creatBarcode($kode, $tamu['qr']);
    // }
    $this->creatBarcode($kode, $tamu['qr']);

    $qr = $tamu['qr'] . '.png';
    $path = base_url() . 'guestbook/assets/images/qr/' . $qr;

    force_download($qr, file_get_contents($path));
  }




  // IMPORT
  public function import()
  {
    sedangLogout();
    sedangLOckAccess();

    $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('emailGuestBook')])->row_array();
    $event = $this->m_event->byId($this->session->userdata('sessionEventCek'));

    $cekTbl = $this->db->table_exists('tamu_import');
    if (!$cekTbl) {
      $this->m_table->creatTbTamuImport();
    }

    $jmlEvent = $this->m_event->jmlEventByAdmin($data['user']['id']);
    if ($jmlEvent < 1) {
      redirect('settings');
    }
    $data['event'] = $event;

    $data['tamuImpor'] = $this->db->get_where('tamu_import', ['event_id' => $event['id']])->result_array();
    $data['jmltamuImpor'] = $this->db->get_where('tamu_import', ['event_id' => $event['id']])->num_rows();
    $data['judul'] = 'Import';
    $this->load->view('temp/header', $data);
    $this->load->view('master/import', $data);
  }




  public function dwlTemplate()
  {
    $data = 'guestbook/assets/templateDataTamu.xlsx';
    force_download($data, null);
  }


  public function importExcel()
  {
    // Load plugin PHPExcel nya
    include APPPATH . 'third_party/PHPExcel/PHPExcel.php';

    $config['upload_path'] = './excel/';
    $config['allowed_types'] = 'xlsx|xls|csv';
    $config['max_size'] = '10000';
    $config['overwrite'] = true;
    $config['encrypt_name'] = true;

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('file')) {

      //upload gagal
      $this->session->set_flashdata('gagal', $this->upload->display_errors());
      //redirect halaman
      redirect('master/import');
    } else {

      $data_upload = $this->upload->data();

      $excelreader     = new PHPExcel_Reader_Excel2007();
      $loadexcel         = $excelreader->load('excel/' . $data_upload['file_name']); // Load file yang telah diupload ke folder excel
      $sheet             = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

      $data = array();
      $event = $this->m_event->byId($this->session->userdata('sessionEventCek'));
      $numrow = 1;
      foreach ($sheet as $row) {
        if ($numrow > 1) {
          array_push($data, array(
            'nama' => $row['A'],
            'alamat' => $row['B'],
            'event_id' => $event['id']
          ));
        }
        $numrow++;
      }
      $this->db->insert_batch('tamu_import', $data);

      //delete file from server
      unlink(FCPATH . 'excel/' . $data_upload['file_name']);
      $temp = glob('excel/*');
      foreach ($temp as $tmp) {
        if (is_file($tmp))
          unlink($tmp); //delete file
      }

      //upload success
      redirect('master/import');
    }
  }


  public function addDataImport()
  {
    $event = $this->m_event->byId($this->session->userdata('sessionEventCek'));
    $tamuImport = $this->db->get_where('tamu_import', ['event_id' => $event['id']])->result_array();
    $jmlImp = $this->db->get_where('tamu_import', ['event_id' => $event['id']])->num_rows();

    if ($jmlImp > 0) {

      // $data = [];
      $numrow = 1;
      foreach ($tamuImport as $key) {
        $nama = $key['nama'];
        $alamat = $key['alamat'];

        $nama = str_replace('&', 'dan', $nama);
        $nama = str_replace('&amp;', 'dan', $nama);

        if ($alamat == "") {
          $alamat = '-';
        }

        $cekTamu = $this->db->get_where('tamu', ['nama' => $key['nama'], 'event_id' => $event['id']])->row_array();
        if ($cekTamu) {
          $this->db->set('nama', $nama)->where('id', $cekTamu['id'])->update('tamu');
        } else {

          $data = [
            'nama' => $nama,
            'alamat' => $alamat,
            'telp' => 0,
            'event_id' => $event['id'],
            'poto' => 'tamu.jpg',
            'qr' => time() . uniqid() . $key['id'],
          ];
          $this->db->insert('tamu', $data);
        }
      }
      $this->db->where('event_id', $event['id'])->delete('tamu_import');
      $this->session->set_flashdata('berhasil', 'Import data Berhasil');
      redirect('master');
    } else {
      // $this->session->set_flashdata('gagal', 'Import data GAGAL');
      redirect('master/import');
    }
  }




  public function delImport($id)
  {
    $this->db->where('id', $id)->delete('tamu_import');
    redirect('master/import');
  }


  public function cancelImport()
  {
    $event = $this->m_event->byId($this->session->userdata('sessionEventCek'));
    $this->db->where('event_id', $event['id'])->delete('tamu_import');
    redirect('master/import');
  }
}