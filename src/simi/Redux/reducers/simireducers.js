import { handleActions } from 'redux-actions';

import simiActions from 'src/simi/Redux/actions/simiactions';


const initialState = {
    simiValue: 'cody_initialize_value',
};

const reducerMap = {
    [simiActions.sampleAction]: (state, { value }) => {
        return {
            ...state,
            simiValue: value
        };
    },
};

export default handleActions(reducerMap, initialState);
