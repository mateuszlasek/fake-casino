<template>
    <Layout>
        <div v-if="loading" class="fixed w-full h-full flex items-center justify-center bg-casino-1 z-50">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-t-transparent border-blue-500"></div>
        </div>
        <div class="container mx-auto min-h-screen p-6 flex flex-col items-center text-center">
            <h1 class="text-4xl font-bold text-yellow-400 mb-6">Roulette</h1>

            <RouletteWheel :wheelStyles="wheelStyles" :rows="rows" :cards="cards" />
            <RouletteHistory :historyColors="historyColors" />

            <!-- Timer Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 my-4">
                <div
                    class="bg-green-500 h-full rounded-full"
                    :style="{ width: timerWidth + '%' }"
                ></div>
            </div>

            <div class="w-full max-w-5xl mt-6">
                <button
                    :disabled="spinning || timerActive"
                    @click="initiateSpin"
                    class="w-full h-12 bg-green-600 hover:bg-green-700 rounded text-white text-lg font-semibold transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
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
                    :disabled="spinning"
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
import RouletteHistory from "@/Components/Roulette/RouletteHistory.vue";

export default {
    name: "Roulette",
    components: {
        Layout,
        RouletteWheel,
        BalanceDisplay,
        BetAmountControls,
        BetPlacementButtons,
        RouletteHistory,
    },
    props: {
        balance: Number,
        user: Object,
        initialBets: Object,
    },
    data() {
        return {
            loading: true,
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
                { number: 8, color: "black" },
            ],
            wheelStyles: {},
            totalBetRed: 0,
            totalBetGreen: 0,
            totalBetBlack: 0,
            activeBets: [],
            betAmount: 0,
            balance: this.$props.balance,
            redPlayerTable: this.initialBets.red || [],
            greenPlayerTable: this.initialBets.green || [],
            blackPlayerTable: this.initialBets.black || [],
            spinning: false,
            historyColors: [],
            // Właściwości związane z timerem
            timerActive: false,          // Czy timer już działa
            timerWidth: 100,             // Pasek zaczyna od 100%
            timerDuration: 30000,        // Całkowity czas timera – 30 sekund (w ms)
            timerStart: null,            // Czas rozpoczęcia timera (ustawiany raz)
            timerEnd: null,              // Czas zakończenia timera (timerStart + timerDuration)
            animationFrame: null,        // ID requestAnimationFrame
        };
    },
    mounted() {
        // Gdy nowy użytkownik wchodzi na stronę, pobieramy aktualny stan ruletki/timera
        this.fetchCurrentSpin();
        this.fetchHistory().finally(() => {
            this.loading = false;
        });

        // Subskrypcja eventów z kanału "roulette"
        window.Echo.channel("roulette")
            .listen("TimerTickEvent", (event) => {
                // Dla nowych użytkowników mogą przychodzić ticki – ale timer ustawiamy raz
                this.onTimerTick(event.timeLeft);
            });

        window.Echo.channel("roulette").listen("RouletteSpinEvent", (data) => {
            this.handleRemoteSpin(data.winningNumber, data.randomize);
        });

        window.Echo.channel("bet-placed")
            .listen("BetPlacedEvent", (data) => {
                this.updateBetTable(data);
            });
    },
    beforeUnmount() {
        if (this.animationFrame) {
            cancelAnimationFrame(this.animationFrame);
        }
    },
    methods: {
        async fetchHistory() {
            try {
                const response = await axios.get("/get-history");
                this.historyColors = response.data;
            } catch (error) {
                console.error("Błąd podczas pobierania historii:", error);
            }
        },
        async initiateSpin() {
            if (this.spinning) return;
            this.spinning = true;
            try {
                const response = await axios.post("/spin-wheel");
                this.outcome = response.data.number;
            } catch (error) {
                alert("Wystąpił błąd podczas obracania ruletki.");
                this.spinning = false;
            }
        },
        handleRemoteSpin(winningNumber, randomize, duration = 6000) {
            if (typeof randomize !== "number" || isNaN(randomize)) {
                randomize = 0;
            }
            const order = [0, 11, 5, 10, 6, 9, 7, 8, 1, 14, 2, 13, 3, 12, 4];
            const position = order.indexOf(parseInt(winningNumber));
            if (position === -1) {
                this.spinning = false;
                return;
            }
            const rows = 12;
            const card = 75 + 3 * 2;
            const landingPosition = rows * 15 * card + position * card;
            const bezierValues = {x: 0.5, y: 0.5};
            this.wheelStyles = {
                transitionTimingFunction: `cubic-bezier(0,${bezierValues.x},${bezierValues.y},1)`,
                transitionDuration: `${duration}ms`,
                transform: `translate3d(-${landingPosition + randomize}px, 0px, 0px)`,
            };
            setTimeout(() => {
                const resetTo = -(position * card + randomize);
                this.wheelStyles = {
                    transitionTimingFunction: "",
                    transitionDuration: "",
                    transform: `translate3d(${resetTo}px, 0px, 0px)`,
                };
                // Resetujemy zakłady i pobieramy nową historię
                this.activeBets = [];
                this.totalBetRed = 0;
                this.totalBetBlack = 0;
                this.totalBetGreen = 0;
                this.redPlayerTable = [];
                this.greenPlayerTable = [];
                this.blackPlayerTable = [];
                this.updateBalance();
                this.spinning = false;
                this.fetchHistory();
                axios.post("/clear-spin");
            }, duration);
        },
        async placeBet(color) {
            if (this.spinning) return;
            try {
                const response = await axios.post("/place-bet", {
                    color: color,
                    amount: this.betAmount,
                });
                // Jeśli to pierwszy zakład, uruchamiamy timer (na backendzie dostępny endpoint /start-timer)
                if (!this.timerActive) {
                    axios.post("/start-timer");
                }
                this.activeBets.push(response.data.bet_id);
                alert("Zakład przyjęty! Nowe saldo: " + response.data.new_balance);
                this.balance = response.data.new_balance;
            } catch (error) {
                console.error("Błąd przy składaniu zakładu:", error);
                alert("Błąd: " + error.response.data.error);
            }
        },
        updateBetTable(betData) {
            const newBet = {
                username: betData.username,
                amount: betData.amount,
            };
            if (betData.color === "red") {
                this.redPlayerTable.push(newBet);
            } else if (betData.color === "green") {
                this.greenPlayerTable.push(newBet);
            } else if (betData.color === "black") {
                this.blackPlayerTable.push(newBet);
            }
            this.updateTotalBet(betData.color, betData.amount);
        },
        updateTotalBet(color, betAmount) {
            if (color === "red") {
                this.totalBetRed += betAmount;
            } else if (color === "green") {
                this.totalBetGreen += betAmount;
            } else if (color === "black") {
                this.totalBetBlack += betAmount;
            }
        },
        async updateBalance() {
            try {
                const response = await axios.get("/get-balance", {params: {user_id: this.user.id}});
                this.balance = response.data.balance;
            } catch (error) {
                alert("Błąd: " + error.response.data.error);
            }
        },
        handleMaxBet() {
            this.betAmount = this.balance;
        },
        // Metoda pobierająca aktualny stan ruletki/timera – używana przy wejściu nowego użytkownika
        async fetchCurrentSpin() {
            try {
                const response = await axios.get("/get-current-spin");
                if (response.data.spinning && response.data.startTime) {
                    const startTime = new Date(response.data.startTime).getTime();
                    const now = Date.now();
                    const elapsedTime = now - startTime;
                    if (elapsedTime < this.timerDuration) {
                        const remainingTime = this.timerDuration - elapsedTime;
                        // Jeśli timer jeszcze nie został ustawiony lokalnie, ustawiamy go
                        if (!this.timerActive) {
                            this.onTimerTick(remainingTime);
                        }
                    } else {
                        // Timer wygasł – ustawiamy pasek na 0
                        this.timerActive = false;
                        this.timerWidth = 0;
                    }
                }
            } catch (error) {
                console.error("Błąd podczas pobierania stanu ruletki:", error);
            }
        },
        // Metoda ustawiająca timer tylko raz – przy pierwszym ticku (lub przy wejściu nowego użytkownika)
        onTimerTick(timeLeftMs) {
            if (!this.timerActive) {
                const elapsed = this.timerDuration - timeLeftMs;
                this.timerStart = Date.now() - elapsed;
                this.timerEnd = this.timerStart + this.timerDuration;
                this.timerActive = true;
                this.startAnimation();
            }
            // Kolejne ticki nie zmieniają już timerStart/timerEnd – dzięki temu nie występują przeskoki.
        },
        // Płynna animacja paska przy użyciu requestAnimationFrame
        startAnimation() {
            const update = () => {
                const now = Date.now();
                const remaining = this.timerEnd - now;
                if (remaining <= 0) {
                    this.timerWidth = 0;
                    this.timerActive = false;
                    this.animationFrame = null;
                    return;
                }
                this.timerWidth = (remaining / this.timerDuration) * 100;
                this.animationFrame = requestAnimationFrame(update);
            };
            this.animationFrame = requestAnimationFrame(update);
        },
    },
};
</script>
