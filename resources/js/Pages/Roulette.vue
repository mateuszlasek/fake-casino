<template>
    <Layout>
        <div class="container mx-auto min-h-screen p-6 flex flex-col items-center text-center">
            <h1 class="text-4xl font-bold text-yellow-400 mb-6">Roulette</h1>

            <RouletteWheel :wheelStyles="wheelStyles" :rows="rows" :cards="cards" />

            <div class="w-full max-w-5xl mt-6">
                <button
                    @click="spin"
                    class="w-full h-12 bg-green-600 hover:bg-green-700 rounded text-white text-lg font-semibold transition duration-300"
                >
                    Spin
                </button>
            </div>

            <div class="flex flex-col space-y-8 w-full max-w-7xl px-4 text-white mt-8">
                <div class="flex flex-col md:flex-row justify-between items-center w-full space-y-4 md:space-y-0 md:space-x-4">
                    <BalanceDisplay :balance="balance" />
                    <BetAmountControls
                        :betAmount="betAmount"
                        @update:betAmount="betAmount = $event"
                        @max-bet="handleMaxBet"
                    />
                </div>

                <BetPlacementButtons
                    :redPlayerTable="redPlayerTable"
                    :greenPlayerTable="greenPlayerTable"
                    :blackPlayerTable="blackPlayerTable"
                    :totalBetRed="totalBetRed"
                    :totalBetGreen="totalBetGreen"
                    :totalBetBlack="totalBetBlack"
                    :user="user"
                    @place-bet="placeBet"
                />
            </div>
        </div>
    </Layout>
</template>

<script>
import Layout from "@/layouts/Layout.vue";
import axios from "axios";
import RouletteWheel from "@/Components/Roulette/RouletteWheel.vue";
import BalanceDisplay from "@/Components/Roulette/BalanceDisplay.vue";
import BetAmountControls from "@/Components/Roulette/BetAmountControls.vue";
import BetPlacementButtons from "@/Components/Roulette/BetPlacementButtons.vue";

export default {
    name: "Roulette",
    components: { BetPlacementButtons, BetAmountControls, BalanceDisplay, RouletteWheel, Layout },
    props: {
        balance: Number,
        user: Object
    },
    data() {
        return {
            outcome: null,
            rows: Array(29).fill(null),
            cards: [
                { number: 1, color: "red" },
                { number: 14, color: "black" },
                { number: 2, color: "red" },
                { number: 13, color: "black" },
                { number: 3, color: "red" },
                { number: 12, color: "black" },
                { number: 4, color: "red" },
                { number: 0, color: "green" },
                { number: 11, color: "black" },
                { number: 5, color: "red" },
                { number: 10, color: "black" },
                { number: 6, color: "red" },
                { number: 9, color: "black" },
                { number: 7, color: "red" },
                { number: 8, color: "black" }
            ],
            wheelStyles: {},
            totalBetRed: 0,
            totalBetGreen: 0,
            totalBetBlack: 0,
            activeBets: [],
            betAmount: 0,
            balance: this.$props.balance,
            redPlayerTable: {},
            greenPlayerTable: {},
            blackPlayerTable: {}
        };
    },
    methods: {
        async spin() {
            try {
                const response = await axios.post("/spin-wheel", {
                    activeBets: this.activeBets
                });
                this.outcome = response.data.number;

                const order = [0, 11, 5, 10, 6, 9, 7, 8, 1, 14, 2, 13, 3, 12, 4];
                const position = order.indexOf(parseInt(this.outcome));

                if (position === -1) {
                    alert("Wrong result!");
                    return;
                }

                const rows = 12;
                const card = 75 + 3 * 2;
                const landingPosition = rows * 15 * card + position * card;
                const randomize = Math.floor(Math.random() * 75) - 75 / 2;

                const object = {
                    x: Math.floor(Math.random() * 50) / 100,
                    y: Math.floor(Math.random() * 20) / 100
                };

                this.wheelStyles = {
                    transitionTimingFunction: `cubic-bezier(0,${object.x},${object.y},1)`,
                    transitionDuration: "6s",
                    transform: `translate3d(-${landingPosition + randomize}px, 0px, 0px)`
                };

                setTimeout(() => {
                    const resetTo = -(position * card + randomize);
                    this.wheelStyles = {
                        transitionTimingFunction: "",
                        transitionDuration: "",
                        transform: `translate3d(${resetTo}px, 0px, 0px)`
                    };

                    this.activeBets = [];
                    this.totalBetRed = 0;
                    this.totalBetBlack = 0;
                    this.totalBetGreen = 0;

                    this.redPlayerTable = {};
                    this.greenPlayerTable = {};
                    this.blackPlayerTable = {};

                    this.updateBalance();
                }, 6000);
            } catch (error) {
                console.error("Error retrieving result:", error);
                alert("An error occurred while turning the wheel.");
            }
        },
        async placeBet(color) {
            try {
                const response = await axios.post("/place-bet", {
                    color: color,
                    amount: this.betAmount
                });
                this.addToPlayerTable(color);
                this.activeBets.push(response.data.bet_id);
                alert("Bet accepted! New balance: " + response.data.new_balance);
                this.updateTotalBet(color, this.betAmount);
                this.balance = response.data.new_balance;
            } catch (error) {
                alert("Error: " + error.response.data.error);
            }
        },
        addToPlayerTable(color) {
            if (color === "red") {
                this.redPlayerTable[this.user.id] =
                    (this.redPlayerTable[this.user.id] || 0) + this.betAmount;
            } else if (color === "green") {
                this.greenPlayerTable[this.user.id] =
                    (this.greenPlayerTable[this.user.id] || 0) + this.betAmount;
            } else if (color === "black") {
                this.blackPlayerTable[this.user.id] =
                    (this.blackPlayerTable[this.user.id] || 0) + this.betAmount;
            }
        },
        async updateBalance() {
            try {
                const response = await axios.get("/get-balance", {
                    params: { user_id: this.user.id }
                });
                this.balance = response.data.balance;
            } catch (error) {
                alert("Error: " + error.response.data.error);
            }
        },
        updateTotalBet(color, totalBet) {
            if (color === "red") {
                this.totalBetRed += totalBet;
            } else if (color === "green") {
                this.totalBetGreen += totalBet;
            } else if (color === "black") {
                this.totalBetBlack += totalBet;
            }
        },
        handleMaxBet() {
            this.betAmount = this.balance;
        }
    }
};
</script>
