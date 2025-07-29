<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="row align-items-center">
                    <div class="col">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2">
                                <li class="breadcrumb-item">
                                    <a href="/dashboard" class="text-decoration-none">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="/admin/categories" class="text-decoration-none">Kategori</a>
                                </li>
                                <li class="breadcrumb-item active">{{ category.name }}</li>
                            </ol>
                        </nav>
                        <h2 class="page-title text-dark fw-bold mb-0">
                            <i class="fa fa-tag text-primary me-2"></i>
                            {{ category.name }}
                        </h2>
                        <p class="text-muted mb-0">{{ category.news_count }} berita tersedia</p>
                    </div>
                    <div class="col-auto">
                        <a href="/admin/news/create" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>
                            Tambah Berita
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input v-model="filters.search" 
                                               type="text" 
                                               class="form-control" 
                                               placeholder="Cari berita...">
                                        <span class="input-group-text">
                                            <i class="fa fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select v-model="filters.status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="published">Published</option>
                                        <option value="draft">Draft</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select v-model="filters.sortBy" class="form-select">
                                        <option value="created_at">Terbaru</option>
                                        <option value="title">Judul A-Z</option>
                                        <option value="views">Paling Dilihat</option>
                                        <option value="updated_at">Terakhir Diupdate</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button @click="toggleView" class="btn btn-outline-secondary w-100">
                                        <i :class="viewMode === 'grid' ? 'fa fa-list' : 'fa fa-th'"></i>
                                        {{ viewMode === 'grid' ? 'List' : 'Grid' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- News Grid/List -->
            <div class="row">
                <!-- Grid View -->
                <template v-if="viewMode === 'grid'">
                    <div v-for="news in filteredNews" :key="news.id" class="col-lg-4 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm news-card">
                            <div class="news-image">
                                <img :src="news.image ? `/storage/images/${news.image}` : '/img/noimg.jpg'" 
                                     :alt="news.title" 
                                     class="card-img-top">
                                <div class="news-status">
                                    <span class="badge" :class="getStatusClass(news.status)">
                                        {{ getStatusText(news.status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title text-truncate">{{ news.title }}</h5>
                                <p class="card-text text-muted small mb-2">
                                    <i class="fa fa-user me-1"></i>{{ news.author?.name }}
                                    <span class="mx-2">•</span>
                                    <i class="fa fa-calendar me-1"></i>{{ formatDate(news.created_at) }}
                                </p>
                                <p class="card-text text-muted small mb-3">
                                    <i class="fa fa-eye me-1"></i>{{ news.views || 0 }} views
                                    <span class="mx-2">•</span>
                                    <i class="fa fa-heart me-1"></i>{{ news.likes_count || 0 }} likes
                                </p>
                                <div class="d-flex gap-2">
                                    <a :href="`/admin/news/${news.id}`" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-eye me-1"></i>Lihat
                                    </a>
                                    <a :href="`/admin/news/${news.id}/edit`" class="btn btn-sm btn-outline-warning">
                                        <i class="fa fa-edit me-1"></i>Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- List View -->
                <template v-else>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4 py-3">Berita</th>
                                                <th class="px-4 py-3">Penulis</th>
                                                <th class="px-4 py-3">Status</th>
                                                <th class="px-4 py-3">Views</th>
                                                <th class="px-4 py-3">Tanggal</th>
                                                <th class="px-4 py-3 text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="news in filteredNews" :key="news.id">
                                                <td class="px-4 py-3">
                                                    <div class="d-flex align-items-center">
                                                        <img :src="news.image ? `/storage/images/${news.image}` : '/img/noimg.jpg'" 
                                                             :alt="news.title"
                                                             class="news-thumbnail me-3">
                                                        <div>
                                                            <h6 class="mb-1 fw-medium">{{ news.title }}</h6>
                                                            <small class="text-muted">ID: {{ news.id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">{{ news.author?.name || 'Unknown' }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="badge" :class="getStatusClass(news.status)">
                                                        {{ getStatusText(news.status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">{{ news.views || 0 }}</td>
                                                <td class="px-4 py-3">{{ formatDate(news.created_at) }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="btn-group" role="group">
                                                        <a :href="`/admin/news/${news.id}`" 
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Lihat">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a :href="`/admin/news/${news.id}/edit`" 
                                                           class="btn btn-sm btn-outline-warning"
                                                           title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if="filteredNews.length === 0">
                                                <td colspan="6" class="text-center py-5 text-muted">
                                                    <i class="fa fa-newspaper fa-3x mb-3 opacity-25"></i>
                                                    <p class="mb-0">Tidak ada berita yang ditemukan</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- No News -->
                <div v-if="filteredNews.length === 0 && viewMode === 'grid'" class="col-12">
                    <div class="text-center py-5">
                        <i class="fa fa-newspaper fa-5x text-muted opacity-25 mb-4"></i>
                        <h4 class="text-muted">Belum ada berita di kategori ini</h4>
                        <p class="text-muted mb-4">Mulai tambahkan berita untuk kategori {{ category.name }}</p>
                        <a href="/admin/news/create" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>
                            Tambah Berita Pertama
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="news.length > perPage" class="row mt-4">
                <div class="col-12">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                <button @click="currentPage = Math.max(1, currentPage - 1)" 
                                        class="page-link">Previous</button>
                            </li>
                            <li v-for="page in totalPages" 
                                :key="page" 
                                class="page-item" 
                                :class="{ active: currentPage === page }">
                                <button @click="currentPage = page" class="page-link">{{ page }}</button>
                            </li>
                            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" 
                                        class="page-link">Next</button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
const props = defineProps({
    category: Object,
    news: Array
})

// State
const viewMode = ref('grid')
const currentPage = ref(1)
const perPage = ref(12)
const filters = ref({
    search: '',
    status: '',
    sortBy: 'created_at'
})

// Computed
const filteredNews = computed(() => {
    let filtered = [...props.news]
    
    // Search filter
    if (filters.value.search) {
        filtered = filtered.filter(news =>
            news.title.toLowerCase().includes(filters.value.search.toLowerCase()) ||
            (news.author?.name || '').toLowerCase().includes(filters.value.search.toLowerCase())
        )
    }
    
    // Status filter
    if (filters.value.status) {
        filtered = filtered.filter(news => news.status === filters.value.status)
    }
    
    // Sort
    filtered.sort((a, b) => {
        switch (filters.value.sortBy) {
            case 'title':
                return a.title.localeCompare(b.title)
            case 'views':
                return (b.views || 0) - (a.views || 0)
            case 'updated_at':
                return new Date(b.updated_at) - new Date(a.updated_at)
            default:
                return new Date(b.created_at) - new Date(a.created_at)
        }
    })
    
    // Pagination
    const start = (currentPage.value - 1) * perPage.value
    return filtered.slice(start, start + perPage.value)
})

const totalPages = computed(() => {
    return Math.ceil(props.news.length / perPage.value)
})

// Methods
const toggleView = () => {
    viewMode.value = viewMode.value === 'grid' ? 'list' : 'grid'
}

const getStatusClass = (status) => {
    switch (status) {
        case 'published': return 'bg-success'
        case 'draft': return 'bg-secondary'
        case 'pending': return 'bg-warning'
        default: return 'bg-secondary'
    }
}

const getStatusText = (status) => {
    switch (status) {
        case 'published': return 'Published'
        case 'draft': return 'Draft'
        case 'pending': return 'Pending'
        default: return 'Unknown'
    }
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

// Watch for filter changes to reset pagination
watch(filters, () => {
    currentPage.value = 1
}, { deep: true })
</script>

<style scoped>
.page-header {
    padding: 1.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.page-title {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
}

.news-card {
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.news-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.news-image {
    position: relative;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
}

.news-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s;
}

.news-card:hover .news-image img {
    transform: scale(1.05);
}

.news-status {
    position: absolute;
    top: 12px;
    right: 12px;
}

.news-thumbnail {
    width: 60px;
    height: 45px;
    object-fit: cover;
    border-radius: 6px;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e9ecef;
}

.btn-group .btn {
    border-radius: 6px;
    margin: 0 2px;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
}

.card {
    border-radius: 12px;
}
</style>
