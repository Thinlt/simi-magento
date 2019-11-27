import React from 'react'
import {defaultStyle} from './Consts'

const ChevronLeft = props => {
    const color = props.color ? {fill: props.color} : {fill: "#000000"};
    const style = {...defaultStyle, ...props.style, ...color}
    const {className} = props;
    
    return (
        <svg className={className || 'chevron-left'} style={style} version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path d="M14 20c0.128 0 0.256-0.049 0.354-0.146 0.195-0.195 0.195-0.512 0-0.707l-8.646-8.646 8.646-8.646c0.195-0.195 0.195-0.512 0-0.707s-0.512-0.195-0.707 0l-9 9c-0.195 0.195-0.195 0.512 0 0.707l9 9c0.098 0.098 0.226 0.146 0.354 0.146z"></path>
        </svg>
    );
}
export default ChevronLeft