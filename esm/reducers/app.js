function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import { handleActions } from 'redux-actions';
import actions from "../actions/app";
export const name = 'app';
const initialState = {
  drawer: null,
  hasBeenOffline: !navigator.onLine,
  isOnline: navigator.onLine,
  overlay: false,
  searchOpen: false,
  query: '',
  pending: {}
};
const reducerMap = {
  [actions.toggleDrawer]: (state, {
    payload
  }) => {
    return _objectSpread({}, state, {
      drawer: payload,
      overlay: !!payload
    });
  },
  [actions.toggleSearch]: state => {
    return _objectSpread({}, state, {
      searchOpen: !state.searchOpen
    });
  },
  [actions.executeSearch]: (state, {
    payload
  }) => {
    return _objectSpread({}, state, {
      query: payload
    });
  },
  [actions.setOnline]: state => {
    return _objectSpread({}, state, {
      isOnline: true
    });
  },
  [actions.setOffline]: state => {
    return _objectSpread({}, state, {
      isOnline: false,
      hasBeenOffline: true
    });
  }
};
export default handleActions(reducerMap, initialState);
//# sourceMappingURL=app.js.map