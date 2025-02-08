<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import axios from 'axios';

const selectedUserId = ref(null);

const props = defineProps({
    users: Array
});

const isPopupVisible = ref(false);

const coins = ref(0);

const openPopup = (userId) => {
    selectedUserId.value = userId;
    isPopupVisible.value = true;
};

const closePopup = () => {
    isPopupVisible.value = false;
};

const assignCoins = () => {

    axios.post('/assign-coins', {
        user_id: selectedUserId.value,
        coins: coins.value
    })
        .then(response => {
            alert(response.data.message);
            closePopup();
        })
        .catch(error => {
            alert('Coś poszło nie tak');
            console.error(error);
        });    closePopup();
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="flex p-6 text-gray-900">
                        <table>
                            <tr v-for="user in users" :key="user.id">
                                <td class="px-4 py-2">{{ user.id }}</td>
                                <td class="px-4 py-2">{{ user.name }}</td>
                                <td class="px-4 py-2">{{ user.email }}</td>
                            </tr>
                        </table>
                        <button
                            @click="openPopup"
                            class="ml-auto bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-6 rounded-lg text-lg shadow-lg">
                            Set coins
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isPopupVisible" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-96">
                <button
                    @click="closePopup"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                    &times;
                </button>
                <h3 class="text-xl mb-4">Set Coins</h3>
                <label for="coins" class="block text-sm font-medium text-gray-700">Coins</label>
                <input
                    id="coins"
                    v-model="coins"
                    type="number"
                    min="1"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                />
                <div class="mt-4 flex justify-end">
                    <button
                        @click="assignCoins"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mr-2">
                        Set
                    </button>
                    <button
                        @click="closePopup"
                        class="bg-red-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

