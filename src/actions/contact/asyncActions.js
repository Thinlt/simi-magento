import actions from './actions';
import { RestApi } from '@magento/peregrine';
// import {}

const {request} = RestApi.Magento2;

export const sendContact = (payload, callback) =>
    async function thunk(...args) {
        const [dispatch] = args;
        dispatch(actions.sendContact.request());

        try {
            const response = await request('/rest/V1/simiconnector/contacts',{
                method: 'POST',
                body: JSON.stringify(payload)
            })
            dispatch(actions.sendContact.receive(response))
        } catch (error) {
            dispatch(actions.sendContact.receive(error))

            throw error;
        }

        callback();
    };
