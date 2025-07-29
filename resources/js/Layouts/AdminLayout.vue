<template>
    <div class="admin-layout min-vh-100">
        <!-- Sidebar -->
        <nav class="sidebar" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
            <div class="sidebar-header">
                <div class="sidebar-brand d-flex align-items-center">
                    <img src="/img/logo.png" alt="Logo" class="sidebar-logo">
                    <span v-if="!sidebarCollapsed" class="brand-text">Portal Berita</span>
                </div>
                <button @click="toggleSidebar" class="sidebar-toggle btn btn-sm">
                    <i :class="sidebarCollapsed ? 'fa fa-angle-right' : 'fa fa-angle-left'"></i>
                </button>
            </div>
            
            <div class="sidebar-content">
                <ul class="sidebar-nav">
                    <li class="nav-item">
                        <Link href="/dashboard" class="nav-link" :class="{ active: currentRoute === '/dashboard' }">
                            <i class="fa fa-tachometer-alt nav-icon"></i>
                            <span v-if="!sidebarCollapsed" class="nav-text">Dashboard</span>
                        </Link>
                    </li>
                    
                    <li class="nav-section" v-if="!sidebarCollapsed">
                        <span class="nav-section-title">Konten</span>
                    </li>
                    
                    <li class="nav-item">
                        <Link href="/admin/news" class="nav-link" :class="{ active: currentRoute.includes('/admin/news') }">
                            <i class="fa fa-newspaper nav-icon"></i>
                            <span v-if="!sidebarCollapsed" class="nav-text">Kelola Berita</span>
                        </Link>
                    </li>
                    
                    <li class="nav-item">
                        <Link href="/admin/categories" class="nav-link" :class="{ active: currentRoute.includes('/admin/categories') }">
                            <i class="fa fa-tags nav-icon"></i>
                            <span v-if="!sidebarCollapsed" class="nav-text">Kategori</span>
                        </Link>
                    </li>
                    
                    <li class="nav-item">
                        <Link href="/news/draft" class="nav-link" :class="{ active: currentRoute.includes('/news/draft') }">
                            <i class="fa fa-edit nav-icon"></i>
                            <span v-if="!sidebarCollapsed" class="nav-text">My Draft</span>
                        </Link>
                    </li>
                    
                    <li class="nav-section" v-if="!sidebarCollapsed && canManageUsers">
                        <span class="nav-section-title">Manajemen</span>
                    </li>
                    
                    <li v-if="canManageUsers" class="nav-item">
                        <Link href="/admin/users/manage" class="nav-link" :class="{ active: currentRoute.includes('/admin/users/manage') }">
                            <i class="fa fa-users nav-icon"></i>
                            <span v-if="!sidebarCollapsed" class="nav-text">User Management</span>
                        </Link>
                    </li>
                    
                    <li class="nav-section" v-if="!sidebarCollapsed">
                        <span class="nav-section-title">Lainnya</span>
                    </li>
                    
                    <li class="nav-item">
                        <a href="/" class="nav-link">
                            <i class="fa fa-home nav-icon"></i>
                            <span v-if="!sidebarCollapsed" class="nav-text">Lihat Website</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="main-content" :class="{ 'content-expanded': sidebarCollapsed }">
            <!-- Progress Bar -->
            <div v-if="isNavigating" class="progress-bar">
                <div class="progress-line"></div>
            </div>
            
            <!-- Top Navigation -->
            <header class="top-nav shadow-sm">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="nav-breadcrumb">
                            <h6 class="mb-0 text-muted">{{ getBreadcrumb() }}</h6>
                        </div>
                        
                        <div class="nav-actions d-flex align-items-center">
                            <!-- Notifications -->
                            <div class="dropdown me-3">
                                <button class="btn btn-outline-secondary btn-sm position-relative" 
                                        data-bs-toggle="dropdown">
                                    <i class="fa fa-bell"></i>
                                    <span v-if="notifications.length > 0" 
                                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ notifications.length }}
                                    </span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <h6 class="dropdown-header">Notifikasi</h6>
                                    <div v-if="notifications.length === 0" class="dropdown-item-text text-muted">
                                        Tidak ada notifikasi baru
                                    </div>
                                    <div v-else>
                                        <a v-for="notification in notifications" 
                                           :key="notification.id" 
                                           href="#" 
                                           class="dropdown-item">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fa fa-circle text-primary small"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h6 class="dropdown-item-title">{{ notification.title }}</h6>
                                                    <p class="dropdown-item-text">{{ notification.message }}</p>
                                                    <small class="text-muted">{{ notification.time_ago }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- User Menu -->
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm d-flex align-items-center" 
                                        type="button"
                                        id="userDropdownMenuButton"
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false">
                                    <div class="user-avatar me-2">
                                        <img v-if="user?.image" 
                                             :src="`/storage/images/${user.image}`" 
                                             :alt="user?.name"
                                             class="rounded-circle">
                                        <div v-else class="avatar-placeholder">
                                            {{ (user?.name || 'U').charAt(0) }}
                                        </div>
                                    </div>
                                    <span class="d-none d-md-inline">{{ user?.name || 'User' }}</span>
                                    <i class="fa fa-chevron-down ms-2"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownMenuButton">
                                    <li class="dropdown-header">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-2">
                                                <img v-if="user?.image" 
                                                     :src="`/storage/images/${user.image}`" 
                                                     :alt="user?.name"
                                                     class="rounded-circle">
                                                <div v-else class="avatar-placeholder">
                                                    {{ (user?.name || 'U').charAt(0) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ user?.name || 'User' }}</h6>
                                                <small class="text-muted">{{ user?.email }}</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <Link href="/profile" class="dropdown-item">
                                            <i class="fa fa-user me-2"></i>Profile
                                        </Link>
                                    </li>
                                    <li>
                                        <Link href="/settings" class="dropdown-item">
                                            <i class="fa fa-cog me-2"></i>Settings
                                        </Link>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button @click="logout" class="dropdown-item text-danger">
                                            <i class="fa fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="page-content">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'

// Props
const props = defineProps({
    user: Object,
    notifications: {
        type: Array,
        default: () => []
    }
})

// Reactive data
const sidebarCollapsed = ref(false)
const isNavigating = ref(false)
const page = usePage()

// Setup Inertia.js progress
onMounted(() => {
    router.on('start', () => {
        isNavigating.value = true
    })
    
    router.on('finish', () => {
        isNavigating.value = false
    })
    
    // Initialize Bootstrap dropdowns manually if needed
    const initDropdowns = () => {
        if (window.bootstrap && window.bootstrap.Dropdown) {
            const dropdownElementList = document.querySelectorAll('[data-bs-toggle="dropdown"]')
            dropdownElementList.forEach(dropdownToggleEl => {
                new window.bootstrap.Dropdown(dropdownToggleEl)
            })
        }
    }
    
    // Initialize after a short delay to ensure DOM is ready
    setTimeout(initDropdowns, 100)
})

// Computed
const currentRoute = computed(() => page.url)

const canManageUsers = computed(() => {
    return props.user?.roles?.some(role => role.name === 'Super Admin')
})

// Methods
const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value
    localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value)
}

