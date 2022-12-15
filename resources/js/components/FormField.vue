<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :show-help-text="showHelpText"
    :full-width-content="fullWidthContent"
  >
    <template #field>
      <div class="w-full">
        <div v-for="(permissions, group) in field.options" :key="group" class="mb-4">
          <h1 class="font-normal text-xl md:text-xl mb-3 my-2">
            <checkbox :checked="isGroupChecked(group)" @click="toggleGroup(group)"/>
            <label class="w-full ml-1" @click="toggleGroup(group)">
              {{ __(group) }}
            </label>
          </h1>
          <div class="grid grid-cols-4 gap-4 break-words">
            <div v-for="(permission, option) in permissions" :key="permission.option">
              <checkbox
                :value="permission.option"
                :checked="isChecked(permission.option)"
                @input="toggleOption(permission.option)"
              />
              <label
                :for="field.name"
                v-text="permission.label"
                @click="toggleOption(permission.option)"
                class="w-full ml-1"
              ></label>
              </div>
          </div>
        </div>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';

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
  data: {
    checkedGroups: [],
  },
  methods: {
    avaiableOptions(group) {
      return this.field.options[group];
    },

    checkAll(group) {
      this.avaiableOptions(group).forEach(
        (permission) => this.check(permission.option)
      );
    },

    uncheckAll(group) {
      this.avaiableOptions(group).forEach(
        (permission) => this.uncheck(permission.option)
      );
    },

    isChecked(option) {
      return this.value && this.value.includes(option);
    },

    isGroupChecked(group) {
      return this.checkedGroups.includes(group);
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
      const index = this.checkedGroups.indexOf(group);
      const checked = index > -1;

      if (checked) {
        this.checkedGroups.splice(index, 1);
      } else {
        this.checkedGroups.push(group)
      }

      this.avaiableOptions(group).forEach(
        (permission) => checked
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
