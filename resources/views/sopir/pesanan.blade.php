@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
<div class="max-w-6xl space-y-6 md:space-y-8">

    <!-- ================= TOGGLE KEHADIRAN ================= -->
    <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4 md:p-6">
        <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-3 border border-gray-200">
            <div class="flex-1">
                <p class="text-xs font-semibold text-gray-700">Status Kehadiran</p>
                <p id="statusText" class="text-xs text-gray-500">Memuat...</p>
            </div>
            
            <button id="toggleKehadiran" 
                    class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors duration-300
                           bg-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <span id="toggleCircle" 
                      class="inline-block h-6 w-6 transform rounded-full bg-white shadow-lg transition-transform duration-300
                             translate-x-1"></span>
            </button>
        </div>
    </section>

    <!-- ================= ORDER LIST ================= -->
    <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4 md:p-6">
        <!-- Loading Skeleton -->
        <div id="loadingSkeleton" class="space-y-4">
            <div class="animate-pulse">
                <div class="h-40 bg-gray-200 rounded-lg mb-4"></div>
                <div class="h-40 bg-gray-200 rounded-lg mb-4"></div>
                <div class="h-40 bg-gray-200 rounded-lg"></div>
            </div>
        </div>

        <!-- Order List Container -->
        <div id="orderList" class="space-y-4 hidden"></div>

        <!-- Empty State -->
        <div id="emptyState" class="hidden text-center py-12">
            <svg class="w-10 h-10 md:w-12 md:h-12 mx-auto text-gray-300 mb-3"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>

            <h3 class="font-semibold text-sm md:text-base text-gray-700 mb-2">
                Belum ada pesanan aktif
            </h3>
        </div>
    </section>

</div>

<!-- ================= MODAL KONFIRMASI ================= -->
<div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl md:rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
        <div class="text-center mb-6">
            <div id="modalIcon" class="mx-auto mb-4"></div>
            <h3 id="modalTitle" class="text-lg font-bold text-gray-800 mb-2"></h3>
            <p id="modalMessage" class="text-sm text-gray-600"></p>
        </div>
        
        <div class="flex gap-3">
            <button id="cancelBtn" 
                    class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg
                           bg-gray-100 hover:bg-gray-200 text-gray-700
                           transition-all duration-300">
                Tidak
            </button>
            <button id="confirmBtn" 
                    class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg
                           text-white transition-all duration-300 shadow-sm">
                Iya
            </button>
        </div>
    </div>
</div>

<script>
// ================= CONSTANTS =================
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const authToken = "{{ session('token') }}";
const API_URL = "{{ url('/api/sopir/orders') }}";
const TOGGLE_KERJA_URL = "{{ url('/api/sopir/kerja') }}";
const START_ORDER_URL = "{{ url('/api/sopir/start') }}";
const COMPLETE_ORDER_URL = "{{ url('/api/sopir/complete') }}";

let activeOrders = [];
let isKehadiranActive = false; // Status kehadiran sopir
let sopirData = null; // Data sopir dari API

// ================= HELPER FUNCTIONS =================
function formatDateTime(datetime) {
    if (!datetime) return '-';

    const date = new Date(datetime.replace(' ', 'T'));

    const tanggal = date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });

    const jam = date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });

    return `${tanggal} ${jam}`;
}

// ================= STATUS CONFIG =================
function getStatusConfig(status) {
    const configs = {
        'assigned': {
            label: 'Ditugaskan',
            bgColor: 'bg-purple-50',
            textColor: 'text-purple-700',
            borderColor: 'border-purple-200'
        },
        'on-process': {
            label: 'Diperjalanan',
            bgColor: 'bg-green-50',
            textColor: 'text-green-700',
            borderColor: 'border-green-200'
        },
        'confirmed': {
            label: 'Dikonfirmasi',
            bgColor: 'bg-blue-50',
            textColor: 'text-blue-700',
            borderColor: 'border-blue-200'
        }
    };
    
    return configs[status] || {
        label: status,
        bgColor: 'bg-gray-50',
        textColor: 'text-gray-700',
        borderColor: 'border-gray-200'
    };
}

// ================= TOGGLE KEHADIRAN =================
async function fetchStatusKerja() {
    try {
        const response = await fetch(TOGGLE_KERJA_URL, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Gagal mengambil status kerja');
        }

        const result = await response.json();
        
        if (result.status && result.data) {
            sopirData = result.data;
            isKehadiranActive = result.data.masuk_kerja === 1;
            updateToggleUI();
        }
    } catch (error) {
        console.error('Error fetch status kerja:', error);
        // Jika error, set default ke OFF
        isKehadiranActive = false;
        updateToggleUI();
    }
}

