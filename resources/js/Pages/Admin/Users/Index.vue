<template>
    <AdminLayout>
        <!-- Header -->
        <div class="page-header mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="fw-bold mb-2">Kelola Pengguna</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <Link href="/dashboard" class="text-decoration-none">
                                    <i class="fa fa-home me-1"></i>Dashboard
                                </Link>
                            </li>
                            <li class="breadcrumb-item">Pengguna</li>
                            <li class="breadcrumb-item active">Kelola</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <button 
                        @click="showCreateModal = true"
                        class="btn btn-primary">
                        <i class="fa fa-plus me-2"></i>Tambah Pengguna
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Cari Pengguna</label>
                        <input 
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Nama, email, atau role..."
                            @input="applyFilters">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filter Role</label>
                        <select 
                            v-model="filters.role"
                            class="form-select"
                            @change="applyFilters">
                            <option value="">Semua Role</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status Online</label>
                        <select 
                            v-model="filters.status"
                            class="form-select"
                            @change="applyFilters">
                            <option value="">Semua Status</option>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button 
                            @click="resetFilters"
                            class="btn btn-outline-secondary w-100">
                            <i class="fa fa-refresh me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fa fa-users text-primary me-2"></i>Daftar Pengguna
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-3">
                            Total: {{ filteredUsers.length }} pengguna
                        </span>
                        <div class="btn-group" role="group">
                            <button 
                                @click="selectedUsers.length > 0 ? showBulkDeleteModal = true : null"
                                :disabled="selectedUsers.length === 0"
                                class="btn btn-outline-danger btn-sm">
                                <i class="fa fa-trash me-1"></i>
                                Hapus ({{ selectedUsers.length }})
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">
                                    <input 
                                        type="checkbox"
                                        class="form-check-input"
                                        :checked="selectAll"
                                        @change="toggleSelectAll">
                                </th>
                                <th style="width: 60px;">Avatar</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Jumlah Berita</th>
                                <th>Status</th>
                                <th>Bergabung</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td colspan="9" class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                    Memuat data...
                                </td>
                            </tr>
                            <tr v-else-if="filteredUsers.length === 0">
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fa fa-users fa-3x mb-3 opacity-25"></i>
                                    <div>{{ filters.search ? 'Tidak ada pengguna yang ditemukan' : 'Belum ada pengguna' }}</div>
                                </td>
                            </tr>
                            <tr v-else v-for="user in paginatedUsers" :key="user.id" class="user-row">
                                <td>
                                    <input 
                                        type="checkbox"
                                        class="form-check-input"
                                        :value="user.id"
                                        v-model="selectedUsers">
                                </td>
                                <td>
                                    <div class="user-avatar">
                                        <img 
                                            :src="user.image ? `/storage/images/${user.image}` : '/img/default.jpeg'"
                                            :alt="user.name"
                                            class="rounded-circle"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ user.name }}</span>
                                        <small class="text-muted">ID: {{ user.id }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-primary">{{ user.email }}</span>
                                </td>
                                <td>
                                    <span v-for="role in user.roles" :key="role.id" 
                                          class="badge me-1"
                                          :class="getRoleBadgeClass(role.name)">
                                        {{ role.name }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-newspaper text-muted me-1"></i>
                                        <span>{{ user.news_count || 0 }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge"
                                          :class="user.is_online ? 'bg-success' : 'bg-danger'">
                                        {{ user.is_online ? 'Online' : 'Offline' }}
                                    </span>
                                    <div v-if="!user.is_online" class="small text-muted">
                                        {{ user.last_seen_human }}
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ formatDate(user.created_at) }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button 
                                            @click="editUser(user)"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="tooltip"
                                            title="Edit Pengguna">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button 
                                            @click="assignRole(user)"
                                            class="btn btn-sm btn-outline-info"
                                            data-bs-toggle="tooltip"
                                            title="Assign Role">
                                            <i class="fa fa-user-tag"></i>
                                        </button>
                                        <button 
                                            @click="deleteUser(user)"
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="tooltip"
                                            title="Hapus Pengguna"
                                            :disabled="user.id === currentUser?.id">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4" v-if="filteredUsers.length > 0">
            <div class="text-muted">
                Menampilkan {{ (currentPage - 1) * perPage + 1 }} - {{ Math.min(currentPage * perPage, filteredUsers.length) }} 
                dari {{ filteredUsers.length }} pengguna
            </div>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                        <button class="page-link" @click="currentPage = 1" :disabled="currentPage === 1">
                            <i class="fa fa-angle-double-left"></i>
                        </button>
                    </li>
                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                        <button class="page-link" @click="currentPage--" :disabled="currentPage === 1">
                            <i class="fa fa-angle-left"></i>
                        </button>
                    </li>
                    <li v-for="page in visiblePages" :key="page" 
                        class="page-item" :class="{ active: page === currentPage }">
                        <button class="page-link" @click="currentPage = page">{{ page }}</button>
                    </li>
                    <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                        <button class="page-link" @click="currentPage++" :disabled="currentPage === totalPages">
                            <i class="fa fa-angle-right"></i>
                        </button>
                    </li>
                    <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                        <button class="page-link" @click="currentPage = totalPages" :disabled="currentPage === totalPages">
                            <i class="fa fa-angle-double-right"></i>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Create/Edit User Modal -->
        <div class="modal fade" :class="{ show: showCreateModal }" tabindex="-1" style="display: block;" v-if="showCreateModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-user-plus me-2"></i>
                            {{ editingUser ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
                        </h5>
                        <button type="button" class="btn-close" @click="closeCreateModal"></button>
                    </div>
                    <form @submit.prevent="saveUser">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap *</label>
                                    <input 
                                        v-model="userForm.name"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.name }"
                                        required>
                                    <div v-if="errors.name" class="invalid-feedback">
                                        {{ errors.name }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input 
                                        v-model="userForm.email"
                                        type="email"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.email }"
                                        required>
                                    <div v-if="errors.email" class="invalid-feedback">
                                        {{ errors.email }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ editingUser ? 'Password Baru (Kosongkan jika tidak diubah)' : 'Password *' }}
                                    </label>
                                    <input 
                                        v-model="userForm.password"
                                        type="password"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.password }"
                                        :required="!editingUser">
                                    <div v-if="errors.password" class="invalid-feedback">
                                        {{ errors.password }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input 
                                        v-model="userForm.password_confirmation"
                                        type="password"
                                        class="form-control"
                                        :required="userForm.password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Role *</label>
                                    <select 
                                        v-model="userForm.role"
                                        class="form-select"
                                        :class="{ 'is-invalid': errors.role }"
                                        required>
                                        <option value="">Pilih Role</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.id">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                    <div v-if="errors.role" class="invalid-feedback">
                                        {{ errors.role }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Bio</label>
                                    <textarea 
                                        v-model="userForm.bio"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Deskripsi singkat tentang pengguna..."></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Avatar</label>
                                    <input 
                                        @change="handleImageUpload"
                                        type="file"
                                        class="form-control"
                                        accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeCreateModal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="saving">
                                <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                                {{ editingUser ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assign Role Modal -->
        <div class="modal fade" :class="{ show: showRoleModal }" tabindex="-1" style="display: block;" v-if="showRoleModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-user-tag me-2"></i>
                            Assign Role untuk {{ selectedUser?.name }}
                        </h5>
                        <button type="button" class="btn-close" @click="showRoleModal = false"></button>
                    </div>
                    <form @submit.prevent="saveRole">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Role Saat Ini:</label>
                                <div>
                                    <span v-for="role in selectedUser?.roles" :key="role.id" 
                                          class="badge bg-primary me-1">
                                        {{ role.name }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pilih Role Baru *</label>
                                <select 
                                    v-model="roleForm.role"
                                    class="form-select"
                                    required>
                                    <option value="">Pilih Role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="showRoleModal = false">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="saving">
                                <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                                Update Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Backdrop -->
        <div v-if="showCreateModal || showRoleModal || showBulkDeleteModal" 
             class="modal-backdrop fade show"
             @click="closeAllModals"></div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
const props = defineProps({
    users: {
        type: Array,
        default: () => []
    },
    roles: {
        type: Array,
        default: () => []
    },
    currentUser: Object
})

// Reactive data
const loading = ref(false)
const saving = ref(false)
const showCreateModal = ref(false)
const showRoleModal = ref(false)
const showBulkDeleteModal = ref(false)
const editingUser = ref(null)
const selectedUser = ref(null)
const selectedUsers = ref([])

// Filters
const filters = ref({
    search: '',
    role: '',
    status: ''
})

// Pagination
const currentPage = ref(1)
const perPage = ref(10)

// Forms
const userForm = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    bio: '',
    image: null
})

const roleForm = ref({
    role: ''
})

const errors = ref({})

// Computed
const filteredUsers = computed(() => {
    let filtered = props.users || []

    if (filters.value.search) {
        const search = filters.value.search.toLowerCase()
        filtered = filtered.filter(user => 
            user.name.toLowerCase().includes(search) ||
            user.email.toLowerCase().includes(search) ||
            user.roles.some(role => role.name.toLowerCase().includes(search))
        )
    }

    if (filters.value.role) {
        filtered = filtered.filter(user =>
            user.roles.some(role => role.name === filters.value.role)
        )
    }

    if (filters.value.status) {
        if (filters.value.status === 'online') {
            filtered = filtered.filter(user => user.is_online)
        } else if (filters.value.status === 'offline') {
            filtered = filtered.filter(user => !user.is_online)
        }
    }

    return filtered
})

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / perPage.value))

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * perPage.value
    const end = start + perPage.value
    return filteredUsers.value.slice(start, end)
})

