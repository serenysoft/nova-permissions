<template>
  <PanelItem :field="field">
    <template #value>
      <div class="space-y-4">
        <div v-for="(permissions, group) in field.options" :key="group"
             class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
          <h2 class="text-base font-medium text-gray-900 dark:text-gray-100 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">
            {{ __(group) }}
          </h2>
          <div class="grid grid-cols-4 gap-4">
            <div v-for="(permission, option) in permissions" :key="option" 
                 class="flex items-center">
              <Icon
                :name="hasPermission(permission.option) ? 'check-circle' : 'x-circle'"
                :class="hasPermission(permission.option) ? 'text-green-500' : 'text-red-500'"
                class="inline-block flex-shrink-0"
              />
              <span class="ml-1 text-sm text-gray-700 dark:text-gray-300">{{ permission.label }}</span>
            </div>
          </div>
        </div>
      </div>
    </template>
  </PanelItem>
</template>

<script>
import { Icon } from 'laravel-nova-ui';

export default {
  components: { Icon },
  props: [
    'resource',
    'resourceName',
    'resourceId',
    'field'
  ],
  methods: {
    hasPermission(value) {
      return this.field.value && this.field.value.includes(value)
    }
  },
}
</script>
