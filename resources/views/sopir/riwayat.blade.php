@extends('layouts.app')

@section('title', 'Riwayat')

@section('content')
<div class="max-w-6xl space-y-6 md:space-y-8">

    <!-- ================= FILTER SECTION ================= -->
    <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-base md:text-lg font-semibold">Riwayat Pesanan</h2>

            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Filter Tanggal -->
                <div class="relative">
                    <input type="date"
                           id="dateFilter"
                           class="w-full sm:w-auto border-2 border-gray-200 rounded-lg px-4 py-2 text-sm
                                  bg-white shadow-sm focus:ring-2 focus:ring-blue-500
                                  focus:border-blue-500 transition-all cursor-pointer">
                </div>

                <!-- Tombol Reset -->
                <button id="resetFilter"
                        class="px-4 py-2 text-sm font-medium rounded-lg
                               bg-gray-100 text-gray-700
                               transition-all duration-300 shadow-sm">
                    Hapus Filter
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
                Belum ada pesanan hari ini
            </h3>
        </div>

        <!-- Empty State - Filter Tanggal -->
        <div id="emptyStateFiltered" class="hidden text-center py-12">
            <svg class="w-10 h-10 md:w-12 md:h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="font-semibold text-sm md:text-base text-gray-700">
                Tidak ada pesanan
            </h3>
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

