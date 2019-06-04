function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import { handleActions } from 'redux-actions';
import { Util } from '@magento/peregrine';
const {
  BrowserPersistence
} = Util;
const storage = new BrowserPersistence();
import actions from "../actions/user";
export const name = 'user';

const isSignedIn = () => !!storage.getItem('signin_token');

const initialState = {
  currentUser: {
    email: '',
    firstname: '',
    lastname: ''
  },
  getDetailsError: {},
  isGettingDetails: false,
  isSignedIn: isSignedIn(),
  isSigningIn: false,
  forgotPassword: {
    email: '',
    isInProgress: false
  },
  signInError: {}
};
const reducerMap = {
  [actions.signIn.request]: state => {
    return _objectSpread({}, state, {
      isSigningIn: true,
      signInError: {}
    });
  },
  [actions.signIn.receive]: (state, {
    payload,
    error
  }) => {
    if (error) {
      return _objectSpread({}, initialState, {
        signInError: payload
      });
    }

    return _objectSpread({}, state, {
      isSignedIn: true,
      isSigningIn: false
    });
  },
  [actions.getDetails.request]: state => {
    return _objectSpread({}, state, {
      getDetailsError: {},
      isGettingDetails: true
    });
  },
  [actions.getDetails.receive]: (state, {
    payload,
    error
  }) => {
    if (error) {
      return _objectSpread({}, state, {
        getDetailsError: payload,
        isGettingDetails: false
      });
    }

    return _objectSpread({}, state, {
      currentUser: payload,
      isGettingDetails: false
    });
  },
  [actions.createAccountError.receive]: (state, {
    payload
  }) => {
    return _objectSpread({}, state, {
      createAccountError: payload
    });
  },
  [actions.resetCreateAccountError.receive]: state => {
    return _objectSpread({}, state, {
      createAccountError: {}
    });
  },
  [actions.resetPassword.request]: (state, {
    payload
  }) => {
    return _objectSpread({}, state, {
      forgotPassword: {
        email: payload,
        isInProgress: true
      }
    });
  },
  // TODO: handle the reset password response from the API.
  [actions.resetPassword.receive]: state => state,
  [actions.completePasswordReset]: (state, {
    payload
  }) => {
    const {
      email
    } = payload;
    return _objectSpread({}, state, {
      forgotPassword: {
        email,
        isInProgress: false
      }
    });
  },
  [actions.signIn.reset]: () => {
    return _objectSpread({}, initialState, {
      isSignedIn: isSignedIn()
    });
  }
};
export default handleActions(reducerMap, initialState);
//# sourceMappingURL=user.js.map