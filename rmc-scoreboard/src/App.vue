<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router';
import Scoreboard from './components/Scoreboard.vue';
</script>

<template>
  <header>
    <nav>
      <div class="wrapper">
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/about">About</RouterLink>
      </div>
    </nav>
  </header>
  <main>
    <RouterView />
  </main>
</template>

<style scoped>
header {
  width: 100%;
  background-color: #333;
  color: #fff;
  padding: 1rem 0;
}

nav {
  display: flex;
  justify-content: center;
  align-items: center;
}

nav .wrapper {
  display: flex;
  gap: 1rem;
}

nav a {
  color: #fff;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-left: 1px solid #444;
}

nav a:first-of-type {
  border-left: 0;
}

nav a.router-link-exact-active {
  background-color: #555;
}

main {
  width: 100%;
  padding: 2rem;
  box-sizing: border-box;
}

/* Media queries for responsive design */
@media (max-width: 600px) {
  nav {
    flex-direction: column;
  }

  nav .wrapper {
    flex-direction: column;
    align-items: center;
  }
}
</style>
