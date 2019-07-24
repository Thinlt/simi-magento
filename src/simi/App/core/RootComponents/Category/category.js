import React from 'react';
import LoadingSpiner from 'src/simi/BaseComponents/Loading/LoadingSpiner'
import { number } from 'prop-types';
import simicntrCategoryQuery from 'src/simi/queries/catalog/getCategory.graphql'
import Products from 'src/simi/BaseComponents/Products';
import { resourceUrl } from 'src/simi/Helper/Url'
import CategoryHeader from './categoryHeader'
import Identify from 'src/simi/Helper/Identify';
import ObjectHelper from 'src/simi/Helper/ObjectHelper';
import { withRouter } from 'react-router-dom';
import {Simiquery} from 'src/simi/Network/Query'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import {applySimiProductListItemExtraField} from 'src/simi/Helper/Product'

var sortByData = null
var filterData = null

const Category = props => {
    const { id } = props;
    let pageSize = Identify.findGetParameter('product_list_limit')
    pageSize = pageSize?Number(pageSize):window.innerWidth < 1024?12:24
    let currentPage = Identify.findGetParameter('page')
    currentPage = currentPage?Number(currentPage):1
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

    const variables = {
        id: Number(id),
        pageSize: pageSize,
        currentPage: currentPage,
        stringId: String(id)
    }
    if (filterData)
        variables.simiFilter = filterData
    if (sortByData)
        variables.sort = sortByData
        
    const cateQuery = simicntrCategoryQuery
    return (
        <Simiquery query={cateQuery} variables={variables}>
            {({ loading, error, data }) => {
                if (error) return <div>Data Fetch Error</div>;
                if (!data || !data.category) return <LoadingSpiner />;

                if (data) {
                    data.products = applySimiProductListItemExtraField(data.simiproducts)
                    if (data.products.simi_filters)
                        data.products.filters = data.products.simi_filters
                }
                const categoryTitle = data && data.category ? data.category.name : '';
                return (
                    <div className="container">
                        {TitleHelper.renderMetaHeader({
                            title: data.category.meta_title?data.category.meta_title:data.category.name,
                            desc: data.category.meta_description
                        })}
                        {
                            (data.category && data.category.name && data.category.image) &&
                            <CategoryHeader
                                name={data.category.name}
                                image_url={resourceUrl(data.category.image, { type: 'image-category' })}
                            />
                        }
                        <Products
                            title={categoryTitle}
                            history={props.history}
                            location={props.location}
                            currentPage={currentPage}
                            pageSize={pageSize}
                            data={loading ? null : data}
                            sortByData={sortByData}
                            filterData={filterData?JSON.parse(productListFilter):null}
                        />
                    </div>
                )

            }}
        </Simiquery>
    );
};

Category.propTypes = {
    id: number,
    pageSize: number
};

Category.defaultProps = {
    id: 3,
    pageSize: 12
};

export default (withRouter)(Category);