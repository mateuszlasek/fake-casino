<template>
    <div class="mb-6">
        <input
            v-model.number="bet"
            type="number"
            min="1"
            :max="balance"
            class="w-32 px-4 py-2 bg-casino-1 text-white rounded-lg text-center font-bold"
        >
    </div>

    <button
        @click="spin"
        :disabled="!canSpin"
        class="px-8 py-3 bg-yellow-400 text-casino-2 font-bold rounded-lg hover:bg-yellow-300 transition-colors duration-300 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105"
    >
        {{ isSpinning ? 'SPINNING...' : 'SPIN' }}
    </button>
</template>

<script setup>
defineProps({
    bet: Number,
    balance: Number,
    canSpin: Boolean,
    isSpinning: Boolean
});

defineEmits(['update:bet', 'spin']);
</script>
