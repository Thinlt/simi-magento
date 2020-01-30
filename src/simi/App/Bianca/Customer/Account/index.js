import React from 'react'
import MenuItem from '@material-ui/core/MenuItem';
import MenuList from '@material-ui/core/MenuList';
import Identify from "src/simi/Helper/Identify";
import defaultClasses from './style.scss'
// import CloseIcon from 'src/simi/BaseComponents/Icon/TapitaIcons/Close'
// import MenuIcon from 'src/simi/BaseComponents/Icon/Menu'
import BreadCrumb from "src/simi/BaseComponents/BreadCrumb"
import classify from 'src/classify';
import { compose } from 'redux';
import { connect } from 'src/drivers';
import Dashboard from './Page/Dashboard';
import Wishlist from './Page/Wishlist'
import ShareWishlist from './Page/Wishlist/shareWishlist'
import Newsletter from './Page/Newsletter';
import AddressBook from './Page/AddressBook';
import Profile from './Page/Profile';
import MyOrder from './Page/OrderHistory';
import OrderDetail from './Page/OrderDetail';
import MyGiftVouchers from './Page/MyGiftVouchers';
import SizeChart from './Page/SizeChart';
import Myreserved from './Page/Myreserved';
import Mytrytobuy from './Page/Mytrytobuy';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import {
    getUserDetails,
} from 'src/actions/user';
import { element } from 'prop-types';

class CustomerLayout extends React.Component {

    constructor(props) {
        super(props);
        const width = window.innerWidth;
        const isPhone = width < 1024
        this.state = {
            page: 'dashboard',
            isPhone,
            firstname: '',
            customer: null
        }
        this.pushTo = '/';

    }

    setIsPhone() {
        const obj = this;
        window.onresize = function () {
            const width = window.innerWidth;
            const isPhone = width < 1024
            if (obj.state.isPhone !== isPhone) {
                obj.setState({ isPhone: isPhone })
            }
        }
    }

    getMenuConfig = () => {
        const menuConfig = [
            {
                title: Identify.__('My Account'),
                url: '/account.html',
                page: 'dashboard',
                enable: true,
                sort_order: 10
            },
            {
                title: Identify.__('My Orders'),
                url: '/orderhistory.html',
                page: 'my-order',
                enable: true,
                sort_order: 20
            },
            {
                title: Identify.__('Account Information'),
                url: '/profile.html',
                page: 'edit-account',
                enable: true,
                sort_order: 30
            },
            {
                title: Identify.__('My Size Chart'),
                url: '/mysizechart.html',
                page: 'size-chart',
                enable: true,
                sort_order: 40
            },
            {
                title: Identify.__('Newsletter'),
                url: '/newsletter.html',
                page: 'newsletter',
                enable: true,
                sort_order: 50
            },
            {
                title: Identify.__('Address Book'),
                url: '/addresses.html',
                page: 'address-book',
                enable: true,
                sort_order: 60
            },
            {
                title: Identify.__('Wishlist'),
                url: '/wishlist.html',
                page: 'wishlist',
                enable: true,
                sort_order: 70
            },
            {
                title: Identify.__('My Gift Vouchers'),
                url: '/mygiftvouchers.html',
                page: 'giftvoucher',
                enable: true,
                sort_order: 80
            },
            {
                title: Identify.__('My Reserved Products'),
                url: '/myreserved.html',
                page: 'myreserved',
                enable: true,
                sort_order: 90
            },
            {
                title: Identify.__('My Try & Buy Products'),
                url: '/mytrytobuy.html',
                page: 'mytrytobuy',
                enable: true,
                sort_order: 100
            },
            {
                title: Identify.__('Log out'),
                url: '/logout.html',
                page: 'home',
                enable: true,
                sort_order: 110
            }
        ]
        return menuConfig
    }

    handleLink = (link) => {
        this.props.history.push(link)
    }

    static getDerivedStateFromProps(nextProps, prevState) {
        if (!nextProps.page || nextProps.page === prevState.page) {
            return null
        }
        return { page: nextProps.page }
    }

    redirectExternalLink = (url) => {
        if (url) {
            Identify.windowOpenUrl(url)
        }
        return null;
    }

