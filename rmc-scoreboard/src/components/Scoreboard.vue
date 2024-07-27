<template>
  <div>
    <div class="flex flex-row gap-2 items-center m-2">
      <h1 class="font-coolvetica text-4xl pl-2 my-2 text-left">
        {{ props.type?.toUpperCase() }} Records
      </h1>
      <Dropdown :options="options" @update:selected="handleTimeSelection" />
      <Dropdown :options="optionsMedals" @update:selected="handleGoalSelection" />
      <v-icon v-if="loading" name="fa-spinner" fill="white" animation="spin" />
    </div>

    <p class="pl-4">Survive as long as you can, every Author medal replenishes your timer!</p>
    <div
      v-for="(item, index) in paginatedData"
      :key="index"
      class="scoreboard m-2 my-4 md:m-4 text-sm md:text-lg font-coolvetica"
    >
      <div
        class="flex flex-col p-4 bg-slate-900 md:flex-row md:justify-between"
        :class="{ 'gradient-border': index === 0 && currentPage === 1 }"
      >
        <div class="flex items-center justify-between md:justify-start md:w-full">
          <div class="flex-shrink-0 w-1/12 flex items-center">
            <span>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</span>
          </div>
          <div class="flex-shrink-0 w-1/4 flex items-center">
            <v-icon
              v-if="index === 0 && currentPage === 1"
              name="fa-trophy"
              fill="gold"
              animation="pulse"
              speed="slow"
              class="mr-2"
            />
            <span>{{ item.nickname }}</span>
          </div>
          <div class="flex-shrink-0 w-1/4 flex flex-row items-center">
            <img :src="objectiveImages.at" class="h-6 mx-2" />
            <span>{{ item.goals }}</span>
            
            <!-- For 'rmc' type -->
            <template v-if="props.type === 'rmc'">
              <img :src="objectiveImages.secondary" class="h-6 mx-2" />
              <span>{{ isRMC(item) ? item.belowGoals : '' }}</span>
            </template>

            <!-- For 'rms' type -->
            <template v-if="props.type === 'rms'">
              <img :src="skipImage" class="h-6 mx-2" />
              <span>{{ item.skips }}</span>
              <span>&nbsp;|&nbsp;{{ formatTimeSurvived(isRMS(item) ? item.timeSurvived : 0) }}</span>
            </template>
          </div>
          <div class="flex-shrink-0 w-1/12 flex items-center justify-end md:hidden">
            <v-icon
              title="verified"
              v-if="item.verified"
              name="fa-check"
              fill="green"
              class="mr-2"
            />
          </div>
        </div>
        <div class="flex items-center justify-between md:w-1/4 mt-2 md:mt-0 self-end">
          <span>{{ formatTimeStamp(item.timestamp) }}</span>
          <div class="flex-shrink-0 w-1/12 items-center justify-end hidden md:flex">
            <v-icon
              title="verified"
              v-if="item.verified"
              name="fa-check"
              fill="green"
              class="mr-2"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="flex justify-between p-4">
      <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
      <span>Page {{ currentPage }} of {{ totalPages }} ({{ sortedData.length }} results)</span>
      <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue';
import axios from 'axios';
import Dropdown from './Dropdown.vue';

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

const currentYear = new Date().getFullYear();
const years = Array.from({ length: currentYear - 2023 + 1 }, (_, i) =>
  (currentYear - i).toString()
);

const options = ref([...years, 'All Time']);
const optionsMedals = ref(['AT', 'GOLD', 'SILVER', 'BRONZE']);
const selectedTime = ref<string | null>(currentYear.toString());
const selectedGoal = ref<string | null>('AT');

const handleGoalSelection = (option: string) => {
  selectedGoal.value = option;
};

const handleTimeSelection = (option: string) => {
  selectedTime.value = option;
};

// DATA PART MAYBE PUT IN A STORE LATER ------------------------------------------------------------

const loading = ref(false);
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

const sortedData = computed(() => {
  return data.value.slice().sort((a, b) => {
    if (b.ats !== a.ats) {
      return b.ats - a.ats;
    } else if ((b.golds ?? 0) !== (a.golds ?? 0)) {
      return (b.golds ?? 0) - (a.golds ?? 0);
    } else {
      return new Date(a.timestamp).getTime() - new Date(b.timestamp).getTime();
    }
  });
});

// Pagination state
const currentPage = ref(1);
const itemsPerPage = ref(10);

const totalPages = computed(() => {
  return Math.ceil(sortedData.value.length / itemsPerPage.value);
});

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return sortedData.value.slice(start, end);
});

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const fetchData = async (time: string | null, goal: string | null) => {
  loading.value = true;
  try {
    rmcData.value = [];
    rmsData.value = [];
    if (props.type === 'rmc') {
      const response = await axios.get('https://www.flinkblog.de/RMC/dev/api/rmc.php', {
        params: { time, goal }
      });
      rmcData.value = response.data;
    } else {
      const response = await axios.get('https://www.flinkblog.de/RMC/dev/api/rms.php', {
        params: { time, goal }
      });
      rmsData.value = response.data;
    }
  } catch (error) {
    console.error(`Error fetching ${props.type} data:`, error);
  } finally {
    loading.value = false;
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
  fetchData(selectedTime.value, selectedGoal.value);
});

watch([selectedTime, selectedGoal], ([newTime, newGoal]) => {
  fetchData(newTime, newGoal);
});
</script>

<style scoped></style>
