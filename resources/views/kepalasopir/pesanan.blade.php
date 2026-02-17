@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')

<!-- ================= TOAST NOTIFICATION (Z-INDEX LEBIH TINGGI) ================= -->
<div id="toast" class="fixed top-4 right-4 z-[60] transform translate-x-[400px] transition-all duration-300 ease-out">
  <div class="toast-container bg-white rounded-lg shadow-xl border-l-4 p-4 min-w-[300px] max-w-md">
    <div class="flex items-start gap-3">
      <div id="toastIcon" class="flex-shrink-0 w-6 h-6"></div>
      <div class="flex-1">
        <p id="toastTitle" class="font-semibold text-gray-900 mb-1"></p>
        <p id="toastMessage" class="text-sm text-gray-600"></p>
      </div>
      <button onclick="closeToast()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
  </div>
</div>

<!-- ================= CONTENT ================= -->
<div class="max-w-6xl space-y-6 md:space-y-8">

  <!-- ================= MAIN SECTION ================= -->
  <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4 md:p-6">
    
    <!-- FILTER STATUS -->
    <div class="mb-6 pb-4 border-b border-gray-200">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-base md:text-lg font-semibold text-gray-800">Status Pesanan</h3>
        <span id="orderCount" class="px-3 py-1.5 bg-gray-100 rounded-full text-xs font-semibold text-gray-600">
          0 Pesanan
        </span>
      </div>
      
      <div class="flex gap-3">
        <button onclick="filterByStatus('pending', this)" 
          id="btnPending"
          class="filter-btn flex-1 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all
                 bg-pertamina text-white shadow-sm">
          Pending
        </button>
        <button onclick="filterByStatus('assigned', this)" 
          id="btnAssigned"
          class="filter-btn flex-1 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all
                 bg-white border border-gray-300 text-gray-700 hover:bg-red-0">
          Ditugaskan
        </button>
      </div>
    </div>

    <!-- LOADING STATE -->
    <div id="loadingContent" class="text-center py-12">
      <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-pertamina mx-auto mb-4"></div>
      <p class="text-gray-600 font-medium">Memuat data pesanan...</p>
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
      <button onclick="location.reload()" 
        class="bg-pertamina text-white px-6 py-3 rounded-xl hover:bg-pertaminaDark transition-all shadow-sm font-semibold">
        Muat Ulang
      </button>
    </div>

    <!-- EMPTY STATE -->
    <div id="emptyState" class="hidden text-center py-16">
      <div class="bg-gray-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </div>
      <p class="text-gray-900 font-semibold mb-1">Belum ada pesanan</p>
      <p class="text-gray-500 text-sm"><span id="emptyStatusText" class="font-medium"></span></p>
    </div>

    <!-- ORDERS CONTAINER -->
    <div id="ordersContainer" class="hidden space-y-4">
      <!-- Orders will be rendered here -->
    </div>

  </section>

</div>

<!-- ================= MODAL ASSIGN ================= -->
<div id="assignModal"
  class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">

  <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
    <!-- Modal Header -->
    <div class="bg-gradient-to-r from-pertamina to-pertaminaDark text-white rounded-t-2xl p-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold">Tugaskan Pesanan</h2>
          <p class="text-sm opacity-90 mt-1" id="modalOrderId">#ORD-XXX</p>
        </div>
        <button onclick="closeAssignModal()" class="text-white/80 hover:text-white transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Modal Body -->
    <div class="p-6 space-y-5">
      <!-- SOPIR SELECT -->
      <div>
        <label class="block text-sm font-semibold text-gray-900 mb-2">
          Pilih Sopir <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <select id="sopirSelect" 
            class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm appearance-none
                   focus:ring-2 focus:ring-pertamina/30 focus:border-pertamina transition
                   bg-white cursor-pointer font-medium">
            <option value="">Pilih sopir yang tersedia</option>
          </select>
          <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </div>
        </div>
        <p id="sopirError" class="text-xs text-red-600 mt-2 hidden flex items-center gap-1">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          Silakan pilih sopir terlebih dahulu
        </p>
      </div>

      <!-- MOBIL SELECT -->
      <div>
        <label class="block text-sm font-semibold text-gray-900 mb-2">
          Pilih Mobil <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <select id="mobilSelect" 
            class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm appearance-none
                   focus:ring-2 focus:ring-pertamina/30 focus:border-pertamina transition
                   bg-white cursor-pointer font-medium">
            <option value="">Pilih mobil yang tersedia</option>
          </select>
          <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </div>
        </div>
        <p id="mobilError" class="text-xs text-red-600 mt-2 hidden flex items-center gap-1">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          Silakan pilih mobil terlebih dahulu
        </p>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="border-t border-gray-200 p-6 bg-gray-50 rounded-b-2xl flex gap-3">
      <button onclick="closeAssignModal()"
        class="flex-1 border-2 border-gray-300 rounded-xl py-3 text-sm font-semibold text-gray-700
               hover:bg-white transition-all">
        Batal
      </button>
      <button onclick="confirmAssign()"
        id="btnConfirmAssign"
        class="flex-1 bg-pertamina text-white rounded-xl py-3 text-sm font-semibold
               hover:bg-pertaminaDark transition-all shadow-sm hover:shadow-md
               disabled:opacity-50 disabled:cursor-not-allowed">
        <span id="btnAssignText">Tugaskan Sekarang</span>
        <span id="btnAssignLoader" class="hidden flex items-center justify-center gap-2">
          <svg class="inline w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Memproses...
        </span>
      </button>
    </div>
  </div>
