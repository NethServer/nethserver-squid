<template>
  <div>
    <h2>{{ $t('proxy_rules.title') }}</h2>
  </div>
</template>

<script>
export default {
  name: "ProxyRules",
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
