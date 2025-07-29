<template>
    <AdminLayout>
        <!-- Header -->
        <div class="page-header mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="fw-bold mb-2">Users Management</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <Link href="/dashboard" class="text-decoration-none">
                                    <i class="fa fa-home me-1"></i>Dashboard
                                </Link>
                            </li>
                            <li class="breadcrumb-item">Users</li>
                            <li class="breadcrumb-item active">Manage</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Management Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="fw-bold mb-0">
                    <i class="fa fa-users text-primary me-2"></i>Manage Users
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Count News</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th style="width: 5%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="allUsers.length === 0">
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa fa-users fa-3x mb-3 opacity-25"></i>
                                    <div>No users found</div>
                                </td>
                            </tr>
                            <tr v-else v-for="(user, index) in allUsers" :key="user.id" class="user-row">
                                <td>{{ index + 1 }}</td>
                                <td>{{ user.id }}</td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <img 
                                            :src="user.image ? `/storage/images/${user.image}` : '/img/default.jpeg'"
                                            :alt="user.name"
                                            class="rounded-circle me-2"
                                            style="width: 30px; height: 30px; object-fit: cover;">
                                        <span>{{ user.name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ user.news_count || 0 }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                        <span v-for="role in user.roles" :key="role.id" 
                                              class="badge"
                                              :class="getRoleBadgeClass(role.name)">
                                            {{ role.name }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <small>{{ formatDate(user.created_at) }}</small>
                                </td>
                                <td>
                                    <span v-if="user.is_online" class="badge bg-success">Online</span>
                                    <span v-else class="badge bg-danger">{{ user.last_seen_human }}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <!-- Assign Role Button -->
                                        <button 
                                            @click="openRoleModal(user)"
                                            class="btn btn-link btn-primary btn-lg"
                                            data-bs-toggle="tooltip"
                                            title="Assign Role">
                                            <i class="fa fa-address-card"></i>
                                        </button>

                                        <!-- Delete Button -->
                                        <button 
                                            @click="deleteUser(user)"
                                            class="btn btn-link btn-danger"
                                            data-bs-toggle="tooltip"
                                            title="Delete User"
                                            :disabled="user.id === currentUser?.id">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Count News</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Role Assignment Modal -->
        <div class="modal fade" :class="{ show: showRoleModal }" tabindex="-1" style="display: block;" v-if="showRoleModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">
                            <span class="fw-light">Assign </span>
                            <span class="fw-medium">Role</span>
                        </h5>
                        <button type="button" class="btn-close" @click="closeRoleModal"></button>
                    </div>
                    <form @submit.prevent="assignRole">
                        <div class="modal-body">
                            <p class="small text-muted">Assign role to user: <strong>{{ selectedUser?.name }}</strong></p>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Current Role:</label>
                                        <div class="mb-2">
                                            <span v-for="role in selectedUser?.roles" :key="role.id" 
                                                  class="badge bg-primary me-1">
                                                {{ role.name }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Select New Role *</label>
                                        <select v-model="roleForm.role" class="form-select" required>
                                            <option value="">Choose Role</option>
                                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                                {{ role.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="submit" class="btn btn-primary" :disabled="saving">
                                <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                                Assign
                            </button>
                            <button type="button" class="btn btn-secondary" @click="closeRoleModal">
                                Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Backdrop -->
        <div v-if="showRoleModal" class="modal-backdrop fade show" @click="closeRoleModal"></div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
const props = defineProps({
    allUsers: {
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
const showRoleModal = ref(false)
const selectedUser = ref(null)
const saving = ref(false)

const roleForm = ref({
    role: ''
})

// Methods
const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getRoleBadgeClass = (roleName) => {
    const classes = {
        'Super Admin': 'bg-danger',
        'Editor': 'bg-warning text-dark',
        'Writer': 'bg-info',
        'Reader': 'bg-secondary'
    }
    return classes[roleName] || 'bg-secondary'
}

const openRoleModal = (user) => {
    selectedUser.value = user
    roleForm.value.role = user.roles[0]?.id || ''
    showRoleModal.value = true
}

const closeRoleModal = () => {
    showRoleModal.value = false
    selectedUser.value = null
    roleForm.value.role = ''
}

const assignRole = () => {
    if (saving.value) return
    
    saving.value = true

    router.patch(`/admin/users/${selectedUser.value.id}/assignRole`, roleForm.value, {
        onSuccess: () => {
            closeRoleModal()
            router.reload({ only: ['allUsers'] })
        },
        onError: (errors) => {
            console.error('Error assigning role:', errors)
        },
        onFinish: () => {
            saving.value = false
        }
    })
}

const deleteUser = (user) => {
    if (user.id === props.currentUser?.id) {
        alert('You cannot delete your own account!')
        return
    }

    if (confirm(`Are you sure you want to delete user "${user.name}"? This action cannot be undone.`)) {
        router.delete(`/admin/users/${user.id}`, {
            onSuccess: () => {
                router.reload({ only: ['allUsers'] })
            },
            onError: (errors) => {
                console.error('Error deleting user:', errors)
            }
        })
    }
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
.user-row {
    transition: all 0.3s ease;
}

.user-row:hover {
    background-color: rgba(59, 130, 246, 0.05);
}

.modal {
    background-color: rgba(0, 0, 0, 0.5);
}

.btn-link {
    text-decoration: none !important;
}

.badge {
    font-size: 0.75em;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .d-flex.gap-1 {
        flex-direction: column;
        gap: 0.25rem !important;
    }
}
</style>
