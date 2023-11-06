"use strict";

function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

(function (window, B2BPortal) {
  "use strict";

  var ADDTOBASKET_QUANTITY = 1;

  var CatalogSearchArticle =
  /*#__PURE__*/
  function () {
    function CatalogSearchArticle(el, params) {
      _classCallCheck(this, CatalogSearchArticle);

      this.el = el;
      this.signedParameters = params.signedParameters;
      this.attachTemplate();
    }

    _createClass(CatalogSearchArticle, [{
      key: "searchAction",
      value: function searchAction(query) {
        var _this = this;

        return new Promise(function (resolve, reject) {
          BX.ajax.runComponentAction('redsign:b2bportal.catalog.search.article', 'search', {
            mode: 'class',
            data: {
              query: query,
              signedParameters: _this.signedParameters
            }
          }).then(resolve, reject);
        });
      }
    }, {
      key: "search",
      value: function () {
        var _search = _asyncToGenerator(
        /*#__PURE__*/
        regeneratorRuntime.mark(function _callee(query) {
          var foundItems, result;
          return regeneratorRuntime.wrap(function _callee$(_context) {
            while (1) {
              switch (_context.prev = _context.next) {
                case 0:
                  foundItems = [];
                  _context.prev = 1;
                  _context.next = 4;
                  return this.searchAction(query);

                case 4:
                  result = _context.sent;

                  if (result.data) {
                    foundItems = result.data;
                  }

                  _context.next = 11;
                  break;

                case 8:
                  _context.prev = 8;
                  _context.t0 = _context["catch"](1);
                  window.toastr.error(_context.t0.errors[0].message);

                case 11:
                  return _context.abrupt("return", foundItems);

                case 12:
                case "end":
                  return _context.stop();
              }
            }
          }, _callee, this, [[1, 8]]);
        }));

        function search(_x) {
          return _search.apply(this, arguments);
        }

        return search;
      }()
    }, {
      key: "addtobasketAction",
      value: function addtobasketAction(productId, quantity) {
        return new Promise(function (resolve, reject) {
          BX.ajax.runComponentAction('redsign:b2bportal.catalog.search.article', 'addtobasket', {
            mode: 'class',
            data: {
              productId: productId,
              quantity: quantity
            }
          }).then(resolve, reject);
        });
      }
    }, {
      key: "addtobasket",
      value: function () {
        var _addtobasket = _asyncToGenerator(
        /*#__PURE__*/
        regeneratorRuntime.mark(function _callee2(item) {
          var error, success, result, resultData;
          return regeneratorRuntime.wrap(function _callee2$(_context2) {
            while (1) {
              switch (_context2.prev = _context2.next) {
                case 0:
                  error = function error(message) {
                    window.toastr.error(message);
                  };

                  success = function success() {
                    BX.onCustomEvent('updateBasketComponent');
                  };

                  _context2.prev = 2;
                  _context2.next = 5;
                  return this.addtobasketAction(item.id, ADDTOBASKET_QUANTITY);

                case 5:
                  result = _context2.sent;
                  resultData = result.data;

                  if (resultData.isSuccess) {
                    toastr.success((BX.message('RS_B2BPORTAL_CSP_ADDTOBASKET_SUCCESS') || '').replace("#NAME#", item.name));
                    success();
                  } else {
                    error(resultData.errorMessage);
                  }

                  _context2.next = 13;
                  break;

                case 10:
                  _context2.prev = 10;
                  _context2.t0 = _context2["catch"](2);
                  error(((_context2.t0.errors || [])[0] || {}).message);

                case 13:
                case "end":
                  return _context2.stop();
              }
            }
          }, _callee2, this, [[2, 10]]);
        }));

        function addtobasket(_x2) {
          return _addtobasket.apply(this, arguments);
        }

        return addtobasket;
      }()
    }, {
      key: "attachTemplate",
      value: function attachTemplate() {
        var _c = this;

        var el = this.el;
        this.template = new Vue({
          el: el,
          components: {
            'suggestions-input': B2BPortal.Vue.Components.SuggestionsInput
          },
          template: "\n\t\t\t\t\t<suggestions-input \n\t\t\t\t\t\t:placeholder=\"messages.placeholder\"\n\t\t\t\t\t\t:loadSuggestions=\"search\"\n\t\t\t\t\t\t@select=\"addtobasket\"\n\t\t\t\t\t/>\n\t\t\t\t",
          data: function data() {
            return {
              messages: Object.freeze({
                'placeholder': BX.message('RS_B2BPORTAL_CSP_INPUT_PLACEHOLDER')
              })
            };
          },
          methods: {
            search: _c.search.bind(_c),
            addtobasket: _c.addtobasket.bind(_c)
          }
        });
      }
    }]);

    return CatalogSearchArticle;
  }();

  window.CatalogSearchArticle = CatalogSearchArticle;
})(window, B2BPortal);
