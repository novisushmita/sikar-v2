@extends('layouts.app')

@section('title', 'Riwayat')

@section('content')
<div class="max-w-6xl space-y-6 md:space-y-8">

    <!-- ================= FILTER SECTION ================= -->
    <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-base md:text-lg font-semibold">Riwayat Pemesanan</h2>

            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Filter Tanggal Range -->
                <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center">
                    <div class="relative w-full sm:w-auto">
                        <label class="text-xs text-gray-600 mb-1 block sm:hidden">Dari Tanggal</label>
                        <input type="date"
                               id="dateFrom"
                               class="w-full sm:w-auto border-2 border-gray-200 rounded-lg px-4 py-2 text-sm
                                      bg-white shadow-sm focus:ring-2 focus:ring-blue-500
                                      focus:border-blue-500 transition-all cursor-pointer">
                    </div>
                    
                    <span class="hidden sm:block text-gray-500 font-semibold">-</span>
                    
                    <div class="relative w-full sm:w-auto">
                        <label class="text-xs text-gray-600 mb-1 block sm:hidden">Sampai Tanggal</label>
                        <input type="date"
                               id="dateTo"
                               class="w-full sm:w-auto border-2 border-gray-200 rounded-lg px-4 py-2 text-sm
                                      bg-white shadow-sm focus:ring-2 focus:ring-blue-500
                                      focus:border-blue-500 transition-all cursor-pointer">
                    </div>
                </div>

                <!-- Tombol Reset -->
                <button id="resetFilter"
                        class="px-4 py-2 text-sm font-medium rounded-lg
                               bg-gray-100 text-gray-700 hover:bg-gray-200
                               transition-all duration-300 shadow-sm">
                    Hapus Filter
                </button>

                <!-- Tombol Export -->
                <button id="exportBtn"
                        class="px-4 py-2 text-sm font-medium rounded-lg
                               bg-green-600 text-white hover:bg-green-700
                               transition-all duration-300 shadow-sm
                               flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ekspor Excel
                </button>
            </div>
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

        <!-- Empty State - Hari Ini -->
        <div id="emptyStateToday" class="hidden text-center py-12">
            <svg class="w-10 h-10 md:w-12 md:h-12 mx-auto text-gray-300 mb-3"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>

            <h3 class="font-semibold text-sm md:text-base text-gray-700">
                Belum ada pemesanan hari ini
            </h3>
        </div>

        <!-- Empty State - Filter Tanggal -->
        <div id="emptyStateFiltered" class="hidden text-center py-12">
            <svg class="w-10 h-10 md:w-12 md:h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="font-semibold text-sm md:text-base text-gray-700">
                Tidak ada pemesanan pada rentang tanggal tersebut
            </h3>
            <p class="text-xs text-gray-500 mt-2" id="dateRangeInfo"></p>
        </div>
    </section>

    <!-- ================= PAGINATION ================= -->
    <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4">
        <div class="flex justify-between items-center">
            <button id="prevPage"
                    class="px-4 md:px-5 py-2 md:py-2.5 text-sm font-semibold rounded-lg
                           bg-gray-100 hover:bg-gray-200 text-gray-700
                           disabled:opacity-40 disabled:cursor-not-allowed
                           transition-all duration-300"
                    disabled>
                ← Sebelumnya
            </button>

            <span class="text-xs md:text-sm font-semibold text-gray-600">
                <span id="currentPage">1</span> / <span id="totalPages">1</span>
            </span>

            <button id="nextPage"
                    class="px-4 md:px-5 py-2 md:py-2.5 text-sm font-semibold rounded-lg
                           bg-gray-100 hover:bg-gray-200 text-gray-700
                           disabled:opacity-40 disabled:cursor-not-allowed
                           transition-all duration-300">
                Selanjutnya →
            </button>
        </div>
    </section>

</div>