const getBreadcrumb = () => {
    const path = currentRoute.value
    if (path === '/dashboard') return 'Dashboard'
    if (path.includes('/admin/news')) return 'Kelola Berita'
    if (path.includes('/admin/categories')) return 'Kategori'
    if (path.includes('/admin/users/manage')) return 'User Management'
    if (path.includes('/news/draft')) return 'My Draft'
    return 'Admin Panel'
}

const logout = () => {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        router.post('/logout')
    }
}

const initializeDropdowns = () => {
    // Manual dropdown toggle for user menu
    const dropdownBtn = document.getElementById('userDropdownMenuButton')
    const dropdownMenu = dropdownBtn?.nextElementSibling
    
    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', (e) => {
            e.preventDefault()
            e.stopPropagation()
            
            // Toggle dropdown
            const isOpen = dropdownMenu.classList.contains('show')
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show')
            })
            
            // Toggle current dropdown
            if (!isOpen) {
                dropdownMenu.classList.add('show')
            }
        })
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show')
            }
        })
    }
}

// Lifecycle
onMounted(() => {
    const saved = localStorage.getItem('sidebarCollapsed')
    if (saved !== null) {
        sidebarCollapsed.value = saved === 'true'
    }
    
    // Initialize custom dropdown functionality
    setTimeout(() => {
        initializeDropdowns()
    }, 100)
})
</script>

