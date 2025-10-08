<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    companies: Object,
    filters: Object,
    can: Object,
});

const search = ref(props.filters?.search || '');
const orderBy = ref(props.filters?.order_by || 'name');
const orderDirection = ref(props.filters?.order_direction || 'asc');

const searchCompanies = () => {
    router.get(route('companies.index'), {
        search: search.value,
        order_by: orderBy.value,
        order_direction: orderDirection.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const sortBy = (field) => {
    if (orderBy.value === field) {
        orderDirection.value = orderDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        orderBy.value = field;
        orderDirection.value = 'asc';
    }
    searchCompanies();
};

watch(search, () => {
    searchCompanies();
});

const deleteCompany = (company) => {
    if (confirm(`Are you sure you want to delete ${company.name}?`)) {
        router.delete(route('companies.destroy', company.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Companies" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Companies
                </h2>
                <Link v-if="can.create" :href="route('companies.create')">
                    <PrimaryButton>Add Company</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Search Bar -->
                        <div class="mb-4">
                            <TextInput
                                v-model="search"
                                type="text"
                                placeholder="Search by name or email..."
                                class="w-full"
                            />
                        </div>

                        <div v-if="companies.data.length === 0" class="text-center py-8 text-gray-500">
                            No companies found.
                        </div>
                        
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th @click="sortBy('name')" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 cursor-pointer hover:bg-gray-100">
                                            <div class="flex items-center gap-1">
                                                Name
                                                <span v-if="orderBy === 'name'">{{ orderDirection === 'asc' ? '↑' : '↓' }}</span>
                                            </div>
                                        </th>
                                        <th @click="sortBy('email')" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 cursor-pointer hover:bg-gray-100">
                                            <div class="flex items-center gap-1">
                                                Email
                                                <span v-if="orderBy === 'email'">{{ orderDirection === 'asc' ? '↑' : '↓' }}</span>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Website
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Employees
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="company in companies.data" :key="company.id" class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <Link :href="route('companies.show', company.id)" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                {{ company.name }}
                                            </Link>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ company.email }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            <a :href="company.website" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                {{ company.website }}
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ company.employees_count || 0 }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium space-x-2">
                                            <Link :href="route('companies.show', company.id)" class="text-indigo-600 hover:text-indigo-900">
                                                View
                                            </Link>
                                            <Link :href="route('companies.edit', company.id)" class="text-blue-600 hover:text-blue-900">
                                                Edit
                                            </Link>
                                            <button 
                                                v-if="can.create"
                                                @click="deleteCompany(company)" 
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <div v-if="companies.links && companies.links.length > 3" class="mt-6 border-t border-gray-200 pt-4">
                                <div class="flex items-center justify-between">
                                    <!-- Pagination Info -->
                                    <div class="text-sm text-gray-700">
                                        Showing 
                                        <span class="font-medium">{{ companies.from || 0 }}</span>
                                        to 
                                        <span class="font-medium">{{ companies.to || 0 }}</span>
                                        of 
                                        <span class="font-medium">{{ companies.total || 0 }}</span>
                                        results
                                    </div>
                                    
                                    <!-- Pagination Links -->
                                    <div class="flex space-x-1">
                                        <component
                                            :is="link.url ? Link : 'span'"
                                            v-for="(link, index) in companies.links"
                                            :key="index"
                                            :href="link.url"
                                            preserve-scroll
                                            preserve-state
                                            :class="[
                                                'px-3 py-2 text-sm border rounded-md transition-colors',
                                                link.active
                                                    ? 'bg-indigo-600 text-white border-indigo-600 font-semibold'
                                                    : 'bg-white text-gray-700 border-gray-300',
                                                link.url && !link.active ? 'hover:bg-gray-50 hover:border-gray-400' : '',
                                                !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                            ]"
                                        >
                                            <span v-html="link.label"></span>
                                        </component>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

