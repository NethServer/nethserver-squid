<template>
  <div>
    <h2>{{ $t('dashboard.title') }}</h2>

    <div class="row">
      <h3>{{ $t('dashboard.proxy_stats') }}</h3>

      <div class="col-sm-4">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">
              <span>{{ $t('dashboard.proxy_enabled') }}</span>
              <span
                :class="['mg-left-10', proxy.status == 'enabled' ? 'pficon pficon-ok' : 'pficon pficon-error-circle-o']"
              ></span>
            </h3>
          </div>
          <div class="panel-body status-body">
            <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
            <p v-if="view.isLoaded && proxy.status == 'enabled'">
              <strong
                class="green col-xs-7 col-sm-7 col-md-7 col-lg-7"
              >{{$t('dashboard.green_trusted')}}:</strong>
              <code>{{$t('dashboard.'+proxy.GreenMode)}}</code>
            </p>
            <p v-if="view.isLoaded && proxy.status == 'enabled'">
              <strong
                class="blue col-xs-7 col-sm-7 col-md-7 col-lg-7"
              >{{$t('dashboard.blue_zones')}}:</strong>
              <code>{{$t('dashboard.'+proxy.BlueMode)}}</code>
            </p>
            <div
              v-if="view.isLoaded && proxy.status == 'disabled'"
              class="alert alert-info alert-dismissable no-mg-bottom"
            >
              <span class="pficon pficon-info"></span>
              <strong>{{$t('info')}}.</strong>
              {{$t('dashboard.proxy_disabled')}}.
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div v-if="!view.isChartLoaded" class="spinner spinner-lg view-spinner"></div>
        <div
          v-if="view.isChartLoaded && view.invalidChartsData"
          class="alert alert-warning alert-dismissable col-sm-12"
        >
          <span class="pficon pficon-warning-triangle-o"></span>
          <strong>{{$t('warning')}}!</strong>
          {{$t('charts_not_updated')}}.
        </div>
        <div v-show="view.isChartLoaded && !view.invalidChartsData" class="row">
          <div class="col-sm-12">
            <h4 class="col-sm-12">
              <div id="chart-status-proxy-requests" class="legend"></div>
            </h4>
            <div id="chart-proxy-requests" class="col-sm-12"></div>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div v-if="!view.isChartLoaded" class="spinner spinner-lg view-spinner"></div>
        <div
          v-if="view.isChartLoaded && view.invalidChartsData"
          class="alert alert-warning alert-dismissable col-sm-12"
        >
          <span class="pficon pficon-warning-triangle-o"></span>
          <strong>{{$t('warning')}}!</strong>
          {{$t('charts_not_updated')}}.
        </div>
        <div v-show="view.isChartLoaded && !view.invalidChartsData" class="row">
          <div class="col-sm-12">
            <h4 class="col-sm-12">
              <div id="chart-status-proxy-net" class="legend"></div>
            </h4>
            <div id="chart-proxy-net" class="col-sm-12"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <h3>{{ $t('dashboard.proxy_counter') }}</h3>
      <div class="row row-stat">
        <div class="row-inline-block">
          <div class="stats-container col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <span
              class="card-pf-utilization-card-details-count stats-count"
            >{{proxy["source-bypass"]}}</span>
            <span class="card-pf-utilization-card-details-description stats-description">
              <span
                class="card-pf-utilization-card-details-line-2 stats-text"
              >{{$t('dashboard.source_bypass')}}</span>
            </span>
          </div>
          <div class="stats-container col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <span
              class="card-pf-utilization-card-details-count stats-count"
            >{{proxy["destination-bypass"]}}</span>
            <span class="card-pf-utilization-card-details-description stats-description">
              <span
                class="card-pf-utilization-card-details-line-2 stats-text"
              >{{$t('dashboard.destination_bypass')}}</span>
            </span>
          </div>
          <div class="stats-container col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <span class="card-pf-utilization-card-details-count stats-count">{{proxy["rules"]}}</span>
            <span class="card-pf-utilization-card-details-description stats-description">
              <span
                class="card-pf-utilization-card-details-line-2 stats-text"
              >{{$t('dashboard.rules')}}</span>
            </span>
          </div>
          <div class="stats-container col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <span class="card-pf-utilization-card-details-count stats-count">
              <span
                :class="[proxy.PortBlock == 'enabled' ? 'pficon pficon-ok' : 'pficon pficon-error-circle-o']"
              ></span>
            </span>
            <span class="card-pf-utilization-card-details-description stats-description">
              <span
                class="card-pf-utilization-card-details-line-2 stats-text"
              >{{$t('dashboard.port_block')}}</span>
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <h3>{{ $t('dashboard.proxy_clients_stats') }}</h3>
      <div class="row row-stat">
        <div class="row-inline-block">
          <div class="stats-container col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <span class="card-pf-utilization-card-details-count stats-count">
              {{proxy["client_bytes_out"] || 0 | byteFormat}}
              <span
                class="semi-bold"
              >{{$t('download')}}</span>
            </span>
            <span class="card-pf-utilization-card-details-count stats-count mg-left-20">
              {{proxy["client_bytes_in"] || 0 | byteFormat}}
              <span class="semi-bold">{{$t('upload')}}</span>
            </span>
            <span class="card-pf-utilization-card-details-description stats-description mg-left-10">
              <span
                class="card-pf-utilization-card-details-line-2 stats-text"
              >{{$t('dashboard.client_kbytes_sec')}}</span>
            </span>
          </div>
          <div class="stats-container col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <span
              class="card-pf-utilization-card-details-count stats-count"
            >{{proxy["client_requests"] || 0}}</span>
            <span class="card-pf-utilization-card-details-description stats-description">
              <span
                class="card-pf-utilization-card-details-line-2 stats-text"
              >{{$t('dashboard.client_requests')}}</span>
            </span>
          </div>
          <div class="stats-container col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <span
              class="card-pf-utilization-card-details-count stats-count"
            >{{proxy["client_hits"] || 0}}</span>
            <span class="card-pf-utilization-card-details-description stats-description">
              <span
                class="card-pf-utilization-card-details-line-2 stats-text"
              >{{$t('dashboard.client_hits')}}</span>
            </span>
          </div>
          <div class="stats-container col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <span
              class="card-pf-utilization-card-details-count stats-count"
            >{{proxy["client_errors"] || 0}}</span>
            <span class="card-pf-utilization-card-details-description stats-description">
              <span
                class="card-pf-utilization-card-details-line-2 stats-text"
              >{{$t('dashboard.client_errors')}}</span>
            </span>
          </div>
          <div class="stats-container col-xs-12 col-sm-6 col-md-3 col-lg-3">
            <span
              class="card-pf-utilization-card-details-count stats-count"
            >{{proxy["cpu_usage"] || 0}}%</span>
            <span class="card-pf-utilization-card-details-description stats-description">
              <span
                class="card-pf-utilization-card-details-line-2 stats-text"
              >{{$t('dashboard.cpu_usage')}}</span>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <h3>{{ $t('dashboard.today_proxy_traffic') }}</h3>
      <div v-if="!view.isProxyLoaded" class="spinner spinner-lg view-spinner"></div>
      <div v-if="view.isProxyLoaded" class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <h3>{{$t('dashboard.top_5_users')}}</h3>
        <ul class="list-group">
          <li v-for="(i,k) in proxyStats.top_users" v-bind:key="k" class="list-group-item">
            <strong>{{k+1}}.</strong>
            {{i.name}}
            <span class="gray">({{i.bytes | byteFormat}} | {{i.percentage}}%)</span>
          </li>
        </ul>
      </div>
      <div v-if="view.isProxyLoaded" class="col-xs-12 col-sm-6 col-md-4 col-lg-4 stats-container mg-left-20">
        <span class="card-pf-utilization-card-details-count stats-count">{{proxyStats.users || 0}}</span>
        <span class="card-pf-utilization-card-details-description stats-description">
          <span
            class="card-pf-utilization-card-details-line-2 stats-text"
          >{{$t('dashboard.total_users')}}</span>
        </span>
        <span
          class="card-pf-utilization-card-details-count stats-count mg-left-20"
        >{{proxyStats.traffic || 0 | byteFormat}}</span>
        <span class="card-pf-utilization-card-details-description stats-description">
          <span
            class="card-pf-utilization-card-details-line-2 stats-text"
          >{{$t('dashboard.total_traffic')}}</span>
        </span>
      </div>
    </div>

    <div class="row">
      <div class="divider"></div>
      <h3>{{ $t('dashboard.filter_stats') }}</h3>

      <div class="col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">
              <span>{{ $t('dashboard.filter_enabled') }}</span>
              <span
                :class="['mg-left-10', filter.status == 'enabled' ? 'pficon pficon-ok' : 'pficon pficon-error-circle-o']"
              ></span>
            </h3>
          </div>
          <div class="panel-body status-body">
            <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
            <p v-if="view.isLoaded && filter.status == 'enabled'">
              <strong class="col-xs-7 col-sm-7 col-md-7 col-lg-7">{{$t('dashboard.antivirus')}}:</strong>
              <span
                :class="[filter.antivirus == 'enabled' ? 'pficon pficon-ok' : 'pficon pficon-error-circle-o']"
              ></span>
            </p>
            <p v-if="view.isLoaded && filter.status == 'enabled'">
              <strong class="col-xs-7 col-sm-7 col-md-7 col-lg-7">{{$t('dashboard.profiles')}}:</strong>
              <span>{{filter.profiles}}</span>
            </p>
            <div
              v-if="view.isLoaded && filter.status == 'disabled'"
              class="alert alert-info alert-dismissable no-mg-bottom"
            >
              <span class="pficon pficon-info"></span>
              <strong>{{$t('info')}}.</strong>
              {{$t('dashboard.filter_disabled')}}.
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div v-if="!view.isFilterChartLoaded" class="spinner spinner-lg view-spinner"></div>
        <div v-show="view.isFilterChartLoaded" class="row">
          <div class="col-sm-12">
            <div
              v-if="filterStats.requests && filterStats.requests == 0 && filterStats.blocked && filterStats.blocked == 0"
              class="empty-piechart"
            >
              <span class="fa fa-pie-chart"></span>
              <div>{{ $t('dashboard.empty_piechart_label') }}</div>
            </div>
            <div v-else id="filter-pie-chart"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <h3>{{$t('dashboard.top_10_hosts')}}</h3>
        <ul class="list-group">
          <li v-for="(i,k) in filterStats.ip" v-bind:key="k" class="list-group-item">
            <strong>{{k+1}}.</strong>
            {{i.name}}
            <span class="gray">({{i.hits}})</span>
          </li>
        </ul>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <h3>{{$t('dashboard.top_10_sites')}}</h3>
        <ul class="list-group">
          <li v-for="(i,k) in filterStats.host" v-bind:key="k" class="list-group-item">
            <strong>{{k+1}}.</strong>
            {{i.name}}
            <span class="gray">({{i.hits}})</span>
          </li>
        </ul>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <h3>{{$t('dashboard.top_10_categories')}}</h3>
        <ul class="list-group">
          <li v-for="(i,k) in filterStats.category" v-bind:key="k" class="list-group-item">
            <strong>{{k+1}}.</strong>
            {{i.name}}
            <span class="gray">({{i.hits}})</span>
          </li>
        </ul>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <h3>{{$t('dashboard.top_10_profiles')}}</h3>
        <ul class="list-group">
          <li v-for="(i,k) in filterStats.profile" v-bind:key="k" class="list-group-item">
            <strong>{{k+1}}.</strong>
            {{i.name}}
            <span class="gray">({{i.hits}})</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import Dygraph from "dygraphs";