    renderMenu = () => {
        const menuConfig = this.getMenuConfig()
        const { page, isPhone } = this.state;
        const menu = menuConfig.map(item => {
            const active = item.page.toString().indexOf(page) > -1 || (page === 'order-detail' && item.page === 'my-order') ? 'active' : '';

            return item.enable ?
                <MenuItem key={item.title}
                    onClick={() => item.page === 'webtrack-login' ? this.redirectExternalLink(item.url) : this.handleLink(item.url)}
                    className={`customer-menu-item ${item.page} ${active}`}>
                    <div className="menu-item-title">
                        {Identify.__(item.title)}
                    </div>
                </MenuItem> : null
        }, this)
        return (
            <div className={`dashboard-menu ${isPhone ? 'mobile' : ''}`}>
                {/* <div className="menu-header">
                    <div className="welcome-customer">
                        {Identify.__("Welcome %s").replace('%s', firstname + ' ' + lastname)}
                    </div>
                    <div role="presentation" className="menu-toggle" onClick={()=>this.handleToggleMenu()}>
                        <MenuIcon color={`#fff`} style={{width:30,height:30, marginTop: 1}}/>
                        <CloseIcon className={`hidden`} color={`#fff`} style={{width:16,height:16, marginTop:7, marginLeft: 9, marginRight: 5}}/>
                    </div>
                </div> */}
                <div className="list-menu-item">
                    <MenuList className='list-menu-item-content'>
                        {menu}
                    </MenuList>
                </div>
            </div>
        )
    }

    renderContent = () => {
        const { page } = this.state;
        const { custom_attributes, firstname, lastname, email, extension_attributes, id } = this.props;

        const data = {
            id,
            firstname,
            lastname,
            email,
            extension_attributes,
            custom_attributes
        }
        let content = null;
        switch (page) {
            case 'dashboard':
                content = <Dashboard customer={data} isPhone={this.state.isPhone} history={this.props.history} />
                break;
            case 'address-book':
                content = <AddressBook isPhone={this.state.isPhone} history={this.props.history} />
                break;
            case 'edit':
                content = <Profile data={data} isPhone={this.state.isPhone} history={this.props.history} />
                break;
            case 'size-chart':
                content = <SizeChart data={data} isPhone={this.state.isPhone} history={this.props.history} />
                break;
            case 'my-order':
                content = <MyOrder data={data} isPhone={this.state.isPhone} history={this.props.history} />
                break;
            case 'newsletter':
                content = <Newsletter isPhone={this.state.isPhone} history={this.props.history} />
                break;
            case 'order-detail':
                content = <OrderDetail isPhone={this.state.isPhone} history={this.props.history} />
                break;
            case 'wishlist':
                content = <Wishlist isPhone={this.state.isPhone} history={this.props.history} />
                break;
            case 'sharewishlist':
                content = <ShareWishlist isPhone={this.state.isPhone} history={this.props.history}/>
                break;
            case 'giftvoucher':
                content = <MyGiftVouchers isPhone={this.state.isPhone} history={this.props.history} />
                break;
            case 'myreserved':
                content = <Myreserved isPhone={this.state.isPhone} history={this.props.history} />
                break;
            case 'mytrytobuy':
                content = <Mytrytobuy isPhone={this.state.isPhone} history={this.props.history} />
                break;
            default:
                content = 'customer dashboard 2'
        }
        return content;
    }

    componentDidMount() {
        this.props.getUserDetails();
        // Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, 'user_detail', '')
        this.setIsPhone()
        $('body').addClass('body-customer-dashboard')
    }

    componentWillUnmount() {
        $('body').removeClass('body-customer-dashboard')
    }

    render() {
        const { page, isPhone } = this.state;
        const { isSignedIn, history } = this.props
        const { custom_attributes } = this.props
        this.pushTo = '/login.html';
        if (!isSignedIn) {
            history.push(this.pushTo);
            return ''
        }

        const { firstname, lastname } = this.props

        return (
            <React.Fragment>
                <div className={`customer-dashboard ${page} ${isPhone ? 'mobile' : ''}`} style={{ minHeight: window.innerHeight - 200 }}>
                    <BreadCrumb history={this.props.history} breadcrumb={[{ name: 'Home', link: '/' }, { name: 'Account' }]} />
                    <div className='container'>
                        <div className="welcome-customer">
                            {Identify.__("Welcome %s").replace('%s', firstname + ' ' + lastname)}
                        </div>
                        <div className="dashboard-layout">
                            {this.renderMenu()}
                            <div className={`dashboard-content ${isPhone ? 'mobile' : ''}`}>
                                {this.renderContent()}
                            </div>
                        </div>
                    </div>
                </div>
            </React.Fragment>

        );
    }
}

const mapStateToProps = ({ user }) => {
    const { currentUser, isSignedIn } = user
    const { firstname, lastname, email, id } = currentUser;
    const { custom_attributes } = currentUser
    return {
        id,
        firstname,
        lastname,
        email,
        isSignedIn,
        custom_attributes
    };
}

const mapDispatchToProps = {
    getUserDetails,
    toggleMessages,
};

export default compose(
    classify(defaultClasses),
    connect(
        mapStateToProps,
        mapDispatchToProps
    )
)(CustomerLayout);
