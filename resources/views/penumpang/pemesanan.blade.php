@extends('layouts.app')

@section('title', 'Pemesanan Kendaraan')

@section('content')

<!-- ================= MODAL POPUP PERINGATAN ================= -->
<div id="warningModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm px-4">
  <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl transform transition-all">
    <div class="p-6">
      <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-amber-100">
        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
      </div>
      
      <h2 class="text-xl font-bold text-gray-900 text-center mb-2">Pesanan Masih Aktif</h2>
      <p class="text-sm text-gray-600 text-center mb-6">Anda masih memiliki pesanan yang belum selesai. Silakan selesaikan pesanan sebelumnya terlebih dahulu.</p>

      <div class="space-y-2">
        <button onclick="goToMonitoring()"
          class="w-full bg-pertamina text-white rounded-xl py-3 font-semibold
                 hover:bg-pertaminaDark transition-all shadow-sm">
          Lihat Pesanan Aktif
        </button>
        <button onclick="closeWarningModal()"
          class="w-full bg-gray-100 text-gray-700 rounded-xl py-3 font-semibold
                 hover:bg-gray-200 transition-all">
          Tutup
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ================= CONTENT ================= -->
<main class="max-w-[1200px] mx-auto px-6 py-5">
  <section class="bg-white rounded-2xl shadow-xl p-10 max-w-[900px] mx-auto">

    <!-- HEADER -->
    <div class="text-center mb-10">
      <h2 class="text-xl font-semibold text-gray-800">
        Formulir Pemesanan Kendaraan
      </h2>
    </div>

    <!-- ALERT -->
    <div id="alertSuccess" class="hidden mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl">
      <span id="alertSuccessText"></span>
    </div>

    <div id="alertError" class="hidden mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
      <ul id="alertErrorList" class="list-disc list-inside space-y-1"></ul>
    </div>

    <!-- FORM -->
<!-- SETELAH DIUBAH — tambah opacity-50 dan pointer-events-none -->
<form id="orderForm" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 opacity-50 pointer-events-none">      @csrf

      <!-- LOKASI JEMPUT -->
      <div>
        <label class="block text-sm font-medium text-gray-600 mb-2">
          Lokasi Jemput <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          name="tempat_penjemputan"
          id="tempat_penjemputan"
          placeholder="Kantor PGE Karaha"
          required
          class="w-full h-12 px-4 rounded-xl border border-gray-300
                 text-sm text-gray-700
                 focus:border-blue-500 focus:ring-4 focus:ring-blue-100
                 transition">
      </div>

      <!-- TUJUAN -->
      <div>
        <label class="block text-sm font-medium text-gray-600 mb-2">
          Tujuan <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          name="tempat_tujuan"
          id="tempat_tujuan"
          placeholder="Bandung"
          required
          class="w-full h-12 px-4 rounded-xl border border-gray-300
                 text-sm text-gray-700
                 focus:border-blue-500 focus:ring-4 focus:ring-blue-100
                 transition">
      </div>

      <!-- TANGGAL & WAKTU -->
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-600 mb-2">
          Tanggal & Waktu <span class="text-red-500">*</span>
        </label>
        <input
          type="datetime-local"
          name="waktu_penjemputan"
          id="waktu_penjemputan"
          min="{{ now()->format('Y-m-d\TH:i') }}"
          required
          class="w-full h-12 px-4 rounded-xl border border-gray-300
                 text-sm text-gray-700
                 focus:border-blue-500 focus:ring-4 focus:ring-blue-100
                 transition">
      </div>

      <!-- KETERANGAN -->
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-600 mb-2">
          Keterangan<span class="text-red-500">*</span>
        </label>
        <textarea
          name="keterangan"
          id="keterangan"
          rows="4"
          placeholder="Tambahkan keterangan"
          required
          class="w-full px-4 py-3 rounded-xl border border-gray-300
                 text-sm text-gray-700
                 focus:border-blue-500 focus:ring-4 focus:ring-blue-100
                 transition resize-none"></textarea>
      </div>

      <!-- BUTTON -->
      <div class="md:col-span-2 pt-4">
<!-- SETELAH DIUBAH — tambah disabled -->
<button
  type="submit"
  id="submitBtn"
  disabled
  class="w-full h-12 rounded-xl                bg-gradient-to-r from-pertamina to-pertaminaDark
                text-white font-semibold text-sm tracking-wide
                hover:opacity-90 transition
                disabled:opacity-50 disabled:cursor-not-allowed">
          Kirim Permintaan
        </button>
      </div>

    </form>

  </section>
</main>

@endsection

@push('scripts')
<script>
const apiToken = "{{ session('token', '') }}";

console.log('Token dari session:', apiToken);

function toggleProfile(){
  const dropdown = document.getElementById('profileDropdown');
  if (dropdown) {
    dropdown.classList.toggle("hidden");
  }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
  const dropdown = document.getElementById('profileDropdown');
  if (!dropdown) return;
  
  const profileBtn = event.target.closest('button[onclick="toggleProfile()"]');
  
  if (!profileBtn && !dropdown.contains(event.target)) {
    dropdown.classList.add('hidden');
  }
});

// Modal Functions
function closeWarningModal() {
  document.getElementById('warningModal').classList.add('hidden');
}
window.closeWarningModal = closeWarningModal;

