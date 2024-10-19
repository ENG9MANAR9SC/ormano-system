
<script lang="ts" setup>


import { ref } from 'vue';
import type { CourtProperties } from '@/views/pages/courts/types';
import { useCourtListStore } from '@/views/pages/courts/useCourtListStore';

const courtStore = useCourtListStore();
const emit = defineEmits(['add-court']);


const items = ref([
  { text: 'Home', disabled: false, href: '/' },
  { text: 'Courts', disabled: false, href: '/courts' },
]);

const courtData = ref<CourtProperties>({
  id: 0,
  name:'',
  desc:'',
  address:'',
  phone:'',
  city_id:0,
});

const handleAddCourt = (courtData: CourtProperties) => {
  courtStore.addCourt(courtData)
    .then(() => {
      console.log('Court added successfully');
    })
    .catch((err: any) => console.error(err));
};

</script>
<template>
  <div>
    <VBreadcrumbs :items="items"></VBreadcrumbs>
    <VCard class="ga-3 pt-3">
      <VCardActions class="justify-space-between ga-3">
        
        <VCardTitle>Create New Court</VCardTitle>
        <div>
          <VBtn color="primary" variant="tonal" @click="handleAddCourt(courtData)">
            Save
          </VBtn>
          <VBtn color="error" variant="tonal">Cancel</VBtn>
        </div>
  
      </VCardActions>
      <VContainer>
        <VRow>
          <VCol cols="6">
            <VLabel class="my-3">Full Name</VLabel> <br />
            <VTextField label="Name" hint="Enter Name" v-model="courtDate.name" outlined />
          </VCol>
          <VCol cols="6">
            <VLabel class="my-3">Full Name</VLabel> <br />
            <VTextField label="Name" hint="Enter Name" v-model="courtDate.desc" outlined />
          </VCol>

        </VRow>
        <VRow>
          <VCol cols="6">
            <VLabel class="my-3">Address</VLabel> <br />
            <VTextField label="Address"  hint="Enter Email" v-model="courtDate.address" outlined/> 
          </VCol>
          <VCol cols="6">
            <VLabel class="my-3">Phone Number</VLabel> <br />
            <VTextField label="Phone Number" hint="Enter Phone Number" v-model="courtDate.phone" outlined />
          </VCol>
        </VRow>
        <VRow>
          <!-- <VCol cols="6">
            <VLabel class="my-3">Gender</VLabel> <br />
            <VSelect
              :items="[{ text: 'Male', value: 1 }, { text: 'Female', value: 2 }]"
              label="Gender"
              hint="Select Gender"
              v-model="courtDate.gender"
              outlined
            />
          </VCol> -->
        </VRow>
      </VContainer>
    </VCard>
  </div>
</template>


