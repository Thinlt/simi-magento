import React from 'react'
import {defaultStyle} from './Consts'

const EnvelopeOpen = props => {
    const color = props.color ? {fill: props.color} : {};
    const style = {...defaultStyle, ...props.style, ...color}
    const {className} = props;
    return (
        <svg className={className || 'envelope-icon'} style={style} version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path d="M17.907 5.552l-7.293-4.457c-0.304-0.186-0.7-0.288-1.114-0.288s-0.81 0.102-1.114 0.288l-7.293 4.457c-0.623 0.381-1.093 1.218-1.093 1.948v9c0 0.827 0.673 1.5 1.5 1.5h16c0.827 0 1.5-0.673 1.5-1.5v-9c0-0.73-0.47-1.567-1.093-1.948zM1.614 6.405l7.293-4.457c0.147-0.090 0.363-0.142 0.593-0.142s0.445 0.052 0.593 0.142l7.293 4.457c0.155 0.095 0.304 0.266 0.416 0.459l-7.747 5.165c-0.29 0.193-0.819 0.193-1.109 0l-7.747-5.165c0.112-0.194 0.261-0.365 0.416-0.459zM17.5 17h-16c-0.276 0-0.5-0.224-0.5-0.5v-8.566l7.391 4.927c0.311 0.207 0.71 0.311 1.109 0.311s0.798-0.104 1.109-0.311l7.391-4.927v8.566c0 0.276-0.224 0.5-0.5 0.5z"></path>
        </svg>
    );
}
export default EnvelopeOpen