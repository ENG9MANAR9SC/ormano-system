<script lang="ts" setup>
interface Props {
  menuList?: unknown[]
  itemProps?: boolean
}

const props = defineProps<Props>()
</script>

<template>
  <IconBtn
    density="compact"
    color="disabled"
  >
    <VIcon icon="tabler-dots-vertical" />

    <VMenu
      v-if="props.menuList"
      activator="parent"
    >
      <!-- <VList
        :items="props.menuList"
        :item-props="props.itemProps"
      /> -->
      <VList
        :item-props="props.itemProps"
      >
        <template v-for="item in props.menuList">
          <VListItem
            v-if="item.type !== 'divider'"
            :key="item.title"
            :to="item.to"
            @click="item.onClick"
            :class="item.class"
            :prepend-icon="item.prependIcon"
          >
            {{ item.type }}
              {{ item.title }}
          </VListItem>

          <VDivider
            v-else
            :class="item.class"
          />
        </template>

      </VList>
    </VMenu>
  </IconBtn>
</template>
