/**
 * Created by codynguyen on 7/10/18.
 */
import React, {Component} from 'react';
// import {configColor} from "src/simi/Config";
import PropTypes from 'prop-types';
import defaultClasses from './menuitem.css'
import { mergeClasses } from 'src/classify'

class MenuItem extends Component {
    render() {
        const {title,icon,divider,menuStyle,iconStyle,titleStyle,classes} = this.props;
        const mergedClasses = mergeClasses(classes, defaultClasses)
        return (
            <div className={`menu-item-wrap ${divider?mergedClasses['divider']:''} menu-item`}>
                <div role="presentation" style={menuStyle} onClick={this.props.onClick}>
                    <div className={'menu-content'}>
                        {
                            icon && 
                            <div className={'icon-menu'} style={iconStyle}>
                                {icon}
                            </div>
                        }
                        <div className={'menu-title'} style={titleStyle}>
                            {title}
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}
MenuItem.defaultProps = {
    title : '',
    icon : '',
    divider : true,
    menuStyle : {},
    titleStyle : {},
    iconStyle : {},
    onClick : function () {}
}
MenuItem.propsTypes = {
    classes: PropTypes.object.isRequired,
    title: PropTypes.oneOfType([
        PropTypes.element,
        PropTypes.node,
        PropTypes.string,
    ]),
    icon: PropTypes.oneOfType([
        PropTypes.element,
        PropTypes.node,
        PropTypes.string,
    ]),
    divider: PropTypes.bool,
    menuStyle : PropTypes.object,
    titleStyle : PropTypes.object,
    iconStyle : PropTypes.object,
    onClick : PropTypes.func
}
export default MenuItem;