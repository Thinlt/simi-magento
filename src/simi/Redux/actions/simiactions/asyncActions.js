import actions from './actions';

export const sampleAction = value => async dispatch =>
    dispatch(actions.sampleAction(value));