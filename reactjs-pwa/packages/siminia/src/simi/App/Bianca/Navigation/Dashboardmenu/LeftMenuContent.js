import React from 'react'
import MenuItem from 'src/simi/App/Bianca/BaseComponents/MenuItem'
import {configColor} from 'src/simi/Config';
import Identify from "src/simi/Helper/Identify"
import DownloadIcon from 'src/simi/BaseComponents/Icon/Download'
import UserIcon from 'src/simi/BaseComponents/Icon/TapitaIcons/User'
import {itemTypes} from './Consts'
import PropTypes from 'prop-types'
import { LazyComponent } from 'src/simi/BaseComponents/LazyComponent/'
import CateTree from './CateTree'
import Setting from './Setting'
import { connect } from 'src/drivers';
import LeftAccountMenu from '../../BaseComponents/LeftAccountMenu';
import Storeview from "src/simi/App/Bianca/BaseComponents/Settings/Storeview";
import Currency from "src/simi/App/Bianca/BaseComponents/Settings/Currency";
import ProxyClasses from 'src/simi/App/Bianca/Header/Component/ProxyClasses';
require('./LeftMenuContent.scss')

const styles = {
    iconMenu : {
        fill : configColor.menu_icon_color,
        width: 18,
        height: 18
    },
    menu : {
        color : configColor.menu_text_color,
    },
    divider : {
        backgroundColor : configColor.menu_line_color
    }
}

class LeftMenuContent extends React.Component{

    constructor(props) {
        super(props);
        this.parent=this.props.parent;
        this.classes = Object.assign(ProxyClasses, this.props.classes);
    }
    handleLink = (location) => {
        this.props.handleLink(location)
    }

    handleMenuItem =(item)=>{
        if(item && item.url){
            this.handleLink(item.url)
        } else if (item && item.pathname) {
            this.handleLink(item)
        }
    }

    renderItemDownloadApp =()=> {
        const jsonSimiCart = Identify.getAppDashboardConfigs();
        if (jsonSimiCart && jsonSimiCart['app-configs'] && jsonSimiCart['app-configs'][0]) {
            const config = jsonSimiCart['app-configs'][0];
            if(Identify.detectPlatforms() === 1 && config.ios_link){
                return (
                    <a href={config.ios_link} rel='noopener noreferrer' title="download-app" target="_blank">
                        <MenuItem icon={<DownloadIcon style={styles.iconMenu}/>}
                                  titleStyle={styles.menu}
                                  title={Identify.__('Download app')}
                        />
                    </a>
                )
            }else if(Identify.detectPlatforms() === 2 && config.android_link){
                return (
                    <a href={config.android_link} rel='noopener noreferrer' title="download-app" target="_blank">
                        <MenuItem icon={<DownloadIcon style={styles.iconMenu}/>}
                                  titleStyle={styles.menu}
                                  title={Identify.__('Download app')}
                        />
                    </a>
                )
            }
        }
    }

    renderPbItem = () => {
        const jsonSimiCart = Identify.getAppDashboardConfigs();
        if (!jsonSimiCart || !jsonSimiCart['app-configs'] || !jsonSimiCart['app-configs'][0])
            return
        const config = jsonSimiCart['app-configs'][0];
        const menu_pb_page = []
        if (
            config.api_version &&
            parseInt(config.api_version) &&
            config.themeitems &&
            config.themeitems.pb_pages &&
            config.themeitems.pb_pages.length
            ) {
            const merchantConfigs = Identify.getStoreConfig();
            if (merchantConfigs &&
                merchantConfigs.storeConfig &&
                merchantConfigs.storeConfig.id) {
                const storeId = merchantConfigs.storeConfig.id
                config.themeitems.pb_pages.every(element => {
                    if (
                        element.visibility &&
                        parseInt(element.visibility, 10) === 3 && 
                        element.storeview_visibility &&
                        (element.storeview_visibility.split(',').indexOf(storeId.toString()) !== -1)
                    ){
                        menu_pb_page.push(element)
                    }
                    return true
                });
            }
        }

        if (menu_pb_page && menu_pb_page.length > 0) {
            const obj = this;
            const pb_page = menu_pb_page.map(function (item) {
                const location = {
                    pathname: `/pb_page/${item.entity_id}`
                };
                if (item.url_path && item.url_path !== '/')
                    location.pathname = item.url_path

                const imgRegex = /\.(gif|jpg|jpeg|tiff|png|ico|svg)$/i;
                let img = imgRegex.test(item.image) ? item.image : 'https://image.flaticon.com/icons/svg/470/470615.svg';

                img = <img style={{width: '27px', height: '27px'}}
                            src={img} alt={`cms`}/>
                return (
                    <MenuItem 
                        icon={img}
                        title={Identify.__(item.name)}
                        titleStyle={styles.menu}
                        key={`pb-menu-item-${item.entity_id}`}
                        onClick={()=>obj.handleMenuItem(location)}
                    />
                );
            }, this);
            return pb_page;
        }
        return <div></div>;
    }

