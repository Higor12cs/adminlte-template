<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import NavItem from "@/Components/NavItem.vue";
import NavItemCollapsible from "@/Components/NavItemCollapsible.vue";
import { toast } from "vue3-toastify";

const appName = import.meta.env.VITE_APP_NAME || "";
const page = usePage();
const darkMode = ref(false);

// Toasts
const showFlashMessages = () => {
    const { flash, success, error, info, warning } = usePage().props;

    [flash?.success, success].forEach((msg) => msg && toast.success(msg));
    [flash?.error, error].forEach((msg) => msg && toast.error(msg));
    [flash?.info, info].forEach((msg) => msg && toast.info(msg));
    [flash?.warning, warning].forEach((msg) => msg && toast.warning(msg));
};

// Sidebar
const sidebarItems = [
    // Início
    {
        type: "header",
        label: "Início",
        items: [
            {
                type: "link",
                routeName: "home.index",
                iconClass: "fas fa-home",
                label: "Home",
            },
        ],
    },

    // Configurações
    {
        type: "header",
        label: "Configurações",
        items: [
            {
                type: "link",
                routeName: "users.index",
                iconClass: "fas fa-users",
                label: "Usuários",
                permission: "users.index",
            },
            {
                type: "link",
                routeName: "roles.index",
                iconClass: "fas fa-user-lock",
                label: "Papéis",
                permission: "roles.index",
            },
            {
                type: "collapsible",
                iconClass: "fas fa-circle",
                label: "Teste",
                subItems: [
                    {
                        type: "link",
                        routeName: "home.index",
                        iconClass: "fas fa-circle",
                        label: "Teste 1",
                        permission: "home.index",
                    },
                    {
                        type: "link",
                        routeName: "home.index",
                        iconClass: "fas fa-circle",
                        label: "Teste 2",
                        permission: "home.index",
                    },
                ],
            },
        ],
    },
];

// Permissions
const hasPermission = (permission, page) => {
    if (!permission) return true;

    const userRoles = page.props.auth.roles || [];
    const userPermissions = page.props.auth.permissions || [];

    if (userRoles.includes("Administrador")) return true;

    return userPermissions.includes(permission);
};

const checkPermission = (permission) => hasPermission(permission, page);

const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("darkMode", darkMode.value ? "true" : "false");
};

const visibleMenuItems = computed(() => {
    return sidebarItems.filter((section) => {
        const visibleItems = section.items.filter((item) => {
            if (item.type === "link") {
                return !item.permission || checkPermission(item.permission);
            } else if (item.type === "collapsible") {
                const filteredSubItems = item.subItems.filter(
                    (subItem) =>
                        !subItem.permission ||
                        checkPermission(subItem.permission)
                );

                if (filteredSubItems.length === 0) return false;

                item.filteredSubItems = filteredSubItems;
                return !item.permission || checkPermission(item.permission);
            }
            return false;
        });

        section.visibleItems = visibleItems;
        return visibleItems.length > 0;
    });
});

onMounted(() => {
    showFlashMessages();
    const savedDarkMode = localStorage.getItem("darkMode");

    if (savedDarkMode === "true") {
        darkMode.value = true;
        document.body.classList.add("dark-mode");
    }
});

watch(
    () => page.props,
    (newProps) => {
        showFlashMessages();
    },
    { deep: true }
);
</script>

<template>
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a
                        class="nav-link"
                        data-widget="pushmenu"
                        href="#"
                        role="button"
                    >
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#"
                        @click.prevent="toggleDarkMode"
                        role="button"
                    >
                        <i
                            class="fas"
                            :class="darkMode ? 'fa-sun' : 'fa-moon'"
                        ></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        {{ $page.props.auth.user.name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="dropdown-item"
                        >
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Sair
                        </Link>
                    </div>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-2">
            <Link
                :href="route('home.index')"
                class="brand-link d-flex justify-content-center"
            >
                <span class="brand-text font-weight-semibold">
                    {{ appName }}
                </span>
            </Link>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul
                        class="nav nav-pills nav-sidebar flex-column"
                        data-widget="treeview"
                        role="menu"
                        data-accordion="false"
                    >
                        <template
                            v-for="(section, sectionIndex) in visibleMenuItems"
                            :key="sectionIndex"
                        >
                            <!-- Header -->
                            <li class="nav-header">{{ section.label }}</li>

                            <!-- Menu Items -->
                            <template
                                v-for="(
                                    item, itemIndex
                                ) in section.visibleItems"
                                :key="`${sectionIndex}-${itemIndex}`"
                            >
                                <!-- Regular Link -->
                                <NavItem
                                    v-if="item.type === 'link'"
                                    :route-name="item.routeName"
                                    :icon-class="item.iconClass"
                                    :label="item.label"
                                />

                                <!-- Collapsible Menu -->
                                <NavItemCollapsible
                                    v-else-if="item.type === 'collapsible'"
                                    :icon-class="item.iconClass"
                                    :label="item.label"
                                    :sub-items="item.filteredSubItems"
                                />
                            </template>
                        </template>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid py-3">
                    <slot />
                </div>
            </section>
        </div>
    </div>
</template>
