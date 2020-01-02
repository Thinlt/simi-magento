import React, {useState} from 'react';
// import LoadingSpiner from 'src/simi/BaseComponents/Loading/LoadingSpiner';
import Loading from 'src/simi/BaseComponents/Loading';
import { number, string } from 'prop-types';
import Identify from 'src/simi/Helper/Identify';
import ObjectHelper from 'src/simi/Helper/ObjectHelper';
import { withRouter } from 'react-router-dom';
import {Simiquery} from 'src/simi/Network/Query';
import productsQuery from 'src/simi/queries/vendorProducts.graphql';
import Products from './Products';
// import { resourceUrl } from 'src/simi/Helper/Url'
// import CategoryHeader from './categoryHeader'
// import TitleHelper from 'src/simi/Helper/TitleHelper'
import {applySimiProductListItemExtraField} from 'src/simi/Helper/Product';
// import BreadCrumb from "src/simi/App/Bianca/BaseComponents/BreadCrumb";
// import { cateUrlSuffix } from 'src/simi/Helper/Url';
// import ChildCats from './childCats';

var sortByData = null
var filterData = null
let loadedData = null

const AllProducts = props => {
    let pageSize = Identify.findGetParameter('product_list_limit')
    pageSize = pageSize ? Number(pageSize) : window.innerWidth < 1024 ? 10:9
    const paramPageval = Identify.findGetParameter('page')
    const [currentPage, setCurrentPage] = useState(paramPageval?Number(paramPageval):1)
    sortByData = null
    const productListOrder = Identify.findGetParameter('product_list_order')
    const productListDir = Identify.findGetParameter('product_list_dir')
    const newSortByData = productListOrder?productListDir?{[productListOrder]: productListDir.toUpperCase()}:{[productListOrder]: 'ASC'}:null
    if (newSortByData && (!sortByData || !ObjectHelper.shallowEqual(sortByData, newSortByData))) {
        sortByData = newSortByData
    }
    filterData = null
    const productListFilter = Identify.findGetParameter('filter')
    if (productListFilter) {
        if (JSON.parse(productListFilter)){
            filterData = productListFilter
        }
    }

    let variables = {
        pageSize: pageSize,
        currentPage: currentPage,
        simiFilter: `{"vendor_id":"${props.vendorId}"}`
    }
    if (filterData)
        variables.simiFilter = filterData
    if (sortByData)
        variables.sort = sortByData
        
    return (
        <Simiquery query={productsQuery} variables={variables}>
            {({ loading, error, data }) => {
                if (error) return <div>{Identify.__('Data Fetch Error')}</div>;

                //prepare data
                if (data && data.vendorproducts) {
                    data.products = applySimiProductListItemExtraField(data.vendorproducts)
                    if (data.products.simi_filters)
                        data.products.filters = data.products.simi_filters
                        
                    const stringVar = JSON.stringify({...variables, ...{currentPage: 0}})
                    if (!loadedData || !loadedData.vars || loadedData.vars !== stringVar) {
                        loadedData = data
                    } else {
                        let loadedItems = loadedData.products.items
                        const newItems = data.products.items
                        loadedItems = loadedItems.concat(newItems)
                        for(var i=0; i<loadedItems.length; ++i) {
                            for(var j=i+1; j<loadedItems.length; ++j) {
                                if(loadedItems[i] && loadedItems[j] && loadedItems[i].id === loadedItems[j].id)
                                    loadedItems.splice(j--, 1);
                            }
                        }
                        loadedData.products.items = loadedItems
                    }
                    loadedData.vars = stringVar
                }

                if (loadedData) data = loadedData

                if (!data || loading) {
                    return <Loading />
                }

                return (
                    <div className="container">
                        <Products
                            isPhone={props.isPhone}
                            history={props.history}
                            location={props.location}
                            currentPage={currentPage}
                            pageSize={pageSize}
                            data={data}
                            sortByData={sortByData}
                            filterData={filterData?JSON.parse(productListFilter):null}
                            setCurrentPage={setCurrentPage}
                            loading={loading}
                        />
                    </div>
                )

            }}
        </Simiquery>
    );
};

AllProducts.propTypes = {
    vendorId: string,
    pageSize: number
};

AllProducts.defaultProps = {
    pageSize: 12
};

export default (withRouter)(AllProducts);