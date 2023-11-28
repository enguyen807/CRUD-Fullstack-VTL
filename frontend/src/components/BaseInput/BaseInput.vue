
<script setup lang="ts">
import './BaseInput.style.css';
import { useDebounceFn } from '@vueuse/core'

defineProps<{
    modelValue: string | number | Date | null,
    id: string,
    label: string,
    type: string,
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

</script>

<template>
    <div class="app-input" :class="errorMsg ? 'app-input--error' : null">
        <label :for="id">{{ label }}</label>
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
        <span v-show="errorMsg" class="text-red-500 text-sm">{{ errorMsg }}</span>
    </div>
</template>

