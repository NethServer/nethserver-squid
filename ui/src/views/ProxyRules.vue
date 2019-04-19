<template>
  <div>
    <h2>{{ $t('proxy_rules.title') }}</h2>
    <doc-info
      :placement="'top'"
      :title="$t('docs.proxy_rules')"
      :chapter="'web_proxy'"
      :section="'priority-and-divert-rules'"
      :inline="false"
    ></doc-info>

    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-if="rules.length == 0 && view.isLoaded" class="blank-slate-pf" id>
      <div class="blank-slate-pf-icon">
        <span class="pficon pficon-key"></span>
      </div>
      <h1>{{$t('proxy_rules.no_rules_find')}}</h1>
      <p>{{$t('proxy_rules.no_rules_find_desc')}}.</p>
      <div class="blank-slate-pf-main-action">
        <button @click="openCreate()" class="btn btn-primary btn-lg">{{$t('proxy_rules.add_rule')}}</button>
      </div>
    </div>
    <div class="pf-container" v-if="rules.length > 0 && view.isLoaded">
      <h3>{{$t('actions')}}</h3>
      <button @click="openCreate()" class="btn btn-primary btn-lg">{{$t('proxy_rules.add_rule')}}</button>

      <h3>{{$t('list')}}</h3>
      <ul class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10">
        <li
          :class="[r.status == 'disabled' ? 'gray-list' : mapList(r.Action), 'list-group-item', r.status == 'disabled' ? 'gray' : '']"
          v-for="(r,k) in rules"
          v-bind:key="k"
        >
          <div class="drag-size">
            <span class="gray mg-right-5">{{r.Action.name | uppercase}}</span>
          </div>
          <div class="list-view-pf-actions">
            <button
              @click="r.status == 'disabled' ? toggleStatus(r) : openEdit(r)"
              :class="['btn btn-default', r.status == 'disabled' ? 'btn-primary' : '']"
            >
              <span
                :class="['fa', r.status == 'disabled' ? 'fa-check' : 'fa-pencil', 'span-right-margin']"
              ></span>
              {{r.status == 'disabled' ? $t('enable') : $t('edit')}}
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
                  <a @click="toggleStatus(r)">
                    <span
                      :class="['fa', r.status == 'enabled' ? 'fa-lock' : 'fa-check', 'span-right-margin']"
                    ></span>
                    {{r.status == 'enabled' ? $t('disable') : $t('enable')}}
                  </a>
                </li>
                <li role="presentation" class="divider"></li>
                <li>
                  <a @click="openDelete(r)">
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
                data-toggle="tooltip"
                data-placement="top"
                data-html="true"
                :title="mapTitleAction(r)"
                :class="[mapIcon(r.Action, r.status), 'list-view-pf-icon-sm']"
              ></span>
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description rules-src-dst">
                <div class="list-group-item-heading">
                  <span
                    data-toggle="tooltip"
                    data-placement="top"
                    data-html="true"
                    :title="mapTitleSrc(r)"
                    class="handle-overflow"
                  >
                    <span :class="mapObjectIcon(r.Src, r.status)"></span>
                    <span
                      :class="[r.status == 'disabled' ? 'gray' : r.Src.name.toLowerCase(),'mg-left-5']"
                    >{{r.Src.type == 'role' || r.Src.type == 'any' ? (r.Src.name.toUpperCase()): r.Src.name}}</span>
                  </span>
                </div>
                <div class="list-group-item-text">
                  <span :class="[mapArrow(r.Action), 'mg-right-10 big-icon']"></span>
                  <span class="handle-overflow">
                    <span class="fa fa-tags"></span>
                    <span
                      :class="[r.status == 'disabled' ? 'gray' : '','mg-left-5']"
                    >{{r.Dst.join(', ')}}</span>
                  </span>
                </div>
              </div>
              <div class="list-view-pf-additional-info rules-info">
                <div class="list-view-pf-additional-info-item">{{r.Description}}</div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- MODALS -->
    <div class="modal" id="createRuleModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4
              class="modal-title"
            >{{currentRule.isEdit ? $t('proxy_rules.edit_rule') + ' '+ currentRule.name : $t('proxy_rules.add_rule')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="saveRule(currentRule)">
            <div class="modal-body">
              <div :class="['form-group', currentRule.errors.Src.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('proxy_rules.source')}}</label>
                <div class="col-sm-9">
                  <suggestions
                    v-model="currentRule.Src"
                    :options="autoOptions"
                    :onInputChange="filterSrcAuto"
                    :onItemSelected="selectSrcAuto"
                    :required="!currentRule.isEdit"
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
                    v-if="currentRule.SrcType && currentRule.SrcType.length > 0"
                    class="help-block gray"
                  >{{currentRule.SrcType}}</span>
                  <span v-if="currentRule.errors.Src.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+currentRule.errors.Src.message)}}
                  </span>
                </div>
              </div>
              <div :class="['form-group', currentRule.errors.Dst.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('proxy_rules.destination')}}</label>
                <div class="col-sm-9">
                  <textarea v-model="currentRule.Dst" class="form-control min-textarea-height"></textarea>
                  <span v-if="currentRule.errors.Dst.hasError" class="help-block">
                    {{$t('validation.validation_failed')}}:
                    {{$t('validation.'+currentRule.errors.Dst.message)}}
                  </span>
                </div>
              </div>
              <div :class="['form-group', currentRule.errors.Action.hasError ? 'has-error' : '']">
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('proxy_rules.action')}}</label>
                <div class="col-sm-9">
                  <select type="text" v-model="currentRule.Action" class="form-control">
                    <option
                      v-for="(a,ak) in actions"
                      v-bind:key="ak"
                      :value="a"
                    >{{$t("proxy_rules." + a.type.toLowerCase())}} {{a.name}}</option>
                  </select>
                  <span
                    v-if="currentRule.errors.Action.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentRule.errors.Action.message)}}</span>
                </div>
              </div>
              <div
                :class="['form-group', currentRule.errors.Description.hasError ? 'has-error' : '']"
              >
                <label
                  class="col-sm-3 control-label"
                  for="textInput-modal-markup"
                >{{$t('proxy_rules.description')}}</label>
                <div class="col-sm-9">
                  <input type="text" v-model="currentRule.Description" class="form-control">
                  <span
                    v-if="currentRule.errors.Description.hasError"
                    class="help-block"
                  >{{$t('validation.validation_failed')}}: {{$t('validation.'+currentRule.errors.Description.message)}}</span>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <div v-if="currentRule.isLoading" class="spinner spinner-sm form-spinner-loader"></div>
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button
                class="btn btn-primary"
                type="submit"
              >{{currentRule.isEdit ? $t('edit') : $t('save')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="modal" id="deleteRuleModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('proxy_rules.delete_rule')}} {{toDeleteRule.Src.name}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="deleteRule(toDeleteRule)">
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
  name: "ProxyRules",
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    next();
  },
  mounted() {
    this.getRules();
    this.getSources();
    this.getActions();
  },
  data() {
    return {
      view: {
        isLoaded: false
      },
      autoOptions: {
        inputClass: "form-control"
      },
      rules: [],
      source: [],
      actions: [],
      currentRule: this.initRule(),
      toDeleteRule: {
        Src: {
          name: "",
          type: ""
        },
        Action: {
          name: "",
          type: ""
        }
      }
    };
  },
  methods: {
    filterSrcAuto(query) {
      this.currentRule.Type = null;

      if (query.trim().length === 0) {
        return null;
      }

      return this.sources.filter(function(source) {
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
    selectSrcAuto(item) {
      this.currentRule.Src = item.name;
      this.currentRule.Type = item.type;
      this.currentRule.SrcType =
        item.name +
        " " +
        (item.IpAddress ? item.IpAddress + " " : "") +
        (item.Address ? item.Address + " " : "") +
        (item.Start && item.End ? item.Start + " - " + item.End + " " : "") +
        "(" +
        item.type +
        ")";
    },
    initRule() {
      return {
        status: "enabled",
        Action: {
          name: "",
          type: ""
        },
        name: null,
        Dst: [],
        Src: "",
        SrcType: "",
        Type: "",
        type: "rule",
        Description: "",
        errors: this.initErrors()
      };
    },
    initErrors() {
      return {
        Action: {
          hasError: false,
          message: ""
        },
        Src: {
          hasError: false,
          message: ""
        },
        Dst: {
          hasError: false,
          message: ""
        },
        Description: {
          hasError: false,
          message: ""
        }
      };
    },
    getRules() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-squid/rules/read"],
        {
          action: "rule-list"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.rules = success["rules"];
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
    getSources() {
      var context = this;

      nethserver.exec(
        ["nethserver-squid/rules/read"],
        {
          action: "source-list"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.sources = success["sources"];
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getActions() {
      var context = this;

      nethserver.exec(
        ["nethserver-squid/rules/read"],
        {
          action: "action-list"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.actions = success["actions"];
        },
        function(error) {
          console.error(error);
        }
      );
    },
    openCreate() {
      this.currentRule = this.initRule();
      $("#createRuleModal").modal("show");
    },
    openEdit(rule) {
      this.currentRule = JSON.parse(JSON.stringify(rule));
      this.currentRule.Src = this.currentRule.Src.name;
      this.currentRule.Type = this.currentRule.Src.type;
      this.currentRule.SrcType = this.currentRule.Src.type;
      this.currentRule.Dst = this.currentRule.Dst.join("\n");
      this.currentRule.errors = this.initErrors();
      this.currentRule.isEdit = true;
      $("#createRuleModal").modal("show");
    },
    openDelete(rule) {
      this.toDeleteRule = JSON.parse(JSON.stringify(rule));
      $("#deleteRuleModal").modal("show");
    },
    saveRule(rule) {
      var context = this;

      var ruleObj = {
        action: rule.isEdit ? "update" : "create",
        status: rule.status,
        Action: {
          type: rule.Action.type,
          name: rule.Action.name
        },
        Src: {
          name: rule.Src,
          type: rule.Type
        },
        Dst: rule.Dst.split("\n"),
        Description: rule.Description,
        name: rule.name
      };

      context.currentRule.isLoading = true;
      context.$forceUpdate();
      nethserver.exec(
        ["nethserver-squid/rules/validate"],
        ruleObj,
        null,
        function(success) {
          context.currentRule.isLoading = false;
          $("#createRuleModal").modal("hide");

          // notification
          nethserver.notifications.success = context.$i18n.t(
            "proxy_rules.rule_" +
              (context.currentRule.isEdit ? "updated" : "created") +
              "_ok"
          );
          nethserver.notifications.error = context.$i18n.t(
            "proxy_rules.rule_" +
              (context.currentRule.isEdit ? "updated" : "created") +
              "_error"
          );

          // update values
          if (rule.isEdit) {
            nethserver.exec(
              ["nethserver-squid/rules/update"],
              ruleObj,
              function(stream) {
                console.info("rule", stream);
              },
              function(success) {
                // get all
                context.getRules();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          } else {
            nethserver.exec(
              ["nethserver-squid/rules/create"],
              ruleObj,
              function(stream) {
                console.info("rule", stream);
              },
              function(success) {
                // get all
                context.getRules();
              },
              function(error, data) {
                console.error(error, data);
              }
            );
          }
        },
        function(error, data) {
          var errorData = {};
          context.currentRule.isLoading = false;
          context.currentRule.errors = context.initErrors();

          try {
            errorData = JSON.parse(data);
            for (var e in errorData.attributes) {
              var attr = errorData.attributes[e];
              context.currentRule.errors[attr.parameter].hasError = true;
              context.currentRule.errors[attr.parameter].message = attr.error;
              context.$forceUpdate();
            }
          } catch (e) {
            console.error(e);
          }
        }
      );
    },
    toggleStatus(rule) {
      var context = this;
      // notification
      nethserver.notifications.success = context.$i18n.t(
        "proxy_rules.rule_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "proxy_rules.rule_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-squid/rules/update"],
        {
          action: rule.status == "enabled" ? "disable" : "enable",
          name: rule.name
        },
        function(stream) {
          console.info("update-status", stream);
        },
        function(success) {
          // get all
          context.getRules();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    deleteRule(rule) {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "proxy_rules.rule_deleted_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "proxy_rules.rule_deleted_error"
      );

      $("#deleteRuleModal").modal("hide");
      nethserver.exec(
        ["nethserver-squid/rules/delete"],
        {
          name: rule.name
        },
        function(stream) {
          console.info("rule", stream);
        },
        function(success) {
          // get all
          context.getRules();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    mapTitleAction(rule) {
      var html =
        "<b>" +
        this.$i18n.t("proxy_rules." + rule.Action.type.toLowerCase()) +
        "</b><br>";

      html +=
        '<div class="type-info"><span class="fa fa-asterisk mg-right-5 mg-top-5 detail-icon"></span>' +
        "<span>" +
        rule.Action.name.toUpperCase() +
        "</span></div>";

      return html;
    },
    mapTitleSrc(rule) {
      var html =
        "<b>" +
        (rule.Src.type == "role" || rule.Src.type == "any"
          ? rule.Src.name.toUpperCase()
          : rule.Src.name) +
        "</b>";

      // host zone
      if (rule.Src.zone && rule.Src.zone.length > 0) {
        html +=
          ' | <span class="' +
          this.mapZoneIcon(rule.Src.zone) +
          ' mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.zone.toUpperCase() +
          "</span>";
      }

      html += "<br>";

      // host
      if (rule.Src.IpAddress) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.IpAddress +
          "</span></div>";
      }

      // cidr
      if (rule.Src.Address) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.Address +
          "</span></div>";
      }

      // ip range
      if (rule.Src.Start || rule.Src.End) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.Start +
          " - " +
          rule.Src.End +
          "</span></div>";
      }

      // zone
      if (rule.Src.Network || rule.Src.Interface) {
        html +=
          '<div><span class="pficon pficon-screen mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.Network +
          "</span></div>";

        html +=
          '<div><span class="pficon pficon-plugged mg-right-5 mg-top-5 detail-icon"></span>' +
          "<span>" +
          rule.Src.Interface +
          "</span></div>";
      }

      // role
      if (rule.Src.type == "role") {
        if (rule.Src.Interfaces && rule.Src.Interfaces.length > 0) {
          html +=
            '<div><span class="pficon pficon-plugged mg-right-5 mg-top-5 detail-icon"></span>' +
            "<span>" +
            rule.Src.Interfaces.join(", ") +
            "</span></div>";
        }
      }

      html +=
        '<span class="type-info"><b>' +
        this.$i18n.t("proxy_rules." + rule.Src.type) +
        "</b></span>";

      return html;
    },
    mapZoneIcon(zone) {
      switch (zone) {
        case "red":
        case "green":
        case "orange":
        case "blue":
          return "square-" + zone.toUpperCase();
          break;
        default:
          return "square-OTHER";
          break;
      }
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
        case "any":
          return "fa fa-globe";
          break;
      }
    },
    mapArrow(action) {
      switch (action.type) {
        case "class":
          return "gray fa fa-arrow-right";
          break;
        case "provider":
          return "gray fa fa-arrow-right";
          break;
        case "force":
          return "gray fa fa-arrow-right";
          break;
      }
    },
    mapList(action) {
      switch (action.type) {
        case "class":
          return "blue-list";
          break;
        case "provider":
          return "blue-list";
          break;
        case "force":
          return "blue-list";
          break;
      }
    },
    mapIcon(action, status) {
      switch (action.type) {
        case "class":
          return (
            "fa fa-crosshairs " +
            (status == "disabled" ? "gray border-gray" : "blue border-blue")
          );
          break;
        case "provider":
          return (
            "fa fa-share " +
            (status == "disabled" ? "gray border-gray" : "blue border-blue")
          );
          break;
        case "force":
          return (
            "fa fa-link " +
            (status == "disabled" ? "gray border-gray" : "blue border-blue")
          );
          break;
      }
    }
  }
};
</script>

<style>
.min-textarea-height {
  min-height: 100px;
}

.info-desc-local {
  min-width: 75px;
}

.small-icon {
  font-size: 12px !important;
  height: 25px !important;
  width: 25px !important;
}

.small-icon::before {
  line-height: 20px !important;
}

.flex-50 {
  flex: 1 0 calc(50% - 20px) !important;
}

.adjust-checkbox-nested {
  margin: 0px 0 0 !important;
}

.square-GREEN {
  background: #3f9c35;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-RED {
  background: #cc0000;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-ORANGE {
  background: #ec7a08;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-BLUE {
  background: #0088ce;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-VPN {
  background: black;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-IVPN {
  background: black;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.square-OTHER {
  background: #703fec;
  width: 15px;
  height: 15px;
  display: inline-block;
  margin-bottom: -2px;
  border-radius: 3px;
  margin-right: 3px;
}

.red {
  color: #cc0000;
}
.green {
  color: #3f9c35;
}
.gray {
  color: #72767b;
}
.orange {
  color: #ec7a08;
}
.blue {
  color: #0088ce;
}
.other {
  color: #703fec;
}
.vpn {
  color: black;
}
.ivpn {
  color: black;
}

.red-list {
  border-left: 3px solid #cc0000 !important;
}
.green-list {
  border-left: 3px solid #3f9c35 !important;
}
.gray-list {
  border-left: 3px solid #72767b !important;
}
.orange-list {
  border-left: 3px solid #ec7a08 !important;
}
.blue-list {
  border-left: 3px solid #0088ce !important;
}
.other-list {
  border-left: 3px solid #703fec !important;
}

.border-red {
  border: 2px solid #cc0000 !important;
}
.border-green {
  border: 2px solid #3f9c35 !important;
}
.border-gray {
  border: 2px solid #72767b !important;
}
.border-orange {
  border: 2px solid #ec7a08 !important;
}
.border-blue {
  border: 2px solid #0088ce !important;
}
.border-other {
  border: 2px solid #703fec !important;
}

.list-group-item-heading {
  flex: auto !important;
  width: calc(40% - 20px) !important;
  font-size: 16px !important;
}
.list-group-item-text {
  width: calc(60% - 20px) !important;
  font-size: 16px !important;
  font-weight: 600;
}

.sortable-chosen {
  border: 1px solid rgb(162, 212, 237) !important;
  z-index: 9;
}
.sortable-ghost {
  border: 2px dashed #b4b5b8 !important;
  z-index: 9;
}

.line-icon::before {
  vertical-align: sub;
}

.handle-overflow {
  text-overflow: ellipsis;
  overflow: hidden;
}

.drag-here {
  cursor: pointer;
}

.detail-icon {
  font-size: 12px !important;
}
.big-icon {
  font-size: 16px !important;
}

.log-icon {
  position: absolute;
  bottom: 9px;
  margin-left: -40px;
  font-size: 12px !important;
}

.type-info {
  margin-top: 10px;
  display: inline-block;
}

.drag-size {
  min-width: 35px !important;
}

.tooltip-inner {
  width: auto;
}

.rules-src-dst {
  width: 90% !important;
}
.rules-info {
  width: 10% !important;
}
@media (max-width: 992px) {
  .rules-info {
    width: 100% !important;
  }
}
.expand-text {
  margin-right: 5px;
  vertical-align: top;
}

.span-right-margin {
  margin-right: 4px;
}

.no-pd-bottom {
  padding-bottom: 0px !important;
}

.no-mg-top {
  margin-top: 0px !important;
}
.no-mg-bottom {
  margin-bottom: 0px !important;
}
.no-mg-left {
  margin-left: 0px !important;
}
.no-mg-right {
  margin-right: 0px !important;
}

.mg-top-5 {
  margin-top: 5px !important;
}
.mg-bottom-5 {
  margin-bottom: 5px !important;
}
.mg-left-5 {
  margin-left: 5px !important;
}
.mg-right-5 {
  margin-right: 5px !important;
}

.mg-top-10 {
  margin-top: 10px !important;
}
.mg-bottom-10 {
  margin-bottom: 10px !important;
}
.mg-right-10 {
  margin-right: 10px !important;
}
.mg-left-10 {
  margin-left: 10px !important;
}
.mg-left-20 {
  margin-left: 20px !important;
}

.mg-top-20 {
  margin-top: 20px !important;
}
.mg-top-35 {
  margin-top: 35px !important;
}
</style>