<!-- ================= MODAL KONFIRMASI EXPORT ================= -->
<div id="confirmExportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl md:rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
        <div class="text-center mb-6">
            <div class="mx-auto mb-4">
                <svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Ekspor Data?</h3>
            <p id="exportModalMessage" class="text-sm text-gray-600"></p>
        </div>
        
        <div class="flex gap-3">
            <button id="cancelExportBtn" 
                    class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg
                           bg-gray-100 hover:bg-gray-200 text-gray-700
                           transition-all duration-300">
                Tidak
            </button>
            <button id="confirmExportBtn" 
                    class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg
                           bg-green-500 hover:bg-green-600 text-white 
                           transition-all duration-300 shadow-sm">
                Ya
            </button>
        </div>
    </div>
</div>

<script>
// ================= CONSTANTS =================
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const authToken = "{{ session('token') }}";
const API_URL = "{{ url('/api/kepalasopir/order') }}";

let allOrders = [];
let filteredOrders = [];
let currentPage = 1;
const perPage = 2;

// ================= HELPER FUNCTIONS =================
function getTodayDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    return date.toLocaleDateString('id-ID', options);
}

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
        'completed': {
            label: 'Selesai',
            bgColor: 'bg-green-50',
            textColor: 'text-green-700',
            borderColor: 'border-green-200'
        },
        'on-process': {
            label: 'Diperjalanan',
            bgColor: 'bg-yellow-50',
            textColor: 'text-yellow-700',
            borderColor: 'border-yellow-200'
        },
        'confirmed': {
            label: 'Dikonfirmasi',
            bgColor: 'bg-blue-50',
            textColor: 'text-blue-700',
            borderColor: 'border-blue-200'
        },
        'canceled': {
            label: 'Dibatalkan',
            bgColor: 'bg-red-50',
            textColor: 'text-red-700',
            borderColor: 'border-red-200'
        },
        'rejected': {
            label: 'Ditolak',
            bgColor: 'bg-orange-50',
            textColor: 'text-orange-700',
            borderColor: 'border-orange-200'
        },
        'assigned': {
            label: 'Ditugaskan',
            bgColor: 'bg-purple-50',
            textColor: 'text-purple-700',
            borderColor: 'border-purple-200'
        }
    };

    return configs[status] || {
        label: status,
        bgColor: 'bg-gray-50',
        textColor: 'text-gray-700',
        borderColor: 'border-gray-200'
    };
}

