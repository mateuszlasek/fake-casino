<script>
import Layout from "@/Layouts/Layout.vue";
import axios from 'axios'; // Import Axiosa

export default {
    components: {Layout},
    data() {
        return {
            outcome: null,
            rows: Array(29).fill(null),
            cards: [
                { number: 1, color: 'red' },
                { number: 14, color: 'black' },
                { number: 2, color: 'red' },
                { number: 13, color: 'black' },
                { number: 3, color: 'red' },
                { number: 12, color: 'black' },
                { number: 4, color: 'red' },
                { number: 0, color: 'green' },
                { number: 11, color: 'black' },
                { number: 5, color: 'red' },
                { number: 10, color: 'black' },
                { number: 6, color: 'red' },
                { number: 9, color: 'black' },
                { number: 7, color: 'red' },
                { number: 8, color: 'black' },
            ],
            wheelStyles: {},
            totalBetRed: 0, // Suma zakładów na Red
            totalBetGreen: 0, // Suma zakładów na Green
            totalBetBlack: 0, // Suma zakładów na Black
            activeBets: [],
        }
    },
    methods: {
        async spin() {
            try {
                const response = await axios.get('/spin-wheel');
                this.outcome = response.data.number;

                const order = [0, 11, 5, 10, 6, 9, 7, 8, 1, 14, 2, 13, 3, 12, 4];
                const position = order.indexOf(parseInt(this.outcome));

                if (position === -1) {
                    alert('Nieprawidłowy wynik!');
                    return;
                }

                const rows = 12;
                const card = 75 + 3 * 2;
                const landingPosition = (rows * 15 * card) + (position * card);
                const randomize = Math.floor(Math.random() * 75) - (75 / 2);

                const object = {
                    x: Math.floor(Math.random() * 50) / 100,
                    y: Math.floor(Math.random() * 20) / 100
                };

                this.wheelStyles = {
                    transitionTimingFunction: `cubic-bezier(0,${object.x},${object.y},1)`,
                    transitionDuration: '6s',
                    transform: `translate3d(-${landingPosition + randomize}px, 0px, 0px)`
                };

                setTimeout(() => {
                    const resetTo = -(position * card + randomize);
                    this.wheelStyles = {
                        transitionTimingFunction: '',
                        transitionDuration: '',
                        transform: `translate3d(${resetTo}px, 0px, 0px)`
                    };
                }, 6000);

                if(this.activeBets.length !== 0) {
                    this.checkBets(this.activeBets, this.outcome);
                }
            } catch (error) {
                console.error('Błąd podczas pobierania wyniku:', error);
                alert('Wystąpił błąd podczas kręcenia kołem.');
            }
        },

        async placeBet(color) {
            try {
                const response = await axios.post('/place-bet', {
                    color: color,
                    amount: 10, // Stała kwota zakładu
                });

                this.activeBets.push(response.data.bet_id);

                alert('Zakład przyjęty! Nowe saldo: ' + response.data.new_balance);

                // Zaktualizuj Total Bet dla danego koloru
                this.updateTotalBet(color, response.data.total_bet);
            } catch (error) {
                alert('Błąd: ' + error.response.data.error);
            }
        },

        updateTotalBet(color, totalBet) {
            // Zaktualizuj Total Bet dla danego koloru
            if (color === 'red') {
                this.totalBetRed = totalBet;
            } else if (color === 'green') {
                this.totalBetGreen = totalBet;
            } else if (color === 'black') {
                this.totalBetBlack = totalBet;
            }
        },

        async checkBets(activeBets, winningNumber) {
            try {
                const response = await axios.post('/check-bets', {
                    activeBets: activeBets,
                    winningNumber: winningNumber
                });

            } catch (error) {
                alert('Błąd: ' + error.response.data.error);
            }
        }
    }
}
</script>

<template>
    <Layout>
        <div class="container mx-auto min-h-screen p-6 flex flex-col items-center text-center">
            <h1 class="text-4xl font-bold text-yellow-400 mb-6">Roulette</h1>

            <div class="w-full max-w-5xl px-4">
                <div class="roulette-wrapper">
                    <div class="selector"></div>
                    <div class="wheel" :style="wheelStyles">
                        <div
                            v-for="(row, index) in rows"
                            :key="index"
                            class="row"
                        >
                            <div
                                v-for="(card, cardIndex) in cards"
                                :key="cardIndex"
                                class="card"
                                :class="card.color"
                            >
                                {{ card.number }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="controls">
                    <button @click="spin">
                        Spin
                    </button>
                </div>
            </div>

            <!-- Sekcja z przyciskami i Total Bet -->
            <div class="flex flex-col space-y-4 w-full max-w-7xl px-4 text-white mt-8">
                <div class="flex justify-between space-x-4">
                    <div class="w-full">
                        <button
                            @click="placeBet('red')"
                            class="btn-color w-full h-12 bg-red-500 hover:bg-red-600 rounded">
                            Red
                        </button>
                        <div class="w-full bg-casino-2 mt-4 p-2 text-right rounded">
                            Total Bet: {{ totalBetRed }}
                        </div>
                    </div>
                    <div class="w-full">
                        <button
                            @click="placeBet('green')"
                            class="btn-color w-full h-12 bg-green-600 hover:bg-green-700 rounded">
                            Green
                        </button>
                        <div class="w-full bg-casino-2 mt-4 p-2 text-right rounded">
                            Total Bet: {{ totalBetGreen }}
                        </div>
                    </div>
                    <div class="w-full">
                        <button
                            @click="placeBet('black')"
                            class="btn-color w-full h-12 bg-gray-900 hover:bg-gray-800 rounded">
                            Black
                        </button>
                        <div class="w-full bg-casino-2 mt-4 p-2 text-right rounded">
                            Total Bet: {{ totalBetBlack }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>
body {
    font-family: 'Titillium Web', sans-serif;
    background: #191B28;
    margin: 0;
}

.roulette-wrapper {
    position: relative;
    display: flex;
    justify-content: center;
    width: 100%;
    margin: 0 auto;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 1rem 0;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
}

.roulette-wrapper .selector {
    width: 3px;
    background: grey;
    left: 50%;
    height: 100%;
    transform: translate(-50%, 0%);
    position: absolute;
    z-index: 2;
}

.roulette-wrapper .wheel {
    display: flex;
}

.roulette-wrapper .wheel .row {
    display: flex;
}

.roulette-wrapper .wheel .row .card {
    height: 75px;
    width: 75px;
    margin: 3px;
    border-radius: 8px;
    border-bottom: 3px solid rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5em;
}

.card.red { background: #F95146; }
.card.black { background: #2D3035; }
.card.green { background: #00C74D; }

.controls {
    margin-top: 20px;
    text-align: left;
}

.controls input {
    padding: 8px;
    margin-right: 10px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

.controls button {
    padding: 8px 16px;
    background: #00C74D;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

* {
    box-sizing: border-box;
}
</style>
