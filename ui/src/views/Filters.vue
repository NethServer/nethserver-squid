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

    <div v-show="view.menu.installed && view.isLoaded">
      <h3>{{ $t('filter.configuration') }}</h3>
      <div class="panel panel-default">
        <div class="panel-heading">
          <button
            v-if="configuration.Filter"
            :disabled="!proxyConfiguration.status"
            @click="toggleStatus(true)"
            class="btn btn-primary right proxy-details"
          >{{$t('edit')}}</button>
          <toggle-button
            v-if="proxyConfiguration.status"
            class="min-toggle right"
            :width="40"
            :height="20"
            :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
            :value="configuration.Filter"
            :sync="true"
            @change="toggleStatus(false)"
          />
          <a
            v-if="!proxyConfiguration.status"
            href="#/proxy"
            class="right external-smarthost span-left-margin"
          >
            {{$t('filter.enable_proxy_before')}}
            <span class="fa fa-external-link"></span>
          </a>
          <span
            v-if="!proxyConfiguration.status"
            class="pficon pficon-warning-triangle-o right adjust-triangle"
          ></span>

          <span class="panel-title">
            {{$t('filter.enabled')}}
            <span
              :class="['fa', configuration.Filter ? 'fa-check green' : 'fa-times red']"
            ></span>
          </span>
        </div>
      </div>

      <h3>{{ $t('filter.antivirus') }}</h3>
      <div class="panel panel-default">
        <div class="panel-heading">
          <toggle-button
            v-if="proxyConfiguration.status"
            class="min-toggle right"
            :width="40"
            :height="20"
            :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
            :value="configuration.AntiVirus"
            :sync="true"
            @change="toggleAntivirus()"
          />
          <span class="panel-title">
            {{$t('filter.enabled')}}
            <span
              :class="['fa', configuration.AntiVirus ? 'fa-check green' : 'fa-times red']"
            ></span>
          </span>
        </div>
      </div>

      <div v-if="profiles.length == 0" class="blank-slate-pf" id>
        <div class="blank-slate-pf-icon">
          <span class="pficon pficon-users"></span>
        </div>
        <h1>{{$t('filter.no_profiles_find')}}</h1>
        <p>{{$t('filter.no_profiles_find_desc')}}.</p>
        <div class="blank-slate-pf-main-action">
          <button
            @click="openCreateProfile()"
            class="btn btn-primary btn-lg"
          >{{$t('filter.add_profile')}}</button>
        </div>
      </div>

      <h3 v-if="profiles.length > 0">{{ $t('actions') }}</h3>
      <a v-if="categories.length > 0" class="right adjust-clear" @click="openConfigureDefault()">
        <span class="fa fa-user"></span>
        {{$t('filter.configure_default')}}
      </a>
      <button
        v-if="profiles.length > 0"
        @click="openCreateProfile()"
        class="btn btn-primary btn-lg"
      >{{$t('filter.add_profile')}}</button>

      <h3 v-if="profiles.length > 0">{{ $t('filter.profiles') }}</h3>
      <ul
        v-if="profiles.length > 0"
        class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
      >
        <li
          :class="['list-group-item', p.broken == 1 ? 'gray' : '']"
          v-for="(p,pk) in profiles"
          v-bind:key="pk"
          v-show="p.Removable == 'yes'"
        >
          <div class="list-view-pf-actions">
            <button
              :disabled="p.broken == 1"
              @click="openEditProfile(p)"
              :class="['btn btn-default']"
            >
              <span :class="['fa', 'fa-pencil', 'span-right-margin']"></span>
              {{$t('edit')}}
            </button>
            <div class="dropdown pull-right dropdown-kebab-pf">
              <button
                class="btn btn-link dropdown-toggle"
                type="button"
                id="dropdownKebabRight9"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="true"
              >
                <span class="fa fa-ellipsis-v"></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-right">
                <li>
                  <a @click="openDeleteProfile(p)">
                    <span class="fa fa-times span-right-margin"></span>
                    {{$t('delete')}}
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <div class="list-view-pf-main-info small-list">
            <div class="list-view-pf-left">
              <span
                :class="['list-view-pf-icon-sm pficon pficon-user', p.broken == 1 ? 'border-broken' : '']"
              ></span>
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description rules-src-dst">
                <div class="list-group-item-heading">
                  <span
                    data-toggle="tooltip"
                    data-placement="top"
                    data-html="true"
                    :title="mapTitleName(p)"
                    class="handle-overflow"
                  >
                    <span class="mg-left-5">{{p.name}}</span>
                  </span>
                </div>
                <div class="list-group-item-text">
                  <span class="mg-right-10 big-icon"></span>
                  <span
                    data-toggle="tooltip"
                    data-placement="top"
                    data-html="true"
                    :title="mapTitleSrc(p)"
                    class="handle-overflow"
                  >
                    <span :class="[mapObjectIcon(p.Src)]"></span>
                    <span :class="['mg-left-5']">{{p.Src.name}}</span>
                  </span>
                </div>
              </div>
              <div class="list-view-pf-additional-info">
                <span
                  data-toggle="tooltip"
                  data-placement="top"
                  data-html="true"
                  :title="mapTitleFilter(p)"
                  class="list-view-pf-additional-info-item col-sm-5"
                >
                  <span class="fa fa-filter"></span>
                  {{p.Filter.name}}
                </span>

                <span class="list-view-pf-additional-info-item col-sm-5">
                  <span class="fa fa-clock-o"></span>
                  <span
                    v-for="(t,tk) in p.Time"
                    v-bind:key="tk"
                    data-toggle="tooltip"
                    data-placement="top"
                    data-html="true"
                    :title="mapTitleTime(t)"
                    class="span-left-margin"
                  >{{t.name}}</span>
                  <span v-if="p.Time.length == 0">{{$t('always')}}</span>
                </span>
                <!-- <span class="list-view-pf-additional-info-item col-sm-12">{{p.Description}}</span> -->
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <!-- MODALS -->
    <div class="modal" id="configureFilterModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('filter.configure_filter')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveConfiguration()">
            <div class="modal-body">
              <div
                :class="['form-group', newConfiguration.errors.BlockedFileTypes.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('filter.blocked_file_types')}}</label>
                <div class="col-sm-9">
                  <input class="form-control" v-model="newConfiguration.BlockedFileTypes">
                  <span
                    v-if="newConfiguration.errors.BlockedFileTypes.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newConfiguration.errors.BlockedFileTypes.message)}}</span>
                </div>
              </div>
              <div
                :class="['form-group', newConfiguration.errors.DomainBlacklist.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('filter.domain_blacklist')}}</label>
                <div class="col-sm-9">
                  <textarea
                    class="form-control min-textarea-height"
                    v-model="newConfiguration.DomainBlacklist"
                  ></textarea>
                  <span
                    v-if="newConfiguration.errors.DomainBlacklist.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newConfiguration.errors.DomainBlacklist.message)}}</span>
                </div>
              </div>
              <div
                :class="['form-group', newConfiguration.errors.DomainWhitelist.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('filter.domain_whitelist')}}</label>
                <div class="col-sm-9">
                  <textarea
                    class="form-control min-textarea-height"
                    v-model="newConfiguration.DomainWhitelist"
                  ></textarea>
                  <span
                    v-if="newConfiguration.errors.DomainWhitelist.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newConfiguration.errors.DomainWhitelist.message)}}</span>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">{{$t('filter.enable_expression_url')}}</label>
                <div class="col-sm-9">
                  <toggle-button
                    class="min-toggle"
                    :width="40"
                    :height="20"
                    :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                    :value="newConfiguration.Expressions"
                    :sync="true"
                    @change="newConfiguration.Expressions = !newConfiguration.Expressions"
                  />
                </div>
              </div>
              <div
                v-show="newConfiguration.Expressions"
                :class="['form-group', newConfiguration.errors.UrlBlacklist.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('filter.url_blacklist')}}</label>
                <div class="col-sm-9">
                  <textarea
                    class="form-control min-textarea-height"
                    v-model="newConfiguration.UrlBlacklist"
                  ></textarea>
                  <span
                    v-if="newConfiguration.errors.UrlBlacklist.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newConfiguration.errors.UrlBlacklist.message)}}</span>
                </div>
              </div>
              <div
                v-show="newConfiguration.Expressions"
                :class="['form-group', newConfiguration.errors.UrlWhitelist.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('filter.url_whitelist')}}</label>
                <div class="col-sm-9">
                  <textarea
                    class="form-control min-textarea-height"
                    v-model="newConfiguration.UrlWhitelist"
                  ></textarea>
                  <span
                    v-if="newConfiguration.errors.UrlWhitelist.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newConfiguration.errors.UrlWhitelist.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer no-mg-top">
              <div v-if="newConfiguration.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button
                @click="resetConfiguration(newConfiguration.isEdit)"
                class="btn btn-default"
                type="button"
                data-dismiss="modal"
              >{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div
      class="modal"
      id="configureDefaultModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('filter.configure_default')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveConfiguration()">
            <div class="modal-body">
              <div class="form-group">
                <label class="col-sm-3 control-label">{{$t('filter.enable_global_blacklist')}}</label>
                <div class="col-sm-9">
                  <toggle-button
                    class="min-toggle"
                    :width="40"
                    :height="20"
                    :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                    :value="defaultProfile.BlackList"
                    :sync="true"
                    @change="defaultProfile.BlackList = !defaultProfile.BlackList"
                  />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">{{$t('filter.enable_global_whitelist')}}</label>
                <div class="col-sm-9">
                  <toggle-button
                    class="min-toggle"
                    :width="40"
                    :height="20"
                    :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                    :value="defaultProfile.WhiteList"
                    :sync="true"
                    @change="defaultProfile.WhiteList = !defaultProfile.WhiteList"
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label">{{$t('filter.mode')}}</label>
                <div class="col-sm-9">
                  <select class="form-control" v-model="defaultProfile.BlockAll">
                    <option value="enabled">{{$t('filter.block_all')}}</option>
                    <option value="disabled">{{$t('filter.allow_all')}}</option>
                  </select>
                </div>
              </div>

              <div
                :class="['form-group', newConfiguration.errors.DefaultFilter.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('filter.categories')}}</label>
                <div class="col-sm-7">
                  <v-select
                    @input="addCategoryDefault"
                    v-model="defaultProfile.categoryToAdd"
                    :options="categories"
                    label="name"
                    :clearable="false"
                    :placeholder="$t('filter.select_categories')"
                    :components="{OpenIndicator}"
                  >
                    <template slot="option" slot-scope="option">
                      {{ option.name }}
                      <span
                        class="gray"
                        v-if="option.type == 'custom'"
                      >({{$t('categories.custom')}})</span>
                    </template>
                    <div slot="no-options">{{$t('filter.category_not_found')}}</div>
                  </v-select>
                  <span
                    v-if="newConfiguration.errors.DefaultFilter.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newConfiguration.errors.DefaultFilter.message)}}</span>
                </div>
                <div class="col-sm-2">
                  <a
                    class="adjust-clear"
                    @click="defaultProfile.Categories = []"
                  >{{$t('clear_all')}}</a>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label" for="textInput-modal-markup"></label>
                <div class="col-sm-9">
                  <ul class="list-inline compact">
                    <li
                      v-for="(c,i) in defaultProfile.Categories"
                      v-bind:key="i"
                      class="adjust-space"
                    >
                      <span class="label label-info">
                        {{c}}
                        <a @click="removeCategoryDefault(i)" class="remove-item-inline">
                          <span class="fa fa-times"></span>
                        </a>
                      </span>
                    </li>
                  </ul>
                </div>
              </div>

              <legend class="fields-section-header-pf" aria-expanded="true">
                <span
                  :class="['fa fa-angle-right field-section-toggle-pf', defaultProfile.advanced ? 'fa-angle-down' : '']"
                ></span>
                <a
                  class="field-section-toggle-pf"
                  @click="toggleAdvancedMode()"
                >{{$t('advanced_mode')}}</a>
              </legend>

              <div v-show="defaultProfile.advanced" class="form-group">
                <label class="col-sm-3 control-label">{{$t('filter.block_access_ip_address')}}</label>
                <div class="col-sm-9">
                  <toggle-button
                    class="min-toggle"
                    :width="40"
                    :height="20"
                    :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                    :value="defaultProfile.BlockIpAccess"
                    :sync="true"
                    @change="defaultProfile.BlockIpAccess = !defaultProfile.BlockIpAccess"
                  />
                </div>
              </div>

              <div v-show="defaultProfile.advanced" class="form-group">
                <label class="col-sm-3 control-label">{{$t('filter.block_file_extensions')}}</label>
                <div class="col-sm-9">
                  <toggle-button
                    class="min-toggle"
                    :width="40"
                    :height="20"
                    :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                    :value="defaultProfile.BlockFileTypes"
                    :sync="true"
                    @change="defaultProfile.BlockFileTypes = !defaultProfile.BlockFileTypes"
                  />
                </div>
              </div>
            </div>
            <div class="modal-footer no-mg-top">
              <div v-if="defaultProfile.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteProfileModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('filter.delete_profile')}} {{toDeleteProfile.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteProfile(toDeleteProfile)">
            <div class="modal-body">
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('are_you_sure')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-danger" type="submit">{{$t('delete')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- END MODALS -->
  </div>
</template>

<script>
export default {
  name: "Filters",
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
          vm.getCategories();
          vm.getConfiguration();
          vm.getProxyConfiguration();
          vm.getProfiles();
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
  mounted() {},
  data() {
    return {
      view: {
        isLoaded: false,
        isInstalling: false,
        menu: {
          installed: false,
          packages: []
        }
      },
      profiles: [],
      currentProfile: this.initProfile(),
      toDeleteProfile: {},
      configuration: {
        AntiVirus: false,
        DomainWhitelist: [],
        DomainBlacklist: [],
        UrlBlacklist: [],
        BlockedFileTypes: [],
        Filter: true,
        Expressions: false,
        UrlWhitelist: []
      },
      newConfiguration: {
        AntiVirus: false,
        DomainWhitelist: [],
        DomainBlacklist: [],
        UrlBlacklist: [],
        BlockedFileTypes: [],
        Filter: true,
        Expressions: false,
        UrlWhitelist: [],
        errors: this.initConfigurationErrors()
      },
      proxyConfiguration: {},
      defaultProfile: {},
      categories: [],
      OpenIndicator: {
        render: createElement =>
          createElement("span", { class: { toggle: true } })
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
    },
    toggleAdvancedMode() {
      this.defaultProfile.advanced = !this.defaultProfile.advanced;
      this.$forceUpdate();
    },
    mapObjectIcon(obj, status) {
      switch (obj.type) {
        case "host":
          return "fa fa-desktop";
          break;
        case "host-group":
          return "fa fa-cubes";
          break;
        case "iprange":
          return "pficon pficon-network";
          break;
        case "cidr":
          return "pficon pficon-network";
          break;
        case "zone":
          return "pficon pficon-zone";
          break;
        case "role":
          return (
            "square-" + (status == "disabled" ? "GRAY" : obj.name.toUpperCase())
          );
          break;
        case "user":
          return "fa fa-user";
          break;
        case "any":
          return "fa fa-globe";
          break;
      }
    },
    mapTitleName(profile) {
      var html = "<b>" + profile.name + "</b>";
      html += "<br><br>";

      if (profile.broken == 1) {
        html += this.$i18n.t("filter.profile_broken");
      } else {
        html += profile.Description;
      }

      return html;
    },
    mapTitleSrc(profile) {
      var html =
        this.$i18n.t("filter.who") +
        ": <b>" +
        (profile.Src.type == "role" || profile.Src.type == "any"
          ? profile.Src.name.toUpperCase()
          : profile.Src.name) +
        "</b>";

      // host zone
      if (profile.Src.zone && profile.Src.zone.length > 0) {
        html +=
          ' | <span class="' +
          this.mapZoneIcon(profile.Src.zone) +
          ' mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          profile.Src.zone.toUpperCase() +
          "</span>";
      }

      html += "<br>";

      // host
      if (profile.Src.IpAddress) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          profile.Src.IpAddress +
          "</span></div>";
      }

      // cidr
      if (profile.Src.Address) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          profile.Src.Address +
          "</span></div>";
      }

      // ip range
      if (profile.Src.Start || profile.Src.End) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          profile.Src.Start +
          " - " +
          profile.Src.End +
          "</span></div>";
      }

      // zone
      if (profile.Src.Network || profile.Src.Interface) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          profile.Src.Network +
          "</span></div>";

        html +=
          '<div><span class="pficon pficon-plugged mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          profile.Src.Interface +
          "</span></div>";
      }

      // role
      if (profile.Src.type == "role") {
        if (profile.Src.Interfaces && profile.Src.Interfaces.length > 0) {
          html +=
            '<div><span class="pficon pficon-plugged mg-right-5 mg-top-5 detail-icon"></span>' +
            "<span>" +
            profile.Src.Interfaces.join(", ") +
            "</span></div>";
        }
      }

      html +=
        '<span class="type-info"><b>' +
        this.$i18n.t("proxy_rules." + profile.Src.type) +
        "</b></span>";

      return html;
    },
    mapTitleFilter(profile) {
      var context = this;

      var html =
        context.$i18n.t("filter.what") +
        ": <b>" +
        (profile.Filter.type == "role" || profile.Filter.type == "any"
          ? profile.Filter.name.toUpperCase()
          : profile.Filter.name) +
        "</b>";

      html += "<br>";
      html += "<br>";

      html +=
        '<div><span>BlockIpAccess</span>:<span class="span-left-margin fa fa-' +
        (profile.Filter.BlockIpAccess == "enabled"
          ? "check green"
          : "times red") +
        '"></span></div>';
      html +=
        '<div><span>BlockFileTypes</span>:<span class="span-left-margin fa fa-' +
        (profile.Filter.BlockFileTypes == "enabled"
          ? "check green"
          : "times red") +
        '"></span></div>';
      html +=
        '<div><span>BlockBuiltinRules</span>:<span class="span-left-margin fa fa-' +
        (profile.Filter.BlockBuiltinRules == "enabled"
          ? "check green"
          : "times red") +
        '"></span></div>';
      html +=
        '<div><span>BlockAll</span>:<span class="span-left-margin fa fa-' +
        (profile.Filter.BlockAll == "enabled" ? "check green" : "times red") +
        '"></span></div>';
      html +=
        '<div><span>BlackList</span>:<span class="span-left-margin fa fa-' +
        (profile.Filter.BlackList == "enabled" ? "check green" : "times red") +
        '"></span></div>';
      html +=
        '<div><span>WhiteList</span>:<span class="span-left-margin fa fa-' +
        (profile.Filter.WhiteList == "enabled" ? "check green" : "times red") +
        '"></span></div>';

      if (profile.Filter.Categories.length > 0) {
        html +=
          '<br><div><span class="fa fa-cubes mg-right-5 mg-top-5 detail-icon"></span>' +
            context.$i18n.t("categories.title") +
            ": <br>" +
            "<span>" +
            profile.Filter.Categories.join(", ") || +"</span></div>";
        html += "<br>";
      }

      html +=
        '<span class="type-info"><b>' +
        context.$i18n.t("proxy_rules." + profile.Filter.type) +
        "</b></span>";

      return html;
    },
    mapTitleTime(time) {
      var context = this;

      var html = "";
      if (time.Days) {
        html +=
          context.$i18n.t("filter.when") +
          ": <b>" +
          (time.type == "role" || time.type == "any"
            ? time.name.toUpperCase()
            : time.name) +
          "</b>";

        html += "<br>";

        html +=
          '<br><div><span class="fa fa-clock-o mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          time.StartTime +
          " - " +
          time.EndTime +
          "</span></div>";

        html +=
          '<br><div><span class="fa fa-calendar mg-right-5 mg-top-5 detail-icon"></span>' +
            this.$i18n.t("weekdays") +
            ": <br>" +
            "<span>" +
            time.Days.map(function(t) {
              return context.$i18n.t("filter." + t);
            }).join("<br>") || +"</span></div>";

        html += "<br>";

        html +=
          '<span class="type-info"><b>' +
          context.$i18n.t("proxy_rules." + time.type) +
          "</b></span>";
      } else {
        html +=
          context.$i18n.t("filter.when") +
          ": <b>" +
          context.$i18n.t("always") +
          "</b>";
      }

      return html;
    },
    getCategories() {
      var context = this;

      nethserver.exec(
        ["nethserver-squid/categories/read"],
        {
          action: "categories"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.categories = success.categories;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getConfiguration() {
      var context = this;

      nethserver.exec(
        ["nethserver-squid/filter/read"],
        {
          action: "configuration"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.configuration = success.configuration;
          context.configuration.Filter =
            context.configuration.Filter == "enabled";
          context.configuration.AntiVirus =
            context.configuration.AntiVirus == "enabled";
          context.configuration.Expressions =
            context.configuration.Expressions == "enabled";

          context.defaultProfile = success.configuration.DefaultFilter;
          context.defaultProfile.BlackList =
            context.defaultProfile.BlackList == "enabled";
          context.defaultProfile.WhiteList =
            context.defaultProfile.WhiteList == "enabled";
          context.defaultProfile.BlockIpAccess =
            context.defaultProfile.BlockIpAccess == "enabled";
          context.defaultProfile.BlockFileTypes =
            context.defaultProfile.BlockFileTypes == "enabled";

          context.categoryToAdd = {};

          context.defaultProfile.advanced = false;
          context.defaultProfile.isLoading = false;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getProxyConfiguration() {
      var context = this;

      nethserver.exec(
        ["nethserver-squid/proxy/read"],
        {
          action: "configuration"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.proxyConfiguration = success.props;
          context.proxyConfiguration.status =
            context.proxyConfiguration.status == "enabled";

          context.$forceUpdate();
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getProfiles() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-squid/filter/read"],
        {
          action: "profiles"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.profiles = success.profiles;
          context.view.isLoaded = true;

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
          }, 500);
        },
        function(error) {
          console.error(error);
        }
      );
    },
    initProfile() {
      return {
        Profile: "",
        SrcType: "",
        Description: "",
        isLoading: false,
        isEdit: false,
        errors: this.initProfileErrors()
      };
    },
    initProfileErrors() {
      return {
        Profile: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    initConfigurationErrors() {
      return {
        DomainWhitelist: {
          hasError: false,
          message: ""
        },
        DomainBlacklist: {
          hasError: false,
          message: ""
        },
        UrlBlacklist: {
          hasError: false,
          message: ""
        },
        BlockedFileTypes: {
          hasError: false,
          message: ""
        },
        UrlWhitelist: {
          hasError: false,
          message: ""
        },
        DefaultFilter: {
          hasError: false,
          message: ""
        }
      };
    },
    toggleAntivirus() {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "filter.configuration_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "filter.configuration_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-squid/filter/update"],
        {
          AntiVirus: context.configuration.AntiVirus ? "disabled" : "enabled",
          DomainWhitelist: context.configuration.DomainWhitelist,
          DomainBlacklist: context.configuration.DomainBlacklist,
          UrlBlacklist: context.configuration.UrlBlacklist,
          BlockedFileTypes: context.configuration.BlockedFileTypes,
          Filter: context.configuration.Filter ? "enabled" : "disabled",
          Expressions: context.configuration.Expressions
            ? "enabled"
            : "disabled",
          UrlWhitelist: context.configuration.UrlWhitelist,
          DefaultFilter: context.configuration.DefaultFilter,
          action: "configuration"
        },
        function(stream) {
          console.info("update-config", stream);
        },
        function(success) {
          // get all
          context.getConfiguration();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    toggleStatus(isEdit) {
      var context = this;
      if (!isEdit) {
        context.configuration.Filter = !context.configuration.Filter;
      }
      context.configuration.isEdit = isEdit;

      if (context.configuration.Filter == true) {
        $("#configureFilterModal").modal("show");
        context.newConfiguration = JSON.parse(
          JSON.stringify(context.configuration)
        );
        context.newConfiguration.DomainWhitelist = context.newConfiguration.DomainWhitelist.join(
          "\n"
        );
        context.newConfiguration.DomainBlacklist = context.newConfiguration.DomainBlacklist.join(
          "\n"
        );
        context.newConfiguration.UrlBlacklist = context.newConfiguration.UrlBlacklist.join(
          "\n"
        );
        context.newConfiguration.UrlWhitelist = context.newConfiguration.UrlWhitelist.join(
          "\n"
        );
        context.newConfiguration.BlockedFileTypes = context.newConfiguration.BlockedFileTypes.join(
          ", "
        );
        context.newConfiguration.errors = context.initConfigurationErrors();
      } else {
        // save state
        // notification
        nethserver.notifications.success = context.$i18n.t(
          "filter.configuration_updated_ok"
        );
        nethserver.notifications.error = context.$i18n.t(
          "filter.configuration_updated_error"
        );

        // update values
        nethserver.exec(
          ["nethserver-squid/filter/update"],
          {
            AntiVirus: context.configuration.AntiVirus ? "enabled" : "disabled",
            DomainWhitelist: context.configuration.DomainWhitelist,
            DomainBlacklist: context.configuration.DomainBlacklist,
            UrlBlacklist: context.configuration.UrlBlacklist,
            BlockedFileTypes: context.configuration.BlockedFileTypes,
            DefaultFilter: context.configuration.DefaultFilter,
            Filter: "disabled",
            Expressions: context.configuration.Expressions
              ? "enabled"
              : "disabled",
            UrlWhitelist: context.configuration.UrlWhitelist,
            action: "configuration"
          },
          function(stream) {
            console.info("update-config", stream);
          },
          function(success) {
            // get all
            context.getConfiguration();
          },
          function(error, data) {
            console.error(error, data);
          }
        );
      }
    },
    resetConfiguration(isEdit) {
      if (!isEdit) {
        this.configuration.Filter = !this.configuration.Filter;
      }
    },
    saveConfiguration() {
      var context = this;

      var configObj = {
        AntiVirus: this.newConfiguration.AntiVirus ? "enabled" : "disabled",
        DomainWhitelist:
          this.newConfiguration.DomainWhitelist.length > 0
            ? this.newConfiguration.DomainWhitelist.split("\n")
            : [],
        DomainBlacklist:
          this.newConfiguration.DomainBlacklist.length > 0
            ? this.newConfiguration.DomainBlacklist.split("\n")
            : [],
        UrlBlacklist:
          this.newConfiguration.UrlBlacklist.length > 0
            ? this.newConfiguration.UrlBlacklist.split("\n")
            : [],
        BlockedFileTypes:
          this.newConfiguration.BlockedFileTypes.length > 0
            ? this.newConfiguration.BlockedFileTypes.split(",").map(function(
                u
              ) {
                return u.trim();
              })
            : [],
        Filter: this.newConfiguration.Filter ? "enabled" : "disabled",
        Expressions: this.newConfiguration.Expressions ? "enabled" : "disabled",
        UrlWhitelist:
          this.newConfiguration.UrlWhitelist.length > 0
            ? this.newConfiguration.UrlWhitelist.split("\n")
            : [],
        DefaultFilter: {
          BlockIpAccess: this.defaultProfile.BlockIpAccess
            ? "enabled"
            : "disabled",
          Categories: this.defaultProfile.Categories,
          BlockFileTypes: this.defaultProfile.BlockFileTypes
            ? "enabled"
            : "disabled",
          name: "default",
          BlackList: this.defaultProfile.BlackList ? "enabled" : "disabled",
          BlockAll: this.defaultProfile.BlockAll,
          type: "filter",
          WhiteList: this.defaultProfile.WhiteList ? "enabled" : "disabled",
          Description: "Default filter",
          Removable: "no"
        },
        action: "configuration"
      };

      context.newConfiguration.isLoading = true;
      context.defaultProfile.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-squid/filter/validate"],
        configObj,
        null,
        function(success) {
          context.newConfiguration.isLoading = false;
          context.defaultProfile.isLoading = false;
          $("#configureFilterModal").modal("hide");
          $("#configureDefaultModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "filter.configuration_updated_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "filter.configuration_updated_error"
          );

          // update values
          nethserver.exec(
            ["nethserver-squid/filter/update"],
            configObj,
            function(stream) {
              console.info("update-config", stream);
            },
            function(success) {
              // get all
              context.getConfiguration();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          var errorData = {};
          context.newConfiguration.isLoading = false;
          context.defaultProfile.isLoading = false;

          context.newConfiguration.errors = context.initConfigurationErrors();
          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newConfiguration.errors[attr.parameter].hasError = true;
              context.newConfiguration.errors[attr.parameter].message =
                attr.error;
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    openConfigureDefault() {
      $("#configureDefaultModal").modal("show");
    },
    openCreateProfile() {
      this.currentProfile = this.initProfile();
      $("#createProfileModal").modal("show");
    },

    openEditProfile(profile) {
      this.currentProfile = JSON.parse(JSON.stringify(profile));
      this.currentProfile.Profile = this.currentProfile.props.Host.name;
      this.currentProfile.Type = this.currentProfile.props.Host.type;
      this.currentProfile.SrcType = this.currentProfile.props.Host.type;
      this.currentProfile.Description = this.currentProfile.props.Description;
      this.currentProfile.errors = this.initProfileErrors();
      this.currentProfile.isEdit = true;
      $("#createProfileModal").modal("show");
    },

    openDeleteProfile(profile) {
      this.toDeleteProfile = JSON.parse(JSON.stringify(profile));
      $("#deleteProfileModal").modal("show");
    },

    saveProfile(profile) {
      var context = this;

      var profileObj = {
        action: profile.isEdit ? "update-bypass" : "create-bypass",
        Host: {
          type: profile.Type,
          name: profile.Profile
        },
        Description: profile.Description,
        name: profile.isEdit ? profile.name : null,
        type: "bypass-src"
      };

      context.currentProfile.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-squid/filter/validate"],
        profileObj,
        null,
        function(success) {
          context.currentProfile.isLoading = false;
          $("#createProfileModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "filter.profile_" +
              (context.currentProfile.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "filter.profile_" +
              (context.currentProfile.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (profile.isEdit) {
            nethserver.exec(
              ["nethserver-squid/filter/update"],
              profileObj,
              function(stream) {
                console.info("profile", stream);
              },
              function(success) {
                // get all
                context.getProfiles();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-squid/filter/create"],
              profileObj,
              function(stream) {
                console.info("profile", stream);
              },
              function(success) {
                // get all
                context.getProfiles();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.currentProfile.isLoading = false;
          context.currentProfile.errors = context.initProfileErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              if (attr.parameter == "Host") {
                attr.parameter = "Profile";
              }
              context.currentProfile.errors[attr.parameter].hasError = true;
              context.currentProfile.errors[attr.parameter].message =
                attr.error;
              context.$forceUpdate();
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },

    deleteProfile(profile) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "filter.profile_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "filter.profile_deleted_error"
      );

      $("#deleteProfileModal").modal("hide");
      nethserver.exec(
        ["nethserver-squid/filter/delete"],
        {
          name: profile.name
        },
        function(stream) {
          console.info("profile", stream);
        },
        function(success) {
          // get all
          context.getProfiles();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },

    toggleProfileStatus(profile) {
      var context = this;
      // notification
      nethserver.notifications.success = context.$i18n.t(
        "filter.profile_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "filter.profile_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-squid/filter/update"],
        {
          action:
            profile.props.status == "enabled"
              ? "disable-bypass"
              : "enable-bypass",
          name: profile.name
        },
        function(stream) {
          console.info("update-status", stream);
        },
        function(success) {
          // get all
          context.getProfiles();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    addCategoryDefault(category) {
      category = (category && category.name) || null;
      if (category && category.length > 0) {
        if (!this.categoryDefaultAlreadyAdded(category)) {
          this.defaultProfile.Categories.push(category);
        }
      }
    },
    categoryDefaultAlreadyAdded(category) {
      return this.defaultProfile.Categories.indexOf(category) > -1;
    },
    removeCategoryDefault(index) {
      this.defaultProfile.Categories.splice(index, 1);
    }
  }
};
</script>

<style scoped>
.proxy-details {
  margin-left: 10px;
}

.adjust-triangle {
  margin-top: 3px;
}

.adjust-space {
  margin: 4px;
}

.adjust-clear {
  margin-top: 4px;
}

.list-group-item-heading {
  width: calc(60% - 20px) !important;
}

.list-group-item-text {
  width: calc(40% - 20px) !important;
}

.border-broken {
  border: 2px solid #cc0000 !important;
}
</style>
