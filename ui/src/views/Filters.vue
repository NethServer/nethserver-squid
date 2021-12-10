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
      <div class="blank-slate-pf">
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

    <div v-show="view.menu.installed && view.isLoaded && !source.downloaded" class="blank-slate-pf">
      <div class="blank-slate-pf-icon">
        <span class="fa fa-ban"></span>
      </div>
      <h1>{{$t('filter.categories_not_found')}}</h1>
      <p>
        {{$t('filter.categories_not_found_desc')}}:
        <b>{{source.list | capitalize}}</b>.
      </p>
      <div class="blank-slate-pf-main-action">
        <button @click="downloadCategories()" class="btn btn-primary btn-lg">{{$t('download')}}</button>
      </div>
    </div>

    <div v-show="view.menu.installed && view.isLoaded && source.downloaded">
      <h3>{{ $t('filter.configuration') }}</h3>
      <div v-if="!configuration.Filter" class="blank-slate-pf">
        <h1>{{$t('filter.filter_is_disabled')}}</h1>
        <p>{{$t('filter.filter_is_disabled_desc')}}.</p>
        <div class="blank-slate-pf-main-action">
          <button
            v-if="proxyConfiguration.status"
            @click="toggleStatus(false)"
            class="btn btn-primary btn-lg"
          >{{$t('filter.configure_filter')}}</button>

          <a
            v-if="!proxyConfiguration.status"
            href="#/proxy"
            class="external-smarthost span-left-margin"
          >
            <span class="pficon pficon-warning-triangle-o adjust-triangle"></span>
            {{$t('filter.enable_proxy_before')}}
            <span class="fa fa-external-link"></span>
          </a>
        </div>
      </div>
      <div v-show="configuration.Filter" class="panel panel-default">
        <div class="panel-heading">
          <button
            v-if="configuration.Filter"
            :disabled="!proxyConfiguration.status"
            @click="toggleStatus(true)"
            class="btn btn-primary right proxy-details"
          >{{$t('edit')}}</button>
          <toggle-button
            class="min-toggle right"
            :width="40"
            :height="20"
            :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
            :value="configuration.Filter"
            :sync="true"
            @change="toggleStatus(false)"
          />

          <span class="panel-title">
            {{$t('filter.enabled')}}
            <span
              :class="['fa', configuration.Filter ? 'fa-check green' : 'fa-times red']"
            ></span>

            <span
              data-toggle="tooltip-second"
              data-placement="bottom"
              data-html="true"
              :title="configuration.DomainBlacklist.join('<br>')"
              class="handle-overflow span-left-margin semi-bold color-link-hover"
            >{{$t('filter.blacklist')}}</span>

            <span
              data-toggle="tooltip-second"
              data-placement="bottom"
              data-html="true"
              :title="configuration.DomainWhitelist.join('<br>')"
              class="handle-overflow span-left-margin semi-bold color-link-hover"
            >{{$t('filter.whitelist')}}</span>
          </span>
        </div>
      </div>

      <h3 v-if="categories.length > 0">{{ $t('filter.default_profile') }}</h3>
      <div v-if="categories.length > 0" class="panel panel-default">
        <div class="panel-heading">
          <button @click="openConfigureDefault()" class="btn btn-primary right">{{$t('configure')}}</button>
          <span class="panel-title">
            {{$t('filter.mode')}}:
            <span
              class="semi-bold"
            >{{defaultProfile.BlockAll == 'enabled' ? $t('filter.block_all') : $t('filter.allow_all')}}</span>
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
            <button @click="openEditProfile(p)" :class="['btn btn-default']">
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
                :class="['list-view-pf-icon-sm pficon', p.broken == 1 ? 'border-broken pficon-warning-triangle-o' : 'pficon-user']"
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
                  {{p.Filter.Description}}
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
                  >{{t.Description}}</span>
                  <span v-if="p.Time.length == 0">{{$t('always')}}</span>
                </span>
                <!-- <span class="list-view-pf-additional-info-item col-sm-12">{{p.Description}}</span> -->
              </div>
            </div>
          </div>
        </li>
      </ul>

      <h3>{{ $t('filter.antivirus') }}</h3>
      <div class="panel panel-default">
        <div class="panel-heading">
          <toggle-button
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
                <label class="col-sm-3 control-label">
                  {{$t('filter.blocked_file_types')}}
                  <doc-info
                    :placement="'top'"
                    :title="$t('filter.blocked_file_types')"
                    :chapter="'blocked_file_types'"
                    :inline="true"
                  ></doc-info>
                </label>
                <div class="col-sm-9">
                  <input
                    placeholder="exe, zip"
                    class="form-control"
                    v-model="newConfiguration.BlockedFileTypes"
                  />
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
                        v-if="option.type != 'custom'"
                      >({{ option.info }})</span>
                      <span
                        class="gray"
                        v-if="option.type == 'custom'"
                      >({{$t('categories.custom')}})</span>
                    </template>
                    <div slot="no-options">{{$t('filter.category_not_found')}}</div>
                  </v-select>
                  <span v-if="newConfiguration.errors.DefaultFilter.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}. {{$t('validation.'+newConfiguration.errors.DefaultFilter.message)}}:
                    <b>{{newConfiguration.errors.DefaultFilter.value}}</b>
                  </span>
                </div>
                <div class="col-sm-2">
                  <a class="adjust-clear" @click="selectAll('default')">{{$t('select_all')}}</a>
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
                  <a
                    v-show="defaultProfile.Categories && defaultProfile.Categories.length > 0"
                    class="adjust-clear-bottom"
                    @click="defaultProfile.Categories = []"
                  >{{$t('clear_all')}}</a>
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

    <div class="modal" id="wizardProfileModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog modal-lg wizard-pf">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{currentProfile.isEdit ? $t('filter.edit_profile') + ' ' +currentProfile.name : $t('filter.add_profile')}}</h4>
          </div>
          <div class="modal-body wizard-pf-body clearfix">
            <div class="wizard-pf-steps">
              <ul class="wizard-pf-steps-indicator wizard-pf-steps-alt-indicator active">
                <li :class="['wizard-pf-step', currentProfile.step == 1 ? 'active' : '']">
                  <a>
                    <span class="wizard-pf-step-number">1</span>
                    <span class="wizard-pf-step-title">{{$t('filter.who')}}</span>
                  </a>
                </li>

                <li :class="['wizard-pf-step', currentProfile.step == 2 ? 'active' : '']">
                  <a>
                    <span class="wizard-pf-step-number">2</span>
                    <span class="wizard-pf-step-title">{{$t('filter.what')}}</span>
                  </a>
                </li>

                <li :class="['wizard-pf-step', currentProfile.step == 3 ? 'active' : '']">
                  <a>
                    <span class="wizard-pf-step-number">3</span>
                    <span class="wizard-pf-step-title">{{$t('filter.when')}}</span>
                  </a>
                </li>

                <li :class="['wizard-pf-step', currentProfile.step == 4 ? 'active' : '']">
                  <a>
                    <span class="wizard-pf-step-number">4</span>
                    <span class="wizard-pf-step-title">{{$t('filter.final')}}</span>
                  </a>
                </li>
              </ul>
            </div>

            <div class="wizard-pf-row">
              <div class="wizard-pf-sidebar">
                <ul class="list-group">
                  <li :class="['list-group-item', currentProfile.step == 1 ? 'active' : '']">
                    <a>
                      <span class="wizard-pf-substep-number">{{$t('filter.who')}}:</span>
                      <br />
                      <span class="wizard-pf-substep-title reduce-font-span span-color">
                        {{currentProfile.who.Src || '-'}}
                        <span
                          v-if="currentProfile.who.Src.length > 0"
                        >
                          <br />
                          ({{currentProfile.who.Type}})
                        </span>
                      </span>
                    </a>
                  </li>
                  <li
                    v-if="currentProfile.step >= 2"
                    :class="['list-group-item', currentProfile.step == 2 ? 'active' : '']"
                  >
                    <a>
                      <span class="wizard-pf-substep-number">{{$t('filter.what')}}:</span>
                      <br />
                      <span class="wizard-pf-substep-title reduce-font-span span-color">
                        {{$t('filter.categories')}}:
                        <span
                          class="semi-bold"
                        >{{currentProfile.what.Categories.length == 0 ? '-' : currentProfile.what.Categories.join(', ')}}</span>
                        <br />
                        {{$t('filter.blacklist')}}:
                        <span
                          :class="['fa', currentProfile.what.BlackList ? 'fa-check green' : 'fa-times red']"
                        ></span>
                        <br />
                        {{$t('filter.whitelist')}}:
                        <span
                          :class="['fa', currentProfile.what.WhiteList ? 'fa-check green' : 'fa-times red']"
                        ></span>
                        <br />
                        {{$t('filter.mode')}}:
                        {{currentProfile.what.BlockAll == 'enabled' ? $t('filter.block_all_short') : $t('filter.allow_all_short')}}
                      </span>
                    </a>
                  </li>
                  <li
                    v-if="currentProfile.step >= 3"
                    :class="['list-group-item', currentProfile.step == 3 ? 'active' : '']"
                  >
                    <a>
                      <span class="wizard-pf-substep-number">{{$t('filter.when')}}:</span>
                      <br />
                      <span class="wizard-pf-substep-title reduce-font-span span-color">
                        {{currentProfile.when.Times.length}}
                        <span
                          class="semi-bold"
                        >{{currentProfile.when.Times.length == 1 ? $t('filter.time_condition_set') : $t('filter.time_conditions_set')}}</span>
                      </span>
                    </a>
                  </li>
                </ul>
              </div>
              <div class="wizard-pf-main">
                <!-- STEP WHO -->
                <div :class="['wizard-pf-contents', currentProfile.step == 1 ? '' : 'hidden']">
                  <form
                    id="profile-step-1-form"
                    class="form-horizontal"
                    v-on:submit.prevent="validateStep()"
                  >
                    <div class="blank-slate-pf adjust-blank-pad">
                      <div class="blank-slate-pf-icon">
                        <span class="pficon pficon-user"></span>
                      </div>
                      <h1>{{$t('filter.main_desc_wizard')}}</h1>
                      <p v-html="$t('filter.second_desc_wizard')">.</p>
                    </div>

                    <div
                      :class="['form-group', currentProfile.who.errors.Src.hasError ? 'has-error' : '']"
                    >
                      <label class="col-sm-3 control-label">{{$t('filter.who')}}</label>
                      <div class="col-sm-9">
                        <suggestions
                          v-model="currentProfile.who.Src"
                          :options="autoOptions"
                          :onInputChange="filterWhoAuto"
                          :onItemSelected="selectWhoAuto"
                          :required="!currentProfile.isEdit"
                        >
                          <div slot="item" slot-scope="props" class="single-item">
                            <span>
                              <span
                                v-show="props.item.type == 'role'"
                                :class="['square-'+ props.item.name.toUpperCase()]"
                              ></span>
                              {{props.item.type == 'role' ? props.item.name.toUpperCase() : props.item.name}}
                              <span
                                v-show="props.item.IpAddress || props.item.Address"
                                class="gray"
                              >({{ props.item.IpAddress || props.item.Address }})</span>
                              <i class="mg-left-5">{{props.item.Description}}</i>
                              <b class="mg-left-5">{{props.item.type | capitalize}}</b>
                            </span>
                          </div>
                        </suggestions>
                        <span
                          v-if="currentProfile.who.SrcType && currentProfile.who.SrcType.length > 0"
                          class="help-block gray"
                        >{{currentProfile.who.SrcType}}</span>
                        <span v-if="currentProfile.who.errors.Src.hasError" class="help-block">
                          {{$t('validation.validation_failed')}}:
                          {{$t('validation.'+currentProfile.who.errors.Src.message)}}
                        </span>
                      </div>
                    </div>
                    <button class="hidden" type="submit" id="profile-step-1-submit"></button>
                  </form>
                </div>
                <!-- END STEP WHO -->

                <!-- STEP WHAT -->
                <div :class="['wizard-pf-contents', currentProfile.step == 2 ? '' : 'hidden']">
                  <form
                    id="profile-step-2-form"
                    class="form-horizontal"
                    v-on:submit.prevent="validateStep()"
                  >
                    <div class="form-group">
                      <label class="col-sm-3 control-label">{{$t('filter.enable_global_blacklist')}}</label>
                      <div class="col-sm-9">
                        <toggle-button
                          class="min-toggle"
                          :width="40"
                          :height="20"
                          :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                          :value="currentProfile.what.BlackList"
                          :sync="true"
                          @change="currentProfile.what.BlackList = !currentProfile.what.BlackList"
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
                          :value="currentProfile.what.WhiteList"
                          :sync="true"
                          @change="currentProfile.what.WhiteList = !currentProfile.what.WhiteList"
                        />
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label">{{$t('filter.mode')}}</label>
                      <div class="col-sm-9">
                        <select class="form-control" v-model="currentProfile.what.BlockAll">
                          <option value="enabled">{{$t('filter.block_all')}}</option>
                          <option value="disabled">{{$t('filter.allow_all')}}</option>
                        </select>
                      </div>
                    </div>

                    <div
                      :class="['form-group', currentProfile.what.errors.DefaultFilter.hasError ? 'has-error' : '']"
                    >
                      <label class="col-sm-3 control-label">{{$t('filter.categories')}}</label>
                      <div class="col-sm-7">
                        <v-select
                          @input="addCategoryProfile"
                          v-model="currentProfile.what.categoryToAdd"
                          :options="categories"
                          label="name"
                          :clearable="false"
                          :placeholder="$t('filter.select_categories')"
                          :components="{OpenIndicator}"
                          required
                        >
                          <template slot="option" slot-scope="option">
                            {{ option.name }}
                            <span
                              class="gray"
                              v-if="option.type != 'custom'"
                            >({{ option.info }})</span>
                            <span
                              class="gray"
                              v-if="option.type == 'custom'"
                            >({{$t('categories.custom')}})</span>
                          </template>
                          <div slot="no-options">{{$t('filter.category_not_found')}}</div>
                        </v-select>
                        <span
                          v-if="currentProfile.what.errors.DefaultFilter.hasError"
                          class="help-block"
                        >
                          {{$t('validation.validation_failed')}}. {{$t('validation.'+currentProfile.what.errors.DefaultFilter.message)}}:
                          <b>{{currentProfile.what.errors.DefaultFilter.value}}</b>
                        </span>
                      </div>
                      <div class="col-sm-2">
                        <a class="adjust-clear" @click="selectAll('current')">{{$t('select_all')}}</a>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="textInput-modal-markup"></label>
                      <div class="col-sm-9">
                        <ul class="list-inline compact">
                          <li
                            v-for="(c,i) in currentProfile.what.Categories"
                            v-bind:key="i"
                            class="adjust-space"
                          >
                            <span class="label label-info">
                              {{c}}
                              <a @click="removeCategoryProfile(i)" class="remove-item-inline">
                                <span class="fa fa-times"></span>
                              </a>
                            </span>
                          </li>
                        </ul>
                        <a
                          v-show="currentProfile.what.Categories && currentProfile.what.Categories.length > 0"
                          class="adjust-clear-bottom"
                          @click="currentProfile.what.Categories = []"
                        >{{$t('clear_all')}}</a>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label">{{$t('filter.description')}}</label>
                      <div class="col-sm-9">
                        <input
                          v-model="currentProfile.what.Description"
                          type="text"
                          class="form-control"
                        />
                      </div>
                    </div>

                    <legend class="fields-section-header-pf" aria-expanded="true">
                      <span
                        :class="['fa fa-angle-right field-section-toggle-pf', currentProfile.what.advanced ? 'fa-angle-down' : '']"
                      ></span>
                      <a
                        class="field-section-toggle-pf"
                        @click="toggleAdvancedProfileMode()"
                      >{{$t('advanced_mode')}}</a>
                    </legend>

                    <div v-show="currentProfile.what.advanced" class="form-group">
                      <label class="col-sm-3 control-label">{{$t('filter.block_access_ip_address')}}</label>
                      <div class="col-sm-9">
                        <toggle-button
                          class="min-toggle"
                          :width="40"
                          :height="20"
                          :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                          :value="currentProfile.what.BlockIpAccess"
                          :sync="true"
                          @change="currentProfile.what.BlockIpAccess = !currentProfile.what.BlockIpAccess"
                        />
                      </div>
                    </div>

                    <div v-show="currentProfile.what.advanced" class="form-group">
                      <label class="col-sm-3 control-label">{{$t('filter.block_file_extensions')}}</label>
                      <div class="col-sm-9">
                        <toggle-button
                          class="min-toggle"
                          :width="40"
                          :height="20"
                          :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                          :value="currentProfile.what.BlockFileTypes"
                          :sync="true"
                          @change="currentProfile.what.BlockFileTypes = !currentProfile.what.BlockFileTypes"
                        />
                      </div>
                    </div>

                    <button class="hidden" type="submit" id="profile-step-2-submit"></button>
                  </form>
                </div>
                <!-- END STEP WHO -->

                <!-- STEP WHEN -->
                <div :class="['wizard-pf-contents', currentProfile.step == 3 ? '' : 'hidden']">
                  <form
                    id="profile-step-3-form"
                    class="form-horizontal"
                    v-on:submit.prevent="validateStep()"
                  >
                    <div class="form-group">
                      <label
                        class="col-sm-3 control-label"
                        for="textInput-modal-markup"
                      >{{$t('filter.always')}}</label>
                      <div class="col-sm-4">
                        <toggle-button
                          class="min-toggle"
                          :width="40"
                          :height="20"
                          :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
                          :value="currentProfile.when.always"
                          :sync="true"
                          @change="toggleAlways()"
                        />
                      </div>
                    </div>

                    <legend v-show="!currentProfile.when.always">{{$t('filter.specify_time_range')}}</legend>
                    <div
                      v-show="!currentProfile.when.always"
                      :class="['form-group', currentProfile.when.empty.errors.StartTime.hasError || currentProfile.when.empty.errors.EndTime.hasError ? 'has-error' : '']"
                    >
                      <label
                        class="col-sm-3 control-label"
                        for="textInput-modal-markup"
                      >{{$t('filter.range')}}</label>
                      <div class="col-sm-4">
                        <label>{{$t('filter.time_start')}}</label>
                        <input
                          class="col-sm-3 form-control"
                          type="text"
                          placeholder="00:15"
                          v-model="currentProfile.when.empty.StartTime"
                        />
                        <span
                          v-if="currentProfile.when.empty.errors.StartTime.hasError"
                          class="help-block"
                        >
                          {{$t('validation.validation_failed')}}:
                          {{$t('validation.'+currentProfile.when.empty.errors.StartTime.message)}}
                        </span>
                      </div>
                      <div class="col-sm-4">
                        <label>{{$t('filter.time_stop')}}</label>
                        <input
                          class="col-sm-3 form-control"
                          type="text"
                          placeholder="23:30"
                          v-model="currentProfile.when.empty.EndTime"
                        />
                        <span
                          v-if="currentProfile.when.empty.errors.EndTime.hasError"
                          class="help-block"
                        >
                          {{$t('validation.validation_failed')}}:
                          {{$t('validation.'+currentProfile.when.empty.errors.EndTime.message)}}
                        </span>
                      </div>
                    </div>
                    <div
                      v-show="!currentProfile.when.always"
                      :class="['form-group', currentProfile.when.empty.errors.Days.hasError ? 'has-error' : '']"
                    >
                      <label
                        class="col-sm-3 control-label"
                        for="textInput-modal-markup"
                      >{{$t('weekdays')}}</label>
                      <div class="col-sm-9">
                        <select
                          @change="addDayToWeekdays(currentProfile.when.empty.dayToAdd)"
                          v-model="currentProfile.when.empty.dayToAdd"
                          class="combobox form-control"
                        >
                          <option>-</option>
                          <option
                            :value="d"
                            v-for="(d,k) in weekdays"
                            v-bind:key="k"
                          >{{$t('filter.'+d)}}</option>
                        </select>
                        <span
                          v-if="currentProfile.when.empty.errors.Days.hasError"
                          class="help-block"
                        >{{currentProfile.when.empty.errors.Days.message}}</span>
                      </div>
                    </div>
                    <div v-show="!currentProfile.when.always" class="form-group">
                      <label class="col-sm-3 control-label" for="textInput-modal-markup"></label>
                      <div class="col-sm-9">
                        <ul class="list-inline compact">
                          <li
                            v-for="(i, ki) in currentProfile.when.empty.Days"
                            v-bind:key="i"
                            class="mg-bottom-5"
                          >
                            <span class="label label-info">
                              {{$t('filter.'+i)}}
                              <a
                                @click="removeDayToWeekdays(ki)"
                                class="remove-item-inline"
                              >
                                <span class="fa fa-times"></span>
                              </a>
                            </span>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div v-show="!currentProfile.when.always" class="form-group">
                      <label class="col-sm-3 control-label">{{$t('filter.description')}}</label>
                      <div class="col-sm-9">
                        <input
                          v-model="currentProfile.when.empty.Description"
                          type="text"
                          class="form-control"
                        />
                      </div>
                    </div>

                    <button
                      v-show="!currentProfile.when.always"
                      class="btn btn-primary"
                      type="submit"
                      :disabled="currentProfile.when.empty.StartTime.length == 0 || currentProfile.when.empty.EndTime.length == 0 || currentProfile.when.empty.Days.length == 0"
                      @click="addTime()"
                    >{{$t('filter.add_time')}}</button>
                    <div
                      v-if="currentProfile.when.empty.isLoading && !currentProfile.when.always"
                      class="spinner spinner-sm form-spinner-loader adjust-add-time-spinner"
                    ></div>
                    <div class="form-group"></div>
                    <ul v-show="!currentProfile.when.always" class="list-inline compact">
                      <li
                        v-for="(i, ki) in currentProfile.when.Times"
                        v-bind:key="ki"
                        class="mg-bottom-5 adjust-background-time"
                      >
                        <span class="label label-info adjust-padding-time">
                          <a
                            @click="removeTimeFromTimes(ki)"
                            class="remove-item-inline adjust-remove-time"
                          >
                            <span class="fa fa-times"></span>
                          </a>
                          {{i.Description}}
                          <br />
                          {{i.StartTime}} - {{i.EndTime}}
                          <br />
                          {{$t('days')}}:
                          <span class="semi-bold">{{printDays(i.Days)}}</span>
                        </span>
                      </li>
                    </ul>
                    <div v-show="!currentProfile.when.always" class="divider adjust-divider-times"></div>
                    <div class="form-group"></div>
                    <div class="form-group"></div>

                    <div v-show="!currentProfile.when.always" class="form-group">
                      <label class="col-sm-3 control-label">{{$t('filter.condition_not_match')}}</label>
                      <div class="col-sm-9">
                        <select class="form-control" v-model="currentProfile.FilterElse">
                          <option
                            v-show="s.type == 'profile'"
                            v-for="(s,sk) in sources"
                            v-bind:key="sk"
                            :value="s.name"
                          >{{s.name}} - {{s.Description}}</option>
                        </select>
                      </div>
                    </div>

                    <button class="hidden" type="submit" id="profile-step-3-submit"></button>
                  </form>
                </div>
                <!-- END STEP WHEN -->

                <!-- STEP FINAL -->
                <div :class="['wizard-pf-contents', currentProfile.step == 4 ? '' : 'hidden']">
                  <form
                    id="profile-step-4-form"
                    class="form-horizontal"
                    v-on:submit.prevent="saveProfile(currentProfile)"
                  >
                    <div
                      :class="['form-group', currentProfile.errors.name.hasError ? 'has-error' : '']"
                    >
                      <label
                        class="col-sm-3 control-label"
                        for="textInput-markup"
                      >{{$t('filter.name')}}</label>
                      <div class="col-sm-9">
                        <input
                          :disabled="currentProfile.isEdit"
                          v-model="currentProfile.name"
                          required
                          type="text"
                          class="form-control"
                        />
                        <span v-if="currentProfile.errors.name.hasError" class="help-block">
                          {{$t('validation.validation_failed')}}:
                          {{$t('validation.'+currentProfile.errors.name.message)}}
                        </span>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label">{{$t('filter.description')}}</label>
                      <div class="col-sm-9">
                        <input
                          v-model="currentProfile.Description"
                          type="text"
                          class="form-control"
                        />
                      </div>
                    </div>

                    <button class="hidden" type="submit" id="profile-step-4-submit"></button>
                  </form>
                </div>
                <!-- END STEP FINAL -->
              </div>
            </div>
          </div>

          <div class="modal-footer wizard-pf-footer">
            <div
              v-if="currentProfile.isLoading"
              class="spinner spinner-sm form-spinner-loader adjust-wizard-spinner"
            ></div>
            <button
              type="button"
              class="btn btn-primary wizard-pf-next"
              :disabled="currentProfile.step == 4 || !currentProfile.who.Type"
              v-if="currentProfile.step <= 3"
              @click="nextStep()"
            >
              <span class="wizard-pf-button-text">{{$t('next')}}</span>
              <span class="i fa fa-angle-right"></span>
            </button>
            <button
              type="button"
              class="btn btn-default wizard-pf-back"
              :disabled="currentProfile.step == 1"
              @click="prevStep()"
            >
              <span class="i fa fa-angle-left"></span>
              <span class="wizard-pf-button-text">{{$t('back')}}</span>
            </button>
            <button
              type="button"
              class="btn btn-default btn-cancel wizard-pf-cancel wizard-pf-dismiss"
              data-dismiss="modal"
              @click="resetProfile()"
            >{{$t('cancel')}}</button>
            <button
              type="button"
              class="btn btn-primary wizard-pf-close wizard-pf-dismiss"
              v-if="currentProfile.step == 4"
              @click="saveProfile(currentProfile)"
            >{{$t('save')}}</button>
          </div>
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

          if (vm.view.menu.installed) {
            vm.getCategories();
            vm.getConfiguration();
            vm.getProxyConfiguration();
            vm.getProfiles();
            vm.getSources();
          } else {
            vm.view.isLoaded = true;
          }
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
      sources: [],
      weekdays: ["m", "t", "w", "h", "f", "a", "s"],
      currentProfile: this.initProfile(),
      toDeleteProfile: {},
      configuration: {
        AntiVirus: false,
        DomainWhitelist: [],
        DomainBlacklist: [],
        BlockedFileTypes: [],
        Filter: true,
        Expressions: false
      },
      newConfiguration: {
        errors: this.initConfigurationErrors()
      },
      proxyConfiguration: {},
      defaultProfile: {},
      defaultProfileLoaded: {},
      categories: [],
      source: {
        list: null,
        downloaded: false
      },
      OpenIndicator: {
        render: createElement =>
          createElement("span", { class: { toggle: true } })
      },
      autoOptions: {
        inputClass: "form-control"
      }
    };
  },
  methods: {
    initProfile(profile) {
      return {
        isEdit: false,
        isLoading: false,
        who: {
          Src: profile ? profile.Src.name : "",
          SrcType: "",
          Type: profile ? profile.Src.type : "",
          errors: this.initProfileErrors().who
        },
        what: {
          BlockIpAccess: profile
            ? profile.Filter.BlockIpAccess == "enabled"
              ? true
              : false
            : false,
          Categories: profile ? profile.Filter.Categories : [],
          BlockFileTypes: profile
            ? profile.Filter.BlockFileTypes == "enabled"
              ? true
              : false
            : false,
          BlackList: profile
            ? profile.Filter.BlackList == "enabled"
              ? true
              : false
            : false,
          BlockAll: profile ? profile.Filter.BlockAll : "disabled",
          WhiteList: profile
            ? profile.Filter.WhiteList == "enabled"
              ? true
              : false
            : false,
          Description: profile ? profile.Filter.Description : "",
          categoryToAdd: {},
          advanced: false,
          errors: this.initProfileErrors().what
        },
        when: {
          empty: {
            StartTime: "",
            EndTime: "",
            Days: [],
            Description: "",
            dayToAdd: {},
            isLoading: false,
            errors: this.initProfileErrors().when
          },
          always: profile ? (profile.Time.length > 0 ? false : true) : true,
          Times: profile ? profile.Time : []
        },
        name: profile ? profile.name : "",
        Description: profile ? profile.Description : "",
        FilterElse: profile ? profile.FilterElse : "",
        errors: {
          name: {
            hasError: false,
            message: ""
          }
        },
        step: 1
      };
    },
    initProfileErrors() {
      return {
        who: {
          Src: {
            hasError: false,
            message: ""
          }
        },
        what: {
          DefaultFilter: {
            hasError: false,
            message: "",
            value: ""
          }
        },
        when: {
          StartTime: {
            hasError: false,
            message: ""
          },
          EndTime: {
            hasError: false,
            message: ""
          },
          Days: {
            hasError: false,
            message: ""
          }
        },
        name: {
          hasError: false,
          message: ""
        }
      };
    },
    nextStep() {
      var context = this;
      $("#profile-step-" + this.currentProfile.step + "-submit").trigger(
        "click"
      );
      if (
        document
          .getElementById("profile-step-" + this.currentProfile.step + "-form")
          .checkValidity()
      ) {
        context.validateStep(function() {
          context.currentProfile.step++;
        });
      }
    },
    prevStep() {
      this.currentProfile.step--;
    },
    validateStep(callback) {
      var context = this;
      var validateObj = {};
      var errorsObj = "";

      switch (context.currentProfile.step) {
        case 1:
          errorsObj = "who";
          validateObj = {
            name: context.currentProfile.who.Src,
            type: context.currentProfile.who.Type,
            action: "Src"
          };
          break;

        case 2:
          errorsObj = "what";
          validateObj = {
            BlockIpAccess: context.currentProfile.what.BlockIpAccess
              ? "enabled"
              : "disabled",
            Categories: context.currentProfile.what.Categories,
            BlockFileTypes: context.currentProfile.what.BlockFileTypes
              ? "enabled"
              : "disabled",
            BlackList: context.currentProfile.what.BlackList
              ? "enabled"
              : "disabled",
            BlockAll: context.currentProfile.what.BlockAll,
            WhiteList: context.currentProfile.what.WhiteList
              ? "enabled"
              : "disabled",
            action: "Filter"
          };
          break;

        case 3:
          errorsObj = "when";
          break;
      }

      if (callback && context.currentProfile.step < 3) {
        context.currentProfile.isLoading = true;
        nethserver.exec(
          ["nethserver-squid/filter/validate"],
          validateObj,
          null,
          function(success) {
            context.currentProfile.isLoading = false;
            callback ? callback() : undefined;
          },
          function(error, data) {
            var errorData = {};
            context.currentProfile.isLoading = false;
            context.currentProfile[
              errorsObj
            ].errors = context.initProfileErrors()[errorsObj];

            try {
              errorData = JSON.parse(data);
              for (var e in errorData.attributes) {
                var attr = errorData.attributes[e];
                context.currentProfile[errorsObj].errors[
                  attr.parameter
                ].hasError = true;
                context.currentProfile[errorsObj].errors[
                  attr.parameter
                ].message = attr.error;

                if (attr.parameter == "DefaultFilter") {
                  context.currentProfile[errorsObj].errors[
                    attr.parameter
                  ].value = attr.value;
                }
                context.$forceUpdate();
              }
            } catch (e) {
              console.error(e);
            }
          }
        );
      } else {
        callback ? callback() : undefined;
      }
    },
    filterWhoAuto(query) {
      this.currentProfile.who.Type = null;
      this.currentProfile.who.SrcType = null;

      if (query.trim().length === 0) {
        return null;
      }

      return this.sources.filter(function(source) {
        return (
          source.type != "profile" &&
          (source.type.toLowerCase().includes(query.toLowerCase()) ||
            source.name.toLowerCase().includes(query.toLowerCase()) ||
            (source.Description &&
              source.Description.toLowerCase().includes(query.toLowerCase())) ||
            (source.IpAddress &&
              source.IpAddress.toLowerCase().includes(query.toLowerCase())) ||
            (source.Address &&
              source.Address.toLowerCase().includes(query.toLowerCase())))
        );
      });
    },
    selectWhoAuto(item) {
      this.currentProfile.who.Src = item.name;
      this.currentProfile.who.Type = item.type;
      this.currentProfile.who.SrcType =
        item.name +
        " " +
        (item.IpAddress ? item.IpAddress + " " : "") +
        (item.Address ? item.Address + " " : "") +
        (item.Start && item.End ? item.Start + " - " + item.End + " " : "") +
        "(" +
        item.type +
        ")";
    },
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
    toggleAdvancedMode() {
      this.defaultProfile.advanced = !this.defaultProfile.advanced;
      this.$forceUpdate();
    },
    toggleAdvancedProfileMode() {
      this.currentProfile.what.advanced = !this.currentProfile.what.advanced;
      this.$forceUpdate();
    },
    toggleAlways() {
      this.currentProfile.when.always = !this.currentProfile.when.always;
      this.$forceUpdate();
    },
    selectAll(obj) {
      var cats = this.categories.map(function(i) {
        return i.name;
      });

      if (obj == "current") {
        this.currentProfile.what.Categories = cats;
      }

      if (obj == "default") {
        this.defaultProfile.Categories = cats;
      }
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
        "<div><span>" +
        context.$i18n.t("filter.mode") +
        "</span>: " +
        (profile.Filter.BlockAll == "enabled"
          ? context.$i18n.t("filter.block_all_short")
          : context.$i18n.t("filter.allow_all_short")) +
        "</div>";
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

      context.view.isLoaded = false;
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
          context.categories = success.categories.sort(function(a, b) {
            return a.name > b.name ? 1 : -1;
          });
          context.source = success.source;

          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getSources() {
      var context = this;

      nethserver.exec(
        ["nethserver-squid/filter/read"],
        {
          action: "objects"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.sources = success.objects;
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

          context.defaultProfileLoaded = JSON.parse(JSON.stringify(context.defaultProfile));

          context.categoryToAdd = {};

          context.defaultProfile.advanced = false;
          context.defaultProfile.isLoading = false;

          setTimeout(function() {
            $('[data-toggle="tooltip-second"]').tooltip();
          }, 500);
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

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
          }, 500);
        },
        function(error) {
          console.error(error);
        }
      );
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
        BlockedFileTypes: {
          hasError: false,
          message: ""
        },
        DefaultFilter: {
          hasError: false,
          message: "",
          value: ""
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
          BlockedFileTypes: context.configuration.BlockedFileTypes,
          Filter: context.configuration.Filter ? "enabled" : "disabled",
          Expressions: context.configuration.Expressions
            ? "enabled"
            : "disabled",
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
            BlockedFileTypes: context.configuration.BlockedFileTypes,
            DefaultFilter: context.configuration.DefaultFilter,
            Filter: "disabled",
            Expressions: context.configuration.Expressions
              ? "enabled"
              : "disabled",
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
              if (attr.parameter == "DefaultFilter") {
                context.newConfiguration.errors[attr.parameter].value =
                  attr.value;
              }
            }

            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    openConfigureDefault() {
      this.newConfiguration = JSON.parse(JSON.stringify(this.configuration));
      this.newConfiguration.errors = this.initConfigurationErrors();
      this.newConfiguration.DomainWhitelist = this.newConfiguration.DomainWhitelist.join(
        "\n"
      );
      this.newConfiguration.DomainBlacklist = this.newConfiguration.DomainBlacklist.join(
        "\n"
      );
      this.newConfiguration.BlockedFileTypes = this.newConfiguration.BlockedFileTypes.join(
        ","
      );

      this.defaultProfile = JSON.parse(JSON.stringify(this.defaultProfileLoaded));
      this.defaultProfile.categoryToAdd = {};

      $("#configureDefaultModal").modal("show");
    },
    openCreateProfile() {
      this.currentProfile = this.initProfile();
      $("#wizardProfileModal").modal("show");
    },

    openEditProfile(profile) {
      this.currentProfile = this.initProfile(profile);
      this.currentProfile.errors = this.initProfileErrors();
      this.currentProfile.isEdit = true;
      this.currentProfile.who.SrcType = this.currentProfile.who.Type;
      $("#wizardProfileModal").modal("show");
    },

    openDeleteProfile(profile) {
      this.toDeleteProfile = JSON.parse(JSON.stringify(profile));
      $("#deleteProfileModal").modal("show");
    },

    saveProfile(profile) {
      var context = this;

      $("#profile-step-" + this.currentProfile.step + "-submit").trigger(
        "click"
      );
      if (
        document
          .getElementById("profile-step-" + this.currentProfile.step + "-form")
          .checkValidity()
      ) {
        var profileObj = {
          Time: context.currentProfile.when.always
            ? []
            : context.currentProfile.when.Times,
          name: context.currentProfile.name,
          FilterElse: context.currentProfile.FilterElse,
          Filter: {
            BlockIpAccess: context.currentProfile.what.BlockIpAccess
              ? "enabled"
              : "disabled",
            Categories: context.currentProfile.what.Categories,
            BlockFileTypes: context.currentProfile.what.BlockFileTypes
              ? "enabled"
              : "disabled",
            BlackList: context.currentProfile.what.BlackList
              ? "enabled"
              : "disabled",
            BlockAll: context.currentProfile.what.BlockAll,
            WhiteList: context.currentProfile.what.WhiteList
              ? "enabled"
              : "disabled",
            Description: context.currentProfile.what.Description
          },
          Src: {
            name: context.currentProfile.who.Src,
            type: context.currentProfile.who.Type
          },
          Description: context.currentProfile.Description,
          action: profile.isEdit ? "update-profile" : "create-profile"
        };

        context.currentProfile.isLoading = true;
        context.$forceUpdate();
        nethserver.exec(
          ["nethserver-squid/filter/validate"],
          profileObj,
          null,
          function(success) {
            context.currentProfile.isLoading = false;
            $("#wizardProfileModal").modal("hide");

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
            context.$forceUpdate();

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
      }
    },

    resetProfile() {
      this.currentProfile = this.initProfile();
      $("#wizardProfileModal").modal("hide");
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
      this.defaultProfile.categoryToAdd = {};
    },
    categoryDefaultAlreadyAdded(category) {
      return this.defaultProfile.Categories.indexOf(category) > -1;
    },
    removeCategoryDefault(index) {
      this.defaultProfile.Categories.splice(index, 1);
    },
    addCategoryProfile(category) {
      category = (category && category.name) || null;
      if (category && category.length > 0) {
        if (!this.categoryProfileAlreadyAdded(category)) {
          this.currentProfile.what.Categories.push(category);
        }
      }
      this.currentProfile.what.categoryToAdd = {};
    },
    categoryProfileAlreadyAdded(category) {
      return this.currentProfile.what.Categories.indexOf(category) > -1;
    },
    removeCategoryProfile(index) {
      this.currentProfile.what.Categories.splice(index, 1);
    },
    dayAlreadyAdded(day) {
      return this.currentProfile.when.empty.Days.indexOf(day) > -1;
    },
    addDayToWeekdays(day) {
      if (day.length > 0 && day != "-") {
        if (!this.dayAlreadyAdded(day)) {
          this.currentProfile.when.empty.Days.push(day);
        }
      }
    },
    removeDayToWeekdays(index) {
      this.currentProfile.when.empty.Days.splice(index, 1);
    },
    addTime() {
      var context = this;

      context.currentProfile.when.empty.isLoading = true;
      nethserver.exec(
        ["nethserver-squid/filter/validate"],
        {
          StartTime: context.currentProfile.when.empty.StartTime,
          EndTime: context.currentProfile.when.empty.EndTime,
          Days: context.currentProfile.when.empty.Days,
          action: "Time"
        },
        null,
        function(success) {
          context.currentProfile.when.empty.isLoading = false;

          context.currentProfile.when.Times.push(
            context.currentProfile.when.empty
          );
          context.currentProfile.when.empty = {
            StartTime: "",
            EndTime: "",
            Days: [],
            Description: "",
            dayToAdd: {},
            isLoading: false,
            errors: context.initProfileErrors().when
          };

          context.$forceUpdate();
        },
        function(error, data) {
          var errorData = {};
          context.currentProfile.when.empty.isLoading = false;
          context.currentProfile.when.empty.errors = context.initProfileErrors().when;

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.currentProfile.when.empty.errors[
                attr.parameter
              ].hasError = true;
              context.currentProfile.when.empty.errors[attr.parameter].message =
                attr.error;
              context.$forceUpdate();
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    removeTimeFromTimes(index) {
      this.currentProfile.when.Times.splice(index, 1);
    },
    printDays(days) {
      var context = this;
      return days
        .map(function(d) {
          return context.$i18n.t("filter." + d);
        })
        .join(", ");
    },
    downloadCategories() {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "filter.categories_downloaded_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "filter.categories_downloaded_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-squid/categories/update"],
        {
          action: "download"
        },
        function(stream) {
          console.info("download", stream);
        },
        function(success) {
          // get all
          context.getConfiguration();
          context.getProfiles();
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
  border: 2px solid #ec7a08 !important;
}

.wizard-pf-sidebar .list-group-item > a {
  height: 100%;
  padding-bottom: 10px;
}

.wizard-pf-sidebar .list-group-item.active > a {
  height: 100%;
  padding-bottom: 10px;
  padding-left: 23px;
}

.wizard-pf-sidebar .list-group-item.active > a:after {
  display: none;
}

.wizard-pf-sidebar .list-group-item > a {
  white-space: unset !important;
}

.reduce-font-span {
  font-size: 12px;
}

.adjust-wizard-spinner {
  order: -2 !important;
  margin: 0 !important;
}

.span-color {
  color: #424242;
}

.adjust-divider-times {
  margin-top: 10px;
  margin-bottom: 10px;
}

.adjust-add-time-spinner {
  margin-left: 10px;
}

.adjust-remove-time {
  float: right;
  margin-top: 3px;
  margin-left: 10px;
}

.adjust-padding-time {
  padding: 0px;
}

.adjust-background-time {
  background: #21659c;
  margin: 4px;
}

.adjust-blank-pad {
  padding: 25px 120px;
}

.color-link-hover {
  color: #318fd1;
}

.adjust-clear-bottom {
  margin-top: 8px;
  display: inline-block;
}
</style>
