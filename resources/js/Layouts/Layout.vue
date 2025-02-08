<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { LogIn, UserPlus, User, Coins, LogOut } from 'lucide-vue-next';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios'; // Ensure axios is imported

const balance = ref(0);
const user = computed(() => usePage().props.auth?.user ?? null);
const isLoggedIn = computed(() => user.value !== null);

const getBalance = async () => {
    try {
        const response = await axios.get('/get-balance', {
            params: {
                user_id: user.value.id
            }
        });
        console.log('Balance response:', response.data);
        balance.value = response.data.balance;
    } catch (error) {
        console.error('Error fetching balance:', error);
    }
};

onMounted(() => {
    if (isLoggedIn.value) {
        getBalance();
    }
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-casino-1">
            <header class="bg-casino-2 text-white">
                <nav class="flex items-center justify-between p-4 max-w-screen-lg mx-auto">
                    <div class="space-x-6">
                        <Link href="/">Home</Link>
                        <Link href="/about">About</Link>
                        <Link href="/casino">Casino</Link>
                        <Link href="/contact">Contact</Link>
                    </div>
                    <div class="flex space-x-6">

                        <template v-if="isLoggedIn">
                            <div class="flex items-center space-x-2 px-3 py-2 ml-auto">
                                <Coins class="w-5 h-5 text-yellow-400"/>
                                <span class="text-yellow-400 font-semibold">{{ balance }}</span>
                            </div>

                            <div class="flex items-center space-x-2 ml-auto">
                                <Link
                                    :href="route('dashboard')"
                                    class="flex items-center space-x-2 hover:text-yellow-400">
                                    <User class="w-5 h-5"/>
                                </Link>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    class="flex items-center space-x-2 hover:text-yellow-400">
                                    <LogOut class="w-5 h-5"/>
                                    <span>Logout</span>
                                </Link>
                            </div>
                        </template>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="flex items-center space-x-2 hover:text-yellow-400">
                                <LogIn class="w-5 h-5"/>
                                <span>Login</span>
                            </Link>
                            <Link
                                :href="route('register')"
                                class="flex items-center space-x-2 hover:text-yellow-400">
                                <UserPlus class="w-5 h-5"/>
                                <span>Register</span>
                            </Link>
                        </template>

                    </div>
                </nav>
            </header>

            <main class="p-4">
                <slot/>
            </main>
        </div>
    </div>
</template>

<style scoped>
</style>
