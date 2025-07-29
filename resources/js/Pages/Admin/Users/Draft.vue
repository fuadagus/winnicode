<template>
    <AdminLayout>
        <!-- Header -->
        <div class="page-header mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="fw-bold mb-2">{{ currentUser?.name }} Draft</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <Link href="/dashboard" class="text-decoration-none">
                                    <i class="fa fa-home me-1"></i>Dashboard
                                </Link>
                            </li>
                            <li class="breadcrumb-item">Draft</li>
                            <li class="breadcrumb-item active">My Draft</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $page.props.flash.error }}
            <button type="button" class="btn-close" @click="dismissAlert" aria-label="Close"></button>
        </div>

        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $page.props.flash.success }}
            <button type="button" class="btn-close" @click="dismissAlert" aria-label="Close"></button>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-gradient p-3 rounded-3">
                                    <i class="fa fa-edit text-white fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Draft Articles</h6>
                                <h4 class="mb-0">{{ draftCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-gradient p-3 rounded-3">
                                    <i class="fa fa-clock text-white fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Pending Review</h6>
                                <h4 class="mb-0">{{ pendingCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-gradient p-3 rounded-3">
                                    <i class="fa fa-check-circle text-white fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Published</h6>
                                <h4 class="mb-0">{{ publishedCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-danger bg-gradient p-3 rounded-3">
                                    <i class="fa fa-times-circle text-white fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Rejected</h6>
                                <h4 class="mb-0">{{ rejectedCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-line nav-color-secondary justify-content-center mb-4" role="tablist">
                    <li class="nav-item">
                        <button 
                            class="nav-link"
                            :class="{ active: activeTab === 'draft' }"
                            @click="activeTab = 'draft'"
                            type="button">
                            <i class="fa fa-edit me-2"></i>Draft & Pending
                        </button>
                    </li>
                    <li class="nav-item">
                        <button 
                            class="nav-link"
                            :class="{ active: activeTab === 'published' }"
                            @click="activeTab = 'published'"
                            type="button">
                            <i class="fa fa-check-circle me-2"></i>Published
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Draft & Pending Tab -->
                    <div v-show="activeTab === 'draft'" class="tab-pane">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="fa fa-list text-warning me-2"></i>Draft & Pending Articles
                            </h5>
                            <div class="d-flex align-items-center">
                                <span class="text-muted me-3">Total: {{ notAcceptedNews.length }} articles</span>
                                <Link href="/news/create" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus me-1"></i>Create New
                                </Link>
                            </div>
                        </div>

                        <!-- Search & Filter -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa fa-search"></i>
                                    </span>
                                    <input 
                                        v-model="searchTerm"
                                        type="text"
                                        class="form-control"
                                        placeholder="Search articles...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select v-model="statusFilter" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Reject">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select v-model="categoryFilter" class="form-select">
                                    <option value="">All Categories</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.name">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th style="width: 80px;">ID</th>
                                        <th>Title</th>
                                        <th style="width: 150px;">Category</th>
                                        <th style="width: 150px;">Updated At</th>
                                        <th style="width: 120px;">Status</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="filteredDraftNews.length === 0">
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fa fa-newspaper fa-3x mb-3 opacity-25"></i>
                                            <div>{{ searchTerm ? 'No articles found' : 'No draft articles yet' }}</div>
                                        </td>
                                    </tr>
                                    <tr v-else v-for="(news, index) in paginatedDraftNews" :key="news.id" class="news-row">
                                        <td>{{ (currentDraftPage - 1) * perPage + index + 1 }}</td>
                                        <td><span class="badge bg-secondary">{{ news.id }}</span></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">{{ truncateText(news.title, 50) }}</span>
                                                <small class="text-muted">{{ truncateText(news.description, 80) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ news.category.name }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ formatDate(news.updated_at) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge" :class="getStatusBadgeClass(news.status)">
                                                {{ news.status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <Link 
                                                    v-if="news.status === 'Reject'"
                                                    :href="`/news/${news.id}/edit`"
                                                    class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="tooltip"
                                                    title="Edit Article">
                                                    <i class="fa fa-edit"></i>
                                                </Link>
                                                <Link 
                                                    v-else-if="news.status === 'Pending'"
                                                    :href="`/news/${news.id}/view`"
                                                    class="btn btn-sm btn-outline-info"
                                                    data-bs-toggle="tooltip"
                                                    title="View Article">
                                                    <i class="fa fa-eye"></i>
                                                </Link>
                                                <Link 
                                                    v-else
                                                    :href="`/news/${news.id}/edit`"
                                                    class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="tooltip"
                                                    title="Edit Draft">
                                                    <i class="fa fa-edit"></i>
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination for Draft -->
                        <div class="d-flex justify-content-between align-items-center mt-3" v-if="filteredDraftNews.length > 0">
                            <div class="text-muted">
                                Showing {{ (currentDraftPage - 1) * perPage + 1 }} - {{ Math.min(currentDraftPage * perPage, filteredDraftNews.length) }} 
                                of {{ filteredDraftNews.length }} articles
                            </div>
                            <nav>
                                <ul class="pagination mb-0">
                                    <li class="page-item" :class="{ disabled: currentDraftPage === 1 }">
                                        <button class="page-link" @click="currentDraftPage = 1" :disabled="currentDraftPage === 1">
                                            <i class="fa fa-angle-double-left"></i>
                                        </button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: currentDraftPage === 1 }">
                                        <button class="page-link" @click="currentDraftPage--" :disabled="currentDraftPage === 1">
                                            <i class="fa fa-angle-left"></i>
                                        </button>
                                    </li>
                                    <li v-for="page in visibleDraftPages" :key="page" 
                                        class="page-item" :class="{ active: page === currentDraftPage }">
                                        <button class="page-link" @click="currentDraftPage = page">{{ page }}</button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: currentDraftPage === totalDraftPages }">
                                        <button class="page-link" @click="currentDraftPage++" :disabled="currentDraftPage === totalDraftPages">
                                            <i class="fa fa-angle-right"></i>
                                        </button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: currentDraftPage === totalDraftPages }">
                                        <button class="page-link" @click="currentDraftPage = totalDraftPages" :disabled="currentDraftPage === totalDraftPages">
                                            <i class="fa fa-angle-double-right"></i>
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>

                    <!-- Published Tab -->
                    <div v-show="activeTab === 'published'" class="tab-pane">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="fa fa-list text-success me-2"></i>Published Articles
                            </h5>
                            <span class="text-muted">Total: {{ acceptedNews.length }} articles</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">No</th>
                                        <th style="width: 80px;">ID</th>
                                        <th>Title</th>
                                        <th style="width: 150px;">Category</th>
                                        <th style="width: 150px;">Updated At</th>
                                        <th style="width: 120px;">Status</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="acceptedNews.length === 0">
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fa fa-newspaper fa-3x mb-3 opacity-25"></i>
                                            <div>No published articles yet</div>
                                        </td>
                                    </tr>
                                    <tr v-else v-for="(news, index) in paginatedPublishedNews" :key="news.id" class="news-row">
                                        <td>{{ (currentPublishedPage - 1) * perPage + index + 1 }}</td>
                                        <td><span class="badge bg-secondary">{{ news.id }}</span></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">{{ truncateText(news.title, 50) }}</span>
                                                <small class="text-muted">{{ truncateText(news.description, 80) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ news.category.name }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ formatDate(news.updated_at) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ news.status }}</span>
                                        </td>
                                        <td>
                                            <Link 
                                                :href="`/news/${news.id}`"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="tooltip"
                                                title="View Article">
                                                <i class="fa fa-eye"></i>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination for Published -->
                        <div class="d-flex justify-content-between align-items-center mt-3" v-if="acceptedNews.length > 0">
                            <div class="text-muted">
                                Showing {{ (currentPublishedPage - 1) * perPage + 1 }} - {{ Math.min(currentPublishedPage * perPage, acceptedNews.length) }} 
                                of {{ acceptedNews.length }} articles
                            </div>
                            <nav>
                                <ul class="pagination mb-0">
                                    <li class="page-item" :class="{ disabled: currentPublishedPage === 1 }">
                                        <button class="page-link" @click="currentPublishedPage = 1" :disabled="currentPublishedPage === 1">
                                            <i class="fa fa-angle-double-left"></i>
                                        </button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: currentPublishedPage === 1 }">
                                        <button class="page-link" @click="currentPublishedPage--" :disabled="currentPublishedPage === 1">
                                            <i class="fa fa-angle-left"></i>
                                        </button>
                                    </li>
                                    <li v-for="page in visiblePublishedPages" :key="page" 
                                        class="page-item" :class="{ active: page === currentPublishedPage }">
                                        <button class="page-link" @click="currentPublishedPage = page">{{ page }}</button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: currentPublishedPage === totalPublishedPages }">
                                        <button class="page-link" @click="currentPublishedPage++" :disabled="currentPublishedPage === totalPublishedPages">
                                            <i class="fa fa-angle-right"></i>
                                        </button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: currentPublishedPage === totalPublishedPages }">
                                        <button class="page-link" @click="currentPublishedPage = totalPublishedPages" :disabled="currentPublishedPage === totalPublishedPages">
                                            <i class="fa fa-angle-double-right"></i>
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
const props = defineProps({
    acceptedNews: {
        type: Array,
        default: () => []
    },
    notAcceptedNews: {
        type: Array,
        default: () => []
    },
    currentUser: Object
})

// Reactive data
const activeTab = ref('draft')
const searchTerm = ref('')
const statusFilter = ref('')
const categoryFilter = ref('')
const perPage = ref(10)
const currentDraftPage = ref(1)
const currentPublishedPage = ref(1)

// Computed
const categories = computed(() => {
    const allCategories = [...props.acceptedNews, ...props.notAcceptedNews]
        .map(news => news.category)
        .filter((category, index, self) => 
            index === self.findIndex(c => c.id === category.id)
        )
    return allCategories
})

const draftCount = computed(() => 
    props.notAcceptedNews.filter(news => news.status === 'Draft').length
)

const pendingCount = computed(() => 
    props.notAcceptedNews.filter(news => news.status === 'Pending').length
)

const rejectedCount = computed(() => 
    props.notAcceptedNews.filter(news => news.status === 'Reject').length
)

const publishedCount = computed(() => props.acceptedNews.length)

const filteredDraftNews = computed(() => {
    let filtered = [...props.notAcceptedNews]

    if (searchTerm.value) {
        const search = searchTerm.value.toLowerCase()
        filtered = filtered.filter(news => 
            news.title.toLowerCase().includes(search) ||
            news.description?.toLowerCase().includes(search) ||
            news.category.name.toLowerCase().includes(search)
        )
    }

    if (statusFilter.value) {
        filtered = filtered.filter(news => news.status === statusFilter.value)
    }

    if (categoryFilter.value) {
        filtered = filtered.filter(news => news.category.name === categoryFilter.value)
    }

    return filtered
})

const totalDraftPages = computed(() => Math.ceil(filteredDraftNews.value.length / perPage.value))
const totalPublishedPages = computed(() => Math.ceil(props.acceptedNews.length / perPage.value))

const paginatedDraftNews = computed(() => {
    const start = (currentDraftPage.value - 1) * perPage.value
    const end = start + perPage.value
    return filteredDraftNews.value.slice(start, end)
})

const paginatedPublishedNews = computed(() => {
    const start = (currentPublishedPage.value - 1) * perPage.value
    const end = start + perPage.value
    return props.acceptedNews.slice(start, end)
})

const visibleDraftPages = computed(() => {
    const pages = []
    const total = totalDraftPages.value
    const current = currentDraftPage.value
    const delta = 2

    for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) {
        pages.push(i)
    }

    return pages
})

const visiblePublishedPages = computed(() => {
    const pages = []
    const total = totalPublishedPages.value
    const current = currentPublishedPage.value
    const delta = 2

    for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) {
        pages.push(i)
    }

    return pages
})

// Methods
const truncateText = (text, length) => {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getStatusBadgeClass = (status) => {
    const classes = {
        'Draft': 'bg-warning text-dark',
        'Pending': 'bg-info',
        'Reject': 'bg-danger',
        'Published': 'bg-success'
    }
    return classes[status] || 'bg-secondary'
}

const dismissAlert = () => {
    // Hide alert (you can implement this based on your needs)
}

// Lifecycle
onMounted(() => {
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    tooltipTriggerList.forEach(tooltipTriggerEl => {
        new bootstrap.Tooltip(tooltipTriggerEl)
    })
})
</script>

<style scoped>
.news-row {
    transition: all 0.3s ease;
}

.news-row:hover {
    background-color: rgba(59, 130, 246, 0.05);
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
    background: none;
}

.badge {
    font-size: 0.75em;
}

.page-link {
    border-radius: 0.375rem;
    margin: 0 2px;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>
