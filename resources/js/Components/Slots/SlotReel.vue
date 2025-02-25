<template>
    <div class="slot-container w-20 h-20 bg-casino-1 rounded-lg border-2 border-yellow-400 overflow-hidden relative">
        <div class="symbols absolute w-full" :style="{ transform: `translateY(${-offset}px)` }">
            <SlotSymbol v-for="(symbol, i) in symbols" :key="i" :symbol="symbol" />
        </div>
    </div>
</template>

<script setup>
import SlotSymbol from './SlotSymbol.vue';

defineProps({
    symbols: Array,
    offset: Number
});
</script>
