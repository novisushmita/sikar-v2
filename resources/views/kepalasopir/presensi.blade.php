@extends('layouts.app')

@section('title', 'Presensi Sopir')

@section('content')
<div class="max-w-6xl space-y-6 md:space-y-8">

    <!-- ================= FILTER & EXPORT SECTION ================= -->
    <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-base md:text-lg font-semibold">Presensi Sopir</h2>

            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Info Tanggal -->
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-700" id="currentDate">-</span>
                </div>

                <!-- Tombol Export -->
                <button id="exportBtn"
                        class="px-4 py-2 text-sm font-medium rounded-lg
                               bg-green-500 hover:bg-green-600 text-white
                               transition-all duration-300 shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ekspor Excel
                </button>
            </div>
        </div>
    </section>

    <!-- ================= SOPIR HADIR CARD ================= -->
    <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Sopir Hadir</h3>
                    <p class="text-2xl font-bold text-green-600" id="totalHadir">0</p>
                </div>
            </div>
        </div>

        <!-- List Sopir Hadir -->
        <div class="space-y-2 max-h-[500px] overflow-y-auto" id="listHadir">
            <div class="text-center py-8 text-sm text-gray-400">Memuat data...</div>
        </div>
    </section>

</div>

<!-- ================= MODAL EXPORT ================= -->
<div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl md:rounded-2xl shadow-2xl max-w-md w-full p-6 md:p-8 transform transition-all">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg md:text-xl font-bold text-gray-800">Export Presensi</h3>
            <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" 
                       id="exportStartDate"
                       class="w-full border-2 border-gray-200 rounded-lg px-4 py-2.5 text-sm
                              focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" 
                       id="exportEndDate"
                       class="w-full border-2 border-gray-200 rounded-lg px-4 py-2.5 text-sm
                              focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
            </div>
        </div>

        <div class="flex gap-3">
            <button onclick="closeExportModal()"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg
                           bg-gray-100 hover:bg-gray-200 text-gray-700
                           transition-all duration-300">
                Batalkan
            </button>
            <button onclick="confirmExport()"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg
                           bg-green-500 hover:bg-green-600 text-white
                           transition-all duration-300 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M5 13l4 4L19 7"/>
                </svg>
                Ya, Export
            </button>
        </div>
    </div>
</div>

<style>
#listHadir::-webkit-scrollbar {
    width: 6px;
}

#listHadir::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#listHadir::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

#listHadir::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>

<script>
// ================= CONSTANTS =================
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const authToken = "{{ session('token') }}";

let presensiData = [];

// ================= HELPER FUNCTIONS =================
function getTodayDate() {
    // Get today's date in Jakarta timezone (WIB = UTC+7)
    const today = new Date();
    
    // Manual calculation for Jakarta timezone (UTC+7)
    const jakartaOffset = 7 * 60; // 7 hours in minutes
    const localOffset = today.getTimezoneOffset(); // Browser's timezone offset
    const totalOffset = jakartaOffset + localOffset;
    
    const jakartaTime = new Date(today.getTime() + (totalOffset * 60 * 1000));
    
    const year = jakartaTime.getFullYear();
    const month = String(jakartaTime.getMonth() + 1).padStart(2, '0');
    const day = String(jakartaTime.getDate()).padStart(2, '0');
    
    return `${year}-${month}-${day}`;
}

function formatDate(dateString) {
    // Parse tanggal dengan timezone lokal Indonesia
    const parts = dateString.split('-');
    const year = parseInt(parts[0]);
    const month = parseInt(parts[1]) - 1; // Month is 0-indexed
    const day = parseInt(parts[2]);
    
    const date = new Date(year, month, day);
    
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'Asia/Jakarta'
    };
    
    return date.toLocaleDateString('id-ID', options);
}

