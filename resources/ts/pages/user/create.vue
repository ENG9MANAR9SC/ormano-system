
<script lang="ts" setup>

  import { ref } from 'vue';


  const items = ref([
    { text: 'Home', disabled: false, href: '/' },
    { text: 'Users', disabled: false, href: '/users' },
  ]);

  const submit = () => {
    console.log('submit');
  }

</script>
<template>
  <div>
    <VBreadcrumbs :items="items"></VBreadcrumbs>
    <VCard class="ga-3 pt-3">
      <VCardActions class="justify-space-between ga-3">
        
        <VCardTitle>Create New User</VCardTitle>
        <div>
          <VBtn color="primary" variant="tonal" @click="submit">
            Save
          </VBtn>
          <VBtn color="error" variant="tonal">Cancel</VBtn>
        </div>
  
      </VCardActions>
      <VContainer>
        <VRow>
          <VCol cols="6">
            <VLabel class="my-3">Full Name</VLabel> <br />
            <VTextField label="Name" hint="Enter Name" outlined />
          </VCol>
          <VCol cols="6">
            <VLabel class="my-3">Phone Number</VLabel> <br />
            <VTextField label="Phone Number" hint="Enter Phone Number" outlined />
          </VCol>
        </VRow>
        <VRow>
          <VCol cols="6">
            <VLabel class="my-3">Email</VLabel> <br />
            <VTextField label="Email" type="email" hint="Enter Email" outlined/> 
          </VCol>
          <VCol cols="6">
            <VLabel class="my-3">Password</VLabel> <br />
            <VTextField label="Password" type="password" hint="Enter Password" outlined />
          </VCol>
        </VRow>
        <VRow>
          <VCol cols="6">
            <VLabel class="my-3">Gender</VLabel> <br />
            <VSelect
              :items="['Male', 'Female']"
              label="Gender"
              hint="Select Gender"
              outlined
            />
          </VCol>
          <VCol cols="6">
            <VLabel class="my-3">Birth Day</VLabel> <br />
            <VTextField label="Birth Day" type="date" hint="Enter Birth Day" outlined />
          </VCol>
        </VRow>
      </VContainer>
    </VCard>
  </div>
</template>

