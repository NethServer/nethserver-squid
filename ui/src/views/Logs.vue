<template>
  <div>
    <h2>{{ $t('logs.title') }}</h2>
  </div>
</template>

<script>
export default {
  name: "Logs",
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
