<template>
    <AppLayout>
        <div class="container-fluid">
            <!-- Gallery Header -->
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h1 class="gallery-title">Our Gallery</h1>
                    <p class="gallery-subtitle">Koleksi foto dari berbagai berita menarik</p>
                    <button @click="refreshGallery" class="btn btn-primary">
                        <i class="fa fa-refresh me-2"></i>
                        Muat Foto Baru
                    </button>
                </div>
            </div>

            <!-- Gallery Grid -->
            <div class="row g-3" v-if="galleryItems.length > 0">
                <div v-for="(item, index) in galleryItems" 
                     :key="item.id" 
                     :class="getColumnClass(index)"
                     class="gallery-item">
                    <div class="gallery-card" @click="openLightbox(index)">
                        <div class="image-container">
                            <img :src="`/storage/images/${item.image}`" 
                                 :alt="item.title"
                                 class="gallery-image"
                                 @error="handleImageError">
                            <div class="image-overlay">
                                <div class="overlay-content">
                                    <h6 class="image-title">{{ truncateTitle(item.title) }}</h6>
                                    <p class="image-date">{{ item.date }}</p>
                                    <i class="fa fa-search-plus overlay-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-else-if="isLoading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Memuat galeri foto...</p>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-5">
                <i class="fa fa-images fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada foto tersedia</h5>
                <p class="text-muted">Foto akan muncul setelah ada berita dengan gambar</p>
            </div>

            <!-- Load More Button -->
            <div class="text-center mt-4" v-if="galleryItems.length > 0">
                <button @click="loadMoreImages" class="btn btn-outline-primary" :disabled="isLoading">
                    <i class="fa fa-plus me-2" v-if="!isLoading"></i>
                    <i class="fa fa-spinner fa-spin me-2" v-else></i>
                    {{ isLoading ? 'Memuat...' : 'Muat Lebih Banyak' }}
                </button>
            </div>
        </div>

        <!-- Lightbox Modal -->
        <div v-if="showLightbox" class="lightbox-overlay" @click="closeLightbox">
            <div class="lightbox-container">
                <button class="lightbox-close" @click="closeLightbox">
                    <i class="fa fa-times"></i>
                </button>
                
                <div class="lightbox-content">
                    <img v-if="currentImageIndex >= 0" 
                         :src="`/storage/images/${galleryItems[currentImageIndex].image}`"
                         :alt="galleryItems[currentImageIndex].title"
                         class="lightbox-image">
                    
                    <div class="lightbox-info">
                        <h5 class="lightbox-title">{{ galleryItems[currentImageIndex]?.title }}</h5>
                        <p class="lightbox-date">{{ galleryItems[currentImageIndex]?.date }}</p>
                        <a :href="galleryItems[currentImageIndex]?.url" 
                           class="btn btn-primary btn-sm">
                            <i class="fa fa-external-link-alt me-2"></i>
                            Baca Berita
                        </a>
                    </div>
                </div>
                
                <button v-if="currentImageIndex > 0" 
                        class="lightbox-nav lightbox-prev" 
                        @click.stop="previousImage">
                    <i class="fa fa-chevron-left"></i>
                </button>
                
                <button v-if="currentImageIndex < galleryItems.length - 1" 
                        class="lightbox-nav lightbox-next" 
                        @click.stop="nextImage">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

// Props
const props = defineProps({
    galleryItems: Array
})

// Reactive data
const galleryItems = ref(props.galleryItems || [])
const isLoading = ref(false)
const showLightbox = ref(false)
const currentImageIndex = ref(-1)

// Methods
const getColumnClass = (index) => {
    // Create masonry-like layout
    const patterns = [
        'col-lg-4 col-md-6 col-sm-12',
        'col-lg-3 col-md-6 col-sm-12', 
        'col-lg-5 col-md-6 col-sm-12',
        'col-lg-3 col-md-6 col-sm-12',
        'col-lg-6 col-md-6 col-sm-12',
        'col-lg-3 col-md-6 col-sm-12'
    ]
    return patterns[index % patterns.length]
}

const truncateTitle = (title) => {
    return title.length > 60 ? title.substring(0, 60) + '...' : title
}

const handleImageError = (event) => {
    event.target.src = '/img/noimg.jpg'
}

const refreshGallery = async () => {
    isLoading.value = true
    try {
        const response = await axios.get('/api/gallery/random?limit=12')
        galleryItems.value = response.data
    } catch (error) {
        console.error('Error refreshing gallery:', error)
    } finally {
        isLoading.value = false
    }
}

const loadMoreImages = async () => {
    isLoading.value = true
    try {
        const response = await axios.get('/api/gallery/random?limit=6')
        galleryItems.value.push(...response.data)
    } catch (error) {
        console.error('Error loading more images:', error)
    } finally {
        isLoading.value = false
    }
}

const openLightbox = (index) => {
    currentImageIndex.value = index
    showLightbox.value = true
    document.body.style.overflow = 'hidden'
}

const closeLightbox = () => {
    showLightbox.value = false
    currentImageIndex.value = -1
    document.body.style.overflow = 'auto'
}

const nextImage = () => {
    if (currentImageIndex.value < galleryItems.value.length - 1) {
        currentImageIndex.value++
    }
}

const previousImage = () => {
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--
    }
}

// Keyboard navigation
const handleKeydown = (event) => {
    if (!showLightbox.value) return
    
    switch (event.key) {
        case 'Escape':
            closeLightbox()
            break
        case 'ArrowRight':
            nextImage()
            break
        case 'ArrowLeft':
            previousImage()
            break
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
.gallery-title {
    font-size: 3rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.gallery-subtitle {
    font-size: 1.2rem;
    color: #718096;
    margin-bottom: 2rem;
}

.gallery-item {
    margin-bottom: 1rem;
}

.gallery-card {
    cursor: pointer;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.gallery-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.image-container {
    position: relative;
    width: 100%;
    height: 250px;
    overflow: hidden;
}

.gallery-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-card:hover .gallery-image {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
    display: flex;
    align-items: flex-end;
    padding: 1.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-card:hover .image-overlay {
    opacity: 1;
}

.overlay-content {
    color: white;
    width: 100%;
}

.image-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    line-height: 1.3;
}

.image-date {
    font-size: 0.875rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.overlay-icon {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.5rem;
    opacity: 0.8;
}

/* Lightbox Styles */
.lightbox-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.9);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.lightbox-container {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.lightbox-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    cursor: pointer;
    transition: background 0.3s ease;
}

.lightbox-close:hover {
    background: rgba(0, 0, 0, 0.7);
}

.lightbox-content {
    display: flex;
    flex-direction: column;
    max-height: 90vh;
}

.lightbox-image {
    max-width: 100%;
    max-height: 70vh;
    object-fit: contain;
}

.lightbox-info {
    padding: 1.5rem;
    background: white;
}

.lightbox-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2d3748;
}

.lightbox-date {
    color: #718096;
    margin-bottom: 1rem;
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.3s ease;
    font-size: 1.25rem;
}

.lightbox-nav:hover {
    background: rgba(0, 0, 0, 0.7);
}

.lightbox-prev {
    left: 1rem;
}

.lightbox-next {
    right: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .gallery-title {
        font-size: 2rem;
    }
    
    .image-container {
        height: 200px;
    }
    
    .lightbox-container {
        margin: 1rem;
        max-width: calc(100vw - 2rem);
    }
    
    .lightbox-nav {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .gallery-title {
        font-size: 1.75rem;
    }
    
    .image-container {
        height: 180px;
    }
}
</style>
