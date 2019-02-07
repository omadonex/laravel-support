<template>
    <el-dialog
            ref="dialog"
            width="750px"
            @close="clearForm()"
            :title="title"
            :visible.sync="isOpen">
        <slot :model="model" :shown="isOpen"></slot>
    </el-dialog>
</template>

<script>
  export default {
    name: 'OmxElementModelListEditor',

    data() {
      return {
        isOpen: false,
        isCreatingModel: false,
        model: null,
      };
    },

    props: {
      modelList: { type: [Array, Object], required: true },
      storePropKey: { type: String, default: null },
      title: { type: String, default: '' },
    },

    methods: {
      clearForm() {
        this.$refs.dialog.$children[0].formModel__reset();
      },

      closeForm() {
        this.isOpen = false;
      },

      openForm(model) {
        this.model = model;
        this.isOpen = true;
      },

      editModel(model) {
        this.isCreatingModel = false;
        this.openForm(model);
      },

      newModel() {
        this.isCreatingModel = true;
        this.openForm({});
      },

      saveModel(model) {
        if (this.storePropKey) {
          this.$store.commit('page/pl__addItemToList', {
            propKey: this.storePropKey,
            creating: this.isCreatingModel,
            item: model,
          });
        } else {
          let list = Array.isArray(this.modelList) ? this.modelList : this.modelList[this.modelList.meta.current_page];
          const index = this.isCreatingModel ? list.length : list.findIndex(item => item.id === model.id);
          if (index > -1) {
            list.splice(index, 1, model);
          }
        }

        if (this.isCreatingModel) {
          this.$emit('stored', model);
        } else {
          this.$emit('updated', model);
        }

        this.closeForm();
      },
    },
  };
</script>
