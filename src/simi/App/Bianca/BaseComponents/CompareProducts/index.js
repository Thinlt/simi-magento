import React from 'react';
import Modal from 'react-responsive-modal';
import Identify from 'src/simi/Helper/Identify';
import ReactHTMLParse from 'react-html-parser';
require('./styles.scss');

const CompareProduct = props => {
    const { openModal, closeModal } = props;
    const listItem = Identify.getDataFromStoreage(
        Identify.LOCAL_STOREAGE,
        'compare_product'
    );

    const renderItem = () => {
        const compareItem = listItem.map(item => {
            return (
                <div key={item.id} className="compare-item">
                    <div>
                        <div className="compare-remove-btn">Remove</div>
                        <img src={item.small_image} alt={item.name}/>
                        <div className="compare-item-name">{item.name}</div>
                        <div className="compare-item-price">
                            <span>{item.price.regularPrice.amount.currency}</span>
                            {item.price.regularPrice.amount.value}
                        </div>
                        <div className="compare-add-to-cart">Add to cart</div>
                    </div>
                    <div>
                        {ReactHTMLParse(item.short_description.html)}
                    </div>
                    <div className="compare-item-sku">
                        {item.sku}
                    </div>
                    <div>
                        {/* {item.simiExtraField ? item.simiExtraField.attriute_value.weight : null} */}
                        WEIGHT
                    </div>
                    <div>
                        SIZE
                    </div>
                </div>
            );
        });
        return compareItem;
    };

    const renderList = () => {
        return (
            <div>
                {listItem ? (
                    <div className="compare-table">
                        <div className="compare-table-header">
                            <div className="empty-header"></div>
                            <div>DESCRIPTION</div>
                            <div>SKU</div>
                            <div>AVAILABILITY</div>
                            <div>WEIGHT</div>
                            <div>COLOR</div>
                            <div>SIZE</div>
                        </div>
                        <div className="list-compare-item">
                            {renderItem()}
                        </div>
                    </div>
                ) : (
                    <div>{Identify.__('NO ITEMS TO COMPARE')}</div>
                )}
            </div>
        );
    };

    return (
        <Modal
            modalId="modal-compare"
            overlayId="modal-compare-overlay"
            open={openModal}
            onClose={closeModal}
        >
            <div className="modal-compare-inner">{renderList()}</div>
        </Modal>
    );
};

export default CompareProduct;
