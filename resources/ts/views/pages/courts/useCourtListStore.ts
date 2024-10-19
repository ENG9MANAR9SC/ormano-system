import type { CourtParams, CourtProperties } from '@/views/pages/courts/types';

import axios from '@axios';
import { defineStore } from 'pinia';

export const useCourtListStore = defineStore('CourtListStore', {
  actions: {

    // 👉 Fetch Courts data
    fetchCourts(params: CourtParams) { return axios.get('/admin/courts', { params }) },

    // 👉 Add Court
    addCourt(courtData: CourtProperties) {
      return new Promise((resolve, reject) => {
      axios.post('/admin/courts', {
        court: courtData,
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
