import React from 'react'
import {defaultStyle} from './Consts'

const Bubble = props => {
    const color = props.color ? {fill: props.color} : {};
    const style = {...defaultStyle, ...props.style, ...color}
    const {className} = props;
    return (
        <svg className={className || 'bubble-icon'} style={style} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 896">
            <path d="M512 64q121 0 224.5 43T900 223.5T960 384t-60 160.5T736.5 661T512 704h-27q-14 0-24-.5t-23-2.5l-35-5l-23 28q-5 5-16.5 14.5T336 760t-38 25.5t-43 23.5q10-25 16-51t6-50l1-2v-45l-35-18q-85-43-132-111T64 384q0-87 60-160.5T287.5 107T512 64zm0-64Q373 0 255 51.5t-186.5 140T0 384q0 99 56 181.5T214 700q0 1-.5 1.5t-.5 1.5q0 65-51 153q-2 5-2 11q0 12 8.5 20.5T189 896h8q46-8 99-36t86.5-54t46.5-42q26 4 56 4h27q139 0 257-51.5t186.5-140T1024 384q0-141-122-249Q775 24 583 4q-36-4-71-4z"/>
        </svg>
    );
}
export default Bubble
