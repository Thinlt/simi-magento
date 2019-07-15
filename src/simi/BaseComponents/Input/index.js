import React from 'react';
import {configColor} from 'src/simi/Config';
import Identify from 'src/simi/Helper/Identify';
import PropTypes from 'prop-types';
export const Qty = (props)=>{
    const {classes} = props
    let style = {
        border: '1px solid ' + configColor.button_background,
        padding: 0,
        borderRadius : '5px',
        height : 36,
        fontSize : 15,
        fontWeight : 600,
        direction : 'ltr'
    };
    style = {...style,...props.inputStyle};
    console.log(props)
    let className = classes['option-number']?classes['option-number']:''
    className +=  " " + props.className
    const value = props.value ? parseInt(props.value,10) : 1;
    return (
        <input
            min={1}
            type="number"
            defaultValue={value}
            pattern="[1-9]*"
            className={className}
            style={style}
            data-id={props.dataId}
            onChange={props.onChange?props.onChange:null}
            disabled={props.disabled}
        />
    )
};

Qty.propTypes = {
    className: PropTypes.string,
    dataId: PropTypes.number,
    value: PropTypes.number,
    classes: PropTypes.object,
    onChange: PropTypes.func,
}

Qty.defaultProps = {
    className: '',
    value: 1,
    classes: {}
}

export const InputField = (props)=>{
    const {classes} = props
    const type = props.type ? props.type : 'text';
    const name = props.name ? props.name : '';
    const labelStyle = {
        display: 'block',
        paddingBottom: 5,
        fontWeight: 600,
        fontSize : 16
    }
    const label = props.label ?
        <label htmlFor={name} style={labelStyle}>{Identify.__(props.label)}
            <span style={{marginLeft : 5,color : 'red'}} className={classes['required-label']}>
                {props.required ? props.required : ''}
            </span>
        </label>
        : null;
    const value = props.value ? props.value : '';
    const className = props.className ? props.className : '';
    const inputStyle = {
        width: '100%',
        borderRadius: 5,
        border: 'none',
        background: '#f2f2f2',
        height: 40,
        paddingLeft: 16,
    }
    return (
        <div
            className={classes['input-field']}
            key={Identify.makeid()}
            style={{marginBottom : 15}}>
            {label}
            <input
                type={type}
                name={name}
                id={name}
                className={className}
                defaultValue={value}
                style={{...inputStyle,...props.style}}
                onChange={props.onChange?props.onChange:null}
                {...props.input}
            />
            <div className={classes["error-message"]}>
                {Identify.__('This field is required')}
            </div>
        </div>
    )
}