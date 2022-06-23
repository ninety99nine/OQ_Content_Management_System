<template>

    <div>

        <create-subscriber-modal v-model="isShowingModal" :action="modalAction" :subscriber="subscriber" />

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <!-- Table -->
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="w-1/2 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Mobile</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Subscriptions</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                <span>Last Subscription</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Messages</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Percentage</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                <span>Last Message</span>
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
                            <tr v-for="subscriber in subscribersPayload.data" :key="subscriber.id">
                                <!-- Mobile -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ subscriber.msisdn }}</div>
                                </td>
                                <!-- Subscriptions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ subscriber.subscriptions_count }}
                                </td>
                                <!-- Last Subscription -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ getLastSubscriptionDate(subscriber.latest_subscriptions) }}
                                </td>
                                <!-- Messages -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ subscriber.messages_count }}
                                </td>
                                <!-- Percentage -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <span class="text-lg text-green-600">{{ getPercentageOfCoverage(subscriber.messages_count) }}%</span>
                                </td>
                                <!-- Last Message -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ getLastMessageDate(subscriber.latest_messages) }}
                                </td>
                                <!-- Created Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ subscriber.created_at == null ? '...' : moment(subscriber.created_at).format('lll') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a v-if="$inertia.page.props.projectPermissions.includes('Manage subscribers')" href="#" @click.prevent="showModal(subscriber, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a v-if="$inertia.page.props.projectPermissions.includes('Manage subscribers')" href="#" @click.prevent="showModal(subscriber, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>

                            <tr v-if="subscribersPayload.data.length == 0">
                                <!-- Content -->
                                <td :colspan="8" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-center text-gray-900 text-sm p-6">No subscribers</div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="subscribersPayload" :updateData="['subscribersPayload']" />

        </div>

    </div>

</template>
<script>

    import CreateSubscriberModal from './ManageSubscriberModal.vue'
    import Pagination from '../../../../Partials/Pagination.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            CreateSubscriberModal, Pagination
        },
        props: {
            totalMessages: Number,
            subscribersPayload: Object
        },
        data() {
            return {
                refreshContentInterval: null,
                isShowingModal: false,
                modalAction: null,
                subscriber: null,
                moment: moment
            }
        },
        methods: {
            refreshContent()
            {
                this.$inertia.reload();
            },
            getLastSubscriptionDate(latestSubscriptions){
                if( latestSubscriptions.length > 0 ){
                    if( latestSubscriptions[0].created_at ){
                        return this.moment(latestSubscriptions[0].created_at).fromNow();
                    }
                }

                return '...';
            },
            getLastMessageDate(latestMessages){
                if( latestMessages.length > 0 ){
                    if( latestMessages[0].pivot ){
                        if( latestMessages[0].pivot.created_at ){
                            return this.moment(latestMessages[0].pivot.created_at).fromNow();
                        }
                    }
                }

                return '...';
            },
            getPercentageOfCoverage(MessagesCount){
                if( this.totalMessages > 0 ){
                    return Math.round((MessagesCount / this.totalMessages) * 100)
                }

                return 0;
            },
            showModal(subscriber, action){
                this.subscriber = subscriber;
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
        }
    })
</script>
