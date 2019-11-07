import React from 'react'
import {defaultStyle} from './Consts'

const Question = props => {
    const color = props.color ? {fill: props.color} : {};
    const style = {...defaultStyle, ...props.style, ...color}

    return (
        <svg className={props.className} style={style} version="1.1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20">
            <title>Question</title>
            <path d="M9.5 17c-0.276 0-0.5-0.224-0.5-0.5v-3c0-0.276 0.224-0.5 0.5-0.5 3.033 0 5.5-2.467 5.5-5.5s-2.467-5.5-5.5-5.5-5.5 2.467-5.5 5.5c0 0.276-0.224 0.5-0.5 0.5s-0.5-0.224-0.5-0.5c0-3.584 2.916-6.5 6.5-6.5s6.5 2.916 6.5 6.5c0 3.416-2.649 6.225-6 6.481v2.519c0 0.276-0.224 0.5-0.5 0.5z"></path>
            <path d="M9.5 20c-0.276 0-0.5-0.224-0.5-0.5v-1c0-0.276 0.224-0.5 0.5-0.5s0.5 0.224 0.5 0.5v1c0 0.276-0.224 0.5-0.5 0.5z"></path>
        </svg>
    );
}
export default Question