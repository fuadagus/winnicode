<template>
    <AdminLayout :user="$page.props.auth?.user" :notifications="notifications">
        <div class="edit-news">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fa fa-edit me-2"></i>
                            Edit Berita
                        </h1>
                        <p class="page-subtitle text-muted">
                            Edit dan perbarui berita Anda
                        </p>
                    </div>
                    <div class="header-actions">
                        <Link href="/admin/news" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i>
                            Kembali
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <form @submit.prevent="submitForm" class="news-form">
                <div class="row">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <!-- Basic Information -->
                        <div class="form-section">
                            <div class="section-header">
                                <h5 class="section-title">Informasi Dasar</h5>
                                <p class="section-subtitle">Edit detail utama berita Anda</p>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <!-- Title -->
                                    <div class="mb-3">
                                        <label class="form-label required">Judul Berita</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': errors.title }"
                                            v-model="form.title"
                                            placeholder="Masukkan judul berita..."
                                            maxlength="255">
                                        <div class="form-text">
                                            {{ form.title.length }}/255 karakter
                                        </div>
                                        <div v-if="errors.title" class="invalid-feedback">
                                            {{ errors.title }}
                                        </div>
                                    </div>

                                    <!-- Slug -->
                                    <div class="mb-3">
                                        <label class="form-label">Slug URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ baseUrl }}/news/</span>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                :class="{ 'is-invalid': errors.slug }"
                                                v-model="form.slug"
                                                placeholder="url-berita">
                                        </div>
                                        <div class="form-text">
                                            URL akan otomatis dibuat dari judul jika dikosongkan
                                        </div>
                                        <div v-if="errors.slug" class="invalid-feedback">
                                            {{ errors.slug }}
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="mb-3">
                                        <label class="form-label required">Konten Berita</label>
                                        <div class="editor-wrapper">
                                            <div ref="editorElement" class="editor-container"></div>
                                        </div>
                                        <div v-if="errors.content" class="invalid-feedback d-block">
                                            {{ errors.content }}
                                        </div>
                                    </div>

                                    <!-- Excerpt -->
                                    <div class="mb-3">
                                        <label class="form-label">Ringkasan</label>
                                        <textarea 
                                            class="form-control" 
                                            :class="{ 'is-invalid': errors.excerpt }"
                                            v-model="form.excerpt"
                                            rows="3"
                                            placeholder="Ringkasan singkat berita (opsional)..."
                                            maxlength="500"></textarea>
                                        <div class="form-text">
                                            {{ (form.excerpt || '').length }}/500 karakter
                                        </div>
                                        <div v-if="errors.excerpt" class="invalid-feedback">
                                            {{ errors.excerpt }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="form-section">
                            <div class="section-header">
                                <h5 class="section-title">Pengaturan SEO</h5>
                                <p class="section-subtitle">Optimasi untuk mesin pencari</p>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <!-- Meta Title -->
                                    <div class="mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            v-model="form.meta_title"
                                            placeholder="Judul untuk SEO (60 karakter)..."
                                            maxlength="60">
                                        <div class="form-text">
                                            {{ (form.meta_title || '').length }}/60 karakter
                                        </div>
                                    </div>

                                    <!-- Meta Description -->
                                    <div class="mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea 
                                            class="form-control" 
                                            v-model="form.meta_description"
                                            rows="2"
                                            placeholder="Deskripsi untuk SEO (160 karakter)..."
                                            maxlength="160"></textarea>
                                        <div class="form-text">
                                            {{ (form.meta_description || '').length }}/160 karakter
                                        </div>
                                    </div>

                                    <!-- Keywords -->
                                    <div class="mb-3">
                                        <label class="form-label">Keywords</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            v-model="form.keywords"
                                            placeholder="keyword1, keyword2, keyword3...">
                                        <div class="form-text">
                                            Pisahkan dengan koma
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Publish Settings -->
                        <div class="form-section">
                            <div class="card sticky-card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fa fa-cog me-2"></i>
                                        Pengaturan Publikasi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Status -->
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" v-model="form.status">
                                            <option value="draft">Draft</option>
                                            <option value="pending">Menunggu Review</option>
                                            <option value="published" v-if="canPublish">Publish</option>
                                        </select>
                                        <small class="form-text text-muted mt-1">
                                            Status: {{ getStatusText(form.status) }}
                                        </small>
                                    </div>

                                    <!-- Category -->
                                    <div class="mb-3">
                                        <label class="form-label required">Kategori</label>
                                        <select 
                                            class="form-select" 
                                            :class="{ 'is-invalid': errors.category_id }"
                                            v-model="form.category_id">
                                            <option value="">Pilih Kategori</option>
                                            <option v-for="category in categories" 
                                                    :key="category.id" 
                                                    :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.category_id" class="invalid-feedback">
                                            {{ errors.category_id }}
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    <div class="mb-3">
                                        <label class="form-label">Lokasi</label>
                                        
                                        <!-- Province -->
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <select 
                                                    class="form-select" 
                                                    v-model="form.province"
                                                    @change="onProvinceChange">
                                                    <option value="">Pilih Provinsi</option>
                                                    <option v-for="province in provinces" :key="province" :value="province">
                                                        {{ province }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select 
                                                    class="form-select" 
                                                    v-model="form.city"
                                                    @change="onCityChange"
                                                    :disabled="!form.province || loadingCities">
                                                    <option value="">{{ loadingCities ? 'Loading...' : 'Pilih Kota' }}</option>
                                                    <option v-for="city in cities" :key="city" :value="city">
                                                        {{ city }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Coordinates -->
                                        <div class="row mb-2">
                                            <div class="col-md-4">
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    v-model="form.latitude"
                                                    @input="onCoordinateChange"
                                                    placeholder="Latitude">
                                            </div>
                                            <div class="col-md-4">
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    v-model="form.longitude"
                                                    @input="onCoordinateChange"
                                                    placeholder="Longitude">
                                            </div>
                                            <div class="col-md-4">
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    v-model="form.country"
                                                    placeholder="Negara">
                                            </div>
                                        </div>
                                        
                                        <!-- Get Current Location Button -->
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-primary btn-sm"
                                            @click="getCurrentLocation"
                                            :disabled="gettingLocation">
                                            <i class="fa" :class="gettingLocation ? 'fa-spinner fa-spin' : 'fa-map-marker'"></i>
                                            {{ gettingLocation ? 'Mendapatkan Lokasi...' : 'Gunakan Lokasi Saat Ini' }}
                                        </button>
                                    </div>

                                    <!-- Publish Date -->
                                    <div class="mb-3" v-if="form.status === 'published'">
                                        <label class="form-label">Tanggal Publikasi</label>
                                        <input 
                                            type="datetime-local" 
                                            class="form-control" 
                                            v-model="form.published_at">
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-grid gap-2">
                                        <button type="submit" 
                                                class="btn btn-primary" 
                                                :disabled="processing">
                                            <i class="fa fa-save me-1"></i>
                                            <span v-if="processing">Menyimpan...</span>
                                            <span v-else>{{ getSubmitText }}</span>
                                        </button>
                                        
                                        <button type="button" 
                                                @click="saveDraft" 
                                                class="btn btn-outline-secondary"
                                                :disabled="processing">
                                            <i class="fa fa-bookmark me-1"></i>
                                            Simpan Draft
                                        </button>
                                        
                                        <Link :href="`/admin/news/${news.id}`" class="btn btn-outline-info">
                                            <i class="fa fa-eye me-1"></i>
                                            Lihat Berita
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="form-section">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fa fa-image me-2"></i>
                                        Gambar Utama
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Current Image -->
                                    <div v-if="currentImage && !imagePreview" class="current-image mb-3">
                                        <img :src="currentImage" alt="Current Image" class="img-fluid rounded">
                                        <div class="mt-2">
                                            <small class="text-muted">Gambar saat ini</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Image Upload -->
                                    <div class="image-upload-area" 
                                         @drop="handleDrop" 
                                         @dragover.prevent 
                                         @dragenter.prevent>
                                        <input type="file" 
                                               ref="fileInput" 
                                               @change="handleFileSelect" 
                                               accept="image/*" 
                                               class="d-none">
                                        
                                        <div v-if="!imagePreview" 
                                             class="upload-placeholder" 
                                             @click="$refs.fileInput.click()">
                                            <i class="fa fa-cloud-upload-alt upload-icon"></i>
                                            <h6>{{ currentImage ? 'Ganti Gambar' : 'Upload Gambar' }}</h6>
                                            <p class="text-muted small">
                                                Drag & drop atau klik untuk memilih<br>
                                                Max: 2MB, Format: JPG, PNG, WebP
                                            </p>
                                        </div>
                                        
                                        <div v-else class="image-preview">
                                            <img :src="imagePreview" alt="Preview" class="preview-image">
                                            <div class="image-overlay">
                                                <button type="button" 
                                                        @click="$refs.fileInput.click()" 
                                                        class="btn btn-sm btn-light me-2">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" 
                                                        @click="removeImage" 
                                                        class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="errors.image" class="invalid-feedback d-block">
                                        {{ errors.image }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="form-section">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fa fa-tags me-2"></i>
                                        Tags
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="tag-input">
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            v-model="currentTag"
                                            @keydown.enter.prevent="addTag"
                                            @keydown.comma.prevent="addTag"
                                            placeholder="Masukkan tag...">
                                        <div class="form-text">
                                            Tekan Enter atau koma untuk menambah tag
                                        </div>
                                    </div>
                                    
                                    <div v-if="form.tags.length > 0" class="tags-list mt-3">
                                        <span v-for="(tag, index) in form.tags" 
                                              :key="index" 
                                              class="tag-item">
                                            {{ tag }}
                                            <button type="button" 
                                                    @click="removeTag(index)" 
                                                    class="tag-remove">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
const props = defineProps({
    news: Object,
    categories: Array,
    canPublish: Boolean,
    notifications: {
        type: Array,
        default: () => []
    },
    errors: {
        type: Object,
        default: () => ({})
    }
})

// Refs
const editorElement = ref(null)
const fileInput = ref(null)
const editor = ref(null)

// Reactive data
const processing = ref(false)
const imagePreview = ref(null)
const currentTag = ref('')
const baseUrl = ref(window.location.origin)

// Location state
const provinces = ref([])
const cities = ref([])
const loadingCities = ref(false)
const gettingLocation = ref(false)
let coordinateTimeout = null

const form = reactive({
    title: props.news.title || '',
    slug: props.news.slug || '',
    content: props.news.content || '',
    excerpt: props.news.excerpt || '',
    meta_title: props.news.meta_title || '',
    meta_description: props.news.meta_description || '',
    keywords: props.news.keywords || '',
    status: props.news.status || 'draft',
    category_id: props.news.category_id || '',
    province: props.news.province || '',
    city: props.news.city || '',
    country: props.news.country || 'Indonesia',
    latitude: props.news.latitude || '',
    longitude: props.news.longitude || '',
    published_at: props.news.published_at ? 
        new Date(props.news.published_at).toISOString().slice(0, 16) : '',
    image: null,
    tags: props.news.tags ? JSON.parse(props.news.tags) : []
})

// Computed
const currentImage = computed(() => {
    return props.news.image ? `/storage/images/${props.news.image}` : null
})

const getSubmitText = computed(() => {
    switch (form.status) {
        case 'published':
            return 'Publikasikan'
        case 'pending':
            return 'Kirim untuk Review'
        default:
            return 'Simpan Draft'
    }
})

// Methods
const initializeEditor = async () => {
    try {
        // Import CKEditor (assuming it's available globally or via CDN)
        if (typeof ClassicEditor !== 'undefined') {
            editor.value = await ClassicEditor.create(editorElement.value, {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'imageUpload', 'blockQuote', 'insertTable', '|',
                    'undo', 'redo'
                ],
                image: {
                    toolbar: [
                        'imageTextAlternative',
                        'imageStyle:full',
                        'imageStyle:side'
                    ]
                }
            })
            
            // Set initial content
            editor.value.setData(form.content)
            
            editor.value.model.document.on('change:data', () => {
                form.content = editor.value.getData()
            })
        } else {
            // Fallback to textarea if CKEditor is not available
            console.warn('CKEditor not available, using textarea fallback')
        }
    } catch (error) {
        console.error('Editor initialization failed:', error)
    }
}

const generateSlug = (title) => {
    return title
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-')
}

const handleFileSelect = (event) => {
    const file = event.target.files[0]
    if (file) {
        processImage(file)
    }
}

const handleDrop = (event) => {
    event.preventDefault()
    const file = event.dataTransfer.files[0]
    if (file && file.type.startsWith('image/')) {
        processImage(file)
    }
}

const processImage = (file) => {
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 2MB.')
        return
    }
    
    // Validate file type
    if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
        alert('Format file tidak didukung. Gunakan JPG, PNG, atau WebP.')
        return
    }
    
    form.image = file
    
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
        imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
}

const removeImage = () => {
    form.image = null
    imagePreview.value = null
    fileInput.value.value = ''
}

const addTag = () => {
    const tag = currentTag.value.trim().replace(',', '')
    if (tag && !form.tags.includes(tag)) {
        form.tags.push(tag)
        currentTag.value = ''
    }
}

const removeTag = (index) => {
    form.tags.splice(index, 1)
}

// Location Methods
const loadProvinces = async () => {
    try {
        const response = await fetch('/api/provinces')
        const data = await response.json()
        if (data.success) {
            provinces.value = data.data
        }
    } catch (error) {
        console.error('Error loading provinces:', error)
    }
}

const onProvinceChange = async () => {
    form.city = ''
    cities.value = []
    
    if (form.province) {
        // Clear coordinates when changing province
        form.latitude = ''
        form.longitude = ''
        
        loadingCities.value = true
        try {
            const response = await fetch(`/api/cities?province=${encodeURIComponent(form.province)}`)
            const data = await response.json()
            
            if (data.success) {
                cities.value = data.data
            }
        } catch (error) {
            console.error('Error loading cities:', error)
        }
        loadingCities.value = false
    } else {
        // Clear coordinates when province is cleared
        form.latitude = ''
        form.longitude = ''
    }
}

const onCityChange = async () => {
    if (form.city && form.province) {
        try {
            const response = await fetch(`/api/city-coordinates?province=${encodeURIComponent(form.province)}&city=${encodeURIComponent(form.city)}`)
            const data = await response.json()
            
            if (data.success) {
                form.latitude = data.data.latitude
                form.longitude = data.data.longitude
            }
        } catch (error) {
            console.error('Error loading coordinates:', error)
        }
    } else {
        // Clear coordinates when city is cleared
        form.latitude = ''
        form.longitude = ''
    }
}

const onCoordinateChange = () => {
    if (coordinateTimeout) {
        clearTimeout(coordinateTimeout)
    }
    
    coordinateTimeout = setTimeout(() => {
        if (form.latitude && form.longitude && !isNaN(form.latitude) && !isNaN(form.longitude)) {
            reverseGeocode(form.latitude, form.longitude)
        }
    }, 1000)
}

const getCurrentLocation = () => {
    gettingLocation.value = true
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                form.latitude = position.coords.latitude
                form.longitude = position.coords.longitude
                
                reverseGeocode(form.latitude, form.longitude)
                
                gettingLocation.value = false
                
                setTimeout(() => {
                    // You can add success notification here
                }, 2000)
            },
            (error) => {
                console.error('Error getting location:', error)
                alert('Tidak dapat mendapatkan lokasi Anda. Silakan masukkan secara manual.')
                gettingLocation.value = false
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            }
        )
    } else {
        alert('Geolocation tidak didukung oleh browser ini.')
        gettingLocation.value = false
    }
}

