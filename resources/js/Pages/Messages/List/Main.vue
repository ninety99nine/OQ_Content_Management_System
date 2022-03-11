<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <manage-message-modal :showAddbutton="true" :languages="languages" />

                <messages-content :messagesPayload="messagesPayload" :languages="languages" :totalSubscribers="totalSubscribers" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import MessagesContent from './Partials/Content.vue'
    import ManageMessageModal from './Partials/ManageMessageModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            MessagesContent,
            ManageMessageModal
        },
        props: {
            languages: Array,
            messagesPayload: Object,
            totalSubscribers: Number,
        }
    })
</script>