</div>

<!-- ================= MODAL REJECT ================= -->
<div id="rejectModal"
  class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm px-4">

  <div class="bg-white rounded-2xl w-[90%] max-w-sm shadow-2xl">
    <div class="p-6">
      <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </div>
      
      <h2 class="text-xl font-bold text-gray-900 text-center mb-2">Tolak Pesanan?</h2>
      <p class="text-sm text-gray-600 text-center mb-6">
        Pesanan ini akan dibatalkan dan penumpang akan dinotifikasi
      </p>

      <div class="flex gap-3">
        <button onclick="closeRejectModal()"
          class="flex-1 border-2 border-gray-300 rounded-xl py-2.5 font-semibold text-gray-700
                 hover:bg-gray-50 transition">
          Batal
        </button>
        <button onclick="confirmReject()"
          id="btnConfirmReject"
          class="flex-1 bg-red-600 text-white rounded-xl py-2.5 font-semibold
                 hover:bg-red-700 transition shadow-sm
                 disabled:opacity-50 disabled:cursor-not-allowed">
          <span id="btnRejectText">Ya, Tolak</span>
          <span id="btnRejectLoader" class="hidden">Memproses...</span>
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
  'use strict';

  console.log('=== KEPALA SOPIR PESANAN PAGE ===');

  const API_BASE = '/api';
  const API_TOKEN = `{{ session('token') }}`;
  
  let currentOrders = [];
  let availableSopirs = [];
  let availableMobils = [];
  let allSopirs = [];
  let allMobils = [];
  let currentFilter = 'pending';
  let selectedOrderId = null;
  let autoRefreshInterval = null;

  /* ===============================
     TOAST NOTIFICATION
  ================================ */
  function showToast(title, message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastIcon = document.getElementById('toastIcon');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    const toastContainer = toast.querySelector('.toast-container');
    
    toastContainer.className = 'toast-container bg-white rounded-lg shadow-xl border-l-4 p-4 min-w-[300px] max-w-md';
    
    if (type === 'success') {
      toastContainer.classList.add('border-green-500');
      toastIcon.innerHTML = `
        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
      `;
    } else if (type === 'error') {
      toastContainer.classList.add('border-red-500');
      toastIcon.innerHTML = `
        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      `;
    } else if (type === 'warning') {
      toastContainer.classList.add('border-yellow-500');
      toastIcon.innerHTML = `
        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
      `;
    }
    
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    toast.style.transform = 'translateX(0)';
    
    setTimeout(() => {
      closeToast();
    }, 4000);
  }
  
  function closeToast() {
    const toast = document.getElementById('toast');
    toast.style.transform = 'translateX(400px)';
  }
  window.closeToast = closeToast;

  /* ===============================
     UTILITY FUNCTIONS
  ================================ */
  function formatDateTime(datetime) {
    if (!datetime) return '-';
    const date = new Date(datetime.replace(' ', 'T'));
    const tanggal = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    const jam = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
    return `${tanggal} ${jam}`;
  }

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
      },
      'canceled': {
        label: 'Dibatalkan',
        bgColor: 'bg-red-50',
        textColor: 'text-red-700',
        borderColor: 'border-red-200'
      },
      'rejected': {
        label: 'Ditolak',
        bgColor: 'bg-red-50',
        textColor: 'text-red-700',
        borderColor: 'border-red-200'
      }
    };
    
    return configs[status] || {
      label: status,
      bgColor: 'bg-gray-50',
      textColor: 'text-gray-700',
      borderColor: 'border-gray-200'
    };
  }

  /* ===============================
     FETCH DATA
  ================================ */
  async function loadOrders(status = 'pending') {
    showLoading(true);
    
    try {
      const url = `${API_BASE}/kepalasopir/order?status=${status}&token=${API_TOKEN}`;
      console.log('🔍 Fetching URL:', url);
      
      const response = await fetch(url);
      console.log('📡 Response Status:', response.status);
      
      if (!response.ok) {
        const errorText = await response.text();
        console.error('❌ Response error:', errorText);
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const result = await response.json();
      console.log('📦 Full Response:', result);
      
      if (result.status) {
        currentOrders = result.data || [];
        console.log('✅ Orders loaded:', currentOrders.length, 'orders');
        
        document.getElementById('orderCount').textContent = `${currentOrders.length} Pesanan`;
        
        renderOrders(currentOrders);
      } else {
        console.error('❌ API returned false status');
        showToast('Gagal Memuat Data', result.message || 'Terjadi kesalahan', 'error');
        renderOrders([]);
      }
      
    } catch (error) {
      console.error('❌ FETCH ERROR:', error);
      showError(error.message || 'Tidak dapat terhubung ke server');
    } finally {
      showLoading(false);
    }
  }

async function loadSopirs() {
    try {
        console.log('🔍 Loading sopirs...');
        const response = await fetch(`${API_BASE}/kepalasopir/sopir?token=${API_TOKEN}`);
        const result = await response.json();
        
        console.log('📦 Sopir Response:', result);
        
        if (result.status) {
            // ✅ Langsung pakai data dari API
            availableSopirs = result.data || [];
            console.log('✅ Available sopirs:', availableSopirs.length);
            
            populateSopirSelect();
        }
    } catch (error) {
        console.error('❌ Error loading sopirs:', error);
        showToast('Gagal Memuat Sopir', 'Tidak dapat memuat data sopir', 'error');
    }
}

async function loadMobils() {
    try {
        console.log('🔍 Loading mobils...');
        const response = await fetch(`${API_BASE}/kepalasopir/mobil?token=${API_TOKEN}`);
        const result = await response.json();
        
        console.log('📦 Mobil Response:', result);
        
        if (result.status) {
            // ✅ Langsung pakai data dari API
            availableMobils = result.data || [];
            console.log('✅ Available mobils:', availableMobils.length);
            
            populateMobilSelect();
        }
    } catch (error) {
        console.error('❌ Error loading mobils:', error);
        showToast('Gagal Memuat Mobil', 'Tidak dapat memuat data mobil', 'error');
    }
}
  /* ===============================
     RENDER FUNCTIONS
  ================================ */
  function renderOrders(orders) {
    const container = document.getElementById('ordersContainer');
    const emptyState = document.getElementById('emptyState');
    const emptyStatusText = document.getElementById('emptyStatusText');
    
    console.log('🎨 Rendering', orders.length, 'orders');
    
    
    if (!orders || orders.length === 0) {
      container.innerHTML = '';
      container.classList.add('hidden');
      emptyState.classList.remove('hidden');
      return;
    }
    
    emptyState.classList.add('hidden');
    container.classList.remove('hidden');
    
    container.innerHTML = orders.map(order => {
      const isPending = order.status === 'pending';
      const statusConfig = getStatusConfig(order.status);
      
      let penumpangName = order.penumpang?.name || '-';
      const penumpangNomor = order.penumpang?.nomor || null;
      
      const sopirName = order.assignment?.sopir?.name || order.assignment?.sopir?.nama || 'Belum ditugaskan';
      const sopirNomor = order.assignment?.sopir?.nomor || null;
      const mobilDesc = order.assignment?.mobil?.deskripsi || 'Belum ditugaskan';
      
      const waktuPenjemputan = new Date(order.waktu_penjemputan);
      const tanggal = waktuPenjemputan.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
      const waktu = waktuPenjemputan.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
      
      return `
        <div class="border border-gray-200 rounded-lg md:rounded-xl p-4 md:p-5 transition-all">

          <!-- Header -->
          <div class="flex justify-between items-start mb-4">
            <div>
              <p class="text-xs text-gray-500">
                ID Pemesanan <span class="font-semibold text-gray-700">#${order.order_id}</span>
              </p>
              <p class="text-xs text-gray-500">
                Dibuat <span class="font-medium text-green-500">${formatDateTime(order.dibuat_pada || order.created_at)}</span>
              </p>
              <p class="text-xs text-gray-500 mb-1">
                Diperbarui <span class="font-medium text-red-500">${formatDateTime(order.diupdate_pada || order.updated_at)}</span>
              </p>
              <p class="font-semibold text-gray-800 text-sm md:text-base">${penumpangName}</p>
            </div>

          <span class="inline-flex items-center text-xs px-3 py-1 rounded-full 
            font-semibold whitespace-nowrap
            ${statusConfig.bgColor} ${statusConfig.textColor} border ${statusConfig.borderColor}">
            ${statusConfig.label}
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
                <p class="text-xs font-semibold text-gray-700 mb-1">Tempat Penjemputan</p>
                <p class="text-xs md:text-sm text-gray-600">${order.tempat_penjemputan}</p>
                <p class="text-xs text-blue-500">
                  <span class="font-medium">${tanggal} • ${waktu}</span>
                </p>
              </div>

              <div>
                <p class="text-xs font-semibold text-gray-700 mb-1">Tempat Tujuan</p>
                <p class="text-xs md:text-sm text-gray-600">${order.tempat_tujuan}</p>
              </div>
            </div>
          </div>

          ${order.keterangan && order.keterangan !== '-' ? `
          <div class="pt-3 space-y-2 mb-4">
            <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-200">
              <p class="text-xs font-semibold text-gray-500 mb-1">Keterangan</p>
              <p class="text-xs md:text-sm text-gray-700">${order.keterangan}</p>
            </div>
          </div>
          ` : ''}

          ${!isPending ? `
          <!-- Sopir & Mobil Info -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <p class="text-xs font-semibold text-blue-700 mb-1">
                  <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                  </svg>
                  Sopir
                </p>
                <p class="text-xs text-gray-700">${sopirName}</p>
              </div>
              <div>
                <p class="text-xs font-semibold text-blue-700 mb-1">
                  <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                  </svg>
                  Mobil
                </p>
                <p class="text-xs text-gray-700">${mobilDesc}</p>
              </div>
            </div>
          </div>
          ` : ''}

          <!-- ACTION BUTTONS -->
          ${isPending ? `
          <div class="border-t border-gray-100 pt-3 flex gap-3">
            <button onclick="openRejectModal(${order.order_id})"
              class="flex-1 flex items-center justify-center gap-2 border-2 border-red-200 text-red-600
                     rounded-xl py-2.5 text-center font-semibold text-sm
                     hover:bg-red-50 hover:border-red-300 transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
              Tolak
            </button>

            <button onclick="openAssignModal(${order.order_id})"
              class="flex-1 flex items-center justify-center gap-2 bg-pertamina text-white
                     rounded-xl py-2.5 font-semibold text-sm
                     hover:bg-pertaminaDark transition-all shadow-sm hover:shadow-md">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
              </svg>
              Tugaskan
            </button>
          </div>
          ` : `
          <div class="border-t border-gray-100 pt-3 grid grid-cols-2 gap-3">
            ${penumpangNomor ? `
              <a href="https://wa.me/${penumpangNomor}?text=Halo%20${encodeURIComponent(penumpangName)},%20saya%20dari%20tim%20SIKAR."
                 target="_blank"
                 class="flex items-center justify-center gap-2 border-2 border-green-200 text-green-700
                        rounded-xl py-2.5 text-center font-semibold text-sm
                        hover:bg-green-50 hover:border-green-300 transition-all">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path>
                </svg>
                Penumpang
              </a>
            ` : ''}

            ${sopirNomor ? `
              <a href="https://wa.me/${sopirNomor}?text=Halo%20${encodeURIComponent(sopirName)},%20ini%20tentang%20order%20%23${order.order_id}."
                 target="_blank"
                 class="flex items-center justify-center gap-2 border-2 border-blue-200 text-blue-700
                        rounded-xl py-2.5 text-center font-semibold text-sm
                        hover:bg-blue-50 hover:border-blue-300 transition-all">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path>
                </svg>
                Sopir
              </a>
            ` : ''}
          </div>
          `}

        </div>
      `;
    }).join('');
  }

  function populateSopirSelect() {
    const select = document.getElementById('sopirSelect');
    select.innerHTML = '<option value="">Pilih sopir yang tersedia</option>';
    
    if (!availableSopirs || availableSopirs.length === 0) {
      select.innerHTML = '<option value="">Tidak ada sopir tersedia</option>';
      return;
    }
    
    availableSopirs.forEach(sopir => {
      const option = document.createElement('option');
      option.value = sopir.sopir_id;
      option.textContent = sopir.name;
      select.appendChild(option);
    });
    
    console.log('✅ Sopir select populated with', availableSopirs.length, 'options');
  }

  function populateMobilSelect() {
    const select = document.getElementById('mobilSelect');
    select.innerHTML = '<option value="">Pilih mobil yang tersedia</option>';
    
    if (!availableMobils || availableMobils.length === 0) {
      select.innerHTML = '<option value="">Tidak ada mobil tersedia</option>';
      return;
    }
    
    availableMobils.forEach(mobil => {
      const option = document.createElement('option');
      option.value = mobil.mobil_id;
      option.textContent = mobil.deskripsi;
      select.appendChild(option);
    });
    
    console.log('✅ Mobil select populated with', availableMobils.length, 'options');
  }

  /* ===============================
     MODAL FUNCTIONS
  ================================ */
  function openAssignModal(orderId) {
    selectedOrderId = orderId;
    const order = currentOrders.find(o => o.order_id === orderId);
    
    if (order) {
      document.getElementById('modalOrderId').textContent = `#${order.order_id}`;
    }
    
    document.getElementById('sopirSelect').value = '';
    document.getElementById('mobilSelect').value = '';
    document.getElementById('sopirError').classList.add('hidden');
    document.getElementById('mobilError').classList.add('hidden');
    
    loadSopirs();
    loadMobils();
    
    document.getElementById('assignModal').classList.remove('hidden');
    
    console.log('📝 Modal opened for order:', orderId);
  }
  window.openAssignModal = openAssignModal;

  function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    selectedOrderId = null;
  }
  window.closeAssignModal = closeAssignModal;

  function openRejectModal(orderId) {
    selectedOrderId = orderId;
    document.getElementById('rejectModal').classList.remove('hidden');
  }
  window.openRejectModal = openRejectModal;

  function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    selectedOrderId = null;
  }
  window.closeRejectModal = closeRejectModal;

  /* ===============================
     API ACTIONS
  ================================ */
  async function confirmAssign() {
    const sopirSelect = document.getElementById('sopirSelect');
    const mobilSelect = document.getElementById('mobilSelect');
    const sopirId = sopirSelect.value;
    const mobilId = mobilSelect.value;
    
    const btnConfirm = document.getElementById('btnConfirmAssign');
    const btnText = document.getElementById('btnAssignText');
    const btnLoader = document.getElementById('btnAssignLoader');
    
    let hasError = false;
    
    if (!sopirId || sopirId === '' || sopirId === 'Tidak ada sopir tersedia') {
      document.getElementById('sopirError').classList.remove('hidden');
      hasError = true;
    } else {
      document.getElementById('sopirError').classList.add('hidden');
    }
    
    if (!mobilId || mobilId === '' || mobilId === 'Tidak ada mobil tersedia') {
      document.getElementById('mobilError').classList.remove('hidden');
      hasError = true;
    } else {
      document.getElementById('mobilError').classList.add('hidden');
    }
    
    if (hasError) {
      return;
    }
    
    if (!selectedOrderId) {
      showToast('Error', 'Order ID tidak valid', 'error');
      return;
    }
    
    btnConfirm.disabled = true;
    btnText.classList.add('hidden');
    btnLoader.classList.remove('hidden');
    
    try {
      const parsedSopirId = parseInt(sopirId, 10);

      if (isNaN(parsedSopirId)) {
        throw new Error(`Sopir ID tidak valid`);
      }
            
      const payload = {
        order_id: selectedOrderId,
        sopir_id: parsedSopirId,
        mobil_id: mobilId,
        token: API_TOKEN
      };
      
      const response = await fetch(`${API_BASE}/kepalasopir/assign`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });
      
      const result = await response.json();
      
      if (result.status) {
        closeAssignModal();
        showToast('Berhasil!', 'Pesanan berhasil ditugaskan ke sopir dan mobil', 'success');
        
        setTimeout(() => {
          loadOrders(currentFilter);
        }, 500);
      } else {
        closeAssignModal();
        const errorMsg = result.message || 'Terjadi kesalahan saat menugaskan pesanan';
        if (result.errors) {
          const errorDetails = Object.values(result.errors).flat().join(', ');
          showToast('Gagal Menugaskan', `${errorMsg}: ${errorDetails}`, 'error');
        } else {
          showToast('Gagal Menugaskan', errorMsg, 'error');
        }
      }
    } catch (error) {
      console.error('❌ Error assigning order:', error);
      closeAssignModal();
      showToast('Kesalahan', error.message || 'Tidak dapat terhubung ke server', 'error');
    } finally {
      btnConfirm.disabled = false;
      btnText.classList.remove('hidden');
      btnLoader.classList.add('hidden');
    }
  }
  window.confirmAssign = confirmAssign;

  async function confirmReject() {
    const btnReject = document.getElementById('btnConfirmReject');
    const btnText = document.getElementById('btnRejectText');
    const btnLoader = document.getElementById('btnRejectLoader');
    
    if (!selectedOrderId) {
      showToast('Error', 'Order ID tidak valid', 'error');
      return;
    }
    
    btnReject.disabled = true;
    btnText.classList.add('hidden');
    btnLoader.classList.remove('hidden');
    
    try {
      const url = `${API_BASE}/kepalasopir/reject/${selectedOrderId}?token=${API_TOKEN}`;
      
      const response = await fetch(url, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      });
      
      const result = await response.json();
      
      if (result.status) {
        closeRejectModal();
        showToast('Berhasil!', 'Pesanan berhasil ditolak', 'success');
        
        setTimeout(() => {
          loadOrders(currentFilter);
        }, 500);
      } else {
        closeRejectModal();
        showToast('Gagal Menolak', result.message || 'Terjadi kesalahan saat menolak pesanan', 'error');
      }
    } catch (error) {
      console.error('❌ Error rejecting order:', error);
      closeRejectModal();
      showToast('Kesalahan Jaringan', 'Tidak dapat terhubung ke server', 'error');
    } finally {
      btnReject.disabled = false;
      btnText.classList.remove('hidden');
      btnLoader.classList.add('hidden');
    }
  }
  window.confirmReject = confirmReject;

  /* ===============================
     FILTER
  ================================ */
