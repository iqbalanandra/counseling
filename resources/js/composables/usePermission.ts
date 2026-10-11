import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export interface AuthUser {
    id: number;
    name: string;
    email: string;
    phone?: string;
    identifier_number?: string;
    status: string;
    is_system_user: boolean;
    avatar_url?: string;
    roles: string[];
    permissions: string[];
    student?: {
        id: number;
        nis: string;
        class_id?: number;
        class_name?: string;
        total_violation_points: number;
        total_achievement_points: number;
    };
    teacher?: {
        id: number;
        nip?: string;
        position?: string;
        homeroom_class?: {
            id: number;
            name: string;
        };
    };
}

export function usePermission() {
    const page = usePage<{
        auth: {
            user: AuthUser | null;
        };
    }>();

    const user = computed(() => page.props.auth?.user || null);

    const isSuperAdmin = computed(() => {
        if (!user.value) return false;
        return (
            user.value.is_system_user || user.value.roles.includes('Superadmin')
        );
    });

    const can = (permission: string): boolean => {
        if (!user.value) return false;
        if (user.value.is_system_user) return true;
        if (user.value.permissions?.includes('*')) return true;
        return user.value.permissions?.includes(permission) || false;
    };

    const hasRole = (role: string | string[]): boolean => {
        if (!user.value) return false;
        if (user.value.is_system_user) return true;
        const roles = Array.isArray(role) ? role : [role];
        return roles.some((r) => user.value?.roles?.includes(r));
    };

    return {
        user,
        isSuperAdmin,
        can,
        hasRole,
    };
}
