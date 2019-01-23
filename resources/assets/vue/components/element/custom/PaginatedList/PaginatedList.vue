<template>
    <div v-loading="isLoading">
        <el-pagination
            class="mb--2"
            v-if="isTopPaginate"
            v-show="isPaginated"
            background
            :layout="layout"
            @current-change="changePage"
            :total="paginateInfo.total"
            :current-page.sync="page"
            :page-size="paginateInfo.per_page">
        </el-pagination>
        <slot name="default" :itemList="itemList"></slot>
        <el-pagination
            class="mt--2"
            v-if="isBottomPaginate"
            v-show="isPaginated"
            background
            :layout="layout"
            @current-change="changePage"
            :total="paginateInfo.total"
            :current-page.sync="page"
            :page-size="paginateInfo.per_page">
        </el-pagination>
    </div>
</template>

<script>
  export default {
    name: 'OmxElementPaginatedList',

    data() {
      return {
        isLoading: false,
        page: 1,
      };
    },

    props: {
      paginatedList: Object,
      layout: { type: String, default: 'prev, pager, next' },
      isTopPaginate: { type: Boolean, default: false },
      isBottomPaginate: { type: Boolean, default: true },
    },

    computed: {
      paginateInfo() {
        return this.paginatedList.meta;
      },

      itemList() {
        return this.paginatedList[this.page];
      },

      isPaginated() {
        return this.paginateInfo.total > this.paginateInfo.per_page;
      },
    },

    methods: {
      changePage(page) {
        this.$emit('page', page);
      },
    },
  };
</script>

<style lang="scss">
    .el-pagination {
        padding-left: 0;
        .btn-prev {
            margin-left: 0 !important;
        }
    }

    .el-pagination.is-background .btn-next,
    .el-pagination.is-background .btn-prev,
    .el-pagination.is-background .el-pager li {
        background-color: white;
    }
</style>