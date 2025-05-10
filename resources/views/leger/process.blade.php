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
    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <style>
        .progress {
            height: 25px;
        }
        .progress-bar {
            transition: width 0.5s ease;
        }
        .container-fluid {
            padding: 20px;
        }
        .table-responsive {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Form Pemilihan Tahun Akademik -->
            <div class="card mb-4">
                <div class="card-header">Pilih Tahun Akademik</div>
                <div class="card-body">
                    <form id="academicYearForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="academic_year_id">Tahun Akademik</label>
                            <select class="form-control" id="academic_year_id" name="academic_year_id" required>
                                <option value="">-- Pilih Tahun Akademik --</option>
                                @foreach(\App\Models\AcademicYear::all() as $academicYear)
                                    <option value="{{ $academicYear->id }}" {{ session('academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                                        {{ $academicYear->year }} - {{ $academicYear->semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success" id="setAcademicYearBtn">Set Tahun Akademik</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Form Proses Leger -->
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
            
            <!-- Tabel Teacher Subject dengan Leger -->
            <div class="card mt-4">
                <div class="card-header">Daftar Teacher Subject dengan Leger</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="legerTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Guru</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Tahun Akademik</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teacherSubjectsWithLeger as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->teacher->name }}</td>
                                        <td>{{ $item->subject->name }}</td>
                                        <td>{{ $item->grade->name }}</td>
                                        <td>{{ $item->academic->year }} {{ $item->academic->semester }}</td>
                                        <td>
                                            <a href="{{ route('leger-print', $item->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fas fa-print"></i> Lihat Leger
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data leger yang diproses</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
        // Inisialisasi DataTable
        $('#legerTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            columnDefs: [
                { orderable: false, targets: 5 }, // Kolom aksi tidak bisa diurutkan
                { width: '5%', targets: 0 }, // Kolom nomor
                { width: '25%', targets: 1 }, // Kolom nama guru
                { width: '20%', targets: 2 }, // Kolom mata pelajaran
                { width: '20%', targets: 3 }, // Kolom kelas
                { width: '15%', targets: 4 }, // Kolom tahun akademik
                { width: '15%', targets: 5 }  // Kolom aksi
            ]
        });
        
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
        
        // Handler untuk form academic year
        $('#academicYearForm').on('submit', function(e) {
            e.preventDefault();
            
            const academicYearId = $('#academic_year_id').val();
            if (!academicYearId) {
                alert('Silakan pilih Tahun Akademik terlebih dahulu!');
                return;
            }
            
            // Kirim data ke server untuk menyimpan ke session
            $.ajax({
                url: '{{ route("leger.set-academic-year") }}',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Tampilkan notifikasi sukses
                        alert(response.message);
                        
                        // Reload halaman untuk memperbarui data
                        location.reload();
                    } else {
                        alert('Terjadi kesalahan: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menyimpan data.');
                }
            });
        });
        
        // Handler untuk form process leger
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
                        
                        // Reload halaman setelah 3 detik untuk memperbarui tabel
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
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