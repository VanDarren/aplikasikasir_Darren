<!-- resources/views/register.blade.php -->
<body class="light">
    <div class="wrapper vh-100">
        <div class="row align-items-center h-100">
            <form class="col-lg-3 col-md-4 col-10 mx-auto text-center" action="{{ url('aksi_register') }}" method="POST">
                @csrf
                <div class="w-100 mb-4 d-flex">
                <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="./index.html">
        <img src="{{ asset('img/' . htmlspecialchars($darren2->iconlogin)) }}" alt="Iconlogin" class="logo-dashboard img-fit-menu">
    </a>
                </div>
                <h1 class="h6 mb-3">Register</h1>
                <div class="form-group">
                    <input type="text" name="username" class="form-control form-control-lg" placeholder="Username" required autofocus>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" class="form-control form-control-lg" placeholder="Confirm Password" required>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
                <p class="mt-5 mb-3 text-muted">© 2024/@vdarren</p>
                <p>Already have an account? <a href="{{ url('login') }}">Login here</a>.</p>
            </form>
        </div>
    </div>
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