<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import axios from 'axios';

const selectedUserId = ref(null);
const coins = ref(0);
const isPopupVisible = ref(false);

const props = defineProps({
    users: Array
});

const openPopup = (userId) => {
    selectedUserId.value = userId;
    isPopupVisible.value = true;
    coins.value = 0; // Reset wartości monet
};

const closePopup = () => {
    isPopupVisible.value = false;
};

const assignCoins = () => {
    if (!selectedUserId.value) {
        alert('Error: User not selected.');
        return;
    }

    axios.post('/assign-coins', {
        user_id: selectedUserId.value,
        coins: coins.value
    })
        .then(response => {
            alert(response.data.message);

            const user = props.users.find(user => user.id === selectedUserId.value);
            if (user) {
                user.coins = coins.value;
            }

            closePopup();
        })
        .catch(error => {
            alert('Something went wrong');
            console.error(error);
        });
};
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4 py-2 border">ID</th>
                                <th class="px-4 py-2 border">Name</th>
                                <th class="px-4 py-2 border">Email</th>
                                <th class="px-4 py-2 border">Coins</th>
                                <th class="px-4 py-2 border">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-100">
                                <td class="px-4 py-2 border">{{ user.id }}</td>
                                <td class="px-4 py-2 border">{{ user.name }}</td>
                                <td class="px-4 py-2 border">{{ user.email }}</td>
                                <td class="px-4 py-2 border">{{ user.coins }}</td>
                                <td class="px-4 py-2 border">
                                    <button
                                        @click="openPopup(user.id)"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded-lg shadow">
                                        Set coins
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isPopupVisible"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-96 relative">
                <button
                    @click="closePopup"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">
                    &times;
                </button>
                <h3 class="text-xl mb-4 font-semibold">Set Coins</h3>
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
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
