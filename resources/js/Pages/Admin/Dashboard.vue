<template>
    <AdminLayout>
        <!-- Dashboard Header -->
        <div class="dashboard-header py-4 mb-4" style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="text-white fw-bold mb-2">Dashboard</h1>
                        <p class="text-white opacity-75 mb-0">Selamat datang kembali, {{ user?.name }}!</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="dashboard-date text-white">
                            <i class="fa fa-calendar me-2"></i>{{ getCurrentDate() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="container-fluid mb-4">
            <div class="row g-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stats-card card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-primary-light text-primary rounded-3 p-3 me-3">
                                    <i class="fa fa-newspaper fa-2x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="fw-bold mb-1">{{ stats.totalNews || 0 }}</h3>
                                    <p class="text-muted mb-0">Total Berita</p>
                                    <small class="text-success">
                                        <i class="fa fa-arrow-up me-1"></i>+12% bulan ini
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stats-card card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-success-light text-success rounded-3 p-3 me-3">
                                    <i class="fa fa-check-circle fa-2x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="fw-bold mb-1">{{ stats.publishedNews || 0 }}</h3>
                                    <p class="text-muted mb-0">Berita Published</p>
                                    <small class="text-success">
                                        <i class="fa fa-arrow-up me-1"></i>+8% bulan ini
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stats-card card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-warning-light text-warning rounded-3 p-3 me-3">
                                    <i class="fa fa-clock fa-2x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="fw-bold mb-1">{{ stats.pendingNews || 0 }}</h3>
                                    <p class="text-muted mb-0">Menunggu Review</p>
                                    <small class="text-warning">
                                        <i class="fa fa-exclamation me-1"></i>Perlu perhatian
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stats-card card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-info-light text-info rounded-3 p-3 me-3">
                                    <i class="fa fa-users fa-2x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h3 class="fw-bold mb-1">{{ stats.totalUsers || 0 }}</h3>
                                    <p class="text-muted mb-0">Total Users</p>
                                    <small class="text-success">
                                        <i class="fa fa-arrow-up me-1"></i>+5% bulan ini
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="container-fluid">
            <div class="row g-4">
                <!-- Recent News -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">
                                    <i class="fa fa-newspaper text-primary me-2"></i>Berita Terbaru
                                </h5>
                                <a :href="route('admin.news.manage')" class="btn btn-sm btn-outline-primary">
                                    Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div v-if="recentNews.length === 0" class="text-center py-4 text-muted">
                                <i class="fa fa-newspaper fa-3x mb-3 opacity-25"></i>
                                <p>Belum ada berita terbaru</p>
                            </div>
                            <div v-else class="news-list">
                                <div v-for="news in recentNews" :key="news.id" 
                                     class="news-item d-flex align-items-center p-3 border-bottom">
                                    <div class="news-image me-3">
                                        <img :src="news.image ? `/storage/images/${news.image}` : '/img/noimg.jpg'"
                                             :alt="news.title"
                                             class="rounded"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ truncateText(news.title, 60) }}</h6>
                                        <div class="d-flex align-items-center text-muted small">
                                            <span class="me-3">
                                                <i class="fa fa-user me-1"></i>{{ news.author?.name || 'Unknown' }}
                                            </span>
                                            <span class="me-3">
                                                <i class="fa fa-clock me-1"></i>{{ formatDate(news.created_at) }}
                                            </span>
                                            <span class="badge" 
                                                  :class="getStatusBadgeClass(news.status)">
                                                {{ news.status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="news-actions">
                                        <a :href="route('news.edit', news.id)" 
                                           class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a :href="route('news.show', news.id)" 
                                           class="btn btn-sm btn-outline-success">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Stats -->
                <div class="col-lg-4">
                    <!-- Quick Actions -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fa fa-bolt text-primary me-2"></i>Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a :href="route('news.create')" 
                                   class="btn btn-primary btn-lg">
                                    <i class="fa fa-plus me-2"></i>Buat Berita Baru
                                </a>
                                <a :href="route('admin.news.manage')" 
                                   class="btn btn-outline-primary">
                                    <i class="fa fa-list me-2"></i>Kelola Berita
                                </a>
                                <a :href="route('admin.categories.index')" 
                                   class="btn btn-outline-secondary">
                                    <i class="fa fa-tags me-2"></i>Kelola Kategori
                                </a>
                                <a v-if="user?.hasRole && (user.hasRole('Super Admin') || user.hasRole('Editor'))"
                                   :href="route('admin.users.index')" 
                                   class="btn btn-outline-info">
                                    <i class="fa fa-users me-2"></i>Kelola Users
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Category Distribution -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="fw-bold mb-0">
                                <i class="fa fa-chart-pie text-primary me-2"></i>Distribusi Kategori
                            </h5>
                        </div>
                        <div class="card-body">
                            <div v-if="categoryStats.length === 0" class="text-center py-4 text-muted">
                                <i class="fa fa-chart-pie fa-3x mb-3 opacity-25"></i>
                                <p>Belum ada data kategori</p>
                            </div>
                            <div v-else>
                                <!-- Doughnut Chart -->
                                <div class="chart-container mb-4" style="position: relative; height: 250px;">
                                    <canvas ref="categoryChart"></canvas>
                                </div>
                                
                                <!-- Category Legend -->
                                <div class="category-stats">
                                    <div v-for="category in categoryStats" :key="category.id"
                                         class="category-item d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="category-color me-2 rounded-circle" 
                                                 :style="{ backgroundColor: getCategoryColor(category.id) }"
                                                 style="width: 12px; height: 12px;"></div>
                                            <span class="fw-semibold">{{ category.name }}</span>
                                        </div>
                                        <span class="badge bg-light text-dark">{{ category.news_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed, onMounted, ref, nextTick, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Chart, registerables } from 'chart.js'

// Register Chart.js components
Chart.register(...registerables)

// Template refs
const categoryChart = ref(null)

// Props
const props = defineProps({
    user: Object,
    stats: {
        type: Object,
        default: () => ({})
    },
    recentNews: {
        type: Array,
        default: () => []
    },
    categoryStats: {
        type: Array,
        default: () => []
    }
})

// Chart instance
let categoryChartInstance = null

// Methods
const getCurrentDate = () => {
    return new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const truncateText = (text, length) => {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}

const getStatusBadgeClass = (status) => {
    const classes = {
        'Published': 'bg-success text-white',
        'Pending': 'bg-warning text-dark',
        'Draft': 'bg-secondary text-white',
        'Rejected': 'bg-danger text-white'
    }
    return classes[status] || 'bg-light text-dark'
}

const getCategoryColor = (categoryId) => {
    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16', '#f97316']
    return colors[categoryId % colors.length]
}

const initCategoryChart = () => {
    if (!categoryChart.value || props.categoryStats.length === 0) return

    // Destroy existing chart
    if (categoryChartInstance) {
        categoryChartInstance.destroy()
    }

    const ctx = categoryChart.value.getContext('2d')
    
    const chartData = {
        labels: props.categoryStats.map(cat => cat.name),
        datasets: [{
            data: props.categoryStats.map(cat => cat.news_count),
            backgroundColor: props.categoryStats.map((cat, index) => getCategoryColor(cat.id)),
            borderColor: '#ffffff',
            borderWidth: 2,
            hoverBorderWidth: 3,
            hoverOffset: 4
        }]
    }

    categoryChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // We'll use custom legend
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0)
                            const percentage = ((context.parsed / total) * 100).toFixed(1)
                            return `${context.label}: ${context.parsed} (${percentage}%)`
                        }
                    }
                }
            },
            cutout: '60%',
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    })
}

