<?php 

class Admin extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->data['nip'] 	= $this->session->userdata('nip');
		$this->data['role']	= $this->session->userdata('role');
		if (!isset($this->data['nip'], $this->data['role']))
		{
			$this->session->sess_destroy();
			redirect('login');
			exit;
		}

		if ($this->data['role'] != 'admin')
		{
			$this->session->sess_destroy();
			redirect('login');
			exit;
		}

	}

	public function index()
	{
		$this->load->model('jalan_m');
		$this->load->model('pegawai_m');
		$this->load->model('kota_m');
		$this->data['jalan']	= $this->jalan_m->get();
		$this->data['pegawai']	= $this->pegawai_m->get();
		$this->data['kota']		= $this->kota_m->get();
		$this->data['title'] 	= 'Dashboard | ' . $this->title;
		$this->data['content']	= 'admin/dashboard';
		$this->template($this->data);
	}

	public function map()
	{
		$this->load->model('jalan_m');
		$this->load->model('pegawai_m');
		$this->data['jalan']	= $this->jalan_m->get();
		$this->data['pegawai']	= $this->pegawai_m->get();
		$this->data['title'] 	= 'Dashboard | ' . $this->title;
		$this->data['content']	= 'admin/map';
		$this->template($this->data);
	}

	public function jalan()
	{
		$this->load->model('jalan_m');
		
		if ($this->POST('simpan'))
		{
			$this->data['jalan'] = [
				'nama'			=> $this->POST('nama'),
				'kelurahan'		=> $this->POST('kelurahan'),
				'kecamatan'		=> $this->POST('kecamatan'),
				'tipe'			=> $this->POST('tipe'),
				'kondisi'		=> $this->POST('kondisi'),
				'latitude'		=> $this->POST('latitude'),
				'longitude'		=> $this->POST('longitude')
			];

			$this->jalan_m->insert($this->data['jalan']);
			$this->upload($this->db->insert_id(), '../img', 'foto');

			$this->flashmsg('<i class="fa fa-check"></i> Data jalan baru berhasil disimpan');
			redirect('admin/jalan');
			exit;
		}

		if ($this->POST('edit') && $this->POST('id_data'))
		{
			$this->data['jalan'] = [
				'nama'			=> $this->POST('nama'),
				'kelurahan'		=> $this->POST('kelurahan'),
				'kecamatan'		=> $this->POST('kecamatan'),
				'tipe'			=> $this->POST('tipe'),
				'kondisi'		=> $this->POST('kondisi'),
				'latitude'		=> $this->POST('latitude'),
				'longitude'		=> $this->POST('longitude')
			];

			$this->jalan_m->update($this->POST('id_data'), $this->data['jalan']);
			$this->upload($this->POST('id_data'), '../img', 'foto');

			$this->flashmsg('<i class="fa fa-check"></i> Data jalan berhasil diedit');
			redirect('admin/jalan');
			exit;	
		}

		if ($this->POST('get') && $this->POST('id_data'))
		{
			$this->data['jalan'] = $this->jalan_m->get_row(['id_data' => $this->POST('id_data')]);
			$tipe 		= [
				'Tanah' => 'Tanah', 
				'Semen' => 'Semen', 
				'Aspal' => 'Aspal'
			];
			$kondisi	= [
				'Baik'  => 'Baik', 
				'Sedang'=> 'Sedang', 
				'Buruk'	=> 'Buruk'
			];
			$this->data['jalan']->tipe_jalan = form_dropdown('tipe', $tipe, $this->data['jalan']->tipe, ['class' => 'form-control']);
			$this->data['jalan']->kondisi_jalan = form_dropdown('kondisi', $kondisi, $this->data['jalan']->kondisi, ['class' => 'form-control']);
			echo json_encode($this->data['jalan']);
			exit;
		}

		if ($this->GET('delete') && $this->GET('id'))
		{
			$this->data['id_data'] = $this->GET('id', true);
			$this->jalan_m->delete($this->data['id_data']);
			@unlink(realpath(APPPATH . '../img/' . $this->data['id_data'] . '.jpg'));
			$this->flashmsg('<i class="fa fa-trash"></i> Data jalan berhasil dihapus', 'warning');
			redirect('admin/jalan');
			exit;	
		}

		$this->data['jalan']		= $this->jalan_m->get_by_order('id_data', 'DESC');
		$this->data['title']	= 'Data Jalan | ' . $this->title;
		$this->data['content']	= 'admin/data_jalan';
		$this->template($this->data);	
	}

	public function detail_jalan()
	{
		$this->data['id_data'] = $this->uri->segment(3);
		if (!isset($this->data['id_data']))
		{
			$this->flashmsg('<i class="fa fa-warning"></i> Required parameters are missing', 'danger');
			redirect('admin/jalan');
			exit;
		}

		$this->load->model('jalan_m');
		$this->data['jalan'] = $this->jalan_m->get_row(['id_data' => $this->data['id_data']]);
		if (!$this->data['jalan'])
		{
			$this->flashmsg('<i class="fa fa-warning"></i> Data jalan tidak ditemukan', 'danger');
			redirect('admin/jalan');
			exit;
		}

		$this->data['title'] 	= 'Detail Jalan | ' . $this->title;
		$this->data['content']	= 'admin/detail_jalan';
		$this->template($this->data);
	}

	public function kota()
	{
		$this->load->model('kota_m');
		
		if ($this->POST('simpan'))
		{

			$this->data['kota'] = [
				'namobj'		=> $this->POST('namobj'),
				'kl_dat_das'	=> $this->POST('kl_dat_das'),
				'thn_data'		=> $this->POST('thn_data'),
				'provinsi'		=> $this->POST('provinsi'),
				'kab_kota'		=> $this->POST('kab_kota'),
				'vol'			=> $this->POST('vol'),
				'biaya'			=> $this->POST('biaya'),
				'latitude'		=> $this->POST('latitude'),
				'longitude'		=> $this->POST('longitude'),
				'remarks'		=> $this->POST('remarks'),
				'metadata'		=> $this->POST('metadata'),
				'lcode'			=> $this->POST('lcode'),
				'fcode'			=> $this->POST('fcode')
			];

			$this->kota_m->insert($this->data['kota']);
			// $this->upload($this->db->insert_id(), '../img', 'foto');

			$this->flashmsg('<i class="fa fa-check"></i> Data kota baru berhasil disimpan');
			redirect('admin/kota');
			exit;
		}

		if ($this->POST('edit') && $this->POST('id'))
		{
			$this->data['kota'] = [
				'namobj'		=> $this->POST('namobj'),
				'kl_dat_das'	=> $this->POST('kl_dat_das'),
				'thn_data'		=> $this->POST('thn_data'),
				'provinsi'		=> $this->POST('provinsi'),
				'kab_kota'		=> $this->POST('kab_kota'),
				'vol'			=> $this->POST('vol'),
				'biaya'			=> $this->POST('biaya'),
				'latitude'		=> $this->POST('latitude'),
				'longitude'		=> $this->POST('longitude'),
				'remarks'		=> $this->POST('remarks'),
				'metadata'		=> $this->POST('metadata'),
				'lcode'			=> $this->POST('lcode'),
				'fcode'			=> $this->POST('fcode')
			];

			$this->kota_m->update($this->POST('id'), $this->data['kota']);
			//$this->upload($this->POST('id_data'), '../img', 'foto');

			$this->flashmsg('<i class="fa fa-check"></i> Data kota berhasil diedit');
			redirect('admin/kota');
			exit;	
		}

		if ($this->POST('get') && $this->POST('id'))
		{
			$this->data['kota'] = $this->kota_m->get_row(['id' => $this->POST('id')]);
			// $tipe 		= [
			// 	'Tanah' => 'Tanah', 
			// 	'Semen' => 'Semen', 
			// 	'Aspal' => 'Aspal'
			// ];
			// $kondisi	= [
			// 	'Baik'  => 'Baik', 
			// 	'Sedang'=> 'Sedang', 
			// 	'Buruk'	=> 'Buruk'
			// ];
			// $this->data['kota']->tipe_kota = form_dropdown('tipe', $tipe, $this->data['kota']->tipe, ['class' => 'form-control']);
			// $this->data['kota']->kondisi_kota = form_dropdown('kondisi', $kondisi, $this->data['kota']->kondisi, ['class' => 'form-control']);
			echo json_encode($this->data['kota']);
			exit;
		}

		if ($this->GET('delete') && $this->GET('id'))
		{
			$this->data['id_data'] = $this->GET('id', true);
			$this->kota_m->delete($this->data['id_data']);
			@unlink(realpath(APPPATH . '../img/' . $this->data['id_data'] . '.jpg'));
			$this->flashmsg('<i class="fa fa-trash"></i> Data kota berhasil dihapus', 'warning');
			redirect('admin/kota');
			exit;	
		}

		$this->data['kota']		= $this->kota_m->get_by_order('id', 'DESC');
		$this->data['title']	= 'Data Kota | ' . $this->title;
		$this->data['content']	= 'admin/data_kota';
		$this->template($this->data);	
	}

	public function detail_kota()
	{
		$this->data['id_data'] = $this->uri->segment(3);
		if (!isset($this->data['id_data']))
		{
			$this->flashmsg('<i class="fa fa-warning"></i> Required parameters are missing', 'danger');
			redirect('admin/kota');
			exit;
		}

		$this->load->model('kota_m');
		$this->data['kota'] = $this->kota_m->get_row(['id' => $this->data['id_data']]);
		if (!$this->data['kota'])
		{
			$this->flashmsg('<i class="fa fa-warning"></i> Data kota tidak ditemukan', 'danger');
			redirect('admin/kota');
			exit;
		}

		$this->data['title'] 	= 'Detail Kota | ' . $this->title;
		$this->data['content']	= 'admin/detail_kota';
		$this->template($this->data);
	}

	public function user()
	{
		$this->load->model('pegawai_m');
		
		if ($this->POST('simpan'))
		{
			$this->data['user'] = [
				'nip'			=> $this->POST('nip'),
				'nama'			=> $this->POST('nama'),
				'jabatan'		=> $this->POST('jabatan'),
				'email'			=> $this->POST('email'),
				'nomor_hp'		=> $this->POST('nomor_hp'),
				'password'		=> md5($this->POST('password')),
				'role'			=> $this->POST('role')
			];

			$this->pegawai_m->insert($this->data['user']);

			$this->flashmsg('<i class="fa fa-check"></i> Data user baru berhasil disimpan');
			redirect('admin/user');
			exit;
		}

		if ($this->POST('edit') && $this->POST('nip_pk'))
		{
			$password = $this->POST('password');

			$this->data['user'] = [
				'nip'			=> $this->POST('nip'),
				'nama'			=> $this->POST('nama'),
				'jabatan'		=> $this->POST('jabatan'),
				'email'			=> $this->POST('email'),
				'nomor_hp'		=> $this->POST('nomor_hp'),
				'role'			=> $this->POST('role')
			];

			if (!empty($password)) $this->data['user']['password'] = md5($password);

			$this->pegawai_m->update($this->POST('nip_pk'), $this->data['user']);
			$this->flashmsg('<i class="fa fa-check"></i> Data user berhasil diedit');
			redirect('admin/user');
			exit;	
		}

		if ($this->POST('get') && $this->POST('nip'))
		{
			$this->data['user'] = $this->pegawai_m->get_row(['nip' => $this->POST('nip')]);
			$role = [
				'admin'			=> 'Admin',
				'kepala dinas'	=> 'Kepala Dinas'
			];
			$this->data['user']->dropdown = form_dropdown('role', $role, $this->data['user']->role, ['id' => 'role', 'class' => 'form-control']);
			echo json_encode($this->data['user']);
			exit;
		}

		if ($this->GET('delete') && $this->GET('id'))
		{
			$this->data['nip'] = $this->GET('id', true);
			$this->pegawai_m->delete($this->data['nip']);
			$this->flashmsg('<i class="fa fa-trash"></i> Data user berhasil dihapus', 'warning');
			redirect('admin/user');
			exit;	
		}

		$this->data['pegawai']	= $this->pegawai_m->get_by_order('nama', 'ASC');
		$this->data['title']	= 'Data User | ' . $this->title;
		$this->data['content']	= 'admin/data_user';
		$this->template($this->data);
	}

	public function detail_user()
	{
		$this->data['nip'] = $this->uri->segment(3);
		if (!isset($this->data['nip']))
		{
			$this->flashmsg('<i class="fa fa-warning"></i> Required parameters are missing', 'danger');
			redirect('admin/user');
			exit;
		}

		$this->load->model('pegawai_m');
		$this->data['pegawai'] = $this->pegawai_m->get_row(['nip' => $this->data['nip']]);
		if (!isset($this->data['pegawai']))
		{
			$this->flashmsg('<i class="fa fa-warning"></i> Data pegawai tidak ditemukan', 'danger');
			redirect('admin/user');
			exit;	
		}

		$this->data['title']	= 'Detail User | ' . $this->title;
		$this->data['content']	= 'admin/detail_user';
		$this->template($this->data);
	}
}