"use strict";

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(function (window) {
  "use strict";

  var STORAGE_PRICE_ID_KEY = 'SECTIONS_EXTRA_SAVED_PRICE_ID';

  var Store =
  /*#__PURE__*/
  function () {
    function Store(data) {
      _classCallCheck(this, Store);

      this.columns = data.columns;
      this.rows = data.rows;
      this.prices = data.prices;
      this.changes = [];
      this.priceId = null;
      this.initPrices();
      this.initRows(this.rows);
    }

    _createClass(Store, [{
      key: "initPrices",
      value: function initPrices() {
        if (window.localStorage) {
          this.priceId = parseInt(localStorage.getItem(STORAGE_PRICE_ID_KEY));
        }

        if (!this.priceId) {
          for (var i in this.prices) {
            if (this.prices.hasOwnProperty(i)) {
              this.priceId = this.selectPrice(this.prices[i]['ID']);
              break;
            }
          }
        }
      }
    }, {
      key: "initRows",
      value: function initRows(rows) {
        var _this = this;

        var namePrefix = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
        rows.forEach(function (row, index) {
          row._EXTRA = B2BPortal.Utils.cloneDeep(row.EXTRA);
          row._NAME = row.NAME;
          row.NAME = namePrefix + (index + 1) + '. ' + row._NAME;

          if ((row.CHILDREN || []).length) {
            _this.initRows(row.CHILDREN, namePrefix + (index + 1) + '. ');
          }
        });
      }
    }, {
      key: "addChange",
      value: function addChange(data) {
        for (var i in this.changes) {
          if (this.changes.hasOwnProperty(i)) {
            if (this.changes[i].priceId == data.priceId && this.changes[i].row.ID == data.row.ID) {
              this.changes.splice(i, 1);
              break;
            }
          }
        }

        if (data.oldValue != data.newValue) {
          if (!data.price) {
            data.price = this.prices[data.priceId];
          }

          this.changes.push(data);
        }
      }
    }, {
      key: "clearChanges",
      value: function clearChanges() {
        this.changes = [];
      }
    }, {
      key: "resetExtraValues",
      value: function resetExtraValues(rows, priceId) {
        var _this2 = this;

        if (priceId) {
          rows.forEach(function (row) {
            row.EXTRA[priceId] = B2BPortal.Utils.cloneDeep(row._EXTRA[priceId]);
          });
        } else {
          rows.forEach(function (row) {
            row.EXTRA = B2BPortal.Utils.cloneDeep(row._EXTRA);

            if ((row.CHILDREN || []).length) {
              _this2.resetExtraValues(row.CHILDREN);
            }
          });
        }
      }
    }, {
      key: "setExtraValues",
      value: function setExtraValues(row, values) {
        var groupedValues = B2BPortal.Utils.groupBy(values, 'PRICE_ID');

        for (var priceId in groupedValues) {
          if (row._EXTRA[priceId]) {
            row._EXTRA[priceId].VALUE = row.EXTRA[priceId].VALUE = groupedValues[priceId][0].VALUE;
            row._EXTRA[priceId].ID = row.EXTRA[priceId].ID = groupedValues[priceId][0].ID;
          }
        }
      }
    }, {
      key: "setExtrasValues",
      value: function setExtrasValues(rows, values) {
        var _this3 = this;

        rows.filter(function (row) {
          return values[row.ID];
        }).forEach(function (row) {
          return _this3.setExtraValues(row, values[row.ID]);
        });
        rows.filter(function (row) {
          return (row.CHILDREN || []).length;
        }).forEach(function (row) {
          return _this3.setExtrasValues(row.CHILDREN, values);
        });
      }
    }, {
      key: "completeChanges",
      value: function completeChanges() {
        var completed = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
        this.changes = this.changes.filter(function (change) {
          var priceId = change.priceId;
          var extra = change.row.EXTRA[priceId];
          return !completed.includes(extra.ID);
        });
      }
    }, {
      key: "resetChanges",
      value: function resetChanges() {
        this.clearChanges();
        this.resetExtraValues(this.rows);
      }
    }, {
      key: "selectPrice",
      value: function selectPrice(id) {
        this.priceId = parseInt(id);

        if (window.localStorage) {
          localStorage.setItem(STORAGE_PRICE_ID_KEY, this.priceId);
        }
      }
    }]);

    return Store;
  }();

  window.SectionsExtraStore = Store;
})(window, B2BPortal);
