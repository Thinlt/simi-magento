import React from 'react'
import {defaultStyle} from './Consts'

const ArrowDown = props => {
    const color = props.color ? {fill: props.color} : {};
    const style = {...defaultStyle, ...props.style, ...color}
    const {className} = props;
    return (
        <svg className={className || 'telephone-icon'} style={style} version="1.1" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
            <path d="M0 6c0-0.128 0.049-0.256 0.146-0.354 0.195-0.195 0.512-0.195 0.707 0l8.646 8.646 8.646-8.646c0.195-0.195 0.512-0.195 0.707 0s0.195 0.512 0 0.707l-9 9c-0.195 0.195-0.512 0.195-0.707 0l-9-9c-0.098-0.098-0.146-0.226-0.146-0.354z"></path>
        </svg>
    );
}
export default ArrowDown
