<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Object,
    filters: Object,
    can: Object,
});

const search = ref(props.filters?.search || '');
const orderBy = ref(props.filters?.order_by || 'name');
const orderDirection = ref(props.filters?.order_direction || 'asc');

const searchUsers = () => {
    router.get(route('users.index'), {
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
    searchUsers();
};

watch(search, () => {
    searchUsers();
});

const deleteUser = (user) => {
    if (confirm(`Are you sure you want to delete ${user.name}?`)) {
        router.delete(route('users.destroy', user.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Users Management
                </h2>
                <Link v-if="can.create" :href="route('users.create')">
                    <PrimaryButton>Add User</PrimaryButton>
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
                                placeholder="Search by name, email or company..."
                                class="w-full"
                            />
                        </div>

                        <div v-if="users.data.length === 0" class="text-center py-8 text-gray-500">
                            No users found.
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
                                            Role
                                        </th>
                                        <th @click="sortBy('company')" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 cursor-pointer hover:bg-gray-100">
                                            <div class="flex items-center gap-1">
                                                Company
                                                <span v-if="orderBy === 'company'">{{ orderDirection === 'asc' ? '↑' : '↓' }}</span>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <Link :href="route('users.show', user.id)" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                {{ user.name }}
                                            </Link>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            {{ user.email }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <span :class="[
                                                'px-2 py-1 text-xs font-semibold rounded-full',
                                                user.role === 'admin' 
                                                    ? 'bg-purple-100 text-purple-800' 
                                                    : 'bg-blue-100 text-blue-800'
                                            ]">
                                                {{ user.role }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            <span v-if="user.company">
                                                {{ user.company.name }}
                                            </span>
                                            <span v-else class="text-gray-400">N/A</span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium space-x-2">
                                            <Link :href="route('users.show', user.id)" class="text-indigo-600 hover:text-indigo-900">
                                                View
                                            </Link>
                                            <Link :href="route('users.edit', user.id)" class="text-blue-600 hover:text-blue-900">
                                                Edit
                                            </Link>
                                            <button 
                                                @click="deleteUser(user)" 
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <div v-if="users.links && users.links.length > 3" class="mt-6 border-t border-gray-200 pt-4">
                                <div class="flex items-center justify-between">
                                    <!-- Pagination Info -->
                                    <div class="text-sm text-gray-700">
                                        Showing 
                                        <span class="font-medium">{{ users.from || 0 }}</span>
                                        to 
                                        <span class="font-medium">{{ users.to || 0 }}</span>
                                        of 
                                        <span class="font-medium">{{ users.total || 0 }}</span>
                                        results
                                    </div>
                                    
                                    <!-- Pagination Links -->
                                    <div class="flex space-x-1">
                                        <component
                                            :is="link.url ? Link : 'span'"
                                            v-for="(link, index) in users.links"
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

