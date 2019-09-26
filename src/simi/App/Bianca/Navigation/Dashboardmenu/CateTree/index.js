import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import {cateUrlSuffix} from 'src/simi/Helper/Url';
import {configColor} from 'src/simi/Config';
import CateIcon from 'src/simi/BaseComponents/Icon/TapitaIcons/List'
import SubCate from "./Subcate";

class CateTree extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            treecate : null,
            loaded : false,
            open:false
        }
    }

    shouldComponentUpdate(){
        return !this.renderedOnce
    }

    openLocation = (location)=>{
        this.props.handleMenuItem(location);
    }

    renderTitleMenu = (title)=>{
        const classes = this.props
        return (
            <div className={classes["menu-cate-name-item"]}
                 style={{color:configColor.menu_text_color}}>{Identify.__(title)}</div>
        )
    }

    renderTreeMenu = (data) => {
        const {classes} = this.props
        if (data) {
            const obj =this;
            const categories = data.children.map(function (item,key) {
                if (!item.name)
                    return ''
                const cate_name = <div className={classes["root-menu"]} >{obj.renderTitleMenu(item.name)}</div>;
                const hasChild = (item.children && item.children.length > 0)
                const location = {
                    pathname: item.url_path !== undefined ? "/" + item.url_path + cateUrlSuffix() :  "/products.html?cat=" + item.id,
                    state: {
                        cate_id: item.id,
                        hasChild: hasChild,
                        name: item.name
                    }
                };
                return !hasChild  ?
                    obj.renderMenuItem(cate_name, location) : <SubCate 
                                                                    key={key}
                                                                    classes={classes}
                                                                    cate_name={cate_name}
                                                                    item={item} parent={this}
                                                                    openLocation={this.openLocation.bind(this)}
                                                                    />;
            }, this);
            return (
                <div style={{
                    padding: 0,
                    direction : Identify.isRtl() ? 'rtl' : 'ltr',
                }}>
                    <div>
                        {categories}
                    </div>
                    {this.renderJs()}
                </div>
            )
        }
        return '';
    };

    renderMenuItem = (cate_name,location) => {
        const {classes} = this.props
        return(
            <div 
                role="presentation" 
                key={Identify.randomString(10)}
                style={{color:configColor.menu_text_color, textTransform:'uppercase'}} 
                onClick={()=>this.openLocation(location)}
                className={`${classes['cate-child-item']}`}>
                <div style = {{color:configColor.menu_text_color}} >{cate_name}</div>
            </div>
        )
    }

    handleToggleMenu = (id) => {
        const {classes} = this.props
        const cate = $('.cate-'+id);
        $('.sub-cate-'+id).slideToggle('fast');
        cate.find(`.${classes['cate-icon']}`).toggleClass('hidden')
    };

    renderJs = ()=>{
        $(function () {
            if(Identify.isRtl()){
                $('div.menu-cate-name-item').each(function () {
                    const parent = $(this).parent();
                    const margin = parent.css('margin-left');
                    parent.css({
                        'margin-left' : 0,
                        'margin-right' : margin
                    })
                });
            }
        })
    }

    render(){
        const {props} = this
        const { classes, hideHeader } = props
        const hanndleClick = ()=>{
            $('.'+ classes["cate-icon-down"]).toggleClass('hidden')
            $('.'+classes["cate-icon-up"]).toggleClass('hidden')
        }
        const storeConfig = Identify.getStoreConfig();
        if (!storeConfig || !storeConfig.simiRootCate)
            return <div></div>
        this.renderedOnce = false;
        const primarytext = hideHeader?'':(
            <div className={classes["menu-content"]} 
                id="cate-tree"
                onClick={()=>   hanndleClick()}
            >
                {/* <div className={classes["icon-menu"]}>
                    <CateIcon style={{fill:configColor.menu_icon_color, width: 18, height: 18}}/>
                </div> */}
                <div className={classes["menu-title"]}
                        style={{color:configColor.menu_text_color}}>
                    {Identify.__('ALL CATEGORIES')}</div>
                <div className={`${classes["cate-icon-down"]} appear`} style={{display:'block', color:configColor.menu_text_color}}>
                    <div className={`${classes["icon-down"]}`}></div> 
                </div> 
                <div className={`${classes["cate-icon-up"]} hidden`} style={{display:'block', color:configColor.menu_text_color}}>
                    <div className={`${classes['icon-up']}`}></div>
                </div> 

            </div>
        )
        return (
            <div style={{paddingTop:'7px'}}>
                <div 
                    role="presentation"
                    className={`${classes["cate-root"]} ${classes["cate-parent-item"]}`} 
                    onClick={()=>this.handleToggleMenu('root')}
                    style={{color:configColor.menu_text_color}}>
                    <div style={{color:configColor.menu_text_color}}>
                        {primarytext}
                    </div>
                </div>
                <div className="sub-cate-root" style={{display:'none'}}>
                    {this.renderTreeMenu(storeConfig.simiRootCate)}
                </div>
            </div>
        ) 
    }
}

export default CateTree