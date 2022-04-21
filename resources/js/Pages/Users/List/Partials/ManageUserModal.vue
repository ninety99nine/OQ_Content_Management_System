<template>

    <div>

        <!-- Add User Button -->
        <jet-button v-if="$inertia.page.props.projectPermissions.includes('Manage users') && showAddbutton" @click="openModal()" class="float-right mb-6">
            Add User
        </jet-button>

        <div class="clear-both">

            <!-- Success Message -->
            <div v-if="showSuccessMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">User updated successfully</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">User deleted successfully</strong>
                <strong v-else class="font-bold">User created successfully</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Error Message -->
            <div v-if="showErrorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">User update failed</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">User delete failed</strong>
                <strong v-else class="font-bold">User creation failed</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Dialog Modal -->
            <jet-dialog-modal :show="showModal" :closeable="false">

                <!-- Modal Title -->
                <template #title>

                    <template v-if="wantsToUpdate">Update User</template>

                    <template v-else-if="wantsToDelete">Delete User</template>

                    <template v-else>Add User</template>

                </template>

                <!-- Modal Content -->
                <template #content>

                    <template v-if="wantsToDelete">

                        <span class="block mt-6 mb-6">Are you sure you want to delete this user?</span>

                        <p class="text-sm text-gray-500">{{ user.name }}</p>

                    </template>

                    <template v-else>

                        <!-- Name -->
                        <div class="mb-4">
                            <jet-label for="name" value="Name" />
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" placeholder = "John Doe" />
                            <jet-input-error :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <jet-label for="email" value="Email" />
                            <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" placeholder = "example@gmail.com" />
                            <jet-input-error :message="form.errors.email" class="mt-2" />
                        </div>

                        <!-- Permissions -->
                        <div class="mt-10 mb-10">
                            <el-divider content-position="left"><span class="font-semibold">Permissions</span></el-divider>
                            <jet-input-error :message="form.errors.permissions" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div v-for="(availablePermission, index) in availablePermissions" :key="index" class="flex items-center">
                                <input v-model="form.permissions" :value="availablePermission" :name="availablePermission" type="checkbox" :checked="form.permissions.includes(availablePermission)" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                                <label :for="availablePermission" class="ml-3 block text-sm font-medium text-gray-700">{{ availablePermission }}</label>
                            </div>
                        </div>

                        <!-- Note -->
                        <el-divider content-position="left" class="mt-10 mb-10"></el-divider>

                        <div>
                            <span class="font-bold mr-2">Note:</span>
                            <span>When the user account does not exist, a new account is created. The user can login in using their email and the word</span>
                            <span class="font-bold text-green-600 font-italic mx-2">password</span>
                            <span>as their default password. Otherwise if the user already has an existing account, they may use their usual password.</span>
                        </div>

                    </template>

                </template>

                <!-- Modal Footer -->
                <template #footer>

                    <jet-secondary-button @click="closeModal()" class="mr-2">
                        Cancel
                    </jet-secondary-button>

                    <jet-button v-if="!hasUser" @click.prevent="create()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create
                    </jet-button>

                    <jet-button v-if="wantsToUpdate" @click.prevent="update()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Update
                    </jet-button>

                    <jet-danger-button v-if="wantsToDelete" @click.prevent="destroy()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Delete
                    </jet-danger-button>

                </template>

            </jet-dialog-modal>

        </div>

    </div>

</template>

<script>

    import { defineComponent } from 'vue'

    import JetLabel from '@/Jetstream/Label'
    import JetInput from '@/Jetstream/Input'
    import JetButton from '@/Jetstream/Button'
    import JetInputError from '@/Jetstream/InputError'
    import JetSelectInput from '@/Jetstream/SelectInput'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default defineComponent({
        components: {
            JetLabel, JetInput, JetButton, JetInputError, JetSelectInput, JetDialogModal, JetSecondaryButton, JetActionMessage,
            JetDangerButton
        },
        props: {
            action: {
                type: String,
                default: 'update'
            },
            availablePermissions: {
                type: Array
            },
            modelValue: {
                type: Boolean,
                default: false
            },
            showAddbutton: {
                type: Boolean,
                default: false
            },
            user: {
                type: Object,
                default: null
            },
            show: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {

                //  Form attributes
                form: null,

                //  Modal attributes
                showModal: this.modelValue,

                showSuccessMessage: false,
                showErrorMessage: false
            }
        },

        watch: {

            showModal: {
                handler: function (val, oldVal) {

                    if(val != this.modelValue){
                        this.$emit('update:modelValue', val);
                    }

                }
            },

            modelValue: {
                handler: function (val, oldVal) {

                    if(val != this.showModal){
                        this.showModal = val;
                        this.reset();
                    }

                }
            },

        },

        computed: {
            hasUser(){
                return this.user == null ? false : true;
            },
            wantsToUpdate(){
                return (this.hasUser && this.action == 'update') ? true : false;
            },
            wantsToDelete(){
                return (this.hasUser && this.action == 'delete') ? true : false;
            }
        },
        methods: {
            /**
             *  MODAL METHODS
             */
            openModal() {
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
            },

            /**
             *  FORM METHODS
             */
            create() {
                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },

                };

                this.form.post(route('create-user', { project: route().params.project }), options);
            },
            update() {
                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },
                };

                this.form.put(route('update-user', { project: route().params.project, user: this.user.id }), options);
            },
            destroy() {

                var options = {

                    preserveState: true, preserveScroll: true, replace: true,

                    onSuccess: (response) => {

                        this.handleOnSuccess();

                    },

                    onError: errors => {

                        this.handleOnError();

                    },
                };

                this.form.delete(route('delete-user', { project: route().params.project, user: this.user.id }), options);
            },
            handleOnSuccess(){

                this.reset();
                this.closeModal();

                this.showSuccessMessage = true;

                setTimeout(() => {
                    this.showSuccessMessage = false;
                }, 3000);

            },
            handleOnError(){

                this.showErrorMessage = true;

                setTimeout(() => {
                    this.showErrorMessage = false;
                }, 3000);

            },
            reset() {
                this.form = this.$inertia.form({
                    name: this.hasUser ? this.user.name : null,
                    email: this.hasUser ? this.user.email : null,
                    permissions: this.hasUser ? this.user.pivot.permissions : [],
                });
            },
        },
        created(){

            this.reset();

        }
    })
</script>
