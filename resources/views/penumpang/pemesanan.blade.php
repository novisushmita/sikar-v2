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
      </div>
    </div>
  </div>
</div>

<!-- ================= CONTENT ================= -->
<main class="max-w-[1200px] mx-auto px-6 py-5">
  <section class="bg-white rounded-2xl shadow-xl p-10 max-w-[900px] mx-auto">

    <!-- HEADER -->
    <div class="text-center mb-10">
      <h2 class="text-xl font-semibold text-gray-800">Formulir Pemesanan Kendaraan</h2>
    </div>

    <!-- FORM -->
    <form id="orderForm" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 opacity-50 pointer-events-none">
      @csrf

      <!-- LOKASI JEMPUT -->
      <div>
        <label class="block text-sm font-medium text-gray-600 mb-2">
          Lokasi Jemput <span class="text-red-500">*</span>
        </label>
        <input type="text" name="tempat_penjemputan" id="tempat_penjemputan"
          placeholder="Kantor PGE Karaha" required
          class="w-full h-12 px-4 rounded-xl border border-gray-300 text-sm text-gray-700
                 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition">
      </div>

      <!-- TUJUAN -->
      <div>
        <label class="block text-sm font-medium text-gray-600 mb-2">
          Tujuan <span class="text-red-500">*</span>
        </label>
        <input type="text" name="tempat_tujuan" id="tempat_tujuan"
          placeholder="Bandung" required
          class="w-full h-12 px-4 rounded-xl border border-gray-300 text-sm text-gray-700
                 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition">
      </div>

      <!-- TANGGAL & WAKTU -->
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-600 mb-2">
          Tanggal & Waktu <span class="text-red-500">*</span>
        </label>
        <input type="datetime-local" name="waktu_penjemputan" id="waktu_penjemputan"
          min="{{ now()->format('Y-m-d\TH:i') }}" required
          class="w-full h-12 px-4 rounded-xl border border-gray-300 text-sm text-gray-700
                 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition">
      </div>

      <!-- KETERANGAN -->
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-600 mb-2">
          Keterangan<span class="text-red-500">*</span>
        </label>
        <textarea name="keterangan" id="keterangan" rows="4"
          placeholder="Tambahkan keterangan" required
          class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm text-gray-700
                 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition resize-none"></textarea>
      </div>

      <!-- BUTTON -->
      <div class="md:col-span-2 pt-4">
        <button type="submit" id="submitBtn" disabled
          class="w-full h-12 rounded-xl bg-gradient-to-r from-pertamina to-pertaminaDark
                 text-white font-semibold text-sm tracking-wide hover:opacity-90 transition
                 disabled:opacity-50 disabled:cursor-not-allowed">
          Kirim Permintaan
        </button>
      </div>
    </form>

  </section>
</main>

<script>
const apiToken = "{{ session('token', '') }}";

// ================= NOTIFICATION =================
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  let bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
  let icon = type === 'success'
    ? `<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`
    : type === 'error'
    ? `<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`
    : `<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;

  notification.className = `fixed top-4 right-4 z-[60] px-5 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full ${bgColor}`;
  notification.innerHTML = `<div class="flex items-center gap-3 text-white">${icon}<span class="font-medium">${message}</span></div>`;
  document.body.appendChild(notification);

  setTimeout(() => { notification.classList.remove('translate-x-full'); notification.classList.add('translate-x-0'); }, 100);
  setTimeout(() => {
    notification.classList.remove('translate-x-0');
    notification.classList.add('translate-x-full');
    setTimeout(() => { if (document.body.contains(notification)) document.body.removeChild(notification); }, 300);
  }, 3000);
}

// ================= MODAL FUNCTIONS =================
function goToMonitoring() { window.location.href = '/penumpang/monitoring'; }
window.goToMonitoring = goToMonitoring;

// ================= FORM LOCK/UNLOCK =================
function unlockForm() {
  document.getElementById('orderForm').classList.remove('opacity-50', 'pointer-events-none');
  document.getElementById('submitBtn').disabled = false;
}

function lockForm() {
  document.getElementById('orderForm').classList.add('opacity-50', 'pointer-events-none');
  document.getElementById('submitBtn').disabled = true;
}

// ================= CHECK ACTIVE ORDER =================
async function checkActiveOrder() {
  try {
    if (!apiToken) return;
    const response = await fetch('/api/penumpang/orders?token=' + apiToken, {
      method: 'GET', headers: { 'Content-Type': 'application/json' }
    });
    const result = await response.json();
    if (result.status && result.data && result.data.length > 0) {
      const activeOrder = result.data.find(order => ['pending', 'assigned', 'on-process'].includes(order.status));
      if (activeOrder) { lockForm(); document.getElementById('warningModal').classList.remove('hidden'); }
      else { unlockForm(); }
    } else { unlockForm(); }
  } catch (error) {
    console.error('Error checking active order:', error);
    unlockForm();
  }
}

// ================= FORM SUBMISSION =================
document.getElementById("orderForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  if (!apiToken) { showNotification('Token tidak tersedia. Silakan login kembali.', 'error'); return; }

  const submitBtn = document.getElementById("submitBtn");
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = "Mengirim...";

  const formData = new FormData(this);
  const data = {
    tempat_penjemputan: formData.get('tempat_penjemputan'),
    tempat_tujuan:      formData.get('tempat_tujuan'),
    waktu_penjemputan:  formData.get('waktu_penjemputan'),
    keterangan:         formData.get('keterangan'),
    token:              apiToken
  };

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

    if (result.status) {
      showNotification(result.message || 'Pemesanan berhasil dibuat!', 'success');
      if (result.data?.order_id) sessionStorage.setItem('new_order_id', result.data.order_id);
      setTimeout(() => { window.location.href = '/penumpang/monitoring'; }, 500);
    } else {
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;

      let errorMsg = result.message || 'Gagal membuat pemesanan';
      if (result.errors) {
        const msgs = [];
        if (typeof result.errors === 'string') { msgs.push(result.errors); }
        else { for (let k in result.errors) { const v = result.errors[k]; Array.isArray(v) ? msgs.push(...v) : msgs.push(v); } }
        if (msgs.length) errorMsg = msgs.join('. ');
      }
      showNotification(errorMsg, 'error');
    }
  } catch (error) {
    showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  }
});

// ================= INIT =================
document.addEventListener('DOMContentLoaded', function() {
  checkActiveOrder();
  const dateTimeInput = document.querySelector('input[name="waktu_penjemputan"]');
  if (dateTimeInput) {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    dateTimeInput.min = now.toISOString().slice(0, 16);
  }
});
</script>

@endsection

@push('styles')
<style>
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 13px; font-weight: 600; color: #374151; }
.form-group input, .form-group textarea { border: 1px solid #d1d5db; border-radius: 14px; padding: 12px 14px; font-size: 14px; }
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: #D32F2F; box-shadow: 0 0 0 3px rgba(211,47,47,.15); }
.form-group input.error, .form-group textarea.error { border-color: #ef4444; }
</style>
@endpush