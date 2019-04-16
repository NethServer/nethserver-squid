<template>
  <div>
    <h2>{{ $t('settings.title') }}</h2>
  </div>
</template>

<script>
export default {
  name: "Settings",
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
