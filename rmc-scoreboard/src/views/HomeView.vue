<script setup lang="ts">
import Scoreboard from '../components/Scoreboard.vue';
</script>

<template>
  <div class="flex flex-col xl:flex-row">

    <div class="xl:w-1/2">
      <h1 class="font-coolvetica text-4xl my-2 text-left pl-4">RMC Records</h1>
      <p class="pl-4">Get as many Author medals in 1 hour as possible!</p>
      <Scoreboard :type="'rmc'"/>
    </div>
  

    <div class="xl:w-1/2">
      <h1 class="font-coolvetica text-4xl pl-4 my-2 text-left">RMS Records</h1>
      <p class="pl-4">Survive as long as you can, every Author medal replenishes your timer!</p>
      <Scoreboard :type="'rms'" />
    </div>
  </div>
</template>