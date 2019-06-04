function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import { handleActions } from 'redux-actions';
import actions from "../actions/catalog";
export const name = 'catalog';
export const initialState = {
  categories: null,
  rootCategoryId: null,
  currentPage: 1,
  pageSize: 6,
  prevPageTotal: null
};
const reducerMap = {
  [actions.getAllCategories.receive]: (state, {
    payload,
    error
  }) => {
    if (error) {
      return state;
    }

    return _objectSpread({}, state, {
      categories: getNormalizedCategories(payload),
      rootCategoryId: payload.id
    });
  },
  [actions.setCurrentPage.receive]: (state, {
    payload,
    error
  }) => {
    if (error) {
      return state;
    }

    return _objectSpread({}, state, {
      currentPage: payload
    });
  },
  [actions.setPrevPageTotal.receive]: (state, {
    payload,
    error
  }) => {
    if (error) {
      return state;
    }

    return _objectSpread({}, state, {
      prevPageTotal: payload
    });
  }
};
export default handleActions(reducerMap, initialState);
/* helpers */

function* extractChildCategories(category) {
  const {
    childrenData
  } = category;

  for (const child of childrenData) {
    yield* extractChildCategories(child);
  }

  category.childrenData = childrenData.map(({
    id
  }) => id);
  yield category;
}

function getNormalizedCategories(rootCategory) {
  const map = {};

  for (const category of extractChildCategories(rootCategory)) {
    map[category.id] = category;
  }

  return map;
}
//# sourceMappingURL=catalog.js.map