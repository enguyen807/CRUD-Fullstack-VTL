<script setup lang="ts">
import "./BaseCard.style.css";
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{ 
        isRounded?: boolean
        backgroundColor?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'light' | 'dark' }>(), 
        { isRounded: true, backgroundColor: 'light' }
);

const classes = computed(() => ({
  'app-card': true,
  'app-card--rounded': props.isRounded,
  [`app-card--${props.backgroundColor || 'light'}`]: true,
}))
</script>

<template>
  <div :class="classes">
    <slot></slot>
  </div>
</template>