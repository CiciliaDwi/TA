<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"
    integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
                    return 'UNKNOWN ERROR';
                }
                let notaNumber = response.nota_number;
                // console.log('Generated Nota Number:', response); // Untuk debugging
                return notaNumber;
                return 'NULL'
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
            const localizeDate = now.toLocaleString('id-ID', options);
            const parseDate = localizeDate.replaceAll('.', ':').replaceAll('pukul', '|');
            transactionDate.value = parseDate
        }
    }

    // Update setiap detik
    setInterval(updateDateTime, 1000);
    // Jalankan sekali saat halaman dimuat
    updateDateTime();
</script>


<script>
    // ====== Handle Task Interval ======
    let taskInterval = null;

    function stopTaskPooling() {
        if (taskInterval) {
            clearInterval(taskInterval);
            return;
        }
    }

    // ====== Save Response Data Prediction ======
    function handleResponsePrediction(taskStatusResponse) {
        $.ajax({
            url: "{{ route('prediction.save-result') }}",
            method: "POST",
            data: {
                response: taskStatusResponse
            },
            headers: {
                "X-CSRF-TOKEN": '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#buttonSubmit').prop('disabled', true).html(
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...`
                )
            },
            success: function(response) {
                if (response.success) {
                    const message = taskStatusResponse.message;
                    const resultQty = taskStatusResponse.result.result_qty;

                    $("#loading").html(
                        `<div class="alert alert-success">${message}</div>`
                    );

                    $("#resultData").removeClass('d-none');
                    $("#resultQty").html(resultQty);

                    // $("#predictForm")[0].reset(); // reset form

                    stopTaskPooling(); // stop loop checking
                    $('#buttonSubmit').prop('disabled', false).html(
                        '<i class="fas fa-bolt me-1"></i> Prediksi Jumlah Unit'
                    )
                }
            },
            error: function() {
                stopTaskPooling();
                $('#buttonSubmit').prop('disabled', false).html(
                    '<i class="fas fa-bolt me-1"></i> Prediksi Jumlah Unit'
                )

                $("#loading").html(
                    `<div class="alert alert-danger">Terjadi kesalahan saat proses prediksi.</div>`
                );
            },
        })
    }

    // ====== Handle Pooling - Download Report ======
    function poolDownloadReport(payload) {
        const {
            event,
            taskId,
            taskCategory
        } = payload;

        event.preventDefault();

        $.ajax({
            url: `/reports/pooling/download/${taskId}/${taskCategory}`,
            method: "GET",
            // signal: controller.signal,
            success: function(response) {
                const {
                    success,
                    data,
                    message
                } = response;

                console.log("Polling:", response);

                if (success && data) {
                    $('#responseMessage').html(
                        `<div class="alert alert-success">${message}</div>`
                    );

                    window.location.href = data.filepath;
                    stopTaskPooling(); // stop loop checking when data not null
                    return;
                }

                $('#responseMessage').html(
                    `<div class="alert alert-warning">${message}</div>`
                );
                stopTaskPooling();
            },
            error: function(xhr) {
                const {
                    status,
                    responseJSON: response
                } = xhr;
                console.error(xhr)
                console.log(response)
                stopTaskPooling();
            }
        });
    }
</script>