async function toggleStatusKerja() {
    try {
        const response = await fetch(TOGGLE_KERJA_URL, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();
        
        if (!response.ok) {
            // Tampilkan error dari backend
            throw new Error(result.message || 'Gagal mengubah status kerja');
        }

        if (result.status && result.data) {
            sopirData = result.data;
            isKehadiranActive = result.data.masuk_kerja === 1;
            updateToggleUI();
            
            // Tampilkan notifikasi
            showNotification(result.message, isKehadiranActive ? 'success' : 'info');
            
            // Refresh orders setelah toggle
            fetchOrders();
        } else {
            showNotification(result.message || 'Gagal mengubah status', 'error');
        }
    } catch (error) {
        console.error('Error toggle status kerja:', error);
        showNotification(error.message || 'Terjadi kesalahan saat mengubah status', 'error');
    }
}

function updateToggleUI() {
    const toggleBtn = document.getElementById('toggleKehadiran');
    const toggleCircle = document.getElementById('toggleCircle');
    const statusText = document.getElementById('statusText');
    
    if (isKehadiranActive) {
        toggleBtn.classList.remove('bg-gray-300');
        toggleBtn.classList.add('bg-green-500');
        toggleCircle.classList.remove('translate-x-1');
        toggleCircle.classList.add('translate-x-7');
        statusText.textContent = 'Sedang Bekerja';
        statusText.classList.remove('text-gray-500');
        statusText.classList.add('text-green-600');
    } else {
        toggleBtn.classList.remove('bg-green-500');
        toggleBtn.classList.add('bg-gray-300');
        toggleCircle.classList.remove('translate-x-7');
        toggleCircle.classList.add('translate-x-1');
        statusText.textContent = 'Sedang Tidak Bekerja';
        statusText.classList.remove('text-green-600');
        statusText.classList.add('text-gray-500');
    }
}

function showNotification(message, type = 'info') {
    // Buat elemen notifikasi
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-5 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    
    let bgColor = 'bg-blue-500';
    let icon = '';
    
    if (type === 'success') {
        bgColor = 'bg-green-500';
        icon = `<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>`;
    } else if (type === 'error') {
        bgColor = 'bg-red-500';
        icon = `<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>`;
    } else {
        bgColor = 'bg-blue-500';
        icon = `<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
    }
    
    notification.className += ` ${bgColor}`;
    notification.innerHTML = `
        <div class="flex items-center gap-3 text-white">
            ${icon}
            <span class="font-medium">${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animasi masuk
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);
    
    // Hapus setelah 3 detik
    setTimeout(() => {
        notification.classList.remove('translate-x-0');
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function showConfirmModal(willActivate) {
    const modal = document.getElementById('confirmModal');
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const confirmBtn = document.getElementById('confirmBtn');
    
    if (willActivate) {
        modalIcon.innerHTML = `
            <svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        `;
        modalTitle.textContent = 'Mulai Bekerja?';
        modalMessage.textContent = 'Jumlah pesanan hari ini akan mulai dihitung';
        confirmBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
        confirmBtn.classList.add('bg-green-500', 'hover:bg-green-600');
    } else {
        modalIcon.innerHTML = `
            <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        `;
        modalTitle.textContent = 'Berhenti Bekerja?';
        modalMessage.textContent = 'Jumlah pesanan hari ini akan dikembalikan ke 0';
        confirmBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
        confirmBtn.classList.add('bg-red-500', 'hover:bg-red-600');
    }
    
    modal.classList.remove('hidden');
}

function hideConfirmModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.add('hidden');
}

// ================= FETCH DATA =================
async function fetchOrders() {
    showLoading(true);
    
    try {
        const response = await fetch(`${API_URL}`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Gagal mengambil data');
        }

        const result = await response.json();
        
        if (result.status) {
            activeOrders = result.data || [];
            renderOrders();
        } else {
            showError(result.message || 'Gagal memuat data');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Terjadi kesalahan saat memuat data');
    } finally {
        showLoading(false);
    }
}

async function startOrder(orderId) {
    try {
        const response = await fetch(`${START_ORDER_URL}/${orderId}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal memulai perjalanan');
        }

        showNotification(result.message || 'Perjalanan dimulai', 'success');
        fetchOrders();

    } catch (error) {
        showNotification(error.message, 'error');
    }
}

async function completeOrder(orderId) {
    try {
        const response = await fetch(`${COMPLETE_ORDER_URL}/${orderId}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal menyelesaikan order');
        }

        showNotification(result.message || 'Order selesai', 'success');
        fetchOrders();

    } catch (error) {
        showNotification(error.message, 'error');
    }
}


// ================= CREATE ORDER CARD =================
function createOrderCard(order) {
    const statusConfig = getStatusConfig(order.status);
    const waktuPenjemputan = formatDateTime(order.waktu_penjemputan);
    const waktuDiperbarui = formatDateTime(order.updated_at);
    const waktuPembuatan = formatDateTime(order.created_at);
    
    // Data sopir dan mobil dari assignment
    const sopirName = order.assignment?.sopir?.name || '-';
    const sopirId = order.assignment?.sopir?.pengguna_id || '-';
    const mobilId = order.assignment?.mobil_id || '-';
    const mobilDeskripsi = order.assignment?.mobil?.deskripsi || '-';

    return `
        <div class="border border-gray-200 rounded-lg md:rounded-xl p-4 md:p-5 transition-all hover:shadow-md">

            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs text-gray-500">
                        ID Pesanan <span class="font-semibold text-gray-700">#${order.order_id}</span>
                    </p>
                    <p class="text-xs text-gray-500">
                        Dibuat <span class="font-medium text-green-500">${waktuPembuatan}</span>
                    </p>
                    <p class="text-xs text-gray-500 mb-1">
                        Diperbarui <span class="font-medium text-red-500">${waktuDiperbarui}</span>
                    </p>
                    <p class="font-semibold text-gray-800 text-sm md:text-base">
                        ${order.penumpang?.name || '-'}
                    </p>
                </div>

                <span class="text-xs px-3 py-1.5 rounded-full font-semibold
                             ${statusConfig.bgColor} ${statusConfig.textColor}
                             border ${statusConfig.borderColor}">
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
                        <p class="text-xs font-semibold text-gray-700 mb-1">
                            Tempat Penjemputan
                        </p>
                        <p class="text-xs md:text-sm text-gray-600">
                            ${order.tempat_penjemputan}
                        </p>
                        <p class="text-xs text-blue-500">
                            <span class="font-medium">${waktuPenjemputan}</span>
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-700 mb-1">
                            Tempat Tujuan
                        </p>
                        <p class="text-xs md:text-sm text-gray-600">
                            ${order.tempat_tujuan}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="pt-3 space-y-2 mb-4">
                <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-200">
                    <p class="text-xs font-semibold text-gray-500 mb-1">Keterangan</p>
                    <p class="text-xs md:text-sm text-gray-700">
                        ${order.keterangan || '-'}
                    </p>
                </div>
            </div>

            <!-- Sopir & Mobil Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <p class="text-xs font-semibold text-blue-700 mb-1">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                            </svg>
                            Mobil
                        </p>
                        <p class="text-xs text-gray-700">
                            ${mobilId}
                        </p>
                        <p class="text-xs text-gray-500">
                            ${mobilDeskripsi}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-100 pt-3 space-y-2">

                <!-- ACTION BUTTON -->
                ${
                    order.status === 'assigned'
                    ? `
                        <button onclick="startOrder(${order.order_id})"
                            class="w-full bg-purple-500 text-white
                                font-semibold text-sm py-2.5 rounded-lg transition-all">
                            Mulai Perjalanan
                        </button>
                    `
                    : order.status === 'confirmed'
                    ? `
                        <button onclick="completeOrder(${order.order_id})"
                            class="w-full bg-blue-500 text-white
                                font-semibold text-sm py-2.5 rounded-lg transition-all">
                            Pesanan Selesai
                        </button>
                    `
                    : ''
                }

                <!-- WHATSAPP -->
                <a href="https://wa.me/${order.penumpang?.nomor || ''}" 
                target="_blank"
                class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 
                        text-white font-semibold text-sm py-2.5 rounded-lg transition-all duration-300
                        shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">...</svg>
                    Hubungi Penumpang
                </a>

            </div>

        </div>
    `;
}

// ================= RENDER ORDERS =================
function renderOrders() {
    const container = document.getElementById('orderList');
    const emptyState = document.getElementById('emptyState');
    
    container.innerHTML = '';

    if (activeOrders.length === 0) {
        container.classList.add('hidden');
        emptyState.classList.remove('hidden');
        return;
    }

    container.classList.remove('hidden');
    emptyState.classList.add('hidden');

    activeOrders.forEach(order => {
        const card = createOrderCard(order);
        container.innerHTML += card;
    });
}

// ================= LOADING & ERROR =================
function showLoading(show) {
    const loadingSkeleton = document.getElementById('loadingSkeleton');
    const orderList = document.getElementById('orderList');
    const emptyState = document.getElementById('emptyState');
    
    if (show) {
        loadingSkeleton.classList.remove('hidden');
        orderList.classList.add('hidden');
        emptyState.classList.add('hidden');
    } else {
        loadingSkeleton.classList.add('hidden');
    }
}

function showError(message) {
    const container = document.getElementById('orderList');
    container.innerHTML = `
        <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
            <svg class="w-10 h-10 md:w-12 md:h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-red-700 font-semibold text-sm md:text-base">${message}</p>
        </div>
    `;
    container.classList.remove('hidden');
    document.getElementById('emptyState').classList.add('hidden');
}

// ================= EVENT LISTENERS =================
document.getElementById('toggleKehadiran').onclick = () => {
    const willActivate = !isKehadiranActive;
    showConfirmModal(willActivate);
};

document.getElementById('cancelBtn').onclick = () => {
    hideConfirmModal();
};

document.getElementById('confirmBtn').onclick = async () => {
    hideConfirmModal();
    await toggleStatusKerja();
};

// Close modal when clicking outside
document.getElementById('confirmModal').onclick = (e) => {
    if (e.target.id === 'confirmModal') {
        hideConfirmModal();
    }
};

// ================= INIT =================
document.addEventListener('DOMContentLoaded', function() {
    // Fetch status kerja dari backend saat halaman dimuat
    fetchStatusKerja();
    
    // Fetch orders pertama kali
    fetchOrders();
    
    // Auto refresh setiap 5 menit
    setInterval(fetchOrders, 300000);
});
</script>
@endsection