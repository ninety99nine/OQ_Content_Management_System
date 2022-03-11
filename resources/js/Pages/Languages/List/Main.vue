<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <manage-language-modal :showAddbutton="true" />

                <languages-content :languagesPayload="languagesPayload" :totalMessages="totalMessages" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import LanguagesContent from './Partials/Content.vue'
    import ManageLanguageModal from './Partials/ManageLanguageModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            LanguagesContent,
            ManageLanguageModal
        },
        props: {
            totalMessages: Number,
            languagesPayload: Object
        }
    })
</script>
