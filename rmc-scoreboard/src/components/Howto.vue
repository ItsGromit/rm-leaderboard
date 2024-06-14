<template>
  <div class="flex flex-col lg:flex-row gap-16" id="rules" ref="rules">
    <div class="flex flex-col xl:flex-row align-middle max-w-fit">
      <div class="m-4 font-coolvetica text-xl">
        <h1 class="font-coolvetica text-4xl my-2 text-left">How to Play</h1>
        <p>To play you need our custom Plugin.</p>
        <br />
        <ol type="1" class="list-decimal list-inside">
          <li>
            Use Openplanet andthe
            <a href="https://openplanet.dev/plugin/mxrandom" target="_blank" class="underline"
              >ManiaExchange Random Map Picker Plugin</a
            >
          </li>
          <li>Open the plugin with F3 ingame</li>
          <li>Play!</li>
        </ol>
      </div>
      <div class="">
        <img src="@/assets/img/plugin.png" class="h-100 mt-10" />
      </div>
    </div>
    <div class="flex flex-col xl:flex-row align-middle max-w-fit">
      <div class="">
        <img src="@/assets/img/plugin2.png" class="h-100 mt-8" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts"></script>

<style scoped></style>
