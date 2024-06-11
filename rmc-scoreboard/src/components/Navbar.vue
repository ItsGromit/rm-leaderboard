<template>
  <nav class="p-4 font-coolvetica text-xl">
    <div class="container mx-auto flex items-center justify-between">
      <!-- Left Links -->
      <div class="flex space-x-4">
        <a router-link to="/" class="text-white border-b-4 border- p-1 hover:border-white">Home</a>
        <a href="#rules" class="text-white border-b-4 border- p-1 hover:border-white">Rules</a>
      </div>

      <!-- Logo -->
      <div class="flex-shrink-0">
        <img src="@/assets/img/logo.webp" alt="Logo" width="350" height="350" />
      </div>

      <!-- Right Links -->
      <div class="flex space-x-4">
        <a href="#" class="text-white border-b-4 border- p-1 hover:border-white">How To Play</a>
        <a href="#" class="text-white border-b-4 border- p-1 hover:border-white">Contact</a>
      </div>
    </div>
  </nav>
</template>
