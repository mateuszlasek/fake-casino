<script setup>

import Layout from "@/Layouts/Layout.vue";
</script>

<template>
   <Layout>
       <div>
         <div class="container mx-auto flex flex-col md:flex-row items-center justify-between min-h-screen p-6">
           <div class="w-full md:w-1/2 flex justify-center">
             <img src="{{ asset('storage/images/cards.jpg') }}" alt="Placeholder for dice and cards" class="rounded-lg shadow-lg">
           </div>

           <div class="w-full md:w-1/2 text-center md:text-left">
             <h1 class="text-4xl font-bold text-yellow-400">Welcome to Fake Casino!</h1>
             <p class="mt-4 text-lg text-gray-300">The place where excitement meets the chance to win big! Try your luck in our thrilling games.</p>

             <button class="mt-6 bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-6 rounded-lg text-lg shadow-lg">
               Play Now
             </button>

             <div class="mt-8 grid grid-cols-2 gap-4">
               <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                 <h2 class="text-xl font-semibold text-yellow-400">🎰 Slots</h2>
                 <p class="text-gray-400">Spin and win big prizes!</p>
               </div>
               <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                 <h2 class="text-xl font-semibold text-yellow-400">🃏 Blackjack</h2>
                 <p class="text-gray-400">Beat the dealer and claim your winnings!</p>
               </div>
               <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                 <h2 class="text-xl font-semibold text-yellow-400">🎲 Roulette</h2>
                 <p class="text-gray-400">Bet on your lucky number!</p>
               </div>
               <div class="p-4 bg-gray-800 rounded-lg shadow-md">
                 <h2 class="text-xl font-semibold text-yellow-400">♠️ Poker</h2>
                 <p class="text-gray-400">Test your skills!</p>
               </div>
             </div>
           </div>
         </div>
       </div>
   </Layout>
</template>

<style scoped>

</style>
