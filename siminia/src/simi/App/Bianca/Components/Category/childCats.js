import React from 'react'
import { Link } from 'src/drivers'
import Identify from 'src/simi/Helper/Identify'
import {cateUrlSuffix, resourceUrl} from 'src/simi/Helper/Url'
let foundChild = false

require('./childCats.scss')

const findChild = (catArr, idToFind) => {
    catArr.every(catItem => {
        if (catItem && catItem.id && catItem.id === idToFind) {
            foundChild = catItem
            return true
        }
        if (catItem.children && catItem.children.length)
            return findChild(catItem.children, idToFind)
    })
    return foundChild === false
}
const childCats = props => {
    const {cateEmpty} = props
    if (props.category && props.category.id) {
        const storeConfig = Identify.getStoreConfig();
        if (storeConfig && storeConfig.simiRootCate && storeConfig.simiRootCate.children) {
            if (!foundChild || foundChild.id !== props.category.id ) {
                foundChild = false
                findChild(storeConfig.simiRootCate.children, props.category.id)
            }

            if (foundChild.children && foundChild.children.length) {
                if (cateEmpty) {
                    return (
                        <div className="category-top-images">
                            {
                                foundChild.children.map((child, index)=> {
                                    const location = {pathname: `/${child.url_path}${cateUrlSuffix()}`}
                                    return (
                                        <Link to={location} className="category-top-child-image-ctn" key={index}>
                                            <div className="category-top-child-image" 
                                                style={{backgroundImage: `url("${'pub' + resourceUrl(child.image, { type: 'image-category' })}")`}}>
                                            </div>
                                            <div className="name-title">
                                                {child.name}
                                            </div>
                                        </Link>
                                    )
                                })
                            }
                        </div>
                    )
                }
                return (
                    <div className="category-top-children">
                        {
                            foundChild.children.map((child, index)=> {
                                const location = {
                                    pathname: `/${child.url_path}${cateUrlSuffix()}`,
                                }
                                return (
                                    <div className="category-top-child" key={index}>
                                        <Link to={location}>
                                            {child.name}
                                        </Link>
                                    </div>
                                )
                            })
                        }
                    </div>
                )
            }
        }
    }
    return ''
}
export default childCats