const reverseGeocode = async (lat, lng) => {
    try {
        const response = await fetch(`/api/reverse-geocode?lat=${lat}&lng=${lng}`)
        const data = await response.json()
        
        if (data.success && data.data.province) {
            form.province = data.data.province
            form.city = data.data.city
            form.country = data.data.country || 'Indonesia'
            
            // Load cities for the province if not already loaded
            if (form.province && cities.value.length === 0) {
                await onProvinceChange()
            }
            
            // Wait a moment for cities to load, then set city
            setTimeout(() => {
                if (data.data.city) {
                    form.city = data.data.city
                }
            }, 500)
        }
    } catch (error) {
        console.error('Error reverse geocoding:', error)
    }
}

const getStatusText = (status) => {
    switch (status) {
        case 'published':
            return 'Akan dipublikasikan secara langsung'
        case 'pending':
            return 'Akan dikirim untuk review editor'
        case 'draft':
            return 'Disimpan sebagai draft'
        default:
            return ''
    }
}

const saveDraft = () => {
    form.status = 'draft'
    submitForm()
}

const submitForm = () => {
    processing.value = true
    
    // Auto-generate slug if empty
    if (!form.slug && form.title) {
        form.slug = generateSlug(form.title)
    }
    
    // Set published_at for published status
    if (form.status === 'published' && !form.published_at) {
        form.published_at = new Date().toISOString().slice(0, 16)
    }
    
    // Create FormData for file upload
    const formData = new FormData()
    
    Object.keys(form).forEach(key => {
        if (key === 'tags') {
            formData.append('tags', JSON.stringify(form.tags))
        } else if (key === 'image' && form.image) {
            formData.append('image', form.image)
        } else if (form[key] !== null && form[key] !== '') {
            formData.append(key, form[key])
        }
    })
    
    // Add method spoofing for PUT request
    formData.append('_method', 'PUT')
    
    router.post(`/admin/news/${props.news.id}`, formData, {
        onSuccess: () => {
            processing.value = false
        },
        onError: () => {
            processing.value = false
        }
    })
}

