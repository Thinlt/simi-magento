import React from 'react'
import { configColor } from 'src/simi/Config';
import { resourceUrl, logoUrl } from 'src/simi/Helper/Url';
import Image from 'src/simi/BaseComponents/Image';
import { isArray } from 'util';
import Identify from 'src/simi/Helper/Identify';
import Checkbox from 'src/components/Checkbox';
import { switchServiceSupport } from 'src/simi/Model/Servicesupport'
import { showFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';


const Shippingproduct = props => {
    const {cart, designer, getCartDetails} = props
    
    const switchedServiceCallback = () => {
        getCartDetails()
    }

    const toggleServiceSupport = (item) => {
        if (cart && cart.cartId && getCartDetails) {
            showFogLoading()
            switchServiceSupport(switchedServiceCallback, item.item_id, cart.cartId, parseInt(item.is_buy_service) !== 1)
        }
    }
    
    if (cart && cart.totals && cart.totals.items && designer) {
        return cart.totals.items.map((item, index) => {
            
            if (
                item.attribute_values && 
                (
                    (parseInt(item.attribute_values.vendor_id) === parseInt(designer.entity_id)) ||
                    (item.attribute_values.vendor_id === designer.entity_id)
                )
            ) {
                let itemsOption = '';
                let optionElement = ''
                item.options = (item.options)?(isArray(item.options)?item.options:JSON.parse(item.options)):[]
                if (item.options.length > 0 && !item.simi_trytobuy_option) {
                    itemsOption = item.options.map((optionObject, optionObjectindex) => {
                        return (
                            <div key={optionObjectindex} className="option-selected-item">
                                <span className='option-title'>{optionObject.label}: </span>
                                <span className='option-value'>{optionObject.value}</span>
                            </div>
                        );
                    });

                    optionElement = (
                        <div className='option-selected'>
                            {itemsOption}
                        </div>
                    );
                }
                const image = (item.image && item.image.file) ? item.image.file : item.simi_image

                return (
                    <div key={index} className='order-item'>
                            <div className='item-image' style={{ borderColor: configColor.image_border_color }}>
                                <Image
                                    src={
                                        image ?
                                            resourceUrl(image, {
                                                type: 'image-product',
                                                width: 80
                                            }) :
                                            logoUrl()
                                    }
                                    alt={item.name} />
                            </div>
                            <div className='item-info'>
                                <label className='item-name'>{item.name}</label>
                                {optionElement}
                                <div className='item-qty-price'>
                                    <span className='qty'>{Identify.__("Quantity")}: {item.qty}</span>
                                </div>
                                <div className="service_support_checkbox">
                                    <Checkbox
                                        fieldState={{value: parseInt(item.is_buy_service) === 1}}
                                        field="service_support"
                                        label={Identify.__("Service support")}
                                        classes={{
                                            label: 'service_support_label',
                                            icon:'service_support_icon'
                                        }}
                                        onChange={() => toggleServiceSupport(item)}
                                    />
                                </div>
                            </div>
                    </div>
                )
            }
        })
    }
    return ''
}
export default Shippingproduct