@extends('layouts.app')

@section('title', 'Pemantauan')

@section('content')

<!-- ================= MODAL POPUP ================= -->
<div id="alertModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm px-4">
  <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl transform transition-all">
    <div class="p-6">
      <div id="modalContent">
        <!-- Content will be inserted here -->
      </div>
    </div>
  </div>
</div>

<!-- ================= CONTENT ================= -->
<div class="max-w-6xl space-y-6 md:space-y-8">

  <!-- ================= MAIN SECTION ================= -->
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
      
      <!-- ORDER CARD -->
      <div class="border border-gray-200 rounded-lg md:rounded-xl p-4 md:p-5 transition-all">

<!-- Header -->
<div class="flex justify-between items-start mb-4">
  <div class="flex-1">
    <p class="text-xs text-gray-500">
      ID Pemesanan <span class="font-semibold text-gray-700" id="orderId">-</span>
    </p>
    <p class="text-xs text-gray-500">
      Dibuat <span class="font-medium text-green-500" id="createdAt">-</span>
    </p>
    <p class="text-xs text-gray-500 mb-1">
      Diperbarui <span class="font-medium text-red-500" id="updatedAt">-</span>
    </p>
    <p class="font-semibold text-gray-800 text-sm md:text-base" id="passengerName">-</p>
  </div>

  <span id="statusBadge" class="text-xs px-2.5 py-1 rounded-full font-semibold whitespace-nowrap">
    -
  </span>
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
              <p class="text-xs font-semibold text-gray-700 mb-1">
                Tempat Penjemputan
              </p>
              <p class="text-xs md:text-sm text-gray-600" id="pickupLocation">-</p>
              <p class="text-xs text-blue-500">
                <span class="font-medium" id="pickupTime">-</span>
              </p>
            </div>

            <div>
              <p class="text-xs font-semibold text-gray-700 mb-1">
                Tempat Tujuan
              </p>
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

        <!-- Sopir & Mobil Info (hidden by default, shown when assigned) -->
        <div id="driverInfo" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <p class="text-xs font-semibold text-blue-700 mb-1">
                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                Sopir
              </p>
              <p class="text-xs text-gray-700" id="driverName">-</p>
            </div>
            <div>
              <p class="text-xs font-semibold text-blue-700 mb-1">
                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                  <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                </svg>
                Mobil
              </p>
              <p class="text-xs text-gray-700" id="driverCar">-</p>
              <p class="text-xs text-gray-500" id="driverPlate">-</p>
            </div>
          </div>
        </div>

        <!-- Footer dengan Tombol WA (hidden by default) -->
        <div id="waDriverWrapper" class="hidden border-t border-gray-100 pt-3 mb-1">
          <a id="waDriver" href="#" target="_blank"
             class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 
                    text-white font-semibold text-sm py-2.5 rounded-lg transition-all duration-300
                    shadow-sm hover:shadow-md">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
            </svg>
            Hubungi Sopir
          </a>
        </div>

<!-- ACTION BUTTONS -->
        <div class="border-t border-gray-100 pt-3 space-y-2">
          <!-- Tombol Konfirmasi Sampai (shown when on-process or confirmed) -->
          <button id="confirmBtn" onclick="confirmArrival()" 
            class="hidden flex items-center justify-center gap-2 w-full bg-blue-500 hover:bg-blue-600 
                   text-white font-semibold text-sm py-2.5 rounded-lg transition-all duration-300
                   shadow-sm hover:shadow-md">
            Konfirmasi Sampai Tujuan
          </button>

          <!-- Tombol Batalkan (shown when pending) -->
          <button id="cancelBtn" onclick="cancelOrder()" 
            class="hidden flex items-center justify-center gap-2 w-full bg-red-600 hover:bg-red-700 
                   text-white font-semibold text-sm py-2.5 rounded-lg transition-all duration-300
                   shadow-sm hover:shadow-md">
            Batalkan Pemesanan
          </button>
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

@endsection

