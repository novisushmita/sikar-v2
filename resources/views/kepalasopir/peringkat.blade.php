@extends('layouts.app')

@section('title', 'Peringkat')

@section('content')
<div class="max-w-6xl space-y-6 md:space-y-8">

    <!-- ================= LEADERBOARD SOPIR ================= -->
    <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4 md:p-6">
        <div class="flex justify-between items-center mb-3 md:mb-4">
            <h2 class="text-base md:text-lg font-semibold">Peringkat Sopir</h2>
            <div class="text-xs md:text-sm text-gray-500">
                <span id="driverLastUpdate">Memuat...</span>
            </div>
        </div>

        <!-- Header Desktop -->
        <div class="hidden md:grid grid-cols-12 text-xs font-semibold text-gray-500 border-b pb-3">
            <div class="col-span-1">Peringkat</div>
            <div class="col-span-7">Sopir</div>
            <div class="col-span-4 text-center">Pesanan Selesai</div>
        </div>

        <!-- Header Mobile -->
        <div class="grid md:hidden grid-cols-12 gap-2 text-xs font-semibold text-gray-500 border-b pb-2">
            <div class="col-span-3">Peringkat</div>
            <div class="col-span-5">Sopir</div>
            <div class="col-span-4 text-center">Pesanan Selesai</div>
        </div>

        <div id="driverList" class="divide-y max-h-[300px] md:max-h-[400px] overflow-y-auto">
            <div class="py-6 text-center text-sm text-gray-400">Memuat...</div>
        </div>
    </section>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    loadDrivers();
    
    setInterval(loadDrivers, 300000);
});

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const authToken = "{{ session('token') }}";

function getTimeNow() {
    const now = new Date();
    return now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

/* ================= PERINGKAT SOPIR ================= */
async function loadDrivers() {
    try {
        const res = await fetch("/api/kepalasopir/leaderboard", {
            method: "GET",
            headers: {
                "Accept": "application/json",
                "Authorization": `Bearer ${authToken}`,
                "X-CSRF-TOKEN": csrfToken
            },
            credentials: "same-origin"
        });

        const json = await res.json();
        
        if (!json.status) {
            throw new Error(json.message);
        }

        const drivers = json.data || [];
        const container = document.getElementById("driverList");
        const lastUpdate = document.getElementById("driverLastUpdate");

        if (drivers.length === 0) {
            container.innerHTML = `<div class="py-6 text-center text-xs md:text-sm text-gray-400">Tidak ada sopir tersedia</div>`;
            lastUpdate.textContent = `Diperbarui ${getTimeNow()}`;
            return;
        }

        container.innerHTML = drivers.map((driver, index) => {
            const rankBadgeClass = index === 0 ? 'bg-gradient-to-br from-yellow-400 to-yellow-600 text-white' : 
                                   index === 1 ? 'bg-gradient-to-br from-gray-300 to-gray-500 text-white' : 
                                   index === 2 ? 'bg-gradient-to-br from-orange-400 to-orange-600 text-white' : 
                                   'bg-gray-100 text-gray-600';
            
            return `
            <div class="grid grid-cols-12 gap-2 items-center py-3 md:py-4 text-xs md:text-sm">
                <div class="col-span-3 md:col-span-1 flex justify">
                    <div class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center rounded-full ${rankBadgeClass} font-bold text-sm md:text-base shadow-sm">
                        ${index + 1}
                    </div>
                </div>
                <div class="col-span-5 md:col-span-7 font-medium text-gray-800 truncate">
                    ${driver.name}
                </div>
                <div class="col-span-4 text-center font-semibold">${driver.order_completed}</div>
            </div>
        `;
        }).join('');

        lastUpdate.textContent = `Diperbarui ${getTimeNow()}`;

    } catch (err) {
        console.error("Error loadDrivers:", err);
        document.getElementById("driverList").innerHTML = `
            <div class="py-6 text-center">
                <div class="text-xs md:text-sm text-red-400">${err.message}</div>
            </div>
        `;
        document.getElementById("driverLastUpdate").textContent = "Gagal update";
    }
}
</script>
@endsection