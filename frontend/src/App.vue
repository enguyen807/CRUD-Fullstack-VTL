<script setup lang="ts">
import { onMounted, ref, reactive, watch } from 'vue'
import type { WatchSource } from 'vue'

import BaseButton from './components/BaseButton/BaseButton.vue'
import BaseCard from './components/BaseCard/BaseCard.vue'
import BaseTable from './components/BaseTable/BaseTable.vue'
import BaseInput from './components/BaseInput/BaseInput.vue'

interface Customer {
  id?: number,
  password?: string,
  first_name: string,
  last_name: string,
  birth_date: string,
  username: string,
}

interface CustomerErrors {
  [key: string]: string;
}

const customers = ref<Customer[]>([]);
const newCustomer = reactive<CustomerErrors>({
  first_name: '',
  last_name: '',
  birth_date: '',
  username: '',
  password: '',
})
const customerErrors = reactive<CustomerErrors>({
  first_name_errors: '',
  last_name_errors: '',
  birth_date_errors: '',
  username_errors: '',
  password_errors: ''
})

const handleSubmit = (event: Event) => {
  // submit form
  // handle similar username error
  // 

  console.log(event);
}

const generateWatcher = (
  colName: string,
  label: string,
  min?: number | undefined,
  max?: number | undefined,
  regex?: RegExp | undefined): WatchSource => {
  return watch(() => [newCustomer[colName]], () => {
    if (!newCustomer[colName]) {
      customerErrors[`${colName}_errors`] = `${label} is required`
    }
    else if (max && newCustomer[colName]!.length > max) {
      customerErrors[`${colName}_errors`] = 'Must be 50 or fewer characters long'
    }
    else if (min && newCustomer[colName]!.length < min) {
      customerErrors[`${colName}_errors`] = 'Must be 6 or more characters long'
    }
    else if (regex && !regex.test(newCustomer[colName]!)) {
      customerErrors[`${colName}_errors`] = `${label} must contain at least 6 characters, 1 uppercase letter(A-Z), 1 lowercase letter(a-z), 1 numeric character(0-9), and 1 special character(#?!@$%^&*-).`;
    }
    else {
      customerErrors[`${colName}_errors`] = ''
    }
  });
}

onMounted(async () => {
  const response = await fetch("http://localhost:8000/api/customers", {
    method: 'get'
  });
  const data = await response.json();
  customers.value = data.data;

  generateWatcher('first_name', 'First Name', undefined, 50);
  generateWatcher('last_name', 'Last Name', undefined, 50);
  generateWatcher('username', 'Username', 6, 50);
  generateWatcher('password', 'Password', 6, 50, /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/);
})
</script>

<template>
  <div style="display: flex; gap: 10px;">
    <div>
      <BaseCard>
        <div class="max-w-md">
          <form class="grid grid-cols-1 gap-6" @submit.prevent="handleSubmit">
            <div class="grid md:grid-cols-2 md:gap-6">
              <BaseInput 
                id="first_name" 
                label="First Name" 
                type="text" 
                v-model="newCustomer.first_name"
                :error-msg="customerErrors.first_name_errors" 
              />
              <BaseInput 
                id="last_name" 
                label="Last Name" 
                type="text" 
                v-model="newCustomer.last_name"
                :error-msg="customerErrors.last_name_errors" 
              />
            </div>
            <BaseInput 
              id="birth_date" 
              label="Birth Date" 
              type="date" 
              v-model="newCustomer.birth_date"
              :error-msg="customerErrors.birth_date_errors" 
            />
            <BaseInput 
              id="username" 
              label="Username" 
              type="text" 
              v-model="newCustomer.username"
              :error-msg="customerErrors.username_errors" 
            />
            <BaseInput 
              id="password" 
              label="Password" 
              type="password" 
              v-model="newCustomer.password"
              :error-msg="customerErrors.password_errors" 
            />
            <hr />
            <BaseButton type="submit" label="Submit" />
          </form>
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
}</style>
