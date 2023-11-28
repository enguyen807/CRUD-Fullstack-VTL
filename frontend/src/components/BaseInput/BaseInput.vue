
<script setup lang="ts">
import './BaseInput.style.css';
import { computed } from 'vue';
import { useDebounceFn } from '@vueuse/core'

const props = defineProps<{
    modelValue: string | number | Date | null,
    id: string,
    label: string,
    type: string,
    togglePassword?: boolean,
    placeholder?: string,
    isDisabled?: boolean,
    errorMsg?: string
}>()


const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const handleOnKeyUp = useDebounceFn((event: Event): void => {
    emit("update:modelValue", (event.target as HTMLInputElement).value);
}, 500);

const classes = computed(() => ({
    'app-input': true,
    'app-input--error': props.errorMsg,
    'app-input--togglePassword': props.togglePassword
}));

</script>

<template>
    <div :class="classes">
        <label :for="id">{{ label }}</label>
        <div class="relative">
            <input 
                autocomplete="new-password" 
                required
                :id="id" 
                :type="type" 
                :placeholder="placeholder" 
                :value="modelValue" 
                @input="handleOnKeyUp"
                min='1900-01-01'
                :max="new Date().toISOString().split('T')[0]"
            />
            <slot name="show_hide_password"></slot>
        </div>
        <span v-show="errorMsg" class="text-red-500 text-sm">{{ errorMsg }}</span>
    </div>
</template>

