<template>
    <div>
        <!-- Navbar Start -->
        <div class="container-fluid sticky-top px-0">
            <!-- <div class="container-fluid topbar bg-dark d-none d-lg-block">
                <div class="container px-0">
                    <div class="row gx-0 align-items-center" style="height: 45px;">
                        <div class="col-lg-8 text-center text-lg-start mb-lg-0">
                            <div class="d-flex flex-wrap">
                                <div class="border-end border-primary pe-3">
                                    <a href="#" class="text-muted small"><i class="fas fa-map-marker-alt text-primary me-2"></i>Find A Location</a>
                                </div>
                                <div class="ps-3">
                                    <a href="mailto:example@gmail.com" class="text-muted small"><i class="fas fa-envelope text-primary me-2"></i>example@gmail.com</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-center text-lg-end">
                            <div class="d-flex align-items-center justify-content-end">
                                <a href="#" class="btn btn-light btn-sm-square rounded-circle me-3"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="btn btn-light btn-sm-square rounded-circle me-3"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="btn btn-light btn-sm-square rounded-circle me-3"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="btn btn-light btn-sm-square rounded-circle me-0"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="container-fluid bg-light">
                <div class="container px-0">
                    <nav class="navbar navbar-light navbar-expand-xl">
                        <Link :href="route('home')" class="navbar-brand mt-3">
                            <img src="/img/logo.png" alt="Logo" class="img-fluid" style="max-height: 60px;">
                            <!-- <p class="text-primary display-6 mb-2" style="line-height: 0;">Newsers</p>
                            <small class="text-body fw-normal" style="letter-spacing: 12px; color: darkgray;">Newspaper</small> -->
                        </Link>
                        <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                            <span class="fa fa-bars text-primary"></span>
                        </button>
                        <div class="collapse navbar-collapse bg-light py-3" id="navbarCollapse">
                            <div class="navbar-nav mx-auto border-top">
                                <Link :href="route('home')" class="nav-item nav-link active">Home</Link>
                                <Link :href="route('gallery.index')" class="nav-item nav-link">Gallery</Link>
                                <div class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle" @click="toggleDropdown($event)">Berita</a>
                                    <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                        <Link :href="route('home', { filter: 'trending' })" class="dropdown-item">Trending</Link>
                                        <Link :href="route('home', { filter: 'terbaru' })" class="dropdown-item">Terbaru</Link>
                                        <Link :href="route('home', { filter: 'terdekat' })" class="dropdown-item">Terdekat</Link>
                                    </div>
                                </div>
                                <div class="nav-item dropdown" v-if="$page.props.auth.user">
                                    <a href="#" class="nav-link dropdown-toggle" @click="toggleDropdown($event)">Admin</a>
                                    <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                        <Link :href="route('dashboard')" class="dropdown-item">
                                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                        </Link>
                                        <Link :href="route('admin.news.index')" class="dropdown-item">
                                            <i class="fas fa-newspaper me-2"></i>Kelola Berita
                                        </Link>
                                        <Link :href="route('admin.users.manage')" class="dropdown-item" v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.some(role => role.name === 'Super Admin')">
                                            <i class="fas fa-users me-2"></i>Kelola User
                                        </Link>
                                        <Link :href="route('admin.category.manage')" class="dropdown-item">
                                            <i class="fas fa-tags me-2"></i>Kategori
                                        </Link>
                                    </div>
                                </div>
                                <a href="contact.html" class="nav-item nav-link">Kontak</a>
                            </div>
                            <!-- <div class="d-flex me-4">
                                <div class="d-flex flex-column pe-3 border-end border-primary">
                                    <span class="text-body">Get 20% off</span>
                                    <span class="text-primary">1234567890</span>
                                </div>
                            </div> -->
                            
                            <!-- Authentication Links -->
                            <div v-if="$page.props.auth.user" class="d-flex align-items-center">
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary rounded-pill py-2 px-3 dropdown-toggle" 
                                            type="button" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                        {{ $page.props.auth.user.name }}
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <Link :href="route('dashboard')" class="dropdown-item">
                                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                            </Link>
                                        </li>
                                        <li>
                                            <Link :href="route('profile.edit', $page.props.auth.user.id)" class="dropdown-item">
                                                <i class="fas fa-user me-2"></i>Profile
                                            </Link>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form @submit.prevent="logout">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div v-else>
                                <Link :href="route('login')" class="btn btn-primary rounded-pill py-2 px-4 ms-4">
                                    Login
                                </Link>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        <!-- Navbar End -->

        <main>
            <slot />
        </main>

        <!-- Footer End -->

        <!-- Copyright Start -->
        <div class="container-fluid copyright bg-dark py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <span class="text-light"><a href="#"><i class="fas fa-copyright text-light me-2"></i>Winnicode</a>, All right reserved.</span>
                    </div>
                
                </div>
            </div>
        </div>
        <!-- Copyright End -->
    </div>
</template>

<script setup>
import { onMounted, nextTick } from 'vue'
import { Link, router } from '@inertiajs/vue3'

// Initialize dropdowns when component is mounted
onMounted(async () => {
    await nextTick()
    
    // Initialize all Bootstrap dropdowns
    const dropdownElementList = document.querySelectorAll('[data-bs-toggle="dropdown"]')
    dropdownElementList.forEach(dropdownToggleEl => {
        // Check if Bootstrap is available
        if (window.bootstrap && window.bootstrap.Dropdown) {
            new window.bootstrap.Dropdown(dropdownToggleEl)
        }
    })
})

const toggleDropdown = (event) => {
    event.preventDefault()
    event.stopPropagation()
    
    const dropdownMenu = event.target.nextElementSibling
    
    // Close all other dropdowns
    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
        if (menu !== dropdownMenu) {
            menu.classList.remove('show')
        }
    })
    
    // Toggle current dropdown
    if (dropdownMenu) {
        dropdownMenu.classList.toggle('show')
    }
}

// Close dropdowns when clicking outside
onMounted(() => {
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show')
            })
        }
    })
})

const logout = () => {
    router.post(route('logout'))
}
</script>

<style scoped>
/* Dropdown Styles */
.dropdown {
    position: relative;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    min-width: 160px;
    padding: 0.5rem 0;
    margin: 0;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    display: block;
    width: 100%;
    padding: 0.375rem 1rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    text-decoration: none;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
    cursor: pointer;
}

.dropdown-item:hover,
.dropdown-item:focus {
    color: #1e2125;
    background-color: #f8f9fa;
}

.nav-link {
    cursor: pointer;
}

.nav-link:hover {
    color: var(--bs-primary) !important;
}
</style>
