<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Belajar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .footer {
                position: absolute;
                bottom: 0;
                width: 100%;
                text-align: center;
            }

            .content {
                min-height: calc(100vh - 120px);
                /* Adjust space for footer */
            }
        }
    </style>
</head>

<body class="bg-white text-gray-900 font-sans">
    <div class="flex flex-col items-center py-10">
        <!-- Header -->
        <div class="text-center mb-20">
            <h1 class="text-4xl font-bold uppercase">Laporan Hasil Belajar</h1>
            <h2 class="text-2xl font-semibold">Madrasah Ibtida'iyah Ar Ridlo</h2>
        </div>

        <!-- Logo -->
        <div class="mb-40">
            <img src="https://picsum.photos/seed/picsum/200" alt="Logo" class="w-32 h-32 mx-auto rounded-full">
        </div>

        <!-- Student Details -->
        <div class="w-full max-w-md border border-gray-800 rounded-lg shadow-lg">
            <div class="p-4 text-center">
                <p class="font-semibold text-lg uppercase">Nama Peserta Didik</p>
                <p class="text-gray-700 font-bold">Abdullah Azzam Basyir</p>
            </div>
            <div class="border-t border-gray-800 p-4 text-center">
                <p class="font-semibold text-lg uppercase">NIS</p>
                <p class="text-gray-700 font-bold">230001</p>
            </div>
            <div class="border-t border-gray-800 p-4 text-center">
                <p class="font-semibold text-lg uppercase">NISN</p>
                <p class="text-gray-700 font-bold">3166534301</p>
            </div>
        </div>


        <!-- Footer -->
        <div class="mt-12 text-center text-sm text-gray-600 footer">
            <p class="font-semibold">Yayasan AR Ridlo Indragiri Malang</p>
            <p class="font-medium">Madrasah Ibtida'iyah Ar Ridlo Malang</p>
            <p class="mt-1">NSM: 111235730053 | NPSN: 69983045</p>
            <p>Jalan Tumenggung Suryo 31A, Kel. Purwantoro, Kec. Blimbing</p>
            <p>Kota Malang</p>
        </div>
    </div>
</body>

</html>
