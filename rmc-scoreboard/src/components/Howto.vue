<template>
  <div class="flex flex-col lg:flex-row gap-16 p-6" id="howto" ref="howto">
    <div class="flex flex-col xl:flex-row items-center mx-auto">
      <div class="m-4 font-coolvetica text-xl">
        <h1 class="font-coolvetica text-4xl my-4 text-left">How to Play</h1>
        <p>To play you need our custom Plugin.</p>
        <br />
        <ol class="list-decimal list-inside pl-4">
          <li class="mb-2">
            Install
            <a
              href="https://openplanet.dev/"
              target="_blank"
              class="underline text-blue-500 hover:text-blue-700"
              >Openplanet</a
            >
          </li>
          <li class="mb-2">Open Openplanet with F3 in-game and navigate to Plugin Manager</li>
          <li class="mb-2">Search for the ManiaExchange Random Map Picker and install</li>
          <li class="mb-2">Under the plugins tab choose TMX Randomizer -> Random Map Challenge</li>
          <li>Play!</li>
        </ol>
      </div>
      <div class="m-4">
        <img
          src="@/assets/img/plugin.png"
          class="w-full max-w-xl mx-auto rounded shadow-lg mt-4 xl:mt-0"
        />
      </div>
    </div>
    <div class="flex flex-col xl:flex-row items-center max-w-2xl mx-auto">
      <div class="m-4">
        <img
          src="@/assets/img/plugin2.png"
          class="w-full max-w-sm mx-auto rounded shadow-lg mt-4 xl:mt-0"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts"></script>
