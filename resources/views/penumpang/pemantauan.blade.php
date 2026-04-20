@extends('layouts.app')

@section('title', 'Pemantauan')

@section('content')

<!-- ================= MODAL KONFIRMASI BIASA ================= -->
<div id="alertModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm px-4">
  <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl transform transition-all">
    <div class="p-6">
      <div id="modalContent"></div>
    </div>
  </div>
</div>

<!-- ================= MODAL RATING ================= -->
<div id="ratingModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm px-4">
  <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl">
    <div class="p-6">
      <div class="text-center mb-5">
        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-yellow-100 mb-3">
          <svg class="h-7 w-7 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
          </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900">Beri Rating Sopir</h3>
        <p class="text-sm text-gray-500 mt-1">
          Bagaimana pengalaman perjalananmu bersama
          <span id="ratingDriverName" class="font-semibold text-gray-700"></span>?
        </p>
      </div>

      <!-- Bintang interaktif -->
      <div class="flex justify-center gap-2 mb-2" id="starRow">
        <span class="star-btn text-5xl cursor-pointer select-none" data-value="1">☆</span>
        <span class="star-btn text-5xl cursor-pointer select-none" data-value="2">☆</span>
        <span class="star-btn text-5xl cursor-pointer select-none" data-value="3">☆</span>
        <span class="star-btn text-5xl cursor-pointer select-none" data-value="4">☆</span>
        <span class="star-btn text-5xl cursor-pointer select-none" data-value="5">☆</span>
      </div>
      <p class="text-center text-sm text-gray-400 mb-6" id="ratingLabel">Pilih bintang di atas</p>

      <button id="submitRatingBtn" onclick="submitRatingAndConfirm()" disabled
        class="w-full bg-pertamina text-white rounded-xl py-3 font-semibold transition-all text-sm
               disabled:opacity-40 disabled:cursor-not-allowed hover:bg-pertaminaDark">
        Konfirmasi Selesai
      </button>
    </div>
  </div>
</div>

