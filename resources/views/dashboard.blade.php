<main role="main" class="main-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="row align-items-center mb-2">
                    <div class="col">
                        <h2 class="h5 page-title">Selamat Datang, {{ $username }}!</h2>
                    </div>
                </div>

                <!-- Kasir System Section -->
                <div class="row">
                    <!-- Left Section: Product Input -->
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="h6 mb-4">Input Produk</h5>
                                <form id="kasirForm" action="/save-transaction" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="id_pelanggan">Pilih Nama Pelanggan</label>
                                        <select name="id_pelanggan" id="id_pelanggan" class="form-control" required>
                                            <option value="" disabled selected>Pilih Pelanggan</option>
                                            @foreach($pelanggan as $cust)
                                                <option value="{{ $cust->id_pelanggan }}">{{ $cust->nama_pelanggan }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="id_produk">Pilih Produk</label>
                                        <select name="id_produk" id="id_produk" class="form-control" required>
                                            <option value="" disabled selected>Pilih Produk</option>
                                            @foreach($produk as $item)
                                                <option value="{{ $item->id_produk }}" data-harga="{{ $item->harga }}">
                                                    {{ $item->nama_produk }} (Rp {{ number_format($item->harga, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="quantity">Jumlah</label>
                                        <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="total">Total Harga</label>
                                        <input type="text" id="total" class="form-control" readonly>
                                    </div>

                                    <button type="button" class="btn btn-success btn-sm" id="addItemButton">Tambah ke Keranjang</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Kasir Receipt Style -->
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="h6 mb-4">Nota Kasir</h5>
                                <div>
                                    <strong>Nama Pelanggan:</strong> <span id="pelangganNota"></span>
                                </div>

                                <form id="notaForm" action="/save-transaction" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_pelanggan" id="idPelangganHidden">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="notaTable">
                                            <thead>
                                                <tr>
                                                    <th>Nama Produk</th>
                                                    <th>Harga</th>
                                                    <th>Jumlah</th>
                                                    <th>Total</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="notaBody">
                                                <!-- Items will be added here dynamically -->
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <span><strong>Total Pembayaran:</strong></span>
                                        <span id="grandTotal">Rp 0</span>
                                        <input type="hidden" name="grand_total" id="grandTotalInput">
                                    </div>

                                    <div class="mt-3 text-center">
                                        <button type="submit" class="btn btn-primary btn-sm">Selesaikan Transaksi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        let cartItems = [];
        let grandTotal = 0;
        let itemIdCounter = 0;

        document.getElementById('id_produk').addEventListener('change', function () {
            let harga = parseInt(this.options[this.selectedIndex].getAttribute('data-harga'));
            let quantityField = document.getElementById('quantity');
            quantityField.addEventListener('input', function () {
                let quantity = parseInt(this.value);
                let total = harga * quantity;
                document.getElementById('total').value = 'Rp ' + new Intl.NumberFormat().format(total);
            });
        });

        document.getElementById('addItemButton').addEventListener('click', function () {
            let produkId = document.getElementById('id_produk').value;
            let produkNama = document.getElementById('id_produk').options[document.getElementById('id_produk').selectedIndex].textContent.split('(')[0].trim();
            let harga = document.getElementById('id_produk').selectedOptions[0].getAttribute('data-harga');
            let quantity = document.getElementById('quantity').value;
            let totalHarga = parseInt(harga) * parseInt(quantity);

            if (!produkId || !quantity || quantity <= 0) {
                alert('Harap pilih produk dan masukkan jumlah yang valid.');
                return;
            }

            cartItems.push({
                id: itemIdCounter++,
                produkId,
                produkNama,
                harga: parseInt(harga),
                quantity: parseInt(quantity),
                totalHarga
            });

            updateCartTable();
            document.getElementById('id_produk').value = '';
            document.getElementById('quantity').value = '';
            document.getElementById('total').value = '';
        });

        function updateCartTable() {
    let notaBody = document.getElementById('notaBody');
    notaBody.innerHTML = '';
    grandTotal = 0;

    cartItems.forEach(item => {
        grandTotal += item.totalHarga;

        let row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="hidden" name="items[${item.id}][id_produk]" value="${item.produkId}">${item.produkNama}</td>
            <td>Rp ${new Intl.NumberFormat().format(item.harga)}</td>
            <td><input type="hidden" name="items[${item.id}][quantity]" value="${item.quantity}">${item.quantity}</td>
            <td><input type="hidden" name="items[${item.id}][harga]" value="${item.harga}">Rp ${new Intl.NumberFormat().format(item.totalHarga)}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${item.id})">Hapus</button>
            </td>
        `;
        notaBody.appendChild(row);
    });

    document.getElementById('grandTotal').textContent = 'Rp ' + new Intl.NumberFormat().format(grandTotal);
    document.getElementById('grandTotalInput').value = grandTotal;
}


        function removeItem(itemId) {
            cartItems = cartItems.filter(item => item.id !== itemId);
            updateCartTable();
        }

        document.getElementById('id_pelanggan').addEventListener('change', function () {
            const pelangganNota = document.getElementById('pelangganNota');
            pelangganNota.textContent = this.options[this.selectedIndex].textContent;
            document.getElementById('idPelangganHidden').value = this.value;
        });
    </script>
</main>
