<template>

    <div>

        <create-project-modal v-model="isShowingModal" :action="modalAction" :project="project" />

        <div class="lg:w-1/2 w-full">
            <h1 role="heading" class="md:text-5xl text-3xl font-bold leading-10 mt-3 text-gray-800">My Projects</h1>
            <p role="contentinfo" class="text-base leading-5 mt-5 text-gray-600">Start managing your projects quick and easy, select your project and start creating content for automated sending to your subscribers.</p>
        </div>
        <div class="w-full mt-12 relative lg:mt-0" role="list">

            <img src="https://i.ibb.co/0n6DSS3/bgimg.png" class="absolute right-0 w-80" alt="background circle images" />

            <div class="grid grid-cols-3 gap-4 mt-12 mb-12 relative z-30">

                <a :href="route('messages', { project: project.id })" v-for="project in projectsPayload.data" :key="project.id" role="listitem" class="bg-white cursor-pointer shadow rounded-lg p-8 w-full aspect-video relative">

                    <h2 class="text-2xl font-semibold leading-6 text-gray-800">{{ project.name }}</h2>
                    <p class="md:w-80 text-base leading-6 mt-4 text-gray-600">{{ project.description }}</p>

                    <div class="absolute bottom-10 right-10">
                        <a href="#" @click.prevent="showModal(project, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <a href="#" @click.prevent="showModal(project, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                    </div>

                </a>

            </div>
        </div>

    </div>

</template>
<script>

    import CreateProjectModal from './ManageProjectModal.vue'
    import Pagination from '../../../../Partials/Pagination.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            CreateProjectModal, Pagination
        },
        props: {
            projectsPayload: Object
        },
        data() {
            return {
                isShowingModal: false,
                modalAction: null,
                project: null,
                moment: moment
            }
        },
        methods: {
            showModal(project, action){
                this.project = project;
                this.modalAction = action;
                this.isShowingModal = true
            }
        }
    })
</script>
