import React from 'react'
import {defaultStyle} from './Consts'

const FiveStars = props => {
    const color = props.color ? {fill: props.color} : {};
    const style = {...defaultStyle, ...props.style, ...color, display: 'inline-flex', justifyContent: 'space-between'}
    const svgWidth = style.height ? style.height : '16px';

    return (
        <span className={props.className} style={style}>
            <svg xmlns="http://www.w3.org/2000/svg" style={{fill: style.fill, width: svgWidth, height: style.height}} viewBox="0 0 24 24">
                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" style={{fill: style.fill, width: svgWidth, height: style.height}} viewBox="0 0 24 24">
                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" style={{fill: style.fill, width: svgWidth, height: style.height}} viewBox="0 0 24 24">
                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" style={{fill: style.fill, width: svgWidth, height: style.height}} viewBox="0 0 24 24">
                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" style={{fill: style.fill, width: svgWidth, height: style.height}} viewBox="0 0 24 24">
                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
            </svg>
        </span>
    )
}
export default FiveStars