<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <create-subscriber-modal :showAddbutton="true" />

                <subscribers-content :subscribersPayload="subscribersPayload" :totalMessages="totalMessages" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubscribersContent from './Partials/Content.vue'
    import CreateSubscriberModal from './Partials/ManageSubscriberModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            SubscribersContent,
            CreateSubscriberModal
        },
        props: {
            totalMessages: Number,
            subscribersPayload: Object
        }
    })
</script>
