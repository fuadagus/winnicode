<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title text-dark fw-bold">
                            <i class="fa fa-tags text-primary me-2"></i>
                            Kelola Kategori
                        </h2>
                        <p class="text-muted mb-0">Manajemen kategori berita</p>
                    </div>
                    <div class="col-auto">
                        <button @click="showCreateModal = true" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>
                            Tambah Kategori
                        </button>
                    </div>
                </div>
            </div>

            <!-- Categories Grid -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">Daftar Kategori</h5>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group">
                                        <input v-model="searchQuery" 
                                               type="text" 
                                               class="form-control" 
                                               placeholder="Cari kategori...">
                                        <span class="input-group-text">
                                            <i class="fa fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-4 py-3">No</th>
                                            <th class="px-4 py-3">Nama Kategori</th>
                                            <th class="px-4 py-3 text-center">Jumlah Berita</th>
                                            <th class="px-4 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(category, index) in filteredCategories" 
                                            :key="category.id">
                                            <td class="px-4 py-3">{{ index + 1 }}</td>
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="category-icon me-3">
                                                        <i class="fa fa-tag text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-medium">{{ category.name }}</h6>
                                                        <small class="text-muted">ID: {{ category.id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ category.news_count }} berita
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="btn-group" role="group">
                                                    <a :href="`/admin/categories/${category.id}/view`" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Lihat Detail">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <button @click="editCategory(category)" 
                                                            class="btn btn-sm btn-outline-warning"
                                                            title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button @click="deleteCategory(category)" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Hapus"
                                                            :disabled="category.news_count > 0">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="filteredCategories.length === 0">
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="fa fa-tags fa-3x mb-3 opacity-25"></i>
                                                <p class="mb-0">Tidak ada kategori yang ditemukan</p>
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

        <!-- Create Modal -->
        <div v-if="showCreateModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form @submit.prevent="createCategory">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Kategori Baru</h5>
                            <button @click="showCreateModal = false" type="button" class="btn-close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Kategori *</label>
                                <input v-model="createForm.name" 
                                       type="text" 
                                       class="form-control" 
                                       :class="{ 'is-invalid': createForm.errors.name }"
                                       placeholder="Masukkan nama kategori"
                                       required>
                                <div v-if="createForm.errors.name" class="invalid-feedback">
                                    {{ createForm.errors.name }}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button @click="showCreateModal = false" type="button" class="btn btn-secondary">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="createForm.processing">
                                <span v-if="createForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form @submit.prevent="updateCategory">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Kategori</h5>
                            <button @click="showEditModal = false" type="button" class="btn-close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Kategori *</label>
                                <input v-model="editForm.name" 
                                       type="text" 
                                       class="form-control" 
                                       :class="{ 'is-invalid': editForm.errors.name }"
                                       placeholder="Masukkan nama kategori"
                                       required>
                                <div v-if="editForm.errors.name" class="invalid-feedback">
                                    {{ editForm.errors.name }}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button @click="showEditModal = false" type="button" class="btn btn-secondary">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="editForm.processing">
                                <span v-if="editForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
const props = defineProps({
    categories: Array
})

// State
const searchQuery = ref('')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const selectedCategory = ref(null)

// Forms
const createForm = useForm({
    name: ''
})

const editForm = useForm({
    id: null,
    name: ''
})

// Computed
const filteredCategories = computed(() => {
    if (!searchQuery.value) return props.categories
    
    return props.categories.filter(category =>
        category.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})

// Methods
const createCategory = () => {
    createForm.post(route('admin.category.store'), {
        onSuccess: () => {
            showCreateModal.value = false
            createForm.reset()
        }
    })
}

const editCategory = (category) => {
    selectedCategory.value = category
    editForm.id = category.id
    editForm.name = category.name
    showEditModal.value = true
}

const updateCategory = () => {
    editForm.put(route('admin.category.update', editForm.id), {
        onSuccess: () => {
            showEditModal.value = false
            editForm.reset()
            selectedCategory.value = null
        }
    })
}

const deleteCategory = (category) => {
    if (category.news_count > 0) {
        alert('Tidak dapat menghapus kategori yang masih memiliki berita!')
        return
    }
    
    if (confirm(`Apakah Anda yakin ingin menghapus kategori "${category.name}"?`)) {
        router.delete(route('admin.category.destroy', category.id))
    }
}
</script>

<style scoped>
.page-header {
    padding: 1.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.page-title {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
}

.card {
    border-radius: 12px;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e9ecef;
}

.category-icon {
    width: 40px;
    height: 40px;
    background: rgba(13, 110, 253, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-group .btn {
    border-radius: 6px;
    margin: 0 2px;
}

.modal.show {
    animation: fadeIn 0.15s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.badge {
    font-size: 0.75rem;
}
</style>