<!-- ================= CONTENT ================= -->
<div class="max-w-6xl space-y-6 md:space-y-8">

  <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4 md:p-6">

    <!-- LOADING STATE -->
    <div id="loadingContent" class="text-center py-12">
      <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-pertamina mx-auto mb-4"></div>
      <p class="text-gray-600 font-medium">Memuat data pemesanan...</p>
      <p class="text-xs text-gray-400 mt-2" id="loadingAttempt"></p>
    </div>

    <!-- ERROR STATE -->
    <div id="errorContent" class="hidden text-center py-12">
      <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
      </div>
      <p class="text-gray-800 font-semibold text-lg mb-2">Gagal Memuat Data</p>
      <p class="text-gray-600 mb-6 text-sm" id="errorMessage">Terjadi kesalahan saat memuat data</p>
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <button onclick="location.reload()"
          class="bg-pertamina text-white px-6 py-3 rounded-xl hover:bg-pertaminaDark transition-all shadow-sm font-semibold">
          Muat Ulang
        </button>
        <button onclick="window.location.href='/penumpang/pemesanan'"
          class="bg-gray-500 text-white px-6 py-3 rounded-xl hover:bg-gray-600 transition-all shadow-sm font-semibold">
          Buat Pesanan Baru
        </button>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div id="mainContent" class="hidden">
      <div class="border border-gray-200 rounded-lg md:rounded-xl p-4 md:p-5 transition-all">

        <!-- Header -->
        <div class="flex justify-between items-start mb-4">
          <div class="flex-1">
            <p class="text-xs text-gray-500">ID Pemesanan <span class="font-semibold text-gray-700" id="orderId">-</span></p>
            <p class="text-xs text-gray-500">Dibuat <span class="font-medium text-green-500" id="createdAt">-</span></p>
            <p class="text-xs text-gray-500 mb-1">Diperbarui <span class="font-medium text-red-500" id="updatedAt">-</span></p>
            <p class="font-semibold text-gray-800 text-sm md:text-base" id="passengerName">-</p>
          </div>
          <span id="statusBadge" class="text-xs px-2.5 py-1 rounded-full font-semibold whitespace-nowrap">-</span>
        </div>

        <!-- Route -->
        <div class="flex gap-3 mb-2">
          <div class="flex flex-col items-center pt-1">
            <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
            <div class="flex-1 w-0.5 bg-gray-300 my-2 min-h-[40px]"></div>
            <span class="w-2.5 h-2.5 bg-gray-400 rounded-full"></span>
          </div>
          <div class="flex-1 space-y-3">
            <div>
              <p class="text-xs font-semibold text-gray-700 mb-1">Tempat Penjemputan</p>
              <p class="text-xs md:text-sm text-gray-600" id="pickupLocation">-</p>
              <p class="text-xs text-blue-500"><span class="font-medium" id="pickupTime">-</span></p>
            </div>
            <div>
              <p class="text-xs font-semibold text-gray-700 mb-1">Tempat Tujuan</p>
              <p class="text-xs md:text-sm text-gray-600" id="destination">-</p>
            </div>
          </div>
        </div>

        <div id="descriptionWrapper" class="pt-3 space-y-2 mb-4">
          <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-200">
            <p class="text-xs font-semibold text-gray-500 mb-1">Keterangan</p>
            <p class="text-xs md:text-sm text-gray-700" id="description">-</p>
          </div>
        </div>

        <!-- Sopir & Mobil Info -->
        <div id="driverInfo" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <p class="text-xs font-semibold text-blue-700 mb-1">
                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>Sopir
              </p>
              <p class="text-xs text-gray-700" id="driverName">-</p>
            </div>
            <div>
              <p class="text-xs font-semibold text-blue-700 mb-1">
                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                  <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                </svg>Mobil
              </p>
              <p class="text-xs text-gray-700" id="driverCar">-</p>
              <p class="text-xs text-gray-500" id="driverPlate">-</p>
            </div>
          </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="border-t border-gray-100 pt-3 space-y-2">

          <!-- Konfirmasi Sampai -->
          <button id="confirmBtn" onclick="openRatingModal()"
            class="hidden flex items-center justify-center gap-2 w-full bg-blue-500 hover:bg-blue-600
                   text-white font-semibold text-sm py-2.5 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md">
            Konfirmasi Sampai Tujuan
          </button>

          <!-- Batalkan -->
          <button id="cancelBtn" onclick="cancelOrder()"
            class="hidden flex items-center justify-center gap-2 w-full bg-red-600 hover:bg-red-700
                   text-white font-semibold text-sm py-2.5 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md">
            Batalkan Pemesanan
          </button>

          <!-- WA Kepala Sopir & Sopir (sejajar) -->
          <div id="waRowWrapper" class="hidden flex gap-2">
            <div id="waKepalaSopirWrapper" class="flex-1">
              <a id="waKepalaSopir" href="#" target="_blank"
              class="flex items-center justify-center gap-1.5 w-full bg-green-500 hover:bg-green-600
                    text-white font-semibold text-sm py-2.5 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                Kepala Sopir
              </a>
            </div>
            <div id="waDriverWrapper" class="flex-1">
              <a id="waDriver" href="#" target="_blank"
              class="flex items-center justify-center gap-1.5 w-full bg-green-500 hover:bg-green-600
                    text-white font-semibold text-sm py-2.5 rounded-lg transition-all duration-300 shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                Sopir
              </a>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

</div>

<!-- LOADING OVERLAY -->
<div id="loadingOverlay" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm">
  <div class="bg-white rounded-2xl p-8 w-[200px] text-center shadow-2xl">
    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-pertamina mx-auto mb-4"></div>
    <p class="text-sm text-gray-600 font-medium">Memproses...</p>
  </div>
</div>

