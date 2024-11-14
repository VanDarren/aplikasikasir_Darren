<?php

namespace App\Http\Controllers;
use App\Models\kasir;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
public function register()
{
    $model = new kasir();
    $data['darren2'] = $model->getWhere('setting', ['id_setting' => 1]);
    echo view('header',$data);
    echo view('register',$data);
    echo view('footer');
}

public function aksiregister(Request $request)
{
    $model = new kasir();

    $username = $request->input('username');
    $password = $request->input('password');
    $confirmPassword = $request->input('confirm_password');

    // Validasi bahwa password dan confirm password harus sama
    if ($password !== $confirmPassword) {
        return redirect()->back()->withErrors(['confirm_password' => 'Password dan konfirmasi password harus sama']);
    }

    $data = [
        'username' => $username,
        'password' => $password,
        'id_level' => 2 
    ];

    $model->tambah('user', $data);
    return redirect('login')->with('success', 'Registrasi berhasil, silakan login');
}



    public function login()
	{
        $model = new kasir();
        $data['darren2'] = $model->getWhere('setting', ['id_setting' => 1]);
		echo view('header',$data);
		echo view('login',$data);
        echo view('footer');
	}

    

    public function aksi_login(Request $request)
    {
        // Mengakses input dari request
        $name = $request->input('username');
        $pw = $request->input('password');
        $captchaResponse = $request->input('g-recaptcha-response');
        $backupCaptcha = $request->input('backup_captcha');
        
        // Secret key untuk Google reCAPTCHA
        $secretKey = '6LdFhCAqAAAAAM1ktawzN-e2ebDnMnUQgne7cy53'; 
        $recaptchaSuccess = false;
        
        // Membuat instance model
        $model = new kasir(); 
        
        // Cek koneksi internet dari sisi server
        if ($this->isInternetAvailable()) {
            // Server terhubung ke internet, gunakan Google reCAPTCHA
            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse");
            $responseKeys = json_decode($response, true);
            $recaptchaSuccess = $responseKeys["success"];
        }
        
        // Jika reCAPTCHA Google berhasil diverifikasi
        if ($recaptchaSuccess) {
            // Dapatkan pengguna berdasarkan username
            $user = $model->getWhere('user', ['username' => $name]);
            
            if ($user && $user->password === $pw) { // Verifikasi password tanpa hash
                // Set session
                session()->put('username', $user->username);
                session()->put('id_user', $user->id_user);
                session()->put('id_level', $user->id_level);
    
                return redirect()->to('dashboard');
            } else {
                return redirect()->to('login')->with('error', 'Invalid username or password.');
            }
        } else {
            $storedCaptcha = session()->get('captcha_code'); 
            
            if ($storedCaptcha !== null) {
                // Verifikasi backup CAPTCHA (offline)
                if ($storedCaptcha === $backupCaptcha) {
                    // CAPTCHA valid, lanjutkan login
                    $user = $model->getWhere('user', ['username' => $name]);
    
                    if ($user && $user->password === $pw) { // Verifikasi password tanpa hash
                        // Set session
                        session()->put('username', $user->username);
                        session()->put('id_user', $user->id_user);
                        session()->put('id_level', $user->id_level);
    
                        return redirect()->to('dashboard');
                    } else {
                        return redirect()->to('login')->with('error', 'Invalid username or password.');
                    }
                } else {
                    // CAPTCHA tidak valid
                    return redirect()->to('login')->with('error', 'Invalid CAPTCHA.');
                }
            } else {
                return redirect()->to('login')->with('error', 'CAPTCHA session is not set.');
            }
        }
    }
    
    private function isInternetAvailable()
    {

        $connected = @fsockopen("www.google.com", 80); 
        if ($connected){
            fclose($connected);
            return true;
        }
        return false;
    }
    

    public function generateCaptcha()
    {
        $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        session()->put('captcha_code', $code);
    
        $image = imagecreatetruecolor(120, 40);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
    
        imagefilledrectangle($image, 0, 0, 120, 40, $bgColor);
        imagestring($image, 5, 10, 10, $code, $textColor);
    
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
    
        imagedestroy($image);
    
        return response($imageData)
                    ->header('Content-Type', 'image/png'); 
    }
    
    public function logout()
    {
        $model = new kasir();
        $id_user = session()->get('id_user');
    

        session()->flush();
        return redirect()->route('login'); 
    }

    public function dashboard()
    {
        $id_level = session()->get('id_level');
        if (!$id_level) {
            return redirect()->route('login');
        }
    
        $model = new kasir();
        $userId = session()->get('id_user');
        $username = session()->get('username');
    
        // Fetch all products for the cashier system
        $produk = $model->tampil('produk');
        
        // Fetch all customers
        $pelanggan = $model->tampil('pelanggan');
    
        $data = [
            'username' => $username,
            'id_level' => $id_level,
            'produk' => $produk,
            'pelanggan' => $pelanggan // Pass customers data to the view
        ];
    
        // Get settings or any other needed data
        $data['darren2'] = $model->getWhere('setting', ['id_setting' => 1]);
    
        // Load views
        echo view('header', $data);
        echo view('menu', $data);
        echo view('dashboard', $data);  // Changed to 'kasir' view for the cashier functionality
        echo view('footer');
    }
    
    
    public function setting()
    {
        $id_level = session()->get('id_level');	

        // Cek apakah pengguna sudah login
        if (!$id_level) {
            return redirect()->route('login'); // Redirect ke halaman login
        } elseif ($id_level != 1) {
            return redirect()->route('error404'); // Redirect ke halaman error
        } else {
            // Ambil data dari database
            $model = new kasir();
            $data['darren2'] = $model->getWhere('setting', ['id_setting' => 1]);

            // Log aktivitas pengguna
            $id_user = session()->get('id_user');
         

            $data['id_level'] = $id_level; 

            echo view('header', $data);
            echo view('menu', $data);
            echo view('setting', $data);
            echo view('footer');
        }
    }

    public function editsetting(Request $request)
    {
        // Initialize the model
        $model = new kasir();
        $namawebsite = $request->input('namaweb');
    
        $data = ['namawebsite' => $namawebsite];
    
        // Process upload for tab icon
        if ($request->hasFile('tab') && $request->file('tab')->isValid()) {
            $tab = $request->file('tab');
            $tabName = time() . '_' . $tab->getClientOriginalName(); // Save file with unique name
            $tab->move(public_path('img'), $tabName);
            $data['icontab'] = $tabName; // Save file name to database
        }
    
        // Process upload for menu icon
        if ($request->hasFile('menu') && $request->file('menu')->isValid()) {
            $menu = $request->file('menu');
            $menuName = time() . '_' . $menu->getClientOriginalName();
            $menu->move(public_path('img'), $menuName);
            $data['iconmenu'] = $menuName;
        }
    
        // Process upload for login icon
        if ($request->hasFile('login') && $request->file('login')->isValid()) {
            $login = $request->file('login');
            $loginName = time() . '_' . $login->getClientOriginalName();
            $login->move(public_path('img'), $loginName);
            $data['iconlogin'] = $loginName;
        }
    
        $where = ['id_setting' => 1];
        $model->edit('setting',$where, $data ); 
    
       
        return redirect()->route('setting')->with('success', 'Settings updated successfully!'); // Adjust as necessary
    }

    public function error404()
	{
			$model = new kasir();
			$where = array('id_setting' => 1);
			$data['darren2'] = $model->getwhere('setting', $where);
			echo view('header', $data);
			echo view('error404');
	}
    
    public function databarang()
    {
        $id_level = session()->get('id_level');
    
        // Cek apakah pengguna sudah login
        if (!$id_level) {
            return redirect()->route('login'); 
        } else {
            $model = new kasir();
            $data['barang'] = $model->tampil('produk');
            $data['darren2'] = $model->getWhere('setting', ['id_setting' => 1]);
            $data['user'] = $model->tampil('user');  // Data user untuk akses pribadi
            $data['id_level'] = $id_level;
    
            echo view('header', $data);
            echo view('menu', $data);
            echo view('databarang', $data);
            echo view('footer');
        }
    }

    public function saveTransaction(Request $request)
{
    $id_pelanggan = $request->input('id_pelanggan');
    $grandTotal = $request->input('grand_total');
    $items = $request->input('items');

    // Buat instance dari model Penjualan
    $penjualanModel = new kasir();

    // Buat data untuk tabel penjualan
    $penjualanData = [
        'id_pelanggan' => $id_pelanggan,
        'tanggal_penjualan' => now(),
        'total_harga' => $grandTotal
    ];

    // Insert ke tabel penjualan dan dapatkan id_penjualan
    $id_penjualan = $penjualanModel->tambah('penjualan', $penjualanData);

    // Buat instance dari model DetailPenjualan
    $detailPenjualanModel = new kasir();

    // Loop melalui setiap item dan masukkan ke tabel detail_penjualan
    foreach ($items as $item) {
        $detailPenjualanData = [
            'id_penjualan' => $id_penjualan,
            'id_produk' => $item['id_produk'],
            'jumlah_produk' => $item['quantity'],
            'subtotal' => $item['quantity'] * $item['harga'] // Menggunakan harga dari input
        ];

        // Insert setiap item ke tabel detail_penjualan menggunakan fungsi tambah
        $detailPenjualanModel->tambah('detailpenjualan', $detailPenjualanData);
    }

    return redirect()->back()->with('success', 'Transaksi berhasil disimpan');
}

