<template>
  <div>
    <h2>{{ $t('dashboard.title') }}</h2>
  </div>
</template>

<script>
export default {
  name: "Dashboard",
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
