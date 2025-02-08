<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

import Layout from "@/Layouts/Layout.vue";

const props = defineProps({
    users: Array // Oczekujemy tablicy użytkowników
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <tr v-for="user in users" :key="user.id">
                            <td class="px-4 py-2">{{ user.id }}</td>
                            <td class="px-4 py-2">{{ user.name }}</td>
                            <td class="px-4 py-2">{{ user.email }}</td>

                        </tr>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