function goToMonitoring() {
  window.location.href = '/penumpang/monitoring';
}
window.goToMonitoring = goToMonitoring;

// Show alert
function showAlert(type, message) {
  const alertSuccess = document.getElementById('alertSuccess');
  const alertError = document.getElementById('alertError');
  
  if (type === 'success') {
    document.getElementById('alertSuccessText').textContent = message;
    alertSuccess.classList.remove('hidden');
    alertError.classList.add('hidden');
  } else {
    const errorList = document.getElementById('alertErrorList');
    errorList.innerHTML = '';
    
    if (Array.isArray(message)) {
      message.forEach(msg => {
        const li = document.createElement('li');
        li.textContent = msg;
        errorList.appendChild(li);
      });
    } else {
      const li = document.createElement('li');
      li.textContent = message;
      errorList.appendChild(li);
    }
    
    alertError.classList.remove('hidden');
    alertSuccess.classList.add('hidden');
  }
  
  // Scroll to top
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Check active order on page load - LANGSUNG CEK SAAT LOAD
function unlockForm() {
  const form = document.getElementById('orderForm');
  form.classList.remove('opacity-50', 'pointer-events-none');
  document.getElementById('submitBtn').disabled = false;
}

function lockForm() {
  const form = document.getElementById('orderForm');
  form.classList.add('opacity-50', 'pointer-events-none');
  document.getElementById('submitBtn').disabled = true;
}

async function checkActiveOrder() {
  try {
    if (!apiToken) return;

    const response = await fetch('/api/penumpang/orders?token=' + apiToken, {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' }
    });

    const result = await response.json();

    if (result.status && result.data && result.data.length > 0) {
      const activeOrder = result.data.find(order => 
        ['pending', 'assigned', 'on-process'].includes(order.status)
      );

      if (activeOrder) {
        lockForm();
        document.getElementById('warningModal').classList.remove('hidden');
      } else {
        unlockForm();
      }
    } else {
      unlockForm();
    }
  } catch (error) {
    console.error('❌ Error checking active order:', error);
    unlockForm();
  }
}

// Form submission with AJAX
document.getElementById("orderForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  
  if (!apiToken) {
    showAlert('error', ['Token tidak tersedia. Silakan login kembali.']);
    return;
  }
  
  const submitBtn = document.getElementById("submitBtn");
  const originalText = submitBtn.textContent;
  
  // Disable button
  submitBtn.disabled = true;
  submitBtn.textContent = "Mengirim...";

  // Get form data
  const formData = new FormData(this);
  const data = {
    tempat_penjemputan: formData.get('tempat_penjemputan'),
    tempat_tujuan: formData.get('tempat_tujuan'),
    waktu_penjemputan: formData.get('waktu_penjemputan'),
    keterangan: formData.get('keterangan'),
    token: apiToken
  };

  console.log('Sending data:', data);

  try {
    const response = await fetch('/api/penumpang/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify(data)
    });

    const result = await response.json();
    console.log('Create order result:', result);

    if (result.status) {
      // Success
      showAlert('success', result.message || 'Pemesanan berhasil dibuat!');
      
      // Store order_id
      if (result.data && result.data.order_id) {
        sessionStorage.setItem('new_order_id', result.data.order_id);
      }
      
      // Redirect
      setTimeout(() => {
        window.location.href = '/penumpang/monitoring';
      }, 500);
    } else {
      // Error
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
      
      if (result.errors) {
        let errorMessages = [];
        
        if (typeof result.errors === 'string') {
          errorMessages.push(result.errors);
        } else if (typeof result.errors === 'object') {
          for (let key in result.errors) {
            if (Array.isArray(result.errors[key])) {
              errorMessages = errorMessages.concat(result.errors[key]);
            } else {
              errorMessages.push(result.errors[key]);
            }
          }
        }
        
        showAlert('error', errorMessages.length > 0 ? errorMessages : [result.message]);
      } else {
        showAlert('error', [result.message || 'Gagal membuat pemesanan']);
      }
    }
  } catch (error) {
    console.error('Error:', error);
    showAlert('error', ['Terjadi kesalahan. Silakan coba lagi.']);
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  }
});

// INIT - CEK ACTIVE ORDER SAAT PAGE LOAD
document.addEventListener('DOMContentLoaded', function() {
  console.log('✨ Page loaded - Checking active orders...');
  console.log('Token available:', apiToken ? 'Yes' : 'No');
  
  // LANGSUNG CEK ACTIVE ORDER
  checkActiveOrder();

  // Set min datetime
  const dateTimeInput = document.querySelector('input[name="waktu_penjemputan"]');
  if (dateTimeInput) {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    dateTimeInput.min = now.toISOString().slice(0, 16);
  }
});
</script>
@endpush

@push('styles')
<style>
.form-group{
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group label{
  font-size: 13px;
  font-weight: 600;
  color: #374151;
}

.form-group input,
.form-group textarea{
  border: 1px solid #d1d5db;
  border-radius: 14px;
  padding: 12px 14px;
  font-size: 14px;
}

.form-group input:focus,
.form-group textarea:focus{
  outline: none;
  border-color: #D32F2F;
  box-shadow: 0 0 0 3px rgba(211,47,47,.15);
}

.form-group input.error,
.form-group textarea.error{
  border-color: #ef4444;
}
</style>
@endpush