    renderSections() {
        const {classes, isSignedIn, leftMenuItems} = this.props
        const storeConfig = Identify.getStoreConfig()
        let bianca_header_phone = ''
        if (storeConfig && storeConfig.simiStoreConfig && storeConfig.simiStoreConfig.config.base)
            bianca_header_phone = storeConfig.simiStoreConfig.config.base.bianca_header_phone
            
        const storeViewOptions = <Storeview classes={classes} className="storeview"/>
        const currencyOptions = <Currency classes={classes} className="currency"/>
        if (!leftMenuItems)
            return (
                <React.Fragment>
                    <CateTree classes={classes} handleMenuItem={this.handleMenuItem.bind(this)} hideHeader={false}/>
                    {/* <MenuItem 
                        classes={classes}
                        icon={<UserIcon style={styles.iconMenu}/>}
                        titleStyle={styles.menu}
                        title={Identify.__('ACCOUNT')}
                        onClick={()=>this.handleLink('/account.html')}
                    /> */}
                    { isSignedIn 
                    ?
                        <LeftAccountMenu handleMenuItem={this.handleMenuItem.bind(this)} />
                    :   null
                    }
                    <div className="left-store">
                        <div onClick={() => this.handleLink('/storelocator.html')}>
                            {Identify.__("Store")}
                        </div>
                    </div>
                    <div className="left-store-switch">
                        <div className="storeview-switcher">
                            {storeViewOptions}
                        </div>
                        <div className="currency-switcher">
                            {currencyOptions}
                        </div>
                    </div>
                    <div className="left-contact-us">
                        <MenuItem title={Identify.__(`CONTACT US 24/7: ${bianca_header_phone}`)}
                            />
                    </div>
                    {/* <Setting parent={this} style={styles} classes={classes}/> */}
                </React.Fragment>
            )
            
        const items = []
        leftMenuItems.map((section) => {
            if (section.name)
                items.push(
                    <div className={classes["item-section"]} key={`item-section-${section.entity_id}`}>
                        <span>{Identify.__(section.name).toUpperCase()}</span>
                    </div>
                )
                section.menu_items.forEach(menuitem => {
                    const itemTypeIndex = parseInt(menuitem.type, 10) - 1
                    if (itemTypes[itemTypeIndex]) {
                        const itemType = itemTypes[itemTypeIndex]
                        if (itemType['disabled'])
                            return
                        if (isSignedIn) {
                            if (itemType['required_logged_out'])
                            return
                        } else if (itemType['required_logged_in'])
                            return
                        let icon = itemType.svg_icon?itemType.svg_icon:''
                        if (menuitem.icon)
                            icon = <img src={menuitem.icon} alt={menuitem.name} style={{width: 18, height: 18}}/>
                        else if (itemType.svg_icon) {
                            icon = (
                                <LazyComponent 
                                    component={()=>import(`src/simi/BaseComponents/Icon/TapitaIcons/${itemType.svg_icon}`)} 
                                    style={styles.iconMenu} 
                                    color={configColor.menu_icon_color}
                                />
                            )
                        }
                        if (itemType.type_id === 21) {
                            items.push(
                                <Setting key={`item-${menuitem.entity_id}`} parent={this} style={styles} classes={classes}/>
                            )
                            return
                        } else if (itemType.type_id === 14) {
                            items.push(
                                <CateTree key={`item-${menuitem.entity_id}`} classes={classes} handleMenuItem={this.handleMenuItem.bind(this)}/>
                            )
                            return
                        } else if (itemType.type_id === 12) {
                            items.push(
                                this.renderPbItem()
                            )
                            return
                        }
                        items.push(
                            <MenuItem 
                                classes={classes}
                                key={`item-${menuitem.entity_id}`}
                                icon={icon}
                                title={Identify.__(menuitem.name)}
                                titleStyle={styles.menu}
                                onClick={()=>this.handleMenuItem(itemType)}
                            />
                        )
                    }
                    
                });
            return null
        }, this)
        return items
    }

    renderBottomMenu = () => {
        return (
            <div className='left-all-cats left-bottom-menu'>
                <div onClick={() => this.handleLink('/login.html')}>
                    {Identify.__('Login as Buyer')}
                </div>
                <div onClick={() => this.handleLink('/designer_login.html')}>
                    {Identify.__('Login as Designer')}
                </div>
            </div>
        )
    }

    render(){
        const {classes, isSignedIn} = this.props
        return (
            <div className={classes["list-menu-header"]} style={{maxHeight:window.innerHeight}}>
                <div>
                    {this.renderSections()}
                    {this.renderItemDownloadApp()}
                </div>
                { !isSignedIn
                ?  this.renderBottomMenu()
                :   null
                }
            </div>
        )
    }
}

LeftMenuContent.contextTypes = {
    classes: PropTypes.object
};

const mapStateToProps = ({ user }) => { 
    const { isSignedIn } = user;
    return {
        isSignedIn
    }; 
};

export default connect(
    mapStateToProps
)(LeftMenuContent);