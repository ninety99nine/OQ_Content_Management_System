<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <manage-subscription-plan-modal :showAddbutton="true" />

                <subscriptionPlans-content :subscriptionPlansPayload="subscriptionPlansPayload" :totalSubscriptions="totalSubscriptions" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubscriptionPlansContent from './Partials/Content.vue'
    import ManageSubscriptionPlanModal from './Partials/ManageSubscriptionPlanModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            SubscriptionPlansContent,
            ManageSubscriptionPlanModal
        },
        props: {
            totalSubscriptions: Number,
            subscriptionPlansPayload: Object
        }
    })
</script>
