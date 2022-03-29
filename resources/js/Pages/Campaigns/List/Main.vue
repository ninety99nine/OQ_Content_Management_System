<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <manage-campaign-modal :showAddbutton="true" :subscriptionPlans="subscriptionPlans" :contentToSendOptions="contentToSendOptions" :scheduleTypeOptions="scheduleTypeOptions" />

                <campaigns-content :campaignsPayload="campaignsPayload" :subscriptionPlans="subscriptionPlans" :contentToSendOptions="contentToSendOptions" :scheduleTypeOptions="scheduleTypeOptions" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import CampaignsContent from './Partials/Content.vue'
    import ManageCampaignModal from './Partials/ManageCampaignModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            CampaignsContent,
            ManageCampaignModal
        },
        props: {
            contentToSendOptions: Array,
            scheduleTypeOptions: Array,
            subscriptionPlans: Array,
            campaignsPayload: Object
        }
    })
</script>
