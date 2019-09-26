import React from 'react'
import Identify from 'src/simi/Helper/Identify';
import { configColor } from 'src/simi/Config';
import MenuItem from 'src/simi/App/Bianca/BaseComponents/MenuItem'
require('./index.scss')

const listAccountMenu = [
    {
        title: 'My Account',
        url: '/account.html',
        page: 'dashboard',
        enable: true,
        sort_order: 10
    },
    {
        title: 'My Orders',
        url: '/orderhistory.html',
        page: 'my-order',
        enable: true,
        sort_order: 20
    },
    {
        title: 'Account Information',
        url: '/profile.html',
        page: 'edit-account',
        enable: true,
        sort_order: 30
    },
    {
        title: 'Newsletter',
        url: '/newsletter.html',
        page: 'newsletter',
        enable: true,
        sort_order: 40
    },
    {
        title: 'Address Book',
        url: '/addresses.html',
        page: 'address-book',
        enable: true,
        sort_order: 50
    },
    {
        title: 'Favourites',
        url: '/wishlist.html',
        page: 'wishlist',
        enable: true,
        sort_order: 60
    }
]

class LeftAccountMenu extends React.Component {
    constructor(props) {
        super(props)
        this.state={
            displayDown: "block",
            displayUp: "none"
        }
    }

    handleToggleMenu = (id) => {
        const listItemAccount = $('#' + id + ' .list-item-account')
        listItemAccount.slideToggle('fast')
    }

    listPages = pages => {
        let result = null;
        if (pages.length > 0) {
            result = pages.map((page, index) => {
                return (
                    <li key={index}
                        onClick={() => this.openLocation(page)}
                    >
                        <MenuItem
                            title={Identify.__(page.title)}
                        />
                    </li>
                );
            })
        }

        return <ul>{result}</ul>;
    }

    openLocation = (location) => {
        this.props.handleMenuItem(location);
    }

    hanndleClick = ()=>{
        $('.cate-icon-down').toggleClass('hidden')
        $('.cate-icon-up').toggleClass('hidden')
    }

    render() {
        return (
            <div className="leftAccountMenu">
                <div className="wrapper-account-title"
                    onClick={()=>this.hanndleClick()}
                >
                    <div className="account-menu-title"
                        style={{ color: configColor.menu_text_color }}                                                                                                                                                                                                                      
                        onClick={() => this.handleToggleMenu('root')} >
                        {Identify.__('ACCOUNT')}
                    </div>
                    <div className="cate-icon-down appear" style={{display:'block',color:configColor.menu_text_color}}>                                                                                                                                                                                                                                                                                                                                                                                                                                           
                        <div className="icon-down"></div> 
                    </div> 
                    <div className="cate-icon-up hidden" style={{display:'block',color:configColor.menu_text_color}}>
                        <div className='icon-up'></div>
                    </div>
                </div> 
                <div className="list-item-account" style={{display:'none',color:configColor.menu_line_color}}>
                    {this.listPages(listAccountMenu)}
                </div>
            </div>
        )

    }
}

export default LeftAccountMenu