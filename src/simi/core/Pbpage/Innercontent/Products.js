import React from 'react'
/*
import {GridItemHoc} from "../../../Tapita/Products/HoC"
import LoadingSpiner from "../../../../BaseComponent/Loading/LoadingSpiner"
import ProductModelCollection from "../../../../Model/product/ModelCollection"
import Identify from '../../../../Helper/Identify'
import * as Constants from "../../../../Config/Constants";
*/

class Products extends React.Component {
    /*
    constructor(props) {
        super(props)
        this.state = {
            products: null
        }
        this.isPhone = window.innerWidth < 768 ;
        this.productModelCollection = new ProductModelCollection({obj: this})
    }

    componentDidMount() {
        if (this.props.item && this.props.item.data && this.props.item.data.openCategoryProducts) {
            
            let params = {
                'filter[cat_id]': this.props.item.data.openCategoryProducts,
                image_height: this.state.isPhone?Constants.HEIGHT_IMAGE_PHONE:Constants.HEIGHT_IMAGE,
                image_width: this.state.isPhone?Constants.WIDTH_IMAGE_PHONE:Constants.WIDTH_IMAGE
            }
            let api = Identify.ApiDataStorage('product_list_api')||{};
            const key = JSON.stringify(params)
            if(api.hasOwnProperty(key)){
                let data = api[key]
                this.setData(data)
                return;
            }
            this.productModelCollection.getCollection(params)
        }
    }

    setData(data) {
        if (data && data.products) {
            this.setState({products: data.products})
            let api = Identify.ApiDataStorage('product_list_api') || {}
            const key = JSON.stringify(this.productModelCollection.getParams())
            api[key] = data;
            Identify.ApiDataStorage('product_list_api','update',api)
        }
    }

    renderProducts() {
        let count = 0
        let maxItem = 4
        let products = []
        let style={minWidth: 170, display: 'inline-block'}
        style.width = '50%'
        if (!this.isPhone) {
            style.width = '25%'
        }
        if (this.props.item && this.props.item.type === 'product_scroll') {
            maxItem = 12
            style.width = '30%'
            if (!this.isPhone) {
                style.width = '25%'
            }
        }
        this.state.products.every((item, index) => {
            const itemKey = `pb-product-items-${index}-${item.entity_id}`;
            if (count < maxItem) {
                count ++ 
                products.push (
                    <div key={itemKey} className="pb-product-item" style={style}>
                        <GridItemHoc
                            item={item}
                            lazyImage={true}
                            fadingImg={false}
                            />
                    </div>
                )
                return true
            }
            return false
        });
        return products
    }

    render() {
        if (!this.state.products)
            return <LoadingSpiner />
        return this.renderProducts()
    }
    */
    render() {
        return 'products'
    }
}
export default Products