import React, { useEffect, useState } from 'react'
import { simiUseQuery } from 'src/simi/Network/Query' 
import getCategory from 'src/simi/queries/catalog/getCategory.graphql'
import {getProductDetail} from 'src/simi/Model/Product'

const BannerItem = props => {
    const {classes, history, item} = props;
    const [productData, handleProductData] = useState(null);
    const [queryResult, queryApi] = simiUseQuery(getCategory, false);
    const {data} = queryResult
    const {runQuery} = queryApi

    const getCategoryDetail = () => {
        runQuery({
            variables: {
                id: Number(item.category_id),
                currentPage: Number(1),
                pageSize: Number(8),
                stringId: String(item.category_id),
            }
        })
    }

    const processData = (data) => {
        if(data && data.hasOwnProperty('product')) {
            handleProductData(data.product);
        }
    }

    useEffect(() => {
        if(parseInt(item.type, 10) === 2 && !data) {
            getCategoryDetail()
        } else if(parseInt(item.type, 10) === 1 && !productData) {
            getProductDetail(processData, item.product_id)
        }
    }, [])

    const renderBannerTitle = item => {
        let action = () => {}
        if (parseInt(item.type, 10) === 1 && productData) {
            //open product
            let location = {
                pathname: productData.url_key + '.html'
            };
            action = () => history.push(location);
        } else if(parseInt(item.type, 10) === 2 && data){
            let pathname = '/';
            if(data.category.breadcrumbs && data.category.breadcrumbs instanceof Array) {
                data.category.breadcrumbs.forEach(value => {
                    pathname += value.category_url_key + '/';
                }) 
            }

            if(data.category.url_key) {
                pathname += data.category.url_key + '.html';
                let location = {
                    pathname,
                }
                action = () => history.push(location) ;
            }
            
        }else{
            action = (e) => {
                e.preventDefault();
                window.open(item.banner_url);
            }
        }

        return(
            <div className={classes["banner-title"]} onClick={action}>
                <div className={classes["bannner-content"]}>
                    <div className={classes["title"]}>{item.banner_title}</div>
                </div>
                {/* <Colorbtn text={"show"}
                            className={`${classes["banner-action"]}`}/> */}
            </div>
        )
    }
    
    let w = '100%';
    let h = '100%';
    return (
        <div 
            style={{position: 'relative', maxWidth: w, minHeight: h}} 
            className={classes['banner-item']}
        >
            {renderBannerTitle(item)}
            <img className="img-responsive" width={w} height={h} src={window.innerWidth < 1024 ? item.banner_name : item.banner_name_tablet} alt={item.banner_title}/>
        </div>
    )
}

export default BannerItem;