// Watchers
watch(() => form.title, (newTitle) => {
    if (!form.slug || form.slug === generateSlug(props.news.title)) {
        form.slug = generateSlug(newTitle)
    }
})

// Lifecycle
onMounted(() => {
    initializeEditor()
    loadProvinces()
    
    // Load cities if province is already selected
    if (form.province) {
        onProvinceChange()
    }
    
    // Set default published_at for published status if not set
    if (form.status === 'published' && !form.published_at) {
        form.published_at = new Date().toISOString().slice(0, 16)
    }
})

onUnmounted(() => {
    if (editor.value) {
        editor.value.destroy()
    }
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

/* Form Sections */
.form-section {
    margin-bottom: 2rem;
}

.section-header {
    margin-bottom: 1rem;
    border-left: 4px solid #3b82f6;
    padding-left: 1rem;
}

.section-title {
    color: #1e293b;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.section-subtitle {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0;
}

/* Cards */
.card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border-radius: 1rem;
}

.card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 1rem 1rem 0 0 !important;
    padding: 1rem 1.5rem;
}

.card-title {
    color: #1e293b;
    font-weight: 600;
}

.sticky-card {
    position: sticky;
    top: 2rem;
}

/* Form Elements */
.form-label.required::after {
    content: ' *';
    color: #ef4444;
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    padding: 0.75rem;
}

