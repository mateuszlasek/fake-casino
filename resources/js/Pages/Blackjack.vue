<script setup>
import {onMounted, onUnmounted, ref} from 'vue';
import axios from 'axios';
import Layout from '../Layouts/Layout.vue';
import { usePage } from "@inertiajs/vue3";

const playerCards = ref([]);
const dealerCards = ref([]);
const playerScore = ref(0);
const dealerScore = ref(0);
const gameOver = ref(true);
const bet = ref(10);
const result = ref('');
const balance = ref(usePage().props.auth?.user?.balance ?? 0);

const suitSymbols = {
    '♥': '❤️',
    '♦': '♦️',
    '♣': '♣️',
    '♠': '♠️'
};

const startGame = async () => {
    try {
        const response = await axios.post('/blackjack/start', { bet: bet.value });
        playerCards.value = response.data.playerCards;
        dealerCards.value = response.data.dealerCards;
        playerScore.value = response.data.playerScore;
        dealerScore.value = response.data.dealerScore;
        gameOver.value = false;
        result.value = '';
        balance.value = response.data.balance;
    } catch (error) {
        console.error('Error while starting the game:', error);
    }
};

const handleBeforeUnload = (event) => {
    if (!gameOver.value) {
        event.preventDefault();
        event.returnValue = '';
    }
};


onMounted(() => {
    window.addEventListener('beforeunload', handleBeforeUnload);
});

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
});

const stand = async () => {
    try {
        const response = await axios.post('/blackjack/stand');
        dealerCards.value = response.data.dealerCards;
        dealerScore.value = response.data.dealerScore;
        gameOver.value = true;
        balance.value = response.data.balance;

        switch(response.data.result) {
            case 'player':
                result.value = `🎉 You won ${bet.value * 1.5} ! 🎉`;
                break;
            case 'dealer':
                result.value = 'Dealer wins!';
                break;
            case 'push':
                result.value = 'Bust! Bet returned';
                break;
        }
    } catch (error) {
        console.error('Error during stand:', error);
    }
};

const hit = async () => {
    try {
        const response = await axios.post('/blackjack/hit');
        playerCards.value = response.data.playerCards;
        playerScore.value = response.data.playerScore;
        gameOver.value = response.data.gameOver;

        if (gameOver.value) {
            result.value = playerScore.value > 21 ? 'Bust! You lose!' : '';
        }
    } catch (error) {
        console.error('Error hitting:', error);
    }
};

</script>

<template>
    <Layout>
        <div class="max-w-lg mx-auto p-6 bg-casino-2 rounded-lg shadow-lg">
            <div class="text-center mb-6">
                <div class="text-2xl font-bold text-yellow-400">
                    BALANCE: {{ balance }}
                </div>
            </div>

            <div class="flex justify-center gap-4 mb-6">
                <input
                    v-model.number="bet"
                    type="number"
                    min="10"
                    class="w-32 px-4 py-2 bg-gray-700 text-white rounded-lg text-center font-bold focus:outline-none focus:ring-2 focus:ring-yellow-400"
                >
                <button
                    @click="startGame"
                    class="px-8 py-3 bg-yellow-400 text-gray-800 font-bold rounded-lg hover:bg-yellow-300 transition-transform duration-300 transform hover:scale-105"
                >
                    NEW GAME
                </button>
            </div>

            <div class="mb-8">
                <h2 class="text-center text-xl font-bold text-yellow-400 mb-4">
                    DEALER: {{ dealerScore }}
                </h2>
                <div class="flex gap-4 justify-center">
                    <div
                        v-for="(card, index) in dealerCards"
                        :key="index"
                        class="w-20 h-28 bg-white rounded-lg p-2 shadow-lg flex flex-col relative"
                        :class="{ 'bg-yellow-400': card === 'hidden' }"
                    >
                        <template v-if="card !== 'hidden'">
                            <div class="absolute top-1 left-2 text-xl font-bold text-gray-800">
                                {{ card.replace(/[^0-9AJQK]/, '') }}
                            </div>
                            <div class="flex-grow flex items-center justify-center text-3xl text-gray-800">
                                {{ suitSymbols[card.slice(-1)] }}
                            </div>
                        </template>
                        <template v-else>
                            <div class="w-full h-full flex items-center justify-center text-3xl text-gray-800">?</div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-center text-xl font-bold text-yellow-400 mb-4">
                    PLAYER: {{ playerScore }}
                </h2>
                <div class="flex gap-4 justify-center">
                    <div
                        v-for="(card, index) in playerCards"
                        :key="index"
                        class="w-20 h-28 bg-white rounded-lg p-2 shadow-lg flex flex-col relative"
                        :style="{ 'transition-delay': `${index * 0.2}s` }"
                    >
                        <div class="absolute top-1 left-2 text-xl font-bold text-gray-800">
                            {{ card.replace(/[^0-9AJQK]/, '') }}
                        </div>
                        <div class="flex-grow flex items-center justify-center text-3xl text-gray-800">
                            {{ suitSymbols[card.slice(-1)] }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center gap-4">
                <button
                    @click="hit"
                    :disabled="gameOver"
                    class="px-8 py-3 bg-yellow-400 text-gray-800 font-bold rounded-lg hover:bg-yellow-300 transition-transform duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    HIT
                </button>
                <button
                    @click="stand"
                    :disabled="gameOver"
                    class="px-8 py-3 bg-yellow-400 text-gray-800 font-bold rounded-lg hover:bg-yellow-300 transition-transform duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    STAND
                </button>
            </div>

            <div v-if="result" class="mt-6 text-center text-xl font-semibold text-yellow-400 animate-pulse">
                {{ result }}
            </div>
        </div>
    </Layout>
</template>

<style scoped>
</style>
