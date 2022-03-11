<template>

    <div>

        <!-- Add Topic Button -->
        <div v-if="showHeader" class="grid grid-cols-2 gap-4">

            <div v-if="showSelectLanguage && !parentTopic" class="flex items-center">

                <!-- Select Language -->
                <span>Topics For: </span>
                <el-select v-model="showTopicsForSelectedLanguageName" class="m-2" placeholder="Select" size="large"
                    @change="$inertia.get(route('topics', { project: route().params.project, _query: { language: showTopicsForSelectedLanguageName } }))">
                    <el-option v-for="language in languageOptions" :key="language.name" :value="language.name" :label="language.name"></el-option>
                </el-select>

            </div>

            <div v-if="parentTopic" class="bg-gray-50 pt-3 pl-6 border-b rounded-t">
                <div class="text-2xl font-semibold leading-6 text-gray-500">{{ parentTopic.title }}</div>
                <div class="text-sm text-gray-500 my-2">{{ parentTopic.content }}</div>
                <div class="text-sm text-gray-500 my-2">
                    <span class="font-bold mr-2">Api Link:</span>
                    <span class="text-green-500 font-semibold">{{ route('api.subtopics', { project: route().params.project, topic: parentTopic.id }) }}</span>
                </div>
            </div>

            <div :class="'grid grid-cols-' + (parentTopic ? 2 : 1) + ' flex items-center'">

                <div v-if="parentTopic">
                    <jet-secondary-button @click="goBackToPreviousPage()" class="mx-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                        </svg>
                        <span class="ml-2">Go Back</span>
                    </jet-secondary-button>
                </div>

                <div>
                    <jet-button @click="openModal()" class="float-right w-fit">
                        {{ showTopicsForSelectedLanguageName ? 'Add '+showTopicsForSelectedLanguageName+' Topic' : 'Add Topic' }}
                    </jet-button>
                </div>

            </div>

        </div>

        <div class="clear-both"></div>

        <div>

            <!-- Success Topic -->
            <div v-if="showSuccessTopic" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 mt-3" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Topic updated successfully</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Topic deleted successfully</strong>
                <strong v-else class="font-bold">Topic created successfully</strong>

                <span @click="showSuccessTopic = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Error Topic -->
            <div v-if="showErrorTopic" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 mt-3" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Topic update failed</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Topic delete failed</strong>
                <strong v-else class="font-bold">Topic creation failed</strong>

                <span @click="showSuccessTopic = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Dialog Modal -->
            <jet-dialog-modal :show="showModal" :closeable="false">

                <!-- Modal Title -->
                <template #title>

                    <template v-if="wantsToUpdate">Update Topic</template>

                    <template v-else-if="wantsToDelete">Delete Topic</template>

                    <template v-else>Add Topic</template>

                </template>

                <!-- Modal Content -->
                <template #content>

                    <template v-if="wantsToDelete">

                        <span class="block mt-6 mb-6">Are you sure you want to delete this topic?</span>

                        <p class="text-sm text-gray-500">{{ topic.title }}</p>

                    </template>

                    <template v-else>

                        <span class="block mt-6 mb-6">

                            <span>
                                You are {{ wantsToUpdate ? 'updating' : 'adding' }} a topic for
                                <span class="rounded-lg py-1 px-2 border border-green-400 text-green-500 text-sm">
                                    {{ parentTopic ? parentTopic.title : showTopicsForSelectedLanguageName }}
                                </span>
                            </span>

                            <span v-if="parentTopic">
                                <span class="px-2">in</span>
                                <span class="rounded-lg py-1 px-2 border border-green-400 text-green-500 text-sm">
                                    {{ showTopicsForSelectedLanguageName }}
                                </span>
                            </span>

                        </span>

                        <!-- Title -->
                        <div class="mb-4">
                            <jet-label for="title" value="Name" />
                            <jet-input id="title" type="text" class="mt-1 block w-full" v-model="form.title" />
                            <jet-input-error :message="form.errors.title" class="mt-2" />
                        </div>

                        <!-- Content -->
                        <div class="mb-4">
                            <jet-label for="content" value="Content" />
                            <jet-textarea id="content" class="mt-1 block w-full" v-model="form.content" />
                            <jet-input-error :topic="form.errors.content" class="mt-2" />

                            <!-- Other errors -->
                            <jet-input-error :topic="form.errors.language" class="mt-2" />
                            <jet-input-error :topic="form.errors.parent_topic_id" class="mt-2" />
                        </div>

                    </template>

                </template>

                <!-- Modal Footer -->
                <template #footer>

                    <jet-secondary-button @click="closeModal()" class="mr-2">
                        Cancel
                    </jet-secondary-button>

                    <jet-button v-if="!hasTopic" @click.prevent="create()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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
            topic: Object,
            languages: Array,
            parentTopic: Object,
            selectedLanguage: Object,
            action: {
                type: String,
                default: 'update'
            },
            modelValue: {
                type: Boolean,
                default: false
            },
            showHeader: {
                type: Boolean,
                default: false
            },
            showSelectLanguage: {
                type: Boolean,
                default: false
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

                showSuccessTopic: false,
                showErrorTopic: false,

                showTopicsForSelectedLanguageId: (this.selectedLanguage || {}).id,
                showTopicsForSelectedLanguageName: (this.selectedLanguage || {}).name,
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
            hasTopic(){
                return this.topic == null ? false : true;
            },
            wantsToUpdate(){
                return (this.hasTopic && this.action == 'update') ? true : false;
            },
            wantsToDelete(){
                return (this.hasTopic && this.action == 'delete') ? true : false;
            },
            languageOptions() {
                return this.languages.map(function(language){
                    return {
                        'name': language.name,
                        'value': language.id,
                    };
                });
            }
        },
        methods: {
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

                this.form.post(route('create-topic', { project: route().params.project }), options);
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

                this.form.put(route('update-topic', { project: route().params.project, topic_id: this.topic.id }), options);
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

                this.form.delete(route('delete-topic', { project: route().params.project, topic_id: this.topic.id }), options);
            },
            handleOnSuccess(){

                this.reset();
                this.closeModal();

                this.showSuccessTopic = true;

                setTimeout(() => {
                    this.showSuccessTopic = false;
                }, 3000);

            },
            handleOnError(){

                this.showErrorTopic = true;

                setTimeout(() => {
                    this.showErrorTopic = false;
                }, 3000);

            },
            reset() {
                this.form = this.$inertia.form({
                    title: this.hasTopic ? this.topic.title : null,
                    content: this.hasTopic ? this.topic.content : null,
                    parent_topic_id: this.parentTopic ? this.parentTopic.id : null,
                    language: this.hasTopic ? this.topic.language.id : this.showTopicsForSelectedLanguageId
                });
            },
        },
        created(){

            this.reset();

        }
    })
</script>
