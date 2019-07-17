import React, { useEffect } from 'react'
import ArrowRight from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowLeft';
import { simiUseQuery } from 'src/simi/Network/Query' 
import getCategory from 'src/simi/queries/catalog/getCategory.graphql'

const HomeCatItem = props => {
    const {item, classes, history} = props;
    const [queryResult, queryApi] = simiUseQuery(getCategory, false);
    const {data} = queryResult
    const {runQuery} = queryApi

    useEffect(() => {
        runQuery({
            variables: {
                id: Number(item.category_id),
                currentPage: Number(1),
                pageSize: Number(8),
                stringId: String(item.category_id),
            }
        })
    }, [])

    const action = () => {
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
            history.push(location);
        }
    }

    return (
        <div className={classes["home-cate-item"]} onClick={() => action()}>
            <div className={classes["cate-img"]}>
                <img src={window.innerWidth < 1024 ?  item.simicategory_filename : item.simicategory_filename_tablet}
                     alt={item.simicategory_name}/>
            </div>
            <div className={classes["cate-title"]}>
                <div className={classes["--text"]}>{item.simicategory_name}</div>
            </div>
            <div className={classes["cate-arrow"]}>
                <ArrowRight color="#fff" style={{width:60,height:60}}/>
            </div>
        </div>
    )
}

export default HomeCatItem