<template>
  <div>
    <h2>{{ $t('categories.title') }}</h2>
    <doc-info
      :placement="'top'"
      :title="$t('docs.categories')"
      :chapter="'content_filter'"
      :section="'filters'"
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
      <h3>{{ $t('categories.configuration') }}</h3>
      <div class="panel panel-default">
        <div class="panel-heading">
          <button
            @click="openConfiguration()"
            class="btn btn-primary right proxy-details"
          >{{$t('configure')}}</button>

          <span class="panel-title">
            <span class="semi-bold">{{$t('categories.blacklist')}}</span>
            : {{$t('categories.'+configuration.Lists)}}
            <span
              v-if="configuration.Lists == 'custom'"
            >({{configuration.CustomListURL}})</span>
            <a
              v-if="listCategories.length > 0"
              data-toggle="modal"
              data-target="#listCategoriesModal"
              class="handle-overflow span-left-margin semi-bold color-link-hover"
            >{{$t('categories.list_categories')}}</a>
          </span>
        </div>
      </div>

      <div v-if="categories.length == 0" class="blank-slate-pf" id>
        <div class="blank-slate-pf-icon">
          <span class="fa fa-ban"></span>
        </div>
        <h1>{{$t('categories.no_categories_find')}}</h1>
        <p>{{$t('categories.no_categories_find_desc')}}.</p>
        <div class="blank-slate-pf-main-action">
          <button
            @click="openCreateCategory()"
            class="btn btn-primary btn-lg"
          >{{$t('categories.add_category')}}</button>
        </div>
      </div>

      <h3 v-if="categories.length > 0">{{ $t('actions') }}</h3>
      <button
        v-if="categories.length > 0"
        @click="openCreateCategory()"
        class="btn btn-primary btn-lg"
      >{{$t('categories.add_category')}}</button>

      <h3 v-if="categories.length > 0">{{ $t('categories.custom_categories') }}</h3>
      <ul
        v-if="categories.length > 0"
        class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
      >
        <li class="list-group-item" v-for="(c,ck) in categories" v-bind:key="ck">
          <div class="list-view-pf-actions">
            <button @click="openEditCategory(c)" :class="['btn btn-default']">
              <span :class="['fa', 'fa-pencil', 'span-right-margin']"></span>
              {{$t('edit')}}
            </button>
            <div class="dropup pull-right dropdown-kebab-pf">
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
                  <a @click="openDeleteCategory(c)">
                    <span class="fa fa-times span-right-margin"></span>
                    {{$t('delete')}}
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <div class="list-view-pf-main-info small-list">
            <div class="list-view-pf-left">
              <span class="list-view-pf-icon-sm fa fa-ban"></span>
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description rules-src-dst">
                <div class="list-group-item-heading">
                  <span class="handle-overflow">
                    <span class="mg-left-5">{{c.name}}</span>
                  </span>
                </div>
                <div class="list-group-item-text">
                  <span class="mg-right-10 big-icon"></span>
                  <span class="handle-overflow">
                    <span class="fa fa-tags"></span>
                    <span :class="['mg-left-5']">{{c.domains && c.domains.join(', ')}}</span>
                  </span>
                </div>
              </div>
              <div class="list-view-pf-additional-info rules-info">
                <div class="list-view-pf-additional-info-item">{{c.description}}</div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- MODALS -->
    <div class="modal" id="configurationModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('categories.configuration')}}</h4>
          </div>
          <form
            class="form-horizontal"
            v-on:submit.prevent="saveConfiguration(currentConfiguration)"
          >
            <div class="modal-body">
              <div
                v-if="currentConfiguration.profiles.length > 0"
                class="alert alert-warning alert-dismissable"
              >
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('warning')}}.</strong>
                {{$t('categories.change_list_warning')}}:
                <br>
                <code>{{currentConfiguration.profiles.join(' | ')}}</code>
              </div>
              <div class="form-group">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('categories.available_lists')}}</label>
                <div class="col-sm-9">
                  <select type="text" v-model="currentConfiguration.Lists" class="form-control">
                    <option
                      v-for="(l,lk) in lists"
                      v-bind:key="lk"
                      :value="l"
                    >{{$t('categories.'+l)}}</option>
                  </select>
                </div>
              </div>
              <div
                v-if="currentConfiguration.Lists == 'custom'"
                :class="['form-group', currentConfiguration.errors.CustomListURL.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('categories.custom_url')}}</label>
                <div class="col-sm-9">
                  <input
                    type="url"
                    v-model="currentConfiguration.CustomListURL"
                    class="form-control"
                  >
                  <span
                    v-if="currentConfiguration.errors.CustomListURL.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentConfiguration.errors.CustomListURL.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div
                v-if="currentConfiguration.isLoading"
                class="spinner spinner-sm form-spinner-loader"
              ></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('categories.save_download')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="createCategoryModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{currentCategory.isEdit ? $t('categories.edit_category') + ' '+ currentCategory.name : $t('categories.add_category')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveCategory(currentCategory)">
            <div class="modal-body">
              <div :class="['form-group', currentCategory.errors.name.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('categories.name')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="currentCategory.name" class="form-control">
                  <span
                    v-if="currentCategory.errors.name.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCategory.errors.name.message)}}</span>
                </div>
              </div>
              <div
                :class="['form-group', currentCategory.errors.description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('categories.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="currentCategory.description" class="form-control">
                  <span
                    v-if="currentCategory.errors.description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCategory.errors.description.message)}}</span>
                </div>
              </div>
              <div
                :class="['form-group', currentCategory.errors.domains.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('categories.domains')}}</label>
                <div class="col-sm-9">
                  <textarea
                    type="text"
                    v-model="currentCategory.domains"
                    class="form-control min-textarea-height"
                  ></textarea>
                  <span
                    v-if="currentCategory.errors.domains.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentCategory.errors.domains.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentCategory.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{currentCategory.isEdit ? $t('edit') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteCategoryModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('categories.delete_category')}} {{toDeleteCategory.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteCategory(toDeleteCategory)">
            <div class="modal-body">
              <div
                v-if="toDeleteCategory.profiles.length > 0"
                class="alert alert-warning alert-dismissable"
              >
                <span class="pficon pficon-warning-triangle-o"></span>
                <strong>{{$t('warning')}}.</strong>
                {{$t('categories.remove_category_warning')}}:
                <br>
                <code>{{toDeleteCategory.profiles.join(' | ')}}</code>
              </div>
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
    <div class="modal" id="listCategoriesModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('categories.title')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="undefined">
            <div class="modal-body">
              <div class="form-group">
                <label class="col-sm-3 control-label" for="textInput-modal-markup">{{$t('list')}}</label>
                <div class="col-sm-9">
                  <pre type="text" class="form-control min-list-cat">{{listCategories.join('\n')}}</pre>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('close')}}</button>
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
  name: "Categories",
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
          vm.getConfiguration();
          vm.getCategories();
          vm.getListCategories();
          vm.getLists();
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
      configuration: {
        Lists: "",
        CustomListURL: ""
      },
      categories: [],
      listCategories: [],
      lists: [],
      currentConfiguration: {
        Lists: "",
        CustomListURL: "",
        isLoading: false,
        errors: {
          CustomListURL: {
            hasError: false,
            message: ""
          }
        },
        profiles: []
      },
      currentCategory: this.initCategory(),
      toDeleteCategory: {
        profiles: []
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
          window.parent.location.reload();
        },
        function(error) {
          console.error(error);
        }
      );
    },
    initCategory() {
      return {
        description: "",
        name: "",
        domains: [],
        errors: this.initErrors(),
        isEdit: false,
        isLoading: false
      };
    },
    initErrors() {
      return {
        description: {
          hasError: false,
          message: ""
        },
        name: {
          hasError: false,
          message: ""
        },
        domains: {
          hasError: false,
          message: ""
        }
      };
    },
    initConfigurationErrors() {
      return {
        CustomListURL: {
          hasError: false,
          message: ""
        }
      };
    },
    getConfiguration() {
      var context = this;

      nethserver.exec(
        ["nethserver-squid/categories/read"],
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
          context.configuration.profiles = success.profiles;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getCategories() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-squid/categories/read"],
        {
          action: "categories",
          filter: "custom"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.categories = success.categories;
          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getListCategories() {
      var context = this;

      nethserver.exec(
        ["nethserver-squid/categories/read"],
        {
          action: "categories",
          filter: "downloaded"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.listCategories = success.categories.map(function(c) {
            return c.name;
          });
          setTimeout(function() {
            $('[data-toggle="tooltip-second"]').tooltip();
          }, 500);
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getLists() {
      var context = this;

      nethserver.exec(
        ["nethserver-squid/categories/read"],
        {
          action: "lists"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.lists = success.lists;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    openConfiguration() {
      this.currentConfiguration = JSON.parse(
        JSON.stringify(this.configuration)
      );
      this.currentConfiguration.isLoading = false;
      this.currentConfiguration.errors = this.initConfigurationErrors();

      $("#configurationModal").modal("show");
    },
    openCreateCategory() {
      this.currentCategory = this.initCategory();
      $("#createCategoryModal").modal("show");
    },
    openEditCategory(category) {
      this.currentCategory = JSON.parse(JSON.stringify(category));
      this.currentCategory.errors = this.initErrors();
      this.currentCategory.isEdit = true;
      this.currentCategory.domains = this.currentCategory.domains.join("\n");
      $("#createCategoryModal").modal("show");
    },
    openDeleteCategory(category) {
      this.toDeleteCategory = JSON.parse(JSON.stringify(category));
      $("#deleteCategoryModal").modal("show");
    },
    saveConfiguration() {
      var context = this;

      var configObj = {
        action: "configuration",
        Lists: context.currentConfiguration.Lists,
        CustomListURL: context.currentConfiguration.CustomListURL
      };

      context.currentConfiguration.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-squid/categories/validate"],
        configObj,
        null,
        function(success) {
          context.currentConfiguration.isLoading = false;
          $("#configurationModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "categories.configuration_updated_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "categories.configuration_updated_error"
          );

          // update values
          nethserver.exec(
            ["nethserver-squid/categories/update"],
            configObj,
            function(stream) {
              console.info("configuration", stream);
            },
            function(success) {
              // get configuration
              context.getConfiguration();

              // get categories list
              context.getListCategories();
            },
            function(error, data) {
              console.error(error, data);
            }
          );
        },
        function(error, data) {
          var errorData = {};
          context.currentConfiguration.isLoading = false;

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.currentConfiguration.errors[
                attr.parameter
              ].hasError = true;
              context.currentConfiguration.errors[attr.parameter].message =
                attr.error;
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    saveCategory(category) {
      var context = this;

      var categoryObj = {
        action: category.isEdit ? "update-category" : "create-category",
        Description: category.description,
        name: category.name,
        Domains: category.domains.length > 0 ? category.domains.split("\n") : []
      };

      context.currentCategory.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-squid/categories/validate"],
        categoryObj,
        null,
        function(success) {
          context.currentCategory.isLoading = false;
          $("#createCategoryModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "categories.category_" +
              (context.currentCategory.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "categories.category_" +
              (context.currentCategory.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (category.isEdit) {
            nethserver.exec(
              ["nethserver-squid/categories/update"],
              categoryObj,
              function(stream) {
                console.info("category", stream);
              },
              function(success) {
                // get all
                context.getCategories();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-squid/categories/create"],
              categoryObj,
              function(stream) {
                console.info("category", stream);
              },
              function(success) {
                // get all
                context.getCategories();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.currentCategory.isLoading = false;
          context.currentCategory.errors = context.initErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.currentCategory.errors[attr.parameter].hasError = true;
              context.currentCategory.errors[attr.parameter].message =
                attr.error;
              context.$forceUpdate();
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    deleteCategory(category) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "categories.category_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "categories.category_deleted_error"
      );

      $("#deleteCategoryModal").modal("hide");
      nethserver.exec(
        ["nethserver-squid/categories/delete"],
        {
          name: category.name
        },
        function(stream) {
          console.info("category", stream);
        },
        function(success) {
          // get all
          context.getCategories();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    }
  }
};
</script>

<style scoped>
.color-link-hover {
  color: #318fd1;
  cursor: pointer;
}

.min-list-cat {
  min-height: 350px;
}
</style>
