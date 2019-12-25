import React from 'react';
import Modal from 'react-responsive-modal';
import Identify from 'src/simi/Helper/Identify';
import ReactHTMLParse from 'react-html-parser';
import Deleteicon from 'src/simi/App/Bianca/BaseComponents/Icon/Trash'

require('./styles.scss');

const CompareProduct = props => {
    const { openModal, closeModal } = props;
    const listItem = Identify.getDataFromStoreage(
        Identify.LOCAL_STOREAGE,
        'compare_product'
    );

    const renderImgItem = () => {
        const imgItem = listItem.map(item => {
            return(
                <div key={item.id} className="td compare-img">
                        <div className="compare-remove-btn">
                            <Deleteicon
                                style={{ width: '16px', height: '16px', marginRight: '8px', color:'#727272' }} />
                            {Identify.__('Remove')}
                        </div>
                        <img src={item.small_image} alt={item.name}/>
                        
                            
                        <div className="compare-item-name">{item.name}</div>
                        <div className="compare-item-price">
                            <span>{item.price.regularPrice.amount.currency}</span>
                            {item.price.regularPrice.amount.value}
                            <span className="vendor-name">{item.simiExtraField.attribute_values.vendor_name}</span>
                        </div>
                    
                        <div className="compare-add-to-cart">Add to cart</div>
                
                </div>
        )})
        return imgItem
    }

    const renderDescription = () => {
        const descriptions = listItem.map(item => {
            return(
                <div key={item.id} className="td">
                    {ReactHTMLParse(item.short_description.html)}
                </div>
        )})
        return descriptions;
    }

    const renderSKU = () => {
        const skuItem = listItem.map(item => {
            return (
                <div key={item.id} className="td">
                    {item.sku}
                </div>
            )
        })
        return skuItem;
    }

    const renderQtyInStock = () => {
        const qtyInStock = listItem.map(item => {
            if(item.simiExtraField){
                return (
                    <div key={item.id} className="td">
                        {item.simiExtraField.attribute_values.quantity_and_stock_status.qty} in stock
                    </div>
                )
            } else {
                <div key={item.id} className="td"></div>
            }
        })

        return qtyInStock;
    }

    const renderWeight = () => {
        const weight = listItem.map(item => {
            let weightItem;
                
            if(item.simiExtraField){
                if(item.simiExtraField.attribute_values){
                    weightItem = item.simiExtraField.attribute_values.weight;
                } else {
                    weightItem = null;
                }
            } else {
                weightItem = null;
            }

            return (
                <div key={item.id} className="td">{weightItem}</div>
            )
        }) 

        return weight;
    }

    const renderColor = () => {
        const colors = listItem.map(item => {
            let itemColors;

            if(item.configurable_options){
                const colorObj = item.configurable_options.find(obj => {
                    return obj.attribute_code === 'color';
                })

                if(colorObj){
                    itemColors = colorObj.values.map(obj=>{
                        return obj.default_label;
                    }).join(', ')
                }

                return (
                    <div key={item.id} className="td">
                        {itemColors}
                    </div>
                )
            } else {
                return (
                    <div key={item.id} className="td"></div>
                )
            }
        })

        return colors;
    }

    const renderSize = () => {
        const size = listItem.map(item => {
            let itemSize;

            if(item.configurable_options){
                const sizeObj = item.configurable_options.find(obj => {
                    return obj.attribute_code === 'size';
                })

                if(sizeObj){
                    itemSize = sizeObj.values.map(obj=>{
                        return obj.default_label;
                    }).join(', ')
                }

                return (
                    <div key={item.id} className="td">
                        {itemSize}
                    </div>
                )
            } else {
                return (
                    <div key={item.id} className="td"></div>
                )
            }
        })

        return size;
    }

    const renderList = () => {
        return (
            <React.Fragment>
                {listItem ? (
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
                    <div>{Identify.__('NO ITEMS TO COMPARE')}</div>
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
        >
            <div className="title">{Identify.__("COMPARE PRODUCTS")}</div>
            <div className="modal-compare-inner">{renderList()}</div>
        </Modal>
    );
};

export default CompareProduct;
