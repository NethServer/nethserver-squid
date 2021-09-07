<template>
  <div>
    <h2>{{ $t('proxy.title') }}</h2>
    <doc-info
      :placement="'top'"
      :title="$t('docs.proxy')"
      :chapter="'web_proxy'"
      :section="''"
      :inline="false"
    ></doc-info>

    <div v-show="!view.isLoaded" class="spinner spinner-lg"></div>

    <h3 v-if="view.isLoaded">{{ $t('proxy.configuration') }}</h3>
    <div v-if="view.isLoaded && !configuration.status" class="blank-slate-pf">
      <h1>{{$t('proxy.proxy_is_disabled')}}</h1>
      <p>{{$t('proxy.proxy_is_disabled_desc')}}.</p>
      <div class="blank-slate-pf-main-action">
        <button
          @click="toggleStatus(false)"
          class="btn btn-primary btn-lg"
        >{{$t('proxy.configure_proxy')}}</button>
      </div>
    </div>
    <div v-if="view.isLoaded && configuration.status" class="panel panel-default">
      <div class="panel-heading">
        <button
          v-if="configuration.status"
          @click="toggleStatus(true)"
          class="btn btn-primary right proxy-details"
        >{{$t('edit')}}</button>
        <toggle-button
          class="min-toggle right"
          :width="40"
          :height="20"
          :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
          :value="configuration.status"
          :sync="true"
          @change="toggleStatus(false)"
        />

        <span class="panel-title">
          {{$t('proxy.enabled')}}
          <span
            :class="['fa', configuration.status ? 'fa-check green' : 'fa-times red']"
          ></span>
        </span>
        <span v-if="configuration.status" class="proxy-details">
          <strong class="green">{{$t('proxy.green_trusted')}}</strong>:
          <code>{{$t('proxy.'+configuration.GreenMode)}}</code>
        </span>
        <span v-if="configuration.status" class="proxy-details">
          <strong class="blue">{{$t('proxy.blue_zones')}}</strong>:
          <code>{{$t('proxy.'+configuration.BlueMode)}}</code>
        </span>
      </div>
    </div>

    <h3 v-if="view.isLoaded && configuration.status">
      {{ $t('proxy.bypass') }}
      <doc-info
        :placement="'top'"
        :title="$t('proxy.bypass_desc_title')"
        :chapter="'bypass_desc'"
        :inline="true"
      ></doc-info>

    </h3>

    <ul class="nav nav-tabs nav-tabs-pf" v-if="view.isLoaded && configuration.status">
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#source-tab"
          id="source-tab-parent"
        >{{$t('proxy.source')}}</a>
      </li>
      <li>
        <a
          class="nav-link"
          data-toggle="tab"
          href="#destination-tab"
          id="destination-tab-parent"
        >{{$t('proxy.destination')}}</a>
      </li>
    </ul>

    <div class="tab-content" v-if="view.isLoaded && configuration.status">
      <div class="tab-pane fade active" id="source-tab" role="tabpanel" aria-labelledby="hosts-tab">
        <h3>{{$t('actions')}}</h3>
        <button
          @click="openCreateSource()"
          class="btn btn-primary btn-lg"
        >{{$t('proxy.add_source')}}</button>

        <h3>{{$t('list')}}</h3>
        <div v-if="!view.isLoaded" class="spinner spinner-lg"></div>
        <vue-good-table
          v-show="view.isLoaded"
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="sourceColumns"
          :rows="sourceRows"
          :lineNumbers="false"
          :defaultSortBy="{field: 'props.Host.name', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td :class="['fancy', props.row.props.status == 'enabled' ? '' : 'gray']">
              <span class="fa fa-user span-right-icon"></span>
              <a
                :class="['mg-left-5', props.row.props.status == 'enabled' ? '' : 'gray']"
                @click="props.row.props.status == 'enabled' ? openEditSource(props.row) : undefined"
              >
                <strong>{{ props.row.props.Host.name}}</strong>
                <span class="gray mg-left-5">({{ props.row.props.Host.type}})</span>
              </a>
            </td>
            <td :class="['fancy', props.row.props.status == 'enabled' ? '' : 'gray']">
              <span>{{props.row.props.Description}}</span>
            </td>
            <td>
              <button
                @click="props.row.props.status == 'disabled' ? toggleSourceStatus(props.row) : openEditSource(props.row)"
                :class="['btn btn-default', props.row.props.status == 'disabled' ? 'btn-primary' : '']"
              >
                <span
                  :class="['fa', props.row.props.status == 'disabled' ? 'fa-check' : 'fa-pencil', 'span-right-margin']"
                ></span>
                {{props.row.props.status == 'disabled' ? $t('enable') : $t('edit')}}
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
                    <a @click="toggleSourceStatus(props.row)">
                      <span
                        :class="['fa', props.row.props.status == 'enabled' ? 'fa-lock' : 'fa-check', 'span-right-margin']"
                      ></span>
                      {{props.row.props.status == 'enabled' ? $t('disable') : $t('enable')}}
                    </a>
                  </li>
                  <li role="presentation" class="divider"></li>
                  <li>
                    <a @click="openDeleteSource(props.row)">
                      <span class="fa fa-times span-right-margin"></span>
                      {{$t('delete')}}
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </template>
        </vue-good-table>
      </div>

      <div
        class="tab-pane fade active"
        id="destination-tab"
        role="tabpanel"
        aria-labelledby="hosts-tab"
      >
        <h3>{{$t('actions')}}</h3>
        <button
          @click="openCreateDestination()"
          class="btn btn-primary btn-lg"
        >{{$t('proxy.add_destination')}}</button>

        <h3>{{$t('list')}}</h3>
        <div v-if="!view.isLoaded" class="spinner spinner-lg"></div>
        <vue-good-table
          v-show="view.isLoaded"
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="destinationColumns"
          :rows="destinationRows"
          :lineNumbers="false"
          :defaultSortBy="{field: 'props.Host.name', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td :class="['fancy', props.row.props.status == 'enabled' ? '' : 'gray']">
              <span class="fa fa-user span-right-icon"></span>
              <a
                :class="['mg-left-5', props.row.props.status == 'enabled' ? '' : 'gray']"
                @click="props.row.props.status == 'enabled' ? openEditDestination(props.row) : undefined"
              >
                <strong
                  data-toggle="tooltip"
                  data-placement="top"
                  data-html="true"
                  :title="(props.row.props.Host && props.row.props.Host.name) || props.row.props.Domains.join('<br>')"
                >{{ (props.row.props.Host && props.row.props.Host.name) || props.row.props.Domains.join(',') | truncate}}</strong>
                <span
                  class="gray mg-left-5"
                >({{ props.row.props.Host && props.row.props.Host.type || "domains"}})</span>
              </a>
            </td>
            <td :class="['fancy', props.row.props.status == 'enabled' ? '' : 'gray']">
              <span>{{props.row.props.Description}}</span>
            </td>
            <td>
              <button
                @click="props.row.props.status == 'disabled' ? toggleDestinationStatus(props.row) : openEditDestination(props.row)"
                :class="['btn btn-default', props.row.props.status == 'disabled' ? 'btn-primary' : '']"
              >
                <span
                  :class="['fa', props.row.props.status == 'disabled' ? 'fa-check' : 'fa-pencil', 'span-right-margin']"
                ></span>
                {{props.row.props.status == 'disabled' ? $t('enable') : $t('edit')}}
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
                    <a @click="toggleDestinationStatus(props.row)">
                      <span
                        :class="['fa', props.row.props.status == 'enabled' ? 'fa-lock' : 'fa-check', 'span-right-margin']"
                      ></span>
                      {{props.row.props.status == 'enabled' ? $t('disable') : $t('enable')}}
                    </a>
                  </li>
                  <li role="presentation" class="divider"></li>
                  <li>
                    <a @click="openDeleteDestination(props.row)">
                      <span class="fa fa-times span-right-margin"></span>
                      {{$t('delete')}}
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </template>
        </vue-good-table>
      </div>
    </div>
    <!-- MODALS -->
    <div class="modal" id="configureProxyModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('proxy.configure_proxy')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveConfiguration(newConfiguration)">
            <div class="modal-body">
              <div
                :class="['form-group', newConfiguration.errors.GreenMode.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('proxy.mode_green_zones')}}</label>
                <div class="col-sm-9">
                  <select class="form-control" v-model="newConfiguration.GreenMode">
                    <option value="manual">{{$t('proxy.manual')}}</option>
                    <option value="authenticated">{{$t('proxy.authenticated')}}</option>
                    <option value="transparent">{{$t('proxy.transparent')}}</option>
                    <option value="transparent_ssl">{{$t('proxy.transparent_ssl')}}</option>
                  </select>
                </div>
              </div>

              <div
                :class="['form-group', newConfiguration.errors.BlueMode.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">{{$t('proxy.mode_blue_zones')}}</label>
                <div class="col-sm-9">
                  <select class="form-control" v-model="newConfiguration.BlueMode">
                    <option value="manual">{{$t('proxy.manual')}}</option>
                    <option value="authenticated">{{$t('proxy.authenticated')}}</option>
                    <option value="transparent">{{$t('proxy.transparent')}}</option>
                    <option value="transparent_ssl">{{$t('proxy.transparent_ssl')}}</option>
                  </select>
                </div>
              </div>

              <legend class="fields-section-header-pf" aria-expanded="true">
                <span
                  :class="['fa fa-angle-right field-section-toggle-pf', newConfiguration.advanced ? 'fa-angle-down' : '']"
                ></span>
                <a
                  class="field-section-toggle-pf"
                  @click="toggleAdvancedMode()"
                >{{$t('advanced_mode')}}</a>
              </legend>

              <div
                v-show="newConfiguration.advanced"
                :class="['form-group', newConfiguration.errors.PortBlock.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">
                  {{$t('proxy.block_http')}}
                  <doc-info
                    :placement="'top'"
                    :title="$t('proxy.block_http')"
                    :chapter="'block_http'"
                    :inline="true"
                  ></doc-info>
                </label>
                <div class="col-sm-1">
                  <input
                    :disabled="(newConfiguration.GreenMode == 'transparent' || newConfiguration.GreenMode == 'transparent_ssl') && (newConfiguration.BlueMode == 'transparent' || newConfiguration.BlueMode == 'transparent_ssl')"
                    type="checkbox"
                    class="form-control"
                    v-model="newConfiguration.PortBlock"
                  />
                  <span
                    v-if="newConfiguration.errors.PortBlock.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newConfiguration.errors.PortBlock.message)}}</span>
                </div>
                <div
                  v-if="(newConfiguration.GreenMode == 'transparent' || newConfiguration.GreenMode == 'transparent_ssl') && (newConfiguration.BlueMode == 'transparent' || newConfiguration.BlueMode == 'transparent_ssl')"
                  class="col-sm-8"
                >
                  <div class="alert alert-warning alert-dismissable">
                    <span class="pficon pficon-warning-triangle-o"></span>
                    <strong>{{$t('warning')}}.</strong>
                    {{$t('proxy.we_suggest_no_block')}}.
                  </div>
                </div>
              </div>
              <div
                v-show="newConfiguration.advanced"
                :class="['form-group', newConfiguration.errors.ParentProxy.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">
                  {{$t('proxy.parent_proxy')}}
                  <doc-info
                    :placement="'top'"
                    :title="$t('proxy.parent_proxy')"
                    :chapter="'parent_proxy'"
                    :inline="true"
                  ></doc-info>
                </label>
                <div class="col-sm-9">
                  <input
                    placeholder="192.168.0.1:8080"
                    class="form-control"
                    v-model="newConfiguration.ParentProxy"
                  />
                  <span
                    v-if="newConfiguration.errors.ParentProxy.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newConfiguration.errors.ParentProxy.message)}}</span>
                </div>
              </div>
              <div
                v-show="newConfiguration.advanced"
                :class="['form-group', newConfiguration.errors.SafePorts.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">
                  {{$t('proxy.safe_ports')}}
                  <doc-info
                    :placement="'top'"
                    :title="$t('proxy.safe_ports')"
                    :chapter="'safe_ports'"
                    :inline="true"
                  ></doc-info>
                </label>
                <div class="col-sm-9">
                  <input class="form-control" v-model="newConfiguration.SafePorts" />
                  <span
                    v-if="newConfiguration.errors.SafePorts.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newConfiguration.errors.SafePorts.message)}}</span>
                </div>
              </div>
              <div
                v-show="newConfiguration.advanced"
                :class="['form-group', newConfiguration.errors.PortRedirect.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label">
                  {{$t('proxy.redirect_port')}}
                  <doc-info
                    :placement="'top'"
                    :title="$t('proxy.redirect_port')"
                    :chapter="'redirect_port'"
                    :inline="true"
                  ></doc-info>
                </label>
                <div class="col-sm-9">
                  <input class="form-control" v-model="newConfiguration.PortRedirect" />
                  <span
                    v-if="newConfiguration.errors.PortRedirect.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+newConfiguration.errors.PortRedirect.message)}}</span>
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
    <div class="modal" id="createSourceModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{currentSource.isEdit ? $t('proxy.edit_source') + ' '+ currentSource.name : $t('proxy.add_source')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveSource(currentSource)">
            <div class="modal-body">
              <div :class="['form-group', currentSource.errors.Source.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('proxy.source')}}</label>
                <div class="col-sm-9">
                  <suggestions
                    v-model="currentSource.Source"
                    :options="autoOptions"
                    :onInputChange="filterSrcAuto"
                    :onItemSelected="selectSrcAuto"
                    :required="!currentSource.isEdit"
                  >
                    <div slot="item" slot-scope="props" class="single-item">
                      <span>
                        {{props.item.name}}
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
                    v-if="currentSource.SrcType && currentSource.SrcType.length > 0"
                    class="help-block gray"
                  >{{currentSource.SrcType}}</span>
                  <span v-if="currentSource.errors.Source.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+currentSource.errors.Source.message)}}
                  </span>
                </div>
              </div>
              <div
                :class="['form-group', currentSource.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('proxy.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="currentSource.Description" class="form-control" />
                  <span
                    v-if="currentSource.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentSource.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentSource.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{currentSource.isEdit ? $t('edit') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div
      class="modal"
      id="createDestinationModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{currentDestination.isEdit ? $t('proxy.edit_destination') + ' '+ currentDestination.name : $t('proxy.add_destination')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveDestination(currentDestination)">
            <div class="modal-body">
              <div
                :class="['form-group', currentDestination.errors.Destination.hasError ? 'has-error' : '']"
              >
                <label class="col-sm-3 control-label" for="textInput-modal-markup">
                  {{$t('proxy.destination')}}
                  <doc-info
                    :placement="'top'"
                    :title="$t('proxy.destination')"
                    :chapter="'destination'"
                    :inline="true"
                  ></doc-info>
                </label>
                <div class="col-sm-9">
                  <suggestions
                    v-model="currentDestination.Destination"
                    :options="autoOptions"
                    :onInputChange="filterDstAuto"
                    :onItemSelected="selectDstAuto"
                    :required="!currentDestination.isEdit"
                  >
                    <div slot="item" slot-scope="props" class="single-item">
                      <span>
                        {{props.item.name}}
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
                    v-if="currentDestination.DstType && currentDestination.DstType.length > 0"
                    class="help-block gray"
                  >{{currentDestination.DstType}}</span>
                  <span v-if="currentDestination.errors.Destination.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+currentDestination.errors.Destination.message)}}
                  </span>
                </div>
              </div>
              <div
                :class="['form-group', currentDestination.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('proxy.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="currentDestination.Description" class="form-control" />
                  <span
                    v-if="currentDestination.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentDestination.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div
                v-if="currentDestination.isLoading"
                class="spinner spinner-sm form-spinner-loader"
              ></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{currentDestination.isEdit ? $t('edit') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteSourceModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('proxy.delete_source')}} {{toDeleteSource.props.Host.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteSource(toDeleteSource)">
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
    <div
      class="modal"
      id="deleteDestinationModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{$t('proxy.delete_destination')}} {{(toDeleteDestination.props.Host && toDeleteDestination.props.Host.name) || toDeleteDestination.props.Domains.join(',')}}</h4>
          </div>
          <form
            class="form-horizontal"
            v-on:submit.prevent="deleteDestination(toDeleteDestination)"
          >
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
  name: "Proxy",
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  mounted() {
    this.getConfiguration();
    this.getBypasses();
    this.getObjects();
  },
  data() {
    return {
      view: {
        isLoaded: false
      },
      autoOptions: {
        inputClass: "form-control"
      },
      tableLangsTexts: this.tableLangs(),
      sourceColumns: [
        {
          label: this.$i18n.t("proxy.source"),
          field: "props.Host.name",
          filterable: true
        },
        {
          label: this.$i18n.t("proxy.description"),
          field: "props.Description",
          filterable: true
        },
        {
          label: this.$i18n.t("action"),
          field: "",
          filterable: true,
          sortable: false
        }
      ],
      sourceRows: [],
      destinationColumns: [
        {
          label: this.$i18n.t("proxy.destination"),
          field: "props.Host.name",
          filterable: true
        },
        {
          label: this.$i18n.t("proxy.description"),
          field: "props.Description",
          filterable: true
        },
        {
          label: this.$i18n.t("action"),
          field: "",
          filterable: true,
          sortable: false
        }
      ],
      destinationRows: [],
      configuration: {
        status: false,
        GreenMode: "manual",
        BlueMode: "manual",
        PortBlock: false,
        ParentProxy: "",
        SafePorts: [],
        PortRedirect: ""
      },
      newConfiguration: {
        status: false,
        GreenMode: "manual",
        BlueMode: "manual",
        PortBlock: false,
        ParentProxy: "",
        SafePorts: [],
        PortRedirect: "",
        errors: this.initConfigurationErrors()
      },
      currentSource: this.initSource(),
      currentDestination: this.initDestination(),
      toDeleteSource: {
        props: {
          Host: {
            name: ""
          },
          Domains: []
        }
      },
      toDeleteDestination: {
        props: {
          Host: {
            name: ""
          },
          Domains: []
        }
      },
      objects: []
    };
  },
  watch: {
    "newConfiguration.GreenMode": function(newVal, oldVal) {
      if (newVal == "transparent" || newVal == "transparent_ssl") {
        this.newConfiguration.PortBlock = false;
        this.$forceUpdate();
      }
    }
  },
  methods: {
    initSource() {
      return {
        Source: "",
        SrcType: "",
        Description: "",
        isLoading: false,
        isEdit: false,
        errors: this.initSourceErrors()
      };
    },
    initDestination() {
      return {
        Destination: "",
        DstType: "",
        Description: "",
        isLoading: false,
        isEdit: false,
        errors: this.initDestinationErrors()
      };
    },
    initSourceErrors() {
      return {
        Source: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    initDestinationErrors() {
      return {
        Destination: {
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
        GreenMode: {
          hasError: false,
          message: ""
        },
        BlueMode: {
          hasError: false,
          message: ""
        },
        PortBlock: {
          hasError: false,
          message: ""
        },
        ParentProxy: {
          hasError: false,
          message: ""
        },
        SafePorts: {
          hasError: false,
          message: ""
        },
        PortRedirect: {
          hasError: false,
          message: ""
        }
      };
    },
    filterSrcAuto(query) {
      this.currentSource.Type = null;

      if (query.trim().length === 0) {
        return null;
      }

      return this.objects.filter(function(source) {
        return (
          source.type.toLowerCase().includes(query.toLowerCase()) ||
          source.name.toLowerCase().includes(query.toLowerCase()) ||
          (source.Description && source.Description.toLowerCase().includes(query.toLowerCase())) ||
          (source.IpAddress &&
            source.IpAddress.toLowerCase().includes(query.toLowerCase())) ||
          (source.Address &&
            source.Address.toLowerCase().includes(query.toLowerCase()))
        );
      });
    },
    selectSrcAuto(item) {
      this.currentSource.Source = item.name;
      this.currentSource.Type = item.type;
      this.currentSource.SrcType =
        item.name +
        " " +
        (item.IpAddress ? item.IpAddress + " " : "") +
        (item.Address ? item.Address + " " : "") +
        (item.Start && item.End ? item.Start + " - " + item.End + " " : "") +
        "(" +
        item.type +
        ")";
    },
    filterDstAuto(query) {
      this.currentDestination.Type = null;

      if (query.trim().length === 0) {
        return null;
      }

      return this.objects.filter(function(source) {
        return (
          source.type.toLowerCase().includes(query.toLowerCase()) ||
          source.name.toLowerCase().includes(query.toLowerCase()) ||
          (source.Description &&
            source.Description.toLowerCase().includes(query.toLowerCase())) ||
          (source.IpAddress &&
            source.IpAddress.toLowerCase().includes(query.toLowerCase())) ||
          (source.Address &&
            source.Address.toLowerCase().includes(query.toLowerCase()))
        );
      });
    },
    selectDstAuto(item) {
      this.currentDestination.Destination = item.name;
      this.currentDestination.Type = item.type;
      this.currentDestination.DstType =
        item.name +
        " " +
        (item.IpAddress ? item.IpAddress + " " : "") +
        (item.Address ? item.Address + " " : "") +
        (item.Start && item.End ? item.Start + " - " + item.End + " " : "") +
        "(" +
        item.type +
        ")";
    },
    toggleStatus(isEdit) {
      var context = this;
      if (!isEdit) {
        context.configuration.status = !context.configuration.status;
      }
      context.configuration.isEdit = isEdit;

      if (context.configuration.status == true) {
        $("#configureProxyModal").modal("show");
        context.newConfiguration = JSON.parse(
          JSON.stringify(context.configuration)
        );
        context.newConfiguration.errors = context.initConfigurationErrors();
      } else {
        // save state
        // notification
        nethserver.notifications.success = context.$i18n.t(
          "proxy.configuration_updated_ok"
        );
        nethserver.notifications.error = context.$i18n.t(
          "proxy.configuration_updated_error"
        );

        // update values
        nethserver.exec(
          ["nethserver-squid/proxy/update"],
          {
            SafePorts: context.configuration.SafePorts.split(","),
            status: "disabled",
            PortBlock: context.configuration.PortBlock,
            ParentProxy: context.configuration.ParentProxy,
            GreenMode: context.configuration.GreenMode,
            BlueMode: context.configuration.BlueMode,
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
    toggleAdvancedMode() {
      this.newConfiguration.advanced = !this.newConfiguration.advanced;
      this.$forceUpdate();
    },
    resetConfiguration(isEdit) {
      if (!isEdit) {
        this.configuration.status = !this.configuration.status;
      }
    },
    getConfiguration() {
      var context = this;

      context.view.isLoaded = false;
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
          context.configuration = success.props;
          context.configuration.status =
            context.configuration.status == "enabled";
          context.configuration.PortBlock =
            context.configuration.PortBlock == "enabled";
          context.configuration.SafePorts = context.configuration.SafePorts.join(
            ","
          );
          context.view.isLoaded = true;

          setTimeout(function() {
            $("#source-tab-parent").click();
          }, 250);
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getBypasses() {
      var context = this;

      nethserver.exec(
        ["nethserver-squid/proxy/read"],
        {
          action: "bypass-list"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.sourceRows = success["sources"];
          context.destinationRows = success["destinations"];

          setTimeout(function() {
            $('[data-toggle="tooltip"]').tooltip();
          }, 500);

          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getObjects() {
      var context = this;

      nethserver.exec(
        ["nethserver-squid/proxy/read"],
        {
          action: "object-list"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.objects = success["objects"];
        },
        function(error) {
          console.error(error);
        }
      );
    },
    saveConfiguration() {
      var context = this;

      var configObj = {
        SafePorts:
          this.newConfiguration.SafePorts.length > 0
            ? this.newConfiguration.SafePorts.split(",")
            : [],
        status: this.newConfiguration.status ? "enabled" : "disabled",
        PortBlock: this.newConfiguration.PortBlock ? "enabled" : "disabled",
        PortRedirect: this.newConfiguration.PortRedirect,
        ParentProxy: this.newConfiguration.ParentProxy,
        GreenMode: this.newConfiguration.GreenMode,
        BlueMode: this.newConfiguration.BlueMode,
        action: "configuration"
      };

      context.newConfiguration.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-squid/proxy/validate"],
        configObj,
        null,
        function(success) {
          context.newConfiguration.isLoading = false;
          $("#configureProxyModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "proxy.configuration_updated_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "proxy.configuration_updated_error"
          );

          // update values
          nethserver.exec(
            ["nethserver-squid/proxy/update"],
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

          context.newConfiguration.errors = context.initConfigurationErrors();
          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.newConfiguration.errors[attr.parameter].hasError = true;
              context.newConfiguration.errors[attr.parameter].message =
                attr.error;
            }

            context.$forceUpdate();
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    openCreateSource() {
      this.currentSource = this.initSource();
      $("#createSourceModal").modal("show");
    },
    openCreateDestination() {
      this.currentDestination = this.initDestination();
      $("#createDestinationModal").modal("show");
    },
    openEditSource(source) {
      this.currentSource = JSON.parse(JSON.stringify(source));
      this.currentSource.Source = this.currentSource.props.Host.name;
      this.currentSource.Type = this.currentSource.props.Host.type;
      this.currentSource.SrcType = this.currentSource.props.Host.type;
      this.currentSource.Description = this.currentSource.props.Description;
      this.currentSource.errors = this.initSourceErrors();
      this.currentSource.isEdit = true;
      $("#createSourceModal").modal("show");
    },
    openEditDestination(dest) {
      this.currentDestination = JSON.parse(JSON.stringify(dest));
      this.currentDestination.Destination =
        (this.currentDestination.props.Host &&
          this.currentDestination.props.Host.name) ||
        this.currentDestination.props.Domains.join(",");
      this.currentDestination.Type =
        (this.currentDestination.props.Host &&
          this.currentDestination.props.Host.type) ||
        null;
      this.currentDestination.DstType =
        (this.currentDestination.props.Host &&
          this.currentDestination.props.Host.type) ||
        null;
      this.currentDestination.Description = this.currentDestination.props.Description;
      this.currentDestination.errors = this.initDestinationErrors();
      this.currentDestination.isEdit = true;
      $("#createDestinationModal").modal("show");
    },
    openDeleteSource(source) {
      this.toDeleteSource = JSON.parse(JSON.stringify(source));
      $("#deleteSourceModal").modal("show");
    },
    openDeleteDestination(dest) {
      this.toDeleteDestination = JSON.parse(JSON.stringify(dest));
      $("#deleteDestinationModal").modal("show");
    },
    saveSource(source) {
      var context = this;

      var sourceObj = {
        action: source.isEdit ? "update-bypass" : "create-bypass",
        Host: {
          type: source.Type,
          name: source.Source
        },
        Description: source.Description,
        name: source.isEdit ? source.name : null,
        type: "bypass-src"
      };

      context.currentSource.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-squid/proxy/validate"],
        sourceObj,
        null,
        function(success) {
          context.currentSource.isLoading = false;
          $("#createSourceModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "proxy.source_" +
              (context.currentSource.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "proxy.source_" +
              (context.currentSource.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (source.isEdit) {
            nethserver.exec(
              ["nethserver-squid/proxy/update"],
              sourceObj,
              function(stream) {
                console.info("source", stream);
              },
              function(success) {
                // get all
                context.getBypasses();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-squid/proxy/create"],
              sourceObj,
              function(stream) {
                console.info("source", stream);
              },
              function(success) {
                // get all
                context.getBypasses();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.currentSource.isLoading = false;
          context.currentSource.errors = context.initSourceErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              if (attr.parameter == "Host") {
                attr.parameter = "Source";
              }
              context.currentSource.errors[attr.parameter].hasError = true;
              context.currentSource.errors[attr.parameter].message = attr.error;
              context.$forceUpdate();
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    saveDestination(destination) {
      var context = this;

      var destinationObj = {
        action: destination.isEdit ? "update-bypass" : "create-bypass",
        Description: destination.Description,
        name: destination.isEdit ? destination.name : null,
        type: "bypass-dst"
      };

      if (destination.Type) {
        destinationObj["Host"] = {
          type: destination.Type,
          name: destination.Destination
        };
      } else {
        destinationObj["Domains"] = destination.Destination.split(",").map(
          function(d) {
            return d.trim();
          }
        );
      }

      context.currentDestination.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-squid/proxy/validate"],
        destinationObj,
        null,
        function(success) {
          context.currentDestination.isLoading = false;
          $("#createDestinationModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "proxy.destination_" +
              (context.currentDestination.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "proxy.destination_" +
              (context.currentDestination.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (destination.isEdit) {
            nethserver.exec(
              ["nethserver-squid/proxy/update"],
              destinationObj,
              function(stream) {
                console.info("destination", stream);
              },
              function(success) {
                // get all
                context.getBypasses();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-squid/proxy/create"],
              destinationObj,
              function(stream) {
                console.info("destination", stream);
              },
              function(success) {
                // get all
                context.getBypasses();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.currentDestination.isLoading = false;
          context.currentDestination.errors = context.initDestinationErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              if (attr.parameter == "Host") {
                attr.parameter = "Destination";
              }
              context.currentDestination.errors[attr.parameter].hasError = true;
              context.currentDestination.errors[attr.parameter].message =
                attr.error;
              context.$forceUpdate();
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    deleteSource(source) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "proxy.source_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "proxy.source_deleted_error"
      );

      $("#deleteSourceModal").modal("hide");
      nethserver.exec(
        ["nethserver-squid/proxy/delete"],
        {
          name: source.name
        },
        function(stream) {
          console.info("source", stream);
        },
        function(success) {
          // get all
          context.getBypasses();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    deleteDestination(destination) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "proxy.destination_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "proxy.destination_deleted_error"
      );

      $("#deleteDestinationModal").modal("hide");
      nethserver.exec(
        ["nethserver-squid/proxy/delete"],
        {
          name: destination.name
        },
        function(stream) {
          console.info("destination", stream);
        },
        function(success) {
          // get all
          context.getBypasses();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    toggleSourceStatus(source) {
      var context = this;
      // notification
      nethserver.notifications.success = context.$i18n.t(
        "proxy.source_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "proxy.source_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-squid/proxy/update"],
        {
          action:
            source.props.status == "enabled"
              ? "disable-bypass"
              : "enable-bypass",
          name: source.name
        },
        function(stream) {
          console.info("update-status", stream);
        },
        function(success) {
          // get all
          context.getBypasses();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    toggleDestinationStatus(destination) {
      var context = this;
      // notification
      nethserver.notifications.success = context.$i18n.t(
        "proxy.destination_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "proxy.destination_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-squid/proxy/update"],
        {
          action:
            destination.props.status == "enabled"
              ? "disable-bypass"
              : "enable-bypass",
          name: destination.name
        },
        function(stream) {
          console.info("update-status", stream);
        },
        function(success) {
          // get all
          context.getBypasses();
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
</style>
