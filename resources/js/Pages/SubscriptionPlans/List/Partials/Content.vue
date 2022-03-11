<template>

    <div>

        <manage-subscription-plan-modal v-model="isShowingModal" :action="modalAction" :subscriptionPlan="subscriptionPlan" />

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

            <!-- Table -->
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Name</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Duration</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Price</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Subscriptions</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Popularity</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Created</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                                <span>Actions</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">

                            <tr v-for="subscriptionPlan in subscriptionPlansPayload.data" :key="subscriptionPlan.id">
                                <!-- Name -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ subscriptionPlan.name }}</div>
                                </td>
                                <!-- Duration -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ getDuration(subscriptionPlan) }}
                                </td>
                                <!-- Price -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ subscriptionPlan.price }}
                                </td>
                                <!-- Total Subscriptions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ subscriptionPlan.subscriptions_count }}
                                </td>
                                <!-- Popularity -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <span class="text-lg text-green-600">{{ getPercentageOfCoverage(subscriptionPlan.subscriptions_count) }}%</span>
                                </td>
                                <!-- Created Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ subscriptionPlan.created_at == null ? '...' : moment(subscriptionPlan.created_at).fromNow() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" @click.prevent="showModal(subscriptionPlan, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a href="#" @click.prevent="showModal(subscriptionPlan, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>

                            <tr v-if="subscriptionPlansPayload.data.length == 0">
                                <!-- Content -->
                                <td :colspan="8" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-center text-gray-900 text-sm p-6">No subscription plans</div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="subscriptionPlansPayload" :updateData="['subscriptionPlansPayload']" />

        </div>

    </div>

</template>
<script>

    import ManageSubscriptionPlanModal from './ManageSubscriptionPlanModal.vue'
    import Pagination from '../../../../Partials/Pagination.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            ManageSubscriptionPlanModal, Pagination
        },
        props: {
            totalSubscriptions: Number,
            subscriptionPlansPayload: Object
        },
        data() {
            return {
                subscriptionPlan: null,
                isShowingModal: false,
                modalAction: null,
                moment: moment
            }
        },
        methods: {
            getDuration(subscriptionPlan){
                let duration = subscriptionPlan.duration;
                let frequency = subscriptionPlan.frequency;

                if( duration == 1 ){

                    return duration +' '+ frequency.substring(-1);

                }

                return duration +' '+ frequency;
            },
            showModal(subscriptionPlan, action){
                this.subscriptionPlan = subscriptionPlan;
                this.modalAction = action;
                this.isShowingModal = true
            },
            getPercentageOfCoverage(subscriptionsCount){
                if( this.totalSubscriptions > 0 ){
                    return Math.round((subscriptionsCount / this.totalSubscriptions) * 100)
                }

                return 0;
            }
        }
    })
</script>
