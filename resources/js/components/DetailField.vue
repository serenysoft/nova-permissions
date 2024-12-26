<template>
  <PanelItem :field="field">
    <template #value>
      <div class="grid gap-4">
        <div v-for="(permissions, group) in field.options" :key="group">
          <h1 class='font-normal text-lg mb-1 mt-2'>
            {{ __(group) }}
          </h1>
          <div class="grid grid-cols-4 gap-4">
            <div v-for="(permission, option) in permissions" :key="option" class="flex items-center">
              <Icon
                :name="hasPermission(permission.option) ? 'check-circle' : 'x-circle'"
                :class="hasPermission(permission.option) ? 'text-green-500' : 'text-red-500'"
                class="inline-block"
              />
              <span class="ml-1">{{ permission.label }}</span>
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
