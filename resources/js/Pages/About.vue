<script setup>

import Layout from "@/Layouts/Layout.vue";
</script>

<template>
    <Layout>
      <div class="container mx-auto min-h-screen p-6 flex flex-col items-center text-center">
      <h1 class="text-4xl font-bold text-yellow-400 mb-6">About Virtual Casino</h1>

      <p class="text-lg text-gray-300 max-w-2xl">
        Virtual Casino is the ultimate destination for gaming enthusiasts who love excitement and thrill.
        Our platform offers a wide range of games, including Slots, Blackjack, Poker, and Roulette. Whether
        you're a seasoned player or a beginner, Virtual Casino provides a fair and immersive gaming experience.
      </p>

      <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-6 bg-gray-800 rounded-lg shadow-md">
          <h2 class="text-2xl font-semibold text-yellow-400">🎰 Our Games</h2>
          <p class="text-gray-400">We offer a variety of exciting games to test your luck and skill.</p>
        </div>
        <div class="p-6 bg-gray-800 rounded-lg shadow-md">
          <h2 class="text-2xl font-semibold text-yellow-400">🔒 Safe & Secure</h2>
          <p class="text-gray-400">We prioritize fair play and ensure a secure gaming environment.</p>
        </div>
        <div class="p-6 bg-gray-800 rounded-lg shadow-md">
          <h2 class="text-2xl font-semibold text-yellow-400">🎁 Promotions</h2>
          <p class="text-gray-400">Enjoy exclusive bonuses and rewards as you play.</p>
        </div>
        <div class="p-6 bg-gray-800 rounded-lg shadow-md">
          <h2 class="text-2xl font-semibold text-yellow-400">📞 Support</h2>
          <p class="text-gray-400">Our customer service team is here to help 24/7.</p>
        </div>
      </div>
    </div>
    </Layout>
</template>

<style scoped>

</style>
