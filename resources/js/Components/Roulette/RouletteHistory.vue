<template>
    <div class="mt-4 w-full overflow-x-auto">
        <div class="flex gap-2 whitespace-nowrap justify-center">
            <div
                v-for="(color, index) in historyColors"
                :key="index"
                class="w-8 h-8 rounded-full shrink-0"
                :class="{
                    'bg-red-600': color === 'red',
                    'bg-green-600': color === 'green',
                    'bg-gray-900': color === 'black'
                }"
            ></div>
        </div>
    </div>
</template>

<script>
export default {
    name: "RouletteHistory",
    props: {
        historyColors: {
            type: Array,
            required: true,
        },
    },
};
</script>
