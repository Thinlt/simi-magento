import React from 'react'
import {defaultStyle} from './Consts'

const Close = props => {
    const color = props.color ? {fill: props.color} : {};
    const style = {...defaultStyle, ...props.style, ...color}

    return (
        <svg className={props.className} style={style} version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <title>Play circle</title>
            <path d="M10.707 10.5l8.646-8.646c0.195-0.195 0.195-0.512 0-0.707s-0.512-0.195-0.707 0l-8.646 8.646-8.646-8.646c-0.195-0.195-0.512-0.195-0.707 0s-0.195 0.512 0 0.707l8.646 8.646-8.646 8.646c-0.195 0.195-0.195 0.512 0 0.707 0.098 0.098 0.226 0.146 0.354 0.146s0.256-0.049 0.354-0.146l8.646-8.646 8.646 8.646c0.098 0.098 0.226 0.146 0.354 0.146s0.256-0.049 0.354-0.146c0.195-0.195 0.195-0.512 0-0.707l-8.646-8.646z"></path>
        </svg>
    )
}
export default Close