<template>
  <div>
    <h2>{{ $t('proxy.title') }}</h2>
  </div>
</template>

<script>
export default {
  name: "Proxy",
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
