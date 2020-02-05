export const analyticClickGTM = (name, id, price) => {
    try {
        if (window.dataLayer){
            window.dataLayer.push({
                'event': 'productClick',
                'ecommerce': {
                    'click': {
                    'products': [{
                        'name': name,                     
                        'id': id,
                        'price': price,
                        }]
                    }
                    },
                });
        }
    } catch (err) {}
}

export const analyticAddCartGTM = (name, id, price) => {
    try {
        if (window.dataLayer){
            window.dataLayer.push({
                'event': 'addToCart',
                'ecommerce': {
                    'add': {                                
                    'products': [{                        
                        'name': name,
                        'id': id,
                        'price': price,
                        'quantity': 1
                        }]
                    }
                }
                });
        }
    } catch (err) {}
}


export const analyticsViewDetailsGTM = (product) => {
    if (window.dataLayer){
        const {simiExtraField} = product;
        const {attribute_values} = simiExtraField;
        const {entity_id, price, name} = attribute_values;
        dataLayer.push({
            'ecommerce': {
                'event': 'productDetailView',
                'detail': {
                    'products': [{
                    'name': name, 
                    'id': entity_id || 0,
                    'price': price || 0
                    }]
                },
                'event': 'product_detail_view'
            }
        });
    }
}

export const analyticPurchaseGTM = (dataOrder) => {
    try {
        if (window.dataLayer){
            dataLayer.push({
                'ecommerce': {
                'purchase': {
                    'actionField': {
                    'id': dataOrder.increment_id,
                    'affiliation': 'Jumla-sa Store',
                    'revenue': dataOrder.total.grand_total_incl_tax,
                    'tax':dataOrder.total.tax,
                    'shipping': dataOrder.total.shipping_hand_incl_tax,
                    },
                    'products': dataOrder.order_items
                }
                }
            });
        }
    } catch (err) {}
}