public function tambah(Request $request)
{
    // Validasi data input
    $request->validate([
        'nama_produk' => 'required|string|max:255',
        'harga' => 'required|numeric|min:0',
        'stok' => 'required|integer|min:0',
    ]);

    // Buat data untuk ditambahkan ke database
    $data = [
        'nama_produk' => $request->input('nama_produk'),
        'harga' => $request->input('harga'),
        'stok' => $request->input('stok'),
    ];

    // Tambahkan data ke tabel barang
    $barang = new kasir(); // Sesuaikan dengan model Barang
    $barang->tambah('produk', $data); // Sesuaikan dengan method tambah yang ada

    return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
}

public function editBarang($id)
{
    $model = new kasir();
    $barang = $model->getWhere('produk', ['id_produk' => $id]);

    // Memeriksa apakah barang ditemukan
    if (!$barang) {
        return redirect()->route('databarang')->with('error', 'Barang tidak ditemukan.');
    }
    $data['darren2'] = $model->getWhere('setting', ['id_setting' => 1]);
    $data['barang'] = $barang;

    // Menampilkan form edit barang dengan data barang yang diambil
    echo view('header', $data);
    echo view('menu', $data);
    echo view('editbarang', $data);
    echo view('footer');
}


public function edit(Request $request, $id)
{
    // Validasi data input
    $request->validate([
        'nama_produk' => 'required|string|max:255',
        'harga' => 'required|numeric|min:0',
        'stok' => 'required|integer|min:0',
    ]);

    // Data untuk diupdate
    $data = [
        'nama_produk' => $request->input('nama_produk'),
        'harga' => $request->input('harga'),
        'stok' => $request->input('stok'),
    ];

    // Kondisi where berdasarkan id barang
    $where = ['id_produk' => $id];

    // Update data barang di tabel menggunakan model edit
    $barang = new kasir(); // Sesuaikan dengan model Barang
    $barang->edit('produk', $where, $data);

    return redirect()->route('databarang')->with('success', 'barang updated successfully!');
}

