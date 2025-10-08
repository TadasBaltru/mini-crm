<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    employees: Object,
    can: Object,
});

const deleteEmployee = (employee) => {
    if (confirm(`Are you sure you want to delete ${employee.full_name}?`)) {
        router.delete(route('employees.destroy', employee.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Employees" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Employees
                </h2>
                <Link v-if="can.create" :href="route('employees.create')">
                    <PrimaryButton>Add Employee</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="employees.data.length === 0" class="text-center py-8 text-gray-500">
                            No employees found.
                        </div>
                        
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Name
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Phone
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Company
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="employee in employees.data" :key="employee.id" class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <Link :href="route('employees.show', employee.id)" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                {{ employee.full_name }}
                                            </Link>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ employee.email }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ employee.phone }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            <span v-if="employee.company">
                                                {{ employee.company.name }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium space-x-2">
                                            <Link :href="route('employees.show', employee.id)" class="text-indigo-600 hover:text-indigo-900">
                                                View
                                            </Link>
                                            <Link :href="route('employees.edit', employee.id)" class="text-blue-600 hover:text-blue-900">
                                                Edit
                                            </Link>
                                            <button 
                                                @click="deleteEmployee(employee)" 
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <div v-if="employees.links.length > 3" class="mt-4 flex justify-center space-x-1">
                                <Link
                                    v-for="(link, index) in employees.links"
                                    :key="index"
                                    :href="link.url"
                                    :class="[
                                        'px-4 py-2 text-sm border rounded',
                                        link.active
                                            ? 'bg-indigo-600 text-white border-indigo-600'
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    v-html="link.label"
                                    :disabled="!link.url"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

