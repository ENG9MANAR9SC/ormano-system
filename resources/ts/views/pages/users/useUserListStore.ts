import type { UserParams, UserProperties } from '@/views/pages/users/types';
import axios from '@axios';
import type { AxiosResponse } from 'axios';
import { defineStore } from 'pinia';

export const useUserListStore = defineStore('UserListStore', {
  actions: {

    // 👉 Fetch users data
    fetchUsers(params: UserParams) { return axios.get('/admin/users', { params }) },

    // 👉 Add User
    addUser(userData: UserProperties) {
      return new Promise((resolve, reject) => {
      axios.post('/admin/users', {
        user: userData,
      }).then(response => resolve(response))
        .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    fetchUser(id: number) {
      return new Promise<AxiosResponse>((resolve, reject) => {
        axios.get(`/admin/users/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 refresh user balance
    refreshUserBalance(id: number) {
      return new Promise<AxiosResponse>((resolve, reject) => {
        axios.get(`/admin/user/${id}/refresh-balance`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Delete User
    deleteUser(id: number) {
      return new Promise((resolve, reject) => {
        axios.delete(`/admin/users/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
