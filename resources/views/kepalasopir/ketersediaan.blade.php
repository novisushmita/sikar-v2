@extends('layouts.app')

@section('title', 'Ketersediaan')

@section('content')
<div class="max-w-6xl space-y-6 md:space-y-8">

    <!-- ================= MOBIL ================= -->
    <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4 md:p-6">
        <div class="flex justify-between items-center mb-3 md:mb-4">
            <h2 class="text-base md:text-lg font-semibold">Ketersediaan Mobil</h2>
            <div class="text-xs md:text-sm text-gray-500">
                <span id="carLastUpdate">Memuat...</span>
            </div>
        </div>

        <div class="car-container">
            <!-- Header Desktop -->
            <div class="hidden md:grid grid-cols-12 text-xs font-semibold text-gray-500 border-b pb-3">
                <div class="col-span-2">ID</div>
                <div class="col-span-7">Mobil</div>
                <div class="col-span-3 text-center">Status</div>
            </div>

            <!-- Header Mobile -->
            <div class="grid md:hidden grid-cols-12 text-xs font-semibold text-gray-500 border-b pb-2">
                <div class="col-span-3">ID</div>
                <div class="col-span-6">Mobil</div>
                <div class="col-span-3 text-center">Status</div>
            </div>

            <div id="carList" class="divide-y max-h-[300px] md:max-h-[400px] overflow-y-scroll">
                <div class="py-6 text-center text-sm text-gray-400">Memuat...</div>
            </div>
        </div>
    </section>

    <!-- ================= SOPIR ================= -->
    <section class="bg-white rounded-xl md:rounded-2xl shadow-lg border p-4 md:p-6">
        <div class="flex justify-between items-center mb-3 md:mb-4">
            <h2 class="text-base md:text-lg font-semibold">Ketersediaan Sopir</h2>
            <div class="text-xs md:text-sm text-gray-500">
                <span id="driverLastUpdate">Memuat...</span>
            </div>
        </div>

        <div class="driver-container">
            <!-- Header Desktop -->
            <div class="hidden md:grid grid-cols-12 text-xs font-semibold text-gray-500 border-b pb-3">
                <div class="col-span-9">Sopir</div>
                <div class="col-span-3 text-center">Status</div>
            </div>

            <!-- Header Mobile -->
            <div class="grid md:hidden grid-cols-12 text-xs font-semibold text-gray-500 border-b pb-2">
                <div class="col-span-9">Sopir</div>
                <div class="col-span-3 text-center">Status</div>
            </div>

            <div id="driverList" class="divide-y max-h-[300px] md:max-h-[400px] overflow-y-scroll">
                <div class="py-6 text-center text-sm text-gray-400">Memuat...</div>
            </div>
        </div>
    </section>

</div>

<style>
.car-container,
.driver-container {
    position: relative;
}

#carList, #driverList {
    scrollbar-gutter: stable;
}

@supports not (scrollbar-gutter: stable) {
    #carList, #driverList {
        padding-right: 15px;
    }
}

@media (min-width: 768px) {
    .car-container > div:first-of-type,
    .driver-container > div:first-of-type {
        padding-right: 15px;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    loadCars();
    loadDrivers();
    
    setInterval(() => {
        loadCars();
        loadDrivers();
    }, 300000);
});

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const authToken = "{{ session('token') }}";

function getTimeNow() {
    const now = new Date();
    return now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

/* ================= MOBIL ================= */
async function loadCars() {
    try {
        const res = await fetch("/api/kepalasopir/mobil", {
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

        const cars = json.data || [];
        const container = document.getElementById("carList");
        const lastUpdate = document.getElementById("carLastUpdate");

        if (cars.length === 0) {
            container.innerHTML = `<div class="py-6 text-center text-xs md:text-sm text-gray-400">Tidak ada mobil tersedia</div>`;
            lastUpdate.textContent = `Diperbarui ${getTimeNow()}`;
            return;
        }

        container.innerHTML = cars.map(car => `
            <div class="grid grid-cols-12 items-center py-3 md:py-4 text-xs md:text-sm">
                <div class="col-span-3 md:col-span-2 font-medium text-gray-600">${car.mobil_id}</div>
                <div class="col-span-6 md:col-span-7 font-medium text-gray-800 truncate">${car.deskripsi}</div>
                <div class="col-span-3 text-center">
                    <span class="px-2 md:px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        Tersedia
                    </span>
                </div>
            </div>
        `).join('');

        lastUpdate.textContent = `Diperbarui ${getTimeNow()}`;

    } catch (err) {
        console.error("Error loadCars:", err);
        document.getElementById("carList").innerHTML = `
            <div class="py-6 text-center">
                <div class="text-xs md:text-sm text-red-400">${err.message}</div>
            </div>
        `;
        document.getElementById("carLastUpdate").textContent = "Gagal update";
    }
}

/* ================= SOPIR ================= */
async function loadDrivers() {
    try {
        const res = await fetch("/api/kepalasopir/sopir", {
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

        container.innerHTML = drivers.map(driver => `
            <div class="grid grid-cols-12 items-center py-3 md:py-4 text-xs md:text-sm">
                <div class="col-span-9 font-medium text-gray-800 truncate">${driver.name}</div>
                <div class="col-span-3 text-center">
                    <span class="px-2 md:px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        Tersedia
                    </span>
                </div>
            </div>
        `).join('');

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