<body class="light ">
    <div class="wrapper vh-100">
      <div class="row align-items-center h-100">
        <form class="col-lg-3 col-md-4 col-10 mx-auto text-center" novalidate action="{{ url('aksi_login') }}" method="POST" id="loginForm" onsubmit="return validateForm();">
        @csrf
        <div class="w-100 mb-4 d-flex">
 
</div>
          <h1 class="h6 mb-3">Sign in</h1>
          <div class="form-group">
            <label for="inputEmail" class="sr-only">Username</label>
            <input type="text" name="username" id="inputUsername" class="form-control form-control-lg" placeholder="Username" required="" autofocus="">
          </div>
          <div class="form-group">
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" name="password" id="inputPassword" class="form-control form-control-lg" placeholder="Password" required="">
          </div>
          <div class="checkbox mb-3">
           
          <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <!-- Online CAPTCHA -->
            <div id="recaptcha-container" class="g-recaptcha" data-sitekey="6LdFhCAqAAAAALvjUzF22OEJLDFAIsg-k7e-aBeH"></div>
            
            <!-- Offline CAPTCHA -->
            <div id="offline-captcha" style="display: none;">
              <p>Please enter the characters shown below:</p>
              <img src="{{ route('generateCaptcha') }}" alt="CAPTCHA">
              <input type="text" name="backup_captcha" class="form-control mt-2" placeholder="Enter CAPTCHA" required>
            </div>
          </div>

          <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
          <p class="mt-2">
    <a href="{{ url('register') }}">Don't have an account? Register here.</a>
</p>

          <p class="mt-5 mb-3 text-muted">© 2024/@vdarren</p>
        </form>
      </div>
    </div>

<script>
    function validateForm() {
        var response = grecaptcha.getResponse();
        var backupCaptcha = document.querySelector('input[name="backup_captcha"]').value;
        var isOffline = !navigator.onLine;

        // Validasi sesuai dengan status online atau offline
        if (isOffline) {
            if (backupCaptcha.length === 0) {
                alert('Please complete the offline CAPTCHA.');
                return false;
            }
        } else {
            if (response.length === 0) {
                alert('Please complete the online CAPTCHA.');
                return false;
            }
        }
        return true;
    }

    // Fungsi untuk mengubah tampilan CAPTCHA berdasarkan status jaringan
    function updateCaptchaDisplay() {
        var online = navigator.onLine;
        if (online) {
            document.getElementById('recaptcha-container').style.display = 'block'; 
            document.getElementById('offline-captcha').style.display = 'none';
        } else {
            document.getElementById('recaptcha-container').style.display = 'none';
            document.getElementById('offline-captcha').style.display = 'block'; // Perbaikan dari 'blovk' menjadi 'block'
        }
    }

    // Event Listener saat halaman di-load
    window.addEventListener('load', function() {
        updateCaptchaDisplay(); // Cek status jaringan saat pertama kali halaman dimuat
    });

    // Event Listener untuk mendeteksi perubahan status jaringan secara real-time
    window.addEventListener('online', updateCaptchaDisplay);
    window.addEventListener('offline', updateCaptchaDisplay);
</script>

</body>
<style>
      .logo-dashboard {
    max-width: 100%; /* Membuat gambar tidak lebih besar dari kontainer */
    height: auto; /* Mempertahankan rasio gambar */
    display: block;
}

.img-fit-menu {
    width: 200px; /* Sesuaikan ukuran yang diinginkan untuk menu */
    height: 100px; /* Sesuaikan tinggi yang diinginkan untuk menu */
    object-fit: contain; /* Memastikan gambar pas tanpa terpotong */
    margin: 0 auto; /* Center image jika diperlukan */
}

     </style>