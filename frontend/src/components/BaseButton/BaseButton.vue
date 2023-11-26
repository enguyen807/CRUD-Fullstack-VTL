
<script setup lang="ts">
import './BaseButton.style.css';
import { computed } from 'vue';


const props = withDefaults(
    defineProps<{ 
        label: string, 
        type?:  'button' | 'submit',
        isRounded?: boolean,
        size?: 'small' | 'medium' | 'large', 
        backgroundColor?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning'}>(), 
        { type: 'button', size: 'medium', backgroundColor: 'primary', isRounded: true }
);

const emit = defineEmits<{
    (e: 'click', id: number): void;
}>();

const classes = computed(() => ({
    'app-button': true,
    'app-button--rounded': props.isRounded,
    [`app-button--${props.backgroundColor || 'primary'}`]: true,
    [`app-button--${props.size || 'large'}`]: true,
}));

const onClick = () => {
    emit("click", 1)
};

</script>

<template>
    <button :type="props.type" :class="classes" @click="onClick">
        {{ label }}
    </button>
</template>

