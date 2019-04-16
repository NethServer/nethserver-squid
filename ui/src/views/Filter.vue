<template>
  <div>
    <h2>{{ $t('filter.title') }}</h2>
  </div>
</template>

<script>
export default {
  name: "Filter",
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