public function delete($id_produk)
{
    $barang = new kasir();

    // Panggil fungsi hapus dengan parameter tabel dan kondisi 'id_produk'
    $deleted = $barang->hapus('produk', ['id_produk' => $id_produk]);

    if ($deleted) {
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    } else {
        return redirect()->back()->with('error', 'Gagal menghapus barang.');
    }
}

public function penjualan()
{
    $id_level = session()->get('id_level');

    // Cek apakah pengguna sudah login
    if (!$id_level) {
        return redirect()->route('login');
    } else {
        $model = new kasir();
        // Mengambil data penjualan dengan join ke tabel pelanggan
        $data['penjualan'] = $model->join2('penjualan', 'pelanggan', 'penjualan.id_pelanggan', 'pelanggan.id_pelanggan');
        $data['id_level'] = $id_level;
        $data['darren2'] = $model->getWhere('setting', ['id_setting' => 1]);

        echo view('header', $data);
        echo view('menu', $data);
        echo view('penjualan', $data);
        echo view('footer');
    }
}

public function datapelanggan()
{
    $id_level = session()->get('id_level');

    // Cek apakah pengguna sudah login
    if (!$id_level) {
        return redirect()->route('login'); 
    } else {
        $model = new kasir();
        $data['pelanggan'] = $model->tampil('pelanggan'); // Mengambil data dari tabel pelanggan
        $data['darren2'] = $model->getWhere('setting', ['id_setting' => 1]);
        $data['id_level'] = $id_level;

        echo view('header', $data);
        echo view('menu', $data);
        echo view('pelanggan', $data); // Menampilkan view pelanggan.blade.php
        echo view('footer');
    }
}

