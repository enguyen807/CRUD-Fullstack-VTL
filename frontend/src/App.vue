<script setup lang="ts">
import { onMounted, ref, reactive, watch } from "vue";
import type { WatchSource } from "vue";
import { useCustomers } from "./store/Customer";
import { storeToRefs } from "pinia";
import { useIntervalFn } from "@vueuse/core";

import BaseButton from "./components/BaseButton/BaseButton.vue";
import BaseCard from "./components/BaseCard/BaseCard.vue";
import BaseTable from "./components/BaseTable/BaseTable.vue";
import BaseInput from "./components/BaseInput/BaseInput.vue";

interface Customer {
  id?: number;
  password?: string;
  first_name: string;
  last_name: string;
  birth_date: string;
  username: string;
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

const selectedCustomer = ref<number>(0);
const selectedCustomerArray = ref<number[]>([]);
const customer = reactive<Customer & CustomerErrors>({
  first_name: "",
  last_name: "",
  birth_date: "",
  username: "",
  password: "",
});
const customerErrors = reactive<any>({
  first_name_errors: {
    msg: "",
    isTouched: false,
  },
  last_name_errors: {
    msg: "",
    isTouched: false,
  },
  birth_date_errors: {
    msg: "",
    isTouched: false,
  },
  username_errors: {
    msg: "",
    isTouched: false,
  },
  password_errors: {
    msg: "",
    isTouched: false,
  }
});

const { pause, resume } = useIntervalFn(() => {
  if (responseSuccessTimeLeft.value === 0) {
    pause();
    responseSuccessTimeLeft.value = 2;
    responseSuccess.value = false;
    toggleEditMode.value = false;
    return;
  }
  responseSuccessTimeLeft.value--;
}, 1000);

const handleSubmit = (): void => {
  if (Object.values(customer).every((currentVal) => currentVal === "")) {
    return;
  }

  customersStore
    .createCustomer(customer)
    .then(() => {
      responseSuccess.value = true;
      handleResetForm();
      resume();
    })
    .catch((e) => {
      if (e.statusCode === 409) {
        customerErrors.username_errors.msg = e.statusText;
      } else {
        console.log(e);
      }
    });
};

const handleUpdate = (): void => {
  // console.log('Updated:', customer);

  let payload = customer;

  if (!customer.password) {
    delete payload.password;
  }

  customersStore
    .updateCustomer(selectedCustomer.value, payload)
    .then(() => {
      handleResetForm();
      responseSuccess.value = true;
      resume();
    })
    .catch((e) => {
      if (e.statusCode === 409) {
        customerErrors.username_errors.msg = e.statusText;
      } else {
        console.log(e);
      }
    });
};

const handleCancel = (): void => {
  handleResetForm()
  toggleEditMode.value = false
}

const handleDelete = (): void => {
  const confirmed = confirm('Are you sure you want to delete these customers? All customer data will be permanently removed. This action cannot be undone.')
  
  if (confirmed) {
    customersStore
      .deleteCustomers(selectedCustomerArray.value)
      .then(() => {
        selectedCustomerArray.value = [];
      })
      .catch((e) => {
        console.log(e);
    });
  } 
}

const selectUserForUpdate = (row: any): void => {
  toggleEditMode.value = true;

  const { first_name, last_name, birth_date, username, id } = row;
  selectedCustomer.value = id;
  customer.first_name = first_name;
  customer.last_name = last_name;
  customer.birth_date = birth_date;
  customer.username = username;

  customerErrors[`first_name_errors`].isTouched = true
  customerErrors[`last_name_errors`].isTouched = true
  customerErrors[`birth_date_errors`].isTouched = true
  customerErrors[`username_errors`].isTouched = true
};

const handleResetForm = (): void => {
  Object.keys(customerErrors).forEach((key) => {
    customerErrors[key].msg = "";
    customerErrors[key].isTouched = false;
  });
  Object.keys(customer).forEach((key) => {
    customer[key] = "";
  });

};

const generateWatcher = (
  colName: string,
  label: string,
  min?: number | undefined,
  max?: number | undefined,
  regex?: RegExp | undefined
): WatchSource => {
  return watch(
    [() => [customer[colName]]],
    () => {
      if (!customer[colName]) {
        customerErrors[`${colName}_errors`].msg = customerErrors[`${colName}_errors`].isTouched
          ? `${label} is required`
          : "";
      } else if (!/\S/.test(customer[colName]!)) {
        customerErrors[
          `${colName}_errors`
        ].msg = `${label} only contains whitespace (ie. spaces, tabs or line breaks)`;
      } else if (max && customer[colName]!.length > max) {
        customerErrors[`${colName}_errors`].msg =
          "Must be 50 or fewer characters long";
      } else if (min && customer[colName]!.length < min) {
        customerErrors[`${colName}_errors`].msg =
          "Must be 6 or more characters long";
        customerErrors[`${colName}_errors`].isTouched = true;
      } else if (regex && !regex.test(customer[colName]!)) {
        customerErrors[
          `${colName}_errors`
        ].msg = `${label} must contain at least 6 characters, 1 uppercase letter(A-Z), 1 lowercase letter(a-z), 1 numeric character(0-9), and 1 special character(#?!@$%^&*-).`;
      } else {
        customerErrors[`${colName}_errors`].msg = "";
        customerErrors[`${colName}_errors`].isTouched = true;
      }
    }
  );
};

onMounted(async (): Promise<void> => {
  pause();
  customersStore.getAllCustomers();

  generateWatcher("first_name", "First Name", undefined, 50);
  generateWatcher("last_name", "Last Name", undefined, 50);
  generateWatcher("username", "Username", 6, 50);
  generateWatcher(
    "password",
    "Password",
    6,
    50,
    /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/
  );
});
</script>

<template>
  <div
    class="flex md:flex-row gap-4"
    :class="toggleEditMode ? 'flex-col-reverse' : 'flex-col'"
  >
    <TransitionGroup name="fade">
      <div v-if="!toggleEditMode" key="create_customer" class="grow">
        <BaseCard
          class="lg:w-80 lg:h-3/6 w-full grid gap-4 content-center justify-items-center text-emerald-500"
          v-if="responseSuccess"
        >
          <img class="w-28 text-emerald-500" src="./assets/circle-check.svg" />
          <h2 class="text-xl">Customer Created!</h2>
          <h4 class="text-base">
            Back to form in {{ responseSuccessTimeLeft }}...
          </h4>
          <BaseButton
            :label="`Back to form now!`"
            @click="responseSuccess = false"
          ></BaseButton>
        </BaseCard>
        <BaseCard class="lg:w-80 w-full" v-else>
          <div class="w-full">
            <h2 class="text-xl mb-1">Create Customer</h2>
            <hr />
            <form
              class="grid grid-cols-1 gap-6 mt-4"
              @submit.prevent="handleSubmit"
            >
              <BaseInput
                id="first_name"
                label="First Name"
                is-required
                type="text"
                v-model="customer.first_name"
                
