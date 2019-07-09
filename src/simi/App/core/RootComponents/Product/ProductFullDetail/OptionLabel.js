import React from 'react'
import {taxConfig, formatPrice} from "src/simi/Helper/Pricing";
import Identify from 'src/simi/Helper/Identify';
import PropTypes from 'prop-types';
import {configColor} from 'src/simi/Config'

const Optionlabel = props => {
    const {classes, item, option_type} = props
    const {title} = item
    let style = props.style?props.style:{}
    const merchantTaxConfig = taxConfig()?taxConfig():{}
    let label = title?title:''
    style = {...{
        display : 'inline-block',
        fontWeight: '400'
    },...style}
    const priceStyle= {
        color: configColor.price_color, 
        fontSize: 13,
        fontWeight: 200
    }
    //custom opton label
    if (option_type === 'custom_options' && item.price_excluding_tax && item.price_including_tax && item.price_including_tax.price) {
        const symbol = <span style={{margin:'0 5px 0 10px'}}>+</span>
        if (parseInt(merchantTaxConfig.tax_display_type) === 3 && (item.price_excluding_tax.price !== item.price_including_tax.price)) {
            label = (
                <div style={style} className={classes['label-option-text']}>
                    <span style={{
                        fontSize: '16px',
                    }}>{title}</span>
                    <span className={classes['label-option-price']} style={priceStyle}>
                        {symbol}
                        {formatPrice(item.price_including_tax.price)}
                    </span>
                    <span style={{...{margin:'0 10px'}, ...priceStyle}} className={classes['label-option-price']}>
                        ({`${Identify.__('Excl. Tax')}`} {formatPrice(item.price_excluding_tax.price)})
                    </span>
                </div>
            )
        } else {
            label = (
                <div style={style} className={classes['label-option-text']}>
                    <span style={{
                        fontSize: '16px',
                    }}>{title}</span>
                    <span className={classes['label-option-price']} style={priceStyle}>
                        {symbol}
                        {formatPrice(item.price_including_tax.price)}
                    </span>
                </div>
            )
        }
    } else {
        console.log(item)
    }
    return label
}

Optionlabel.propTypes = {
    item : PropTypes.object.isRequired,
    classes : PropTypes.object,
    option_type : PropTypes.string, //custom_options, configurable_options, grouped_options, bundle_options, download_options
    style : PropTypes.object
}

Optionlabel.defaultProps = {
    style : {},
    classes : {},
    option_type : 'custom_options',
}

export default Optionlabel