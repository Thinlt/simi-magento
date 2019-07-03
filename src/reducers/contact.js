import {handleActions} from 'redux-actions';
import actions from 'src/actions/contact';

export const name = 'contact';

const initialState = {
    data: null,
    contactError:{}
}

const reducerMap = {
    [actions.sendContact.request]: state => {
        return {
            ...state,
        }
    },
    [actions.sendContact.receive]: (state, {payload, error})=>{
        console.log(payload, 'payload')
        // console.log(error,'error')
        if(error){
            return{
                ...initialState,
                contactError: payload
            }
        }

        return {
            ...state,
            data: payload
        }
    }
}

export default handleActions(reducerMap, initialState)