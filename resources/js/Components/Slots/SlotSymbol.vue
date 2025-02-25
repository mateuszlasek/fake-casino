<template>
    <div class="symbol h-20 flex items-center justify-center text-4xl">
        {{ symbol }}
    </div>
</template>

<script setup>
defineProps({
    symbol: String
});
</script>
