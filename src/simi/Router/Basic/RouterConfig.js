import React from 'react'
import { LazyComponent } from 'src/simi/BaseComponents/LazyComponent'
import Search from 'src/simi/App/core/RootComponents/Search';
import CreateAccountPage from 'src/components/CreateAccountPage/index';
import Product from 'src/simi/App/core/Product';
import Logout from 'src/simi/App/core/Customer/Logout'
import Home from 'src/simi/App/core/RootComponents/CMS/Home'
import Cart from 'src/simi/App/core/Cart'
import Checkout from 'src/simi/App/core/Checkout'
import Thankyou from 'src/simi/App/core/Checkout/Thankyou'
import Account from 'src/simi/App/core/Customer/Account'
import Contact from 'src/simi/App/core/Contact/Contact'

// const Checkout = (props) => {
//     return <LazyComponent component={() => import(/* webpackChunkName: "Checkout"*/'src/simi/App/core/Checkout')} {...props}/>
// }

const Login = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Login"*/'src/simi/App/core/Customer/Login')} {...props}/>
}

const Account1 = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Account"*/'src/simi/App/core/Customer/Account')} {...props}/>
}

const router = {
    home : {
        path: '/',
        render : (location) => <Home {...location}/>
    },
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
    thankyou : {
        path: '/thankyou.html',
        render : (location) => <Thankyou {...location}/>
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
        render : (location) => <Account {...location} page='dashboard'/>
    },
    address_book : {
        path : '/addresses.html/:id?',
        render : location => <Account {...location} page={`address-book`} />
    },
    oder_history : {
        path : '/orderhistory.html',
        render : location => <Account {...location} page={`my-order`} />
    },
    order_history_detail : {
        path : '/orderdetails.html/:orderId',
        render : location => <Account {...location} page={`order-detail`} />
    },
    newsletter : {
        path : '/newsletter.html',
        render : location => <Account {...location} page={`newsletter`} />
    },
    profile : {
        path : '/profile.html',
        render : location => <Account {...location} page={`edit`} />
    },
    wishlist : {
        path: '/wishlist.html',
        render : (location) => <Account {...location} page={`wishlist`}/>
    },
    contact: {
        path: '/contact.html',
        render : location => <Contact {...location} page={`contact`}/>
    }
}
export default router;
