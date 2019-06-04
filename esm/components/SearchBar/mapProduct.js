function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

/*
 * Map Magento 2.3.1 schema changes to Venia 2.0.0 proptype shape
 * to maintain backwards compatibility.
 *
 * TODO: deprecate and remove
 */
export default (product => {
  const {
    small_image
  } = product;
  return _objectSpread({}, product, {
    small_image: typeof small_image === 'object' ? small_image.url : small_image
  });
});
//# sourceMappingURL=mapProduct.js.map