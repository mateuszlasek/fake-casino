<script>
import Layout from "@/Layouts/Layout.vue";
import axios from 'axios';

export default {
  components: { Layout },
  props: {
    balance: Number,
    user: Object
  },
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
      totalBetRed: 0,
      totalBetGreen: 0,
      totalBetBlack: 0,
      activeBets: [],
      betAmount: 0,
      balance: this.$props.balance,
      redPlayerTable: {},
      greenPlayerTable: {},
      blackPlayerTable: {},
    };
  },
  methods: {
    async spin() {
      try {
        const response = await axios.post('/spin-wheel', {
          activeBets: this.activeBets
        });
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
        console.error('Błąd podczas pobierania wyniku:', error);
        alert('Wystąpił błąd podczas kręcenia kołem.');
      }
    },

    async placeBet(color) {
      try {
        const response = await axios.post('/place-bet', {
          color: color,
          amount: this.betAmount,
        });

        this.addToPlayerTable(color);

        this.activeBets.push(response.data.bet_id);

        alert('Zakład przyjęty! Nowe saldo: ' + response.data.new_balance);

        this.updateTotalBet(color, this.betAmount);

        this.balance = response.data.new_balance;

      } catch (error) {
        alert('Błąd: ' + error.response.data.error);
      }
    },

    addToPlayerTable(color) {
      if (color === 'red') {
        this.redPlayerTable[this.user.id] = (this.redPlayerTable[this.user.id] || 0) + this.betAmount;
      } else if (color === 'green') {
        this.greenPlayerTable[this.user.id] = (this.greenPlayerTable[this.user.id] || 0) + this.betAmount;
      } else if (color === 'black') {
        this.blackPlayerTable[this.user.id] = (this.blackPlayerTable[this.user.id] || 0) + this.betAmount;
      }
    },

    async updateBalance() {
      try {
        const response = await axios.get('/get-balance', {
          params: {
            user_id: this.user.id
          }
        });

        this.balance = response.data.balance;

      } catch (error) {
        alert('Błąd: ' + error.response.data.error);
      }
    },

    updateTotalBet(color, totalBet) {
      if (color === 'red') {
        this.totalBetRed += totalBet;
      } else if (color === 'green') {
        this.totalBetGreen += totalBet;
      } else if (color === 'black') {
        this.totalBetBlack += totalBet;
      }
    },

    clearBet() {
      this.betAmount = 0;
    },

    increaseBet(amount) {
      this.betAmount += amount;
    },

    doubleBet() {
      this.betAmount *= 2;
    },
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
      </div>

      <div class="controls w-full max-w-5xl px-4 mt-4">
        <button @click="spin" class="w-full h-12 bg-green-600 hover:bg-green-700 rounded text-white text-lg font-semibold">SPIN</button>
      </div>

      <div class="flex flex-col space-y-4 w-full max-w-7xl px-4 text-white mt-8">
        <!-- Balance & Bet Controls -->
        <div class="flex flex-col md:flex-row gap-4 w-full">
          <div class="w-full md:w-1/3 bg-casino-2 p-4 rounded-lg">
            <div class="text-xl font-bold text-yellow-400 mb-2">Balance</div>
            <div class="text-2xl">{{ balance }}</div>
          </div>

          <div class="w-full md:w-2/3 bg-casino-2 p-4 rounded-lg flex flex-col gap-3">
            <input
                v-model="betAmount"
                type="number"
                min="1"
                class="w-full bg-casino-3 text-white rounded-lg p-3 text-center text-lg"
                placeholder="Bet amount"
            />

            <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
              <button @click="clearBet" class="bg-gray-600 hover:bg-gray-700 rounded-lg p-2 text-sm md:text-base">Clear</button>
              <button @click="increaseBet(10)" class="bg-gray-600 hover:bg-gray-700 rounded-lg p-2 text-sm md:text-base">+10</button>
              <button @click="increaseBet(100)"
                      class="bg-gray-600 hover:bg-gray-700 rounded-lg p-2 text-sm md:text-base">+100
              </button>
              <button @click="doubleBet" class="bg-gray-600 hover:bg-gray-700 rounded-lg p-2 text-sm md:text-base">x2
              </button>
              <button @click="doubleBet"
                      class="bg-red-500 hover:bg-red-600 rounded-lg p-2 text-sm md:text-base col-span-2 md:col-span-1">
                MAX
              </button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
          <div class="bg-casino-2 rounded-lg p-4">
            <button @click="placeBet('red')"
                    class="w-full h-16 bg-red-500 hover:bg-red-600 rounded-lg text-xl font-bold mb-4">RED
            </button>
            <div class="bg-casino-3 rounded-lg p-3">
              <div class="flex justify-between mb-2">
                <span>Total Bet:</span>
                <span class="font-bold">{{ totalBetRed }}</span>
              </div>
              <div v-for="(amount, userId) in redPlayerTable" :key="userId" class="flex justify-between text-sm py-1">
                <span>{{ user.name }}</span>
                <span>{{ amount }}</span>
              </div>
            </div>
          </div>

          <div class="bg-casino-2 rounded-lg p-4">
            <button @click="placeBet('green')"
                    class="w-full h-16 bg-green-600 hover:bg-green-700 rounded-lg text-xl font-bold mb-4">GREEN
            </button>
            <div class="bg-casino-3 rounded-lg p-3">
              <div class="flex justify-between mb-2">
                <span>Total Bet:</span>
                <span class="font-bold">{{ totalBetGreen }}</span>
              </div>
              <div v-for="(amount, userId) in greenPlayerTable" :key="userId" class="flex justify-between text-sm py-1">
                <span>{{ user.name }}</span>
                <span>{{ amount }}</span>
              </div>
            </div>
          </div>

          <!-- Black Bet -->
          <div class="bg-casino-2 rounded-lg p-4">
            <button @click="placeBet('black')"
                    class="w-full h-16 bg-gray-900 hover:bg-gray-800 rounded-lg text-xl font-bold mb-4">BLACK
            </button>
            <div class="bg-casino-3 rounded-lg p-3">
              <div class="flex justify-between mb-2">
                <span>Total Bet:</span>
                <span class="font-bold">{{ totalBetBlack }}</span>
              </div>
              <div v-for="(amount, userId) in blackPlayerTable" :key="userId" class="flex justify-between text-sm py-1">
                <span>{{ user.name }}</span>
                <span>{{ amount }}</span>
              </div>
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
  border-bottom: 3px solid rgba(0, 0, 0, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5em;
}

.card.red {
  background: #F95146;
}

.card.black {
  background: #2D3035;
}

.card.green {
  background: #00C74D;
}

@media (max-width: 768px) {
  .roulette-wrapper .wheel .row .card {
    height: 60px;
    width: 60px;
    font-size: 1.2em;
  }
}

* {
  box-sizing: border-box;
}
</style>
