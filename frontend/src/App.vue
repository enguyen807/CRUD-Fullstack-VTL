<script setup lang="ts">
import { onMounted, ref, reactive } from 'vue'
import BaseCard from './components/BaseCard/BaseCard.vue'
import BaseTable from './components/BaseTable/BaseTable.vue'
import BaseInput from './components/BaseInput/BaseInput.vue'

interface Customer {
  id?: number,
  password?: string,
  first_name: string,
  last_name: string,
  birth_date: string,
  username: string
}

const customers = ref<Customer[]>([]);

const newCustomer = reactive<Customer>({
  first_name: '',
  last_name: '',
  birth_date: '',
  username: '',
  password: '',
})


onMounted(async () => {
  const response = await fetch("http://localhost:8000/api/customers", {
    method: 'get'
  });
  const data = await response.json();
  customers.value = data.data;

})
</script>

<template>
  <div style="display: flex; gap: 10px;">
    <div>
    <div>
      <pre> {{ JSON.stringify(newCustomer, null, 4)}}</pre>
    </div>
      <BaseCard>
        <div class="max-w-md">
          <div class="grid grid-cols-1 gap-6">
            <div class="grid md:grid-cols-2 md:gap-6">
              <BaseInput id="first_name" label="First Name" type="text" v-model="newCustomer.first_name" />
              <BaseInput id="last_name" label="Last Name" type="text" v-model="newCustomer.last_name" />
            </div>
            <BaseInput id="birth_date" label="Birth Date" type="date" v-model="newCustomer.birth_date" />
            <BaseInput id="username" label="Username" type="text" v-model="newCustomer.username" />
            <BaseInput id="password" label="Password" type="password" v-model="newCustomer.password" />
          </div>
        </div>
      </BaseCard>
    </div>
    <BaseCard>
      <BaseTable :data="customers" background-color="light" is-striped is-hoverable is-editable v-if="customers.length">
      </BaseTable>
    </BaseCard>
  </div>
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
