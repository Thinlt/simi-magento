import React, { Suspense, Children } from 'react';
import Identify from 'src/simi/Helper/Identify';
import Favorite from 'src/simi/App/Bianca/BaseComponents/Icon/Favorite';
import MenuIcon from 'src/simi/App/Bianca/BaseComponents/Icon/Menu';
import ToastMessage from 'src/simi/BaseComponents/Message/ToastMessage';
import TopMessage from 'src/simi/BaseComponents/Message/TopMessage';
import NavTrigger from './Component/navTrigger';
import CartTrigger from './cartTrigger';
// import defaultClasses from './header.css'
// import { mergeClasses } from 'src/classify'
import { Link } from 'src/drivers';
import HeaderNavigation from './Component/HeaderNavigation';
import MyAccount from './Component/MyAccount';
// import Settings from './Component/Settings'
import { withRouter } from 'react-router-dom';
import { logoUrl, logoAlt } from 'src/simi/App/Bianca/Helper/Url';
import Storeview from 'src/simi/App/Bianca/BaseComponents/Settings/Storeview';
import Currency from 'src/simi/App/Bianca/BaseComponents/Settings/Currency';
import ProxyClasses from './Component/ProxyClasses';
import SearchFormTrigger from './Component/SearchFormTrigger';
import MiniCart from 'src/simi/App/Bianca/Components/MiniCart';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import CompareProduct from 'src/simi/App/Bianca/BaseComponents/CompareProducts'
import TitleHelper from 'src/simi/Helper/TitleHelper';
require('./header.scss');

const SearchForm = React.lazy(() => import('./Component/SearchForm'));
const $ = window.$;

class Header extends React.Component {
	constructor(props) {
		super(props);
		this._mounted = true;
		const isPhone = window.innerWidth < 1024;
		this.state = {
			isPhone,
			openCompareModal: false,
		};
		// this.classes = mergeClasses(defaultClasses, this.props.classes)
		this.classes = Object.assign(ProxyClasses, this.props.classes);
	}

	searchTrigger = () => {
		if (this.searchFormCallback && typeof this.searchFormCallback === 'function') {
			console.log('toggle search');
			if (Identify.isRtl()) {
					$('.rtl-root #btn-back').toggleClass('move-down')
			} else {
				$('#btn-back').toggleClass('move-down')
			}
			this.searchFormCallback();
		}
	};



	toggleSearch = (callback) => {
		this.searchFormCallback = callback;
	};

	setIsPhone() {
		const obj = this;
		$(window).resize(function () {
			const width = window.innerWidth;
			const isPhone = width < 1024;
			if (obj.state.isPhone !== isPhone) {
				obj.setState({ isPhone });
			}
		});
	}

	componentDidMount() {
		this.setIsPhone();
	}

	showModalCompare = () => {
		this.setState({
			openCompareModal: true
		})
	}

	closeCompareModal = () => {
		this.setState({
			openCompareModal: false
		})
	}

	renderLogo = () => {
		// const {isPhone} = this.state;
		return (
			<div className={this.classes['header-logo']}>
				<div className="header-image">
					<Link to="/">
						<img src={logoUrl()} alt={logoAlt()} />
					</Link>
				</div>
			</div>
		);
	};

	renderSearchForm = () => {
		return (
			<div className="header-search">
				<Suspense fallback={null}>
					<SearchForm history={this.props.history} classes={this.classes} />
				</Suspense>
			</div>
		);
	};

	renderWishList = (isSignedIn) => {
		return isSignedIn ? (
			<div className={'right-bar-item'} id="wish-list">
				<Link to={'/wishlist.html'}>
					<div className={'item-icon'} style={{ display: 'flex', justifyContent: 'center' }}>
						<Favorite />
					</div>
				</Link>
			</div>
		) : (
				''
			);
	};

