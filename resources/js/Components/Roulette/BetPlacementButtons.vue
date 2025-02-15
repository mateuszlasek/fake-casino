<template>
    <div class="flex flex-col md:flex-row justify-between space-y-4 md:space-y-0 md:space-x-4">
        <div class="w-full md:w-1/3">
            <button @click="placeBet('red')" class="w-full h-12 bg-red-500 hover:bg-red-600 rounded text-white">
                Red
            </button>
            <div class="w-full bg-casino-2 mt-4 p-2 text-right rounded">
                Total Bet: {{ totalBetRed }}
            </div>
            <div
                v-for="(amount, username) in redPlayerTable"
                :key="username"
                class="w-full bg-casino-2 mt-4 p-2 flex rounded justify-between"
            >
                <span>{{ username }}</span>
                <span>{{ amount }}</span>
            </div>
        </div>
        <div class="w-full md:w-1/3">
            <button @click="placeBet('green')" class="w-full h-12 bg-green-600 hover:bg-green-700 rounded text-white">
                Green
            </button>
            <div class="w-full bg-casino-2 mt-4 p-2 text-right rounded">
                Total Bet: {{ totalBetGreen }}
            </div>
            <div
                v-for="(amount, username) in greenPlayerTable"
                :key="username"
                class="w-full bg-casino-2 mt-4 p-2 flex rounded justify-between"
            >
                <span>{{ username }}</span>
                <span>{{ amount }}</span>
            </div>
        </div>
        <div class="w-full md:w-1/3">
            <button @click="placeBet('black')" class="w-full h-12 bg-gray-900 hover:bg-gray-800 rounded text-white">
                Black
            </button>
            <div class="w-full bg-casino-2 mt-4 p-2 text-right rounded">
                Total Bet: {{ totalBetBlack }}
            </div>
            <div
                v-for="(amount, username) in blackPlayerTable"
                :key="username"
                class="w-full bg-casino-2 mt-4 p-2 flex rounded justify-between"
            >
                <span>{{ username }}</span>
                <span>{{ amount }}</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "BetPlacementButtons",
    props: {
        redPlayerTable: { type: Object, default: () => ({}) },
        greenPlayerTable: { type: Object, default: () => ({}) },
        blackPlayerTable: { type: Object, default: () => ({}) },
        totalBetRed: { type: Number, default: 0 },
        totalBetGreen: { type: Number, default: 0 },
        totalBetBlack: { type: Number, default: 0 },
        user: { type: Object, required: true }
    },
    methods: {
        placeBet(color) {
            this.$emit("place-bet", color);
        }
    }
};
</script>
