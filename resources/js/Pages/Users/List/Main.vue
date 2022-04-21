<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <create-user-modal :showAddbutton="true" :availablePermissions="availablePermissions" />

                <users-content :usersPayload="usersPayload" :availablePermissions="availablePermissions" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import UsersContent from './Partials/Content.vue'
    import CreateUserModal from './Partials/ManageUserModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            UsersContent,
            CreateUserModal
        },
        props: {
            availablePermissions: Array,
            usersPayload: Object
        }
    })
</script>
