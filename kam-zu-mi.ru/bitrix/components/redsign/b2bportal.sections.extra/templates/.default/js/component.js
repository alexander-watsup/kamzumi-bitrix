"use strict";

function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(function (window, B2BPortal) {
  "use strict";

  var SectionsExtra =
  /*#__PURE__*/
  function () {
    function SectionsExtra(store, params) {
      _classCallCheck(this, SectionsExtra);

      this.sectionsNode = params.sectionsNode;
      this.pricesNode = params.pricesNode;
      this.changelistNode = params.changelistNode;
      this.store = store;
      this.signedParameters = params.signedParameters;
      this.attachTemplate();
    }

    _createClass(SectionsExtra, [{
      key: "blockPage",
      value: function blockPage() {
        var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
        KTApp.blockPage({
          message: '<div id="SE_BlockMessage">' + message + '</div>'
        });
      }
    }, {
      key: "unblockPage",
      value: function unblockPage() {
        KTApp.unblockPage();
      }
    }, {
      key: "updateBlockMessage",
      value: function updateBlockMessage() {
        var message = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
        $("#SE_BlockMessage").text(message);
      }
    }, {
      key: "showError",
      value: function showError(message) {
        window.toastr.error(message);
      }
    }, {
      key: "showWarning",
      value: function showWarning(message) {
        window.toastr.warning(message);
      }
    }, {
      key: "prepareActionData",
      value: function prepareActionData() {
        var data = {};
        data.signedParameters = this.signedParameters;
        data.changes = [];
        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
          for (var _iterator = this.store.changes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
            var change = _step.value;
            data.changes.push({
              SECTION_ID: change.row.ID,
              PRICE_ID: change.priceId,
              VALUE: change.newValue,
              EXTA_ID: change.row.EXTRA[change.priceId].ID
            });
          }
        } catch (err) {
          _didIteratorError = true;
          _iteratorError = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion && _iterator["return"] != null) {
              _iterator["return"]();
            }
          } finally {
            if (_didIteratorError) {
              throw _iteratorError;
            }
          }
        }

        return data;
      }
    }, {
      key: "executeSaveAction",
      value: function executeSaveAction(data) {
        return new Promise(function (resolve, reject) {
          BX.ajax.runComponentAction('redsign:b2bportal.sections.extra', 'save', {
            mode: 'class',
            data: data
          }).then(resolve, reject);
        });
      }
    }, {
      key: "save",
      value: function () {
        var _save = _asyncToGenerator(
        /*#__PURE__*/
        regeneratorRuntime.mark(function _callee() {
          var _this = this;

          var lastItemId, data, result;
          return regeneratorRuntime.wrap(function _callee$(_context) {
            while (1) {
              switch (_context.prev = _context.next) {
                case 0:
                  this.blockPage(BX.message('RS_SECTIONS_EXTRA_SAVE_PRICES'));
                  _context.prev = 1;
                  lastItemId = 0;

                case 3:
                  if (!(this.store.changes.length > 0)) {
                    _context.next = 16;
                    break;
                  }

                  data = this.prepareActionData();
                  data.nLastItemId = lastItemId;
                  _context.next = 8;
                  return this.executeSaveAction(data);

                case 8:
                  result = _context.sent;
                  ;

                  if ((result.data.ERRORS || []).length) {
                    result.data.ERRORS.forEach(function (errorMessage) {
                      return _this.showWarning(errorMessage);
                    });
                  }

                  if ((result.data.UPDATED || []).length) {
                    this.store.setExtrasValues(this.store.rows, B2BPortal.Utils.groupBy(result.data.UPDATED, 'SECTION_ID'));
                  }

                  if ((result.data.COMPLETED || []).length) {
                    this.store.completeChanges(result.data.COMPLETED);
                  }

                  lastItemId = result.data.LAST_ITEM_ID;
                  _context.next = 3;
                  break;

                case 16:
                  Swal.fire(BX.message('RS_SECTIONS_EXTRA_SAVE_SUCCESS'), '', 'success');
                  _context.next = 22;
                  break;

                case 19:
                  _context.prev = 19;
                  _context.t0 = _context["catch"](1);

                  if (_context.t0.errors) {
                    _context.t0.errors.forEach(function (error) {
                      return _this.showError(error.message);
                    });
                  } else {
                    this.showError(BX.message('RS_SECTIONS_EXTRA_ERROR'));
                  }

                case 22:
                  _context.prev = 22;
                  this.unblockPage();
                  return _context.finish(22);

                case 25:
                case "end":
                  return _context.stop();
              }
            }
          }, _callee, this, [[1, 19, 22, 25]]);
        }));

        function save() {
          return _save.apply(this, arguments);
        }

        return save;
      }()
    }, {
      key: "attachSectionsTemplate",
      value: function attachSectionsTemplate() {
        var store = this.store;
        this.sectionsTemplateInstance = new Vue({
          el: this.sectionsNode,
          components: {
            'sections-extra-table': B2BPortal.Vue.Components.SectionsExtraTable
          },
          template: "\n\t\t\t\t\t<sections-extra-table \n\t\t\t\t\t\t:rows=\"store.rows\"\n\t\t\t\t\t\t:columns=\"store.columns\"\n\t\t\t\t\t\t:priceId=\"store.priceId\"\n\t\t\t\t\t\t:messages=\"messages\"\n\t\t\t\t\t\t@setExtraValue=\"addChange\"\n\t\t\t\t\t/>",
          computed: {
            messages: function messages() {
              return Object.freeze({
                'CONFIRM_SET_EXTRA': BX.message('RS_SECTIONS_EXTRA_CONFIRM_SET_EXTRA'),
                'CONFIRM_SET_EXTRA_CATALOG': BX.message('RS_SECTIONS_EXTRA_CONFIRM_SET_EXTRA'),
                'CATALOG': BX.message('RS_SECTIONS_EXTRA_CATALOG')
              });
            }
          },
          data: function data() {
            return {
              store: store
            };
          },
          methods: {
            addChange: function addChange(data) {
              this.store.addChange(data);
            }
          }
        });
      }
    }, {
      key: "attachPricesTemplate",
      value: function attachPricesTemplate() {
        var store = this.store;
        this.pricesTemplateInstance = new Vue({
          el: this.pricesNode,
          components: {
            'dropdown-select': B2BPortal.Vue.Components.Select
          },
          template: '<dropdown-select :variants="variants" :selected="store.priceId" @select="onSelect" />',
          computed: {
            variants: function variants() {
              var variants = {};

              for (var i in this.store.prices) {
                if (this.store.prices.hasOwnProperty(i)) {
                  variants[i] = {
                    id: i,
                    text: this.store.prices[i]['NAME']
                  };
                }
              }

              return variants;
            }
          },
          data: function data() {
            return {
              store: store
            };
          },
          methods: {
            onSelect: function onSelect(selectedVariant) {
              this.store.selectPrice(selectedVariant.id);
            }
          }
        });
      }
    }, {
      key: "attachChangelistTemplate",
      value: function attachChangelistTemplate() {
        var store = this.store;
        var component = this;
        this.changelistTemplateInstance = new Vue({
          el: this.changelistNode,
          components: {
            'sections-extra-changelist': B2BPortal.Vue.Components.SectionsExtraChangelist
          },
          template: "\n\t\t\t\t\t<sections-extra-changelist \n\t\t\t\t\t\t:changes=\"store.changes\"\n\t\t\t\t\t\t:messages=\"messages\"\n\t\t\t\t\t\t@reset=\"store.resetChanges()\"\n\t\t\t\t\t\t@save=\"save\"\n\t\t\t\t\t/>",
          computed: {
            messages: function messages() {
              return Object.freeze({
                'RESET': BX.message('RS_SECTIONS_EXTRA_RESET_CHANGES'),
                'SAVE': BX.message('RS_SECTIONS_EXTRA_SAVE_CHANGES'),
                'EMPTY': BX.message('RS_SECTIONS_EXTRA_CHANGES_EMPTY'),
                'ARE_YOU_SURE': BX.message('RS_SECTIONS_EXTRA_ARE_YOU_SURE')
              });
            }
          },
          methods: {
            save: function save() {
              component.save();
            }
          },
          data: function data() {
            return {
              store: store
            };
          }
        });
      }
    }, {
      key: "attachTemplate",
      value: function attachTemplate() {
        this.attachPricesTemplate();
        this.attachSectionsTemplate();
        this.attachChangelistTemplate();
      }
    }]);

    return SectionsExtra;
  }();

  window.SectionsExtra = SectionsExtra;
})(window, B2BPortal);
