import React from 'react'
import Category from 'src/simi/App/Bianca/Components/Category'
import Identify from 'src/simi/Helper/Identify'

const Shopbybrand = props => {
    const storeConfig = Identify.getStoreConfig();
    if (storeConfig && storeConfig.simiRootCate && storeConfig.simiRootCate.id) {
        let foundBrand = false
        let breadcrumb = false
        try {
            const filter = JSON.parse(Identify.findGetParameter('filter'))
            const brands = storeConfig.simiStoreConfig.config.brands
            brands.map(brand => {
                if (brand.option_id === filter.brand)
                    foundBrand = brand
            })
            if (foundBrand && foundBrand.name) {
                breadcrumb = [
                    {name: Identify.__("Home"), link: '/'},
                    {name: foundBrand.name},
                ];
            }
        } catch (err) {
            console.warn(err)
        }
        return <Category {...props} 
                id={storeConfig.simiRootCate.id}
                foundBrand={foundBrand} breadcrumb={breadcrumb}
            />   
    }
    return ''
}
export default Shopbybrand