@push('scripts')
<script>
(function() {
  'use strict';

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  const apiToken = "{{ session('token', '') }}";

  let currentOrder = null;
  let autoRefreshInterval = null;
  let retryCount = 0;
  const MAX_RETRIES = 0;

  console.log('=== MONITORING PAGE LOADED ===');

  // ================= STATUS CONFIG =================
  function getStatusConfig(status) {
    const configs = {
      'pending': {
        label: 'Menunggu Persetujuan',
        bgColor: 'bg-yellow-50',
        textColor: 'text-yellow-700',
        borderColor: 'border-yellow-200'
      },
      'assigned': {
        label: 'Ditugaskan',
        bgColor: 'bg-purple-50',
        textColor: 'text-purple-700',
        borderColor: 'border-purple-200'
      },
      'on-process': {
        label: 'Dalam Perjalanan',
        bgColor: 'bg-blue-50',
        textColor: 'text-blue-700',
        borderColor: 'border-blue-200'
      },
      'confirmed': {
        label: 'Dikonfirmasi',
        bgColor: 'bg-green-50',
        textColor: 'text-green-700',
        borderColor: 'border-green-200'
      },
      'completed': {
        label: 'Selesai',
        bgColor: 'bg-green-50',
        textColor: 'text-green-700',
        borderColor: 'border-green-200'
      }
    };
    
    return configs[status] || {
      label: status,
      bgColor: 'bg-gray-50',
      textColor: 'text-gray-700',
      borderColor: 'border-gray-200'
    };
  }

  function showAlertModal(title, message, type = 'success') {
    const modal = document.getElementById('alertModal');
    const content = document.getElementById('modalContent');
    
    let html = '';
    
    if (type === 'confirm') {
      html = `
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
            <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">${title}</h3>
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-700">${message}</p>
          </div>
          <div class="flex gap-3">
            <button onclick="closeAlertModal()"
              class="flex-1 bg-gray-200 text-gray-700 rounded-xl py-3 px-4 font-semibold
                     hover:bg-gray-300 transition-all shadow-sm">
              Batal
            </button>
            <button onclick="handleConfirmAction()"
              class="flex-1 bg-pertamina text-white rounded-xl py-3 px-4 font-semibold
                     hover:bg-pertaminaDark transition-all shadow-sm">
              Ya, Lanjutkan
            </button>
          </div>
        </div>
      `;
    } else if (type === 'success') {
      html = `
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">${title}</h3>
          <div class="bg-green-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-700">${message}</p>
          </div>
          <button onclick="closeAlertModal()"
            class="w-full bg-pertamina text-white rounded-xl py-3 px-4 font-semibold
                   hover:bg-pertaminaDark transition-all shadow-sm">
            Tutup
          </button>
        </div>
      `;
    } else if (type === 'error') {
      html = `
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-3">${title}</h3>
          <div class="bg-red-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-700">${message}</p>
          </div>
          <button onclick="closeAlertModal()"
            class="w-full bg-pertamina text-white rounded-xl py-3 px-4 font-semibold
                   hover:bg-pertaminaDark transition-all shadow-sm">
            Tutup
          </button>
        </div>
      `;
    }
    
    content.innerHTML = html;
    modal.classList.remove('hidden');
  }
  window.showAlertModal = showAlertModal;
  
  function handleConfirmAction() {
    if (window.confirmArrivalAction) {
      window.confirmArrivalAction();
      window.confirmArrivalAction = null;
    } else if (window.cancelOrderAction) {
      window.cancelOrderAction();
      window.cancelOrderAction = null;
    }
  }
  window.handleConfirmAction = handleConfirmAction;

  function closeAlertModal() {
    document.getElementById('alertModal').classList.add('hidden');
  }
  window.closeAlertModal = closeAlertModal;

  function showLoading() {
    document.getElementById('loadingOverlay').classList.remove('hidden');
  }

  function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('hidden');
  }

  function formatDateTime(datetime) {
    if (!datetime) return '-';
    const date = new Date(datetime.replace(' ', 'T'));
    const tanggal = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    const jam = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
    return `${tanggal} ${jam}`;
  }

  function cleanPhoneNumber(phone) {
    if (!phone) return '';
    let cleaned = phone.replace(/[^0-9]/g, '');
    if (cleaned.startsWith('0')) {
      cleaned = '62' + cleaned.substring(1);
    }
    return cleaned;
  }

  function updateUI(order) {
    currentOrder = order;
    const status = order.status;

    console.log('📝 Updating UI with order:', order.order_id, '| Status:', status);

    const statusConfig = getStatusConfig(status);

    document.getElementById('orderId').textContent = '#' + order.order_id;
    document.getElementById('passengerName').textContent = order.penumpang?.name || '-';
    document.getElementById('pickupLocation').textContent = order.tempat_penjemputan || '-';
    document.getElementById('destination').textContent = order.tempat_tujuan || '-';
    document.getElementById('pickupTime').textContent = formatDateTime(order.waktu_penjemputan);
    document.getElementById('createdAt').textContent = formatDateTime(order.dibuat_pada || order.created_at);
    document.getElementById('updatedAt').textContent = formatDateTime(order.diupdate_pada || order.updated_at);
    
    const statusBadge = document.getElementById('statusBadge');
    statusBadge.textContent = statusConfig.label;
    statusBadge.className = `inline-flex items-center text-xs px-3 py-1 rounded-full 
    font-semibold whitespace-nowrap
    ${statusConfig.bgColor} ${statusConfig.textColor} border ${statusConfig.borderColor}`;

    if (order.keterangan && order.keterangan !== '-') {
      document.getElementById('description').textContent = order.keterangan;
      document.getElementById('descriptionWrapper').classList.remove('hidden');
    } else {
      document.getElementById('descriptionWrapper').classList.add('hidden');
    }

    const confirmBtn = document.getElementById('confirmBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    confirmBtn.classList.add('hidden');
    cancelBtn.classList.add('hidden');

    if (status === 'pending') {
      cancelBtn.classList.remove('hidden');
    }
    
    if (status === 'on-process' || status === 'confirmed') {
      confirmBtn.classList.remove('hidden');
    }

    if (['assigned', 'on-process', 'confirmed'].includes(status) && order.assignment) {
      const driverInfo = document.getElementById('driverInfo');
      const waDriverWrapper = document.getElementById('waDriverWrapper');
      
      driverInfo.classList.remove('hidden');

      document.getElementById('driverName').textContent = order.assignment.sopir?.name || '-';

      const mobilInfo = order.assignment.mobil?.deskripsi || '';
      const mobilParts = mobilInfo.split(' - ');
      const mobilNama = mobilParts[0] || '-';
      const mobilPlat = mobilParts[1] || '-';

      document.getElementById('driverCar').textContent = mobilNama;
      document.getElementById('driverPlate').textContent = mobilPlat;

      if (order.assignment.sopir?.nomor) {
        const waBtn = document.getElementById('waDriver');
        const phone = cleanPhoneNumber(order.assignment.sopir.nomor);
        waBtn.href = `https://wa.me/${phone}`;
        waDriverWrapper.classList.remove('hidden');
      } else {
        waDriverWrapper.classList.add('hidden');
      }
    } else {
      document.getElementById('driverInfo').classList.add('hidden');
      document.getElementById('waDriverWrapper').classList.add('hidden');
    }

    if (status === 'completed') {
      console.log('✅ Order completed - redirecting...');
      setTimeout(() => {
        window.location.href = '/penumpang/riwayat';
      }, 1000);
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
      console.log('🔄 Auto refresh enabled for status:', status);
      autoRefreshInterval = setInterval(() => {
        console.log('🔄 Auto refresh triggered...');
        fetchOrderData(true);
      }, 15000);
    }
  }

  async function fetchOrderData(silent = false) {
    try {
      console.log('=== FETCH START (Attempt ' + (retryCount + 1) + ') ===');
      
      if (!silent) {
        document.getElementById('loadingAttempt').textContent = 
          retryCount > 0 ? `Percobaan ke-${retryCount + 1}...` : '';
      }
      
      if (!apiToken || apiToken === '') {
        throw new Error('Token tidak tersedia. Silakan login kembali.');
      }

      const newOrderId = sessionStorage.getItem('new_order_id');
      let targetOrder = null;

      if (newOrderId) {
        console.log('🎯 Fetching specific order:', newOrderId);
        await new Promise(resolve => setTimeout(resolve, 500));
        
        try {
          const detailUrl = `/api/penumpang/orders/${newOrderId}?token=${apiToken}`;
          const detailResponse = await fetch(detailUrl);

          if (detailResponse.ok) {
            const detailResult = await detailResponse.json();
            if (detailResult.status && detailResult.data) {
              targetOrder = detailResult.data;
              sessionStorage.removeItem('new_order_id');
              console.log('✅ Specific order found');
            }
          }
        } catch (err) {
          console.warn('⚠️ Failed to fetch specific order:', err.message);
        }
      }

      if (!targetOrder) {
        console.log('📋 Fetching ACTIVE order list...');
        
        const listUrl = `/api/penumpang/orders?token=${apiToken}`;
        const response = await fetch(listUrl);

        if (!response.ok) {
          if (response.status === 401) {
            throw new Error('Sesi berakhir. Silakan login kembali.');
          }
          throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();

        if (!result.status) {
          throw new Error(result.message || 'Gagal memuat data');
        }

        if (!result.data || result.data.length === 0) {
          throw new Error('Tidak ada pemesanan aktif. Buat pesanan baru atau cek riwayat.');
        }

        let activeOrder = result.data.find(order => order.status === 'pending');
        if (!activeOrder) activeOrder = result.data.find(order => order.status === 'assigned');
        if (!activeOrder) activeOrder = result.data.find(order => order.status === 'on-process');
        if (!activeOrder) activeOrder = result.data.find(order => order.status === 'confirmed');
        if (!activeOrder) activeOrder = result.data[0];

        const orderToFetch = activeOrder;
        const detailUrl = `/api/penumpang/orders/${orderToFetch.order_id}?token=${apiToken}`;
        const detailResponse = await fetch(detailUrl);

        if (!detailResponse.ok) {
          throw new Error(`Failed to fetch detail: ${detailResponse.status}`);
        }

        const detailResult = await detailResponse.json();

        if (!detailResult.status || !detailResult.data) {
          throw new Error('Detail data tidak lengkap');
        }

        targetOrder = detailResult.data;
      }

      if (targetOrder) {
        console.log('✅ SUCCESS - Updating UI');
        updateUI(targetOrder);
      } else {
        throw new Error('Data order tidak dapat dimuat');
      }

    } catch (error) {
      console.error('❌ FETCH ERROR:', error.message);
      
      if (!silent) {
        if (retryCount < MAX_RETRIES) {
          retryCount++;
          console.log(`⏳ Retrying in 1 second... (${retryCount}/${MAX_RETRIES})`);
          
          document.getElementById('loadingAttempt').textContent = 
            `Gagal memuat. Mencoba lagi... (${retryCount}/${MAX_RETRIES})`;
          
          setTimeout(() => {
            fetchOrderData(false);
          }, 1000);
        } else {
          showError(error.message);
        }
      }
    }
  }

  function showError(message) {
    document.getElementById('loadingContent').classList.add('hidden');
    document.getElementById('mainContent').classList.add('hidden');
    document.getElementById('errorContent').classList.remove('hidden');
    document.getElementById('errorMessage').textContent = message;
  }

  async function confirmArrival() {
    if (!currentOrder) return;
    
    showAlertModal(
      'Konfirmasi Pesanan',
      'Apakah Anda sudah sampai di tujuan?',
      'confirm'
    );
    
    window.confirmArrivalAction = async function() {
      closeAlertModal();
      showLoading();

      try {
        const response = await fetch(`/api/penumpang/confirm/${currentOrder.order_id}?token=${apiToken}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' }
        });

        const data = await response.json();
        hideLoading();

        if (data.status) {
          document.getElementById('mainContent').classList.add('hidden');
          showAlertModal('Berhasil!', data.message || 'Order berhasil dikonfirmasi!', 'success');
          
          setTimeout(() => {
            window.location.href = '/penumpang/riwayat';
          }, 800);
        } else {
          showAlertModal('Gagal', data.message || 'Gagal mengkonfirmasi order', 'error');
        }
      } catch (error) {
        hideLoading();
        showAlertModal('Kesalahan', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
      }
    };
  }
  window.confirmArrival = confirmArrival;

  async function cancelOrder() {
    if (!currentOrder) return;
    
    showAlertModal(
      'Batalkan Pemesanan',
      'Apakah Anda yakin ingin membatalkan pemesanan ini?',
      'confirm'
    );
    
    window.cancelOrderAction = async function() {
      closeAlertModal();
      showLoading();

      try {
        const response = await fetch(`/api/penumpang/cancel/${currentOrder.order_id}?token=${apiToken}`, {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' }
        });

        const data = await response.json();
        hideLoading();

        if (data.status) {
          document.getElementById('mainContent').classList.add('hidden');
          showAlertModal('Berhasil!', data.message || 'Pemesanan berhasil dibatalkan!', 'success');
          
          setTimeout(() => {
            window.location.href = "{{ url('/penumpang/riwayat') }}";
          }, 800);
        } else {
          showAlertModal('Gagal', data.message || 'Gagal membatalkan pemesanan', 'error');
        }
      } catch (error) {
        hideLoading();
        showAlertModal('Kesalahan', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
      }
    };
  }
  window.cancelOrder = cancelOrder;

  // Initialize
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      console.log('✨ DOM Ready - Starting fetch...');
      fetchOrderData(false);
    });
  } else {
    console.log('✨ DOM Already Ready - Starting fetch...');
    fetchOrderData(false);
  }

  // Cleanup on page unload
  window.addEventListener('beforeunload', () => {
    if (autoRefreshInterval) clearInterval(autoRefreshInterval);
  });
})();
</script>
@endpush