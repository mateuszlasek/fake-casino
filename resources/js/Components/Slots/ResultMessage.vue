<template>
    <div v-if="message" class="mt-6 text-xl font-semibold text-yellow-400 animate-pulse">
        {{ message }}
    </div>
</template>

<script setup>
defineProps({
    message: String
});
</script>
