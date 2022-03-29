<template>

    <div>

        <manage-topic-modal v-model="isShowingModal" :action="modalAction" :topic="topic" :parentTopic="parentTopic" :breadcrumbs="breadcrumbs" />

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
                                <span>Title</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Content</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                                <span>Actions</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="topic in topicsPayload.data" :key="topic.id">
                                <!-- Title -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ topic.title }}</div>
                                </td>
                                <!-- Content -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ topic.content }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" @click.prevent="$inertia.get(route('show-topic', { project: route().params.project, topic: topic.id }))" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                    <a href="#" @click.prevent="showModal(topic, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a href="#" @click.prevent="showModal(topic, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>

                            <tr v-if="topicsPayload.data.length == 0">
                                <!-- Content -->
                                <td :colspan="7" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-center text-gray-900 text-sm p-6">No topics</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="topicsPayload" :updateData="['topicsPayload']" />

        </div>

    </div>

</template>
<script>

    import Pagination from '../../../../Partials/Pagination.vue'
    import ManageTopicModal from './ManageTopicModal.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            ManageTopicModal, Pagination
        },
        props: {
            parentTopic: Object,
            topicsPayload: Object,
            breadcrumbs: Array
        },
        data() {
            return {
                isShowingModal: false,
                modalAction: null,
                topic: null,
                moment: moment
            }
        },
        methods: {
            showModal(topic, action){
                this.topic = topic;
                this.modalAction = action;
                this.isShowingModal = true
            }
        }
    })
</script>
