<script lang="ts" setup>
import type { UserProperties } from '@/views/pages/users/types';
import { useUserListStore } from '@/views/pages/users/useUserListStore';

interface Props {
  user: UserProperties;
}

const emit = defineEmits(['fetch-users']);

const props = defineProps<Props>();
const isLoading = ref(false);

const { refreshUserBalance, deleteUser } = useUserListStore();

const refreshBalanceEvt = () => {
  isLoading.value = true;
  refreshUserBalance(props.user.id)
    .then((res) => props.user.balance = res.data.balance)
    .then(() => isLoading.value = false)
    .catch(err => console.error(err));
}

const deleteUserEvt = () => {
  deleteUser(props.user.id)
    .then(() => emit('fetch-users'))
    .catch(err => console.error(err));
}

</script>

<template>
  <VCard :loading="isLoading">
    <div class="vertical-more">

      <MoreBtn
        :menu-list="[
          { title: 'Edit', prependIcon: 'tabler-edit', to: { name: 'user-id', params: { id: user.id } },},
          { title: 'Add appointment', prependIcon: 'tabler-plus', to: {name: 'appointment-create', query: { patientId: user.id, patientName: user.full_name } } },
          { title: 'Add payment', prependIcon: 'tabler-file-dollar', to:{ name: 'payment-create', query: { patientId: user.id, patientName: user.full_name  } } },
          { title: 'Refresh balance', prependIcon: 'tabler-refresh', onClick: refreshBalanceEvt },
          { type: 'divider', class: 'my-2' },
          { title: 'Delete', prependIcon: 'tabler-trash', class: 'text-error', onClick: deleteUserEvt },

        ]"
        item-props
      />
    </div>

    <VCardItem>
      <VCardTitle class="d-flex flex-column align-center justify-center">
        <VAvatar
          size="100"
          :image="user.avatar"
        />

        <p class="mt-4 mb-0">
          {{ user.full_name }}
        </p>
        <span class="text-body-1">{{ user.phone_number }}</span>

        <!-- !!! -->
        <!-- <div class="d-flex align-center flex-wrap gap-2 mt-2">
          <VChip
            v-for="chip in user.chips"
            :key="chip.title"
            label
            :color="chip.color"
            size="small"
          >
            {{ chip.title }}
          </VChip>
        </div> -->
      </VCardTitle>
    </VCardItem>

    <VCardText>
      <VRow>
        <VCol cols="4" class="text-center">
          <h6 class="text-h6">
            {{ user.balance }}
          </h6>
          <span class="text-body-1">Balance</span>
        </VCol >
        <VCol cols="4" class="text-center">
          <h6 class="text-h6">
            {{ user.gender_title }}
          </h6>
          <span class="text-body-1">Gender</span>
        </VCol >
        <VCol cols="4" class="text-center">
          <h6 class="text-h6">
            {{ user.age }}
          </h6>
          <span class="text-body-1">Age</span>
        </VCol >
      </VRow>

      <div class="d-flex justify-center gap-4 mt-5">
        <VBtn
          prepend-icon="tabler-info-circle"
          variant="tonal"
          @click="() => $router.push({ name: 'user-id-show', params: { id: user.id } })"
        >
          Details
        </VBtn>


        <IconBtn
          variant="tonal"
          class="rounded"
          target="_blank"
          :href="`https://wa.me/${parseInt(user.phone_number)}`"
        >
          <VIcon icon="tabler-brand-whatsapp" />
        </IconBtn>
      </div>
    </VCardText>
  </VCard>
</template>

