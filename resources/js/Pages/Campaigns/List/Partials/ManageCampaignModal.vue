<template>

    <div>

        <!-- Add Campaign Button -->
        <jet-button v-if="showAddbutton" @click="openModal()" class="float-right mb-6">
            Add Campaign
        </jet-button>

        <div class="clear-both">

            <!-- Success Campaign -->
            <div v-if="showSuccessCampaign" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Campaign updated successfully</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Campaign deleted successfully</strong>
                <strong v-else class="font-bold">Campaign created successfully</strong>

                <span @click="showSuccessCampaign = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Error Campaign -->
            <div v-if="showErrorCampaign" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong v-if="wantsToUpdate" class="font-bold">Campaign update failed</strong>
                <strong v-else-if="wantsToDelete" class="font-bold">Campaign delete failed</strong>
                <strong v-else class="font-bold">Campaign creation failed</strong>

                <span @click="showSuccessCampaign = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>

            <!-- Dialog Modal -->
            <jet-dialog-modal :show="showModal" :closeable="false">

                <!-- Modal Title -->
                <template #title>

                    <template v-if="wantsToUpdate">Update Campaign</template>

                    <template v-else-if="wantsToDelete">Delete Campaign</template>

                    <template v-else>Add Campaign</template>


                </template>

                <!-- Modal Content -->
                <template #content>

                    <template v-if="wantsToDelete">

                        <span class="block mt-6 mb-6">Are you sure you want to delete this campaign?</span>

                        <p class="text-sm text-gray-500">{{ campaign.content }}</p>

                    </template>

                    <template v-else>

                        <!-- Name -->
                        <div class="mb-4">
                            <jet-label for="name" value="Name" />
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" />
                            <jet-input-error :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <jet-label for="description" value="Description" />
                            <jet-textarea id="description" class="mt-1 block w-full" v-model="form.description" />
                            <jet-input-error :message="form.errors.description" class="mt-2" />
                        </div>

                        <div class="mt-10 mb-10">

                            <el-divider content-position="left"><span class="font-semibold">Schedule</span></el-divider>

                        </div>

                        <!-- Frequency -->
                        <div class="mb-4">

                            <div class="flex items-center">

                                <span class="block whitespace-nowrap text-sm text-gray-700 mr-4">Send every</span>

                                <div class="mr-4">
                                    <el-input-number v-model="form.duration" :min="1" controls-position="right" />
                                    <jet-input-error :message="form.errors.duration" />
                                </div>

                                <div class="w-full">
                                    <el-select v-model="form.frequency" clearable placeholder="Select frequency" class="w-full">
                                        <el-option v-for="option in frequencyOptions" :key="option.value" :label="option.name" :value="option.value" ></el-option>
                                    </el-select>
                                    <jet-input-error :message="form.errors.frequency" />
                                </div>

                            </div>

                        </div>

                        <!-- Days Of The Week -->
                        <div class="mb-4">

                            <div class="flex items-center">

                                <span class="block whitespace-nowrap text-sm text-gray-700 mr-4">On</span>

                                <div class="w-full">
                                    <el-select v-model="form.days_of_the_week" multiple clearable placeholder="Select days of the week" class="w-full">
                                        <el-option v-for="option in daysOfTheWeekOptions" :key="option" :label="option" :value="option" ></el-option>
                                    </el-select>
                                    <jet-input-error :message="form.errors.days_of_the_week" />
                                </div>

                            </div>

                        </div>

                        <!-- Date -->
                        <div class="grid grid-cols-2 mb-4">

                            <div>
                                <div class="flex items-center">

                                    <span class="block text-sm text-gray-700 mr-4">Date from</span>

                                    <el-date-picker v-model="form.start_date" type="date" value-format="YYYY-MM-DD 00:00:00" format="DD MMM YYYY" placeholder="Start date"></el-date-picker>

                                </div>
                                <jet-input-error :message="form.errors.start_date" />
                            </div>

                            <div>
                                <div class="flex items-center">

                                    <span class="block text-sm text-gray-700 ml-4 mr-4">To</span>

                                    <el-date-picker v-model="form.end_date" type="date" value-format="YYYY-MM-DD 00:00:00" format="DD MMM YYYY" placeholder="End date"></el-date-picker>

                                </div>
                                <jet-input-error :message="form.errors.end_date" />
                            </div>

                        </div>

                        <!-- Time -->
                        <div class="grid grid-cols-2">

                            <div class="flex items-center">

                                <span class="block text-sm text-gray-700 mr-4">Time from</span>

                                <el-time-select v-model="form.start_time" :max-time="form.end_time" placeholder="Start time" start="06:00" step="00:15" end="18:00"></el-time-select>

                            </div>

                            <div class="flex items-center">

                                <span class="block text-sm text-gray-700 ml-4 mr-4">To</span>

                                <el-time-select v-model="form.end_time" :min-time="form.start_time" placeholder="End time" start="06:00" step="00:15" end="18:00"></el-time-select>

                            </div>

                        </div>

                        <div class="mt-10 mb-10">

                            <el-divider content-position="left"><span class="font-semibold">Subscription Plans</span></el-divider>

                        </div>

                        <!-- Subscription Plans -->
                        <div class="mb-4">

                            <span class="block text-sm text-gray-500 mb-4">Choose subscriptions plans required to qualify for this campaign</span>

                            <div class="flex items-center">

                                <el-select id="subscription_plans" v-model="form.subcription_plan_ids" multiple class="w-full" placeholder="Select subcription plan" >
                                    <el-option v-for="plan in subscriptionPlanOptions" :key="plan.value" :value="plan.value" :label="plan.name"></el-option>
                                </el-select>

                            </div>

                        </div>

                        <div class="mt-10 mb-10">

                            <el-divider content-position="left"><span class="font-semibold">Messages / Topics</span></el-divider>

                        </div>

                        <!-- Message Categories -->
                        <div>

                            <span class="block text-sm text-gray-500 mb-4">Choose messages or topics to send for this campaign</span>

                            <!-- Topics to send -->
                            <div class="flex mb-4">
                                <span class="block whitespace-nowrap text-sm text-gray-700 mr-4">Topics</span>
                                <el-cascader v-model="selectedOptions" :props="propsForTopics" class="w-full"/>
                            </div>

                            <!-- Messages to send -->
                            <div class="flex mb-4">
                                <span class="block whitespace-nowrap text-sm text-gray-700 mr-4">Messages</span>
                                <el-cascader v-model="selectedOptions" :props="propsForTopics" class="w-full"/>
                            </div>

                        </div>

                    </template>

                </template>

                <!-- Modal Footer -->
                <template #footer>

                    <jet-secondary-button @click="closeModal()" class="mr-2">
                        Cancel
                    </jet-secondary-button>

                    <jet-button v-if="!hasCampaign" @click.prevent="create()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
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

    import moment from "moment";
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
import axios from "axios";

    export default defineComponent({
        components: {
            JetLabel, JetInput, JetTextarea, JetButton, JetInputError, JetSelectInput, JetDialogModal, JetSecondaryButton,
            JetDangerButton
        },
        props: {
            action: {
                type: String,
                default: 'update'
            },
            modelValue: {
                type: Boolean,
                default: false
            },
            showAddbutton: {
                type: Boolean,
                default: false
            },
            campaign: {
                type: Object,
                default: null
            },
            subscriptionPlans: Array,
            show: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                selectedOptions: [ [ 1 ], [ 2 ], [ 1, 4 ], [ 1, 26 ] ],
                propsForTopics: {
                    lazy: true,
                    multiple: true,
                    checkStrictly: true,
                    lazyLoad: function(node, resolve) {

                        const { level } = node;

                        //  If this is the first list of options
                        if( level === 0  ){

                            var url = route('api.topics', { project: route().params.project });

                        //  If this is the nested list of options
                        }else{

                            console.log('node');
                            console.log(node);

                            console.log('node.data');
                            console.log(node.data);

                            console.log('node.data.value');
                            console.log(node.data.value);

                            var url = route('api.subtopics', { project: route().params.project, topic: node.data.value });

                        }

                        axios.get(url)
                            .then((response) => {
                                console.log(response.data.data);
                                var nodes = response.data.data.map((topic) => {
                                    return {
                                        value: topic.id,
                                        label: topic.title,
                                        leaf: topic.sub_topics_count == 0
                                    }
                                });

                                resolve(nodes);

                            });

                        /*
                        const { level } = node
                        setTimeout(() => {
                        const nodes = Array.from({ length: level + 1 }).map((item) => ({
                            value: ++this.id,
                            label: `Option - ${this.id}`,
                            leaf: level >= 2,
                        }))
                        // Invoke `resolve` callback to return the child nodes data and indicate the loading is finished.
                        resolve(nodes)
                        }, 1000)
                        */
                    },
                },


                moment: moment,

                //  Form attributes
                form: null,

                //  Modal attributes
                showModal: this.modelValue,

                showSuccessCampaign: false,
                showErrorCampaign: false,

                daysOfTheWeekOptions: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
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
                        console.log('val');
                        console.log(val);
                        this.showModal = val;
                        this.reset();
                    }

                }
            },

        },

        computed: {
            hasCampaign(){
                return this.campaign == null ? false : true;
            },
            wantsToUpdate(){
                return (this.hasCampaign && this.action == 'update') ? true : false;
            },
            wantsToDelete(){
                return (this.hasCampaign && this.action == 'delete') ? true : false;
            },
            frequencyOptions(){
                return [
                    {
                        name: this.form.duration == '1' ? 'Day': 'Days',
                        value: 'Days'
                    },
                    {
                        name: this.form.duration == '1' ? 'Week': 'Weeks',
                        value: 'Weeks'
                    },
                    {
                        name: this.form.duration == '1' ? 'Month': 'Months',
                        value: 'Months'
                    },
                    {
                        name: this.form.duration == '1' ? 'Year': 'Years',
                        value: 'Years'
                    }
                ];
            },
            subscriptionPlanOptions() {
                return this.subscriptionPlans.map(function(subscriptionPlan){
                    return {
                        'name': subscriptionPlan.name,
                        'value': subscriptionPlan.id,
                    };
                });
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

                this.form.post(route('create-campaign', { project: route().params.project }), options);
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

                this.form.put(route('update-campaign', { project: route().params.project, campaign_id: this.campaign.id }), options);
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

                this.form.delete(route('delete-campaign', { project: route().params.project, campaign_id: this.campaign.id }), options);
            },
            handleOnSuccess(){

                this.reset();
                this.closeModal();

                this.showSuccessCampaign = true;

                setTimeout(() => {
                    this.showSuccessCampaign = false;
                }, 3000);

            },
            handleOnError(){

                this.showErrorCampaign = true;

                setTimeout(() => {
                    this.showErrorCampaign = false;
                }, 3000);

            },
            reset() {
                this.form = this.$inertia.form({
                    name: this.hasCampaign ? this.campaign.name : null,
                    description: this.hasCampaign ? this.campaign.description : null,
                    duration: this.hasCampaign ? this.campaign.duration : 1,
                    frequency: this.hasCampaign ? this.campaign.frequency : 'Days',

                    message_category_ids: [],
                    subcription_plan_ids: this.hasCampaign ? this.campaign.subscription_plans.map((subscriptionPlan) => subscriptionPlan.id) : [],

                    //  Set start date to today
                    start_date: this.hasCampaign ? moment(this.campaign.start_date).format('YYYY-MM-DD HH:mm:ss') : new Date(),

                    //  Set end date 1 year from now
                    end_date: this.hasCampaign ? moment(this.campaign.end_date).format('YYYY-MM-DD HH:mm:ss') : (new Date()).setFullYear((new Date()).getFullYear() + 1),

                    start_time: '06:00',
                    end_time: '18:00',

                    days_of_the_week: this.daysOfTheWeekOptions
                });
            }
        },
        created(){

            this.reset();

        }
    })
</script>
