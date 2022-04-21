<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <projects-content :project="project" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import ProjectsContent from './Partials/Content.vue'

    export default defineComponent({
        components: {
            AppLayout,
            ProjectsContent
        },
        props: {
            project: Object
        }
    })
</script>