// ================= FETCH DATA =================
async function fetchOrders() {
    showLoading(true);

    try {
        const response = await fetch(`${API_URL}?status=all`, {
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
            allOrders = result.data || [];

            // Set default range: hari ini
            const todayDate = getTodayDate();
            document.getElementById('dateFrom').value = todayDate;
            document.getElementById('dateTo').value = todayDate;

            applyFilters();
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

// ================= FILTER =================
function applyFilters() {
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    filteredOrders = allOrders.filter(order => {
        // Filter hanya status riwayat
        const validStatuses = ['assigned','on-process','confirmed', 'completed', 'canceled', 'rejected'];
        if (!validStatuses.includes(order.status)) {
            return false;
        }

        // Filter berdasarkan rentang tanggal
        if (dateFrom && dateTo) {
            const orderDate = order.waktu_penjemputan.substring(0, 10);
            return orderDate >= dateFrom && orderDate <= dateTo;
        } else if (dateFrom) {
            // Jika hanya dateFrom yang diisi
            const orderDate = order.waktu_penjemputan.substring(0, 10);
            return orderDate >= dateFrom;
        } else if (dateTo) {
            // Jika hanya dateTo yang diisi
            const orderDate = order.waktu_penjemputan.substring(0, 10);
            return orderDate <= dateTo;
        }

        return true;
    });

    // Update info rentang tanggal di empty state
    if (dateFrom && dateTo) {
        const dateRangeInfo = document.getElementById('dateRangeInfo');
        dateRangeInfo.textContent = `${formatDate(dateFrom)} - ${formatDate(dateTo)}`;
    }

    currentPage = 1;
    renderOrders();
}

// ================= CREATE ORDER CARD =================
function createOrderCard(order) {
    const statusConfig = getStatusConfig(order.status);
    const waktuPenjemputan = formatDateTime(order.waktu_penjemputan);
    const waktuDiperbarui = formatDateTime(order.updated_at);
    const waktuPembuatan = formatDateTime(order.created_at);

    // Data sopir dan mobil dari assignment (jika ada)
    const sopirName = order.assignment?.sopir?.name || '-';
    const sopirNomor = order.assignment?.sopir?.nomor || '';
    const penumpangNomor = order.assignment?.penumpang?.nomor || '';
    const mobilId = order.assignment?.mobil?.mobil_id || '-';
    const mobilDeskripsi = order.assignment?.mobil?.deskripsi || '-';

    return `
        <div class="border border-gray-200 rounded-lg md:rounded-xl p-4 md:p-5 transition-all">

            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs text-gray-500">
                        ID Pemesanan <span class="font-semibold text-gray-700">#${order.order_id}</span>
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs font-semibold text-blue-700 mb-1">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            Sopir
                        </p>
                        <p class="text-xs text-gray-700">
                            ${sopirName}
                        </p>
                    </div>
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
            <div class="border-t border-gray-100 pt-3">
                <a href="https://wa.me/${sopirNomor}"
                   target="_blank"
                   class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600
                          text-white font-semibold text-sm py-2.5 rounded-lg transition-all duration-300
                          shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    Hubungi Sopir
                </a>
            </div>
            <div class="border-t border-gray-100 pt-3">
                <a href="https://wa.me/${penumpangNomor}"
                   target="_blank"
                   class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600
                          text-white font-semibold text-sm py-2.5 rounded-lg transition-all duration-300
                          shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    Hubungi Penumpang
                </a>
            </div>

        </div>
    `;
}

// ================= RENDER ORDERS =================
function renderOrders() {
    const container = document.getElementById('orderList');
    const emptyStateToday = document.getElementById('emptyStateToday');
    const emptyStateFiltered = document.getElementById('emptyStateFiltered');
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const todayDate = getTodayDate();
    const isToday = dateFrom === todayDate && dateTo === todayDate;

    container.innerHTML = '';

    if (filteredOrders.length === 0) {
        container.classList.add('hidden');

        if (isToday) {
            emptyStateToday.classList.remove('hidden');
            emptyStateFiltered.classList.add('hidden');
        } else {
            emptyStateToday.classList.add('hidden');
            emptyStateFiltered.classList.remove('hidden');
        }

        updatePagination(0);
        return;
    }

    container.classList.remove('hidden');
    emptyStateToday.classList.add('hidden');
    emptyStateFiltered.classList.add('hidden');

    const start = (currentPage - 1) * perPage;
    const end = start + perPage;
    const pageOrders = filteredOrders.slice(start, end);

    pageOrders.forEach(order => {
        const card = createOrderCard(order);
        container.innerHTML += card;
    });

    updatePagination(filteredOrders.length);
}

// ================= PAGINATION =================
function updatePagination(total) {
    const totalPages = Math.ceil(total / perPage);

    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage >= totalPages || total === 0;
    document.getElementById('currentPage').textContent = total === 0 ? '0' : currentPage;
    document.getElementById('totalPages').textContent = totalPages === 0 ? '0' : totalPages;
}

// ================= LOADING & ERROR =================
function showLoading(show) {
    const loadingSkeleton = document.getElementById('loadingSkeleton');
    const orderList = document.getElementById('orderList');
    const emptyStateToday = document.getElementById('emptyStateToday');
    const emptyStateFiltered = document.getElementById('emptyStateFiltered');

    if (show) {
        loadingSkeleton.classList.remove('hidden');
        orderList.classList.add('hidden');
        emptyStateToday.classList.add('hidden');
        emptyStateFiltered.classList.add('hidden');
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
}

// ================= NOTIFICATION =================
function showNotification(message, type = 'info') {
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
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-0');
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// ================= MODAL EXPORT =================
function showExportModal(dateFrom, dateTo) {
    const modal = document.getElementById('confirmExportModal');
    const modalMessage = document.getElementById('exportModalMessage');
    
    modalMessage.textContent = `Ekspor data dari ${formatDate(dateFrom)} sampai ${formatDate(dateTo)}?`;
    modal.classList.remove('hidden');
}

function hideExportModal() {
    const modal = document.getElementById('confirmExportModal');
    modal.classList.add('hidden');
}

// ================= EVENT LISTENERS =================
document.getElementById('prevPage').onclick = () => {
    currentPage--;
    renderOrders();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

document.getElementById('nextPage').onclick = () => {
    currentPage++;
    renderOrders();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Event listener untuk kedua input date
document.getElementById('dateFrom').onchange = applyFilters;
document.getElementById('dateTo').onchange = applyFilters;

document.getElementById('resetFilter').onclick = () => {
    const todayDate = getTodayDate();
    document.getElementById('dateFrom').value = todayDate;
    document.getElementById('dateTo').value = todayDate;
    applyFilters();
};

// Event listener untuk tombol export
document.getElementById('exportBtn').onclick = () => {
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    if (!dateFrom || !dateTo) {
        showNotification('Silakan pilih rentang tanggal terlebih dahulu', 'error');
        return;
    }

    // Tampilkan modal konfirmasi
    showExportModal(dateFrom, dateTo);
};

// Tombol cancel di modal
document.getElementById('cancelExportBtn').onclick = () => {
    hideExportModal();
};

// Tombol confirm export di modal
document.getElementById('confirmExportBtn').onclick = async () => {
    hideExportModal();
    
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    // Disable tombol export saat proses
    const exportBtn = document.getElementById('exportBtn');
    const originalText = exportBtn.innerHTML;
    exportBtn.disabled = true;
    exportBtn.innerHTML = `
        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Mengexport...
    `;

    try {
        // Buat form data
        const formData = new FormData();
        formData.append('start_date', dateFrom);
        formData.append('end_date', dateTo);

        // Request ke API export
        const response = await fetch("{{ url('/api/kepalasopir/export') }}", {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            },
            body: formData
        });

        if (!response.ok) {
            throw new Error('Gagal export data');
        }

        // Download file
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Rekap Order - ${dateFrom} sd ${dateTo}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        showNotification('Data berhasil di-export!', 'success');
    } catch (error) {
        console.error('Error:', error);
        showNotification('Gagal export data. Silakan coba lagi.', 'error');
    } finally {
        // Restore tombol export
        exportBtn.disabled = false;
        exportBtn.innerHTML = originalText;
    }
};

// Close modal when clicking outside
document.getElementById('confirmExportModal').onclick = (e) => {
    if (e.target.id === 'confirmExportModal') {
        hideExportModal();
    }
};

// Validasi: dateTo tidak boleh lebih kecil dari dateFrom
document.getElementById('dateTo').onchange = function() {
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = this.value;
    
    if (dateFrom && dateTo && dateTo < dateFrom) {
        alert('Tanggal akhir tidak boleh lebih kecil dari tanggal awal');
        this.value = dateFrom;
    }
    applyFilters();
};

document.getElementById('dateFrom').onchange = function() {
    const dateFrom = this.value;
    const dateTo = document.getElementById('dateTo').value;
    
    if (dateFrom && dateTo && dateTo < dateFrom) {
        document.getElementById('dateTo').value = dateFrom;
    }
    applyFilters();
};

// ================= INIT =================
document.addEventListener('DOMContentLoaded', function() {
    fetchOrders();
});
</script>
@endsection