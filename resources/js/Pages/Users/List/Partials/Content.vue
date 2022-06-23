<template>

    <div>

        <create-user-modal v-model="isShowingModal" :action="modalAction" :user="user" :availablePermissions="availablePermissions" />

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
                                <span>Name</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Email</span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                <span>Permissions</span>
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
                            <tr v-for="user in usersPayload.data" :key="user.id">
                                <!-- Name -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ user.name }}</div>
                                </td>
                                <!-- Email -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ user.email }}</div>
                                </td>
                                <!-- Permissions -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                <el-popover
                                    placement="right"
                                    width="200"
                                    trigger="hover">
                                    <template v-slot:reference>
                                        <div>
                                            <span class="text-lg text-green-600">{{ user.pivot.permissions.length }}</span>
                                            <span class="text-gray-400 mx-2">/</span>
                                            <span class="text-sm text-gray-400">{{ availablePermissions.length }}</span>
                                        </div>
                                    </template>

                                    <div>
                                        <div v-for="(availablePermission, index) in availablePermissions" :key="index" class="flex items-center">
                                            <svg v-if="user.pivot.permissions.includes(availablePermission)" xmlns="http://www.w3.org/2000/svg" class="text-green-600 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="text-gray-300 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-3 block text-sm font-medium text-gray-700">{{ availablePermission }}</span>
                                        </div>
                                    </div>

                                </el-popover>
                                </td>
                                <!-- Created Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ user.created_at == null ? '...' : moment(user.created_at).format('lll') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a v-if="$inertia.page.props.projectPermissions.includes('Manage users')" href="#" @click.prevent="showModal(user, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a v-if="$inertia.page.props.projectPermissions.includes('Manage users')" href="#" @click.prevent="showModal(user, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>

                            <tr v-if="usersPayload.data.length == 0">
                                <!-- Content -->
                                <td :colspan="8" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-center text-gray-900 text-sm p-6">No users</div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="usersPayload" :updateData="['usersPayload']" />

        </div>

    </div>

</template>
<script>

    import CreateUserModal from './ManageUserModal.vue'
    import Pagination from '../../../../Partials/Pagination.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            CreateUserModal, Pagination
        },
        props: {
            availablePermissions: Array,
            usersPayload: Object
        },
        data() {
            return {
                refreshContentInterval: null,
                isShowingModal: false,
                modalAction: null,
                user: null,
                moment: moment
            }
        },
        methods: {
            refreshContent()
            {
                this.$inertia.reload();
            },
            showModal(user, action){
                this.user = user;
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
