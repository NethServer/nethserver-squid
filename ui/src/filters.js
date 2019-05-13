import Vue from "vue";

var Filters = {
  byteFormat: function(size) {
    var result;

    switch (true) {
      case size === null || size === "" || isNaN(size):
        result = "-";
        break;

      case size >= 0 && size < 1024:
        result = size + " B";
        break;

      case size >= 1024 && size < Math.pow(1024, 2):
        result = Math.round((size / 1024) * 100) / 100 + " K";
        break;

      case size >= Math.pow(1024, 2) && size < Math.pow(1024, 3):
        result = Math.round((size / Math.pow(1024, 2)) * 100) / 100 + " M";
        break;

      case size >= Math.pow(1024, 3) && size < Math.pow(1024, 4):
        result = Math.round((size / Math.pow(1024, 3)) * 100) / 100 + " G";
        break;

      default:
        result = Math.round((size / Math.pow(1024, 4)) * 100) / 100 + " T";
    }

    return result;
  },
  secondsInHour: function(value) {
    if (!value || value.length == 0) {
      return "-";
    }

    var ret = "";
    let hours = parseInt(Math.floor(value / 3600));
    let minutes = parseInt(Math.floor((value - hours * 3600) / 60));
    let seconds = parseInt((value - (hours * 3600 + minutes * 60)) % 60);

    let dHours = hours > 9 ? hours : "0" + hours;
    let dMins = minutes > 9 ? minutes : "0" + minutes;
    let dSecs = seconds > 9 ? seconds : "0" + seconds;

    ret = dSecs + "s";
    if (minutes) {
      ret = dMins + "m " + ret;
    }
    if (hours) {
      ret = dHours + "h " + ret;
    }

    return ret;
  },
  dateFormat: function(value) {
    var moment = require("moment");
    if (+new Date(value) > 0) {
      var converted = isNaN(value)
        ? String(value)
        : String(value).length == 10
        ? value * 1000
        : value;
      return moment(converted).format("DD MMMM YYYY, HH:mm");
    } else if (value == -1) {
      return ns._i18n.t("never_expires");
    } else return "-";
  },
  capitalize: function(value) {
    return (
      value &&
      value
        .toString()
        .charAt(0)
        .toUpperCase() + value.toString().slice(1)
    );
  },
  uppercase: function(value) {
    return value && value.toUpperCase();
  },
  lowercase: function(value) {
    return value && value.toLowerCase();
  },
  isEmpty: function(value) {
    return jQuery.isEmptyObject(value);
  },
  camelToSentence: function(value) {
    var result = value
      .replace(/([A-Z]+)/g, " $1")
      .replace(/([A-Z][a-z])/g, " $1");
    return result.charAt(0).toUpperCase() + result.slice(1);
  },
  normalize: function(value, arg) {
    if (!value || value.length == 0) {
      return "-";
    }

    if (typeof value === "boolean") {
      return value.toString();
    }
    return value;
  },
  sanitize: function(value) {
    return value.replace(/^[^a-z]+|[^\w-]+/gi, "");
  },
  parseObj: function(value) {
    var parts = value.split(";");
    if (parts.length > 1) {
      var type =
        parts[0] &&
        parts[0]
          .toString()
          .charAt(0)
          .toUpperCase() + parts[0].toString().slice(1);
      var name = parts[1];
      return name;
    } else {
      return value;
    }
  },
  objLength: function(value) {
    return Object.keys(value).length;
  },
  prettyNewLine: function(value) {
    if (value == "-") {
      return "-";
    }
    return value.length > 0 ? value.split(",").join("\n") : "-";
  },
  truncate: function(val) {
    var str = val;

    if (str.length > 20) {
      str = val.substring(0, val.length - (20 + 3)) + "...";
    }
    return str;
  }
};

for (var f in Filters) {
  Vue.filter(f, Filters[f]);
}
