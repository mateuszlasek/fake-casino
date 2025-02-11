<template>
    <div class="w-full md:w-1/3 mt-4 p-4 text-left rounded bg-casino-2">
        Balance: <span class="font-bold">{{ balance }}</span>
    </div>
</template>

<script>
export default {
    name: "BalanceDisplay",
    props: {
        balance: {
            type: Number,
            required: true
        }
    }
};
</script>
