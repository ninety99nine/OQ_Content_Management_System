<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <manage-topic-modal :showHeader="true" :showSelectLanguage="true" :parentTopic="topic" :selectedLanguage="selectedLanguage" :languages="languages" />

                <topics-content :parentTopic="topic" :topicsPayload="topicsPayload" :selectedLanguage="selectedLanguage" :languages="languages" :totalSubscribers="totalSubscribers" />

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
            topic: Object,
            languages: Array,
            hasTopic: Boolean,
            topicsPayload: Object,
            totalSubscribers: Number,
            selectedLanguage: Object
        }
    })
</script>
