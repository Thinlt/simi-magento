import React, {useState} from 'react';
import LoadingSpiner from 'src/simi/BaseComponents/Loading/LoadingSpiner'
import { number } from 'prop-types';
import simicntrCategoryQuery from 'src/simi/queries/catalog/getCategory.graphql'
import Products from '../../BaseComponents/Products';
import Identify from 'src/simi/Helper/Identify';
import ObjectHelper from 'src/simi/Helper/ObjectHelper';
import { withRouter } from 'react-router-dom';
import {Simiquery} from 'src/simi/Network/Query'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import {applySimiProductListItemExtraField} from 'src/simi/Helper/Product'
import BreadCrumb from "src/simi/App/Bianca/BaseComponents/BreadCrumb"
import { cateUrlSuffix } from 'src/simi/Helper/Url';
import ChildCats from './childCats'

var sortByData = null
var filterData = null
let loadedData = null

const Category = props => {
    const { id, foundBrand } = props;
    let pageSize = Identify.findGetParameter('product_list_limit')
    pageSize = pageSize?Number(pageSize):window.innerWidth < 1024?10:9
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
                if (error) return <div>{Identify.__('Data Fetch Error')}</div>;
                //prepare data
                if (data && data.simiproducts) {
                    data.products = applySimiProductListItemExtraField(data.simiproducts)
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
                if (loadedData && loadedData.category && parseInt(loadedData.category.id) === parseInt(id))
                    data = loadedData

                if (!data || !data.category) {
                    return <LoadingSpiner />
                }

                //breadcrumb
                const categoryTitle = (foundBrand && foundBrand.name) ? foundBrand.name : (data && data.category) ? data.category.name : '';
                let breadcrumb = [{name: Identify.__("Home"), link: '/'}];
                if (props.breadcrumb) {
                    breadcrumb = props.breadcrumb
                } else {
                    if(data && data.category && data.category.breadcrumbs instanceof Array) {
                        let path = ''
                        data.category.breadcrumbs.forEach(item => {
                            path += ('/' + item.category_url_key)
                            breadcrumb.push({name: item.category_name, link: path + cateUrlSuffix()})
                        })
                    }
                    breadcrumb.push({name: data.category.name})
                }

                const appliedFilter = filterData?JSON.parse(productListFilter):null
                let cateEmpty = false
                if (!appliedFilter && data.simiproducts && data.simiproducts.total_count === 0)
                    cateEmpty = true
                return (
                    <div className="container">
                        <BreadCrumb breadcrumb={breadcrumb} history={props.history}/>
                        {TitleHelper.renderMetaHeader({
                            title: (foundBrand && foundBrand.name) ? foundBrand.name : data.category.meta_title?data.category.meta_title:data.category.name,
                            desc: (foundBrand && foundBrand.description) ? foundBrand.description : data.category.meta_description
                        })}
                        {
                            <Products
                                title={categoryTitle}
                                underHeader={<ChildCats 
                                    category={data.category} 
                                    cateEmpty={cateEmpty}
                                    />
                                }
                                history={props.history}
                                location={props.location}
                                currentPage={currentPage}
                                pageSize={pageSize}
                                data={data}
                                sortByData={sortByData}
                                filterData={appliedFilter}
                                setCurrentPage={setCurrentPage}
                                loading={loading}
                                cateEmpty={cateEmpty}
                                description={(foundBrand && foundBrand.description)?foundBrand.description:false}
                            />
                        }
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