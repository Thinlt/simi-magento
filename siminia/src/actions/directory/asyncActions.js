import actions from './actions';

import Identify from 'src/simi/Helper/Identify'

export const getCountries = () => 
    async function thunk(dispatch, getState) {
        const { directory } = getState();
        if (directory && directory.countries && directory.countries.length) {
            return;
        }
        const storeConfig = Identify.getStoreConfig();
        const countries = []
        if (storeConfig && storeConfig.simiStoreConfig && storeConfig.simiStoreConfig.config && storeConfig.simiStoreConfig.config.allowed_countries) {
            storeConfig.simiStoreConfig.config.allowed_countries.map((country) => {
                if (country.states && country.states.length)
                    country.states = country.states.map(state => {
                        return {id: state.state_id, code: state.state_code, name: state.state_name}
                    })
                else
                    country.states = []
                countries.push({
                    full_name_english: country.country_name,
                    full_name_locale: country.country_name,
                    id: country.country_code,
                    available_regions: country.states
                })
            })
        }
        return dispatch(actions.getCountries(countries));
    }

/*
import { RestApi } from '@magento/peregrine';

import actions from './actions';

const { request } = RestApi.Magento2;

export const getCountries = () =>
    async function thunk(dispatch, getState) {
        const { directory } = getState();

        if (directory && directory.countries) {
            return;
        }

        try {
            const response = await request('/rest/V1/directory/countries');

            dispatch(actions.getCountries(response));
        } catch (error) {
            dispatch(actions.getCountries(error));
        }
    };

*/