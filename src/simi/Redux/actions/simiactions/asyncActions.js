import actions from './actions';
import userActions from 'src/actions/user/actions'
import { getCartDetails } from 'src/actions/cart';
import {
    getUserDetails,
} from 'src/actions/user';

export const changeSampleValue = value => async dispatch => {
    dispatch(actions.changeSampleValue(value));
}

export const simiSignedIn = response => async dispatch => {
    dispatch(userActions.signIn.receive(response));
    dispatch(getUserDetails());
    dispatch(getCartDetails({ forceRefresh: true }))
}

export const toggleMessages = value => async dispatch => {
    dispatch(actions.toggleMessages(value));
}