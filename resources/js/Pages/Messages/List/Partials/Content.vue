<template>

    <div>

        <manage-message-modal v-model="isShowingModal" :action="modalAction" :message="message" :parentMessage="parentMessage" :breadcrumbs="breadcrumbs" />

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
                                <span>Content</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                                <span>Actions</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="message in messagesPayload.data" :key="message.id">
                                <!-- Content -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ message.content }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" @click.prevent="$inertia.get(route('show-message', { project: route().params.project, message: message.id }))" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
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
            parentMessage: Object,
            messagesPayload: Object,
            breadcrumbs: Array
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
            }
        }
    })
</script>
