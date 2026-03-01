<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>
    <Head title="Configurações" />

    <AuthenticatedLayout>
        <div class="space-y-6 py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row gap-8 bg-white p-4 shadow-lg sm:rounded-lg sm:p-8 w-full items-start">
                
                <div class="flex-1 w-full">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </div>

                <div class="hidden md:block self-stretch w-px bg-emerald-200"></div>
                <hr class="md:hidden w-full border-emerald-200" />

                <div class="flex-1 w-full">
                    <UpdatePasswordForm />
                </div>

            </div>

            <div class="bg-white p-4 shadow-lg sm:rounded-lg sm:p-8">
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>