function filterByStatus(status, element) {
  currentFilter = status;
  
  const filterButtons = document.querySelectorAll('.filter-btn');
  filterButtons.forEach(btn => {
    btn.classList.remove('bg-pertamina', 'text-white', 'shadow-sm');
    btn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-300');
  });
  
  if (element) {
    element.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-300');
    element.classList.add('bg-pertamina', 'text-white', 'shadow-sm');
  }
  
  loadOrders(status);
}
window.filterByStatus = filterByStatus;

  /* ===============================
     AUTO REFRESH
  ================================ */
  function startAutoRefresh() {
    if (autoRefreshInterval) {
      clearInterval(autoRefreshInterval);
    }
    
    autoRefreshInterval = setInterval(() => {
      loadOrders(currentFilter);
    }, 30000);
  }

  function stopAutoRefresh() {
    if (autoRefreshInterval) {
      clearInterval(autoRefreshInterval);
      autoRefreshInterval = null;
    }
  }

  /* ===============================
     SHOW/HIDE FUNCTIONS
  ================================ */
  function showLoading(show) {
    const loading = document.getElementById('loadingContent');
    const error = document.getElementById('errorContent');
    const container = document.getElementById('ordersContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (show) {
      loading.classList.remove('hidden');
      error.classList.add('hidden');
      container.classList.add('hidden');
      emptyState.classList.add('hidden');
    } else {
      loading.classList.add('hidden');
    }
  }

  function showError(message) {
    const loading = document.getElementById('loadingContent');
    const error = document.getElementById('errorContent');
    const errorMessage = document.getElementById('errorMessage');
    const container = document.getElementById('ordersContainer');
    const emptyState = document.getElementById('emptyState');
    
    loading.classList.add('hidden');
    container.classList.add('hidden');
    emptyState.classList.add('hidden');
    error.classList.remove('hidden');
    errorMessage.textContent = message;
  }

  /* ===============================
     INIT
  ================================ */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      loadOrders(currentFilter);
      startAutoRefresh();
    });
  } else {
    loadOrders(currentFilter);
    startAutoRefresh();
  }

  window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
  });
})();
</script>
@endpush