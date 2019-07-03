import { createActions } from 'redux-actions';

const prefix = 'CONTACT';

// classify action creators by domain
// e.g., `actions.order.submit` => CHECKOUT/ORDER/SUBMIT
// a `null` value corresponds to the default creator function
const actionMap = {
    SEND_CONTACT: {
        REQUEST: null,
        RECEIVE: null,
    },
};

export default createActions(actionMap, { prefix });
