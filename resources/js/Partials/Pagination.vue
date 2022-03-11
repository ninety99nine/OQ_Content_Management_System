<template>

    <!-- Pagination Links -->
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
        <a v-if="previousLink != null" :href="previousLink.url == null ? '#' : previousLink.url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Previous
        </a>
        <a v-if="nextLink != null" :href="nextLink.url == null ? '#' : nextLink.url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Next
        </a>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-700">
            Showing
            {{ ' ' }}
            <span class="font-medium">{{ paginationPayload.from }}</span>
            {{ ' ' }}
            to
            {{ ' ' }}
            <span class="font-medium">{{ paginationPayload.to }}</span>
            {{ ' ' }}
            of
            {{ ' ' }}
            <span class="font-medium">{{ paginationPayload.total }}</span>
            {{ ' ' }}
            results
            </p>
        </div>
        <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <a :href="paginationPayload.prev_page_url" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Previous</span>
                    <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                </a>

                <template v-for="(link, p) in links" :key="p">

                    <template v-if="isPreviousLink(link) == false && isNextLink(link) == false">
                        <InertiaLink :only="updateData"  v-if="link.active == true" :href="link.url" aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            {{ link.label }}
                        </InertiaLink>

                        <InertiaLink :only="updateData" v-else-if="link.active == false" :href="link.url" aria-current="page" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            {{ link.label }}
                        </InertiaLink>
                    </template>

                </template>

                <a :href="paginationPayload.next_page_url" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Next</span>
                    <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                </a>
            </nav>
        </div>
        </div>
    </div>

</template>

<script>

    import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/solid'
    import { InertiaLink } from '@inertiajs/inertia-vue3'
    import { defineComponent } from 'vue'

    export default defineComponent({
        components: {
            ChevronLeftIcon, ChevronRightIcon,
            InertiaLink
        },
        props: {
            paginationPayload: Object,
            updateData: {
                type: Array,
                default: () => {
                    return [];
                }
            }
        },
        data() {
            return {

            }
        },
        computed: {
            links(){
                return this.paginationPayload.links;
            },
            previousLink(){
                return this.links.length ? this.links[0] : null;
            },
            nextLink(){
                return this.links.length ? this.links[this.links.length - 1] : null;
            }
        },
        methods: {
            isPreviousLink(link){
                return link.label == '&laquo; Previous' ? true : false;
            },
            isNextLink(link){
                return link.label == 'Next &raquo;' ? true : false;
            }
        }
    })
</script>
