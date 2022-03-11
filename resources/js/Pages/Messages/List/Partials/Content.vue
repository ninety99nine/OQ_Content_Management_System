<template>

    <div>

        <manage-message-modal v-model="isShowingModal" :action="modalAction" :message="message" :languages="languages" />

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
                                <span></span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Message</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Language</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Subscribers</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Percentage</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Schedule</span>
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
                            <tr v-for="message in messagesPayload.data" :key="message.id">
                                <!-- Content -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">#{{ message.id }}</div>
                                </td>
                                <!-- Content -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ message.content }}</div>
                                </td>
                                <!-- Language -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-'+getLanguageColor(message.language)+'-100 text-'+getLanguageColor(message.language)+'-800'">
                                        {{ message.language.name }}
                                    </span>
                                </td>
                                <!-- Sent -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <span class="text-2xl">{{ message.subscribers_count }}</span>
                                    <span class="text-gray-400"> / {{ totalSubscribers }}</span>
                                </td>
                                <!-- Percentage -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <span class="text-lg text-green-600">{{ getPercentageOfCoverage(message.subscribers_count) }}%</span>
                                </td>
                                <!-- Frequency -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Everyday
                                </td>
                                <!-- Created Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ message.created_at == null ? '...' : moment(message.created_at).format('ll') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" @click.prevent="showModal(message, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a href="#" @click.prevent="showModal(message, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>

                            <tr v-if="messagesPayload.data.length == 0">
                                <!-- Content -->
                                <td :colspan="7" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-center text-gray-900 text-sm p-6">No messages</div>
                                </td>
                            </tr>


                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="messagesPayload" :updateData="['messagesPayload']" />

        </div>

    </div>

</template>
<script>

    import Pagination from '../../../../Partials/Pagination.vue'
    import ManageMessageModal from './ManageMessageModal.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            ManageMessageModal, Pagination
        },
        props: {
            languages: Array,
            totalSubscribers: Number,
            messagesPayload: Object
        },
        data() {
            return {
                isShowingModal: false,
                modalAction: null,
                message: null,
                moment: moment
            }
        },
        methods: {
            showModal(message, action){
                this.message = message;
                this.modalAction = action;
                this.isShowingModal = true
            },
            getLanguageColor(language){
                if( language.name == 'English' ){

                    return 'green';

                }else if( language.name == 'Setswana' ){

                    return 'blue';

                }else{

                    return 'lime';

                }
            },
            getPercentageOfCoverage(subscribersCount){
                if( this.totalSubscribers > 0 ){
                    return Math.round((subscribersCount / this.totalSubscribers) * 100)
                }

                return 0;
            }
        }
    })
</script>