// ================= FETCH DATA =================
async function fetchPresensiData() {
    try {
        showLoading();
        
        // Endpoint untuk ambil data history_bekerja_sopir
        const response = await fetch("/api/kepalasopir/sopirmasuk", {
            method: "GET",
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${authToken}`,
                "X-CSRF-TOKEN": csrfToken
            }
        });

        const json = await response.json();
        
        if (json.status && json.data) {
            presensiData = json.data;
            renderPresensi();
        } else {
            throw new Error(json.message || 'Gagal memuat data');
        }

    } catch (error) {
        console.error('Error:', error);
        showError('Terjadi kesalahan saat memuat data: ' + error.message);
    }
}

// ================= RENDER PRESENSI =================
function renderPresensi() {
    const today = getTodayDate();
    
    console.log('=== DEBUG PRESENSI ===');
    console.log('Today:', today);
    console.log('Total data:', presensiData.length);
    console.log('Data:', presensiData);
    
    // Filter data untuk HARI INI saja
    const hadirHariIni = presensiData.filter(item => {
        // Convert UTC datetime to Jakarta timezone (WIB = UTC+7)
        let itemDateStr;
        
        if (typeof item.tanggal === 'string') {
            // Parse UTC datetime from backend (e.g., "2026-02-09T17:00:00.000000Z")
            const utcDate = new Date(item.tanggal);
            
            // Check if valid date
            if (isNaN(utcDate.getTime())) {
                console.warn('Invalid date:', item.tanggal);
                return false;
            }
            
            // Convert to Jakarta timezone (UTC+7)
            const jakartaOffset = 7 * 60 * 60 * 1000; // 7 hours in milliseconds
            const jakartaDate = new Date(utcDate.getTime() + jakartaOffset);
            
            // Extract date components in Jakarta timezone
            const year = jakartaDate.getUTCFullYear();
            const month = String(jakartaDate.getUTCMonth() + 1).padStart(2, '0');
            const day = String(jakartaDate.getUTCDate()).padStart(2, '0');
            
            itemDateStr = `${year}-${month}-${day}`;
        } else {
            console.warn('Unexpected tanggal format:', item.tanggal);
            return false;
        }
        
        const match = itemDateStr === today;
        console.log(`UTC: ${item.tanggal} -> WIB Date: ${itemDateStr} === ${today} = ${match}`, item);
        return match;
    });
    
    console.log('Filtered data count (hari ini):', hadirHariIni.length);

    // Update total
    document.getElementById('totalHadir').textContent = hadirHariIni.length;

    // Update current date dengan HARI INI (bukan dari database)
    document.getElementById('currentDate').textContent = formatDate(today);

    // Render list hadir
    const listHadir = document.getElementById('listHadir');
    
    if (hadirHariIni.length === 0) {
        listHadir.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-base text-gray-500 font-medium">Tidak ada sopir yang hadir hari ini</p>
                <p class="text-sm text-gray-400 mt-1">Belum ada sopir yang melakukan presensi</p>
            </div>
        `;
    } else {
        // Sort berdasarkan nama sopir
        hadirHariIni.sort((a, b) => {
            const nameA = a.sopir?.name || '';
            const nameB = b.sopir?.name || '';
            return nameA.localeCompare(nameB);
        });
        
        listHadir.innerHTML = hadirHariIni.map((item, index) => `
            <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                        ${index + 1}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">${item.sopir?.name || 'N/A'}</p>
                        <p class="text-xs text-gray-500">Pesanan Diselesaikan: ${item.order_completed || 0}</p>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

// ================= LOADING STATE =================
function showLoading() {
    const loadingHTML = `
        <div class="text-center py-12">
            <svg class="w-10 h-10 mx-auto text-gray-400 animate-spin mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm text-gray-500">Memuat data presensi...</p>
        </div>
    `;
    document.getElementById('listHadir').innerHTML = loadingHTML;
}

// ================= ERROR HANDLING =================
function showError(message) {
    const errorHTML = `
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
            <svg class="w-12 h-12 mx-auto text-red-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-red-700 font-semibold text-sm">${message}</p>
            <button onclick="fetchPresensiData()" 
                    class="mt-3 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition-colors">
                Coba Lagi
            </button>
        </div>
    `;
    document.getElementById('listHadir').innerHTML = errorHTML;
}

// ================= EXPORT MODAL =================
function openExportModal() {
    const modal = document.getElementById('exportModal');
    const today = getTodayDate();
    
    document.getElementById('exportStartDate').value = today;
    document.getElementById('exportEndDate').value = today;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeExportModal() {
    const modal = document.getElementById('exportModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

async function confirmExport() {
    const startDate = document.getElementById('exportStartDate').value;
    const endDate = document.getElementById('exportEndDate').value;

    if (!startDate || !endDate) {
        alert('Harap pilih tanggal mulai dan tanggal akhir');
        return;
    }

    if (startDate > endDate) {
        alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
        return;
    }

    try {
        // Show loading state
        const exportBtn = document.querySelector('#exportModal button:last-child');
        const originalText = exportBtn.innerHTML;
        exportBtn.disabled = true;
        exportBtn.innerHTML = `
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Mengunduh...
        `;

        // Build URL with query parameters
        const url = `/api/kepalasopir/export-presensi-sopir?start_date=${startDate}&end_date=${endDate}`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            }
        });

        if (!response.ok) {
            throw new Error('Export gagal');
        }

        // Get blob from response
        const blob = await response.blob();
        
        // Create download link
        const downloadUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = downloadUrl;
        
        // Generate filename
        let filename = 'Presensi Sopir';
        if (startDate && endDate) {
            filename += ` - ${startDate} sd ${endDate}`;
        }
        filename += '.xlsx';
        
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        
        // Cleanup
        window.URL.revokeObjectURL(downloadUrl);
        document.body.removeChild(a);

        // Reset button
        exportBtn.disabled = false;
        exportBtn.innerHTML = originalText;

        closeExportModal();
        
        // Show success message
        showSuccessMessage('Export berhasil diunduh!');

    } catch (error) {
        console.error('Error:', error);
        alert('Gagal melakukan export: ' + error.message);
        
        // Reset button
        const exportBtn = document.querySelector('#exportModal button:last-child');
        if (exportBtn) {
            exportBtn.disabled = false;
            exportBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M5 13l4 4L19 7"/>
                </svg>
                Ya, Export
            `;
        }
    }
}

function showSuccessMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2 animate-fade-in';
    toast.innerHTML = `
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// ================= EVENT LISTENERS =================
document.getElementById('exportBtn').onclick = openExportModal;

document.getElementById('exportModal').onclick = (e) => {
    if (e.target.id === 'exportModal') {
        closeExportModal();
    }
};

// Close modal with ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeExportModal();
    }
});

// ================= INIT =================
document.addEventListener('DOMContentLoaded', function() {
    const today = getTodayDate();
    document.getElementById('currentDate').textContent = formatDate(today);
    fetchPresensiData();
    
    // Auto refresh setiap 30 detik
    setInterval(fetchPresensiData, 10000);
});
</script>
@endsection