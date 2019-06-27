import actions from './actions';
import userActions from 'src/actions/user/actions'

export const changeSampleValue = value => async dispatch => {
    dispatch(actions.changeSampleValue(value));
}

export const simiSignedIn = response => async dispatch => {
    dispatch(userActions.signIn.receive(response));
    dispatch(getCartDetails({ forceRefresh: true }))
}

export const toggleMessages = value => async dispatch => {
    dispatch(actions.toggleMessages(value));
}