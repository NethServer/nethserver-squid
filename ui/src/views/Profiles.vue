<template>
  <div>
    <h2>{{ $t('profiles.title') }}</h2>
  </div>
</template>

<script>
export default {
  name: "Profiles",
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
