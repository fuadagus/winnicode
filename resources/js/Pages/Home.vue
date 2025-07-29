<template>
    <AppLayout>
        <!-- Geolocation Status -->
        <div v-if="geolocationStatus" 
             :class="['alert', 'position-fixed', geolocationAlertClass]"
             style="top: 80px; right: 20px; z-index: 1050; max-width: 350px; cursor: pointer;">
            <i :class="['fa', geolocationIcon, 'me-2']"></i>{{ geolocationStatus }}
            <small v-if="geolocationError" class="d-block">Klik untuk mencoba lagi</small>
        </div>

        <!-- Test Geolocation Button -->
        <button @click="getLocation" 
                class="btn btn-primary position-fixed"
                style="bottom: 20px; left: 20px; z-index: 1050; border-radius: 50px;">
            <i class="fa fa-map-marker"></i> Test Geolocation
        </button>

        <!-- Hero Section -->
        

        <!-- Breaking News Banner -->
        <section class="breaking-news-banner bg-danger text-white py-2">
            <div class="container">
                <div class="d-flex align-items-center">
                    <span class="badge bg-white text-danger me-3 px-3 py-2">
                        <i class="fa fa-bolt me-1"></i>BREAKING NEWS
                    </span>
                    <div class="breaking-news-content flex-grow-1">
                        <div v-if="latestNews.length > 0" class="scrolling-text">
                            {{ latestNews[0].title }} • {{ latestNews[1]?.title || '' }} • {{ latestNews[2]?.title || '' }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured News Section -->
        <section class="featured-news py-5">
            <div class="container">
                <div class="row">
                    <!-- Main Featured Article -->
                    <div class="col-lg-8 mb-4">
                        <div v-for="news in latestNews.slice(0, 1)" :key="news.id" class="featured-article">
                            <div class="card modern-card shadow-lg border-0 overflow-hidden">
                                <div class="position-relative">
                                    <img :src="news.image ? `/storage/images/${news.image}` : '/img/noimg.jpg'"
                                         class="card-img-top featured-image" alt="" style="height: 400px; object-fit: cover;" />
                                    
                                    <!-- Local News Badge -->
                                    <div v-if="news.distance && news.distance <= 100"
                                         class="position-absolute bg-success text-white px-3 py-2 rounded-pill"
                                         style="top: 20px; right: 20px; z-index: 10; font-size: 0.85rem;">
                                        <i class="fa fa-map-marker me-1"></i> {{ news.distance.toFixed(1) }} km
                                    </div>

                                    <!-- Category Badge -->
                                    <div class="position-absolute"
                                         style="top: 20px; left: 20px; z-index: 10;">
                                        <span class="badge bg-primary px-3 py-2 rounded-pill">
                                            {{ news.category?.name || 'Berita' }}
                                        </span>
                                    </div>

                                    <!-- Article Meta Overlay -->
                                    <div class="position-absolute bottom-0 start-0 end-0 p-4"
                                         style="background: linear-gradient(transparent, rgba(0,0,0,0.8));">
                                        <div class="text-white d-flex flex-wrap gap-3 mb-3">
                                            <small><i class="fa fa-clock me-1"></i>{{ formatDate(news.created_at) }}</small>
                                            <small><i class="fa fa-eye me-1"></i>{{ news.views }}</small>
                                            <small><i class="fa fa-thumbs-up me-1"></i>{{ news.likes_count }}</small>
                                            <small v-if="news.location">
                                                <i class="fa fa-map-marker me-1"></i>{{ news.location }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body p-4">
                                    <a :href="route('news.show', news.id)" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="text-decoration-none">
                                        <h2 class="card-title h3 mb-3 text-dark fw-bold lh-base">
                                            {{ news.title }}
                                        </h2>
                                    </a>
                                    <p class="card-text text-muted fs-6 lh-lg">
                                        {{ truncateContent(news.content, 300) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <div class="author-info d-flex align-items-center">
                                            <div class="author-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width: 40px; height: 40px;">
                                                {{ news.author_name?.charAt(0) || 'A' }}
                                            </div>
                                            <div>
                                                <div class="author-name small fw-semibold">{{ news.author_name || 'Admin' }}</div>
                                                <div class="publish-time small text-muted">{{ formatDateTime(news.created_at) }}</div>
                                            </div>
                                        </div>
                                        <a :href="route('news.show', news.id)" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                            Baca Selengkapnya
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar with Top Stories -->
                    <div class="col-lg-4">
                        <div class="sidebar-content">
                            <!-- Popular News -->
                            <div class="card modern-card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white border-0 py-3">
                                    <h4 class="card-title mb-0 fw-bold">
                                        <i class="fa fa-fire text-danger me-2"></i>Trending
                                    </h4>
                                </div>
                                <div class="card-body p-0">
                                    <div v-for="(news, index) in latestNews.slice(1, 6)" :key="news.id" 
                                         class="trending-item p-3 border-bottom">
                                        <div class="d-flex">
                                            <div class="trending-number me-3">
                                                <span class="badge rounded-pill" 
                                                      :class="index === 0 ? 'bg-danger' : index === 1 ? 'bg-warning' : index === 2 ? 'bg-info' : 'bg-secondary'">
                                                    {{ index + 1 }}
                                                </span>
                                            </div>
                                            <div class="trending-content flex-grow-1">
                                                <a :href="route('news.show', news.id)" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer"
                                                   class="text-decoration-none">
                                                    <h6 class="trending-title mb-2 text-dark lh-base">
                                                        {{ truncateText(news.title, 80) }}
                                                    </h6>
                                                </a>
                                                <div class="trending-meta d-flex gap-3 text-muted small">
                                                    <span><i class="fa fa-clock me-1"></i>{{ formatDate(news.created_at) }}</span>
                                                    <span><i class="fa fa-eye me-1"></i>{{ news.views }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Local News -->
                            <div class="card modern-card shadow-sm border-0">
                                <div class="card-header bg-white border-0 py-3">
                                    <h4 class="card-title mb-0 fw-bold">
                                        <i class="fa fa-map-marker text-success me-2"></i>Berita Lokal
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">
                                        Berita dari daerah sekitar Anda berdasarkan lokasi
                                    </p>
                                    <button @click="getLocation" class="btn btn-success btn-sm w-100 rounded-pill">
                                        <i class="fa fa-location-arrow me-2"></i>Aktifkan Lokasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Latest News Grid -->
        <section class="latest-news py-5 bg-light">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title display-5 fw-bold mb-3">Berita Terbaru</h2>
                    <p class="section-subtitle text-muted fs-5">Dapatkan update berita terkini dari berbagai kategori</p>
                </div>
                
                <div class="row g-4">
                    <div v-for="news in latestNews.slice(0, 8)" :key="news.id" class="col-lg-3 col-md-6">
                        <div class="card modern-card h-100 shadow-sm border-0 overflow-hidden hover-card">
                            <div class="position-relative">
                                <img :src="news.image ? `/storage/images/${news.image}` : '/img/noimg.jpg'"
                                     class="card-img-top" alt="" style="height: 200px; object-fit: cover;" />
                                
                                <!-- Local News Badge -->
                                <div v-if="news.distance && news.distance <= 100"
                                     class="position-absolute bg-success text-white px-2 py-1 rounded-pill"
                                     style="top: 10px; right: 10px; z-index: 10; font-size: 0.75rem;">
                                    <i class="fa fa-map-marker me-1"></i>{{ news.distance.toFixed(1) }}km
                                </div>

                                <!-- Category Badge -->
                                <div class="position-absolute" style="top: 10px; left: 10px; z-index: 10;">
                                    <span class="badge bg-primary bg-opacity-90 px-2 py-1 rounded-pill small">
                                        {{ news.category?.name || 'Berita' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body p-3 d-flex flex-column">
                                <a :href="route('news.show', news.id)" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="text-decoration-none">
                                    <h5 class="card-title h6 mb-3 text-dark lh-base fw-semibold">
                                        {{ truncateText(news.title, 80) }}
                                    </h5>
                                </a>
                                
                                <p class="card-text text-muted small mb-3 flex-grow-1">
                                    {{ truncateContent(news.content, 100) }}
                                </p>
                                
                                <div class="card-meta mt-auto">
                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                        <div class="d-flex gap-2">
                                            <span><i class="fa fa-eye me-1"></i>{{ news.views }}</span>
                                            <span><i class="fa fa-heart me-1"></i>{{ news.likes_count }}</span>
                                        </div>
                                        <span><i class="fa fa-clock me-1"></i>{{ formatDate(news.created_at) }}</span>
                                    </div>
                                    <div class="author-info d-flex align-items-center mt-2">
                                        <div class="author-avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                             style="width: 24px; height: 24px; font-size: 0.7rem;">
                                            {{ news.author_name?.charAt(0) || 'A' }}
                                        </div>
                                        <small class="text-muted">{{ news.author_name || 'Admin' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="categories py-5">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title display-5 fw-bold mb-3">Kategori Populer</h2>
                    <p class="section-subtitle text-muted fs-5">Jelajahi berita berdasarkan kategori favorit Anda</p>
                </div>

                <div class="category-tabs">
                    <ul class="nav nav-pills justify-content-center mb-5" id="category-tabs">
                        <li v-for="(category, index) in topCategories.slice(0, 4)" :key="category.id" class="nav-item me-2">
                            <button class="nav-link rounded-pill px-4 py-2 fw-semibold" 
                                    :class="{ active: index === 0 }"
                                    :id="`category-tab-${index}`"
                                    data-bs-toggle="pill" 
                                    :data-bs-target="`#category-${index}`" 
                                    type="button">
                                {{ category.name }}
                                <span class="badge bg-light text-dark ms-2">{{ category.news?.length || 0 }}</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="category-content">
                        <div v-for="(category, index) in topCategories.slice(0, 4)" :key="category.id"
                             class="tab-pane fade" 
                             :class="{ 'show active': index === 0 }"
                             :id="`category-${index}`">
                            <div class="row g-4">
                                <!-- Featured Category Article -->
                                <div v-for="news in category.news?.slice(0, 1)" :key="news.id" class="col-lg-6">
                                    <div class="card modern-card h-100 shadow border-0 overflow-hidden">
                                        <div class="position-relative">
                                            <img :src="news.image ? `/storage/images/${news.image}` : '/img/noimg.jpg'"
                                                 class="card-img-top" alt="" style="height: 300px; object-fit: cover;" />
                                            <div class="position-absolute top-0 start-0 end-0 bottom-0"
                                                 style="background: linear-gradient(transparent 60%, rgba(0,0,0,0.8));">
                                            </div>
                                            <div class="position-absolute bottom-0 start-0 end-0 p-4 text-white">
                                                <span class="badge bg-primary mb-2">{{ category.name }}</span>
                                                <a :href="route('news.show', news.id)" 
                                                   target="_blank" 
                                                   rel="noopener noreferrer"
                                                   class="text-decoration-none text-white">
                                                    <h4 class="mb-2 fw-bold">{{ news.title }}</h4>
                                                </a>
                                                <p class="mb-0 opacity-75">{{ truncateContent(news.content, 150) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category Articles Grid -->
                                <div class="col-lg-6">
                                    <div class="row g-3">
                                        <div v-for="news in category.news?.slice(1, 5)" :key="news.id" class="col-12">
                                            <div class="card modern-card border-0 shadow-sm">
                                                <div class="row g-0">
                                                    <div class="col-4">
                                                        <img :src="news.image ? `/storage/images/${news.image}` : '/img/noimg.jpg'"
                                                             class="img-fluid h-100 w-100" alt="" style="object-fit: cover;" />
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="card-body p-3">
                                                            <a :href="route('news.show', news.id)" 
                                                               target="_blank" 
                                                               rel="noopener noreferrer"
                                                               class="text-decoration-none">
                                                                <h6 class="card-title mb-2 text-dark lh-base">
                                                                    {{ truncateText(news.title, 70) }}
                                                                </h6>
                                                            </a>
                                                            <div class="d-flex justify-content-between text-muted small">
                                                                <span><i class="fa fa-clock me-1"></i>{{ formatDate(news.created_at) }}</span>
                                                                <span><i class="fa fa-eye me-1"></i>{{ news.views }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="newsletter bg-primary py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="newsletter-content text-white">
                            <h3 class="h2 fw-bold mb-3">Dapatkan Update Berita Terbaru</h3>
                            <p class="mb-0 opacity-90">Berlangganan newsletter kami dan dapatkan ringkasan berita terpenting langsung di email Anda setiap hari.</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form class="newsletter-form">
                            <div class="input-group input-group-lg">
                                <input type="email" class="form-control rounded-start-pill" placeholder="Masukkan email Anda...">
                                <button class="btn btn-light rounded-end-pill px-4 fw-semibold" type="submit">
                                    Berlangganan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props from Laravel
const props = defineProps({
    latestNews: Array,
    popularNews: Array,
    topCategories: Array
})

// Geolocation reactive data
const geolocationStatus = ref('')
const geolocationError = ref(false)
const userLocation = ref(null)

// Computed properties for geolocation UI
const geolocationAlertClass = computed(() => {
    if (geolocationError.value) return 'alert-danger'
    if (geolocationStatus.value.includes('Lokasi ditemukan')) return 'alert-success'
    if (geolocationStatus.value.includes('Meminta')) return 'alert-primary'
    return 'alert-info'
})

const geolocationIcon = computed(() => {
    if (geolocationError.value) return 'fa-exclamation-circle'
    if (geolocationStatus.value.includes('Lokasi ditemukan')) return 'fa-check-circle'
    if (geolocationStatus.value.includes('Meminta')) return 'fa-spinner fa-spin'
    return 'fa-info-circle'
})

// Utility functions
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}

const formatDateTime = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const truncateText = (text, length) => {
    if (text.length <= length) return text
    return text.substring(0, length) + '...'
}

const truncateContent = (content, length) => {
    // Remove HTML tags and truncate
    const textContent = content.replace(/<[^>]*>/g, '')
    return truncateText(textContent, length)
}

// Route helper function for JavaScript
const route = (name, params = {}) => {
    const routes = {
        'news.show': (id) => `/news/${id}/show`
    }
    
    if (routes[name]) {
        return routes[name](params)
    }
    
    return '#'
}

// Geolocation functions
const haversineDistance = (lat1, lon1, lat2, lon2) => {
    const R = 6371 // Earth's radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180
    const dLon = (lon2 - lon1) * Math.PI / 180
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2)
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
    return R * c
}

const updateNewsWithDistance = (userLat, userLng) => {
    const allNews = [...props.latestNews, ...props.popularNews]
    
    allNews.forEach(news => {
        if (news.latitude && news.longitude) {
            const distance = haversineDistance(userLat, userLng, news.latitude, news.longitude)
            news.distance = distance
        }
    })

    // Update categories news as well
    props.topCategories.forEach(category => {
        if (category.news) {
            category.news.forEach(news => {
                if (news.latitude && news.longitude) {
                    const distance = haversineDistance(userLat, userLng, news.latitude, news.longitude)
                    news.distance = distance
                }
            })
        }
    })

    const localNewsCount = allNews.filter(news => news.distance && news.distance <= 100).length
    
    if (localNewsCount > 0) {
        geolocationStatus.value = `Ditemukan ${localNewsCount} berita di sekitar Anda`
        geolocationError.value = false
    } else {
        geolocationStatus.value = 'Tidak ada berita lokal di sekitar Anda'
        geolocationError.value = false
    }

    // Auto-hide after 5 seconds
    setTimeout(() => {
        geolocationStatus.value = ''
    }, 5000)
}

const getLocation = () => {
    console.log('getLocation() called')
    
    if (!navigator.geolocation) {
        geolocationStatus.value = 'Browser Anda tidak mendukung geolocation'
        geolocationError.value = true
        return
    }

    geolocationStatus.value = 'Meminta izin lokasi...'
    geolocationError.value = false

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const latitude = position.coords.latitude
            const longitude = position.coords.longitude
            
            console.log(`Lat: ${latitude}, Lng: ${longitude}`)
            
            geolocationStatus.value = `Lokasi ditemukan! Lat: ${latitude.toFixed(4)}, Lng: ${longitude.toFixed(4)}`
            geolocationError.value = false

            // Save to localStorage
            const locationData = {
                lat: latitude,
                lng: longitude,
                timestamp: Date.now()
            }
            localStorage.setItem('user_location', JSON.stringify(locationData))
            
            userLocation.value = locationData
            updateNewsWithDistance(latitude, longitude)
        },
        (error) => {
            let errorMessage = 'Gagal mendapatkan lokasi.'
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = 'Izin akses lokasi ditolak.'
                    break
                case error.POSITION_UNAVAILABLE:
                    errorMessage = 'Lokasi tidak tersedia.'
                    break
                case error.TIMEOUT:
                    errorMessage = 'Timeout saat mencari lokasi.'
                    break
            }
            
            geolocationStatus.value = errorMessage + ' Klik untuk coba lagi.'
            geolocationError.value = true
        },
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        }
    )
}

// Initialize geolocation on mount
onMounted(() => {
    console.log('Component mounted, initializing geolocation')
    
    // Check for saved location
    const savedLocation = localStorage.getItem('user_location')
    if (savedLocation) {
        try {
            const locationData = JSON.parse(savedLocation)
            const now = Date.now()
            const fifteenMinutes = 15 * 60 * 1000

            if (now - locationData.timestamp < fifteenMinutes) {
                console.log('Using cached location')
                geolocationStatus.value = 'Menggunakan lokasi tersimpan'
                geolocationError.value = false
                userLocation.value = locationData
                updateNewsWithDistance(locationData.lat, locationData.lng)
                return
            }
        } catch (e) {
            console.log('Error parsing saved location:', e)
        }
    }
    
    // Request new location after a delay
    setTimeout(() => {
        getLocation()
    }, 1000)
})
</script>

<style scoped>
/* Modern Design Variables */
:root {
    --primary-color: #3b82f6;
    --secondary-color: #1e40af;
    --success-color: #10b981;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --info-color: #06b6d4;
    --light-color: #f8fafc;
    --dark-color: #1e293b;
    --border-radius: 12px;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
}

/* Scrolling Text Animation */
.scrolling-text {
    display: inline-block;
    animation: scroll-left 30s linear infinite;
    white-space: nowrap;
}

@keyframes scroll-left {
    0% {
        transform: translateX(100%);
    }
    100% {
        transform: translateX(-100%);
    }
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 60vh;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
}

.min-vh-50 {
    min-height: 50vh;
}

.hero-title {
    font-weight: 800;
    letter-spacing: -0.025em;
    line-height: 1.1;
}

.hero-subtitle {
    font-weight: 400;
    line-height: 1.6;
}

.hero-stats .stat-item {
    text-align: center;
}

.hero-stats .stat-number {
    font-weight: 700;
    color: #ffffff;
}

.hero-stats .stat-label {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
}

/* Breaking News Banner */
.breaking-news-banner {
    background: linear-gradient(90deg, #dc2626 0%, #b91c1c 100%);
    border-bottom: 3px solid #991b1b;
}

.breaking-news-content marquee {
    font-weight: 500;
    font-size: 0.95rem;
}

/* Modern Cards */
.modern-card {
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.modern-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.hover-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 10px 10px -5px rgb(0 0 0 / 0.04);
}

/* Featured Article */
.featured-article .featured-image {
    transition: transform 0.6s ease;
}

.featured-article:hover .featured-image {
    transform: scale(1.05);
}

/* Author Avatar */
.author-avatar {
    font-weight: 600;
    font-size: 0.85rem;
}

/* Trending Items */
.trending-item {
    transition: background-color 0.2s ease;
}

.trending-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.trending-number .badge {
    font-weight: 700;
    font-size: 0.75rem;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.trending-title {
    font-weight: 600;
    line-height: 1.4;
}

/* Category Tabs */
.nav-pills .nav-link {
    border: 2px solid transparent;
    color: #64748b;
    font-weight: 600;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
    background-color: rgba(59, 130, 246, 0.1);
    color: var(--primary-color);
    border-color: rgba(59, 130, 246, 0.2);
}

.nav-pills .nav-link.active {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

/* Section Headers */
.section-header {
    margin-bottom: 3rem;
}

.section-title {
    font-weight: 800;
    letter-spacing: -0.025em;
    color: var(--dark-color);
}

.section-subtitle {
    font-weight: 400;
    line-height: 1.6;
}

/* Newsletter Section */
.newsletter {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
}

.newsletter-form .form-control {
    border: none;
    box-shadow: none;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.newsletter-form .form-control:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.3);
}

.newsletter-form .btn {
    font-weight: 600;
    border: none;
    padding: 0.75rem 2rem;
}

/* Card Meta */
.card-meta {
    font-size: 0.875rem;
}

.card-meta .author-avatar {
    background-color: #e2e8f0;
    color: #64748b;
}

/* Responsive Improvements */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-stats {
        justify-content: center;
        gap: 2rem;
    }
    
    .breaking-news-content marquee {
        font-size: 0.85rem;
    }
    
    .modern-card:hover {
        transform: none;
    }
    
    .hover-card:hover {
        transform: none;
    }
}

/* Custom Badges */
.badge {
    font-weight: 600;
    font-size: 0.75rem;
}

.rounded-pill {
    border-radius: 50rem;
}

/* Loading States */
.loading-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Link Styles */
a {
    text-decoration: none;
    transition: color 0.2s ease;
}

a:hover {
    color: var(--primary-color);
}

/* Button Styles */
.btn {
    font-weight: 600;
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
    transform: translateY(-1px);
}

.btn-outline-primary {
    color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-outline-primary:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    transform: translateY(-1px);
}
</style>
