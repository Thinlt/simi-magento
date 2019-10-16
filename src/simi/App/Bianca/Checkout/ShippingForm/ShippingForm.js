import React, { useCallback, useEffect, useState, useMemo } from 'react';
// import { Form } from 'informed';
import { array, func, shape, string } from 'prop-types';
import { formatLabelPrice } from 'src/simi/Helper/Pricing';
import Identify from 'src/simi/Helper/Identify';
import FieldShippingMethod from 'src/simi/App/Bianca/Checkout/components/fieldShippingMethod';
import Loading from 'src/simi/BaseComponents/Loading/ReactLoading';
import { request } from 'src/simi/Network/RestMagento';
require('./ShippingForm.scss')

const SHIPPING_METHOD_SELECTED = 'shipping_method_selected';

const ShippingForm = (props) => {
    const {
        availableShippingMethods,
        cancel,
        shippingMethod,
        submit
    } = props;

    let initialValue = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, SHIPPING_METHOD_SELECTED);
    let selectableShippingMethods;
    let selectableShippingMethodsVendors = {};
    let vendorIds = [];
    let [vendors, setVendors] = useState([]); // vendor list

    const defaultMethod = { value: '', label: Identify.__('Please choose') }

    if (availableShippingMethods.length) {
        selectableShippingMethods = availableShippingMethods.map(
            (methodRate) => {
                let { carrier_code, carrier_title, method_code, method_title, price_excl_tax } = methodRate;
                if (carrier_code === "vendor_multirate") {
                    return false
                }
                if (method_code) {
                    let rateVendorId = method_code.split('||');
                    if (rateVendorId[1] !== undefined){
                        if (vendorIds.indexOf(rateVendorId[1]) === -1) vendorIds.push(rateVendorId[1]);
                        if (selectableShippingMethodsVendors[rateVendorId[1]] == undefined)
                            selectableShippingMethodsVendors[rateVendorId[1]] = [];
                        selectableShippingMethodsVendors[rateVendorId[1]].push({
                            id: rateVendorId[0],
                            order: price_excl_tax,
                            value: method_code,
                            label: `${method_title} (${formatLabelPrice(price_excl_tax)})`
                        });
                    }
                }
                return {value: carrier_code, label: `${carrier_title} (${formatLabelPrice(price_excl_tax)})`}
            }
        );
        // initialValue = shippingMethod
    } else {
        selectableShippingMethods = [];
        // initialValue = '';
        return <Loading />
    }

    useEffect(() => {
        async function getVendors(vendorIds) {
            const vendorApi = '/rest/V1/simiconnector/vendors';
            const response = await request(vendorApi, {
                method: 'POST',
                body: JSON.stringify({
                    ids: vendorIds.join(',')
                })
            });
            setVendors(response);
            return response;
        }
        getVendors(vendorIds);

        return () => {
            const controller = new AbortController();
            controller.abort(); //cancel requesting
        }
    }, []);

    let selectableShippingMethodsVendorsArray = Object.values(selectableShippingMethodsVendors);

    selectableShippingMethods.unshift(defaultMethod);

    const handleSubmit = useCallback(
        (shippingMethod) => {
            let selectedShippingMethod = availableShippingMethods.find(
                ({ method_code }) => method_code === shippingMethod
            );
            if (!selectedShippingMethod) {
                console.warn(
                    `Could not find the selected shipping method ${selectedShippingMethod} in the list of available shipping methods.`
                );
                cancel();
                return;
            }
            // selected method from Session storeage
            if (initialValue) {
                let selectedMethodsSessionStoreage = initialValue.replace(/vflatrate_/g, '').split('|_|');
                for (let vendorId in selectableShippingMethodsVendors){
                    let foundSelected = selectedMethodsSessionStoreage.find((method_code) => {
                        let vendorIdSelected = method_code.split('||')[1] ? method_code.split('||')[1] : 0;
                        if (vendorId === vendorIdSelected) return true;
                        return false;
                    });
                    if (foundSelected) {
                        selectableShippingMethodsVendors[vendorId].selectedMethod = foundSelected;
                    }
                }
            }
            //build selected selectedVendorsMethods
            let rateVendorId = shippingMethod.split('||');
            if (rateVendorId[1] != undefined) {
                selectableShippingMethodsVendors[rateVendorId[1]].selectedMethod = shippingMethod;
            } else {
                selectedShippingMethod = shippingMethod;
            }
            let selectedMethodArray = Object.values(selectableShippingMethodsVendors);
            let canSubmit = true;
            selectedShippingMethod = selectedMethodArray.filter((methodItem) => {
                if(methodItem.selectedMethod) return true;
                canSubmit = false; //all vendor must selected shipping method
                return false;
            }).map((vendorMethod) => {
                if (vendorMethod.selectedMethod) {
                    return vendorMethod.selectedMethod;
                }
                return undefined;
            })
            // selectedShippingMethod = selectedShippingMethod.join('|_|');
            if (canSubmit) {
                // get multi vendor shipping method
                let vendor_multirate_method = availableShippingMethods.find(
                    ({ method_code }) => {
                        if (selectedShippingMethod.length === method_code.split('|_|').length) {
                            for(let i = 0; i < selectedShippingMethod.length; i++){
                                if (method_code.indexOf(selectedShippingMethod[i]) === -1) {
                                    return false;
                                }
                            }
                            return true;
                        }
                        return false;
                    }
                );
                if (vendor_multirate_method) {
                    console.log(vendor_multirate_method);
                    Identify.storeDataToStoreage(Identify.SESSION_STOREAGE, SHIPPING_METHOD_SELECTED, vendor_multirate_method.method_code);
                    submit({ shippingMethod: vendor_multirate_method });
                }
            }
        },
        [availableShippingMethods, cancel, submit]
    );

    const childFieldProps = {
        // initialValue,
        // selectableShippingMethods,
        availableShippingMethods,
        submit,
        cancel
    }

    // convert initialValue to methods
    let selectedMethods = {}
    if (initialValue) {
        let selectedMethodsArray = initialValue.replace(/vflatrate_/g, '').split('|_|');
        for(let i = 0; i < selectedMethodsArray.length; i++){
            let vendorIdRate = selectedMethodsArray[i].split('||');
            if (vendorIdRate[1] != undefined) {
                selectedMethods[vendorIdRate[1]] = selectedMethodsArray[i];
            }
        }
    }

    return (
        <form className="shipping-form">
            <div className="shipping-body">
                {
                    selectableShippingMethodsVendorsArray.map((methods, key) => {
                        methods.sort((a, b) => (a.order > b.order) ? 1 : -1); //sort
                        methods.unshift(defaultMethod);
                        let selectedValue = '';
                        let vendorId;
                        if (methods[1]) {
                            vendorId = methods[1].value.split('||')[1] ? methods[1].value.split('||')[1] : 0;
                            selectedValue = selectedMethods[vendorId] ? selectedMethods[vendorId] : ''
                        }
                        const vendor = vendors.find(({entity_id}) => parseInt(entity_id) === parseInt(vendorId));
                        const vendorName = vendor ? (vendor.firstname + (vendor.lastname ? ` ${vendor.lastname}` : '')) : Identify.__('Default');
                        return <div key={key}>
                            <span>{vendorName}</span>
                            <FieldShippingMethod 
                                handleSelect={(selectedMethod) => handleSubmit(selectedMethod)} 
                                initialValue={selectedValue}
                                selectableShippingMethods={methods} 
                                {...childFieldProps} />
                        </div>
                    })
                }
            </div>
        </form>
    );
};

ShippingForm.propTypes = {
    availableShippingMethods: array.isRequired,
    cancel: func.isRequired,
    shippingMethod: string,
    submit: func.isRequired
};

ShippingForm.defaultProps = {
    availableShippingMethods: [{}]
};

export default ShippingForm;
