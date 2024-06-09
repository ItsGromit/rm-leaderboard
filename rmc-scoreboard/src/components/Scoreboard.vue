<template>
  <v-container>
    <h1>Random Map Challenge Records</h1>
    <v-data-table :headers="headers" :items="data" class="elevation-1">
      <template v-slot:[`item.timestamp`]="{ item }">
        {{ formatTimeStamp(item.timestamp) ?? "unknown" }}
      </template>
      <template v-slot:[`item.verified`]="{ item }">
        <div v-if="item.verified">verified</div>
        <div v-else></div>
      </template>
    </v-data-table>
  </v-container>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

interface RmcData {
  timestamp: string
  nickname: string
  ats: number
  golds: number
  verified: number
}

const data = ref<RmcData[]>([])
const headers = [
  { title: 'Nickname', key: 'nickname' },
  { title: 'ATS', key: 'ats' },
  { title: 'Golds', key: 'golds' },
  { title: 'Timestamp', key: 'timestamp' },
  { title: 'Verified', key: 'verified' },
]

const fetchRmcData = async () => {
  try {
    const response = await axios.get('https://www.flinkblog.de/RMC/dev/api/rmc.php');
    data.value = response.data;
  } catch (error) {
    console.error('Error fetching RMC data:', error);
  }
};

const formatTimeStamp = (timestamp: string) => {
  const date = new Date(timestamp)
  return date.toLocaleString('en-EN', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

onMounted(fetchRmcData)
</script>

<style>
.v-data-table {
  border-radius: 15px 0px;
}

.v-data-table-header__content {
  font-size: 1.3rem;
}
.v-data-table__td {
  font-size: 1.2rem;
}

.v-data-table__tr{
  border: 3px solid red;
}

.v-data-table__tr:nth-child(1) {
  font-size: 1.2rem;
  color: gold;
  border: 3px solid red;
  border-color: red green blue yellow; /* Colorful border */
}
</style>
