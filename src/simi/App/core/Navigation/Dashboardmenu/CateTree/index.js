import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import {configColor} from 'src/simi/Config';
import CateIcon from 'src/simi/BaseComponents/Icon/TapitaIcons/List'
import SubCate from "./Subcate";
import ExpandLess from "src/simi/BaseComponents/Icon/TapitaIcons/ArrowUp";
import ExpandMore from "src/simi/BaseComponents/Icon/TapitaIcons/ArrowDown";
import navigationMenu from 'src/simi/queries/getCateTree.graphql';
import { Simiquery } from 'src/simi/Network/Query'

const cateUrlSuffix = '.html';

class CateTree extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            treecate : null,
            loaded : false,
            open:false,
        }
    }

    shouldComponentUpdate(nextProps,nextState){
        return nextState.loaded !== this.state.loaded
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
            const categories = data.category.children.map(function (item,key) {
                if (!item.name)
                    return ''
                const cate_name = <div className={classes["root-menu"]} >{obj.renderTitleMenu(item.name)}</div>;
                const hasChild = (item.children && item.children.length > 0)
                let location = {
                    pathname: item.url_path !== undefined ? "/" + item.url_path + cateUrlSuffix :  "/products.html?cat=" + item.id,
                    state: {
                        cate_id: item.id,
                        hasChild: hasChild,
                        name: item.name
                    }
                };
                location = !item.children ? location : null;
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
                style={{color:configColor.menu_text_color}} 
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
        const {rootCategoryId, classes} = props
        return (
            <Simiquery query={navigationMenu} variables={{ id: rootCategoryId }}>
                {({ loading, error, data }) => {
                    if (loading || error || (data && !data.category)) return '';
                    
                    const primarytext = (
                        <div className={classes["menu-content"]} id="cate-tree">
                            <div className={classes["icon-menu"]}>
                                <CateIcon style={{fill:configColor.menu_icon_color, width: 18, height: 18}}/>
                            </div>
                            <div className={classes["menu-title"]}
                                 style={{color:configColor.menu_text_color}}>
                                {Identify.__('Categories')}</div>
                        </div>
                    )
                    return (
                        <div >
                            <div 
                                role="presentation"
                                className={`${classes["cate-root"]} ${classes["cate-parent-item"]}`} 
                                onClick={()=>this.handleToggleMenu('root')}
                                style={{color:configColor.menu_text_color}}>
                                <div style={{color:configColor.menu_text_color}}>
                                    {primarytext}
                                </div>
                                <div className={`${classes["cate-icon"]} hidden`}>
                                    <ExpandLess className={`${classes["cate-icon"]} hidden`} color={configColor.menu_text_color}/>
                                </div>
                                <div className={`${classes["cate-icon"]}`}>
                                    <ExpandMore className={classes['cate-icon']} color={configColor.menu_text_color}/>
                                </div>
                            </div>
                            <div className="sub-cate-root" style={{display:'none'}}>
                                {this.renderTreeMenu(data)}
                            </div>
                        </div>
                    )
                }}
            </Simiquery>   
        )
    }
}

export default CateTree