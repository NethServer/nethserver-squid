<template>
  <div>
    <h2>{{ $t('proxy_rules.title') }}</h2>
    <doc-info
      :placement="'top'"
      :title="$t('docs.proxy_rules')"
      :chapter="'mail'"
      :section="'mailbox-configuration'"
      :inline="false"
    ></doc-info>
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
