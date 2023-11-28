<script setup lang="ts">
import { onMounted, ref, reactive, watch } from 'vue'
import type { WatchSource } from 'vue'
import { useCustomers } from './store/Customer'
import { storeToRefs } from 'pinia'
import { useIntervalFn } from '@vueuse/core'

import BaseButton from './components/BaseButton/BaseButton.vue'
import BaseCard from './components/BaseCard/BaseCard.vue'
import BaseTable from './components/BaseTable/BaseTable.vue'
import BaseInput from './components/BaseInput/BaseInput.vue'

interface Customer {
  id?: number,
  password: string,
  first_name: string,
  last_name: string,
  birth_date: string,
  username: string,
}

interface CustomerErrors {
  [key: string]: string;
}

const customersStore = useCustomers();
const { customers } = storeToRefs(customersStore);
const show = ref<boolean>(true);
const toggleEditMode = ref<boolean>(true);
const responseSuccess = ref<boolean>(false);
const responseSuccessTimeLeft = ref<number>(2);

const newCustomer = reactive<Customer & CustomerErrors>({
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

const { pause, resume } = useIntervalFn(() => {
  if (responseSuccessTimeLeft.value === 0) {
    pause();
    responseSuccessTimeLeft.value = 2;
    responseSuccess.value = false;
    return;
  }
  responseSuccessTimeLeft.value--;
}, 1000)

const handleSubmit = (): void => {
  if (Object.values(newCustomer).every((currentVal) => currentVal === '')) {
    return
  }

  customersStore.createCustomer(newCustomer)
    .then(() => {
      responseSuccess.value = true;
      handleResetForm()
      resume()
    })
    .catch((e) => {
      customerErrors.username_errors = e.statusText
    });
}

const handleResetForm = (): void => {
  Object.keys(newCustomer).forEach((key) => { newCustomer[key] = "" });
  Object.keys(customerErrors).forEach((key) => { newCustomer[key] = "" });
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
    else if (!/\S/.test(newCustomer[colName]!)) {
      customerErrors[`${colName}_errors`] = `${label} only contains whitespace (ie. spaces, tabs or line breaks)`
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

onMounted(async (): Promise<void> => {
  customersStore.getAllCustomers();

  generateWatcher('first_name', 'First Name', undefined, 50);
  generateWatcher('last_name', 'Last Name', undefined, 50);
  generateWatcher('username', 'Username', 6, 50);
  generateWatcher('password', 'Password', 6, 50, /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/);
})
</script>

<template>
  <div class="flex md:flex-row gap-4" :class="toggleEditMode ? 'flex-col-reverse' : 'flex-col'">
    <TransitionGroup name="fade">
      <div v-if="!toggleEditMode" key="create_customer" class="grow">
        <BaseCard class="lg:w-96 lg:h-3/6 w-full grid gap-4 content-center justify-items-center text-emerald-500"
          v-if="responseSuccess">
          <img class="w-28 text-emerald-500" src="./assets/circle-check.svg" />
          <h2 class="text-xl">Customer Created!</h2>
          <h4 class="text-base">Back to form in {{ responseSuccessTimeLeft }}...</h4>
          <BaseButton :label="`Back to form now!`" @click="responseSuccess = false"></BaseButton>
        </BaseCard>
        <BaseCard class="lg:w-96 w-full" v-else>
          <div class="w-full">
            <h2 class="text-xl mb-1">Create Customer</h2>
            <hr />
            <form class="grid grid-cols-1 gap-6 mt-4" @submit.prevent="handleSubmit">
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
                :type="show ? 'password' : 'text'" 
                v-model="newCustomer.password"
                :error-msg="customerErrors.password_errors" 
                toggle-password
              >
                <template #show_hide_password>
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                    <img class="cursor-pointer" src="./assets/eye-open.svg" @click="show = !show"
                      :class="{ 'hidden': !show, 'block': show }" />
                    <img class="cursor-pointer" src="./assets/eye-close.svg" @click="show = !show"
                      :class="{ 'block': !show, 'hidden': show }" />
                  </div>
                </template>
              </BaseInput>
              <div class="flex gap-3 mt-5">
                <BaseButton type="submit" label="Submit" background-color="success" />
                <BaseButton type="button" label="Clear" @click="handleResetForm" background-color="secondary" />
              </div>
            </form>
          </div>
        </BaseCard>
      </div>
      <BaseCard key="customer_table" class="grow-0">
        <BaseTable :data="customers" background-color="light" is-striped is-hoverable is-editable v-if="customers.length">
        </BaseTable>
      </BaseCard>
      <div v-if="toggleEditMode" key="update_customer" class="grow">
        <BaseCard class="lg:w-96 lg:h-3/6 w-full grid gap-4 content-center justify-items-center text-emerald-500"
          v-if="responseSuccess">
          <img class="w-28 text-emerald-500" src="./assets/circle-check.svg" />
          <h2 class="text-xl">Customer Updated!</h2>
          <h4 class="text-base">Back to form in {{ responseSuccessTimeLeft }}...</h4>
          <BaseButton :label="`Back to form now!`" @click="responseSuccess = false"></BaseButton>
        </BaseCard>
        <BaseCard class="lg:w-96 w-full" v-else>
          <div class="w-full">
            <h2 class="text-xl mb-1">Update Customer</h2>
            <hr />
            <form class="grid grid-cols-1 gap-6 mt-4" @submit.prevent="handleSubmit">
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
                :type="show ? 'password' : 'text'" 
                v-model="newCustomer.password"
                :error-msg="customerErrors.password_errors" 
                toggle-password
              >
                <template #show_hide_password>
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                    <img class="cursor-pointer" src="./assets/eye-open.svg" @click="show = !show"
                      :class="{ 'hidden': !show, 'block': show }" />
                    <img class="cursor-pointer" src="./assets/eye-close.svg" @click="show = !show"
                      :class="{ 'block': !show, 'hidden': show }" />
                  </div>
                </template>
              </BaseInput>
              <div class="flex gap-3 mt-5">
                <BaseButton type="submit" label="Submit" background-color="success" />
                <BaseButton type="button" label="Clear" @click="handleResetForm" background-color="secondary" />
              </div>
            </form>
          </div>
        </BaseCard>
      </div>
    </TransitionGroup>
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

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
