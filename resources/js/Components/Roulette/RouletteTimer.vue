<template>
    <div v-if="timerActive" class="w-full bg-gray-200 rounded-full h-2 my-4">
        <div
            class="bg-green-500 h-full rounded-full transition-all duration-500"
            :style="{ width: timerWidth + '%' }"
        ></div>
    </div>
</template>
