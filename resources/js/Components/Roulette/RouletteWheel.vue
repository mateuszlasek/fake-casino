<template>
    <div class="roulette-wrapper">
        <div class="selector"></div>
        <div class="wheel" :style="wheelStyles">
            <div v-for="(row, index) in rows" :key="index" class="row">
                <div v-for="(card, cardIndex) in cards" :key="cardIndex" class="card" :class="card.color">
                    {{ card.number }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        wheelStyles: Object,
        rows: Array,
        cards: Array,
    },
};
</script>

<style scoped>
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
</style>
