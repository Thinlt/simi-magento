import React from 'react'
import { LazyComponent } from 'src/simi/BaseComponents/LazyComponent'

const Home = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Home"*/'src/simi/App/Bianca/Home')} {...props}/>
}

const Checkout = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "SimiBiancaCheckout"*/'src/simi/App/Bianca/Checkout')} {...props}/>
}

const Thankyou = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "BiancaThankyou"*/'src/simi/App/Bianca/Checkout/Thankyou')} {...props}/>
}

const Login = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Login"*/'src/simi/App/Bianca/Customer/Login')} {...props}/>
}

const VendorLogin = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Login"*/'src/simi/App/Bianca/Vendor/Login')} {...props}/>
}

const Logout = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Logout"*/'src/simi/App/Bianca/Customer/Logout')} {...props}/>
}

const ResetPassword = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "ResetPassword"*/'src/simi/App/Bianca/Customer/ResetPassword')} {...props}/>
}

const Account = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Account"*/'src/simi/App/Bianca/Customer/Account')} {...props}/>
}

const Cart = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Cart"*/'src/simi/App/Bianca/Cart')} {...props}/>
}

const Contact = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Contact"*/'src/simi/App/core/Contact/Contact')} {...props}/>
}

const Clothing = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Contact"*/'src/simi/App/Bianca/Clothing')} {...props}/>
}

const Product = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "SimiBiancaProduct"*/'src/simi/App/Bianca/RootComponents/Product')} {...props}/>
}

const VendorList = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "VendorList"*/'src/simi/App/Bianca/Components/Vendor/VendorList')} {...props}/>
}

const VendorDetail = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "VendorDetail"*/'src/simi/App/Bianca/Components/Vendor/Detail')} {...props}/>
}

const Search = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Search"*/'src/simi/App/Bianca/RootComponents/Search')} {...props}/>
}

const PaypalExpress = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "PaypalExpress"*/'src/simi/App/core/Payment/Paypalexpress')} {...props}/>
}

const NoMatch = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "NoMatch"*/'src/simi/App/Bianca/NoMatch')} {...props}/>
}

const PreorderSecondOrder = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "PreorderSecondOrder"*/'src/simi/App/Bianca/PreorderSecondOrder')} {...props}/>
}

const Webviews = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Payfort"*/'src/simi/App/Bianca/Payment/Webviews')} {...props}/>
}

const StoreLocator = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "StoreLocator"*/'src/simi/App/Bianca/StoreLocator')} {...props}/>
}

const Shopbybrand = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Shopbybrand"*/'src/simi/App/Bianca/Shopbybrand')} {...props}/>
}

const Blog = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Blog"*/'src/simi/App/Bianca/Blog')} {...props}/>
}

const Sharedwishlist = (props) => {
    return <LazyComponent component={() => import(/* webpackChunkName: "Sharedwishlist"*/'src/simi/App/Bianca/Sharedwishlist')} {...props}/>
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
    cart : {
        path : '/cart.html',
        component : (location)=><Cart {...location}/>
    },
    product_detail : {
        path: '/product.html',
        render : (location) => <Product {...location}/>
    },
    vendor_list : {
        path: "/designers.html",
        render : (location) => <VendorList {...location}/>
    },
    vendor_detail : {
        path: "/designers/:id.html",
        render : (location) => <VendorDetail {...location}/>
    },
    category_page : {
        path: '/category.html',
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
    vendor_login : {
        path: '/designer_login.html',
        render : (location) => <VendorLogin {...location}/>
    },
    customer_reset_password : {
        path : '/resetPassword.html',
        render : (location) => <ResetPassword {...location} />
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
    myreserved : {
        path : '/myreserved.html',
        render : location => <Account {...location} page={`myreserved`} />
    },
    mytrytobuy : {
        path : '/mytrytobuy.html',
        render : location => <Account {...location} page={`mytrytobuy`} />
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
    sizechart : {
        path : '/mysizechart.html',
        render : location => <Account {...location} page={`size-chart`}/>
    },
    wishlist : {
        path: '/wishlist.html',
        render : (location) => <Account {...location} page={`wishlist`}/>
    },
    sharewishlist : {
        path: '/sharewishlist.html',
        render : (location) => <Account {...location} page={`sharewishlist`}/>
    },
    sharedwishlist : {
        path: '/sharedwishlist.html',
        render : (location) => <Sharedwishlist {...location}/>
    },
    my_gift_vouchers: {
        path: '/mygiftvouchers.html',
        render : (location) => <Account {...location} page={`giftvoucher`}/>
    },
    contact: {
        path: '/contact.html',
        render : location => <Contact {...location} page={`contact`}/>
    },
    clothing_alterations: {
        path: '/clothing-alterations.html',
        render : location => <Clothing {...location} page={`clothing`}/>
    },
    contact: {
        path: '/paypal_express.html',
        render : location => <PaypalExpress {...location} page={`contact`}/>
    },
    preorder2nd: {
        path: '/preorder_complete.html',
        render : location => <PreorderSecondOrder {...location} page={`contact`}/>
    },
    webview: {
        path: '/payment_webview.html',
        render : location => <Webviews {...location} />
    },
    storelocator: {
        path: '/storelocator.html',
        render : location => <StoreLocator {...location}/>
    },
    shopbybrand: {
        path: '/brands.html',
        render : location => <Shopbybrand {...location}/>
    },
    blog: {
        path: '/blog',
        render : location => <Blog {...location}/>
    },
    noMatch: {
        component : location => <NoMatch {...location} />
    }
}
export default router;
