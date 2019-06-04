function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import { handleActions } from 'redux-actions';
import actions from "../actions/directory";
export const name = 'directory';
const initialState = {};
const reducerMap = {
  [actions.getCountries]: (state, {
    payload,
    error
  }) => {
    if (error) {
      return state;
    }

    return _objectSpread({}, state, {
      countries: payload
    });
  }
};
export default handleActions(reducerMap, initialState);
//# sourceMappingURL=directory.js.map