const route = (name, params = null) => {
    const routes = {
        'admin.news.manage': '/admin/news',
        'news.create': '/admin/news/create',
        'news.edit': params ? `/admin/news/${params}/edit` : '/admin/news/edit',
        'news.show': params ? `/news/${params}/show` : '/news/show',
        'admin.categories.index': '/admin/categories',
        'admin.users.index': '/admin/users'
    }
    return routes[name] || '#'
}

// Watch for changes in categoryStats
watch(() => props.categoryStats, () => {
    nextTick(() => {
        initCategoryChart()
    })
}, { immediate: true })

// Lifecycle
onMounted(() => {
    nextTick(() => {
        initCategoryChart()
    })
})
</script>

<style scoped>
.dashboard-header {
    border-radius: 0 0 1rem 1rem;
    margin: -1rem -1rem 2rem -1rem;
}

.stats-card {
    transition: all 0.3s ease;
    border-radius: 1rem;
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

.stats-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-primary-light {
    background-color: rgba(59, 130, 246, 0.1);
}

.bg-success-light {
    background-color: rgba(16, 185, 129, 0.1);
}

.bg-warning-light {
    background-color: rgba(245, 158, 11, 0.1);
}

.bg-info-light {
    background-color: rgba(6, 182, 212, 0.1);
}

.news-item {
    transition: all 0.3s ease;
    border-radius: 0.5rem;
    margin: 0.25rem 0;
}

.news-item:hover {
    background-color: rgba(59, 130, 246, 0.05);
}

.card {
    border-radius: 1rem;
}

.btn {
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.category-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.category-item:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .dashboard-header {
        text-align: center;
    }
    
    .news-actions {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
}
</style>
