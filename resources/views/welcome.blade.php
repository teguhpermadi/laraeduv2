<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>E-Rapor MI AR RIDLO</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50">

        <!-- Halaman Pembuka -->
        <div class="min-h-screen flex flex-col justify-center bg-cover bg-center" style="background-image: url('https://plus.unsplash.com/premium_photo-1673002094195-f18084be89ce?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');">
            <div class="bg-black bg-opacity-50 text-white p-6 sm:p-12 rounded-xl text-center mx-4">
                <!-- Logo di atas Judul -->
            <div class="mb-6">
                <img src="https://miarridlo.sch.id/wp-content/uploads/2024/01/logo-mi-ar-ridlo-lingkaran-putih.png" alt="Logo MI Ar Ridlo" class="mx-auto w-24 sm:w-32">
            </div>
            
                <h1 class="text-4xl font-bold mb-4">E-Rapor MI Ar Ridlo Kota Malang</h1>
                <p class="text-xl mb-6">Platform untuk memudahkan pencatatan dan pelaporan nilai rapor bagi siswa MI Ar Ridlo.</p>
                <a href="{{route('filament.admin.auth.login')}}" class="bg-blue-600 hover:bg-blue-700 text-white text-lg py-2 px-6 rounded-full transition ease-in-out duration-300">
                    Login
                </a>
            </div>
        </div>
    
    </body>
</html>
