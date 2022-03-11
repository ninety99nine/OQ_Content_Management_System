<template>

    <div>

        <manage-topic-modal v-model="isShowingModal" :action="modalAction" :topic="topic" :parentTopic="parentTopic" :selectedLanguage="selectedLanguage" :languages="languages" />

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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center whitespace-nowrap">
                                <span>Sub-Topics</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Readers</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center whitespace-nowrap">
                                <span>% Readers</span>
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
                                <!-- Total Sub Topics -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <span class="text-2xl">{{ topic.sub_topics_count }}</span>
                                </td>
                                <!-- Total Seen -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <template v-if="topic.parent_topic_id">
                                        <span class="text-2xl">{{ topic.subscribers_count }}</span>
                                        <span class="text-gray-400"> / {{ totalSubscribers }}</span>
                                    </template>
                                    <span v-else class="text-gray-400">N/A</span>
                                </td>
                                <!-- Percentage -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <span v-if="topic.parent_topic_id" class="text-lg text-green-600">{{ getPercentageOfCoverage(topic.subscribers_count) }}%</span>
                                    <span v-else class="text-gray-400">N/A</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" @click.prevent="$inertia.get(route('topics', { project: route().params.project, topic: topic.id, _query: { language: showTopicsForSelectedLanguageName } }))" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
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
            languages: Array,
            parentTopic: Object,
            topicsPayload: Object,
            selectedLanguage: Object,
            totalSubscribers: Number,
        },
        data() {
            return {
                showTopicsForSelectedLanguageName: (this.selectedLanguage || {}).name,
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
