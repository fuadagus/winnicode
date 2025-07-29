<template>
    <AdminLayout :user="$page.props.auth.user" :notifications="notifications">
        <div class="news-index">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fa fa-newspaper me-2"></i>
                            Kelola Berita
                        </h1>
                        <p class="page-subtitle text-muted">
                            Kelola semua berita dan artikel Anda
                        </p>
                    </div>
                    <div class="header-actions">
                        <Link href="/admin/news/create" class="btn btn-success">
                            <i class="fa fa-plus me-1"></i>
                            Buat Berita Baru
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary">
                            <i class="fa fa-newspaper"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ stats.total || 0 }}</h3>
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
                            <h3 class="stat-number">{{ stats.published || 0 }}</h3>
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
                            <h3 class="stat-number">{{ stats.pending || 0 }}</h3>
                            <p class="stat-label">Menunggu</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-secondary">
                            <i class="fa fa-edit"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ stats.draft || 0 }}</h3>
                            <p class="stat-label">Draft</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Actions -->
            <div class="filters-section">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Cari Berita</label>
                                <div class="search-input">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        v-model="filters.search"
                                        placeholder="Cari judul, konten..."
                                        @input="debounceSearch">
                                    <i class="fa fa-search search-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select class="form-select" v-model="filters.status" @change="applyFilters">
                                    <option value="">Semua Status</option>
                                    <option value="Published">Dipublikasi</option>
                                    <option value="Pending">Menunggu</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Rejected">Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" v-model="filters.category" @change="applyFilters">
                                    <option value="">Semua Kategori</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
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
                                    <option value="likes">Likes Terbanyak</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button @click="resetFilters" class="btn btn-outline-secondary w-100" title="Reset Filter">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Bulk Actions -->
                        <div v-if="selectedNews.length > 0" class="bulk-actions mt-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted">
                                    {{ selectedNews.length }} berita dipilih
                                </span>
                                <div class="bulk-buttons">
                                    <button @click="bulkPublish" class="btn btn-sm btn-success me-2">
                                        <i class="fa fa-check me-1"></i>
                                        Publikasikan
                                    </button>
                                    <button @click="bulkDraft" class="btn btn-sm btn-secondary me-2">
                                        <i class="fa fa-edit me-1"></i>
                                        Jadikan Draft
                                    </button>
                                    <button @click="bulkDelete" class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash me-1"></i>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- News List -->
            <div class="news-list-section">
                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat berita...</p>
                </div>

                <!-- News Table -->
                <div v-else-if="news.data && news.data.length > 0" class="card">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">
                                        <div class="form-check">
                                            <input 
                                                type="checkbox" 
                                                class="form-check-input" 
                                                v-model="selectAll"
                                                @change="toggleSelectAll">
                                        </div>
                                    </th>
                                    <th style="width: 80px;">Gambar</th>
                                    <th>Judul</th>
                                    <th style="width: 120px;">Kategori</th>
                                    <th style="width: 100px;">Penulis</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 80px;">Views</th>
                                    <th style="width: 120px;">Tanggal</th>
                                    <th style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in news.data" :key="item.id">
                                    <td>
                                        <div class="form-check">
                                            <input 
                                                type="checkbox" 
                                                class="form-check-input" 
                                                :value="item.id"
                                                v-model="selectedNews">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="news-image-thumb">
                                            <img :src="item.image ? `/storage/images/${item.image}` : '/img/noimg.jpg'" 
                                                 :alt="item.title">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="news-title-cell">
                                            <h6 class="news-title mb-1">{{ item.title }}</h6>
                                            <p class="news-excerpt text-muted">
                                                {{ truncateText(item.content, 100) }}
                                            </p>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ item.category?.name || 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="author-cell">
                                            <small class="d-block">{{ item.author?.name || 'Unknown' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span :class="getStatusClass(item.status)">
                                            {{ getStatusText(item.status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="views-count">
                                            <i class="fa fa-eye text-muted me-1"></i>
                                            {{ formatNumber(item.views || 0) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ formatDate(item.created_at) }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <Link :href="`/admin/news/${item.id}`" 
                                                  class="btn btn-sm btn-outline-primary" 
                                                  title="Lihat">
                                                <i class="fa fa-eye"></i>
                                            </Link>
                                            <Link :href="`/admin/news/${item.id}/edit`" 
                                                  class="btn btn-sm btn-outline-warning" 
                                                  title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </Link>
                                            <button @click="deleteNews(item.id)" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="empty-state">
                    <div class="text-center py-5">
                        <i class="fa fa-newspaper empty-icon"></i>
                        <h5 class="mt-3">Tidak Ada Berita</h5>
                        <p class="text-muted">
                            Belum ada berita yang dibuat.
                            <br>Mulai buat berita pertama Anda.
                        </p>
                        <Link href="/admin/news/create" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i>
                            Buat Berita Pertama
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
    news: Object,
    categories: Array,
    authors: Array,
    stats: Object,
    notifications: {
        type: Array,
        default: () => []
    }
})

// Reactive data
const loading = ref(false)
const searchTimeout = ref(null)
const selectedNews = ref([])
const selectAll = ref(false)

const filters = reactive({
    search: '',
    status: '',
    category: '',
    author: '',
    sort: 'latest'
})

// Methods
const debounceSearch = () => {
    clearTimeout(searchTimeout.value)
    searchTimeout.value = setTimeout(() => {
        applyFilters()
    }, 500)
}

const applyFilters = () => {
    loading.value = true
    
    router.get('/admin/news', filters, {
        preserveState: true,
        onFinish: () => {
            loading.value = false
        }
    })
}

const resetFilters = () => {
    Object.keys(filters).forEach(key => {
        filters[key] = key === 'sort' ? 'latest' : ''
    })
    applyFilters()
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

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedNews.value = props.news.data.map(item => item.id)
    } else {
        selectedNews.value = []
    }
}

const bulkPublish = () => {
    if (confirm(`Publikasikan ${selectedNews.value.length} berita yang dipilih?`)) {
        router.patch('/admin/news/bulk-update', {
            ids: selectedNews.value,
            status: 'published'
        }, {
            onSuccess: () => {
                selectedNews.value = []
                selectAll.value = false
            }
        })
    }
}

const bulkDraft = () => {
    if (confirm(`Jadikan draft ${selectedNews.value.length} berita yang dipilih?`)) {
        router.patch('/admin/news/bulk-update', {
            ids: selectedNews.value,
            status: 'draft'
        }, {
            onSuccess: () => {
                selectedNews.value = []
                selectAll.value = false
            }
        })
    }
}

const bulkDelete = () => {
    if (confirm(`Hapus ${selectedNews.value.length} berita yang dipilih? Tindakan ini tidak dapat dibatalkan.`)) {
        router.delete('/admin/news/bulk-delete', {
            data: { ids: selectedNews.value }
        }, {
            onSuccess: () => {
                selectedNews.value = []
                selectAll.value = false
            }
        })
    }
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
        'Published': 'badge bg-success',
        'published': 'badge bg-success',
        'Pending': 'badge bg-warning',
        'pending': 'badge bg-warning',
        'Draft': 'badge bg-secondary',
        'draft': 'badge bg-secondary',
        'Rejected': 'badge bg-danger',
        'rejected': 'badge bg-danger'
    }
    return classes[status] || 'badge bg-secondary'
}

const getStatusText = (status) => {
    const texts = {
        'Published': 'Dipublikasi',
        'published': 'Dipublikasi',
        'Pending': 'Menunggu',
        'pending': 'Menunggu',
        'Draft': 'Draft',
        'draft': 'Draft',
        'Rejected': 'Ditolak',
        'rejected': 'Ditolak'
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
    const plainText = text.replace(/<[^>]*>/g, '')
    if (plainText.length <= length) return plainText
    return plainText.substring(0, length) + '...'
}

// Lifecycle
onMounted(() => {
    // Initialize filters from URL params if any
    const urlParams = new URLSearchParams(window.location.search)
    Object.keys(filters).forEach(key => {
        if (urlParams.get(key)) {
            filters[key] = urlParams.get(key)
        }
    })
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

/* Filters Section */
.filters-section {
    margin-bottom: 2rem;
}

.filters-section .card {
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

/* Bulk Actions */
.bulk-actions {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
}

.bulk-buttons .btn {
    border-radius: 0.375rem;
}

/* News List */
.news-list-section .card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border-radius: 1rem;
    overflow: hidden;
    margin-bottom: 2rem;
}

/* Table Styling */
.table th {
    background: #f8fafc;
    color: #374151;
    font-weight: 600;
    border: none;
    padding: 1rem 0.75rem;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-color: #f1f5f9;
}

.table tbody tr:hover {
    background: #f8fafc;
}

/* News Image Thumbnail */
.news-image-thumb {
    width: 60px;
    height: 45px;
    border-radius: 0.5rem;
    overflow: hidden;
}

.news-image-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* News Title Cell */
.news-title-cell .news-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.3;
    margin-bottom: 0.25rem;
}

.news-excerpt {
    font-size: 0.8rem;
    line-height: 1.4;
    margin: 0;
}

/* Status Badges */
.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
}

/* Views Count */
.views-count {
    font-size: 0.85rem;
    color: #64748b;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.action-buttons .btn {
    padding: 0.375rem 0.5rem;
    border-radius: 0.375rem;
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
    
    .filters-section .row > div {
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .news-title-cell .news-title {
        font-size: 0.8rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
