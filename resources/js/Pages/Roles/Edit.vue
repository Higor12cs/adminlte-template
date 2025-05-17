<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import RoleForm from "@/Pages/Roles/RoleForm.vue";
import { ref, onMounted } from "vue";

const props = defineProps({
    role: Object,
    permissions: Array,
    rolePermissions: Array,
});

const formRef = ref(null);

const handleSubmit = (form) => {
    form.post(route("roles.update", props.role.id));
};
</script>

<template>
    <Head title="Editar Papel" />
    <AppLayout>
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h4>Editar Papel</h4>
                <Breadcrumb
                    :breadcrumb="[
                        { label: 'Home', routeName: 'home.index' },
                        { label: 'Permissões', routeName: 'roles.index' },
                        { label: 'Editar' },
                    ]"
                />
            </div>
            <Link
                :href="route('roles.index')"
                class="btn btn-secondary mb-auto"
            >
                <i class="fas fa-sm fa-arrow-left"></i>
                &nbsp; Voltar
            </Link>
        </div>
        <div class="card">
            <div class="card-header">Editar Papel</div>
            <div class="card-body">
                <RoleForm
                    ref="formRef"
                    :role="role"
                    :permissions="permissions"
                    :rolePermissions="rolePermissions"
                    :processing="formRef?.form?.processing"
                    @submit="handleSubmit"
                />
            </div>
        </div>
    </AppLayout>
</template>
