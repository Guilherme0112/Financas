<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import ManageSubscription from './Partials/ManageSubscription.vue';

const props = defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
    subscription?: any;
    faturas?: any[];
    planos: any
}>();
</script>

<template>
    <Head title="Configurações" />

    <AuthenticatedLayout>
        <div class="space-y-6 py-5 max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row gap-8 bg-white shadow-lg sm:rounded-lg sm:p-8 w-full items-start">
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
                <ManageSubscription :subscription="$page.props.auth.user.assinatura" :faturas="faturas ?? []" :planos="props.planos" />
            </div>

            <div class="bg-white p-4 shadow-lg sm:rounded-lg sm:p-8">
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>