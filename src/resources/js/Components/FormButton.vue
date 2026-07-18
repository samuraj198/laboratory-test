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
    },
    processing: {
        type: Boolean,
        default: false
    }
});

const model = defineModel();
</script>

<template>
    <button class="text-white rounded-xl px-5 py-3 flex gap-4"
            :class="disabled || processing ? 'bg-black/30 cursor-not-allowed' :
                                             'cursor-pointer bg-black hover:bg-black/80'"
            :disabled="disabled || processing"
            :type="type">
        {{ text }}
        <svg v-if="processing" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,
            0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z" stroke="white" fill="white">
                <animateTransform attributeName="transform"
                                  type="rotate"
                                  dur="0.75s"
                                  values="0 12 12;360 12 12"
                                  repeatCount="indefinite"/>
            </path>
        </svg>
    </button>
</template>

<style scoped>

</style>
