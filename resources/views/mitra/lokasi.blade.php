@extends('layouts.navbar')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Temukan Mitra SisaRasa Terdekat</h1>
            <p class="text-gray-600">Jelajahi restoran-restoran terdekat dan temukan makanan lezat dengan harga hemat!</p>
        </div>

        <!-- Location Input -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" id="location-input" 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent" 
                               placeholder="Masukkan lokasi Anda">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
                <button id="use-current-location" 
                        class="flex items-center gap-2 px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary/90 transition-all">
                    <i class="fas fa-location-crosshairs"></i>
                    <span>Gunakan Lokasi Saat Ini</span>
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-white rounded-lg shadow-lg overflow-hidden">
                <div id="lokasi-alert" class="hidden"></div>
                <div id="map-container" class="relative w-full" style="height: 500px;">
                    <div id="map" class="absolute inset-0"></div>
                    <div id="map-loader" class="absolute inset-0 bg-white bg-opacity-90 z-10 flex items-center justify-center">
                        <div class="text-center">
                            <div class="animate-spin inline-block w-8 h-8 border-4 border-secondary border-t-transparent rounded-full mb-2"></div>
                            <p class="text-gray-600">Memuat peta...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Daftar Mitra</h2>
                        <button id="btn-current-location" 
                                class="p-2 text-secondary hover:text-white hover:bg-secondary rounded-full transition-all duration-200"
                                title="Perbarui Lokasi">
                            <i class="fas fa-location-crosshairs"></i>
                        </button>
                    </div>
                    
                    <div id="mitra-list" class="space-y-4 max-h-[450px] overflow-y-auto pr-2 -mr-2">
                        <div class="animate-pulse space-y-4">
                            @for ($i = 0; $i < 3; $i++)
                            <div class="bg-gray-100 rounded-lg p-4">
                                <div class="h-5 bg-gray-200 rounded w-3/4 mb-2"></div>
                                <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Location Permission Modal -->
