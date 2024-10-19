<script lang="ts" setup>
import type { AdminProperties } from '@/views/pages/admins/types';
import { useAdminListStore } from '@/views/pages/admins/useAdminListStore';
import { ref } from 'vue';

const items = ref([
  { text: 'Home', disabled: false, href: '/' },
  { text: 'Admins', disabled: false, href: '/users' },
]);

const adminListStore = useAdminListStore();

const admins = ref<AdminProperties[]>([]);

const searchQuery = ref('');
const per_page = ref(10);
const currentPage = ref(1);
const search = ref(2);
const total_pages = ref(0);

const options = ref<Options>({
  page: 1,
  itemsPerPage: 12,
})

// 👉 Fetching admins
const fetchAdmins = () => {
  adminListStore.fetchAdmins({
    search: searchQuery.value,
    currentPage: currentPage.value,
    page: currentPage.value,
  }).then(({ data }) => {
    total_pages.value = data.admins.last_page;

    admins.value = data.admins.data;
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchAdmins)

const loadItems = ref(false);
</script>

<template>
  <div>
    <VBreadcrumbs :items="items"></VBreadcrumbs>
    <VCard class="ga-3 pt-3">
      <VCardActions class="justify-space-between ga-3">
        
        <VCardTitle>Create New Admin</VCardTitle>
        <div>
          <VBtn color="primary" variant="tonal" href="/admin/create">
            Add New Admin
          </VBtn>
        </div>
  
      </VCardActions>
      <VContainer>
        <VTable>
          <THead>
            <tr>
              <TH class="text-left">
                Name
              </TH>
              <TH class="text-left">
                Email
              </TH>
              <TH class="text-left">
                Phone
              </TH>
              <TH class="text-left">
                Status
              </TH>
              <!-- <TH class="text-left">
                Role
              </TH> -->
            </tr>
          </THead>
          <TBody>
            <tr
              v-for="admin in admins"
              :key="admin.id"
            >
              <td>{{ admin.name }}</td>
              <td>{{ admin.email }}</td>
              <td>{{ admin.number }}</td>
              <td>
                <span v-if="admin.enabled === 1" style="background-color: seagreen; border-radius: 4px; padding: 8px;color:white">Active</span>
                <span v-else style="background-color: red; border-radius: 4px; padding: 8px;color:white">Un Active</span>
              </td>
            </tr>
          </TBody>
        </VTable>
        
      </VContainer>
    </VCard>
  </div>
</template>


