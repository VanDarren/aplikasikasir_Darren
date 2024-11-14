<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <h2 class="h4 mb-1">Data Penjualan</h2>
                <p class="mb-4">Berikut adalah daftar penjualan yang tercatat.</p>

                <!-- Tabel penjualan -->
                <div class="card shadow">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal Penjualan</th>
                                    <th>Total Harga</th>
                                    <th>Nama Pelanggan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penjualan as $item)
                                    <tr>
                                        <td>{{ $item->tanggal_penjualan }}</td>
                                        <td>{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                        <td>{{ $item->nama_pelanggan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
