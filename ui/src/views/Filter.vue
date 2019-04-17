<template>
  <div>
    <h2>{{ $t('filter.title') }}</h2>
    <doc-info
      :placement="'top'"
      :title="$t('docs.filter')"
      :chapter="'content_filter'"
      :section="''"
      :inline="false"
    ></doc-info>

    <div v-show="!view.isLoaded" class="spinner spinner-lg"></div>
    <div v-show="!view.menu.installed && view.isLoaded">
      <div class="blank-slate-pf" id>
        <div class="blank-slate-pf-icon">
          <span class="pficon pficon pficon-add-circle-o"></span>
        </div>
        <h1>{{$t('package_required')}}</h1>
        <p>{{$t('package_required_desc')}}.</p>
        <pre>{{view.menu.packages.join(' ')}}</pre>
        <div class="blank-slate-pf-main-action">
          <button
            :disabled="view.isInstalling"
            @click="installPackages()"
            class="btn btn-primary btn-lg"
          >{{view.menu.packages.length == 1 ? $t('install_package') : $t('install_packages')}}</button>
          <div v-if="view.isInstalling" class="spinner spinner-sm"></div>
        </div>
      </div>
    </div>

    <div v-show="view.menu.installed && view.isLoaded"></div>
  </div>
</template>

<script>
export default {
  name: "Filter",
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-squid/feature/read"],
        {
          name: vm.$route.path.substr(1)
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }

          vm.view.menu = success;
        },
        function(error) {
          console.error(error);
        },
        false
      );
    });
  },
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  data() {
    return {
      view: {
        isLoaded: false,
        isInstalling: false,
        menu: {
          installed: false,
          packages: []
        }
      }
    };
  },
  methods: {
    installPackages() {
      this.view.isInstalling = true;
      // notification
      nethserver.notifications.success = this.$i18n.t("packages_installed_ok");
      nethserver.notifications.error = this.$i18n.t("packages_installed_error");

      nethserver.exec(
        ["nethserver-squid/feature/update"],
        {
          name: this.$route.path.substr(1)
        },
        function(stream) {
          console.info("install-package", stream);
        },
        function(success) {
          // reload page
          window.location.reload();
        },
        function(error) {
          console.error(error);
        }
      );
    }
  }
};
</script>

<style>
</style>
