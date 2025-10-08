<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    employee: Object,
    companies: Array,
});

const form = useForm({
    first_name: props.employee.first_name,
    last_name: props.employee.last_name,
    email: props.employee.email,
    phone: props.employee.phone,
    company_id: props.employee.company_id,
});

const submit = () => {
    form.put(route('employees.update', props.employee.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Edit Employee" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Employee
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="first_name" value="First Name" />
                                <TextInput
                                    id="first_name"
                                    v-model="form.first_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.first_name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="last_name" value="Last Name" />
                                <TextInput
                                    id="last_name"
                                    v-model="form.last_name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.last_name" class="mt-2" />
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
                                <InputLabel for="phone" value="Phone" />
                                <TextInput
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.phone" class="mt-2" />
                            </div>

                            <div>
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

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    Update Employee
                                </PrimaryButton>
                                <a :href="route('employees.index')">
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

