<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <manage-message-modal :parentMessage="parentMessage" :messagesPayload="messagesPayload" :breadcrumbs="breadcrumbs" :showHeader="true" />

                <messages-content :parentMessage="parentMessage" :messagesPayload="messagesPayload" :breadcrumbs="breadcrumbs" />

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
            parentMessage: Object,
            messagesPayload: Object,
            breadcrumbs: Array
        }
    })
</script>