public function tambahPelanggan(Request $request)
{
    // Validasi data input
    $request->validate([
        'nama_pelanggan' => 'required|string|max:255',
        'alamat' => 'required|string|max:255',
        'no_telp' => 'required|string|max:15',
    ]);

    // Buat data untuk ditambahkan ke database
    $data = [
        'nama_pelanggan' => $request->input('nama_pelanggan'),
        'alamat' => $request->input('alamat'),
        'no_telp' => $request->input('no_telp'),
    ];

    // Tambahkan data ke tabel pelanggan
    $pelanggan = new kasir();
    $pelanggan->tambah('pelanggan', $data);

    return redirect()->back()->with('success', 'Pelanggan berhasil ditambahkan.');
}

public function editPelanggan($id)
{
    $model = new kasir();
    $pelanggan = $model->getWhere('pelanggan', ['id_pelanggan' => $id]);

    // Memeriksa apakah pelanggan ditemukan
    if (!$pelanggan) {
        return redirect()->route('datapelanggan')->with('error', 'Pelanggan tidak ditemukan.');
    }
    $data['pelanggan'] = $pelanggan;
    $data['darren2'] = $model->getWhere('setting', ['id_setting' => 1]);

    echo view('header', $data);
    echo view('menu', $data);
    echo view('editpelanggan', $data);
    echo view('footer');
}

public function updatepelanggan(Request $request, $id)
{
    // Validasi data input
    $request->validate([
        'nama_pelanggan' => 'required|string|max:255',
        'alamat' => 'required|string|max:255',
        'no_telp' => 'required|string|max:15',
    ]);

    // Data untuk diupdate
    $data = [
        'nama_pelanggan' => $request->input('nama_pelanggan'),
        'alamat' => $request->input('alamat'),
        'no_telp' => $request->input('no_telp'),
    ];

    // Kondisi where berdasarkan id pelanggan
    $where = ['id_pelanggan' => $id];

    // Update data pelanggan di tabel
    $pelanggan = new kasir();
    $pelanggan->edit('pelanggan', $where, $data);

    return redirect()->route('datapelanggan')->with('success', 'Pelanggan berhasil diperbarui!');
}

public function deletePelanggan($id_pelanggan)
{
    $pelanggan = new kasir();

    // Hapus data pelanggan berdasarkan id_pelanggan
    $deleted = $pelanggan->hapus('pelanggan', ['id_pelanggan' => $id_pelanggan]);

    if ($deleted) {
        return redirect()->back()->with('success', 'Pelanggan berhasil dihapus.');
    } else {
        return redirect()->back()->with('error', 'Gagal menghapus pelanggan.');
    }
}

    
}
