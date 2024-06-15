<template>
  <nav class="p-4 font-coolvetica md:text-xl text-md">
    <div class="container mx-auto flex items-center justify-between">
      <!-- Left Links -->
      <div class="flex space-x-4">
        <a router-link to="/" class="text-white border-b-4 border- p-1 hover:border-white">Home</a>
        <a
          href="#rules"
          @click.prevent="scrollToSection('rules')"
          class="text-white border-b-4 border- p-1 hover:border-white"
          >Rules</a
        >
      </div>

      <!-- Logo -->
      <div class="flex-shrink-0 w-32 md:w-80">
    <img src="@/assets/img/logo.webp" alt="Logo" class="w-full" />
  </div>

      <!-- Right Links -->
      <div class="flex space-x-4">
        <a
          href="#howto"
          @click.prevent="scrollToSection('howto')"
          class="text-white border-b-4 border- p-1 hover:border-white"
          >How To Play</a
        >
        <a
          href="#about"
          @click.prevent="scrollToSection('about')"
          class="text-white border-b-4 border- p-1 hover:border-white"
          >About</a
        >
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { defineEmits } from 'vue';

const emit = defineEmits(['scroll-to-section']);

const scrollToSection = (section: string) => {
  emit('scroll-to-section', section);
};
</script>

<style scoped>
a {
  text-decoration: none;
}
</style>