const visiblePages = computed(() => {
    const pages = []
    const total = totalPages.value
    const current = currentPage.value
    const delta = 2

    for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) {
        pages.push(i)
    }

    return pages
})

const selectAll = computed({
    get() {
        return paginatedUsers.value.length > 0 && selectedUsers.value.length === paginatedUsers.value.length
    },
    set(value) {
        if (value) {
            selectedUsers.value = paginatedUsers.value.map(user => user.id)
        } else {
            selectedUsers.value = []
        }
    }
})

// Methods
const applyFilters = () => {
    currentPage.value = 1
}

const resetFilters = () => {
    filters.value = {
        search: '',
        role: '',
        status: ''
    }
    currentPage.value = 1
}

const toggleSelectAll = () => {
    selectAll.value = !selectAll.value
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getRoleBadgeClass = (roleName) => {
    const classes = {
        'Super Admin': 'bg-danger',
        'Editor': 'bg-warning',
        'Writer': 'bg-info'
    }
    return classes[roleName] || 'bg-secondary'
}

const editUser = (user) => {
    editingUser.value = user
    userForm.value = {
        name: user.name,
        email: user.email,
        password: '',
        password_confirmation: '',
        role: user.roles[0]?.id || '',
        bio: user.bio || '',
        image: null
    }
    showCreateModal.value = true
}

const assignRole = (user) => {
    selectedUser.value = user
    roleForm.value.role = user.roles[0]?.id || ''
    showRoleModal.value = true
}

const deleteUser = (user) => {
    if (confirm(`Apakah Anda yakin ingin menghapus pengguna "${user.name}"? Tindakan ini tidak dapat dibatalkan.`)) {
        router.delete(`/admin/users/${user.id}`, {
            onSuccess: () => {
                // Refresh data
                router.reload({ only: ['users'] })
            }
        })
    }
}

const closeCreateModal = () => {
    showCreateModal.value = false
    editingUser.value = null
    userForm.value = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: '',
        bio: '',
        image: null
    }
    errors.value = {}
}

