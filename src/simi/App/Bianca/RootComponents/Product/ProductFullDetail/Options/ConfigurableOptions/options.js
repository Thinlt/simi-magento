import React, { Component } from 'react';
import { arrayOf, func, shape, string } from 'prop-types';

import Option from './option';

class Options extends Component {
    static propTypes = {
        onSelectionChange: func,
        options: arrayOf(
            shape({
                attribute_id: string.isRequired
            })
        ).isRequired
    };

    handleSelectionChange = (optionId, selection) => {
        const { onSelectionChange } = this.props;

        if (onSelectionChange) {
            onSelectionChange(optionId, selection);
        }
    };

    handleAskOption = (optionId, code) => {
        const {onSizeGuideClick} = this.props;
        if (code === 'size' && onSizeGuideClick && typeof onSizeGuideClick === 'function') {
            onSizeGuideClick(optionId, code);
        }
    }

    mapOptionInStock = () => {
        const { options, variants } = this.props;
        return options.map(option => {
            if(option.values && option.values instanceof Array){
                option.values.forEach((optionVal) => {
                    let products = [];
                    variants.forEach((variant) => {
                        if (variant.attributes){
                            let variantAttributes = variant.attributes.find((item) => {
                                    return (item.code === option.attribute_code && parseInt(item.value_index) === parseInt(optionVal.value_index))
                                });
                            if (variantAttributes && variant.product && variant.product.stock_status === 'IN_STOCK') {
                                    products.push(variant.product);
                            }
                        }
                    });
                    optionVal.products = products;
                });
            }
            return option;
        });
    }

    render() {
        const { handleSelectionChange, handleAskOption } = this;
        const { optionSelections } = this.props;
        const options = this.mapOptionInStock(); //TODO: implement out-of-stock for color option

        return options.map(option => (
            <Option
                {...option}
                optionSelections={optionSelections}
                key={option.attribute_id}
                onSelectionChange={handleSelectionChange}
                onAskOption={handleAskOption}
            />
        ));
    }
}

export default Options;