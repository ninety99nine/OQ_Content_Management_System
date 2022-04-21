<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <create-project-modal :showAddbutton="true" />

                <projects-content :projectsPayload="projectsPayload" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import ProjectsContent from './Partials/Content.vue'
    import CreateProjectModal from './Partials/ManageProjectModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            ProjectsContent,
            CreateProjectModal
        },
        props: {
            projectsPayload: Object
        }
    })
</script>
