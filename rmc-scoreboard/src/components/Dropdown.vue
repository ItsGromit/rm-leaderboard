<template>
  <div class="dropdown">
    <button @click="toggleDropdown" class="dropdown-toggle">
      {{ selectedOption ? selectedOption : 'Select an option' }}
      <v-icon name="fa-chevron-down" fill="gold" />
    </button>
    <ul v-if="isOpen" class="dropdown-menu">
      <li
        v-for="option in options"
        :key="option"
        @click="selectOption(option)"
        class="dropdown-item"
      >
        {{ option }}
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'Dropdown',
  props: {
    options: {
      type: Array as () => string[],
      required: true
    }
  },
  emits: ['update:selected'],
  setup(props, { emit }) {
    const isOpen = ref(false);
    const selectedOption = ref<string | null>(props.options[0] || null);

    const toggleDropdown = () => {
      isOpen.value = !isOpen.value;
    };

    const selectOption = (option: string) => {
      selectedOption.value = option;
      isOpen.value = false;
      emit('update:selected', option);
    };

    return {
      isOpen,
      selectedOption,
      toggleDropdown,
      selectOption
    };
  }
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
