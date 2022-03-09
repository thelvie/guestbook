<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class M_table extends CI_Model
{

  public function creatTbSerial()
  {
    $this->load->dbforge();

    $field = [
      'id' => [
        'type' => 'int',
        'constraint' => 30,
        'unsigned' => true,
        'auto_increment' => true
      ],
      'serial' => [
        'type' => 'varchar',
        'constraint' => 50
      ],
      'email' => [
        'type' => 'varchar',
        'constraint' => 100
      ],
      'active' => [
        'type' => 'int',
        'constraint' => 1
      ],
      'status' => [
        'type' => 'int',
        'constraint' => 1
      ]
    ];
    $this->dbforge->add_field($field);
    $this->dbforge->add_key('id', true);
    return $this->dbforge->create_table('serial', true);
  }


  public function creatTbEvent()
  {
    $this->load->dbforge();

    $field = [
      'id' => [
        'type' => 'int',
        'constraint' => 30,
        'unsigned' => true,
        'auto_increment' => true
      ],
      'nama' => [
        'type' => 'varchar',
        'constraint' => 100
      ],
      'tgl' => [
        'type' => 'date'
      ],
      'poto' => [
        'type' => 'varchar',
        'constraint' => 200
      ],
      'admin_id' => [
        'type' => 'int',
        'constraint' => 30
      ],
      'template' => [
        'type' => 'varchar',
        'constraint' => 500,
        'null' => true,
        'default' => NULL
      ],
      'urll' => [
        'type' => 'varchar',
        'constraint' => 500,
        'null' => true,
        'default' => NULL
      ],
      'welcome' => [
        'type' => 'varchar',
        'constraint' => 50,
        'default' => 'Welcome'
      ],
      'warna' => [
        'type' => 'varchar',
        'constraint' => 300,
        'default' => '#cdff00'
      ],
      'warna_bg' => [
        'type' => 'varchar',
        'constraint' => 300,
        'default' => '#0d0d0d'
      ],
      'wa' => [
        'type' => 'text',
        'null' => true,
        'default' => NULL
      ]
    ];
    $this->dbforge->add_field($field);
    $this->dbforge->add_key('id', true);
    return $this->dbforge->create_table('event', true);
  }


  public function modifyTabEvent()
  {
    $field = [
      'warna' => [
        'type' => 'varchar',
        'constraint' => 200,
        'default' => '#cdff00'
      ],
      'warna_bg' => [
        'type' => 'varchar',
        'constraint' => 330,
        'default' => '#0d0d0d'
      ]
    ];
    return $this->dbforge->modify_column('event', $field);
  }





  public function creatTbTamu()
  {
    $this->load->dbforge();

    $field = [
      'id' => [
        'type' => 'int',
        'constraint' => 30,
        'unsigned' => true,
        'auto_increment' => true
      ],
      'nama' => [
        'type' => 'varchar',
        'constraint' => 100
      ],
      'alamat' => [
        'type' => 'varchar',
        'constraint' => 100
      ],
      'telp' => [
        'type' => 'varchar',
        'constraint' => 50
      ],
      'event_id' => [
        'type' => 'int',
        'constraint' => 30
      ],
      'poto' => [
        'type' => 'varchar',
        'constraint' => 200
      ],
      'hadir' => [
        'type' => 'int',
        'constraint' => 60,
        'default' => '0'
      ],
      'pesan' => [
        'type' => 'text',
        'default' => NULL
      ],
      'kehadiran' => [
        'type' => 'int',
        'constraint' => 11,
        'default' => '0'
      ],
      'status_pesan' => [
        'type' => 'int',
        'constraint' => 11,
        'default' => '0'
      ],
      'qr' => [
        'type' => 'varchar',
        'constraint' => 300,
        'null' => true,
        'default' => NULL
      ],
      'sapa' => [
        'type' => 'varchar',
        'constraint' => 300,
        'default' => '0'
      ],
      'timer' => [
        'type' => 'varchar',
        'constraint' => 300,
        'default' => '0'
      ],
    ];
    $this->dbforge->add_field($field);
    $this->dbforge->add_key('id', true);
    return $this->dbforge->create_table('tamu', true);
  }



  public function creatTbTamuImport()
  {
    $this->load->dbforge();

    $field = [
      'id' => [
        'type' => 'int',
        'constraint' => 30,
        'unsigned' => true,
        'auto_increment' => true
      ],
      'nama' => [
        'type' => 'varchar',
        'constraint' => 100
      ],
      'alamat' => [
        'type' => 'varchar',
        'constraint' => 100
      ],
      'event_id' => [
        'type' => 'int',
        'constraint' => 30
      ],
    ];
    $this->dbforge->add_field($field);
    $this->dbforge->add_key('id', true);
    return $this->dbforge->create_table('tamu_import', true);
  }



  public function creatTbSeting()
  {
    $this->load->dbforge();

    $field = [
      'id' => [
        'type' => 'int',
        'constraint' => 30,
        'unsigned' => true,
        'auto_increment' => true
      ],
      'bg_login' => [
        'type' => 'varchar',
        'constraint' => 200
      ],
      'logo_login' => [
        'type' => 'varchar',
        'constraint' => 200
      ],
      'url' => [
        'type' => 'varchar',
        'constraint' => 500
      ]
    ];
    $this->dbforge->add_field($field);
    $this->dbforge->add_key('id', true);
    return $this->dbforge->create_table('seting', true);
  }



  public function creatTbKonek()
  {
    $this->load->dbforge();

    $field = [
      'id' => [
        'type' => 'int',
        'constraint' => 30,
        'unsigned' => true,
        'auto_increment' => true
      ],
      'event_id' => [
        'type' => 'int',
        'constraint' => 30
      ],
      'kode' => [
        'type' => 'varchar',
        'constraint' => 50
      ],
      'url' => [
        'type' => 'varchar',
        'constraint' => 500
      ]
    ];
    $this->dbforge->add_field($field);
    $this->dbforge->add_key('id', true);
    return $this->dbforge->create_table('konek', true);
  }


  public function creatTbUndangan()
  {
    $this->load->dbforge();

    $field = [
      'id' => [
        'type' => 'int',
        'constraint' => 30,
        'unsigned' => true,
        'auto_increment' => true
      ],
      'link' => [
        'type' => 'varchar',
        'constraint' => 400
      ],
      'status' => [
        'type' => 'int',
        'constraint' => 1
      ],
      'apk' => [
        'type' => 'varchar',
        'constraint' => 200,
        'null' => true,
        'default' => NULL
      ]
    ];
    $this->dbforge->add_field($field);
    $this->dbforge->add_key('id', true);
    return $this->dbforge->create_table('undangan', true);
  }


  public function creatTbUser()
  {
    $this->load->dbforge();

    $field = [
      'id' => [
        'type' => 'int',
        'constraint' => 30,
        'unsigned' => true,
        'auto_increment' => true
      ],
      'nama' => [
        'type' => 'varchar',
        'constraint' => 400
      ],
      'username' => [
        'type' => 'varchar',
        'constraint' => 200
      ],
      'email' => [
        'type' => 'varchar',
        'constraint' => 200
      ],
      'password' => [
        'type' => 'varchar',
        'constraint' => 200
      ],
      'poto' => [
        'type' => 'varchar',
        'constraint' => 200
      ],
      'active' => [
        'type' => 'int',
        'constraint' => 10
      ],
      'role' => [
        'type' => 'int',
        'constraint' => 10
      ],
      'member' => [
        'type' => 'varchar',
        'constraint' => 100
      ],
      'register' => [
        'type' => 'date'
      ],
      'expired' => [
        'type' => 'date'
      ],
      'kunci' => [
        'type' => 'int',
        'constraint' => 10
      ],
      'event_id' => [
        'type' => 'int',
        'constraint' => 30
      ]

    ];
    $this->dbforge->add_field($field);
    $this->dbforge->add_key('id', true);
    return $this->dbforge->create_table('user', true);
  }


  public function creatdimensi($y, $x, $z)
  {
    $isi = '<?php defined(\'BASEPATH\') or exit(\'No direct script access allowed\');';
    $isi .= '$config[\'hg\']=' . '\'' . $y . '\'' . ';';
    $isi .= '$config[\'wd\']=' . '\'' . $x . '\'' . ';';
    $isi .= '$config[\'lg\']=' . '\'' . $z . '\'' . ';';
    $filename = "application/config/dimension.php";

    $file = fopen($filename, 'w+');
    fwrite($file, $isi);
    return fclose($file);
  }




  public function drop()
  {
    $this->load->dbforge();
    return $this->dbforge->drop_table('serial', TRUE);
  }


  public function isiTblEvent()
  {
    $data = [
      'id' => 1,
      'nama' => 'RamaSinta',
      'tgl' => date('Y-m-d'),
      'poto' => 'wedding.jpg',
      'admin_id' => '1'
    ];
    return $this->db->insert('event', $data);
  }

  public function isiTblUndangan()
  {
    $data = [
      'link' => base_url(),
      'status' => 2,
      'apk' => 'c1ccb8e6285da0f4a33c1e1e69d87992.zip'
    ];
    return $this->db->insert('undangan', $data);
  }

  public function isiTblUser($email)
  {
    $pass = passHash('12345678');
    $data = [
      'id' => 1,
      'nama' => 'myadmin',
      'username' => 'admin',
      'email' => $email,
      'password' => $pass,
      'poto' => 'user.jpg',
      'active' => 1,
      'role' => 1,
      'member' => 'Gold',
      'register' => date('Y-m-d'),
      'expired' => '2100-12-30',
      'kunci' => 0,
      'event_id' => 1,
    ];

    $this->db->insert('user', $data);
  }
}