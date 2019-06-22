import React from 'react'
import { LazyComponent } from 'src/simi/BaseComponents/LazyComponent'
import Search from 'src/RootComponents/Search';
import CreateAccountPage from 'src/components/CreateAccountPage/index';
import Product from 'src/simi/core/Product';
import Logout from '/src/simi/core/Customer/Logout'

const Checkout = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Checkout"*/'src/simi/core/Checkout')} {...props}/>
}

const Cart = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Cart"*/'src/simi/core/Cart')} {...props}/>
}

const Login = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Login"*/'src/simi/core/Customer/Login')} {...props}/>
}

const Account = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Account"*/'src/simi/core/Customer/Account')} {...props}/>
}

const Wishlist = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Wishlist"*/'src/simi/core/Wishlist')} {...props}/>
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
    },
    login : {
        path: '/login.html',
        render : (location) => <Login {...location}/>
    },
    logout : {
        path: '/logout.html',
        render : (location) => <Logout {...location}/>
    },
    account : {
        path: '/account.html',
        render : (location) => <Account {...location}/>
    },
    wishlist : {
        path: '/wishlist.html',
        render : (location) => <Wishlist {...location}/>
    },
}
export default router;