                :error-msg="customerErrors.first_name_errors.msg"
              />
              <BaseInput
                id="last_name"
                label="Last Name"
                is-required
                type="text"
                v-model="customer.last_name"
                
                :error-msg="customerErrors.last_name_errors.msg"
              />
              <BaseInput
                id="birth_date"
                label="Birth Date"
                is-required
                type="date"
                v-model="customer.birth_date"
                :error-msg="customerErrors.birth_date_errors.msg"
              />
              <BaseInput
                id="username"
                label="Username"
                is-required
                type="text"
                v-model="customer.username"
                
                :error-msg="customerErrors.username_errors.msg"
              />
              <BaseInput
                id="password"
                label="Password"
                is-required
                :type="show ? 'password' : 'text'"
                v-model="customer.password"
                
                :error-msg="customerErrors.password_errors.msg"
                toggle-password
              >
                <template #show_hide_password>
                  <div
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5"
                  >
                    <img
                      class="cursor-pointer"
                      src="./assets/eye-open.svg"
                      @click="show = !show"
                      :class="{ hidden: !show, block: show }"
                    />
                    <img
                      class="cursor-pointer"
                      src="./assets/eye-close.svg"
                      @click="show = !show"
                      :class="{ block: !show, hidden: show }"
                    />
                  </div>
                </template>
              </BaseInput>
              <div class="flex gap-3 mt-5">
                <BaseButton
                  type="submit"
                  label="Submit"
                  background-color="success"
                />
                <BaseButton
                  type="button"
                  label="Clear"
                  @click="handleResetForm"
                  background-color="secondary"
                />
              </div>

