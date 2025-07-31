<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :show-help-text="showHelpText"
    :full-width-content="fullWidthContent"
  >
    <template #field>
      <div class="w-full space-y-6">
        <!-- Search Input -->
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="__('Search permissions...')"
            class="w-full py-2 pl-10 pr-4 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          />
          <div class="absolute inset-y-0 search-icon-position flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>
        <div v-for="(permissions, group) in filteredOptions" :key="group"
             v-show="permissions.length > 0"
             class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
          <!-- Group Header with Toggle -->
          <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-medium text-gray-900 dark:text-gray-100">
              {{ __(group) }}
            </h2>
            <button
              type="button"
              @click="toggleGroup(group)"
              class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
              :style="{ backgroundColor: isGroupChecked(group) ? 'rgb(var(--colors-primary-500))' : '' }"
              :class="{ 'bg-gray-200 dark:bg-gray-600': !isGroupChecked(group) }"
              :aria-pressed="isGroupChecked(group)"
              aria-label="Toggle all permissions in group"
            >
              <span
                class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow ring-0 transition-all duration-200 ease-in-out"
                :style="{ transform: isGroupChecked(group) ? 'translateX(1rem)' : 'translateX(0)' }"
              />
            </button>
          </div>

          <!-- Individual Permissions -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            <div v-for="permission in permissions" :key="permission.option"
                 class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-md shadow-sm">
              <label
                :for="`permission-${permission.option}`"
                class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer flex-1 mr-3"
                @click.prevent="toggleOption(permission.option)"
              >
                {{ permission.label }}
              </label>
              <button
                type="button"
                :id="`permission-${permission.option}`"
                @click="toggleOption(permission.option)"
                class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1"
                :style="{ backgroundColor: isChecked(permission.option) ? 'rgb(var(--colors-primary-500))' : '' }"
                :class="{ 'bg-gray-200 dark:bg-gray-600': !isChecked(permission.option) }"
                :aria-pressed="isChecked(permission.option)"
                :aria-label="`Toggle ${permission.label} permission`"
              >
                <span
                  class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow ring-0 transition-all duration-200 ease-in-out"
                  :style="{ transform: isChecked(permission.option) ? 'translateX(1rem)' : 'translateX(0)' }"
                />
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import Fuse from 'fuse.js';

export default {
  mixins: [
    FormField,
    HandlesValidationErrors
  ],
  props: [
    'resourceName',
    'resourceId',
    'field'
  ],
  data() {
    return {
      searchQuery: '',
      fuse: null
    }
  },
  computed: {
    filteredOptions() {
      if (!this.searchQuery) {
        return this.field.options;
      }

      const filtered = {};
      const searchResults = this.fuse.search(this.searchQuery);
      const matchedOptions = new Set(searchResults.map(result => result.item.option));

      // Rebuild the grouped structure with only matched permissions
      Object.entries(this.field.options).forEach(([group, permissions]) => {
        const filteredPermissions = permissions.filter(permission => 
          matchedOptions.has(permission.option)
        );
        
        if (filteredPermissions.length > 0) {
          filtered[group] = filteredPermissions;
        }
      });

      return filtered;
    }
  },
  mounted() {
    this.initializeFuse();
  },
  methods: {
    initializeFuse() {
      // Flatten all permissions for Fuse.js
      const allPermissions = [];
      Object.entries(this.field.options).forEach(([group, permissions]) => {
        permissions.forEach(permission => {
          allPermissions.push({
            ...permission,
            group: group
          });
        });
      });

      // Configure Fuse.js for fuzzy searching
      const fuseOptions = {
        keys: [
          { name: 'label', weight: 0.7 },
          { name: 'option', weight: 0.2 },
          { name: 'group', weight: 0.1 }
        ],
        threshold: 0.3,
        includeScore: true,
        shouldSort: true
      };

      this.fuse = new Fuse(allPermissions, fuseOptions);
    },

    availableOptions(group) {
      return this.filteredOptions[group] || [];
    },

    checkAll(group) {
      this.availableOptions(group).forEach(
        (permission) => this.check(permission.option)
      );
    },

    uncheckAll(group) {
      this.availableOptions(group).forEach(
        (permission) => this.uncheck(permission.option)
      );
    },

    isChecked(option) {
      return this.value && this.value.includes(option);
    },

    isGroupChecked(group) {
      const permissions = this.availableOptions(group);

      if (!permissions || permissions.length === 0) {
        return false;
      }

      return permissions.every(permission => this.isChecked(permission.option));
    },

    check(option) {
      if (!this.isChecked(option)) {
        this.value.push(option);
      }
    },

    uncheck(option) {
      if (this.isChecked(option)) {
        this.value = this.value.filter(item => item != option);
      }
    },

    toggleGroup(group) {
      const isCurrentlyChecked = this.isGroupChecked(group);

      this.availableOptions(group).forEach(
        (permission) => isCurrentlyChecked
          ? this.uncheck(permission.option)
          : this.check(permission.option)
      )
    },

    toggleOption(option) {
      this.isChecked(option) ? this.uncheck(option) : this.check(option);
    },

    setInitialValue() {
      this.value = this.field.value || [];
    },

    fill(formData) {
      formData.append(this.field.attribute, this.value || []);
    },

    handleChange(value) {
      this.value = value;
    }
  }
};
</script>
