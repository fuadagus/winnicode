<template>
    <AdminLayout :user="$page.props.auth.user" :notifications="notifications">
        <div class="category-view">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fa fa-tags me-2"></i>
                            {{ category.name }}
                        </h1>
                        <p class="page-subtitle text-muted">
                            {{ category.description || 'Lihat semua berita dalam kategori ini' }}
                        </p>
                    </div>
                    <div class="header-actions">
                        <Link href="/admin/categories" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-arrow-left me-1"></i>
                            Kembali
                        </Link>
                        <Link :href="`/admin/categories/${category.id}/edit`" class="btn btn-primary">
                            <i class="fa fa-edit me-1"></i>
                            Edit Kategori
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Category Stats -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary">
                            <i class="fa fa-newspaper"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ totalNews }}</h3>
                            <p class="stat-label">Total Berita</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-success">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ publishedNews }}</h3>
                            <p class="stat-label">Dipublikasi</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning">
                            <i class="fa fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ pendingNews }}</h3>
                            <p class="stat-label">Menunggu</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-info">
                            <i class="fa fa-eye"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ formatNumber(totalViews) }}</h3>
                            <p class="stat-label">Total Views</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="filter-section">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Cari Berita</label>
                                <div class="search-input">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        v-model="filters.search"
                                        placeholder="Cari judul berita..."
                                        @input="debounceSearch">
                                    <i class="fa fa-search search-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select class="form-select" v-model="filters.status" @change="applyFilters">
                                    <option value="">Semua Status</option>
                                    <option value="published">Dipublikasi</option>
                                    <option value="pending">Menunggu</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Penulis</label>
                                <select class="form-select" v-model="filters.author" @change="applyFilters">
                                    <option value="">Semua Penulis</option>
                                    <option v-for="author in authors" :key="author.id" :value="author.id">
                                        {{ author.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Urutkan</label>
                                <select class="form-select" v-model="filters.sort" @change="applyFilters">
                                    <option value="latest">Terbaru</option>
                                    <option value="oldest">Terlama</option>
                                    <option value="title">Judul A-Z</option>
                                    <option value="views">Views Tertinggi</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Per Halaman</label>
                                <select class="form-select" v-model="filters.perPage" @change="applyFilters">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- News List -->
            <div class="news-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="section-title">
                        Daftar Berita 
                        <span class="badge bg-primary ms-2">{{ news.total || 0 }}</span>
                    </h5>
                    <Link :href="`/admin/news/create?category=${category.id}`" class="btn btn-success">
                        <i class="fa fa-plus me-1"></i>
                        Tambah Berita
                    </Link>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat berita...</p>
                </div>

                <!-- News Grid -->
                <div v-else-if="news.data && news.data.length > 0" class="news-grid">
                    <div v-for="item in news.data" :key="item.id" class="news-card">
                        <div class="card h-100">
                            <div class="news-image">
                                <img :src="item.image ? `/storage/images/${item.image}` : '/img/noimg.jpg'" 
                                     :alt="item.title" 
                                     class="card-img-top">
                                <div class="news-overlay">
                                    <div class="news-actions">
                                        <Link :href="`/admin/news/${item.id}`" 
                                              class="btn btn-sm btn-primary" 
                                              title="Lihat Detail">
                                            <i class="fa fa-eye"></i>
                                        </Link>
                                        <Link :href="`/admin/news/${item.id}/edit`" 
                                              class="btn btn-sm btn-warning" 
                                              title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </Link>
                                        <button @click="deleteNews(item.id)" 
                                                class="btn btn-sm btn-danger" 
                                                title="Hapus">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="news-status">
                                    <span :class="getStatusClass(item.status)">
                                        {{ getStatusText(item.status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="news-title">{{ item.title }}</h6>
                                <p class="news-excerpt">{{ truncateText(item.content, 100) }}</p>
                                <div class="news-meta">
                                    <div class="meta-item">
                                        <i class="fa fa-user text-muted"></i>
                                        <span>{{ item.user?.name || 'Unknown' }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fa fa-calendar text-muted"></i>
                                        <span>{{ formatDate(item.created_at) }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fa fa-eye text-muted"></i>
                                        <span>{{ formatNumber(item.views || 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="empty-state">
                    <div class="text-center py-5">
                        <i class="fa fa-newspaper empty-icon"></i>
                        <h5 class="mt-3">Tidak Ada Berita</h5>
                        <p class="text-muted">
                            Belum ada berita dalam kategori ini.
                            <br>Mulai tambahkan berita pertama Anda.
                        </p>
                        <Link :href="`/admin/news/create?category=${category.id}`" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i>
                            Tambah Berita Pertama
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="news.links && news.links.length > 3" class="pagination-wrapper">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li v-for="link in news.links" :key="link.label" class="page-item" 
                                :class="{ 'active': link.active, 'disabled': !link.url }">
                                <button v-if="link.url" 
                                        @click="changePage(link.url)" 
                                        class="page-link"
                                        v-html="link.label">
                                </button>
                                <span v-else class="page-link" v-html="link.label"></span>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
const props = defineProps({
    category: Object,
    news: Object,
    authors: Array,
    notifications: {
        type: Array,
        default: () => []
    }
})

// Reactive data
const loading = ref(false)
const searchTimeout = ref(null)
const filters = reactive({
    search: '',
    status: '',
    author: '',
    sort: 'latest',
    perPage: '10'
})

// Computed
const totalNews = computed(() => props.news?.total || 0)
const publishedNews = computed(() => 
    props.news?.data?.filter(item => item.status === 'published').length || 0
)
const pendingNews = computed(() => 
    props.news?.data?.filter(item => item.status === 'pending').length || 0
)
const totalViews = computed(() => 
    props.news?.data?.reduce((sum, item) => sum + (item.views || 0), 0) || 0
)

// Methods
const debounceSearch = () => {
    clearTimeout(searchTimeout.value)
    searchTimeout.value = setTimeout(() => {
        applyFilters()
    }, 500)
}

const applyFilters = () => {
    loading.value = true
    
    const params = {
        category: props.category.id,
        ...filters
    }
    
    router.get(`/admin/categories/${props.category.id}/view`, params, {
        preserveState: true,
        onFinish: () => {
            loading.value = false
        }
    })
}

const changePage = (url) => {
    loading.value = true
    router.get(url, {}, {
        preserveState: true,
        onFinish: () => {
            loading.value = false
        }
    })
}

const deleteNews = (newsId) => {
    if (confirm('Apakah Anda yakin ingin menghapus berita ini?')) {
        router.delete(`/admin/news/${newsId}`, {
            onSuccess: () => {
                // Refresh the page data
                applyFilters()
            }
        })
    }
}

const getStatusClass = (status) => {
    const classes = {
        published: 'badge bg-success',
        pending: 'badge bg-warning',
        draft: 'badge bg-secondary',
        rejected: 'badge bg-danger'
    }
    return classes[status] || 'badge bg-secondary'
}

const getStatusText = (status) => {
    const texts = {
        published: 'Dipublikasi',
        pending: 'Menunggu',
        draft: 'Draft',
        rejected: 'Ditolak'
    }
    return texts[status] || 'Unknown'
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatNumber = (number) => {
    if (number >= 1000000) {
        return (number / 1000000).toFixed(1) + 'M'
    } else if (number >= 1000) {
        return (number / 1000).toFixed(1) + 'K'
    }
    return number.toString()
}

const truncateText = (text, length) => {
    if (!text) return ''
    if (text.length <= length) return text
    return text.substring(0, length) + '...'
}

// Lifecycle
onMounted(() => {
    // Initialize filters from URL params if any
    const urlParams = new URLSearchParams(window.location.search)
    if (urlParams.get('search')) filters.search = urlParams.get('search')
    if (urlParams.get('status')) filters.status = urlParams.get('status')
    if (urlParams.get('author')) filters.author = urlParams.get('author')
    if (urlParams.get('sort')) filters.sort = urlParams.get('sort')
    if (urlParams.get('perPage')) filters.perPage = urlParams.get('perPage')
})
</script>

<style scoped>
/* Page Header */
.page-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    color: white;
    padding: 2rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
}

.header-actions .btn {
    border-radius: 0.5rem;
    font-weight: 500;
}

/* Stats Cards */
.stat-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-right: 1rem;
}

.stat-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: #1e293b;
}

.stat-content p {
    color: #64748b;
    margin: 0;
    font-weight: 500;
}

/* Filter Section */
.filter-section {
    margin-bottom: 2rem;
}

.filter-section .card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border-radius: 1rem;
}

.search-input {
    position: relative;
}

.search-input .form-control {
    padding-left: 2.5rem;
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
}

.search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
}

/* News Section */
.news-section .card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.section-title {
    color: #1e293b;
    font-weight: 600;
    margin-bottom: 0;
}

/* News Grid */
.news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.news-card .card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border-radius: 1rem;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.news-card .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
}

.news-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.news-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.news-card:hover .news-image img {
    transform: scale(1.05);
}

.news-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.news-card:hover .news-overlay {
    opacity: 1;
}

.news-actions {
    display: flex;
    gap: 0.5rem;
}

.news-status {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

.news-status .badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
}

.news-title {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.news-excerpt {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.news-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    font-size: 0.8rem;
    color: #64748b;
}

.meta-item i {
    width: 16px;
    margin-right: 0.5rem;
}

/* Empty State */
.empty-state {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.empty-icon {
    font-size: 4rem;
    color: #d1d5db;
}

/* Pagination */
.pagination-wrapper {
    margin-top: 2rem;
}

.pagination .page-link {
    border: none;
    color: #3b82f6;
    font-weight: 500;
    padding: 0.75rem 1rem;
    margin: 0 0.25rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.pagination .page-item.active .page-link {
    background: #3b82f6;
    color: white;
}

.pagination .page-link:hover {
    background: #eff6ff;
    color: #1e40af;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .header-actions {
        margin-top: 1rem;
    }
    
    .header-actions .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .news-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-section .row > div {
        margin-bottom: 1rem;
    }
}
</style>
