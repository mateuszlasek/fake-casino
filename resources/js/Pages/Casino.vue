<script setup>
import Layout from "@/Layouts/Layout.vue";


const goToRoulette = () => {
    window.location.href = "/roulette";
};

const goToSlots = () => {
    window.location.href = "/slots";
};
</script>

<template>
    <Layout>
        <div class="bg-gray-900 min-h-screen p-8">
            <div class="container mx-auto">
                <h1 class="text-4xl font-bold text-yellow-400 mb-8 text-center">Casino Games</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                        <div class="bg-gray-700 p-8 flex justify-center items-center">
                            <span class="text-6xl">🎡</span>
                        </div>
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-yellow-400 mb-2">Roulette</h2>
                            <p class="text-gray-300 mb-4">Roulette with premium experience</p>
                            <button
                                @click="goToRoulette"
                                class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded-lg w-full transition-colors duration-300"
                            >
                                Place Bet
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                        <div class="bg-gray-700 p-8 flex justify-center items-center">
                            <span class="text-6xl">🎰</span>
                        </div>
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-yellow-400 mb-2">Slots</h2>
                            <p class="text-gray-300 mb-4">Classic slot machine with golden symbols</p>
                            <button
                                @click="goToSlots"
                                class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded-lg w-full transition-colors duration-300"
                            >
                                Spin Now
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                        <div class="bg-gray-700 p-8 flex justify-center items-center">
                            <span class="text-6xl">🃏</span>
                        </div>
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-yellow-400 mb-2">Blackjack</h2>
                            <p class="text-gray-300 mb-4">Beat the dealer in this classic card game</p>
                            <button
                                class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-4 rounded-lg w-full transition-colors duration-300"
                            >
                                Join Table
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
