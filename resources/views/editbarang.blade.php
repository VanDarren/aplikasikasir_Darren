<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-8">
                <h2 class="h4 mb-3">Edit Barang</h2>
                <form action="{{ url('edit', $barang->id_produk) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="nama_produk">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="{{ $barang->nama_produk }}" required>
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" value="{{ $barang->harga }}" required>
                    </div>

                    <div class="form-group">
                        <label for="stok">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" value="{{ $barang->stok }}" required>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('databarang') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
