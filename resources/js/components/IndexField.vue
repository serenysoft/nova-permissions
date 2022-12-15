<template>
  <div class="flex flex-wrap space-1 max-w-sm">
    <span v-for="(permission, option) in availableOptions"
      :key="permission.option"
      :class="hasOption(permission.option) ? 'bg-green-500' : 'bg-red-500'"
      :title="__(permission.label)"
      class="inline-block rounded-full w-2 h-2 mr-1 mb-1 flex-none" >
    </span>
  </div>
</template>

<script>
import { flatMap } from 'lodash';

export default {
  props: [
    'resourceName',
    'field'
  ],
  computed: {
    availableOptions() {
      return flatMap(this.field.options);
    },
  },
  methods: {
    hasOption(option) {
      return this.field.value && this.field.value.includes(option)
    },
  },
}
</script>
