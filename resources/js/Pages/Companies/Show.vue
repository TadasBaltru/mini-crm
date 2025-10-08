<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    company: Object,
    can: Object,
});
</script>

<template>
    <Head :title="company.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Company Details
                </h2>
                <div class="flex gap-2">
                    <Link v-if="can.update" :href="route('companies.edit', company.id)">
                        <PrimaryButton>Edit</PrimaryButton>
                    </Link>
                    <Link :href="route('companies.index')">
                        <SecondaryButton>Back to List</SecondaryButton>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ company.name }}</dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ company.email }}</dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Website</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a :href="company.website" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        {{ company.website }}
                                    </a>
                                </dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Number of Employees</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ company.employees_count || 0 }}</dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ company.created_at }}</dd>
                            </div>

                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Updated At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ company.updated_at }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

