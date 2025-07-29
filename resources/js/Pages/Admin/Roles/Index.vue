<template>
    <AdminLayout>
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0">Role Management</h1>
                                <p class="text-muted">Kelola role dan permission pengguna</p>
                            </div>
                            <button @click="showCreateModal = true" class="btn btn-primary">
                                <i class="fa fa-plus me-2"></i>
                                Tambah Role
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Roles Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Daftar Role</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Role Name</th>
                                                <th>Permissions</th>
                                                <th>Users Count</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="role in roles" :key="role.id">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa fa-shield-alt text-primary me-2"></i>
                                                        <span class="fw-semibold">{{ role.name }}</span>
                                                        <span v-if="['Super Admin', 'Editor', 'Writer'].includes(role.name)" 
                                                              class="badge bg-info ms-2">Default</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <span v-for="permission in role.permissions" 
                                                              :key="permission.id"
                                                              class="badge bg-secondary">
                                                            {{ permission.name }}
                                                        </span>
                                                        <span v-if="role.permissions.length === 0" class="text-muted">
                                                            No permissions
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        {{ role.users_count || 0 }} users
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button @click="editRole(role)" class="btn btn-outline-primary">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button v-if="!['Super Admin', 'Editor', 'Writer'].includes(role.name)"
                                                                @click="deleteRole(role)" 
                                                                class="btn btn-outline-danger">
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Role Modal -->
        <div class="modal fade" :class="{ show: showCreateModal || showEditModal }" 
             :style="{ display: showCreateModal || showEditModal ? 'block' : 'none' }"
             tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ showEditModal ? 'Edit Role' : 'Tambah Role Baru' }}
                        </h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <form @submit.prevent="saveRole">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Role</label>
                                <input v-model="roleForm.name" type="text" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Permissions</label>
                                <div class="row">
                                    <div v-for="permission in permissions" :key="permission.id" class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input v-model="roleForm.permissions" 
                                                   :value="permission.name"
                                                   type="checkbox" 
                                                   class="form-check-input" 
                                                   :id="`permission-${permission.id}`">
                                            <label class="form-check-label" :for="`permission-${permission.id}`">
                                                {{ permission.name }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                {{ showEditModal ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Backdrop -->
        <div v-if="showCreateModal || showEditModal" class="modal-backdrop fade show"></div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
const props = defineProps({
    roles: Array,
    permissions: Array
})

// Reactive data
const showCreateModal = ref(false)
const showEditModal = ref(false)
const editingRole = ref(null)

const roleForm = reactive({
    name: '',
    permissions: []
})

// Methods
const editRole = (role) => {
    editingRole.value = role
    roleForm.name = role.name
    roleForm.permissions = role.permissions.map(p => p.name)
    showEditModal.value = true
}

const deleteRole = (role) => {
    if (confirm(`Yakin ingin menghapus role "${role.name}"?`)) {
        router.delete(`/admin/roles/${role.id}`)
    }
}

const saveRole = () => {
    if (showEditModal.value) {
        // Update role
        router.put(`/admin/roles/${editingRole.value.id}`, roleForm)
    } else {
        // Create role
        router.post('/admin/roles', roleForm)
    }
    closeModal()
}

const closeModal = () => {
    showCreateModal.value = false
    showEditModal.value = false
    editingRole.value = null
    roleForm.name = ''
    roleForm.permissions = []
}
</script>

<style scoped>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
}

.modal.show {
    background-color: rgba(0, 0, 0, 0.5);
}
</style>
