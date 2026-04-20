<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SIKAR</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo_pge.png') }}">
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
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-pertaminaSoft to-white px-4 font-sans">

<div class="w-full max-w-[420px]">

  <!-- LOGO & TITLE -->
  <div class="text-center mb-8">
    <img src="{{ asset('images/logo_pge.png') }}" alt="PGE"
         class="mx-auto h-20 object-contain mb-4">
    <h1 class="text-3xl font-extrabold text-gray-800">
      Selamat Datang di <span class="text-pertamina">SIKAR</span>
    </h1>
    <p class="text-sm text-gray-500 mt-2">
      Sistem Informasi Kendaraan
    </p>
  </div>

  <!-- CARD -->
  <div class="bg-white/95 backdrop-blur rounded-2xl p-8
              border border-gray-100
              shadow-xl shadow-black/10
              hover:shadow-2xl hover:shadow-black/15
              transition-all duration-300">

    <!-- FORM -->
    <form id="loginForm" class="space-y-5">
      @csrf

      <!-- NAMA -->
      <div>
        <label class="block text-sm font-semibold mb-2 text-gray-700">Nama</label>
        <input type="text" id="name" name="name" required
               placeholder="Masukkan nama"
               class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-300
                      focus:bg-white focus:border-pertamina focus:ring-4 focus:ring-pertamina/20
                      transition-all outline-none"
               oninvalid="this.setCustomValidity('Nama wajib diisi!')"
               oninput="this.setCustomValidity('')">
      </div>

      <!-- TOKEN -->
      <div>
        <label class="block text-sm font-semibold mb-2 text-gray-700">Token</label>

        <div class="relative">
          <input type="password" id="token" name="token" required
                placeholder="Masukkan token"
                class="w-full px-4 py-3 pr-12 rounded-xl bg-gray-50 border border-gray-300
                        focus:bg-white focus:border-pertamina focus:ring-4 focus:ring-pertamina/20
                        transition-all outline-none"
                oninvalid="this.setCustomValidity('Token wajib diisi!')"
                oninput="this.setCustomValidity('')">

          <!-- BUTTON EYE -->
          <button type="button"
                  onclick="toggleToken()"
                  class="absolute inset-y-0 right-3 flex items-center text-gray-500">
            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5
                      c4.478 0 8.268 2.943 9.542 7
                      -1.274 4.057-5.064 7-9.542 7
                      -4.477 0-8.268-2.943-9.542-7z"/>
            </svg>

            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19
                      c-4.478 0-8.268-2.943-9.542-7
                      a9.956 9.956 0 012.042-3.368"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6.223 6.223A9.956 9.956 0 0112 5
                      c4.478 0 8.268 2.943 9.542 7
                      a9.964 9.964 0 01-4.304 5.568"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3l18 18"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- BUTTON -->
      <button type="submit"
          class="btn-login w-full py-3 rounded-xl
                 bg-gradient-to-r from-pertamina to-pertaminaDark
                 text-white font-bold tracking-wide
                 shadow-lg shadow-pertamina/30
                 hover:shadow-xl hover:opacity-95
                 active:scale-[0.98]
                 disabled:opacity-60 disabled:cursor-not-allowed
                 transition-all">
          Masuk
      </button>
    </form>

    <!-- HELP -->
    <div class="text-center text-xs mt-8">
      <p class="text-gray-400 mb-1">Lupa atau belum memiliki token?</p>
      <a href="#" onclick="hubungiAdmin(event)"
         class="font-semibold text-pertamina hover:underline">
        Hubungi Admin
      </a>
    </div>

  </div>
</div>

<!-- SCRIPT -->
<script>
document.getElementById("loginForm").addEventListener("submit", async function(e) {
  e.preventDefault();

  const btn = document.querySelector(".btn-login");
  btn.disabled = true;
  btn.innerHTML = `
    <span class="flex items-center justify-center gap-2">
      <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4l-3 3 3 3h-4z"></path>
      </svg>
      Memproses...
    </span>
  `;

  const name = document.getElementById("name").value;
  const token = document.getElementById("token").value;
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

 try {
    const response = await fetch("{{ route('login.post') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",  // Ubah ke JSON
        "X-CSRF-TOKEN": csrfToken,
        "Accept": "application/json"
      },
      body: JSON.stringify({name, token}),  // Kirim sebagai JSON
      credentials: "same-origin"
    });

    const data = await response.json();

    if (data.status) {
      
      localStorage.setItem('sikar_token', data.data.token);
      localStorage.setItem('sikar_role', data.data.role);
      // Redirect langsung tanpa token di URL
      if (data.data.role === 'penumpang') {
        window.location.href = `/penumpang/pemesanan`;
      } else if (data.data.role === 'sopir') {
        window.location.href = `/sopir/pesanan`;
      } else if (data.data.role === 'kepala_sopir') {
        window.location.href = `/kepalasopir/pesanan`;
      }
    } else {
      showPopup("Gagal", data.message);
    }

  } catch (err) {
    console.error('Error detail:', err);
    showPopup("Error", "Terjadi kesalahan: " + err.message);
  } finally {
    btn.disabled = false;
    btn.textContent = "Masuk";
  }
});

function hubungiAdmin(e) {
  e.preventDefault();
  const nama = document.getElementById("name").value || "(Belum diisi)";
  const pesan = `Halo admin SIKAR,\n\nNama: ${nama}\nKendala: Lupa atau belum memiliki token\n\nTerima kasih.`;
  window.open(`https://wa.me/6282295622004?text=${encodeURIComponent(pesan)}`, "_blank");
}

function showPopup(title, message) {
  document.getElementById("popupTitle").textContent = title;
  document.getElementById("popupMessage").textContent = message;
  document.getElementById("popup").classList.remove("hidden");
}

function closePopup() {
  document.getElementById("popup").classList.add("hidden");
}

function toggleToken() {
  const tokenInput = document.getElementById("token");
  const eyeOpen = document.getElementById("eyeOpen");
  const eyeClosed = document.getElementById("eyeClosed");

  if (tokenInput.type === "password") {
    tokenInput.type = "text";
    eyeOpen.classList.remove("hidden");
    eyeClosed.classList.add("hidden");
  } else {
    tokenInput.type = "password";
    eyeOpen.classList.add("hidden");
    eyeClosed.classList.remove("hidden");
  }
}
</script>

<!-- POPUP -->
<div id="popup"
     class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 hidden">
  <div class="bg-white rounded-2xl shadow-2xl p-6 w-80 text-center">
    <h2 id="popupTitle" class="text-sm font-bold text-red-600 mb-2"></h2>
    <p id="popupMessage" class="text-xs text-gray-600 mb-4"></p>
    <button onclick="closePopup()"
            class="px-4 py-2 bg-pertamina text-white rounded-xl font-semibold hover:opacity-90">
      OK
    </button>
  </div>
</div>

</body>
</html>