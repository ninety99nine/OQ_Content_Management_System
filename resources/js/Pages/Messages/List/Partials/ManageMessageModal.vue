<template>

    <div>

        <!-- Add Message Button -->
        <div v-if="showHeader" class="grid grid-cols-2 mb-6 gap-4">

            <div>
                <div class="bg-gray-50 pt-3 pl-6 border-b rounded-t">

                    <div class="text-2xl font-semibold leading-6 text-gray-500 mb-4">{{ parentMessage ? parentMessage.content : 'Messages' }}</div>

                    <template v-if="parentMessage">

                        <el-breadcrumb separator=">" class="mb-4">
                            <el-breadcrumb-item @click="nagivateToMessage()">
                                <span class="hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer">Messages</span>
                            </el-breadcrumb-item>

                            <el-breadcrumb-item v-for="breadcrumb in breadcrumbs" :key="breadcrumb.id" @click="nagivateToMessage(breadcrumb)">
                                <span class="hover:underline hover:text-green-600 text-green-500 font-semibold cursor-pointer">{{ breadcrumb.content }}</span>
                            </el-breadcrumb-item>
                        </el-breadcrumb>

                        <jet-secondary-button @click="goBackToPreviousPage()" class="py-1 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                            </svg>
                            <span class="ml-2">Go Back</span>
                        </jet-secondary-button>

                    </template>

                </div>

                <div class="bg-gray-50 border-b pl-6 py-3 rounded-t text-gray-500 text-sm mb-6">
                    <span class="font-bold mr-2">Api Link:</span>
                    <span v-if="parentMessage">{{ route('api.message', { project: route().params.project, message: parentMessage.id, type: 'children' }) }}</span>
                    <span v-else>{{ route('api.messages', { project: route().params.project }) }}</span>
                </div>
            </div>

            <div>
                <jet-button @click="openModal()" class="w-fit float-right">Add Message</jet-button>
                <div class="clear-both"></div>
            </div>

        </div>

        <div>

            <!-- Success Message -->
            <div v-if="showSuccessMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 mt-3" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Message updated successfully</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Message deleted successfully</strong>
                <strong v-else class="font-bold">Message created successfully</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Error Message -->
            <div v-if="showErrorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 mt-3" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Message update failed</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Message delete failed</strong>
                <strong v-else class="font-bold">Message creation failed</strong>

                <span @click="showSuccessMessage = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Dialog Modal -->
            <jet-dialog-modal :show="showModal" :closeable="false">

                <!-- Modal Title -->
                <template #title>

                    <template v-if="wantsToUpdate">Update Message</template>

                    <template v-else-if="wantsToDelete">Delete Message</template>

                    <template v-else>Add Message</template>

                </template>

                <!-- Modal Content -->
                <template #content>

                    <template v-if="wantsToDelete">

                        <span class="block mt-6 mb-6">Are you sure you want to delete this message?</span>

                        <p class="text-sm text-gray-500">{{ message.content }}</p>

                    </template>

                    <template v-else>

                        <span class="block mt-6 mb-6">

                            <span v-if="parentMessage">
                                You are {{ wantsToUpdate ? 'updating' : 'adding' }} a message for
                                <span class="rounded-lg py-1 px-2 border border-green-400 text-green-500 text-sm">
                                    {{ parentMessage.content }}
                                </span>
                            </span>

                            <div v-if="parentMessage" class="bg-gray-50 py-3 px-3 mt-6 mb-2">

                                <el-breadcrumb separator=">">
                                    <el-breadcrumb-item>
                                        <span class="hover:text-green-600 text-green-500 font-semibold">Messages</span>
                                    </el-breadcrumb-item>

                                    <el-breadcrumb-item v-for="breadcrumb in breadcrumbs" :key="breadcrumb.id">
                                        <span class="text-green-500 font-semibold">{{ breadcrumb.content }}</span>
                                    </el-breadcrumb-item>
                                </el-breadcrumb>

                            </div>

                        </span>

                        <!-- Content -->
                        <div class="mb-4">
                            <jet-label for="content" value="Content" />
                            <jet-textarea id="content" class="mt-1 block w-full" v-model="form.content" />
                            <jet-input-error :message="form.errors.content" class="mt-2" />

                            <!-- Other errors -->
                            <jet-input-error :message="form.errors.parent_message_id" class="mt-2" />
                        </div>

                    </template>

                </template>

                <!-- Modal Footer -->
                <template #footer>

                    <jet-secondary-button @click="closeModal()" class="mr-2">
                        Cancel
                    </jet-secondary-button>

                    <jet-button v-if="!hasMessage" @click.prevent="create()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
    import JetTextarea from '@/Jetstream/Textarea'
    import JetInputError from '@/Jetstream/InputError'
    import JetSelectInput from '@/Jetstream/SelectInput'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default defineComponent({
        components: {
            JetLabel, JetInput, JetTextarea, JetButton, JetInputError, JetSelectInput, JetDialogModal, JetSecondaryButton,
            JetDangerButton
        },
        props: {
            message: Object,
            action: String,
            parentMessage: Object,
            modelValue: Boolean,
            breadcrumbs: Array,
            showHeader: {
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
            hasMessage(){
                return this.message == null ? false : true;
            },
            wantsToUpdate(){
                return (this.hasMessage && this.action == 'update') ? true : false;
            },
            wantsToDelete(){
                return (this.hasMessage && this.action == 'delete') ? true : false;
            }
        },
        methods: {
            nagivateToMessage(message = null){
                if( message ){

                    this.$inertia.get(route('show-message', { project: route().params.project, message: message.id }));

                }else{

                    this.$inertia.get(route('messages', { project: route().params.project }));

                }
            },
            goBackToPreviousPage(){
                window.history.back();
            },
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

                this.form.post(route('create-message', { project: route().params.project }), options);
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

                this.form.put(route('update-message', { project: route().params.project, message_id: this.message.id }), options);
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

                this.form.delete(route('delete-message', { project: route().params.project, message_id: this.message.id }), options);
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
                    content: this.hasMessage ? this.message.content : null,
                    parent_id: this.parentMessage ? this.parentMessage.id : null
                });
            },
        },
        created(){

            this.reset();

        }
    })
</script>
