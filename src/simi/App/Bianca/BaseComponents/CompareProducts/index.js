import React, { useState, useEffect, Fragment } from 'react';
import Modal from 'react-responsive-modal';
import Identify from 'src/simi/Helper/Identify';
import ReactHTMLParse from 'react-html-parser';
import Deleteicon from 'src/simi/App/Bianca/BaseComponents/Icon/Trash';
import { addToCart as simiAddToCart } from 'src/simi/Model/Cart';
import { Colorbtn } from 'src/simi/BaseComponents/Button';
import {showToastMessage} from 'src/simi/Helper/Message';
import { productUrlSuffix } from 'src/simi/Helper/Url';
import Loading from 'src/simi/BaseComponents/Loading';
import { getCartDetails } from 'src/actions/cart';
import { connect } from 'src/drivers';

require('./styles.scss');

const CompareProduct = props => {
    const { openModal, closeModal } = props;
    const [hasRemoved, setHasRemoved] = useState(false);
    const storeConfig = Identify.getStoreConfig();
    let listItem = Identify.getDataFromStoreage(
        Identify.LOCAL_STOREAGE,
        'compare_product'
    );

    useEffect(()=>{
        setHasRemoved(false);
    },[hasRemoved])

    const getVendorName = (vendorId) => {
        let vendorList;
        if(storeConfig){
            vendorList = storeConfig.simiStoreConfig.config.vendor_list;
            const vendor = vendorList.find(vendor => {
                if(vendorId === 'default'){
                    return null;
                }
                return vendor.entity_id === vendorId; //entity_id is Vendor ID in vendor model
            })
            let vendorName = '';
            if (vendor && vendor.firstname) vendorName = `${vendor.firstname}`;
            if (vendor && vendor.lastname) vendorName = `${vendorName} ${vendor.lastname}`;
            const {profile} = vendor || {}
            vendorName = profile && profile.store_name || vendorName;
            if (vendorName) return vendorName;
            // return (vendorName && vendorName.vendor_id)?vendorName.vendor_id:'';
        }
    }

    const removeItem = (itemId) => {
        const itemToRemove = listItem.findIndex(item=>itemId === item.entity_id);
        setHasRemoved(true)

        if(itemToRemove !== -1){
            
            listItem.splice(itemToRemove,1)
            Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE,'compare_product',listItem)
            listItem = Identify.getDataFromStoreage(
                Identify.LOCAL_STOREAGE,
                'compare_product'
            );
            
        }
    }

    const addZeroes = ( num ) => {
        const numStr = num.toString()
        const res = numStr.split(".");
        let value;
        if(res.length == 1 || (res[1].length < 3)) {
            value = num.toFixed(2);
        }
        return value
    }

    const renderImgItem = () => {
        const imgItem = listItem.map(item => {
            const addToCart = (pre_order = false) => {
                if (item) {
                    if ((!parseInt(item.has_options)) && item.type_id === 'simple') {
                        const params = {product: String(item.entity_id), qty: '1'}
                        if (pre_order)
                            params.pre_order = 1
                        // showFogLoading()
                        simiAddToCart(addToCartCallBack, params)
                        return
                    }
                }
                const { url_key } = item
                const { history } = props
                const product_url = `/${url_key}${productUrlSuffix()}`
                history.push(product_url)
                closeModal()
            }
        
            const addToCartCallBack = (data) => {
                // hideFogLoading()
                if (data.errors) {
                    let message = ''
                    data.errors.map(value => {
                        message += value.message
                    })
                    showToastMessage(message?message:Identify.__('Problem occurred.'))
                } else {
                    if (data.message)
                        showToastMessage(data.message)
                    props.getCartDetails()
                }
            }

            let addToCartBtn = (
                <Colorbtn
                    style={{ backgroundColor: '#101820', color: '#FFF' }}
                    className="compare-add-to-cart"
                    onClick={() => addToCart(false)}
                    text={Identify.__('Add to Cart')} />
            )
            
            if (!parseInt(item.is_salable)) {
                if (parseInt(item.pre_order)) {
                    addToCartBtn = (
                        <Colorbtn
                            style={{ backgroundColor: '#101820', color: '#FFF' }}
                            className="compare-add-to-cart"
                            onClick={() => addToCart(true)}
                            text={Identify.__('Pre-order')} />
                    )
                } else
                    addToCartBtn = (
                        <Colorbtn
                            style={{ backgroundColor: '#101820', color: '#FFF', opacity: 0.5 }}
                            className="compare-add-to-cart"
                            text={Identify.__('Out of stock')} />
                    )
            }
            

            return(
                <div key={item.entity_id} className="td compare-img">
                        <div className="compare-remove-btn" onClick={() => removeItem(item.entity_id)}>
                            <Deleteicon
                                style={{ width: '16px', height: '16px', marginRight: '8px', color:'#727272' }} />
                            {Identify.__('Remove')}
                        </div>
                        <div className="compare-item-img">
                            <img src={item.images[0].url} alt={item.name}/>
                        </div>
                        <div>
                            <div className="compare-item-name">{ReactHTMLParse(item.name)}</div>
                            <div className="compare-item-price">
                                {item.special_price
                                ?
                                    <Fragment>
                                        <span>
                                            {storeConfig.simiStoreConfig.currency}&nbsp;
                                        </span>
                                        <span>
                                            { item.special_price
                                            ? parseFloat(item.special_price).toFixed(2)
                                            : null
                                            }
                                        </span>
                                        <span className="compare-item-old-price">
                                            <span>
                                                {storeConfig.simiStoreConfig.currency}&nbsp;
                                            </span>
                                            <span>
                                                {item.app_options && item.app_options.configurable_options
                                                ?
                                                    item.app_options.configurable_options.prices.basePrice.amount
                                                :   
                                                    parseFloat(item.price).toFixed(2)
                                                }
                                            </span>
                                        </span>
                                    </Fragment>
                                :
                                    <span>
                                        <span>
                                            {storeConfig.simiStoreConfig.currency}&nbsp;
                                        </span>
                                        <span>
                                            {item.app_options && item.app_options.configurable_options
                                            ?
                                                <Fragment>
                                                    {addZeroes(item.app_options.configurable_options.prices.basePrice.amount)}
                                                </Fragment>
                                            :   
                                                parseFloat(item.price).toFixed(2)
                                            }
                                        </span>
                                    </span>
                                }
                            </div>
                            <div className="compare-designer-name">{getVendorName(item.vendor_id)}</div>
                            {addToCartBtn} 
                        </div>
                        {/* <div className="compare-add-to-cart">Add to cart</div> */}
                
                </div>
        )})
        return imgItem
    }

    const renderDescription = () => {
        const descriptions = listItem.map(item => {
            return(
                <div key={item.entity_id} className="td compare-description">
                    {ReactHTMLParse(item.description)}
                </div>
        )})
        return descriptions;
    }

    const renderSKU = () => {
        const skuItem = listItem.map(item => {
            return (
                <div key={item.entity_id} className="td">
                    {item.sku}
                </div>
            )
        })
        return skuItem;
    }

    const renderQtyInStock = () => {
        const qtyInStock = listItem.map(item => {
            if(item.quantity_and_stock_status){
                return (
                    <div key={item.entity_id} className="td">
                        {item.quantity_and_stock_status.qty} in stock
                    </div>
                )
            } else {
                <div key={item.entity_id} className="td"></div>
            }
        })

        return qtyInStock;
    }

    const renderWeight = () => {
        const weight = listItem.map(item => {
            let weightItem;
                
            if(item.weight){
                weightItem = item.weight;
            } else {
                weightItem = null;
            }

            return (
                <div key={item.entity_id} className="td">{weightItem}</div>
            )
        }) 

        return weight;
    }

    

    const renderColor = () => {
        const colors = listItem.map(item => {
            let itemColors;

            if(item.app_options && item.app_options.configurable_options){
                const colorObj = item.app_options.configurable_options.attributes[93]
                if(colorObj){
                    itemColors = colorObj.options.map(obj=>{
                        return obj.label;
                    }).join(', ')
                }

                return (
                    <div key={item.entity_id} className="td">
                        {itemColors}
                    </div>
                )
            } else {
                return (
                    <div key={item.entity_id} className="td"></div>
                )
            }
        })

        return colors;
    }

    const renderSize = () => {
        const size = listItem.map(item => {
            let itemSize;

            if(item.app_options && item.app_options.configurable_options){
                const sizeObj = item.app_options.configurable_options.attributes[141]

                if(sizeObj){
                    itemSize = sizeObj.options.map(obj=>{
                        return obj.label;
                    }).join(', ')
                }

                return (
                    <div key={item.entity_id} className="td">
                        {itemSize}
                    </div>
                )
            } else {
                return (
                    <div key={item.entity_id} className="td"></div>
                )
            }
        })

        return size;
    }

    const renderList = () => {
        return (
            <React.Fragment>
                {listItem && listItem.length > 0 ? (
                    <div id="compare-table">
                        <div className="tr">
                            <div className="td td-header">
                            </div>
                            {renderImgItem()}
                        </div>
                        <div className="tr">
                            <div className="td td-header">DESCRIPTION
                            </div>
                            {renderDescription()}
                        </div>
                        <div className="tr compare-item-sku">
                            <div className="td td-header">SKU
                            </div>
                            {renderSKU()}
                            
                        </div>
                        <div className="tr compare-item-in-stock">
                            <div className="td td-header">AVAILABILITY
                            </div>
                            {renderQtyInStock()}
                        </div>
                        <div className="tr compare-weight">
                            <div className="td td-header">WEIGHT
                            </div>
                            {renderWeight()}
                        </div>
                        <div className="tr compare-item-color">
                            <div className="td td-header">COLOR
                            </div>
                            {renderColor()}
                        </div>
                        <div className="tr compare-item-size">
                            <div className="td td-header">SIZE
                            </div>
                            {renderSize()}
                        </div>
                    </div>
                ) : (
                    <div style={{fontWeight:"500"}}>{Identify.__('NO ITEMS TO COMPARE')}</div>
                )}
            </React.Fragment>
        );
    };

    return (
        <Modal
            modalId="modal-compare"
            overlayId="modal-compare-overlay"
            open={openModal}
            onClose={closeModal}
            classNames={{overlay: Identify.isRtl()?"rtl-root":""}}
        >
            <div className="title">{Identify.__("COMPARE PRODUCTS")}</div>
            {/* {listItem
            :<Loading/>
        } */}
            <div className="modal-compare-inner">{renderList()}</div>
        </Modal>
    );
};

const mapDispatchToProps = {
    getCartDetails,
};

export default connect(
    null,
    mapDispatchToProps
)(CompareProduct);