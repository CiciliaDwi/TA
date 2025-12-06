<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
    crossorigin="anonymous"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Fungsi untuk inisialisasi Select2
        function initializeSelect2() {
            $('.product-select').select2({
                placeholder: 'Pilih Produk',
                width: '100%',
                allowClear: true
            });
        }

        initializeSelect2();

        $('#add_product').off('click').on('click', function() {
            const tbody = $('#products_table tbody');
            const originalRow = tbody.find('tr:first');

            originalRow.find('.product-select').select2('destroy');

            const newRow = originalRow.clone(true);

            newRow.find('input').val('');
            // newRow.find('select')
            //     .val('')
            //     .removeClass('select2-hidden-accessible');

            // Hapus container Select2 yang lama
            //newRow.find('.select2-container').remove();

            // Tambahkan baris baru
            tbody.append(newRow);

            // Reinisialisasi Select2 untuk semua dropdown
            initializeSelect2();
        });
    });
    $(document).ready(function() {
        async function generateNoNota() {
            try {
                const response = await $.get('{{ route('get-last-nota-number') }}');
                if (response.error) {
                    console.error('Error:', response.error);
                    return '';
                }

                let nextNumber = response.last_number;

                let date = new Date();
                let year = date.getFullYear().toString().substr(-2);
                let month = (date.getMonth() + 1).toString().padStart(2, '0');
                let day = date.getDate().toString().padStart(2, '0');

                // Format nomor dengan padding 3 digit
                let sequence = nextNumber.toString().padStart(3, '0');

                let notaNumber = `NJ${year}${month}${day}${sequence}`;
                console.log('Generated Nota Number:', notaNumber); // Untuk debugging
                return notaNumber;
            } catch (error) {
                console.error('Error generating nota number:', error);
                return '';
            }
        }

        // Set nomor nota saat halaman dimuat
        generateNoNota().then(notaNumber => {
            $('#no_nota').val(notaNumber);
        });

        // Fungsi untuk menghitung subtotal
        function calculateSubtotal(row) {
            const price = parseFloat(row.find('.product-price').val()) || 0;
            const quantity = parseFloat(row.find('.product-quantity').val()) || 0;
            const subtotal = price * quantity;
            row.find('.product-subtotal').val(subtotal);
            calculateTotal();
        }

        // Fungsi untuk menghitung total
        function calculateTotal() {
            let total = 0;
            $('.product-subtotal').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total_amount').val(total);
        }

        // Event ketika produk dipilih
        $(document).on('change', '.product-select', function() {
            const row = $(this).closest('tr');
            const selectedOption = $(this).find(':selected');

            // Ambil data dari option yang dipilih
            const kodeBarang = selectedOption.val();
            const price = parseFloat(selectedOption.data('price')) || 0;
            const barcode = selectedOption.data('barcode') || '';

            // Isi semua field yang diperlukan
            row.find('.product-code').val(kodeBarang);
            row.find('.product-barcode').val(barcode);
            row.find('.product-price').val(price);
            row.find('.product-price-hidden').val(price);

            // Reset quantity dan recalculate subtotal
            row.find('.product-quantity').val('');
            row.find('.product-subtotal').val('');
            calculateTotal();
        });

        // Event ketika jumlah diubah
        $(document).on('change', '.product-quantity', function() {
            const row = $(this).closest('tr');
            calculateSubtotal(row);
        });

        // Hapus baris produk
        $(document).on('click', '.remove-product', function() {
            if ($('#products_table tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotal();
            }
        });

        // Tambahkan tombol untuk reset form dan generate nomor nota baru
        $('#reset-form').on('click', function() {
            // Reset form
            $('form')[0].reset();

            // Hapus semua produk kecuali yang pertama
            const firstRow = $('tbody tr:first');
            $('tbody tr:not(:first)').remove();

            // Reset nilai di baris pertama
            firstRow.find('.product-select').val('');
            firstRow.find('.product-code').val('');
            firstRow.find('.product-barcode').val('');
            firstRow.find('.product-price').val('');
            firstRow.find('.product-quantity').val('');
            firstRow.find('.product-subtotal').val('');

            // Generate nomor nota baru
            generateNoNota().then(notaNumber => {
                $('#no_nota').val(notaNumber);
            });

            // Reset total
            $('#total_amount').val('0');
        });
    });

    function updateDateTime() {
        var now = new Date();
        var options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };

        const transactionDate = document.getElementById('transaction_date')
        
        if (transactionDate) {
            transactionDate.value = now.toLocaleString('id-ID', options);
        }
    }

    // Update setiap detik
    setInterval(updateDateTime, 1000);
    // Jalankan sekali saat halaman dimuat
    updateDateTime();

    function calculateMonthlyIncome(selectedMonth) {
        // Mengambil semua transaksi dari tabel
        const table = document.getElementById('datatablesSimple');
        const rows = table.getElementsByTagName('tr');
        let totalIncome = 0;

        // Mengubah format bulan yang dipilih menjadi format yang sesuai untuk perbandingan
        const selectedDate = new Date(selectedMonth);
        const selectedYear = selectedDate.getFullYear();
        const selectedMonthIndex = selectedDate.getMonth();

        // Loop melalui setiap baris transaksi
        for (let i = 1; i < rows.length; i++) { // Mulai dari 1 untuk melewati header
            const dateCell = rows[i].cells[1].innerText; // Mengambil kolom tanggal
            const amountText = rows[i].cells[4].innerText; // Mengambil kolom total harga

            // Mengubah format tanggal transaksi
            const [day, month, yearTime] = dateCell.split('/');
            const [year, time] = yearTime.split(' ');
            const transactionDate = new Date(year, month - 1, day);

            // Cek apakah transaksi ada di bulan yang dipilih
            if (transactionDate.getFullYear() === selectedYear &&
                transactionDate.getMonth() === selectedMonthIndex) {
                // Mengubah format harga dari "Rp XX.XXX" menjadi angka
                const amount = parseInt(amountText.replace(/[^0-9]/g, ''));
                totalIncome += amount;
            }
        }

        // Memperbarui tampilan total pendapatan
        document.getElementById('monthlyIncome').innerHTML =
            'Rp ' + totalIncome.toLocaleString('id-ID');
    }
</script>
