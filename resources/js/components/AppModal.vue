<script setup lang="ts">
import { watch, onMounted, onUnmounted, computed } from 'vue';
import { X } from '@lucide/vue';

const props = withDefaults(
    defineProps<{
        show: boolean;
        title?: string;
        description?: string;
        maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl';
        closeOnOverlay?: boolean;
        closeOnEsc?: boolean;
    }>(),
    {
        maxWidth: 'lg',
        closeOnOverlay: true,
        closeOnEsc: true,
    }
);

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const maxWidthClass = computed(() => {
    switch (props.maxWidth) {
        case 'sm':
            return 'max-w-sm';
        case 'md':
            return 'max-w-md';
        case 'lg':
            return 'max-w-lg';
        case 'xl':
            return 'max-w-xl';
        case '2xl':
            return 'max-w-2xl';
        case '3xl':
            return 'max-w-3xl';
        case '4xl':
            return 'max-w-4xl';
        default:
            return 'max-w-lg';
    }
});

const handleKeydown = (event: KeyboardEvent) => {
    if (props.show && props.closeOnEsc && event.key === 'Escape') {
        emit('close');
    }
};

const handleBackdropClick = (event: MouseEvent) => {
    if (props.closeOnOverlay && event.target === event.currentTarget) {
        emit('close');
    }
};

watch(
    () => props.show,
    (isOpen) => {
        if (typeof document !== 'undefined') {
            if (isOpen) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
    }
);

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
    if (typeof document !== 'undefined') {
        document.body.style.overflow = '';
    }
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto"
                @click="handleBackdropClick"
            >
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-95 translate-y-2"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-2"
                >
                    <div
                        v-if="show"
                        :class="[
                            'relative w-full rounded-3xl border border-border/80 bg-white dark:bg-card p-6 sm:p-7 shadow-2xl max-h-[90vh] overflow-y-auto my-auto',
                            maxWidthClass
                        ]"
                        @click.stop
                    >
                        <!-- Close button -->
                        <button
                            type="button"
                            @click="emit('close')"
                            class="absolute top-5 right-5 rounded-full p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors cursor-pointer"
                            aria-label="Tutup"
                            title="Tutup (Esc)"
                        >
                            <X class="size-5" />
                        </button>

                        <!-- Header slot or title/description -->
                        <div v-if="title || $slots.header" class="mb-5 pr-8">
                            <slot name="header">
                                <h2 class="text-xl font-extrabold text-foreground tracking-tight">{{ title }}</h2>
                                <p v-if="description" class="text-sm text-muted-foreground mt-1">{{ description }}</p>
                            </slot>
                        </div>

                        <!-- Modal body -->
                        <div>
                            <slot />
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