import generatePieChart from "@/piechart";

export default {
  name: "Dashboard",
  beforeRouteLeave(to, from, next) {
    $(".modal").modal("hide");
    clearInterval(this.pollingIntervalIdChart);
    next();
  },
  mounted() {
    this.getStats();
    this.getProxyStats();
    this.initProxyCharts();
    this.initFilterCharts();
  },
  data() {
    return {
      view: {
        isLoaded: false,
        isChartLoaded: false,
        isFilterChartLoaded: false,
        isProxyLoaded: false,
        invalidChartsData: false
      },
      proxy: {
        status: "disabled",
        GreenMode: "manual",
        BlueMode: "transparent"
      },
      filter: {
        status: "disabled",
        antivirus: "disabled",
        profiles: 0
      },
      filterStats: {
        ip: [],
        host: [],
        category: []
      },
      proxyStats: {
        traffic: 0,
        top_users: [],
        users: 0
      },
      charts: {
        request: null,
        net: null
      },
      pollingIntervalIdChart: 0
    };
  },
  methods: {
    getStats() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-squid/dashboard/read"],
        {
          action: "status"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.proxy = success["proxy"];
          context.filter = success["filter"];

          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getProxyStats() {
      var context = this;

      context.view.isProxyLoaded = false;
      nethserver.exec(
        ["nethserver-squid/dashboard/read"],
        {
          action: "proxy-stats"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.proxyStats = success;

          context.view.isProxyLoaded = true;
        },
        function(error) {
          console.error(error);
          context.view.isProxyLoaded = true;
        }
      );
    },
    initFilterCharts() {
      var context = this;

      context.view.isFilterChartLoaded = false;
      nethserver.exec(
        ["nethserver-squid/dashboard/read"],
        {
          action: "filter-stats"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }

          context.filterStats = success;

          var $ = window.jQuery;
          $('[data-toggle="tooltip"]').tooltip();
          if (!context.filterPieChart) {
            context.filterPieChart = generatePieChart("#filter-pie-chart", {
              names: {
                requests: context.$t("dashboard.filter_requests"),
                blocked: context.$t("dashboard.filter_blocked")
              },
              colors: {
                blocked: $.pfPaletteColors.red,
                requests: $.pfPaletteColors.green
              },
              columns: []
            });
          }
          context.filterPieChart.load({
            columns: [
              ["requests", context.filterStats.requests || 0],
              ["blocked", context.filterStats.blocked || 0]
            ]
          });

          context.view.isFilterChartLoaded = true;
        },
        function(error) {
          console.error(error);
          context.view.isFilterChartLoaded = true;
        }
      );
    },
    initProxyCharts() {
      var context = this;

      context.view.isChartLoaded = false;
      nethserver.exec(
        ["nethserver-squid/dashboard/read"],
        {
          action: "proxy-charts",
          time: 900
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }

          if (success.requests.data.length > 0 && success.net.data.length > 0) {
            context.view.invalidChartsData = false;

            for (var t in success.requests.data) {
              success.requests.data[t][0] = new Date(
                success.requests.data[t][0] * 1000
              );
            }
            for (var t in success.net.data) {
              success.net.data[t][0] = new Date(success.net.data[t][0] * 1000);
            }

            context.charts["requests"] = new Dygraph(
              document.getElementById("chart-proxy-requests"),
              success.requests.data.reverse(),
              {
                fillGraph: true,
                stackedGraph: true,
                labels: success.requests.labels,
                height: 150,
                strokeWidth: 1,
                strokeBorderWidth: 1,
                ylabel: context.$i18n.t("dashboard.requests"),
                axisLineColor: "white",
                labelsDiv: document.getElementById(
                  "chart-status-proxy-requests"
                ),
                labelsSeparateLines: true,
                legend: "always",
                axes: {
                  y: {
                    axisLabelFormatter: function(y) {
                      return Math.ceil(y);
                    }
                  }
                }
              }
            );
            context.charts["requests"].initialData = success.net.data;

            context.charts["net"] = new Dygraph(
              document.getElementById("chart-proxy-net"),
              success.net.data.reverse(),
              {
                fillGraph: true,
                stackedGraph: true,
                labels: success.net.labels,
                height: 150,
                strokeWidth: 1,
                strokeBorderWidth: 1,
                ylabel: context.$i18n.t("dashboard.net"),
                axisLineColor: "white",
                labelsDiv: document.getElementById("chart-status-proxy-net"),
                labelsSeparateLines: true,
                legend: "always",
                axes: {
                  y: {
                    axisLabelFormatter: function(y) {
                      return Math.ceil(y);
                    }
                  }
                }
              }
            );
            context.charts["net"].initialData = success.net.data;

            context.view.isChartLoaded = true;

            setTimeout(function() {
              context.charts["requests"].resize();
              context.charts["net"].resize();
            }, 100);

            // start polling
            if (context.pollingIntervalIdChart == 0) {
              context.pollingIntervalIdChart = setInterval(function() {
                context.updateProxyCharts(900);
              }, 1000);
            }
          } else {
            context.view.invalidChartsData = true;
            context.view.isChartLoaded = true;
            context.$forceUpdate();
          }
        },
        function(error) {
          console.error(error);
          context.view.isChartLoaded = true;
        }
      );
    },
    updateProxyCharts(time) {
      var context = this;
      nethserver.exec(
        ["nethserver-squid/dashboard/read"],
        {
          action: "proxy-charts",
          time: time
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          if (success.requests.data.length > 0 && success.net.data.length > 0) {
            context.view.invalidChartsData = false;

            for (var t in success.requests.data) {
              success.requests.data[t][0] = new Date(
                success.requests.data[t][0] * 1000
              );
            }
            for (var t in success.net.data) {
              success.net.data[t][0] = new Date(success.net.data[t][0] * 1000);
            }

            context.charts["requests"].updateOptions({
              file: success.requests.data.reverse()
            });
            context.charts["net"].updateOptions({
              file: success.net.data.reverse()
            });
          } else {
            context.view.invalidChartsData = true;
            context.$forceUpdate();
          }
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
.status-body {
  font-size: 14px;
}

.row {
  padding-left: 20px;
  padding-right: 20px;
}

.semi-bold {
  font-weight: 500;
}

.empty-piechart {
  margin-top: 2em;
  text-align: center;
  color: #9c9c9c;
}

.empty-piechart .fa {
  font-size: 92px;
  color: #bbbbbb;
}
</style>
