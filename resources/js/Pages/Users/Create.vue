<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { watch } from 'vue';

const props = defineProps({
    companies: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'company',
    company_id: props.companies.length > 0 ? props.companies[0].id : '',
});

// Watch role changes to clear company_id if admin is selected
watch(() => form.role, (newRole) => {
    if (newRole === 'admin') {
        form.company_id = null;
    } else if (!form.company_id && props.companies.length > 0) {
        form.company_id = props.companies[0].id;
    }
});

const submit = () => {
    form.post(route('users.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Create User" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create User
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="password" value="Password" />
                                <TextInput
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="password_confirmation" value="Confirm Password" />
                                <TextInput
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.password_confirmation" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="role" value="Role" />
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="company">Company User</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <InputError :message="form.errors.role" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-600">
                                    Admins have full access. Company users can only manage their company.
                                </p>
                            </div>

                            <div v-if="form.role === 'company'">
                                <InputLabel for="company_id" value="Company" />
                                <select
                                    id="company_id"
                                    v-model="form.company_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select a company</option>
                                    <option v-for="company in companies" :key="company.id" :value="company.id">
                                        {{ company.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.company_id" class="mt-2" />
                            </div>

                            <div v-else class="p-4 bg-purple-50 rounded-md">
                                <p class="text-sm text-purple-800">
                                    <strong>Note:</strong> Admin users don't need to be assigned to a company.
                                </p>
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    Create User
                                </PrimaryButton>
                                <a :href="route('users.index')">
                                    <SecondaryButton type="button">
                                        Cancel
                                    </SecondaryButton>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

