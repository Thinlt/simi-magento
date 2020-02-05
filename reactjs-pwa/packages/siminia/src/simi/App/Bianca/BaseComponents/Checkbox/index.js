import React from 'react';
import Identify from 'src/simi/Helper/Identify';
require ('./index.scss')

export const Checkbox = (props) => {

    const handleOnChange = (e) => {
        if (props.onChange) {
            props.onChange(e.target.value);
        }
    }

    return (
        <div 
            {...props}
            className={`checkbox-item ${props.className} ${props.selected?'selected':''}`}
        >
            <div className="checkbox-item-icon">
                <div className={`${props.selected?'selected':''}`}></div>
            </div>
            <span className="checkbox-item-text">
                {props.label}
            </span>
            <input onChange={handleOnChange} name={props.name || Identify.randomString(3)} style={{display: 'none'}} type="radio" value={props.value} checked={props.selected}/>
        </div>
    )
}
export default Checkbox