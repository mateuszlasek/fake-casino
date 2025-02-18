<template>
    <div class="history-container mt-4 flex gap-2">
        <div
            v-for="(color, index) in historyColors"
            :key="index"
            class="w-8 h-8 rounded-full"
            :class="{
                'bg-red-600': color === 'red',
                'bg-green-600': color === 'green',
                'bg-gray-900': color === 'black'
            }"
        ></div>
    </div>
</template>

<script>
export default {
    name: "RouletteHistory",
    props: {
        historyColors: {
            type: Array,
            required: true,
        },
    },
};
</script>

<style scoped>
</style>
