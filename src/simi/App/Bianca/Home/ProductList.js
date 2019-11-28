import React from 'react'
import ProductSlider from './ProductSlider';
import Identify from 'src/simi/Helper/Identify';

const ProductList = props => {
    const { homeData, history} = props;
    const productLists = homeData && homeData.home && homeData.home.homeproductlists && homeData.home.homeproductlists.homeproductlists || null

    const renderListProduct = () => {
        if(productLists instanceof Array && productLists.length > 0) {
            const productListRendered = productLists.map((item, index) => {
                if (item.category_id)
                    return (
                        <div className="default-productlist-item" key={index}>
                            <h3 className="title">{Identify.__(item.list_title)}</h3>
                            <ProductSlider dataProduct={item} history={history}/>
                        </div>
                    );
                return '';
            });
            return (
                <div className="productlist-content">
                    {productListRendered}
                </div>
            );
        }
    }
    return (
        <div className={`default-home-product-list ${Identify.isRtl() ? 'default-home-pd-rtl' : ''}`}>
            <div className="container">
                {renderListProduct()}
            </div>
        </div>
    );
}

export default ProductList;