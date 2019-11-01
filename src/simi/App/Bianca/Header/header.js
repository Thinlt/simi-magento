import React, { Suspense, Children } from 'react'
import Identify from "src/simi/Helper/Identify";
import Favorite from 'src/simi/App/Bianca/BaseComponents/Icon/Favorite'
import MenuIcon from 'src/simi/App/Bianca/BaseComponents/Icon/Menu'
import ToastMessage from 'src/simi/BaseComponents/Message/ToastMessage'
import TopMessage from 'src/simi/BaseComponents/Message/TopMessage'
import NavTrigger from './Component/navTrigger'
import CartTrigger from './cartTrigger'
// import defaultClasses from './header.css'
// import { mergeClasses } from 'src/classify'
import { Link } from 'src/drivers';
import HeaderNavigation from './Component/HeaderNavigation'
import MyAccount from './Component/MyAccount'
// import Settings from './Component/Settings'
import { withRouter } from 'react-router-dom';
import { logoUrl, logoAlt } from 'src/simi/App/Bianca/Helper/Url';
import Storeview from "src/simi/App/Bianca/BaseComponents/Settings/Storeview";
import Currency from "src/simi/App/Bianca/BaseComponents/Settings/Currency";
import ProxyClasses from './Component/ProxyClasses';
import SearchFormTrigger from './Component/SearchFormTrigger';
import MiniCart from 'src/simi/App/Bianca/Components/MiniCart';
require('./header.scss')

const SearchForm = React.lazy(() => import('./Component/SearchForm'));

class Header extends React.Component{
    constructor(props) {
        super(props);
        this._mounted = true;
        const isPhone = window.innerWidth < 1024 ;
        this.state = {isPhone}
        // this.classes = mergeClasses(defaultClasses, this.props.classes)
        this.classes = Object.assign(ProxyClasses, this.props.classes);
    }

    searchTrigger = () => {
        if (this.searchFormCallback && typeof(this.searchFormCallback) === 'function') {
            console.log('toggle search')
            this.searchFormCallback()
        }
    }

    toggleSearch = (callback) => {
        this.searchFormCallback = callback;
    }

    setIsPhone(){
        const obj = this;
        $(window).resize(function () {
            const width = window.innerWidth;
            const isPhone = width < 1024;
            if(obj.state.isPhone !== isPhone){
                obj.setState({isPhone})
            }
        })
    }

    componentDidMount(){
        this.setIsPhone();
    }

    renderLogo = () => {
        // const {isPhone} = this.state;
        return (
            <div className={this.classes['header-logo']}>
                <Link to='/'>
                    <img src={logoUrl()} alt={logoAlt()} />
                </Link>
            </div>
        )
    }

    renderSearchForm = () => {
        return(
            <div className="header-search">
                <Suspense fallback={null}>
                    <SearchForm 
                        history={this.props.history} classes={this.classes}
                    />
                </Suspense>
            </div>
        )
    }

    renderRightBar = () => {
        const {classes} = this
        return(
            <div className={'right-bar'}>
                <div className={'right-bar-item'} id="my-account">
                    <MyAccount classes={classes}/>
                </div>
                <div className={'right-bar-item'} id="wish-list">
                    <Link to={'/wishlist.html'}>
                        <div className={'item-icon'} style={{display: 'flex', justifyContent: 'center'}}>
                            <Favorite />
                        </div>
                    </Link>
                </div>
                <div className={'right-bar-item'} id="cart">
                    <CartTrigger classes={classes}/>
                </div>
            </div>
        )
    }

    outerSearchComponent = (props) => {
        return (
            <div className={props.className} {...props}>
                {props.children}
            </div>
        )
    }

    renderViewPhone = () => {
        return(
            <div className="header-wrapper mobile">
                <div className="container-global-notice">
                    <div className="container ">
                        <div className="global-site-notice">
                            <div className="notice-inner">
                                <div className="notice-msg">
                                    <span>{Identify.__('Sale up to 50%: on')} <a href={"#"}>{Identify.__('selected items')}</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="container-header">
                    <div className="container-fluid">
                        <div className={'header'}>
                            <NavTrigger classes={this.classes}>
                                <MenuIcon />
                            </NavTrigger>
                            {this.renderLogo()}
                            <div className={'right-bar'}>
                                <div className={'right-bar-item'}>
                                    <SearchFormTrigger searchTrigger={this.searchTrigger}/>
                                </div>
                                <div className={'right-bar-item cart'}>
                                    <CartTrigger />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <Suspense fallback={null}>
                    <SearchForm outerComponent={this.outerSearchComponent} toggleSearch={this.toggleSearch} waiting={true}
                        history={this.props.history} classes={this.classes}
                    />
                </Suspense>
                <div id="id-message">
                    <TopMessage/>
                    <ToastMessage/>
                </div>
            </div>
        )
    }

    render(){
        const {storeConfig} = this.props
        var bianca_header_phone = ""
        var bianca_header_sale_title = ""
        var bianca_header_sale_link = ""
        var bianca_header_storelocator = ""
        if(storeConfig && storeConfig.simiStoreConfig && storeConfig.simiStoreConfig.config && storeConfig.simiStoreConfig.config.base){
            let base_option = storeConfig.simiStoreConfig.config.base
            bianca_header_phone = base_option.bianca_header_phone ? base_option.bianca_header_phone : ""
            bianca_header_sale_title = base_option.bianca_header_sale_title ? base_option.bianca_header_sale_title : ""
            bianca_header_storelocator = base_option.bianca_header_storelocator ? base_option.bianca_header_storelocator : ""
        }
        const { classes } = this;
        const { drawer } = this.props;
        const cartIsOpen = drawer === 'cart';
        const storeViewOptions = <Storeview classes={classes} className="storeview"/>
        const currencyOptions = <Currency classes={classes} className="currency"/>
        if(window.innerWidth < 1024){
            return this.renderViewPhone()
        }
        return(
            <React.Fragment>
                <div className="header-wrapper">
                    <div className="container-global-notice">
                        <div className="container ">
                            <div className="global-site-notice">
                                <div className="notice-inner">
                                    <div className="contact-info">
                                        <span className="title-phone">{Identify.__('Contact us 24/7')}: {Identify.__(bianca_header_phone)}</span>
                                    </div>
                                    <div className="notice-msg">
                                        <span>{Identify.__(bianca_header_sale_title)}: <a href={bianca_header_sale_link}>{Identify.__('on selected items')}</a></span>
                                    </div>
                                    <div className="store-switch">
                                        <div className="storelocator">
                                            <div className="storelocator-icon"></div>
                                            <div className="storelocator-title"><a href={bianca_header_storelocator}>{Identify.__("Store")}</a></div>
                                        </div>
                                        <div className="storeview-switcher">
                                            {storeViewOptions}
                                        </div>
                                        <div className="currency-switcher">
                                            {currencyOptions}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div className="container-header">
                        <div className="container sub-container">
                            <div className="header">
                                {this.renderSearchForm()}
                                {this.renderLogo()}
                                {this.renderRightBar()}
                            </div>
                            <MiniCart isOpen={cartIsOpen}/>
                        </div>
                    </div>
                </div>
                {window.innerWidth >= 1024 && 
                <HeaderNavigation classes={this.classes}/>}
                <div id="id-message">
                    <TopMessage/>
                    <ToastMessage/>
                </div>
            </React.Fragment>
        )
    }
}
export default (withRouter)(Header)