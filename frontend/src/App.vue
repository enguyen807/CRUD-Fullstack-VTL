<script setup lang="ts">
import { onMounted, ref } from 'vue'
import HelloWorld from './components/HelloWorld.vue'
import BaseCard   from './components/BaseCard/BaseCard.vue'
import BaseButton from './components/BaseButton/BaseButton.vue'
import BaseTable  from './components/BaseTable/BaseTable.vue'

interface Customer {
    id: number,
    first_name: string,
    last_name: string,
    birth_date: string,
    username: string
}


const customers = ref<Customer[]>([]);

onMounted(async () => {
  const response = await fetch("http://localhost:8000/api/customers", {
    method: 'get'
  });
  const data = await response.json();
  customers.value = data.data;

})
</script>

<template>
  <BaseCard>
    <HelloWorld msg="Vite + Vue" />
    <BaseButton label="Test"></BaseButton>
    <BaseTable :data="customers" background-color="light" is-striped is-hoverable is-editable v-if="customers.length"></BaseTable>
  </BaseCard>
</template>

<style scoped>
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
