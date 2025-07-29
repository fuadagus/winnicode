<template>
    <div class="min-vh-100 d-flex align-items-center justify-content-center" 
         style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
        <!-- Background Pattern -->
        <div class="position-fixed w-100 h-100" 
             style="background-image: url('/img/pattern.png'); opacity: 0.1; z-index: 1;"></div>
        
        <div class="container position-relative" style="z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                        <!-- Header -->
                        <div class="card-header bg-primary text-white text-center py-4 border-0">
                            <div class="mb-3">
                                <img src="/img/logo.png" alt="Logo" class="img-fluid" style="max-height: 60px;">
                            </div>
                            <h4 class="mb-1 fw-bold">Selamat Datang</h4>
                            <p class="mb-0 opacity-75">Masuk ke Portal Berita</p>
                        </div>
                        
                        <!-- Body -->
                        <div class="card-body p-5">
                            <form @submit.prevent="submitLogin">
                                <!-- Email Field -->
                                <div class="form-floating mb-4">
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
                                
                                <!-- Password Field -->
                                <div class="form-floating mb-4">
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
                                
                                <!-- Remember Me -->
                                <div class="form-check mb-4">
                                    <input v-model="form.remember" 
                                           class="form-check-input" 
                                           type="checkbox" 
                                           id="remember">
                                    <label class="form-check-label" for="remember">
                                        Ingat saya
                                    </label>
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
                                        <i class="fa fa-spinner fa-spin me-2"></i>Memproses...
                                    </span>
                                    <span v-else>
                                        <i class="fa fa-sign-in-alt me-2"></i>Masuk
                                    </span>
                                </button>
                                
                                <!-- Register Link -->
                                <div class="text-center">
                                    <p class="text-muted mb-0">
                                        Belum punya akun? 
                                        <a :href="route('register')" class="text-primary text-decoration-none fw-semibold">
                                            Daftar sekarang
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
    email: '',
    password: '',
    remember: false
})

const showPassword = ref(false)
const processing = ref(false)

// Methods
const submitLogin = () => {
    processing.value = true
    
    router.post(route('login.submit'), form.value, {
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
        'register': '/register',
        'login.submit': '/login/submit'
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

@media (max-width: 768px) {
    .card-body {
        padding: 2rem !important;
    }
}
</style>
