<template>
    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5" 
         style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
        <!-- Background Pattern -->
        <div class="position-fixed w-100 h-100" 
             style="background-image: url('/img/pattern.png'); opacity: 0.1; z-index: 1;"></div>
        
        <div class="container position-relative" style="z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                        <!-- Header -->
                        <div class="card-header bg-primary text-white text-center py-4 border-0">
                            <div class="mb-3">
                                <img src="/img/logo.png" alt="Logo" class="img-fluid" style="max-height: 50px;">
                            </div>
                            <h4 class="mb-1 fw-bold">Daftar Akun Baru</h4>
                            <p class="mb-0 opacity-75">Bergabung dengan Portal Berita</p>
                        </div>
                        
                        <!-- Body -->
                        <div class="card-body p-5">
                            <form @submit.prevent="submitRegister">
                                <div class="row">
                                    <!-- Name Field -->
                                    <div class="col-md-6 mb-4">
                                        <div class="form-floating">
                                            <input v-model="form.name"
                                                   type="text"
                                                   class="form-control"
                                                   :class="{ 'is-invalid': errors.name }"
                                                   id="name"
                                                   placeholder="Nama Lengkap"
                                                   required>
                                            <label for="name">
                                                <i class="fa fa-user me-2"></i>Nama Lengkap
                                            </label>
                                            <div v-if="errors.name" class="invalid-feedback">
                                                {{ errors.name }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Email Field -->
                                    <div class="col-md-6 mb-4">
                                        <div class="form-floating">
                                            <input v-model="form.email"
                                                   type="email"
                                                   class="form-control"
                                                   :class="{ 'is-invalid': errors.email }"
                                                   id="email"
                                                   placeholder="name@example.com"
                                                   required>
                                            <label for="email">
                                                <i class="fa fa-envelope me-2"></i>Email Address
                                            </label>
                                            <div v-if="errors.email" class="invalid-feedback">
                                                {{ errors.email }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <!-- Password Field -->
                                    <div class="col-md-6 mb-4">
                                        <div class="form-floating position-relative">
                                            <input v-model="form.password"
                                                   :type="showPassword ? 'text' : 'password'"
                                                   class="form-control"
                                                   :class="{ 'is-invalid': errors.password }"
                                                   id="password"
                                                   placeholder="Password"
                                                   required>
                                            <label for="password">
                                                <i class="fa fa-lock me-2"></i>Password
                                            </label>
                                            <button type="button" 
                                                    class="btn btn-outline-secondary position-absolute end-0 top-50 translate-middle-y me-3 border-0"
                                                    @click="showPassword = !showPassword"
                                                    style="z-index: 10;">
                                                <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                                            </button>
                                            <div v-if="errors.password" class="invalid-feedback">
                                                {{ errors.password }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Confirm Password Field -->
                                    <div class="col-md-6 mb-4">
                                        <div class="form-floating position-relative">
                                            <input v-model="form.password_confirmation"
                                                   :type="showConfirmPassword ? 'text' : 'password'"
                                                   class="form-control"
                                                   :class="{ 'is-invalid': errors.password_confirmation }"
                                                   id="password_confirmation"
                                                   placeholder="Konfirmasi Password"
                                                   required>
                                            <label for="password_confirmation">
                                                <i class="fa fa-lock me-2"></i>Konfirmasi Password
                                            </label>
                                            <button type="button" 
                                                    class="btn btn-outline-secondary position-absolute end-0 top-50 translate-middle-y me-3 border-0"
                                                    @click="showConfirmPassword = !showConfirmPassword"
                                                    style="z-index: 10;">
                                                <i :class="showConfirmPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                                            </button>
                                            <div v-if="errors.password_confirmation" class="invalid-feedback">
                                                {{ errors.password_confirmation }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Role Selection -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa fa-users me-2"></i>Pilih Role
                                    </label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check form-check-card">
                                                <input v-model="form.role" 
                                                       class="form-check-input" 
                                                       type="radio" 
                                                       value="Writer"
                                                       id="roleWriter">
                                                <label class="form-check-label w-100 p-3 border rounded" for="roleWriter">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa fa-pen-alt fa-2x text-primary me-3"></i>
                                                        <div>
                                                            <h6 class="mb-1">Writer</h6>
                                                            <small class="text-muted">Menulis dan mengelola artikel</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-check-card">
                                                <input v-model="form.role" 
                                                       class="form-check-input" 
                                                       type="radio" 
                                                       value="Reader"
                                                       id="roleReader">
                                                <label class="form-check-label w-100 p-3 border rounded" for="roleReader">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa fa-book-reader fa-2x text-success me-3"></i>
                                                        <div>
                                                            <h6 class="mb-1">Reader</h6>
                                                            <small class="text-muted">Membaca dan mengikuti berita</small>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="errors.role" class="text-danger small mt-2">
                                        {{ errors.role }}
                                    </div>
                                </div>
                                
                                <!-- Terms Agreement -->
                                <div class="form-check mb-4">
                                    <input v-model="form.terms" 
                                           class="form-check-input" 
                                           type="checkbox" 
                                           id="terms"
                                           required>
                                    <label class="form-check-label" for="terms">
                                        Saya setuju dengan 
                                        <a href="#" class="text-primary text-decoration-none">syarat dan ketentuan</a> 
                                        serta 
                                        <a href="#" class="text-primary text-decoration-none">kebijakan privasi</a>
                                    </label>
                                    <div v-if="errors.terms" class="text-danger small">
                                        {{ errors.terms }}
                                    </div>
                                </div>
                                
                                <!-- Error Message -->
                                <div v-if="errors.general" class="alert alert-danger mb-4">
                                    <i class="fa fa-exclamation-triangle me-2"></i>
                                    {{ errors.general }}
                                </div>
                                
                                <!-- Submit Button -->
                                <button type="submit" 
                                        class="btn btn-primary w-100 py-3 mb-4 fw-semibold"
                                        :disabled="processing">
                                    <span v-if="processing">
                                        <i class="fa fa-spinner fa-spin me-2"></i>Mendaftar...
                                    </span>
                                    <span v-else>
                                        <i class="fa fa-user-plus me-2"></i>Daftar Sekarang
                                    </span>
                                </button>
                                
                                <!-- Login Link -->
                                <div class="text-center">
                                    <p class="text-muted mb-0">
                                        Sudah punya akun? 
                                        <a :href="route('login')" class="text-primary text-decoration-none fw-semibold">
                                            Masuk di sini
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Footer -->
                        <div class="card-footer bg-light text-center border-0 py-3">
                            <small class="text-muted">
                                © 2025 Portal Berita. All rights reserved.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

// Props
defineProps({
    errors: {
        type: Object,
        default: () => ({})
    }
})

// Reactive data
const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'Reader',
    terms: false
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const processing = ref(false)

// Methods
const submitRegister = () => {
    processing.value = true
    
    router.post(route('register.submit'), form.value, {
        onFinish: () => {
            processing.value = false
        },
        onError: () => {
            processing.value = false
        }
    })
}

const route = (name) => {
    const routes = {
        'login': '/login',
        'register.submit': '/register/submit'
    }
    return routes[name] || '#'
}
</script>

<style scoped>
.card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: #3b82f6;
}

.form-floating > .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.btn-primary {
    background: linear-gradient(45deg, #3b82f6, #1e40af);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
}

.card-header {
    background: linear-gradient(45deg, #3b82f6, #1e40af) !important;
}

.form-check-card .form-check-label {
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-check-card .form-check-input:checked + .form-check-label {
    border-color: #3b82f6 !important;
    background: rgba(59, 130, 246, 0.1);
}

.form-check-card .form-check-label:hover {
    border-color: #3b82f6 !important;
    background: rgba(59, 130, 246, 0.05);
}

@media (max-width: 768px) {
    .card-body {
        padding: 2rem !important;
    }
}
</style>
