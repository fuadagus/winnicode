<template>
    <AppLayout>
        <!-- Search Header -->
        <section class="search-header py-5 bg-primary text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-5 fw-bold mb-3">Hasil Pencarian</h1>
                        <p class="fs-5 mb-0">
                            Menampilkan hasil untuk: <span class="fw-bold">"{{ query }}"</span>
                        </p>
                        <p class="small mt-2 opacity-75">
                            Ditemukan {{ news.total }} artikel
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <!-- Search Form -->
                        <form @submit.prevent="searchAgain" class="mt-3">
                            <div class="input-group">
                                <input v-model="newQuery" 
                                       type="text" 
                                       class="form-control" 
                                       placeholder="Cari lagi...">
                                <button class="btn btn-light" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Search Results -->
        <section class="search-results py-5">
            <div class="container">
                <div v-if="news.data.length > 0" class="row g-4">
                    <div v-for="article in news.data" :key="article.id" class="col-lg-4 col-md-6">
                        <div class="card modern-card h-100 shadow-sm border-0 overflow-hidden hover-card">
                            <div class="position-relative">
                                <img :src="article.image ? `/storage/images/${article.image}` : '/img/noimg.jpg'"
                                     :alt="article.title"
                                     class="card-img-top"
                                     style="height: 200px; object-fit: cover;" />
                                
                                <!-- Category Badge -->
                                <div class="position-absolute" style="top: 15px; left: 15px; z-index: 10;">
                                    <span class="badge bg-primary px-3 py-2 rounded-pill">
                                        {{ article.category?.name || 'Berita' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body p-4 d-flex flex-column">
                                <a :href="`/news/${article.id}/show`" 
                                   target="_blank"
                                   class="text-decoration-none">
                                    <h5 class="card-title h6 mb-3 text-dark fw-semibold lh-base">
                                        {{ highlightText(article.title) }}
                                    </h5>
                                </a>
                                
                                <p class="card-text text-muted small mb-3 flex-grow-1">
                                    {{ truncateText(extractExcerpt(article.content), 100) }}
                                </p>
                                
                                <div class="card-meta mt-auto">
                                    <div class="d-flex justify-content-between align-items-center text-muted small mb-2">
                                        <span><i class="fa fa-user me-1"></i>{{ article.author?.name || 'Admin' }}</span>
                                        <span><i class="fa fa-eye me-1"></i>{{ article.views }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                        <span><i class="fa fa-clock me-1"></i>{{ formatDate(article.created_at) }}</span>
                                        <span><i class="fa fa-heart me-1"></i>{{ article.likes_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Results -->
                <div v-else class="text-center py-5">
                    <div class="mb-4">
                        <i class="fa fa-search fa-3x text-muted mb-3"></i>
                        <h3 class="h4 text-muted">Tidak ada hasil ditemukan</h3>
                        <p class="text-muted">Coba gunakan kata kunci yang berbeda atau lebih umum</p>
                    </div>
                    
                    <!-- Search Suggestions -->
                    <div class="search-suggestions">
                        <h6 class="mb-3">Saran pencarian:</h6>
                        <div class="d-flex justify-content-center flex-wrap gap-2">
                            <span v-for="suggestion in searchSuggestions" 
                                  :key="suggestion"
                                  @click="searchWith(suggestion)"
                                  class="badge bg-light text-dark px-3 py-2 cursor-pointer"
                                  style="cursor: pointer;">
                                {{ suggestion }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="news.last_page > 1" class="row mt-5">
                    <div class="col-12">
                        <nav aria-label="Search pagination">
                            <ul class="pagination justify-content-center">
                                <li class="page-item" :class="{ disabled: news.current_page <= 1 }">
                                    <a class="page-link" 
                                       href="#" 
                                       @click.prevent="goToPage(news.current_page - 1)">
                                        <i class="fa fa-chevron-left me-1"></i>Previous
                                    </a>
                                </li>
                                
                                <li v-for="page in visiblePages" 
                                    :key="page" 
                                    class="page-item"
                                    :class="{ active: page === news.current_page }">
                                    <a class="page-link" 
                                       href="#" 
                                       @click.prevent="goToPage(page)">
                                        {{ page }}
                                    </a>
                                </li>
                                
                                <li class="page-item" :class="{ disabled: news.current_page >= news.last_page }">
                                    <a class="page-link" 
                                       href="#" 
                                       @click.prevent="goToPage(news.current_page + 1)">
                                        Next<i class="fa fa-chevron-right ms-1"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trending News Section -->
        <section class="trending-section py-5 bg-light">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title h1 fw-bold mb-3">Berita Trending</h2>
                    <p class="section-subtitle text-muted">Artikel paling populer saat ini</p>
                </div>
                
                <div class="row g-4">
                    <div v-for="(article, index) in trendingNews" :key="article.id" class="col-lg-3 col-md-6">
                        <div class="trending-card card h-100 border-0 shadow-sm">
                            <div class="position-relative">
                                <div class="trending-number position-absolute top-0 start-0 m-3" style="z-index: 10;">
                                    <span class="badge rounded-pill" 
                                          :class="index === 0 ? 'bg-danger' : index === 1 ? 'bg-warning' : index === 2 ? 'bg-info' : 'bg-secondary'">
                                        {{ index + 1 }}
                                    </span>
                                </div>
                                <img :src="article.image ? `/storage/images/${article.image}` : '/img/noimg.jpg'"
                                     :alt="article.title"
                                     class="card-img-top"
                                     style="height: 150px; object-fit: cover;" />
                            </div>
                            
                            <div class="card-body p-3">
                                <a :href="`/news/${article.id}/show`" 
                                   target="_blank"
                                   class="text-decoration-none">
                                    <h6 class="card-title mb-2 text-dark lh-base">
                                        {{ truncateText(article.title, 60) }}
                                    </h6>
                                </a>
                                <div class="d-flex justify-content-between align-items-center text-muted small">
                                    <span><i class="fa fa-eye me-1"></i>{{ article.views }}</span>
                                    <span><i class="fa fa-heart me-1"></i>{{ article.likes_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    news: Object,
    query: String
})

// Reactive data
const newQuery = ref(props.query)
const searchSuggestions = ref(['politik', 'ekonomi', 'olahraga', 'teknologi', 'kesehatan', 'pendidikan'])
const trendingNews = ref([])

// Computed
const visiblePages = computed(() => {
    const current = props.news.current_page
    const last = props.news.last_page
    const delta = 2
    const range = []
    
    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
        range.push(i)
    }
    
    if (current - delta > 2) {
        range.unshift('...')
    }
    if (current + delta < last - 1) {
        range.push('...')
    }
    
    range.unshift(1)
    if (last !== 1) {
        range.push(last)
    }
    
    return range.filter((v, i, a) => a.indexOf(v) === i)
})

// Methods
const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
    })
}

const truncateText = (text, length) => {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}

const extractExcerpt = (content) => {
    if (!content) return ''
    const strippedContent = content.replace(/<[^>]*>/g, '').trim()
    return strippedContent.length > 150 ? strippedContent.substring(0, 150) + '...' : strippedContent
}

const highlightText = (text) => {
    if (!text || !props.query) return text
    const regex = new RegExp(`(${props.query})`, 'gi')
    return text.replace(regex, '<mark>$1</mark>')
}

const searchAgain = () => {
    if (newQuery.value.trim()) {
        router.get('/search', { q: newQuery.value })
    }
}

const searchWith = (suggestion) => {
    newQuery.value = suggestion
    searchAgain()
}

const goToPage = (page) => {
    if (page >= 1 && page <= props.news.last_page) {
        router.get('/search', { q: props.query, page })
    }
}

// Get trending news on mount
onMounted(async () => {
    try {
        const response = await fetch('/api/trending-news')
        if (response.ok) {
            trendingNews.value = await response.json()
        }
    } catch (error) {
        console.error('Error fetching trending news:', error)
        // Fallback trending news
        trendingNews.value = []
    }
})
</script>

<style scoped>
/* Modern Design Variables */
:root {
    --primary-color: #3b82f6;
    --secondary-color: #1e40af;
    --accent-color: #f59e0b;
    --text-dark: #1f2937;
    --text-light: #6b7280;
    --bg-light: #f8fafc;
}

/* Search Header */
.search-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    position: relative;
    overflow: hidden;
}

.search-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('/img/pattern.png') repeat;
    opacity: 0.1;
    z-index: 1;
}