<style>
  .star-btn { color: #d1d5db; transition: color 0.15s, transform 0.15s; }
  .star-btn.active { color: #f59e0b; }
  .star-btn:hover { transform: scale(1.15); }
</style>

<script>
(function() {
  'use strict';

  /* ===== NOTIFICATION (sama seperti pesanan sopir) ===== */
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

  /* ===== STATE ===== */
  const apiToken = "{{ session('token', '') }}";
  let currentOrder = null;
  let autoRefreshInterval = null;
  let retryCount = 0;
  const MAX_RETRIES = 0;
  let selectedRating = 0;
  let nomorKepalaSopir = null;

  /* ===== RATING MODAL ===== */
  const ratingLabels = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'];

  function openRatingModal() {
    if (!currentOrder) return;
    selectedRating = 0;
    document.getElementById('submitRatingBtn').disabled = true;
    document.getElementById('ratingLabel').textContent = 'Pilih bintang di atas';
    document.querySelectorAll('.star-btn').forEach(s => { s.classList.remove('active'); s.textContent = '☆'; });
    document.getElementById('ratingDriverName').textContent = currentOrder.assignment?.sopir?.name || 'Sopir';
    document.getElementById('ratingModal').classList.remove('hidden');
  }
  window.openRatingModal = openRatingModal;

  document.querySelectorAll('.star-btn').forEach(star => {
    star.addEventListener('click', () => {
      selectedRating = parseInt(star.dataset.value);
      updateStars(selectedRating);
      document.getElementById('ratingLabel').textContent = ratingLabels[selectedRating];
      document.getElementById('submitRatingBtn').disabled = false;
    });
    star.addEventListener('mouseover', () => updateStars(parseInt(star.dataset.value)));
    star.addEventListener('mouseout', () => updateStars(selectedRating));
  });

  function updateStars(val) {
    document.querySelectorAll('.star-btn').forEach(s => {
      const v = parseInt(s.dataset.value);
      s.textContent = v <= val ? '★' : '☆';
      s.classList.toggle('active', v <= val);
    });
  }

  async function submitRatingAndConfirm() {
    if (!currentOrder || selectedRating === 0) return;
    document.getElementById('ratingModal').classList.add('hidden');
    showLoading();

    const sopirId = currentOrder.assignment?.sopir_id ?? null;
    const today = new Date().toISOString().split('T')[0];

    try {
      if (sopirId) {
        const reviewRes = await fetch(`/api/penumpang/review?token=${apiToken}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${apiToken}`,
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
          },
          body: JSON.stringify({ review: selectedRating, tanggal: today, sopir_id: sopirId, order_id: currentOrder.order_id })
        });
        if (!reviewRes.ok) console.warn('Review HTTP error:', reviewRes.status);
        else { const rd = await reviewRes.json(); if (!rd.status) console.warn('Review gagal:', rd.message); }
      }

      const confirmRes = await fetch(`/api/penumpang/confirm/${currentOrder.order_id}?token=${apiToken}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json', 'Accept': 'application/json',
          'Authorization': `Bearer ${apiToken}`,
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        }
      });
      const confirmData = await confirmRes.json();
      hideLoading();

      if (confirmData.status) {
        showNotification(confirmData.message || 'Order berhasil dikonfirmasi!', 'success');
        setTimeout(() => { window.location.href = '/penumpang/riwayat'; }, 1500);
      } else {
        showNotification(confirmData.message || 'Gagal mengkonfirmasi order', 'error');
      }
    } catch (error) {
      hideLoading();
      showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
    }
  }
  window.submitRatingAndConfirm = submitRatingAndConfirm;

  /* ===== MODAL KONFIRMASI BATAL ===== */
  function showAlertModal(title, message) {
    document.getElementById('modalContent').innerHTML = `
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
          <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-3">${title}</h3>
        <div class="bg-gray-50 rounded-lg p-4 mb-6"><p class="text-sm text-gray-700">${message}</p></div>
        <div class="flex gap-3">
          <button onclick="closeAlertModal()" class="flex-1 bg-gray-200 text-gray-700 rounded-xl py-3 font-semibold hover:bg-gray-300 transition-all">Batal</button>
          <button onclick="handleConfirmAction()" class="flex-1 bg-pertamina text-white rounded-xl py-3 font-semibold hover:bg-pertaminaDark transition-all">Ya, Lanjutkan</button>
        </div>
      </div>`;
    document.getElementById('alertModal').classList.remove('hidden');
  }

  function closeAlertModal() { document.getElementById('alertModal').classList.add('hidden'); }
  window.closeAlertModal = closeAlertModal;

  function handleConfirmAction() {
    if (window.cancelOrderAction) { window.cancelOrderAction(); window.cancelOrderAction = null; }
  }
  window.handleConfirmAction = handleConfirmAction;

  function showLoading()  { document.getElementById('loadingOverlay').classList.remove('hidden'); }
  function hideLoading()  { document.getElementById('loadingOverlay').classList.add('hidden'); }

  /* ===== STATUS CONFIG ===== */
  function getStatusConfig(status) {
    const configs = {
      'pending':    { label: 'Menunggu Persetujuan', bgColor: 'bg-yellow-50', textColor: 'text-yellow-700', borderColor: 'border-yellow-200' },
      'assigned':   { label: 'Ditugaskan',           bgColor: 'bg-purple-50', textColor: 'text-purple-700', borderColor: 'border-purple-200' },
      'on-process': { label: 'Dalam Perjalanan',     bgColor: 'bg-blue-50',   textColor: 'text-blue-700',   borderColor: 'border-blue-200'   },
      'confirmed':  { label: 'Dikonfirmasi',          bgColor: 'bg-green-50',  textColor: 'text-green-700',  borderColor: 'border-green-200'  },
      'completed':  { label: 'Selesai',               bgColor: 'bg-green-50',  textColor: 'text-green-700',  borderColor: 'border-green-200'  },
    };
    return configs[status] || { label: status, bgColor: 'bg-gray-50', textColor: 'text-gray-700', borderColor: 'border-gray-200' };
  }

  function formatDateTime(datetime) {
    if (!datetime) return '-';
    const date = new Date(datetime.replace(' ', 'T'));
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
      + ' ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
  }

  function cleanPhone(phone) {
    if (!phone) return '';
    let p = phone.replace(/[^0-9]/g, '');
    if (p.startsWith('0')) p = '62' + p.substring(1);
    return p;
  }

  /* ===== UPDATE UI ===== */
  function updateUI(order) {
    currentOrder = order;
    const status = order.status;
    const cfg = getStatusConfig(status);

    document.getElementById('orderId').textContent        = '#' + order.order_id;
    document.getElementById('passengerName').textContent  = order.penumpang?.name || '-';
    document.getElementById('pickupLocation').textContent = order.tempat_penjemputan || '-';
    document.getElementById('destination').textContent    = order.tempat_tujuan || '-';
    document.getElementById('pickupTime').textContent     = formatDateTime(order.waktu_penjemputan);
    document.getElementById('createdAt').textContent      = formatDateTime(order.dibuat_pada || order.created_at);
    document.getElementById('updatedAt').textContent      = formatDateTime(order.diupdate_pada || order.updated_at);

    const badge = document.getElementById('statusBadge');
    badge.textContent = cfg.label;
    badge.className = `inline-flex items-center text-xs px-3 py-1 rounded-full font-semibold whitespace-nowrap ${cfg.bgColor} ${cfg.textColor} border ${cfg.borderColor}`;

    if (order.keterangan && order.keterangan !== '-') {
      document.getElementById('description').textContent = order.keterangan;
      document.getElementById('descriptionWrapper').classList.remove('hidden');
    } else {
      document.getElementById('descriptionWrapper').classList.add('hidden');
    }

    document.getElementById('confirmBtn').classList.add('hidden');
    document.getElementById('cancelBtn').classList.add('hidden');
    if (status === 'pending')                              document.getElementById('cancelBtn').classList.remove('hidden');
    if (status === 'on-process' || status === 'confirmed') document.getElementById('confirmBtn').classList.remove('hidden');

    if (['assigned', 'on-process', 'confirmed'].includes(status) && order.assignment) {
      document.getElementById('driverInfo').classList.remove('hidden');
      document.getElementById('driverName').textContent = order.assignment.sopir?.name || '-';
      const parts = (order.assignment.mobil?.deskripsi || '').split(' - ');
      document.getElementById('driverCar').textContent   = parts[0] || '-';
      document.getElementById('driverPlate').textContent = parts[1] || '-';
      if (order.assignment.sopir?.nomor) {
        document.getElementById('waDriver').href = `https://wa.me/${cleanPhone(order.assignment.sopir.nomor)}`;
      }
    } else {
      document.getElementById('driverInfo').classList.add('hidden');
    }

    if (['assigned', 'on-process', 'confirmed'].includes(status) && order.nomor_kepala_sopir) {
      document.getElementById('waKepalaSopir').href = `https://wa.me/${cleanPhone(order.nomor_kepala_sopir)}`;
      document.getElementById('waRowWrapper').classList.remove('hidden');
      document.getElementById('waRowWrapper').classList.add('flex');
    } else {
      document.getElementById('waRowWrapper').classList.add('hidden');
      document.getElementById('waRowWrapper').classList.remove('flex');
    }

    if (status === 'completed') {
      setTimeout(() => { window.location.href = '/penumpang/riwayat'; }, 1000);
      return;
    }

    document.getElementById('loadingContent').classList.add('hidden');
    document.getElementById('errorContent').classList.add('hidden');
    document.getElementById('mainContent').classList.remove('hidden');

    setupAutoRefresh(status);
    retryCount = 0;
  }

  function setupAutoRefresh(status) {
    if (autoRefreshInterval) clearInterval(autoRefreshInterval);
    if (['pending', 'assigned', 'on-process', 'confirmed'].includes(status)) {
      autoRefreshInterval = setInterval(() => fetchOrderData(true), 5000);
    }
  }

  /* ===== FETCH DATA ===== */
  async function fetchOrderData(silent = false) {
    try {
      if (!silent) {
        document.getElementById('loadingAttempt').textContent = retryCount > 0 ? `Percobaan ke-${retryCount + 1}...` : '';
      }
      if (!apiToken) throw new Error('Token tidak tersedia. Silakan login kembali.');
      sessionStorage.removeItem('new_order_id');

      const res = await fetch(`/api/penumpang/orders?token=${apiToken}`);
      if (!res.ok) {
        if (res.status === 401) throw new Error('Sesi berakhir. Silakan login kembali.');
        throw new Error(`HTTP ${res.status}`);
      }
      const result = await res.json();
      if (!result.status) throw new Error(result.message || 'Gagal memuat data');
      if (!result.data?.length) throw new Error('Tidak ada pemesanan aktif. Buat pesanan baru atau cek riwayat.');

      const active = result.data.find(o => o.status === 'pending')
        || result.data.find(o => o.status === 'assigned')
        || result.data.find(o => o.status === 'on-process')
        || result.data.find(o => o.status === 'confirmed')
        || result.data[0];

      nomorKepalaSopir = active.nomor_kepala_sopir || result.data[0]?.nomor_kepala_sopir || null;

      const detailRes = await fetch(`/api/penumpang/orders/${active.order_id}?token=${apiToken}`);
      if (!detailRes.ok) throw new Error(`Gagal ambil detail: ${detailRes.status}`);
      const detailResult = await detailRes.json();
      if (!detailResult.status || !detailResult.data) throw new Error('Detail data tidak lengkap');

      const targetOrder = detailResult.data;
      targetOrder.nomor_kepala_sopir = nomorKepalaSopir;
      updateUI(targetOrder);

    } catch (error) {
      if (!silent) {
        if (retryCount < MAX_RETRIES) {
          retryCount++;
          document.getElementById('loadingAttempt').textContent = `Gagal memuat. Mencoba lagi... (${retryCount}/${MAX_RETRIES})`;
          setTimeout(() => fetchOrderData(false), 1000);
        } else {
          document.getElementById('loadingContent').classList.add('hidden');
          document.getElementById('mainContent').classList.add('hidden');
          document.getElementById('errorContent').classList.remove('hidden');
          document.getElementById('errorMessage').textContent = error.message;
        }
      }
    }
  }

  /* ===== CANCEL ORDER ===== */
  async function cancelOrder() {
    if (!currentOrder) return;
    showAlertModal('Batalkan Pemesanan', 'Apakah Anda yakin ingin membatalkan pemesanan ini?');
    window.cancelOrderAction = async function() {
      closeAlertModal();
      showLoading();
      try {
        const res = await fetch(`/api/penumpang/cancel/${currentOrder.order_id}?token=${apiToken}`, {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
        });
        const data = await res.json();
        hideLoading();
        if (data.status) {
          showNotification(data.message || 'Pemesanan berhasil dibatalkan!', 'success');
          setTimeout(() => { window.location.href = '/penumpang/riwayat'; }, 1500);
        } else {
          showNotification(data.message || 'Gagal membatalkan pemesanan', 'error');
        }
      } catch (e) {
        hideLoading();
        showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
      }
    };
  }
  window.cancelOrder = cancelOrder;

  /* ===== INIT ===== */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => fetchOrderData(false));
  } else {
    fetchOrderData(false);
  }
  window.addEventListener('beforeunload', () => { if (autoRefreshInterval) clearInterval(autoRefreshInterval); });
})();
</script>

@endsection