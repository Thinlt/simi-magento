import React from 'react'
import Abstract from '../Base'
import { Switch, Route } from 'react-router-dom';
import router from "./RouterConfig";
class AppRouter extends Abstract{

    renderLayout = ()=>{
        return(
            this.renderRoute(router)
        )
    }
    renderRoute =(router = null)=>{
        if(!router) return <div></div>
        return (
            <Switch>
                <Route exact {...router.home}/>
                <Route exact {...router.search_page}/>
                <Route exact {...router.cart}/>
                <Route exact {...router.product_detail}/>
                <Route exact {...router.vendor_list}/>
                <Route exact {...router.vendor_detail}/>
                <Route exact {...router.checkout}/>
                <Route exact {...router.thankyou}/>
                <Route exact {...router.account}/>
                <Route exact {...router.address_book}/>
                <Route exact {...router.oder_history}/>
                <Route exact {...router.order_history_detail}/>
                <Route exact {...router.newsletter}/>
                <Route exact {...router.profile}/>
                <Route exact {...router.sizechart}/>
                <Route exact {...router.wishlist}/>
                <Route exact {...router.sharewishlist}/>
                <Route exact {...router.sharedwishlist}/>
                <Route exact {...router.my_gift_vouchers}/>
                <Route exact {...router.login}/>
                <Route exact {...router.vendor_login}/>
                <Route exact {...router.logout}/>
                <Route exact {...router.customer_reset_password}/>
                <Route exact {...router.contact}/>
                <Route exact {...router.clothing_alterations}/>
                <Route exact {...router.preorder2nd}/>
                <Route exact {...router.webview}/>
                <Route exact {...router.storelocator}/>
                <Route exact {...router.shopbybrand}/>
                <Route exact {...router.blog}/>
                <Route exact {...router.myreserved}/>
                <Route exact {...router.mytrytobuy}/>
                {/*this.renderPbRoute()*/}
                <Route {...router.noMatch}/>
            </Switch>
        )
    }

    render(){
        return super.render()
    }

}
export default AppRouter;