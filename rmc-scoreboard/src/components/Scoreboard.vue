<template>
  <div>
    <div v-for="(item, index) in data" :key="index" class="m-4 text-lg font-coolvetica">
      <div
        class="flex flex-row justify-between p-4 bg-slate-900"
        :class="{ 'gradient-border': index < 1 }"
      >
        <div class="flex-shrink-0 w-1/4 flex items-center">
          <v-icon
            v-if="index < 1"
            name="fa-trophy"
            fill="gold"
            animation="pulse"
            speed="slow"
            class="mr-2"
          />
          <span>{{ item.nickname }}</span>
        </div>
        <div class="flex-shrink-0 w-1/4 flex flex-row items-center">
          <img src="@/assets/img/at.png" class="h-6 mx-2" />
          <span>{{ item.ats }}</span>
          <img src="@/assets/img/gold.png" class="h-6 mx-2" />
          <span>{{ item.golds ?? item.skips }}</span>
        </div>
        <div class="flex-shrink-0 w-1/4 flex items-center">
          <span>{{ formatTimeStamp(item.timestamp) }}</span>
        </div>
        <div class="flex-shrink-0 w-1/4 flex items-center justify-end">
          <span>{{ item.verified }}</span>
        </div>
      </div>
    </div>
    <div><p class="pl-4">{{ data.length }} results</p></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

interface RecordData {
  timestamp: string;
  nickname: string;
  ats: number;
  golds?: number;
  skips?: number;
  verified: number;
}

const props = defineProps({
  type: String
});

const rmcData = ref<RecordData[]>([]);
const rmsData = ref<RecordData[]>([]);

const headers = computed(() => {
  return props.type === 'rmc'
    ? [
        { title: 'Nickname', key: 'nickname' },
        { title: 'ATS', key: 'ats' },
        { title: 'Golds', key: 'golds' },
        { title: 'Timestamp', key: 'timestamp' },
        { title: 'Verified', key: 'verified' }
      ]
    : [
        { title: 'Nickname', key: 'nickname' },
        { title: 'ATS', key: 'ats' },
        { title: 'Skips', key: 'skips' },
        { title: 'Timestamp', key: 'timestamp' },
        { title: 'Verified', key: 'verified' }
      ];
});

const data = computed(() => {
  return props.type === 'rmc' ? rmcData.value : rmsData.value;
});

const fetchData = async () => {
  try {
    if (props.type === 'rmc') {
      const response = await axios.get('https://www.flinkblog.de/RMC/dev/api/rmc.php');
      rmcData.value = response.data;
    } else {
      const response = await axios.get('https://www.flinkblog.de/RMC/dev/api/rms.php');
      rmsData.value = response.data;
    }
  } catch (error) {
    console.error(`Error fetching ${props.type} data:`, error);
  }
};

const formatTimeStamp = (timestamp: string) => {
  const date = new Date(timestamp);
  return date.toLocaleString('en-EN', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
</style>