.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input-group-text {
    background: #f8fafc;
    border: 1px solid #d1d5db;
    color: #64748b;
    border-radius: 0.5rem 0 0 0.5rem;
}

/* Editor */
.editor-wrapper {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    overflow: hidden;
}

.editor-container {
    min-height: 300px;
}

/* Current Image */
.current-image img {
    max-height: 200px;
    object-fit: cover;
    border: 2px solid #e2e8f0;
}

/* Image Upload */
.image-upload-area {
    position: relative;
    border: 2px dashed #d1d5db;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.image-upload-area:hover {
    border-color: #3b82f6;
    background: #f8fafc;
}

.upload-placeholder {
    padding: 3rem 2rem;
    text-align: center;
    cursor: pointer;
}

.upload-icon {
    font-size: 3rem;
    color: #9ca3af;
    margin-bottom: 1rem;
}

.image-preview {
    position: relative;
    overflow: hidden;
    border-radius: 0.75rem;
}

.preview-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.image-overlay {
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

.image-preview:hover .image-overlay {
    opacity: 1;
}

/* Tags */
.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag-item {
    background: #eff6ff;
    color: #1e40af;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tag-remove {
    background: none;
    border: none;
    color: #1e40af;
    padding: 0;
    cursor: pointer;
    font-size: 0.75rem;
}

.tag-remove:hover {
    color: #ef4444;
}

/* Buttons */
.btn {
    border-radius: 0.5rem;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
}

/* Edit specific styles */
.edit-indicator {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.news-info {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.news-info h6 {
    color: #374151;
    margin-bottom: 0.5rem;
}

.news-info p {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 0;
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
    
    .sticky-card {
        position: static;
    }
    
    .upload-placeholder {
        padding: 2rem 1rem;
    }
}
</style>
