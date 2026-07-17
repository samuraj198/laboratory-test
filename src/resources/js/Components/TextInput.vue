<script setup>
defineProps({
    type: {
        type: String,
        default: 'Text'
    },
    label: {
        type: String,
        default: 'Label'
    }
});

const model = defineModel();
</script>

<template>
    <label class="flex flex-col w-full">
        <span>{{ label }}</span>
        <input :type="type" v-model="model" >
    </label>
</template>

<style scoped>

</style>
