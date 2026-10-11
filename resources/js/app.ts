import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return undefined;
        }
    },
    withApp: (app) => {
        app.directive('focus', {
            mounted: (el: HTMLElement, shouldFocus) => {
                if (shouldFocus.value !== false) {
                    el.focus();
                }
            },
        });
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();

// Auto-open native picker when clicking anywhere on a date/time input field
if (typeof window !== 'undefined') {
    document.addEventListener('click', (e) => {
        const target = e.target as HTMLElement | null;
        if (target && target.tagName === 'INPUT') {
            const input = target as HTMLInputElement;
            if (
                ['date', 'datetime-local', 'time', 'month', 'week'].includes(
                    input.type,
                )
            ) {
                try {
                    input.showPicker?.();
                } catch {
                    // Ignore if picker already showing or not supported
                }
            }
        }
    });
}