	renderRightBar = (isSignedIn) => {
		const { history } = this.props
		const { classes } = this;
		const compareData = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'compare_product')
		return (
			<div className={`right-bar ${Identify.isRtl() ? 'rtl-right-bar' : null}`}>
				<div className={'right-bar-item'} id="cart">
					<CartTrigger classes={classes} />
				</div>
				{this.renderWishList(isSignedIn)}
				{compareData && compareData.length > 0
					?
					<div className={'right-bar-item'} id="my-account">
						<div className="compare">
							<span
								role="presentation"
								className="add-to-compare-btn icon-bench-press"
								onClick={this.showModalCompare}
							>
							</span>
							<CompareProduct history={history} openModal={this.state.openCompareModal} closeModal={this.closeCompareModal} />
						</div>

					</div>
					: null
				}
				<div className={'right-bar-item'} id="my-account">
					<MyAccount classes={classes} />
				</div>
			</div>
		);
	};

	outerSearchComponent = (props) => {
		return (
			<div className={props.className} {...props}>
				{props.children}
			</div>
		);
	};

	renderMetaHeader = () => {
		const { pathname } = this.props.location
		if (!pathname)
			return
		ga('send', {
			hitType: 'pageview',
			page: pathname
		});
		if (
			this.props.location && this.props.storeConfig
			&& this.props.storeConfig && this.props.storeConfig.simiStoreConfig
			&& this.props.storeConfig.simiStoreConfig.config
		) {
			const { custom_pwa_titles } = this.props.storeConfig.simiStoreConfig.config
			if (custom_pwa_titles && custom_pwa_titles[pathname]) {
				const custom_pwa_title = custom_pwa_titles[pathname]
				return TitleHelper.renderMetaHeader({
					title: custom_pwa_title.meta_title || null,
					desc: custom_pwa_title.meta_description || null
				})
			}
		}
	}

	renderViewPhone = (bianca_header_sale_title, bianca_header_sale_link) => {
		return (
			<React.Fragment>
				<div className="container-global-notice">
					<div className="container">
						<div className="global-site-notice">
							<div className="notice-inner">
								<div className="notice-msg">
									<span>
										<a href={bianca_header_sale_link}>{Identify.__(bianca_header_sale_title)}</a>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div className="container-header">
					<div className="container-fluid">
						<div className={`header ${Identify.isRtl() ? 'rtl-header' : null}`}>
							<NavTrigger classes={this.classes}>
								<MenuIcon />
							</NavTrigger>
							{this.renderLogo()}
							<div className={`right-bar ${Identify.isRtl() ? 'rtl-right-bar' : null}`}>
								<div className={'right-bar-item'}>
									<SearchFormTrigger searchTrigger={this.searchTrigger} />
								</div>
								<div className={'right-bar-item cart'}>
									<CartTrigger isPhone={this.state.isPhone} />
								</div>
							</div>
						</div>
					</div>
				</div>
				<Suspense fallback={null}>
					<SearchForm
						outerComponent={this.outerSearchComponent}
						toggleSearch={this.toggleSearch}
						waiting={true}
						history={this.props.history}
						classes={this.classes}
					/>
				</Suspense>
				<div id="id-message">
					<TopMessage />
					<ToastMessage />
				</div>
			</React.Fragment>
		);
	};

	render() {
		const { user, storeConfig, location } = this.props;
		// Check user login to show wish lish
		var isSignedIn = false;
		if (user) {
			isSignedIn = user.isSignedIn;
		}
		// Get some custom link on header
		var bianca_header_phone = '';
		var bianca_header_sale_title = '';
		var bianca_header_sale_link = '';
		if (
			storeConfig &&
			storeConfig.simiStoreConfig &&
			storeConfig.simiStoreConfig.config &&
			storeConfig.simiStoreConfig.config.base
		) {
			const base_option = storeConfig.simiStoreConfig.config.base;
			bianca_header_phone = base_option.bianca_header_phone ? base_option.bianca_header_phone : '';
			bianca_header_sale_title = base_option.bianca_header_sale_title ? base_option.bianca_header_sale_title : '';
			bianca_header_sale_link = base_option.bianca_header_sale_link ? base_option.bianca_header_sale_link : '';
		}
		const { classes } = this;
		const { drawer } = this.props;
		const cartIsOpen = drawer === 'cart';
		const storeViewOptions = <Storeview classes={classes} className="storeview" />;
		const currencyOptions = <Currency classes={classes} className="currency" />;
		const simpleHeader = (location && location.pathname &&
			((location.pathname.indexOf("/checkout.html") !== -1) || (location.pathname.indexOf("/cart.html") !== -1)))

		if (window.innerWidth < 1024) {
			return (
				<div className={`header-wrapper mobile ${simpleHeader && 'simple-header'}`}>
					{this.renderMetaHeader()}
					{this.renderViewPhone(bianca_header_sale_title, bianca_header_sale_link)}
				</div>
			)
		}
		return (
			<React.Fragment>
				{this.renderMetaHeader()}
				<div className={`header-wrapper ${simpleHeader && 'simple-header'}`}>
					<div className="container-global-notice">
						<div className="container header-container">
							<div className="global-site-notice">
								<div className="notice-inner">
									<div className="contact-info">
										<span className="title-phone">
											{Identify.__('Contact us 24/7')}: {Identify.__(bianca_header_phone)}
										</span>
									</div>
									<div className="notice-msg">
										<span>
											<a href={bianca_header_sale_link}>{Identify.__(bianca_header_sale_title)}</a>
										</span>
									</div>
									<div className="store-switch">
										<div className="storelocator">
											<div className="storelocator-icon" />
											<div className="storelocator-title">
												<Link to={'/storelocator.html'}>
													{Identify.__('Store')}
												</Link>
											</div>
										</div>
										<div className="storeview-switcher">{storeViewOptions}</div>
										<div className="currency-switcher">{currencyOptions}</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div className="container-header">
						<div className="container sub-container">
							<div className={`header ${Identify.isRtl() ? 'rtl-header' : null}`}>
								{!simpleHeader && this.renderSearchForm()}
								{this.renderLogo()}
								{!simpleHeader && this.renderRightBar(isSignedIn)}
							</div>
							{!simpleHeader && <MiniCart isOpen={cartIsOpen} history={this.props.history} />}
						</div>
					</div>
				</div>
				{!simpleHeader && <HeaderNavigation classes={this.classes} />}
				<div id="id-message">
					<TopMessage />
					<ToastMessage />
				</div>
			</React.Fragment>
		);
	}
}

const mapStateToProps = ({ user }) => {
	return {
		user
	};
};

export default compose(withRouter, connect(mapStateToProps))(Header);
