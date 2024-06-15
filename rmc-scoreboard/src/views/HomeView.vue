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
    <div class="flex flex-row justify-around items-center bg-gray-100 text-gray-900 lg_p-10 p-12 ">
      <img src="@/assets/img/peepocheer.webp" />
      <h1 class="md:text-2xl align-middle text-purple-700"> Help this project by <a target="_blank" href="https://www.twitch.tv/flinktm" class="underline text-blue-500 hover:text-blue-700">dropping a sub!</a> </h1>
      <h1 class="md:text-2xl align-middle text-purple-700"> Or by <a target="_blank" "https://github.com/sponsors/GreepTheSheep" class="underline text-blue-500 hover:text-blue-700">becoming a sponsor!</a> </h1>
      <img src="@/assets/img/peepocheer.webp" />
    </div>
    <div class="flex bg-pattern text-gray-900 lg_p-10 p-12">
      <Rules />
    </div>
    <div class="flex bg-color lg:p-10 p-4">
      <Howto />
    </div>
    <div class="flex bg-footer border-t-8 border-black lg:p-10 p-4">
      <About />
    </div>
  </div>
</template>

<script setup lang="ts">
import Scoreboard from '@/components/Scoreboard.vue';
import Rules from '@/components/Rules.vue';
import Howto from '@/components/Howto.vue';
import About from '@/components/About.vue';
</script>

<style scoped>
.bg-footer {
  background-color: #000e27;
  opacity: 0.8;
  background-image: linear-gradient(
      30deg,
      #01060e 12%,
      transparent 12.5%,
      transparent 87%,
      #01060e 87.5%,
      #01060e
    ),
    linear-gradient(150deg, #01060e 12%, transparent 12.5%, transparent 87%, #01060e 87.5%, #01060e),
    linear-gradient(30deg, #01060e 12%, transparent 12.5%, transparent 87%, #01060e 87.5%, #01060e),
    linear-gradient(150deg, #01060e 12%, transparent 12.5%, transparent 87%, #01060e 87.5%, #01060e),
    linear-gradient(
      60deg,
      #01060e77 25%,
      transparent 25.5%,
      transparent 75%,
      #01060e77 75%,
      #01060e77
    ),
    linear-gradient(
      60deg,
      #01060e77 25%,
      transparent 25.5%,
      transparent 75%,
      #01060e77 75%,
      #01060e77
    );
  background-size: 26px 46px;
  background-position:
    0 0,
    0 0,
    13px 23px,
    13px 23px,
    0 0,
    13px 23px;
}
.bg-gradient {
  background: linear-gradient(to bottom, #000e27 50%, #112429 100%);
}
.bg-color {
  background: #000e27;
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
