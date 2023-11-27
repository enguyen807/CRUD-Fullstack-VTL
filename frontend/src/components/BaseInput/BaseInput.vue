
<script setup lang="ts">
import './BaseInput.style.css';

defineProps<{
    modelValue: string | number | Date,
    id: string,
    label: string,
    type: string,
    placeholder?: string,
    isDisabled?: boolean,
}>()


const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const handleOnKeyUp = (event: Event): void => {
    emit("update:modelValue", (event.target as HTMLInputElement).value);
};

</script>

<template>
    <div class="app-input">
        <label :for="id">{{ label }}</label>
        <input 
            :id="id" 
            :type="type" 
            :placeholder="placeholder" 
            :value="modelValue" 
            @input="handleOnKeyUp"
            min='1900-01-01'
            :max="new Date().toISOString().split('T')[0]"
        />
    </div>
</template>

