import { defineStore } from 'pinia';
const serverURLENV = import.meta.env.VITE_SERVER_URL;

interface Customer {
    id?: number,
    password?: string,
    first_name: string,
    last_name: string,
    birth_date: string,
    username: string,
}

interface State {
    customers: Customer[],
    isLoading: boolean
}

// interface Object {
//     [key: string]: string
// }


function serialize(obj) {
    let str = [];
    for(let p in obj)
      if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
      }
    return str.join("&");
  }

export const useCustomers = defineStore('customers', {
    state: (): State => ({
        customers: [],
        isLoading: false,
    }),
    actions: {
        async getAllCustomers(): Promise<void> {
            try {
                const response = await fetch(`${serverURLENV}/api/customers`, {
                    method: 'get'
                });
                const result = await response.json();
                this.customers = result.data;                
            } catch (error) {
                console.error(error);
            }
        },
        async createCustomer(customer: Customer): Promise<unknown>{
            try {
                const response = await fetch(`${serverURLENV}/api/customers`, {
                    method: 'post',
                    body: JSON.stringify(customer),
                    headers: {
                        'Content-Type': 'text/plain'
                    },
                })
                const result = await response.json();

                if (!response.ok) {
                    return Promise.reject({ statusCode: response.status, statusText: result.message});
                }
                
                this.customers.push(result);
                return Promise.resolve(response);
            } catch (error: unknown) {
                const e = error as { statusCode: number, statusText: string};
                console.error(`${e.statusCode}: ${e.statusText}`);
            }
        }
      },    
})