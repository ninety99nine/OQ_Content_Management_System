<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <create-subscription-modal :showAddbutton="true" :subscriptionPlans="subscriptionPlans" />

                <subscriptions-content :subscriptionsPayload="subscriptionsPayload" :subscriptionPlans="subscriptionPlans" :totalSubscribers="totalSubscribers" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubscriptionsContent from './Partials/Content.vue'
    import CreateSubscriptionModal from './Partials/ManageSubscriptionModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            SubscriptionsContent,
            CreateSubscriptionModal
        },
        props: {
            subscriptionPlans: Array,
            totalSubscribers: Number,
            subscriptionsPayload: Object
        }
    })
</script>
