<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Layout from '../Layouts/Layout.vue';
import {usePage} from "@inertiajs/vue3";

const playerCards = ref([]);
const dealerCards = ref([]);
const playerScore = ref(0);
const dealerScore = ref(0);
const gameOver = ref(false);
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
        balance.value -= bet.value;
    } catch (error) {
        console.error('Error starting game:', error);
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

const stand = async () => {
    try {
        const response = await axios.post('/blackjack/stand');
        dealerCards.value = response.data.dealerCards;
        dealerScore.value = response.data.dealerScore;
        gameOver.value = true;

        switch(response.data.result) {
            case 'player':
                result.value = `You win $${bet.value * 1.5}!`;
                balance.value += bet.value * 2.5;
                break;
            case 'dealer':
                result.value = 'Dealer wins!';
                break;
            case 'push':
                result.value = 'Push! Bet returned';
                balance.value += bet.value;
                break;
        }
    } catch (error) {
        console.error('Error standing:', error);
    }
};
</script>

<template>
    <Layout>
        <div class="blackjack-game container mx-auto p-4">
            <div class="balance mb-4 text-2xl font-bold text-white">
                Balance: ${{ balance }}
            </div>

            <div class="bet-controls mb-4">
                <input v-model.number="bet" type="number" min="10" max="1000"
                       class="p-2 rounded bg-gray-800 text-white mr-2">
                <button @click="startGame"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    New Game
                </button>
            </div>

            <div class="dealer-hand mb-8">
                <h2 class="text-white text-xl mb-2">Dealer: {{ dealerScore }}</h2>
                <div class="cards flex gap-2">
                    <div v-for="(card, index) in dealerCards"
                         :key="index"
                         class="card"
                         :class="{ 'hidden-card': card === 'hidden', 'revealed': index === 0 }">
                        <template v-if="card !== 'hidden'">
                            <div class="value">{{ card.replace(/[^0-9AJQK]/, '') }}</div>
                            <div class="suit">{{ suitSymbols[card.slice(-1)] }}</div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="player-hand mb-8">
                <h2 class="text-white text-xl mb-2">Player: {{ playerScore }}</h2>
                <div class="cards flex gap-2">
                    <div v-for="(card, index) in playerCards"
                         :key="index"
                         class="card"
                         :class="{ 'revealed': true }"
                         :style="{ 'transition-delay': `${index * 0.2}s` }">
                        <div class="value">{{ card.replace(/[^0-9AJQK]/, '') }}</div>
                        <div class="suit">{{ suitSymbols[card.slice(-1)] }}</div>
                    </div>
                </div>
            </div>

            <div class="controls flex gap-2">
                <button @click="hit"
                        :disabled="gameOver"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-50">
                    Hit
                </button>
                <button @click="stand"
                        :disabled="gameOver"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded disabled:opacity-50">
                    Stand
                </button>
            </div>

            <div v-if="result" class="result mt-4 text-2xl font-bold text-yellow-400">
                {{ result }}
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.card {
    @apply w-20 h-28 bg-white rounded-lg p-2 flex flex-col items-center justify-center
    shadow-lg transform transition-all duration-500;
    transform-style: preserve-3d;
}

.card.hidden-card {
    @apply bg-red-500;
}

.card.hidden-card::before {
    content: '🎴';
    @apply text-3xl;
}

.value {
    @apply text-2xl font-bold mb-2;
}

.suit {
    @apply text-2xl;
}

.revealed {
    animation: flip 0.6s ease-out forwards;
}

@keyframes flip {
    0% { transform: rotateY(180deg); }
    100% { transform: rotateY(0deg); }
}
</style>
