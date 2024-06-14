<template>
  <div class="">
    <div class="flex flex-col xl:flex-row min-h-screen bg-gradient">
      <div class="xl:w-1/2">
        <Scoreboard :type="'rmc'" />
      </div>

      <div class="xl:w-1/2">
        <Scoreboard :type="'rms'" />
      </div>
    </div>

    <div class="flex min-h-screen bg-pattern text-gray-900 lg_p-10 p-4">
      <Rules />
    </div>
    <div class="flex min-h-screen bg-gradient lg:p-10 p-4">
      <Howto />
    </div>
  </div>
</template>

<script setup lang="ts">
import Scoreboard from '@/components/Scoreboard.vue';
import Rules from '@/components/Rules.vue';
import Howto from '@/components/Howto.vue';
</script>

<style scoped>
.bg-gradient {
  background: linear-gradient(to bottom, #000e27 66%, #112429 100%);
}
.bg-gradient-invert {
  background: #efefef;
}

.bg-pattern {
  background-color: #ffffff;
  opacity: 0.8;
  background-image: linear-gradient(#f6f6ff 2px, transparent 2px),
    linear-gradient(90deg, #f6f6ff 2px, transparent 2px),
    linear-gradient(#f6f6ff 1px, transparent 1px), linear-gradient(90deg, #f6f6ff 1px, #ffffff 1px);
  background-size:
    50px 50px,
    50px 50px,
    10px 10px,
    10px 10px;
  background-position:
    -2px -2px,
    -2px -2px,
    -1px -1px,
    -1px -1px;
}
</style>
