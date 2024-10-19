import type { AdminParams, AdminProperties } from '@/views/pages/admins/types';

import axios from '@axios';
import { defineStore } from 'pinia';

export const useAdminListStore = defineStore('AdminListStore', {
  actions: {

    // 👉 Fetch admins data
    fetchAdmins(params: AdminParams) { return axios.get('/admin/admins', { params }) },

    // 👉 Add Admin
    addAdmin(adminData: AdminProperties) {
      return new Promise((resolve, reject) => {
      axios.post('/admin/admins', {
        admin: adminData,
      }).then(response => resolve(response))
        .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    // fetchUser(id: number) {
    //   return new Promise<AxiosResponse>((resolve, reject) => {
    //     axios.get(`/admin/users/${id}`).then(response => resolve(response)).catch(error => reject(error))
    //   })
    // },


    // 👉 Delete User
    // deleteUser(id: number) {
    //   return new Promise((resolve, reject) => {
    //     axios.delete(`/admin/users/${id}`).then(response => resolve(response)).catch(error => reject(error))
    //   })
    // },
  },
})
