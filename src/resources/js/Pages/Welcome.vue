<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import FormButton from "@/Components/FormButton.vue";

const form = useForm({
    name: '',
    phone: '',
    email: '',
    comment: ''
});

const submit = () => {
    form.post(route('contact.store'),
        {
            onSuccess: () => form.reset('comment')
        })
}
</script>

<template>
    <Head title="Главная страница" />
    <div class="flex flex-col items-center justify-center p-10">
        <form @submit.prevent="submit" class="flex flex-col items-center gap-6 w-[30%]">
            <TextInput required v-model="form.name" type="text" label="Имя" />
            <TextInput required v-model="form.phone" type="tel" label="Номер телефона" />
            <TextInput required v-model="form.email" type="email" label="Почта" />
            <TextInput required v-model="form.comment" type="text" label="Комменатрий" />
            <FormButton type="submit"
                        text="Отправить"
                        :disabled="form.name === '' ||
                        form.phone === '' ||
                        form.email === '' ||
                        form.comment === ''" />
        </form>
    </div>
</template>

<style scoped>

</style>
