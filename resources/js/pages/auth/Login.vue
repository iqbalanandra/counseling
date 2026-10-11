<script setup lang="ts">
import { ref } from 'vue';
import { Form, Head, Link } from '@inertiajs/vue3';
import { HeartHandshake, ShieldCheck } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Masuk ke Konseling-Yu',
        description: 'Masukkan email dan kata sandi untuk mengakses portal BK',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const emailValue = ref('');
const passwordValue = ref('');

const demoRoles = [
    { label: 'Siswa', email: 'siswa@example.com' },
    { label: 'Guru BK', email: 'bk@example.com' },
    { label: 'Wali Kelas', email: 'walikelas@example.com' },
    { label: 'Kepala Sekolah', email: 'kepala@example.com' },
    { label: 'Superadmin', email: 'superadmin@example.com' },
];

const fillCredentials = (email: string) => {
    emailValue.value = email;
    passwordValue.value = 'password';
};
</script>

<template>
    <Head title="Masuk — Konseling-Yu" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <!-- Quick Demo Accounts Helper -->
    <div
        class="mb-6 rounded-2xl border border-indigo-200/70 bg-indigo-50/60 p-3.5 text-xs dark:border-indigo-900/40 dark:bg-indigo-950/30"
    >
        <p
            class="mb-2 flex items-center gap-1.5 font-semibold text-indigo-900 dark:text-indigo-200"
        >
            <ShieldCheck class="size-4 text-indigo-600 dark:text-indigo-400" />
            Pilih Akun Demo Cepat (1-Klik Isi):
        </p>
        <div class="flex flex-wrap gap-1.5">
            <button
                v-for="role in demoRoles"
                :key="role.label"
                type="button"
                @click="fillCredentials(role.email)"
                class="rounded-lg border border-indigo-200 bg-white px-2.5 py-1 font-medium text-foreground shadow-2xs transition-colors hover:bg-indigo-600 hover:text-white dark:border-indigo-800/80 dark:bg-card dark:hover:bg-indigo-600"
            >
                {{ role.label }}
            </button>
        </div>
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">Alamat Email</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    v-model="emailValue"
                    required
                    v-focus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Kata Sandi</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs"
                        :tabindex="5"
                    >
                        Lupa kata sandi?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    v-model="passwordValue"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Masukkan sandi..."
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label
                    for="remember"
                    class="flex cursor-pointer items-center space-x-2 text-xs select-none"
                >
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>Ingat saya</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-2 w-full bg-indigo-600 font-bold hover:bg-indigo-700"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Masuk ke Portal
            </Button>
        </div>

        <div
            class="border-t border-border pt-2 text-center text-xs text-muted-foreground"
        >
            Kembali ke
            <Link href="/" class="font-medium text-indigo-600 hover:underline"
                >Halaman Utama</Link
            >
        </div>
    </Form>
</template>
