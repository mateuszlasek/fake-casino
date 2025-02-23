<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import Layout from '@/Layouts/Layout.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const symbols = ['🍒', '🍊', '🍋', '🍇', '💎', '7️⃣', '🔔', '💰'];
const slots = ref(['🍒', '🍒', '🍒']);
const isSpinning = ref(false);
const bet = ref(10);
const resultMessage = ref('');
const balance = ref(page.props.balance);

const canSpin = computed(() => {
    return !isSpinning.value && bet.value > 0 && balance.value >= bet.value;
});

const spin = async () => {
    if (!canSpin.value) return;

    isSpinning.value = true;
    resultMessage.value = '';

    try {
        const response = await axios.post('/spin', { bet: bet.value });

        const start = Date.now();
        let lastUpdate = 0;
        const animationDuration = 4000;
        const updateInterval = 200;

        const animate = () => {
            const now = Date.now();
            const elapsed = now - start;

            if (elapsed < animationDuration) {
                if (now - lastUpdate >= updateInterval) {
                    slots.value = Array(3).fill().map(() => symbols[Math.floor(Math.random() * symbols.length)]);
                    lastUpdate = now;
                }
                requestAnimationFrame(animate);
            } else {
                slots.value = response.data.result;
                isSpinning.value = false;
                balance.value = response.data.balance;
                if (response.data.is_win) {
                    document.querySelectorAll('.slot').forEach(slot => {
                        slot.classList.add('win-animation');
                        setTimeout(() => slot.classList.remove('win-animation'), 1500);
                    });
                }
                resultMessage.value = response.data.is_win
                    ? `🎉 You won ${response.data.prize}! 🎉`
                    : 'Try again!';
            }
        };

        animate();
    } catch (error) {
        console.error('Spin error:', error);
        resultMessage.value = error.response?.data?.error || 'Error during the game';
        isSpinning.value = false;
    }
};
</script>

<template>
    <Layout title="Slot Machine">
        <div class="max-w-md mx-auto p-6 bg-casino-2 rounded-lg shadow-lg">
            <div class="text-center mb-8">
                <div class="text-2xl font-bold text-yellow-400 mb-4">
                    BALANCE: {{ balance }}
                </div>

                <div class="flex justify-center gap-4 mb-8">
                    <div
                        v-for="(symbol, index) in slots"
                        :key="index"
                        class="slot w-20 h-20 bg-casino-1 flex items-center justify-center text-4xl rounded-lg
                   border-2 border-yellow-400 transition-all duration-500 ease-in-out"
                    >
                        {{ symbol }}
                    </div>
                </div>

                <div class="mb-6">
                    <input
                        v-model.number="bet"
                        type="number"
                        min="1"
                        :max="balance"
                        class="w-32 px-4 py-2 bg-casino-1 text-white rounded-lg focus:outline-none
                   focus:ring-2 focus:ring-yellow-400 text-center font-bold"
                    >
                </div>

                <button
                    @click="spin"
                    :disabled="!canSpin"
                    class="px-8 py-3 bg-yellow-400 text-casino-2 font-bold rounded-lg
                 hover:bg-yellow-300 transition-colors duration-300 disabled:opacity-50
                 disabled:cursor-not-allowed transform hover:scale-105"
                >
                    {{ isSpinning ? 'SPINNING...' : 'SPIN' }}
                </button>

                <div v-if="resultMessage" class="mt-6 text-xl font-semibold text-yellow-400 animate-pulse">
                    {{ resultMessage }}
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>
@keyframes win-flash {
    0% { background-color: #2a2a2a; }
    50% { background-color: #4a7023; }
    100% { background-color: #2a2a2a; }
}

.win-animation {
    animation: win-flash 1.5s ease-in-out 3;
}

.slot {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
    perspective: 1000px;
}

.slot::before {
    content: '';
    @apply absolute inset-0 bg-gradient-to-b from-transparent to-black opacity-20 rounded-lg;
}
</style>
