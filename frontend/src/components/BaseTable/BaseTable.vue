
<script setup lang="ts">
import './BaseTable.style.css';
import { computed } from 'vue';
import BaseButton from '../BaseButton/BaseButton.vue';

interface Object {
    [key: string]: string
}

const props = withDefaults(
    defineProps<{
        data: Array<any>,
        value: Array<any>,
        isEditable?: boolean,
        isHoverable?: boolean,
        isStriped?: boolean,
        isRounded?: boolean,
        enableMultiSelect?: boolean,
        enableEditableRows?: boolean,
        backgroundColor?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'light' | 'dark'
    }>(),
    { isRounded: true, enableEditableRows: false, backgroundColor: 'primary' }
);

const emit = defineEmits<{
    (e: 'update', row: Object): void;
    (e: 'update:value', value: Object[]): void;
}>();

const onClick = (row: Object): void => {
    emit("update", row)
};

const handleSelect = (rowId: number, event: Event): void => {
    const selectedRow = [...props.value];
      if ((<HTMLInputElement>event.target).checked) {
        selectedRow.push(rowId);
      } else {
        selectedRow.splice(selectedRow.indexOf(rowId), 1);
      }
      emit("update:value", selectedRow);
}

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
                    <th v-if="enableMultiSelect"></th>
                    <th v-for="(header, index) in tableHeaders" :key="index" scope="col">
                        {{ header }}
                    </th>
                    <th v-if="props.isEditable" scope="col">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="row in data" :key="row.id">
                    <td v-if="enableMultiSelect">
                        <input type="checkbox" class="rounded" :checked="value.includes(row.id)" @input="handleSelect(row.id, $event)"/>
                    </td>
                    <th class="font-medium text-gray-900 whitespace-nowrap">{{ row.id }}</th>
                    <td>{{ row.first_name }}</td>
                    <td>{{ row.last_name }}</td>
                    <td>{{ row.birth_date }}</td>
                    <td>{{ row.username }}</td>
                    <td v-if="props.isEditable">
                        <BaseButton label="Edit" background-color="warning" size="small" @click="onClick(row)"></BaseButton>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

