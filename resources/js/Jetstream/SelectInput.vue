<template>

    <div class="relative inline-block w-full text-gray-700">
        <select v-model="selectedValue" :placeholder="placeholder" class="w-full h-10 pl-3 pr-6 text-base placeholder-gray-600 border rounded-lg appearance-none focus:shadow-outline">
            <option v-for="(option, index) in options" :key="index" :value="option.value">{{ option.name }}</option>
        </select>
    </div>

</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        props: ['modelValue', 'placeholder', 'options'],

        emits: ['update:modelValue'],

        data() {
            return {
                selectedValue : this.modelValue
            }
        },

        watch: {

            selectedValue: {
                handler: function (val, oldVal) {

                    if(val != this.modelValue){

                        this.$emit('update:modelValue', val);

                    }

                }
            },

        }

    })
</script>