.search-header .container {
    position: relative;
    z-index: 2;
}

/* Card Styles */
.modern-card {
    border-radius: 1rem;
    transition: all 0.3s ease;
    border: none;
    overflow: hidden;
}

.modern-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.hover-card {
    cursor: pointer;
}

/* Trending Cards */
.trending-card {
    transition: all 0.3s ease;
    border-radius: 1rem;
}

.trending-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.trending-number .badge {
    font-size: 0.9rem;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Search Suggestions */
.search-suggestions .badge {
    transition: all 0.3s ease;
}

.search-suggestions .badge:hover {
    background-color: var(--primary-color) !important;
    color: white !important;
    transform: translateY(-1px);
}

/* Pagination */
.pagination .page-link {
    border: none;
    color: var(--primary-color);
    padding: 0.75rem 1rem;
    margin: 0 0.25rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    color: white;
    box-shadow: 0 3px 10px rgba(59, 130, 246, 0.3);
}

.pagination .page-link:hover {
    background-color: var(--primary-color);
    color: white;
    transform: translateY(-1px);
}

/* Section Title */
.section-title {
    background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Highlight */
mark {
    background: linear-gradient(45deg, #fef3c7, #fbbf24);
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-weight: 600;
}

/* No Results */
.fa-search.fa-3x {
    opacity: 0.3;
}

/* Responsive Design */
@media (max-width: 768px) {
    .search-header {
        text-align: center;
    }
    
    .search-header .input-group {
        max-width: 100%;
        margin-top: 1rem;
    }
    
    .trending-card {
        margin-bottom: 1rem;
    }
}

/* Custom animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modern-card {
    animation: fadeInUp 0.6s ease forwards;
}

.modern-card:nth-child(2) {
    animation-delay: 0.1s;
}

.modern-card:nth-child(3) {
    animation-delay: 0.2s;
}

.modern-card:nth-child(4) {
    animation-delay: 0.3s;
}
</style>
