<script setup>
import Layout from "@/Layouts/Layout.vue";

// Lista liczb i przypisanie im kolorów (czarny / czerwony)
const numbers = [
    { number: 1, color: "red" },
    { number: 2, color: "black" },
    { number: 3, color: "red" },
    { number: 4, color: "black" },
    { number: 5, color: "red" },
    { number: 6, color: "black" },
    { number: 7, color: "red" },
    { number: 8, color: "black" },
    { number: 9, color: "red" },
    { number: 10, color: "black" },
    { number: 11, color: "red" },
    { number: 12, color: "black"},
    {number: 13, color: "red"},
    {number: 14, color: "black"},
    {number: 15, color: "red"},
    {number: 16, color: "black"},
    {number: 17, color: "red"},
    {number: 18, color: "black"},
    {number: 19, color: "red"},
    {number: 20, color: "black"},
];
</script>

<template>
    <Layout>
        <div class="container mx-auto min-h-screen p-6 flex flex-col items-center text-center">

            <div class="line mt-12 w-1 h-24 fixed bg-yellow-300">

            </div>
            <h1 class="text-4xl font-bold text-yellow-400 mb-6">Ruletka</h1>

            <div class="flex justify-center gap-4 overflow-x-auto whitespace-nowrap">
                <div
                    v-for="(item, index) in numbers"
                    :key="index"
                    :class="[
            'number w-16 h-16 flex items-center justify-center font-bold text-white rounded',
            item.color === 'red' ? 'bg-red-600' : 'bg-black'
          ]"
                >
                    {{ item.number }}
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.number {
    font-size: 24px;
}

.number.bg-red-600 {
    background-color: #e60000;
}

.number.bg-black {
    background-color: #333;
}
</style>
