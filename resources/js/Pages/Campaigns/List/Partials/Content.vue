<template>

    <div>

        <manage-campaign-modal v-model="isShowingModal" :action="modalAction" :campaign="campaign" :subscriptionPlans="subscriptionPlans" />

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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Description</span>
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
                            <tr v-for="campaign in campaignsPayload.data" :key="campaign.id">
                                <!-- Name -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ campaign.name }}</div>
                                </td>
                                <!-- Description -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ campaign.description }}</div>
                                </td>
                                <!-- Created Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ campaign.created_at == null ? '...' : moment(campaign.created_at).format('ll') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" @click.prevent="showModal(campaign, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a href="#" @click.prevent="showModal(campaign, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>

                            <tr v-if="campaignsPayload.data.length == 0">
                                <!-- Content -->
                                <td :colspan="7" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-center text-gray-900 text-sm p-6">No campaigns</div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="campaignsPayload" :updateData="['campaignsPayload']" />

        </div>

    </div>

</template>
<script>

    import Pagination from '../../../../Partials/Pagination.vue'
    import ManageCampaignModal from './ManageCampaignModal.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            ManageCampaignModal, Pagination
        },
        props: {
            subscriptionPlans: Array,
            campaignsPayload: Object
        },
        data() {
            return {
                isShowingModal: false,
                modalAction: null,
                campaign: null,
                moment: moment
            }
        },
        methods: {
            showModal(campaign, action){
                this.campaign = campaign;
                this.modalAction = action;
                this.isShowingModal = true
            }
        }
    })
</script>
