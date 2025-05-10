<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Proses Leger</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include jQuery BEFORE your script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .progress {
            height: 25px;
        }
        .progress-bar {
            transition: width 0.5s ease;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Proses Leger</div>

                <div class="card-body">
                    <form id="processLegerForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="teacher_subject_id">Pilih Teacher Subject</label>
                            <select class="form-control" id="teacher_subject_id" name="teacher_subject_id" required>
                                <option value="">-- Pilih Teacher Subject --</option>
                                @foreach($teacherSubjects as $teacherSubject)
                                    <option value="{{ $teacherSubject['id'] }}">{{ $teacherSubject['text'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="time_signature">Time Signature (Opsional)</label>
                            <input type="datetime-local" class="form-control" id="time_signature" name="time_signature" value="{{ now()->format('Y-m-d\TH:i') }}">
                            <small class="form-text text-muted">Jika kosong, akan menggunakan waktu saat ini</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Proses Leger</button>
                        </div>
                    </form>

                    <!-- Progress Bar Container (awalnya tersembunyi) -->
                    <div class="mt-4" id="progressContainer" style="display: none;">
                        <h5>Sedang Memproses Data...</h5>
                        <div class="progress">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">0%</div>
                        </div>
                        <p id="progressStatus" class="text-center mt-2">Menginisialisasi proses...</p>
                    </div>

                    <div class="mt-4" id="result" style="display: none;">
                        <div class="alert" id="resultAlert" role="alert"></div>
                        <div id="resultDetails"></div>
                        
                        <!-- Link ke Halaman Leger Print -->
                        <div id="legerPrintLink" class="mt-3" style="display: none;">
                            <a id="viewLegerBtn" href="#" class="btn btn-success" target="_blank">
                                <i class="fas fa-print"></i> Lihat Hasil Leger
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Include Font Awesome untuk ikon -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<!-- Your script -->
<script>
    $(document).ready(function() {
        // Set nilai default untuk time_signature jika belum diisi
        if (!$('#time_signature').val()) {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            $('#time_signature').val(formattedDateTime);
        }
        
        $('#processLegerForm').on('submit', function(e) {
            e.preventDefault();
            
            // Format time_signature sebelum submit jika ada nilai
            const timeSignature = $('#time_signature').val();
            if (timeSignature) {
                // Tidak perlu melakukan konversi di sini, akan ditangani di controller
            }
            
            const submitBtn = $('#submitBtn');
            submitBtn.prop('disabled', true);
            
            const resultDiv = $('#result');
            const resultAlert = $('#resultAlert');
            const resultDetails = $('#resultDetails');
            const legerPrintLink = $('#legerPrintLink');
            
            // Sembunyikan hasil sebelumnya jika ada
            resultDiv.hide();
            legerPrintLink.hide();
            
            // Tampilkan progress bar
            const progressContainer = $('#progressContainer');
            const progressBar = $('#progressBar');
            const progressStatus = $('#progressStatus');
            
            progressContainer.show();
            
            // Simulasi progress bar (karena tidak ada cara untuk mengetahui progress sebenarnya dari backend)
            let progress = 0;
            const progressInterval = setInterval(function() {
                if (progress < 90) {  // Hanya sampai 90% untuk simulasi
                    progress += Math.floor(Math.random() * 10) + 1;  // Tambah 1-10% secara acak
                    if (progress > 90) progress = 90;
                    
                    progressBar.css('width', progress + '%');
                    progressBar.attr('aria-valuenow', progress);
                    progressBar.text(progress + '%');
                    
                    // Update status berdasarkan progress
                    if (progress < 30) {
                        progressStatus.text('Mengambil data teacher subject...');
                    } else if (progress < 60) {
                        progressStatus.text('Memproses data semester penuh...');
                    } else if (progress < 90) {
                        progressStatus.text('Memproses data tengah semester...');
                    }
                }
            }, 500);
            
            $.ajax({
                url: '{{ route("leger.process") }}',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Hentikan interval progress
                    clearInterval(progressInterval);
                    
                    // Set progress ke 100% untuk menunjukkan proses selesai
                    progressBar.css('width', '100%');
                    progressBar.attr('aria-valuenow', 100);
                    progressBar.text('100%');
                    progressStatus.text('Proses selesai!');
                    
                    // Tunggu sebentar untuk menampilkan 100% sebelum menyembunyikan progress bar
                    setTimeout(function() {
                        progressContainer.hide();
                        
                        // Tampilkan hasil
                        resultDiv.show();
                        resultAlert.removeClass('alert-danger').addClass('alert-success');
                        resultAlert.text(response.message);
                        
                        let detailsHtml = '<h5>Detail Proses:</h5>';
                        detailsHtml += '<ul>';
                        detailsHtml += `<li>Teacher Subject ID: ${response.data.teacher_subject_id}</li>`;
                        detailsHtml += `<li>Time Signature: ${response.data.time_signature}</li>`;
                        detailsHtml += `<li>Jumlah Data Semester Penuh: ${response.data.full_semester_count}</li>`;
                        detailsHtml += `<li>Jumlah Data Tengah Semester: ${response.data.half_semester_count}</li>`;
                        detailsHtml += '</ul>';
                        
                        resultDetails.html(detailsHtml);
                        
                        // Tampilkan link ke halaman leger-print
                        legerPrintLink.show();
                        
                        // Set URL untuk tombol lihat leger
                        const teacherSubjectId = response.data.teacher_subject_id;
                        $('#viewLegerBtn').attr('href', `/${teacherSubjectId}/leger-print`);
                    }, 1000);
                },
                error: function(xhr) {
                    // Hentikan interval progress
                    clearInterval(progressInterval);
                    
                    // Set progress bar ke merah untuk menunjukkan error
                    progressBar.removeClass('progress-bar-animated progress-bar-striped').addClass('bg-danger');
                    progressStatus.text('Terjadi kesalahan!');
                    
                    // Tunggu sebentar sebelum menyembunyikan progress bar
                    setTimeout(function() {
                        progressContainer.hide();
                        
                        // Tampilkan pesan error
                        resultDiv.show();
                        resultAlert.removeClass('alert-success').addClass('alert-danger');
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            resultAlert.text(xhr.responseJSON.message);
                        } else {
                            resultAlert.text('Terjadi kesalahan saat memproses data.');
                        }
                        
                        resultDetails.html('');
                        
                        // Sembunyikan link leger-print jika terjadi error
                        legerPrintLink.hide();
                    }, 1000);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text('Proses Leger');
                }
            });
        });
    });
</script>
</body>
</html>