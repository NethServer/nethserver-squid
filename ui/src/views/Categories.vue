<template>
  <div>
    <h2>{{ $t('categories.title') }}</h2>
  </div>
</template>

<script>
export default {
  name: "Categories",
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  data() {
    return {};
  },
  methods: {}
};
</script>

<style>
</style>
