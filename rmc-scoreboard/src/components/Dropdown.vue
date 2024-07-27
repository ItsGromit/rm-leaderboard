<template>
  <div class="dropdown" ref="dropdown">
    <button @click="toggleDropdown" class="dropdown-toggle">
      {{ selectedOption ? selectedOption.charAt(0).toUpperCase() + selectedOption.slice(1) : 'Select an option' }}
      <v-icon name="fa-chevron-down" fill="gold" />
    </button>
    <ul v-if="isOpen" class="dropdown-menu">
      <li
        v-for="option in options"
        :key="option"
        @click="selectOption(option)"
        class="dropdown-item"
      >
        {{ option.charAt(0).toUpperCase() + option.slice(1)}}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';

interface Props {
  options: string[];
}

const props = defineProps<Props>();
const emit = defineEmits(['update:selected']);

const isOpen = ref(false);
const selectedOption = ref<string | null>(props.options[0] || null);
const dropdown = ref<HTMLElement | null>(null);

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const selectOption = (option: string) => {
  selectedOption.value = option;
  isOpen.value = false;
  emit('update:selected', option);
};

const handleClickOutside = (event: MouseEvent) => {
  if (dropdown.value && !dropdown.value.contains(event.target as Node)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  if (selectedOption.value) {
    emit('update:selected', selectedOption.value);
  }
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
.dropdown {
  position: relative;
  display: inline-block;
  font-family: 'coolvetica', sans-serif;
}

.dropdown-toggle {
  background-color: #112429;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 3px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s ease;
}

.dropdown-toggle:hover {
  background-color: #153138;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 3px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  z-index: 1000;
  color: #112429;
  margin: 0;
  padding: 0;
  list-style: none;
  width: 100%;
}

.dropdown-item {
  padding: 10px 20px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.dropdown-item:hover {
  background-color: #f1f1f1;
}
</style>
