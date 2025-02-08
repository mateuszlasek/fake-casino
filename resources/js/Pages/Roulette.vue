<template>
    <div>
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
            <input v-model.number="outcome" placeholder="Wpisz wynik">
            <button @click="spin">
                Zakręć kołem
            </button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            outcome: null,
            rows: Array(29).fill(null), // 29 rzędów jak w oryginalnym kodzie
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
            wheelStyles: {}
        }
    },
    methods: {
        spin() {
            const order = [0, 11, 5, 10, 6, 9, 7, 8, 1, 14, 2, 13, 3, 12, 4]
            const position = order.indexOf(parseInt(this.outcome))

            if (position === -1) {
                alert('Nieprawidłowy wynik!')
                return
            }

            const rows = 12
            const card = 75 + 3 * 2
            const landingPosition = (rows * 15 * card) + (position * card)
            const randomize = Math.floor(Math.random() * 75) - (75/2)

            const object = {
                x: Math.floor(Math.random() * 50) / 100,
                y: Math.floor(Math.random() * 20) / 100
            }

            this.wheelStyles = {
                transitionTimingFunction: `cubic-bezier(0,${object.x},${object.y},1)`,
                transitionDuration: '6s',
                transform: `translate3d(-${landingPosition + randomize}px, 0px, 0px)`
            }

            setTimeout(() => {
                const resetTo = -(position * card + randomize)
                this.wheelStyles = {
                    transitionTimingFunction: '',
                    transitionDuration: '',
                    transform: `translate3d(${resetTo}px, 0px, 0px)`
                }
            }, 6000)
        }
    }
}
</script>

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
    text-align: center;
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