            </form>

          </div>
        </BaseCard>
      </div>

      <div key="customer_table" class="grow-0">
        <BaseCard v-show="selectedCustomerArray.length" class="sticky top-0 z-50 w-full">
          <div class="flex items-center justify-between">
            <div>
              <span class="inline-block w-7 rounded bg-emerald-500 px-2 py-1 text-white text-center">{{ selectedCustomerArray.length }}</span> customers selected
            </div>
            <BaseButton label="Delete" background-color="danger" @click="handleDelete"></BaseButton>
          </div>
        </BaseCard>
        <BaseCard >
          <BaseTable
            :data="customers"
            background-color="light"
            is-striped
            is-hoverable
            is-editable
            enable-multi-select
            v-model:value="selectedCustomerArray"
            v-if="customers.length"
            @update="selectUserForUpdate"
          >
          </BaseTable>
        </BaseCard>
      </div>

      <div v-if="toggleEditMode" key="update_customer" class="grow">
        <BaseCard
          class="lg:w-80 lg:h-3/6 w-full grid gap-4 content-center justify-items-center text-emerald-500"
          v-if="responseSuccess"
        >
          <img class="w-28 text-emerald-500" src="./assets/circle-check.svg" />
          <h2 class="text-xl">Customer Updated!</h2>
          <h4 class="text-base">
            Back to form in {{ responseSuccessTimeLeft }}...
          </h4>
          <BaseButton
            :label="`Back to form now!`"
            @click="responseSuccess = false"
          ></BaseButton>
        </BaseCard>
        <BaseCard class="lg:w-80 w-full" v-else>
          <div class="w-full">
            <h2 class="text-xl mb-1">Update Customer</h2>
            <hr />
            <form
              class="grid grid-cols-1 gap-6 mt-4"
              @submit.prevent="handleUpdate"
            >
              <BaseInput
                id="first_name"
                label="First Name"
                type="text"
                is-required
                v-model="customer.first_name"
                
                :error-msg="customerErrors.first_name_errors.msg"
              />
              <BaseInput
                id="last_name"
                label="Last Name"
                is-required
                type="text"
                v-model="customer.last_name"
                
                :error-msg="customerErrors.last_name_errors.msg"
              />
              <BaseInput
                id="birth_date"
                label="Birth Date"
                is-required
                type="date"
                v-model="customer.birth_date"
                
                :error-msg="customerErrors.birth_date_errors.msg"
              />
              <BaseInput
                id="username"
                label="Username"
                is-required
                type="text"
                v-model="customer.username"
                
                :error-msg="customerErrors.username_errors.msg"
              />
              <BaseInput
                id="password"
                label="Password"
                :type="show ? 'password' : 'text'"
                v-model="customer.password"
                
                :error-msg="customerErrors.password_errors.msg"
                toggle-password
              >
                <template #show_hide_password>
                  <div
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5"
                  >
                    <img
                      class="cursor-pointer"
                      src="./assets/eye-open.svg"
                      @click="show = !show"
                      :class="{ hidden: !show, block: show }"
                    />
                    <img
                      class="cursor-pointer"
                      src="./assets/eye-close.svg"
                      @click="show = !show"
                      :class="{ block: !show, hidden: show }"
                    />
                  </div>
                </template>
              </BaseInput>
              <div class="flex gap-3 mt-5">
                <BaseButton
                  type="submit"
                  label="Update"
                  background-color="success"
                />
                <BaseButton
                  type="button"
                  label="Cancel"
                  @click="handleCancel"
                  background-color="secondary"
                />
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
