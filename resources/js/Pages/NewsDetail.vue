<template>
    <AppLayout>
        <!-- Breadcrumb -->
        <section class="breadcrumb-section py-3 bg-light">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="/" class="text-decoration-none text-primary">
                                <i class="fa fa-home me-1"></i>Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#" class="text-decoration-none text-primary">{{ news.category?.name || 'Berita' }}</a>
                        </li>
                        <li class="breadcrumb-item active text-muted" aria-current="page">
                            {{ truncateText(news.title, 50) }}
                        </li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Article Header -->
        <section class="article-header py-5" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto text-center">
                        <div class="article-meta mb-4">
                            <span class="badge bg-danger me-3 px-3 py-2 rounded-pill text-white">
                                {{ news.category?.name || 'Breaking News' }}
                            </span>
                            <span class="text-white me-3">
                                <i class="fa fa-clock me-1"></i>{{ formatDateTime(news.created_at) }}
                            </span>
                            <span class="text-white">
                                <i class="fa fa-user me-1"></i>{{ news.author?.name || 'Admin' }}
                            </span>
                        </div>
                        
                        <h1 class="article-title display-4 fw-bold mb-4 text-white lh-base">
                            {{ news.title }}
                        </h1>
                        
                        <p class="article-excerpt fs-5 text-white opacity-90 mb-4 mx-auto" style="max-width: 700px;">
                            {{ extractExcerpt(news.content) }}
                        </p>
                        
                        <!-- Social Share -->
                        <div class="social-share d-flex justify-content-center gap-2 mb-4">
                            <button @click="shareToFacebook" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                <i class="fab fa-facebook-f me-1"></i>Share
                            </button>
                            <button @click="shareToTwitter" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                <i class="fab fa-twitter me-1"></i>Tweet
                            </button>
                            <button @click="shareToWhatsApp" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </button>
                            <button @click="copyLink" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                <i class="fa fa-link me-1"></i>Copy
                            </button>
                        </div>
                        
                        <!-- Article Stats -->
                        <div class="article-stats d-flex justify-content-center gap-4 text-white opacity-75">
                            <span><i class="fa fa-eye me-1"></i>{{ news.views }} views</span>
                            <span><i class="fa fa-heart me-1"></i>{{ news.likes_count || 0 }} likes</span>
                            <span><i class="fa fa-map-marker me-1"></i>{{ news.location || 'Indonesia' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Article Content -->
        <section class="article-content py-5 bg-white">
            <div class="container">
                <div class="row">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <article class="main-article">
                            <!-- Featured Image -->
                            <div class="featured-image mb-5">
                                <img :src="news.image ? `/storage/images/${news.image}` : '/img/noimg.jpg'"
                                     :alt="news.title"
                                     class="img-fluid w-100 rounded shadow-lg"
                                     style="max-height: 500px; object-fit: cover;" />
                            </div>

                            <!-- Article Meta Info -->
                            <div class="article-meta-info mb-4 p-3 bg-light rounded">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="author-info d-flex align-items-center">
                                            <div class="author-avatar me-3">
                                                <img v-if="news.author?.image" 
                                                     :src="`/storage/images/${news.author.image}`" 
                                                     :alt="news.author?.name || 'Author'"
                                                     class="rounded-circle" 
                                                     style="width: 40px; height: 40px; object-fit: cover;" />
                                                <div v-else 
                                                     class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px; font-size: 0.9rem;">
                                                    {{ (news.author?.name || 'A').charAt(0) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="author-name fw-semibold text-dark">{{ news.author?.name || 'Admin' }}</div>
                                                <div class="text-muted small">{{ news.author?.role || 'News Writer' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <div class="article-actions">
                                            <button @click="toggleLike" 
                                                    class="btn btn-outline-danger btn-sm me-2"
                                                    :disabled="likingInProgress">
                                                <i :class="['fa', isLiked ? 'fas fa-heart' : 'far fa-heart']" class="me-1"></i>
                                                <span v-if="likingInProgress">...</span>
                                                <span v-else>{{ news.likes_count || 0 }}</span>
                                            </button>
                                            <span class="text-muted small">
                                                <i class="fa fa-eye me-1"></i>{{ news.views }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Article Body -->
                            <div class="article-body">
                                <div class="content-wrapper fs-6 lh-lg" v-html="formatContent(news.content)"></div>
                            </div>

                            <!-- Article Tags -->
                            <div class="article-tags mt-5 pt-4 border-top">
                                <h6 class="mb-3">Tags:</h6>
                                <div class="tags-list">
                                    <span class="badge bg-primary me-2 mb-2 px-3 py-2 rounded-pill">
                                        {{ news.category?.name || 'Berita' }}
                                    </span>
                                    <span v-if="news.location" class="badge bg-secondary me-2 mb-2 px-3 py-2 rounded-pill">
                                        {{ news.location }}
                                    </span>
                                    <span class="badge bg-info me-2 mb-2 px-3 py-2 rounded-pill text-white">
                                        {{ formatDate(news.created_at) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Author Bio -->
                            <div class="author-bio mt-5 p-4 bg-light rounded-3">
                                <h5 class="mb-3">Tentang Penulis</h5>
                                <div class="row align-items-center">
                                    <div class="col-md-3 text-center">
                                        <div class="author-avatar-large mb-3">
                                            <img v-if="news.author?.image" 
                                                 :src="`/storage/images/${news.author.image}`" 
                                                 :alt="news.author?.name || 'Author'"
                                                 class="rounded-circle img-fluid" 
                                                 style="width: 80px; height: 80px; object-fit: cover;" />
                                            <div v-else 
                                                 class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                 style="width: 80px; height: 80px; font-size: 1.5rem;">
                                                {{ (news.author?.name || 'A').charAt(0) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <h4 class="mb-2">{{ news.author?.name || 'Admin' }}</h4>
                                        <p class="text-muted mb-2">{{ news.author?.role || 'News Writer' }}</p>
                                        <p class="mb-3">{{ news.author?.bio || 'Penulis berpengalaman dalam jurnalistik dan media digital.' }}</p>
                                        <div class="social-links">
                                            <a v-if="news.author?.twitter" :href="news.author.twitter" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                            <a v-if="news.author?.linkedin" :href="news.author.linkedin" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                                                <i class="fab fa-linkedin"></i>
                                            </a>
                                            <a v-if="news.author?.email" :href="`mailto:${news.author.email}`" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-envelope"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <div class="sidebar-content">
                            <!-- Search Widget -->
                            <div class="widget search-widget card shadow-sm border-0 mb-4">
                                <div class="card-body">
                                    <h5 class="widget-title mb-3">Cari Berita</h5>
                                    <form @submit.prevent="searchNews">
                                        <div class="input-group">
                                            <input v-model="searchQuery" 
                                                   type="text" 
                                                   class="form-control" 
                                                   placeholder="Kata kunci...">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Related Articles -->
                            <div class="widget related-widget card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white border-0">
                                    <h5 class="widget-title mb-0">Berita Terkait</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div v-for="(article, index) in relatedNews" :key="article.id" 
                                         class="related-item p-3"
                                         :class="{ 'border-bottom': index < relatedNews.length - 1 }">
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <img :src="article.image ? `/storage/images/${article.image}` : '/img/noimg.jpg'"
                                                     :alt="article.title"
                                                     class="img-fluid rounded"
                                                     style="height: 60px; object-fit: cover;" />
                                            </div>
                                            <div class="col-8">
                                                <a :href="`/news/${article.id}/show`" 
                                                   target="_blank"
                                                   class="text-decoration-none">
                                                    <h6 class="mb-1 lh-base text-dark">
                                                        {{ truncateText(article.title, 60) }}
                                                    </h6>
                                                </a>
                                                <small class="text-muted">
                                                    <i class="fa fa-clock me-1"></i>{{ formatDate(article.created_at) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Popular Articles -->
                            <div class="widget popular-widget card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white border-0">
                                    <h5 class="widget-title mb-0">Berita Populer</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div v-for="(article, index) in popularNews" :key="article.id" 
                                         class="popular-item p-3"
                                         :class="{ 'border-bottom': index < popularNews.length - 1 }">
                                        <div class="d-flex">
                                            <div class="ranking-number me-3">
                                                <span class="badge rounded-pill" 
                                                      :class="index === 0 ? 'bg-danger' : index === 1 ? 'bg-warning' : index === 2 ? 'bg-info' : 'bg-secondary'">
                                                    {{ index + 1 }}
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <a :href="`/news/${article.id}/show`" 
                                                   target="_blank"
                                                   class="text-decoration-none">
                                                    <h6 class="mb-2 lh-base text-dark">
                                                        {{ truncateText(article.title, 70) }}
                                                    </h6>
                                                </a>
                                                <div class="d-flex gap-3 text-muted small">
                                                    <span><i class="fa fa-eye me-1"></i>{{ article.views }}</span>
                                                    <span><i class="fa fa-heart me-1"></i>{{ article.likes_count }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Newsletter Signup -->
                            <div class="widget newsletter-widget card shadow-sm border-0 bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="widget-title mb-3 text-white">Newsletter</h5>
                                    <p class="mb-3 opacity-75">Dapatkan update berita terbaru langsung di email Anda.</p>
                                    <form @submit.prevent="subscribeNewsletter">
                                        <div class="mb-3">
                                            <input v-model="newsletterEmail" 
                                                   type="email" 
                                                   class="form-control" 
                                                   placeholder="Email Anda..."
                                                   required>
                                        </div>
                                        <button type="submit" 
                                                class="btn btn-light w-100"
                                                :disabled="subscribing">
                                            <span v-if="subscribing">Berlangganan...</span>
                                            <span v-else>Berlangganan</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- More Articles Section -->
        <section class="more-articles py-5 bg-light">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2 class="section-title h1 fw-bold mb-3">Baca Juga</h2>
                    <p class="section-subtitle text-muted">Artikel menarik lainnya untuk Anda</p>
                </div>
                
                <div class="row g-4">
                    <div v-for="article in moreArticles" :key="article.id" class="col-lg-4 col-md-6">
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
                                        {{ truncateText(article.title, 80) }}
                                    </h5>
                                </a>
                                
                                <p class="card-text text-muted small mb-3 flex-grow-1">
                                    {{ truncateText(extractExcerpt(article.content), 100) }}
                                </p>
                                
                                <div class="card-meta mt-auto">
                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                        <span><i class="fa fa-clock me-1"></i>{{ formatDate(article.created_at) }}</span>
                                        <span><i class="fa fa-eye me-1"></i>{{ article.views }}</span>
                                    </div>
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
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Props
const props = defineProps({
    news: Object,
    relatedNews: Array,
    popularNews: Array,
    moreArticles: Array
})

// Reactive data
const searchQuery = ref('')
const newsletterEmail = ref('')
const subscribing = ref(false)
const likingInProgress = ref(false)
const isLiked = ref(false)

// Methods
const formatDateTime = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
    })
}

const formatTimeAgo = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    const now = new Date()
    const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))
    
    if (diffInHours < 1) return 'Baru saja'
    if (diffInHours < 24) return `${diffInHours} jam yang lalu`
    if (diffInHours < 48) return 'Kemarin'
    
    const diffInDays = Math.floor(diffInHours / 24)
    if (diffInDays <= 7) return `${diffInDays} hari yang lalu`
    
    return formatDate(dateString)
}

const formatViews = (views) => {
    if (!views) return '0'
    if (views < 1000) return views.toString()
    if (views < 1000000) return Math.floor(views / 1000) + 'rb'
    return Math.floor(views / 1000000) + 'jt'
}

const truncateText = (text, length) => {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}

const extractExcerpt = (content) => {
    if (!content) return ''
    // Remove HTML tags and get first 150 characters
    const strippedContent = content.replace(/<[^>]*>/g, '').trim()
    return strippedContent.length > 150 ? strippedContent.substring(0, 150) + '...' : strippedContent
}

const formatContent = (content) => {
    if (!content) return ''
    
    // Add some modern styling to content
    let formattedContent = content
    
    // Style paragraphs
    formattedContent = formattedContent.replace(/<p>/g, '<p class="mb-4 lh-lg fs-6">')
    
    // Style headings
    formattedContent = formattedContent.replace(/<h1>/g, '<h1 class="h2 fw-bold mb-4 mt-5">')
    formattedContent = formattedContent.replace(/<h2>/g, '<h2 class="h3 fw-bold mb-3 mt-4">')
    formattedContent = formattedContent.replace(/<h3>/g, '<h3 class="h4 fw-semibold mb-3 mt-4">')
    
    // Style lists
    formattedContent = formattedContent.replace(/<ul>/g, '<ul class="list-unstyled ps-3">')
    formattedContent = formattedContent.replace(/<li>/g, '<li class="mb-2 position-relative"><i class="fa fa-check-circle text-primary me-2"></i>')
    
    // Style blockquotes
    formattedContent = formattedContent.replace(/<blockquote>/g, '<blockquote class="blockquote border-start border-primary border-4 ps-4 py-3 my-4 bg-light rounded-end">')
    
    return formattedContent
}

const toggleLike = async () => {
    if (likingInProgress.value) return
    
    likingInProgress.value = true
    
    try {
        await router.post(`/news/${props.news.id}/like`, {}, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (response) => {
                // Update like status and count
                isLiked.value = !isLiked.value
                if (isLiked.value) {
                    props.news.likes_count++
                } else {
                    props.news.likes_count--
                }
            },
            onError: (errors) => {
                console.error('Error liking news:', errors)
            }
        })
    } catch (error) {
        console.error('Error:', error)
    } finally {
        likingInProgress.value = false
    }
}

const shareToFacebook = () => {
    const url = encodeURIComponent(window.location.href)
    const text = encodeURIComponent(props.news.title)
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${text}`, '_blank', 'width=600,height=400')
}

const shareToTwitter = () => {
    const url = encodeURIComponent(window.location.href)
    const text = encodeURIComponent(props.news.title)
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400')
}

const shareToWhatsApp = () => {
    const url = encodeURIComponent(window.location.href)
    const text = encodeURIComponent(`${props.news.title} - ${window.location.href}`)
    window.open(`https://wa.me/?text=${text}`, '_blank')
}

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(window.location.href)
        alert('Link berhasil disalin!')
    } catch (error) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea')
        textArea.value = window.location.href
        document.body.appendChild(textArea)
        textArea.select()
        document.execCommand('copy')
        document.body.removeChild(textArea)
        alert('Link berhasil disalin!')
    }
}

const searchNews = () => {
    if (searchQuery.value.trim()) {
        router.get('/search', { q: searchQuery.value })
    }
}

const subscribeNewsletter = async () => {
    if (!newsletterEmail.value) return
    
    subscribing.value = true
    
    try {
        await router.post('/newsletter/subscribe', {
            email: newsletterEmail.value
        }, {
            preserveState: true,
            onSuccess: () => {
                newsletterEmail.value = ''
                alert('Terima kasih! Anda berhasil berlangganan newsletter.')
            },
            onError: (errors) => {
                console.error('Newsletter subscription error:', errors)
                alert('Terjadi kesalahan. Silakan coba lagi.')
            }
        })
    } catch (error) {
        console.error('Error:', error)
        alert('Terjadi kesalahan. Silakan coba lagi.')
    } finally {
        subscribing.value = false
    }
}

// Check if user has liked this news
onMounted(() => {
    // Check local storage or session for like status
    const deviceId = sessionStorage.getItem('device_id') || localStorage.getItem('device_id')
    if (deviceId && props.news.likes) {
        isLiked.value = props.news.likes.some(like => like.device_id === deviceId)
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
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Global Styles */
.article-header {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    position: relative;
    overflow: hidden;
}

.article-header::before {
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

.article-header .container {
    position: relative;
    z-index: 2;
}

.article-title {
    font-family: 'Georgia', serif;
    line-height: 1.2;
    margin-bottom: 1.5rem;
}

.author-info .author-avatar img,
.author-info .author-avatar > div {
    transition: transform 0.3s ease;
}

.author-info:hover .author-avatar img,
.author-info:hover .author-avatar > div {
    transform: scale(1.1);
}

/* Content Styling */
.article-content {
    font-family: 'Inter', sans-serif;
}

.featured-image .image-container {
    transition: transform 0.3s ease;
    border-radius: 1rem;
    overflow: hidden;
}

.featured-image:hover .image-container {
    transform: translateY(-5px);
}

.content-wrapper {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text-dark);
}

.content-wrapper h1,
.content-wrapper h2,
.content-wrapper h3 {
    color: var(--text-dark);
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.content-wrapper p {
    margin-bottom: 1.5rem;
    text-align: justify;
}

.content-wrapper img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    margin: 1.5rem auto;
    display: block;
    box-shadow: var(--shadow-md);
}

.content-wrapper blockquote {
    background: linear-gradient(45deg, #f8fafc, #e2e8f0);
    border-left: 4px solid var(--primary-color);
    margin: 2rem 0;
    padding: 1.5rem;
    font-style: italic;
    border-radius: 0.5rem;
}

/* Widget Styles */
.widget {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 1rem;
}

.widget:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.widget-title {
    position: relative;
    padding-bottom: 0.5rem;
}

.widget-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 3rem;
    height: 3px;
    background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
    border-radius: 2px;
}

.search-widget .input-group .form-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: none;
}

.search-widget .input-group .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Related & Popular Articles */
.related-item,
.popular-item {
    transition: background-color 0.3s ease;
    border-radius: 0.5rem;
    margin: 0.25rem;
}

.related-item:hover,
.popular-item:hover {
    background-color: var(--bg-light) !important;
}

.ranking-number .badge {
    font-size: 0.9rem;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Newsletter Widget */
.newsletter-widget {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border-radius: 1rem;
}

.newsletter-widget .form-control {
    border: 2px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-radius: 0.5rem;
}

.newsletter-widget .form-control::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.newsletter-widget .form-control:focus {
    border-color: rgba(255, 255, 255, 0.5);
    background: rgba(255, 255, 255, 0.2);
    box-shadow: none;
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

/* Share Buttons */
.share-buttons .btn {
    transition: all 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.share-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Tags */
.tags-list .badge {
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.tags-list .badge:hover {
    background-color: var(--primary-color) !important;
    color: white !important;
    transform: translateY(-1px);
}

/* Like Button */
.like-section .btn {
    transition: all 0.3s ease;
    font-weight: 600;
}

.like-section .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

/* Author Bio */
.author-bio {
    background: linear-gradient(45deg, #f8fafc, #e2e8f0);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.author-bio:hover {
    box-shadow: var(--shadow-md);
}

.author-avatar-large img,
.author-avatar-large > div {
    transition: transform 0.3s ease;
}

.author-bio:hover .author-avatar-large img,
.author-bio:hover .author-avatar-large > div {
    transform: scale(1.05);
}

/* Section Headers */
.section-title {
    background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Responsive Design */
@media (max-width: 768px) {
    .article-header {
        padding: 3rem 0;
    }
    
    .article-title {
        font-size: 2rem;
    }
    
    .content-wrapper {
        font-size: 1rem;
    }
    
    .share-buttons {
        justify-content: center;
        margin-top: 1rem;
    }
    
    .author-info {
        text-align: center;
        flex-direction: column;
    }
    
    .author-info .author-avatar {
        margin-bottom: 1rem;
    }
}

/* Loading Animations */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.loading {
    animation: pulse 2s infinite;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--secondary-color);
}

/* Breadcrumb Styling */
.breadcrumb-section {
    background: linear-gradient(45deg, #f8fafc, #e2e8f0);
    border-bottom: 1px solid #e5e7eb;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: var(--primary-color);
    font-weight: bold;
}

/* Image Caption */
.image-caption {
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
}

/* Article Meta */
.article-meta .badge {
    font-weight: 600;
    font-size: 0.9rem;
}

/* More Articles Section */
.more-articles {
    background: linear-gradient(45deg, #f8fafc, #ffffff);
}

/* Social Links */
.social-links .btn {
    transition: all 0.3s ease;
    border-radius: 50%;
    width: 2.5rem;
    height: 2.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.social-links .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}
</style>