<div id="location-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-semibold mb-4">Izinkan Akses Lokasi</h3>
        <p class="text-gray-600 mb-6">
            Untuk memberikan pengalaman terbaik, kami membutuhkan akses ke lokasi Anda. Ini akan membantu kami menampilkan mitra terdekat dari lokasi Anda.
        </p>
        <div class="flex justify-end gap-4">
            <button id="deny-location" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                Gunakan Lokasi Default
            </button>
            <button id="allow-location" class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary/90">
                Izinkan
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .mitra-card {
        transition: all 0.2s ease-in-out;
    }
    .mitra-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .mitra-card.active {
        border-color: #0B3D3B;
        background-color: #EDFFEF;
    }
    #map { 
        width: 100%;
        height: 100%;
    }
    #mitra-list::-webkit-scrollbar {
        width: 4px;
    }
    #mitra-list::-webkit-scrollbar-track {
        background: #F3F4F6;
        border-radius: 2px;
    }
    #mitra-list::-webkit-scrollbar-thumb {
        background: #D1D5DB;
        border-radius: 2px;
    }
    .pulse {
        animation: pulse-ring 1.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
    }
    @keyframes pulse-ring {
        0% { transform: scale(0.95); }
        70% { transform: scale(1); }
        100% { transform: scale(0.95); }
    }
    .leaflet-container {
        font-family: inherit;
        z-index: 1;
    }
    .leaflet-popup-content {
        margin: 0;
        padding: 0;
    }
    .leaflet-popup-content-wrapper {
        padding: 0;
        border-radius: 8px;
        overflow: hidden;
    }
    .restaurant-card {
        padding: 16px;
        max-width: 300px;
    }    /* Route button styles removed as they are now inline */
    #map-container {
        position: relative;
        z-index: 1;
    }
    #location-modal {
        z-index: 9999;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let map;
    let markers = [];
    let currentLocationMarker;
    let userLocation = [-6.890762, 107.614415]; // Default: Bandung
    const defaultLocation = [-6.890762, 107.614415];
    
    const locationInput = document.getElementById('location-input');
    const useCurrentLocationBtn = document.getElementById('use-current-location');
    const locationModal = document.getElementById('location-modal');
    const allowLocationBtn = document.getElementById('allow-location');
    const denyLocationBtn = document.getElementById('deny-location');

    // Sample data
    const sampleMitra = [
        {
            name: "Restoran Sunda Sedap",
            alamat_lengkap: "Jl. Dipatiukur No. 123, Bandung",
            latitude: -6.893291,
            longitude: 107.619854,
            distance: 0.5,
            status: "Buka"
        },
        {
            name: "Warung Nasi Goreng Spesial",
            alamat_lengkap: "Jl. Ir. H. Djuanda No. 45, Bandung",
            latitude: -6.887752,
            longitude: 107.613620,
            distance: 1.2,
            status: "Buka"
        },
        {
            name: "Cafe Hits Bandung",
            alamat_lengkap: "Jl. Tubagus Ismail No. 78, Bandung",
            latitude: -6.888517,
            longitude: 107.619441,
            distance: 0.8,
            status: "Buka"
        }
    ];

    function initMap() {
        map = L.map('map').setView(userLocation, 14);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add user location marker
        const userIcon = L.divIcon({
            html: '<div class="w-4 h-4 bg-blue-500 rounded-full border-2 border-white shadow-lg pulse"></div>',
            className: 'custom-div-icon'
        });

        currentLocationMarker = L.marker(userLocation, { icon: userIcon })
            .addTo(map)
            .bindPopup('Lokasi Anda')
            .openPopup();

        document.getElementById('map-loader').style.display = 'none';
        updateMitraList(sampleMitra);
        addMitraMarkers(sampleMitra);
    }

    function updateMitraList(mitras) {
        const mitraList = document.getElementById('mitra-list');
        mitraList.innerHTML = '';

        mitras.forEach((mitra, index) => {
            const distance = calculateDistance(userLocation[0], userLocation[1], mitra.latitude, mitra.longitude);
            const card = document.createElement('div');
            card.className = 'mitra-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer transition-all duration-200';
            card.innerHTML = `
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-900">${mitra.name}</h3>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">${mitra.status}</span>
                </div>
                <p class="text-sm text-gray-600 mb-3">${mitra.alamat_lengkap}</p>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">
                        <i class="fas fa-location-dot text-secondary"></i> ${distance.toFixed(1)} km
                    </span>
                    <div class="flex gap-2">
                        <button onclick="window.open('https://www.google.com/maps/dir/?api=1&destination=${mitra.latitude},${mitra.longitude}')" 
                                class="text-secondary hover:text-secondary/80 flex items-center gap-1">
                            <i class="fas fa-directions"></i>
                            <span>Google Maps</span>
                        </button>
                    </div>
                </div>
            `;

            card.addEventListener('click', () => {
                document.querySelectorAll('.mitra-card').forEach(c => c.classList.remove('active', 'border-secondary'));
                card.classList.add('active', 'border-secondary');
                map.flyTo([mitra.latitude, mitra.longitude], 16);
                markers[index].openPopup();
            });

            mitraList.appendChild(card);
        });
    }

    function addMitraMarkers(mitras) {
        markers.forEach(marker => marker && map.removeLayer(marker));
        markers = [];

        const customIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
            shadowSize: [41, 41]
        });

        mitras.forEach(mitra => {
            const distance = calculateDistance(userLocation[0], userLocation[1], mitra.latitude, mitra.longitude);
            const marker = L.marker([mitra.latitude, mitra.longitude], { icon: customIcon }).addTo(map);
            
            marker.bindPopup(`
                <div class="restaurant-card">
                    <h3 class="font-semibold text-gray-900 mb-1">${mitra.name}</h3>
                    <p class="text-sm text-gray-600 mb-2">${mitra.alamat_lengkap}</p>
                    <p class="text-sm text-gray-500 mb-3">
                        <i class="fas fa-location-dot text-secondary"></i> ${distance.toFixed(1)} km
                    </p>                    <a href="https://www.google.com/maps/dir/?api=1&destination=${mitra.latitude},${mitra.longitude}" 
                       target="_blank"
                       class="bg-white text-secondary hover:bg-secondary/10 border border-secondary py-2 px-3 rounded-lg flex items-center gap-2 text-sm">
                        <i class="fas fa-directions"></i>
                        <span>Navigasi</span>
                    </a>
                </div>
            `);
            
            markers.push(marker);
        });
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius of the Earth in kilometers
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                 Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                 Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function updateUserLocation(position) {
        const { latitude, longitude } = position;
        userLocation = [latitude, longitude];
        
        if (currentLocationMarker) {
            currentLocationMarker.setLatLng(userLocation);
        }
        
        map.flyTo(userLocation, 14);
        updateMitraList(sampleMitra);
        addMitraMarkers(sampleMitra);
    }

    // Event Listeners
    useCurrentLocationBtn.addEventListener('click', () => {
        locationModal.style.display = 'flex';
    });

    allowLocationBtn.addEventListener('click', () => {
        locationModal.style.display = 'none';
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    updateUserLocation(position);
                },
                (error) => {
                    console.error('Error getting location:', error);
                    alert('Tidak dapat mengakses lokasi Anda. Menggunakan lokasi default.');
                }
            );
        }
    });

    denyLocationBtn.addEventListener('click', () => {
        locationModal.style.display = 'none';
        updateUserLocation({ latitude: defaultLocation[0], longitude: defaultLocation[1] });
    });

    // Handle location search
    let searchTimeout;
    locationInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const query = e.target.value;
            if (query.length > 2) {
                // Di sini bisa ditambahkan API geocoding untuk mencari lokasi
                // Untuk demo, kita gunakan lokasi default
                updateUserLocation({ latitude: defaultLocation[0], longitude: defaultLocation[1] });
            }
        }, 500);
    });

    // Initialize map
    initMap();
});
</script>
@endpush
