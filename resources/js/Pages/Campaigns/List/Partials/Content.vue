<template>

    <div>

        <manage-campaign-modal v-model="isShowingModal" :action="modalAction" :campaign="campaign" :subscriptionPlans="subscriptionPlans" :contentToSendOptions="contentToSendOptions" :scheduleTypeOptions="scheduleTypeOptions" />

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
                                <span>Status</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Sprints</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center bg-indigo-100">
                                <span>Total</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center bg-indigo-100">
                                <span>Pending</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center bg-indigo-100">
                                <span>Processed</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center bg-indigo-100">
                                <span>Progress</span>
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
                                <!-- Status -->
                                <td class="px-6 py-4">
                                    <CampaignBadge :campaignBatchJob="getLatestCampaignBatchJob(campaign)"></CampaignBadge>
                                </td>
                                <!-- Sprints -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <div class="text-sm text-gray-900">{{ campaign.campaign_batch_jobs_count }}</div>
                                </td>
                                <!-- Total -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <div class="text-sm text-gray-900">{{ getLatestCampaignBatchJob(campaign).total_jobs }}</div>
                                </td>
                                <!-- Pending -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <div class="text-sm text-gray-900">{{ getLatestCampaignBatchJob(campaign).pending_jobs }}</div>
                                </td>
                                <!-- Processed -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <div class="text-sm text-gray-900">{{ getLatestCampaignBatchJob(campaign).processed_jobs }}</div>
                                </td>
                                <!-- Progress -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <span class="text-lg text-green-600">{{ getLatestCampaignBatchJob(campaign).progress }} {{ getLatestCampaignBatchJob(campaign).progress ? '%' : '' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a v-if="$inertia.page.props.projectPermissions.includes('View campaigns')" href="#" @click.prevent="$inertia.get(route('show-campaign-job-batches', { project: route().params.project, campaign: campaign.id }))" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                    <a v-if="$inertia.page.props.projectPermissions.includes('Manage campaigns')" href="#" @click.prevent="showModal(campaign, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a v-if="$inertia.page.props.projectPermissions.includes('Manage campaigns')" href="#" @click.prevent="showModal(campaign, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
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

    import CampaignBadge from './../JobBatches/List/Partials/CampaignBadge.vue'
    import Pagination from '../../../../Partials/Pagination.vue'
    import ManageCampaignModal from './ManageCampaignModal.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            ManageCampaignModal, Pagination, CampaignBadge
        },
        props: {
            contentToSendOptions: Array,
            scheduleTypeOptions: Array,
            subscriptionPlans: Array,
            campaignsPayload: Object
        },
        data() {
            return {
                refreshContentInterval: null,
                isShowingModal: false,
                modalAction: null,
                campaign: null,
                moment: moment
            }
        },
        methods: {
            refreshContent()
            {
                this.$inertia.reload();
            },
            getLatestCampaignBatchJob(campaign)
            {
                if( campaign.latest_campaign_batch_job.length ) {
                    return campaign.latest_campaign_batch_job[0];
                }
                return {};
            },
            showModal(campaign, action)
            {
                this.campaign = campaign;
                this.modalAction = action;
                this.isShowingModal = true
            },
            cleanUp()
            {
                clearInterval( this.refreshContentInterval );
                this.refreshContentInterval = null;
            }
        },
        created() {

            //  Keep refreshing this page content every 3 seconds
            this.refreshContentInterval = setInterval(function() {
                this.refreshContent();
            }.bind(this), 3000);
        },
        unmounted() {
            this.cleanUp()
        },
        destroyed() {
            this.cleanUp()
        }
    })
</script>
