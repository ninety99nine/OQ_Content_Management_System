<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <campaigns-content :campaignBatchJobsPayload="campaignBatchJobsPayload" :campaign="campaign" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import CampaignsContent from './Partials/Content.vue'

    export default defineComponent({
        components: {
            AppLayout,
            CampaignsContent
        },
        props: {
            campaign: Object,
            campaignBatchJobsPayload: Object
        }
    })
</script>
