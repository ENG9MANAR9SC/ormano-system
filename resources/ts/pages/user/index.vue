
<script lang="ts" setup>
import type { UserProperties } from '@/views/pages/users/types';
import { useUserListStore } from '@/views/pages/users/useUserListStore';


const userListStore = useUserListStore();
const searchQuery = ref('');
const per_page = ref(10);
const currentPage = ref(1);
const s = ref(2);
const total_pages = ref(0);

const users = ref<UserProperties[]>([])

const options = ref<Options>({
  page: 1,
  itemsPerPage: 12,
})

// 👉 Fetching users
const fetchUsers = () => {
  userListStore.fetchUsers({
    s: searchQuery.value,
    currentPage: currentPage.value,
    page: currentPage.value,
  }).then(({ data }) => {
    total_pages.value = data.users.last_page;

    users.value = data.users.data;
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchUsers)

</script>


<template>
  <div>
    <VRow>
      <VCol cols="12">
        <VCard>
          <VCardItem class="d-flex flex-wrap justify-space-between gap-4">
            <VCardTitle>{{ $t("Users") }} </VCardTitle>

            <template #append>

              <div class="d-flex flex-wrap justify-space-between gap-4">
                <!-- 👉 Search  -->
                <div style="inline-size: 16rem;">
                  <AppTextField
                    v-model="searchQuery"
                    placeholder="Search"
                    density="compact"
                  />
                </div>

                <!-- 👉 Export button -->
                <VBtn
                  variant="tonal"
                  color="secondary"
                  prepend-icon="tabler-screen-share"
                >
                  Export
                </VBtn>

                <!-- 👉 Add user button -->
                <a
                  href="/user/create"
                  rel="noopener noreferrer"
                  class="text-decoration-none"
                >
                  <VBtn
                    prepend-icon="tabler-plus"
                  >
                    Add New User
                  </VBtn>
                </a> 
              </div>
            </template>
          </VCardItem>
        </VCard>
      </VCol>

      <VCol
        v-for="user in users"
        :key="user.id"
        sm="6"
        lg="4"
        cols="12"
      >

        <UserCard :user="user" @fetch-users="fetchUsers" />
      </VCol>

      <VCol cols="12">
        <VPagination
          v-model="currentPage"
          :length="total_pages"
        />
      </VCol>
    </VRow>
  </div>
</template>

<style lang="scss">
.vertical-more {
  position: absolute;
  inset-block-start: 1rem;
  inset-inline-end: 0.5rem;
}
</style>
