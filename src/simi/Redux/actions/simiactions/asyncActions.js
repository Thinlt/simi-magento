import { Util } from '@magento/peregrine';
import actions from './actions';
import userActions from 'src/actions/user/actions';
import checkoutActions from 'src/actions/checkout/actions';
import { getCartDetails } from 'src/actions/cart';
import {
    getUserDetails,
} from 'src/actions/user';
import isObjectEmpty from 'src/util/isObjectEmpty';
const { BrowserPersistence } = Util;
const storage = new BrowserPersistence();



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

export const submitShippingAddress = payload =>
    async function thunk(dispatch, getState) {
        dispatch(checkoutActions.shippingAddress.submit(payload));

        const { cart, directory } = getState();

        const { cartId } = cart;
        if (!cartId) {
            throw new Error('Missing required information: cartId');
        }

        const { countries } = directory;
        let { formValues: address } = payload;
        try {
            address = formatAddress(address, countries);
            // if (address.id) delete address.id
        } catch (error) {
            dispatch(
                checkoutActions.shippingAddress.reject({
                    incorrectAddressMessage: error.message
                })
            );
            return null;
        }

        await saveShippingAddress(address);
        dispatch(checkoutActions.shippingAddress.accept(address));
    };

export const submitBillingAddress = payload =>
    async function thunk(dispatch, getState) {
        dispatch(checkoutActions.billingAddress.submit(payload));

        const { cart, directory } = getState();

        const { cartId } = cart;
        if (!cartId) {
            throw new Error('Missing required information: cartId');
        }

        let desiredBillingAddress = payload;
        if (!payload.sameAsShippingAddress) {
            const { countries } = directory;
            try {
                desiredBillingAddress = formatAddress(payload, countries);
            } catch (error) {
                dispatch(checkoutActions.billingAddress.reject(error));
                return;
            }
        }

        await saveBillingAddress(desiredBillingAddress);
        dispatch(checkoutActions.billingAddress.accept(desiredBillingAddress));
    };

async function saveShippingAddress(address) {
    if (address.hasOwnProperty('region') && address.region instanceof Object) {
        address = (({ region, ...others }) => ({ ...others }))(address)
    }

    address = (({ id, default_billing, default_shipping, ...others }) => ({ ...others }))(address);
    return storage.setItem('shipping_address', address);
}

async function saveBillingAddress(address) {
    return storage.setItem('billing_address', address);
}

/* helpers */

export function formatAddress(address = {}, countries = []) {
    const country = countries.find(({ id }) => id === address.country_id);

    const { available_regions: regions } = country;
    if (!country.available_regions) {
        return address
    } else {
        let region = {};
        if (address.hasOwnProperty('region_code')) {
            let { region_code } = address;
            region = regions.find(({ code }) => code === region_code);
        } else if (address.hasOwnProperty('region') && !isObjectEmpty(address.region)) {
            const region_list = address.region;
            let { region_code } = region_list;
            if (region_code) {
                region = regions.find(({ code }) => code === region_code);
            } else {
                region = { region: "Mississippi", region_code: "MS", region_id: 35 };
            }
        } else {
            //fake region to accept current shipping address
            region = { region: "Mississippi", region_code: "MS", region_id: 35 };
        }

        return {
            ...address,
            country_id: address.country_id,
            region_id: parseInt(region.id, 10),
            region_code: region.code,
            region: region.name
        }
    }
    /* let region = {};
    if (regions) {
        region = regions.find(({ code }) => code === region_code);
    } else {
        //fake region to accept current shipping address
        region = { region: "Mississippi", region_code: "MS", region_id: 35 };
    }

    return {
        ...address,
        country_id: address.country_id,
        region_id: parseInt(region.id, 10),
        region_code: region.code,
        region: region.name
    }; */
}
