<template>
    <div class="w-full md:w-2/3 bg-casino-2 mt-4 p-4 rounded flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-2">
        <input
            v-model.number="localBetAmount"
            type="number"
            min="1"
            class="w-full md:w-1/4 bg-casino-2 text-white rounded p-2"
            placeholder="Enter bet amount"
            @change="updateBetAmount"
        />
        <div class="w-full md:w-3/4 flex flex-wrap md:flex-nowrap gap-2">
            <button @click="clearBet" class="flex-1 bg-gray-600 hover:bg-gray-700 rounded text-white py-2">
                Clear
            </button>
            <button @click="increaseBet(10)" class="flex-1 bg-gray-600 hover:bg-gray-700 rounded text-white py-2">
                +10
            </button>
            <button @click="increaseBet(100)" class="flex-1 bg-gray-600 hover:bg-gray-700 rounded text-white py-2">
                +100
            </button>
            <button @click="doubleBet" class="flex-1 bg-gray-600 hover:bg-gray-700 rounded text-white py-2">
                x2
            </button>
            <button @click="maxBet" class="flex-1 bg-red-500 hover:bg-red-600 rounded text-white py-2">
                MAX
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "BetAmountControls",
    props: {
        betAmount: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            localBetAmount: this.betAmount
        };
    },
    watch: {
        betAmount(newVal) {
            this.localBetAmount = newVal;
        }
    },
    methods: {
        updateBetAmount() {
            this.$emit("update:betAmount", this.localBetAmount);
        },
        clearBet() {
            this.localBetAmount = 0;
            this.updateBetAmount();
        },
        increaseBet(amount) {
            this.localBetAmount += amount;
            this.updateBetAmount();
        },
        doubleBet() {
            this.localBetAmount *= 2;
            this.updateBetAmount();
        },
        maxBet() {
            this.$emit("max-bet");
        }
    }
};
</script>
