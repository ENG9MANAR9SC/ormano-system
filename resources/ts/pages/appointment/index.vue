<script lang="ts">

</script>

<template>
  <div>
    <VCard>
      <VCardActions class="justify-space-between ga-3">
        <VCardTitle>{{ $t('Add New Appointment') }}</VCardTitle>
        <div>
          <VBtn color="primary" variant="tonal" href="/admin/create">
            {{ $t('Add New Appointment') }}   
          </VBtn>
        </div>
      </VCardActions>
      <VCardBody>
        <VContainer>

          <VCalendar
          type="month"
          value="2021-09-01"
          >
          <template>
            <div>
              <v-sheet
                class="d-flex"
                height="54"
                tile
              >
                <v-select
                  v-model="type"
                  :items="types"
                  class="ma-2"
                  label="View Mode"
                  variant="outlined"
                  dense
                  hide-details
                ></v-select>
                <v-select
                  v-model="weekday"
                  :items="weekdays"
                  class="ma-2"
                  label="weekdays"
                  variant="outlined"
                  dense
                  hide-details
                ></v-select>
              </v-sheet>
              <v-sheet>
                <v-calendar
                  ref="calendar"
                  v-model="value"
                  :events="events"
                  :view-mode="type"
                  :weekdays="weekday"
                ></v-calendar>
              </v-sheet>
            </div>
          </template>
          </VCalendar>
        </VContainer>
      </VCardBody>
    </VCard>

  </div>
</template>
