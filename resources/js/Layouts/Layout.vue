<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { LogIn, UserPlus, User, Coins, LogOut } from 'lucide-vue-next';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const balance = ref(0);
const user = computed(() => usePage().props.auth?.user ?? null);
const isLoggedIn = computed(() => user.value !== null);
const isMobileMenuOpen = ref(false);

const getBalance = async () => {
    try {
        const response = await axios.get('/get-balance', {
            params: { user_id: user.value.id }
        });
        balance.value = response.data.balance;
    } catch (error) {
        console.error('Error fetching balance:', error);
    }
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};

onMounted(() => {
    if (isLoggedIn.value) getBalance();

    const handleResize = () => {
        if (window.innerWidth >= 768) {
            isMobileMenuOpen.value = false;
        }
    };

    window.addEventListener('resize', handleResize);
    onUnmounted(() => window.removeEventListener('resize', handleResize));
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-casino-1">
            <header class="bg-casino-2 text-white">
                <nav class="flex items-center justify-between p-4 max-w-screen-lg ml-auto">
                    <div class="flex md:hidden">
                        <button
                            @click="isMobileMenuOpen = !isMobileMenuOpen"
                            class="hover:text-yellow-400 focus:outline-none"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>

                    <div class="hidden md:flex space-x-6">
                        <Link href="/">Home</Link>
                        <Link href="/about">About</Link>
                        <Link href="/casino">Casino</Link>
                        <Link href="/contact">Contact</Link>
                    </div>

                    <div class="flex items-center space-x-6">
                        <template v-if="isLoggedIn">
                            <div class="flex items-center space-x-4">
                                <Link :href="route('dashboard')" class="hover:text-yellow-400">
                                    <User class="w-5 h-5"/>
                                </Link>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    class="hover:text-yellow-400 flex"
                                >
                                    <LogOut class="w-5 h-5"/>
                                    <span class="hidden md:inline ml-2">Logout</span>
                                </Link>
                            </div>
                        </template>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="flex items-center space-x-2 hover:text-yellow-400"
                            >
                                <LogIn class="w-5 h-5"/>
                                <span class="hidden md:inline">Login</span>
                            </Link>
                            <Link
                                :href="route('register')"
                                class="flex items-center space-x-2 hover:text-yellow-400"
                            >
                                <UserPlus class="w-5 h-5"/>
                                <span class="hidden md:inline">Register</span>
                            </Link>
                        </template>
                    </div>
                </nav>

                <div v-if="isMobileMenuOpen" class="md:hidden">
                    <div class="px-4 pb-2 space-y-2">
                        <Link @click="closeMobileMenu" href="/" class="block py-2 hover:text-yellow-400">Home</Link>
                        <Link @click="closeMobileMenu" href="/about" class="block py-2 hover:text-yellow-400">About</Link>
                        <Link @click="closeMobileMenu" href="/casino" class="block py-2 hover:text-yellow-400">Casino</Link>
                        <Link @click="closeMobileMenu" href="/contact" class="block py-2 hover:text-yellow-400">Contact</Link>

                        <template v-if="isLoggedIn">
                            <div class="flex items-center py-2 text-yellow-400">
                                <Coins class="w-5 h-5"/>
                                <span class="ml-2 font-semibold">{{ balance }}</span>
                            </div>
                            <Link
                                @click="closeMobileMenu"
                                :href="route('dashboard')"
                                class="block py-2 hover:text-yellow-400"
                            >
                                Dashboard
                            </Link>
                            <Link
                                @click="closeMobileMenu"
                                :href="route('logout')"
                                method="post"
                                class="block py-2 hover:text-yellow-400"
                            >
                                Logout
                            </Link>
                        </template>

                        <template v-else>
                            <Link
                                @click="closeMobileMenu"
                                :href="route('login')"
                                class="block py-2 hover:text-yellow-400"
                            >
                                Login
                            </Link>
                            <Link
                                @click="closeMobileMenu"
                                :href="route('register')"
                                class="block py-2 hover:text-yellow-400"
                            >
                                Register
                            </Link>
                        </template>
                    </div>
                </div>
            </header>

            <main class="p-4">
                <slot/>
            </main>
        </div>
    </div>
</template>
