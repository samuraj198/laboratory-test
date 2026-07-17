<script setup>
defineProps({
    type: {
        type: String,
        default: 'type'
    },
    text: {
        type: String,
        default: 'Button'
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const model = defineModel();
</script>

<template>
    <button class="text-white rounded-xl px-5 py-3"
            :class="disabled ? 'bg-black/30 cursor-not-allowed' : 'cursor-pointer bg-black hover:bg-black/80'"
            :disabled="disabled"
            :type="type">{{ text }}</button>
</template>

<style scoped>

</style>
