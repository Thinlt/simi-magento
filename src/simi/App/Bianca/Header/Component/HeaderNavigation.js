import React from 'react'
import Identify from "src/simi/Helper/Identify"
import HeaderNavMegaitem from './HeaderNavMegaitem'
import { Link } from 'src/drivers';
import NavTrigger from './navTrigger'
import MenuIcon from 'src/simi/BaseComponents/Icon/Menu'
import {cateUrlSuffix} from 'src/simi/Helper/Url'


class Navigation extends React.Component{
    toggleMegaItemContainer() {
        const { classes } = this.props
        $(`.${classes['main-nav']}`).find(`.${classes['nav-item-container']}`).each(function() {
            $(this).removeClass(classes['active'])
        });
    }

    render() {
        const { classes } = this.props
        let menuItems = []
        const showMenuTrigger = false
        if (window.DESKTOP_MENU) {
            var menuItemsData = window.DESKTOP_MENU
            menuItems = menuItems.sort(function(a, b){
                if (a.position && b.position){
                    if (a.position > b.position) return 1; 
                    if (a.position < b.position) return -1;
                }
                return 0
            });
            menuItems = menuItemsData.map((item, index) => {
                var isActive = item.link.indexOf(window.location.pathname) !== -1 ? 'active':'';
                if(!item.include_in_menu){
                    return null
                }
                if (item.children && item.children.length > 0) {
                    let title = item.name
                    if (item.link) {
                        const location = {
                            pathname: item.link,
                            state: {}
                        }
                        title = (
                            <Link 
                            className={classes["nav-item"]+' '+isActive}
                            to={location}
                            >
                                {Identify.__(item.name)}
                            </Link>
                        )
                    }

                    const navItemContainerId = `nav-item-container-${item.menu_item_id}`
                    return (
                        <div
                        key={index} 
                        id={navItemContainerId}
                        role='button'
                        tabIndex='0'
                        onKeyDown={()=>{}}
                        className={classes['nav-item-container']}
                        onFocus={() => {
                            $(`#${navItemContainerId}`).find('.nav-mega-item').addClass('active')
                        }}
                        onMouseOver={() => {
                            $(`#${navItemContainerId}`).find('.nav-mega-item').addClass('active')
                        }}
                        onBlur={() => {
                            $(`#${navItemContainerId}`).find('.nav-mega-item').removeClass('active')
                        }}
                        onMouseOut={() => {
                            $(`#${navItemContainerId}`).find('.nav-mega-item').removeClass('active')
                        }}
                        onClick={() => {
                            $(`#${navItemContainerId}`).find('.nav-mega-item').removeClass('active')
                        }}>
                            {title}
                            <HeaderNavMegaitem 
                                classes={classes}
                                data={item} 
                                itemAndChild={item}
                                toggleMegaItemContainer={()=>this.toggleMegaItemContainer()}
                            />
                        </div>
                    )
                }
                else {
                    
                    if (item.link && item.link.includes('http')) {
                        return (
                            <a 
                                className={classes["nav-item"]+' '+isActive}
                                key={index} 
                                href={item.link ? item.link : '/'}
                                style={{textDecoration: 'none'}}
                            >
                                {Identify.__(item.name)}
                            </a>
                        )
                    }

                    return (
                        <Link 
                            className={classes["nav-item"]+' '+isActive}
                            key={index} 
                            to={item.link?`${item.link}`:'/'}
                            style={{textDecoration: 'none'}}
                        >
                            {Identify.__(item.name)}
                        </Link>
                    )
                }
            })
        } else {
            const storeConfig = Identify.getStoreConfig();
            if (storeConfig && storeConfig.simiRootCate && storeConfig.simiRootCate.children) {
                var rootCateChildren  = storeConfig.simiRootCate.children
                rootCateChildren = rootCateChildren.sort(function(a, b){
                    return a.position - b.position
                });
                menuItems = rootCateChildren.map((item, index) => {
                    var isActive = window.location.pathname.indexOf(item.url_path) === 1 ? 'active':'';
                    
                    if(!item.include_in_menu){
                        return null
                    }
                    if (!item.name)
                        return ''
                    if (item.children && item.children.length > 0) {
                        const location = {
                            pathname: '/' + item.url_path + cateUrlSuffix(),
                            state: {}
                        }
                        const navItemContainerId = `nav-item-container-${item.id}`
                        return (
                            <div
                                key={index} 
                                id={navItemContainerId}
                                role='button'
                                tabIndex='0'
                                onKeyDown={()=>{}}
                                className={classes['nav-item-container']}
                                onFocus={() => {
                                    $(`#${navItemContainerId}`).find('.nav-mega-item').addClass('active')
                                }}
                                onMouseOver={() => {
                                    $(`#${navItemContainerId}`).find('.nav-mega-item').addClass('active')
                                }}
                                onBlur={() => {
                                    $(`#${navItemContainerId}`).find('.nav-mega-item').removeClass('active')
                                }}
                                onMouseOut={() => {
                                    $(`#${navItemContainerId}`).find('.nav-mega-item').removeClass('active')
                                }}
                                onClick={() => {
                                    $(`#${navItemContainerId}`).find('.nav-mega-item').removeClass('active')
                                }}
                                >
                                <Link
                                    className={'nav-item '+ isActive}
                                    to={location}
                                    >
                                    {Identify.__(item.name)}
                                </Link>
                                <HeaderNavMegaitem 
                                    classes={classes}
                                    data={item} 
                                    itemAndChild={item}
                                    childCol={2}
                                    toggleMegaItemContainer={()=>this.toggleMegaItemContainer()}
                                />
                            </div>
                        )
                    } else {
                        return (
                            <Link 
                                className={classes["nav-item"]+' '+isActive}
                                key={index} 
                                to={'/' + item.url_path + cateUrlSuffix()}
                                style={{textDecoration: 'none'}}
                                >
                                {Identify.__(item.name)}
                            </Link>
                        )
                    }
                })
            }
        }
        return (
            <div className={classes["app-nav"]}>
                <div className="container">
                    <div className={classes["main-nav"]}>
                        {
                            showMenuTrigger && 
                            <NavTrigger>
                                <MenuIcon color="white" style={{width:30,height:30}}/>
                            </NavTrigger>
                        }
                        {menuItems}
                    </div>
                </div>
            </div>
        );
    }
}
export default Navigation