<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'SIKAR')</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo_pge.png') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        pertamina: '#D32F2F',
        pertaminaSoft: '#FDECEC',
        pertaminaDark: '#B71C1C'
      }
    }
  }
}
</script>
@stack('styles')
</head>

<body class="min-h-screen bg-gradient-to-br from-pertaminaSoft to-white text-gray-800">

@php
$user = request()->auth_user ?? null;
$initial = $user ? strtoupper(substr($user->name, 0, 1)) : 'U';

$menus = [];
if ($user) {
    if ($user->role === 'penumpang') {
        $menus = [
            ['label' => 'Pemesanan', 'route' => 'penumpang.pemesanan'],
            ['label' => 'Pemantauan', 'route' => 'penumpang.pemantauan'],
            ['label' => 'Riwayat', 'route' => 'penumpang.riwayat'],
            ['label' => 'Ketersediaan', 'route' => 'penumpang.ketersediaan'],
        ];
    } elseif ($user->role === 'sopir') {
        $menus = [
            ['label' => 'Pesanan', 'route' => 'sopir.pesanan'],
            ['label' => 'Riwayat', 'route' => 'sopir.riwayat'],
            ['label' => 'Peringkat', 'route' => 'sopir.peringkat'],
        ];
    } elseif ($user->role === 'kepala_sopir') {
        $menus = [
            ['label' => 'Pesanan', 'route' => 'kepalasopir.pesanan'],
            ['label' => 'Riwayat', 'route' => 'kepalasopir.riwayat'],
            ['label' => 'Ketersediaan', 'route' => 'kepalasopir.ketersediaan'],
            ['label' => 'Peringkat', 'route' => 'kepalasopir.peringkat'],
            ['label' => 'Presensi', 'route' => 'kepalasopir.presensi'],
        ];
    }
}
@endphp

<!-- ================= HEADER ================= -->
<header class="bg-white/95 backdrop-blur border-b sticky top-0 z-50">
  <div class="max-w-[1200px] mx-auto px-4 h-16 flex items-center justify-between">

    <!-- LEFT -->
    <div class="flex items-center gap-3">
      <!-- Hamburger -->
      <button class="md:hidden text-base text-gray-500" onclick="toggleMenu()">☰</button>
      <span class="text-lg font-bold text-pertamina">SIKAR</span>
    </div>

    <!-- PROFILE -->
    <div class="relative">
      <button onclick="toggleProfile()" class="w-7 h-7 md:w-8 md:h-8 rounded-full rounded-full bg-pertaminaSoft border border-pertamina text-xs md:text-sm text-pertamina font-semibold flex items-center justify-center">
        {{ $initial }}
      </button>

      <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-56 md:w-[260px] bg-white border rounded-xl shadow-lg p-3 md:p-4 text-sm">
        <p class="font-semibold text-sm md:text-base">{{ $user->name ?? '-' }}</p>
        <p class="font-semibold text-sm md:text-base mb-3">{{ $user->token ?? '-' }}</p>
        <p class="text-xs md:text-sm text-gray-500">{{ $user->nomor ?? '-' }}</p>
        <p class="text-xs md:text-sm text-gray-500 mb-3">
          {{ ucfirst(str_replace('_',' ',$user->role ?? '-')) }}
        </p>
        <button onclick="logout()" class="w-full bg-pertamina text-white py-1.5 md:py-2 rounded-lg hover:bg-pertaminaDark">
          Keluar
        </button>
      </div>
    </div>

  </div>

  <!-- ================= MENU DESKTOP ================= -->
  <div class="hidden md:block bg-white/95 border-b">
    <div class="max-w-[1200px] mx-auto px-4">
      <nav class="flex gap-6 text-sm font-medium">
        @foreach ($menus as $menu)
          <a href="{{ route($menu['route']) }}"
            class="py-3 border-b-2 {{ request()->routeIs($menu['route']) ? 'border-pertamina text-pertamina' : 'border-transparent text-gray-600 hover:text-pertamina' }}">
            {{ $menu['label'] }}
          </a>
        @endforeach
      </nav>
    </div>
  </div>
</header>

<!-- ================= MENU MOBILE ================= -->
<div id="mobileMenu" class="hidden absolute top-16 left-0 right-0 md:hidden bg-white border-b shadow z-40">
  <nav class="flex flex-col px-4 py-2 text-sm font-medium">
    @foreach ($menus as $menu)
      <a href="{{ route($menu['route']) }}" class="py-3 border-b text-gray-700 hover:text-pertamina">
        {{ $menu['label'] }}
      </a>
    @endforeach
  </nav>
</div>

<!-- ================= MAIN CONTENT ================= -->
<main class="max-w-[1200px] mx-auto px-4 sm:px-6 py-4 sm:py-6">
  <div class="w-full overflow-x-auto">
    @yield('content')
  </div>
</main>

<!-- ================= SCRIPT ================= -->
<script>
function toggleMenu() {
  document.getElementById('mobileMenu').classList.toggle('hidden');
  document.getElementById('profileDropdown').classList.add('hidden')
}

function toggleProfile() {
  document.getElementById('profileDropdown').classList.toggle('hidden');
  document.getElementById('mobileMenu').classList.add('hidden');
}

document.addEventListener('click', function(e) {
  const dropdown = document.getElementById('profileDropdown');
  const btn = e.target.closest('button[onclick="toggleProfile()"]');
  if (!btn && dropdown && !dropdown.contains(e.target)) {
    dropdown.classList.add('hidden');
  }
});

// Logout
async function logout() {
  const token = document.querySelector('meta[name="csrf-token"]').content;
  try {
    await fetch("{{ route('logout') }}", {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": token,
        "Content-Type": "application/json"
      },
      credentials: "same-origin"
    });
  } catch (e) {}
  window.location.href = "/";
}
</script>
@stack('scripts')
</body>
</html>