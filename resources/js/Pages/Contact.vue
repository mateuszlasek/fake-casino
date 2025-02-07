<script setup>

import Layout from "@/Layouts/Layout.vue";
</script>

<template>
    <Layout>
        <div class="container mx-auto min-h-screen p-6 flex flex-col items-center text-center">
            <h1 class="text-4xl font-bold text-yellow-400 mb-6">Contact Us</h1>

            <p class="text-lg text-gray-300 max-w-2xl">
                Have questions or need support? Get in touch with our team. We are here to assist you 24/7!
            </p>

            <div class="mt-8 bg-gray-800 p-6 rounded-lg shadow-md w-full max-w-lg text-gray-500">
                <form action="#" method="POST" class="flex flex-col space-y-2 ">
                    <input type="text" name="name" placeholder="Your Name" class="rounded bg-gray-700 border border-gray-600">
                    <input type="email" name="email" placeholder="Your Email" class="rounded bg-gray-700 border border-gray-600">
                    <textarea name="message" rows="4" placeholder="Your Message" class="rounded bg-gray-700 border border-gray-600"></textarea>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-6 rounded-lg text-lg shadow-lg">
                        Send Message
                    </button>
                </form>
            </div>

            <p class="mt-6 text-gray-400">Or reach us via email: <span class="text-yellow-400">support@virtualcasino.com</span></p>

        </div>
    </Layout>
</template>

<style scoped>

</style>
