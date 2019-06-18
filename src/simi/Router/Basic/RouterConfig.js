import React from 'react'
import { LazyComponent } from 'src/simi/BaseComponents/LazyComponent'
import Search from 'src/RootComponents/Search';
import CreateAccountPage from 'src/components/CreateAccountPage/index';
import Cart from 'src/simi/core/Cart';
import Product from 'src/simi/core/Product';

const Checkout = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Checkout"*/'src/simi/core/Checkout')} {...props}/>
}

const router = {
    search_page: {
        path: '/search.html',
        render : (props) => <Search {...props}/>
    },
    register: {
        path: '/create-account',
        render : (props) => <CreateAccountPage {...props}/>
    },
    cart : {
        path : '/cart.html',
        component : (location)=><Cart {...location}/>
    },
    product_detail : {
        path: '/product.html',
        render : (location) => <Product {...location}/>
    },
    checkout : {
        path: '/checkout.html',
        render : (location) => <Checkout {...location}/>
    }
}
export default router;