<style>
    .star-wrap-sm { position: relative; display: inline-block; font-size: 1rem; line-height: 1; }
    .star-bg-sm { color: #d1d5db; }
    .star-fill-sm { position: absolute; top: 0; left: 0; overflow: hidden; color: #f59e0b; white-space: nowrap; }
</style>

<script>
// ================= CONSTANTS =================
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const authToken = "{{ session('token') }}";
const API_URL = "{{ url('/api/sopir/orders') }}";

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

function formatDateTime(datetime) {
    if (!datetime) return '-';
    const date = new Date(datetime.replace(' ', 'T'));
    const tanggal = date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    const jam = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
    return `${tanggal} ${jam}`;
}

// ================= RENDER REVIEW / RATING =================
function renderReviewBadge(order) {
    const reviews = order.review;
    let reviewValue = null;

    if (Array.isArray(reviews) && reviews.length > 0) {
        reviewValue = reviews[0]?.review ?? null;
    } else if (reviews && typeof reviews === 'object' && !Array.isArray(reviews)) {
        reviewValue = reviews?.review ?? null;
    }

    const showReviewStatuses = ['completed', 'confirmed'];
    if (!showReviewStatuses.includes(order.status)) return '';

    if (reviewValue === null || reviewValue === undefined) {
        return `
            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
                <span class="text-xs text-gray-400 italic">Belum ada rating</span>
            </div>`;
    }

    const pct = (parseFloat(reviewValue) / 5) * 100;
    const labels = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'];
    const label = labels[Math.round(reviewValue)] || '';

    return `
        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
            <span class="text-xs font-semibold text-gray-600">Rating:</span>
            <span class="star-wrap-sm">
                <span class="star-bg-sm">★</span>
                <span class="star-fill-sm" style="width: ${pct}%">★</span>
            </span>
            <span class="text-xs font-bold text-yellow-600">${parseFloat(reviewValue).toFixed(1)}</span>
            <span class="text-xs text-gray-400">(${label})</span>
        </div>`;
}

// ================= STATUS CONFIG =================
function getStatusConfig(status) {
    const configs = {
        'completed': { label: 'Selesai',      bgColor: 'bg-green-50',  textColor: 'text-green-700',  borderColor: 'border-green-200'  },
        'confirmed': { label: 'Dikonfirmasi', bgColor: 'bg-blue-50',   textColor: 'text-blue-700',   borderColor: 'border-blue-200'   },
        'canceled':  { label: 'Dibatalkan',   bgColor: 'bg-red-50',    textColor: 'text-red-700',    borderColor: 'border-red-200'    },
        'assigned':  { label: 'Ditugaskan',   bgColor: 'bg-purple-50', textColor: 'text-purple-700', borderColor: 'border-purple-200' },
    };
    return configs[status] || { label: status, bgColor: 'bg-gray-50', textColor: 'text-gray-700', borderColor: 'border-gray-200' };
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

        if (!response.ok) throw new Error('Gagal mengambil data');

        const result = await response.json();

        if (result.status) {
            allOrders = result.data || [];

            // ✅ Urutkan dari pesanan terbaru
            allOrders.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            document.getElementById('dateFilter').value = getTodayDate();
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
    const dateFilter = document.getElementById('dateFilter').value;

    filteredOrders = allOrders.filter(order => {
        const validStatuses = ['assigned', 'confirmed', 'completed', 'canceled'];
        if (!validStatuses.includes(order.status)) return false;

        if (dateFilter) {
            const orderDate = order.waktu_penjemputan.substring(0, 10);
            return orderDate === dateFilter;
        }
        return true;
    });

    currentPage = 1;
    renderOrders();
}

// ================= CREATE ORDER CARD =================
function createOrderCard(order) {
    const statusConfig     = getStatusConfig(order.status);
    const waktuPenjemputan = formatDateTime(order.waktu_penjemputan);
    const waktuDiperbarui  = formatDateTime(order.updated_at);
    const waktuPembuatan   = formatDateTime(order.created_at);

    const mobilId    = order.assignment?.mobil_id          || '-';
    const mobilDesk  = order.assignment?.mobil?.deskripsi  || '-';

    return `
        <div class="border border-gray-200 rounded-lg md:rounded-xl p-4 md:p-5 transition-all">

            <!-- Header -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-xs text-gray-500">ID Pesanan <span class="font-semibold text-gray-700">#${order.order_id}</span></p>
                    <p class="text-xs text-gray-500">Dibuat <span class="font-medium text-green-500">${waktuPembuatan}</span></p>
                    <p class="text-xs text-gray-500 mb-1">Diperbarui <span class="font-medium text-red-500">${waktuDiperbarui}</span></p>
                    <p class="font-semibold text-gray-800 text-sm md:text-base">${order.penumpang?.name || '-'}</p>
                </div>
                <span class="text-xs px-3 py-1.5 rounded-full font-semibold
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
                        <p class="text-xs text-blue-500"><span class="font-medium">${waktuPenjemputan}</span></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-700 mb-1">Tempat Tujuan</p>
                        <p class="text-xs md:text-sm text-gray-600">${order.tempat_tujuan}</p>
                    </div>
                </div>
            </div>

            <div class="pt-3 space-y-2 mb-4">
                <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-200">
                    <p class="text-xs font-semibold text-gray-500 mb-1">Keterangan</p>
                    <p class="text-xs md:text-sm text-gray-700">${order.keterangan || '-'}</p>
                </div>
            </div>

            <!-- Mobil Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <p class="text-xs font-semibold text-blue-700 mb-1">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
                            </svg>Mobil
                        </p>
                        <p class="text-xs text-gray-700">${mobilId}</p>
                        <p class="text-xs text-gray-500">${mobilDesk}</p>
                    </div>
                </div>
            </div>

            <!-- Footer: Rating + WA -->
            <div class="border-t border-gray-100 pt-3">
                ${renderReviewBadge(order)}
                <a href="https://wa.me/${order.penumpang?.nomor || ''}" target="_blank"
                   class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600
                          text-white font-semibold text-sm py-2.5 rounded-lg transition-all duration-300
                          shadow-sm hover:shadow-md mt-3">
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
    const emptyStateToday    = document.getElementById('emptyStateToday');
    const emptyStateFiltered = document.getElementById('emptyStateFiltered');
    const dateFilter = document.getElementById('dateFilter').value;
    const isToday = dateFilter === getTodayDate();

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
    const end   = start + perPage;
    filteredOrders.slice(start, end).forEach(order => {
        container.innerHTML += createOrderCard(order);
    });

    updatePagination(filteredOrders.length);
}

// ================= PAGINATION =================
function updatePagination(total) {
    const totalPages = Math.ceil(total / perPage);
    document.getElementById('prevPage').disabled    = currentPage === 1;
    document.getElementById('nextPage').disabled    = currentPage >= totalPages || total === 0;
    document.getElementById('currentPage').textContent = total === 0 ? '0' : currentPage;
    document.getElementById('totalPages').textContent  = totalPages === 0 ? '0' : totalPages;
}

// ================= LOADING & ERROR =================
function showLoading(show) {
    document.getElementById('loadingSkeleton').classList.toggle('hidden', !show);
    if (show) {
        document.getElementById('orderList').classList.add('hidden');
        document.getElementById('emptyStateToday').classList.add('hidden');
        document.getElementById('emptyStateFiltered').classList.add('hidden');
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

// ================= EVENT LISTENERS =================
document.getElementById('prevPage').onclick = () => { currentPage--; renderOrders(); window.scrollTo({ top: 0, behavior: 'smooth' }); };
document.getElementById('nextPage').onclick = () => { currentPage++; renderOrders(); window.scrollTo({ top: 0, behavior: 'smooth' }); };
document.getElementById('dateFilter').onchange = applyFilters;
document.getElementById('resetFilter').onclick = () => {
    document.getElementById('dateFilter').value = getTodayDate();
    applyFilters();
};

// ================= INIT =================
document.addEventListener('DOMContentLoaded', function() {
    fetchOrders();
});


let autoRefreshInterval;

document.addEventListener('DOMContentLoaded', function() {
    fetchOrders();

    autoRefreshInterval = setInterval(() => {
        fetchOrders();
    }, 60000); // 5 detik
});

</script>
@endsection