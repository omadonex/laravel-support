<template>
  <i :class="[{ 'cursor--pointer': !disabled }, finalDisabledClass, icon, iconColor, iconSize]"
     :title="iconTitle"
     @click="click"></i>
</template>

<script>
  export default {
    name: 'OmxElementIconButton',

    props: {
      icon: { type: String, required: true },
      iconColor: { type: String, default: null },
      iconSize: { type: String, default: 'icn--normal' },
      iconTitle: { type: String, default: null },
      disabled: { type: Boolean, default: false },
      disabledClass: { type: String, default: 'icn--disabled' },
    },

    computed: {
      finalDisabledClass() {
        return this.disabled ? this.disabledClass : null;
      }
    },

    methods: {
      click() {
        if (!this.disabled) {
          this.$emit('clicked');
        }
      }
    }
  };
</script>
