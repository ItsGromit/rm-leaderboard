<template>
  <div class="flex flex-col lg:flex-row gap-16">
    <div class="flex flex-col xl:flex-row align-middle max-w-fit">
      <div class="m-4 font-coolvetica text-xl">
        <h1 class="font-coolvetica text-4xl my-2 text-left">RMC Rules</h1>
        <p>The goal is to get as many author medals as possible on random maps within an hour!</p>
        <br>
        <ul class="list-disc list-inside">
          <li>
            Use the
            <a href="https://openplanet.dev/plugin/mxrandom" target="_blank"
              >ManiaExchange Random Map Picker Plugin</a
            >
          </li>
          <li>
            You can skip if:
            <ul class="list-disc list-inside ml-4">
              <li>One map per run for free</li>
              <li>Maps as soon as you get gold medal, these are the tie breaker</li>
              <li>Maps with an Author time > 3 minutes</li>
              <li>Maps with impossible Author times (e.g. broken PF maps, cheated ATs)</li>
              <li>Maps with old ice physics</li>
              <li>
                Maps which are technically finishable but clearly(!) broken ( fully broken textures,
                missing blocks, very reliant on old physics )
              </li>
            </ul>
          </li>
          <li>Cam 7 is allowed while the timer runs</li>
          <li>Ghosts other than personal best are not allowed but GPS is allowed.</li>
          <li>You can pause/resume the timer for breaks</li>
          <li>
            If you have already beaten Gold medal time or AT before (also on own maps), beat the
            respective time again. It is recommended to use a medals window plugin in OpenPlanet to
            see the medal times. A checkpoint counter is also allowed.
          </li>
          <li>You can turn off Skin/Mod downloads</li>
          <li>
            Some helper plugins are not allowed, see the rules inside of the plugin for the exact
            ones.
          </li>
        </ul>
      </div>
      <div class="">
        <img src="@/assets/img/rmc.png" class="h-100 mt-10" />
      </div>
    </div>
    <div class="flex flex-col xl:flex-row  align-middle max-w-fit">
      <div class="m-4 font-coolvetica text-xl">
        <h1 class="font-coolvetica text-4xl my-2 text-left">RMS Rules</h1>
        <p>The goal is to get as many author medals as possible before the timer runs out!</p>
        <br>
        <ul class="list-disc list-inside">
          <li>
            Use the
            <a href="https://openplanet.dev/plugin/mxrandom" target="_blank"
              >ManiaExchange Random Map Picker Plugin</a
            >
          </li>
          <li>
            You can skip if:
            <ul class="list-disc list-inside ml-4">
              <li>Any map for a cost: One minute of your maximum time (starting with 15 minutes) You can't exceed 15 minutes</li>
              <li>Maps with impossible Author times (e.g. broken PF maps, cheated ATs)</li>
              <li>Maps with old ice physics</li>
              <li>
                Maps which are technically finishable but clearly(!) broken ( fully broken textures,
                missing blocks, very reliant on old physics )
              </li>
            </ul>
          </li>
          <li>Cam 7 is allowed while the timer runs</li>
          <li>Ghosts other than personal best are not allowed but GPS is allowed.</li>
          <li>You can pause/resume the timer for breaks.</li>
          <li>
            If you have already beaten Gold medal time or AT before (also on own maps), beat the
            respective time again. It is recommended to use a medals window plugin in OpenPlanet to
            see the medal times. A checkpoint counter is also allowed.
          </li>
          <li>You can turn off Skin/Mod downloads</li>
          <li>
            Some helper plugins are not allowed, see the rules inside of the plugin for the exact
            ones.
          </li>
        </ul>
      </div>
      <div class="">
        <img src="@/assets/img/rms.png" class="h-100 mt-8" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts"></script>

<style scoped></style>
<style scoped></style>