<style scoped>
/* Layout Structure */
.admin-layout {
    display: flex;
    background: #f8fafc;
}

.sidebar {
    width: 260px;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    transition: all 0.3s ease;
    position: fixed;
    height: 100vh;
    z-index: 1000;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-collapsed {
    width: 70px;
}

.main-content {
    flex: 1;
    margin-left: 260px;
    transition: all 0.3s ease;
    min-height: 100vh;
}

.content-expanded {
    margin-left: 70px;
}

/* Sidebar Header */
.sidebar-header {
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar-brand {
    display: flex;
    align-items: center;
}

.sidebar-logo {
    width: 40px;
    height: 40px;
    object-fit: contain;
}

.brand-text {
    margin-left: 0.75rem;
    font-weight: 700;
    font-size: 1.1rem;
}

.sidebar-toggle {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-toggle:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* Sidebar Navigation */
.sidebar-content {
    height: calc(100vh - 80px);
    overflow-y: auto;
    padding: 1rem 0;
}

.sidebar-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-section {
    padding: 1rem 1.5rem 0.5rem;
}

.nav-section-title {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    opacity: 0.7;
    letter-spacing: 0.5px;
}

.nav-item {
    margin: 0.25rem 0;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    border-radius: 0;
    position: relative;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.nav-link.active {
    background: rgba(255, 255, 255, 0.15);
    color: white;
}

.nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: white;
}

.nav-icon {
    width: 20px;
    text-align: center;
    margin-right: 0.75rem;
}

.sidebar-collapsed .nav-link {
    justify-content: center;
    padding: 0.75rem;
}

/* Top Navigation */
.top-nav {
    background: white;
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.nav-breadcrumb h6 {
    color: #64748b;
    font-weight: 500;
}

/* Dropdown Menu Improvements */
.dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    z-index: 1050;
    min-width: 250px;
    background: white;
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    display: none;
}

.dropdown-menu.show {
    display: block;
    animation: fadeIn 0.15s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-menu-end {
    right: 0;
    left: auto;
}

/* User Avatar */
.user-avatar {
    width: 32px;
    height: 32px;
    position: relative;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: 600;
    font-size: 0.875rem;
}

/* Page Content */
.page-content {
    padding: 2rem;
    min-height: calc(100vh - 80px);
}

/* Dropdown Customization */
.dropdown-menu {
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border-radius: 0.75rem;
    min-width: 250px;
}

/* Sidebar Dropdown */
.sidebar-dropdown {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    min-width: 200px;
    margin-left: 1rem;
}

.sidebar-dropdown .dropdown-item {
    color: #1e293b;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    margin: 0.25rem 0.5rem;
}

.sidebar-dropdown .dropdown-item:hover {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    transform: translateX(4px);
}

.sidebar-dropdown .dropdown-item.active {
    background: rgba(59, 130, 246, 0.15);
    color: #3b82f6;
    font-weight: 600;
}

.sidebar-dropdown .dropdown-item i {
    width: 16px;
    text-align: center;
}

.nav-item.dropdown .nav-link.dropdown-toggle::after {
    display: none;
}

.dropdown-item {
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background: #f8fafc;
}

.dropdown-item-title {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.dropdown-item-text {
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 0.25rem;
}

/* Progress Bar */
.progress-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: rgba(59, 130, 246, 0.1);
    z-index: 9999;
}

.progress-line {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #1e40af);
    width: 0%;
    animation: indeterminate 1.5s infinite linear;
    transform-origin: 0% 50%;
}

@keyframes indeterminate {
    0% {
        transform: translateX(0) scaleX(0);
    }
    40% {
        transform: translateX(0) scaleX(0.4);
    }
    100% {
        transform: translateX(100%) scaleX(0.5);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar-collapsed {
        transform: translateX(-100%);
    }
    
    .main-content,
    .content-expanded {
        margin-left: 0;
    }
    
    .page-content {
        padding: 1rem;
    }
}

/* Scrollbar Styling */
.sidebar-content::-webkit-scrollbar {
    width: 4px;
}

.sidebar-content::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.sidebar-content::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
}

.sidebar-content::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>
