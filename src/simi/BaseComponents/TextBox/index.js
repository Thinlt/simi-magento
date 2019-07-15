import React from 'react';
import './style.css'
import Identify from "src/simi/Helper/Identify";
import classify from 'src/classify';
import defaultClasses from './style.css';
import PropTypes from 'prop-types';

const TextBox = props => {
    const { classes } = props;
    let className = 'form-control base-textField ';
    let label = props.label;
    let id = '';
    if(props.className) className = className + props.className;
    if(props.required)  className = className + ' required'
    if (props.id) id = props.id;

    return (
        <div className={`form-group ${props.name}`} key={props.name}>
            {props.label && <label htmlFor={id}>{label} {props.required ? <span>*</span> : ''}</label>}
            <div className={classes["input-group-field"]}>
                <div className={classes["input-field-content"]}>
                    <input {...props}
                           type={props.type ? props.type : "text"}
                           className={className} required={props.required}/>
                    {props.icon && <div className={classes["input-icon"]}>{props.icon}</div>}
                </div>
                {props.error && <div className={`${classes["error-message"]}  error-input-${props.name}`} style={{marginTop:5}}>{props.error}</div>}
            </div>

            <div className="other-component">{props.other}</div>
        </div>
    )
}

TextBox.propTypes = {
    id: PropTypes.oneOfType([
        PropTypes.string,
        PropTypes.number
    ]),
    error: PropTypes.string,  
    label: PropTypes.string,
    className: PropTypes.string,
    required: PropTypes.bool,
    type: PropTypes.string,
    icon: PropTypes.node,
    other: PropTypes.node
}

export default classify(defaultClasses)(TextBox);
