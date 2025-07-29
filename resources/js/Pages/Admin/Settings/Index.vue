<template>
    <AdminLayout>
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0">System Settings</h1>
                                <p class="text-muted">Kelola pengaturan dan informasi sistem</p>
                            </div>
                            <button @click="clearCache" class="btn btn-warning">
                                <i class="fa fa-broom me-2"></i>
                                Clear Cache
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- System Statistics -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">System Statistics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <div class="stat-card text-center p-3 bg-primary bg-opacity-10 rounded">
                                            <i class="fa fa-users fa-2x text-primary mb-2"></i>
                                            <h4 class="mb-0">{{ systemStats.total_users }}</h4>
                                            <small class="text-muted">Total Users</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="stat-card text-center p-3 bg-success bg-opacity-10 rounded">
                                            <i class="fa fa-user-clock fa-2x text-success mb-2"></i>
                                            <h4 class="mb-0">{{ systemStats.active_users }}</h4>
                                            <small class="text-muted">Active Users</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="stat-card text-center p-3 bg-info bg-opacity-10 rounded">
                                            <i class="fa fa-newspaper fa-2x text-info mb-2"></i>
                                            <h4 class="mb-0">{{ systemStats.total_news }}</h4>
                                            <small class="text-muted">Total News</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="stat-card text-center p-3 bg-warning bg-opacity-10 rounded">
                                            <i class="fa fa-tags fa-2x text-warning mb-2"></i>
                                            <h4 class="mb-0">{{ systemStats.total_categories }}</h4>
                                            <small class="text-muted">Categories</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-file-alt me-2 text-primary"></i>
                                            <div>
                                                <div class="fw-semibold">Published News</div>
                                                <div class="text-muted">{{ systemStats.published_news }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-edit me-2 text-secondary"></i>
                                            <div>
                                                <div class="fw-semibold">Draft News</div>
                                                <div class="text-muted">{{ systemStats.draft_news }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-hdd me-2 text-info"></i>
                                            <div>
                                                <div class="fw-semibold">Storage Usage</div>
                                                <div class="text-muted">{{ systemStats.storage_usage }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Settings Form -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Application Settings</h5>
                            </div>
                            <div class="card-body">
                                <form @submit.prevent="updateSettings">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Site Name</label>
                                            <input v-model="settingsForm.site_name" type="text" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Admin Email</label>
                                            <input v-model="settingsForm.admin_email" type="email" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Timezone</label>
                                            <select v-model="settingsForm.timezone" class="form-select">
                                                <option value="Asia/Jakarta">Asia/Jakarta</option>
                                                <option value="Asia/Makassar">Asia/Makassar</option>
                                                <option value="Asia/Jayapura">Asia/Jayapura</option>
                                                <option value="UTC">UTC</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save me-2"></i>
                                                Save Settings
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- System Information -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">System Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="info-item mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">PHP Version</span>
                                        <span class="fw-semibold">{{ systemStats.php_version }}</span>
                                    </div>
                                </div>
                                <div class="info-item mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Laravel Version</span>
                                        <span class="fw-semibold">{{ systemStats.laravel_version }}</span>
                                    </div>
                                </div>
                                <div class="info-item mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Environment</span>
                                        <span class="badge" :class="settings.environment === 'production' ? 'bg-success' : 'bg-warning'">
                                            {{ settings.environment }}
                                        </span>
                                    </div>
                                </div>
                                <div class="info-item mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Debug Mode</span>
                                        <span class="badge" :class="settings.debug_mode ? 'bg-danger' : 'bg-success'">
                                            {{ settings.debug_mode ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Site URL</span>
                                        <span class="fw-semibold text-truncate" style="max-width: 150px;">
                                            {{ settings.site_url }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button @click="clearCache" class="btn btn-outline-warning">
                                        <i class="fa fa-broom me-2"></i>
                                        Clear All Cache
                                    </button>
                                    <a href="/admin/dashboard" class="btn btn-outline-primary">
                                        <i class="fa fa-tachometer-alt me-2"></i>
                                        View Dashboard
                                    </a>
                                    <a href="/admin/users/manage" class="btn btn-outline-info">
                                        <i class="fa fa-users me-2"></i>
                                        Manage Users
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props
const props = defineProps({
    systemStats: Object,
    settings: Object
})

// Reactive form
const settingsForm = reactive({
    site_name: props.settings.site_name,
    admin_email: props.settings.admin_email,
    timezone: props.settings.timezone
})

// Methods
const updateSettings = () => {
    router.put('/admin/settings', settingsForm)
}

const clearCache = () => {
    if (confirm('Yakin ingin membersihkan semua cache?')) {
        router.post('/admin/settings/clear-cache')
    }
}
</script>

<style scoped>
.stat-card {
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.info-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
