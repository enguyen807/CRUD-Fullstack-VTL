
<script setup lang="ts">
import './BaseTable.style.css';
import { computed } from 'vue';
import BaseButton from '../BaseButton/BaseButton.vue';

const props = withDefaults(
    defineProps<{
        data: Array<any>,
        isEditable?: boolean,
        isHoverable?: boolean,
        isStriped?: boolean,
        isRounded?: boolean,
        enableEditableRows?: boolean,
        backgroundColor?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'light' | 'dark'
    }>(),
    { isRounded: true, enableEditableRows: false, backgroundColor: 'primary' }
);

const tableHeaders = computed((): string[] => {
    return Object.keys(props.data[0]).map(head => head.replace(/_/g," "));
})

const classes = computed(() => ({
    'app-table': true,
    'app-table--hoverable': props.isHoverable,
    'app-table--striped': props.isStriped,
    'app-table--rounded': props.isRounded,
    [`app-table--${props.backgroundColor || 'light'}`]: true,
}));

</script>

<template>


<div class="relative overflow-x-auto">
    <table :class="classes">
        <thead>
            <tr>
                <th v-for="(header, index) in tableHeaders" :key="index" scope="col">
                    {{ header }}
                </th>
                <th v-if="props.isEditable">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="row in data" :key="row.id">
                <th class="font-medium text-gray-900 whitespace-nowrap">{{ row.id }}</th>
                <td>{{ row.first_name }}</td>
                <td>{{ row.last_name }}</td>
                <td>{{ row.birth_date }}</td>
                <td>{{ row.username }}</td>
                <td v-if="props.isEditable">
                    <BaseButton label="Edit" background-color="warning"></BaseButton>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</template>

