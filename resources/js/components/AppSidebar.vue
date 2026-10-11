<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BarChart3,
    BookOpen,
    FolderGit2,
    GraduationCap,
    LayoutGrid,
    Megaphone,
    School,
    ScrollText,
    ShieldCheck,
    Star,
    Users,
    ClipboardList,
    FileText,
    History,
    Key,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { usePermission } from '@/composables/usePermission';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';
import { computed } from 'vue';

const { can } = usePermission();

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    if (can('dashboard.view')) {
        items.push({
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        });
    }

    if (can('student.view')) {
        items.push({
            title: 'Data Siswa',
            href: '/students',
            icon: GraduationCap,
        });
    }

    if (can('student.create') || can('user.view')) {
        items.push({
            title: 'Manajemen Kelas',
            href: '/classes',
            icon: School,
        });
    }

    if (can('counseling.view')) {
        items.push({
            title: 'Konseling',
            href: '/counseling/requests',
            icon: ScrollText,
            children: [
                { title: 'Permohonan', href: '/counseling/requests', icon: ClipboardList },
                { title: 'Sesi Konseling', href: '/counseling/sessions', icon: BookOpen },
            ],
        });
    }

    if (can('violation.view')) {
        items.push({
            title: 'Pelanggaran',
            href: '/violations',
            icon: Megaphone,
        });
    }

    if (can('achievement.view')) {
        items.push({
            title: 'Prestasi',
            href: '/achievements',
            icon: Star,
        });
    }

    if (can('report.view')) {
        items.push({
            title: 'Laporan',
            href: '/reports',
            icon: FileText,
        });
    }

    // Administration group
    const adminItems: NavItem[] = [];

    if (can('user.view')) {
        adminItems.push({ title: 'Pengguna', href: '/users', icon: Users });
    }
    if (can('role.view')) {
        adminItems.push({ title: 'Peran & Hak Akses', href: '/roles', icon: ShieldCheck });
    }
    if (can('audit_log.view')) {
        adminItems.push({ title: 'Audit Log', href: '/audit-logs', icon: History });
    }

    if (adminItems.length > 0) {
        items.push({
            title: 'Administrasi',
            href: adminItems[0]?.href ?? '/roles',
            icon: Key,
            children: adminItems,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'SMPN 1 Website',
        href: '#',
        icon: School,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
