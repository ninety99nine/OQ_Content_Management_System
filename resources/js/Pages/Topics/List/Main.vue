<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <manage-topic-modal :parentTopic="parentTopic" :topicsPayload="topicsPayload" :breadcrumbs="breadcrumbs" :showHeader="true" />

                <topics-content :parentTopic="parentTopic" :topicsPayload="topicsPayload" :breadcrumbs="breadcrumbs" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import TopicsContent from './Partials/Content.vue'
    import ManageTopicModal from './Partials/ManageTopicModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            TopicsContent,
            ManageTopicModal
        },
        props: {
            parentTopic: Object,
            topicsPayload: Object,
            breadcrumbs: Array
        }
    })
</script>
