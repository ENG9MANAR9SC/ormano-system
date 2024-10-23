
<script lang="ts" setup>
import type { CourtProperties } from '@/views/pages/courts/types';
import { useCourtListStore } from '@/views/pages/courts/useCourtListStore';
import { useI18n } from 'vue-i18n';

const courts = ref<CourtProperties[]>([]);
const courtListStore = useCourtListStore();

const searchQuery = ref('');
const per_page = ref(10);
const currentPage = ref(1);
const search = ref(2);
const total_pages = ref(0);

const options = ref<Options>({
  page: 1,
  itemsPerPage: 12,
})
import { ref } from 'vue';
const { t } = useI18n();

const items = ref([
  { text: t('Home'), disabled: false, href: '/' },
  { text: t('Courts'), disabled: false, href: '/court' },
]);


// 👉 Fetching courts
const fetchCourts = () => {
  courtListStore.fetchCourts({
    search: searchQuery.value,
    currentPage: currentPage.value,
    page: currentPage.value,
  }).then(({ data }) => {
    total_pages.value = data.courts.last_page;

    courts.value = data.courts.data;
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchCourts)

const loadItems = ref(false);
</script>

<template>
  <div>
    <VBreadcrumbs :items="items"></VBreadcrumbs>
    <VCard class="ga-3 pt-3">
      <VCardActions class="justify-space-between ga-3">
        
        <VCardTitle>{{ $t('Add New Court') }}</VCardTitle>
        <div>
          <VBtn color="primary" variant="tonal" href="/admin/create">
            {{ $t('Add New Court') }}   
          </VBtn>
        </div>
  
      </VCardActions>
      <VCardBody>
        <VContainer>
          <VTable>
            <THead>
              <tr>
                <TH class="text-left">
                 {{ $t('Name') }} 
                </TH>
                <TH class="text-left">
                  {{ $t('Description') }}   
                </TH>
                <TH class="text-left">
                  {{ $t('City') }}     
                </TH>
                <TH class="text-left">
                  {{ $t('Address') }}   
                </TH>
                <TH class="text-left">
                  {{ $t('Phone') }}    
                </TH>
              </tr>
            </THead>
            <TBody>
              <tr
                v-for="court in courts"
                :key="court.id"
              >
                <td>{{ court.name }}</td>
                <td>{{ court.desc }}</td>
                <td>{{ court.city_id }}</td>
                <td>{{ court.address }}</td>
                <td>{{ court.phone }}</td>
              </tr>
            </TBody>
          </VTable>
        </VContainer>
      </VCardBody>
      </VCard>

  </div>
</template>