const closeAllModals = () => {
    showCreateModal.value = false
    showRoleModal.value = false
    showBulkDeleteModal.value = false
}

const handleImageUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        userForm.value.image = file
    }
}

const saveUser = () => {
    if (saving.value) return
    
    saving.value = true
    errors.value = {}

    const formData = new FormData()
    Object.keys(userForm.value).forEach(key => {
        if (userForm.value[key] !== null && userForm.value[key] !== '') {
            formData.append(key, userForm.value[key])
        }
    })

    const url = editingUser.value 
        ? `/admin/users/${editingUser.value.id}`
        : '/admin/users'
    
    const method = editingUser.value ? 'patch' : 'post'

    router[method](url, formData, {
        onSuccess: () => {
            closeCreateModal()
            router.reload({ only: ['users'] })
        },
        onError: (errs) => {
            errors.value = errs
        },
        onFinish: () => {
            saving.value = false
        }
    })
}

const saveRole = () => {
    if (saving.value) return
    
    saving.value = true

    router.patch(`/admin/users/${selectedUser.value.id}/assignRole`, roleForm.value, {
        onSuccess: () => {
            showRoleModal.value = false
            router.reload({ only: ['users'] })
        },
        onFinish: () => {
            saving.value = false
        }
    })
}

// Watch for filter changes
watch(() => filters.value.search, () => {
    currentPage.value = 1
})

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
.user-row {
    transition: all 0.3s ease;
}

.user-row:hover {
    background-color: rgba(59, 130, 246, 0.05);
}

.user-avatar {
    position: relative;
}

.modal {
    background-color: rgba(0, 0, 0, 0.5);
}

.badge {
    font-size: 0.75em;
}

.page-link {
    border-radius: 0.375rem;
    margin: 0 2px;
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin: 0 1px;
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
