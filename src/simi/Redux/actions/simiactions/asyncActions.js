import actions from './actions';

export const changeSampleValue = value => async dispatch => {
    dispatch(actions.changeSampleValue(value));
}