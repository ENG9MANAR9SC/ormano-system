
<script lang="ts" setup>


import { ref } from 'vue';
import type { UserProperties } from '@/views/pages/users/types';
import { useUserListStore } from '@/views/pages/users/useUserListStore';

const userStore = useUserListStore();
const emit = defineEmits(['add-user']);

// interface Props {
//   user: UserProperties;
// }
const items = ref([
  { text: 'Home', disabled: false, href: '/' },
  { text: 'Users', disabled: false, href: '/users' },
]);

const userData = ref<UserProperties>({
  id: 0,
  full_name: '',
  phone_number: '',
  email: '',
  password: '',
  gender: 0,
  birth_date: '',
  active: 0,
  avatar: '',
  balance: 0,
  email_verified_at: '',
  notes: '',
  address: '',
  created_at: '',
  updated_at: '',
  occupation: '',
  civil_status: '',
  age: 0,
  gender_title: ''
});

const handleAddUser = (userData: UserProperties) => {
  userStore.addUser(userData)
    .then(() => {
      console.log('User added successfully');
    })
    .catch((err: any) => console.error(err));
};

</script>
<template>
  <div>
    <VBreadcrumbs :items="items"></VBreadcrumbs>
    <VCard class="ga-3 pt-3">
      <VCardActions class="justify-space-between ga-3">
        
        <VCardTitle>Create New User</VCardTitle>
        <div>
          <VBtn color="primary" variant="tonal" @click="handleAddUser(userData)">
            Save
          </VBtn>
          <VBtn color="error" variant="tonal">Cancel</VBtn>
        </div>
  
      </VCardActions>
      <VContainer>
        <VRow>
          <VCol cols="6">
            <VLabel class="my-3">Full Name</VLabel> <br />
            <VTextField label="Name" hint="Enter Name" v-model="userData.full_name" outlined />
          </VCol>
          <VCol cols="6">
            <VLabel class="my-3">Phone Number</VLabel> <br />
            <VTextField label="Phone Number" hint="Enter Phone Number" v-model="userData.phone_number" outlined />
          </VCol>
        </VRow>
        <VRow>
          <VCol cols="6">
            <VLabel class="my-3">Email</VLabel> <br />
            <VTextField label="Email" type="email" hint="Enter Email" v-model="userData.email" outlined/> 
          </VCol>
          <VCol cols="6">
            <VLabel class="my-3">Password</VLabel> <br />
            <VTextField label="Password" type="password" hint="Enter Password" v-model="userData.password" outlined />
          </VCol>
        </VRow>
        <VRow>
          <VCol cols="6">
            <VLabel class="my-3">Gender</VLabel> <br />
            <VSelect
              :items="[{ text: 'Male', value: 1 }, { text: 'Female', value: 2 }]"
              label="Gender"
              hint="Select Gender"
              v-model="userData.gender"
              outlined
            />
          </VCol>
          <VCol cols="6">
            <VLabel class="my-3">Birth Day</VLabel> <br />
            <VTextField label="Birth Day" type="date" hint="Enter Birth Day" v-model="userData.birth_date" outlined />
          </VCol>
        </VRow>
      </VContainer>
    </VCard>
  </div>
</template>

