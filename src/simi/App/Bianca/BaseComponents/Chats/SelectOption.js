import React, {useState, useMemo, useEffect, useRef} from 'react';
import Identify from "src/simi/Helper/Identify";
require('./SelectOption.scss');

const Select = (props) => {
    const {items, isOpen, onChange, triggerRef, defaultValue, placeholder, className, display, hiddenInput} = props;
    const [isOpenDropDown, setIsOpenDropDown] = useState(isOpen || false);
    const [selected, setSelected] = useState(defaultValue);
    const [itemSelected, setItemSelected] = useState(null);
    const InputSelectRef = useRef(null);
    // const RandomId = Identify.randomString(3);


    useEffect(() => {
        setIsOpenDropDown(isOpen || false);
        setSelected(defaultValue);
    }, [isOpen, defaultValue]);

    const onChangeValue = (item, event) => {
        event.stopPropagation();
        if (onChange) {
            onChange(item.value);
        }
        setSelected(item.value);
        setItemSelected(item);
        setIsOpenDropDown(false);
    }

    const onClickOutside = (event) => {
        if (InputSelectRef && InputSelectRef.current && !InputSelectRef.current.contains(event.target)) {
            if (triggerRef){
                if(triggerRef.current && !triggerRef.current.contains(event.target)) {
                    setIsOpenDropDown(false);
                }
                return;
            }
            setIsOpenDropDown(false);
        }
    }

    const onClickTriggerRef = (event) => {
        if (triggerRef){
            setIsOpenDropDown(!isOpenDropDown);
        }
    }

    useEffect(() => {
        if (triggerRef && triggerRef.current) {
            triggerRef.current.addEventListener('mousedown', onClickTriggerRef, false)
        }
        document.addEventListener('mousedown', onClickOutside, false)
        return () => {
            if (triggerRef && triggerRef.current) {
                triggerRef.current.removeEventListener('mousedown', onClickTriggerRef, false)
            }
            document.removeEventListener('mousedown', onClickOutside, false)
        }
    }, []);

    const Options = useMemo(() => {
        let _items = []
        Object.values(items).forEach((item, index) => {
            _items.push(<span className={`input-option ${selected === item.value ? 'selected':''}`} 
                onClick={(e) => onChangeValue(item, e)} key={index}>{item.label}</span>
            );
        });
        return _items;
    } , [items, isOpen, defaultValue]);

    return (
        <React.Fragment>
        {
            display && 
            <div className="input-display">{itemSelected ? itemSelected.label : placeholder}</div>
        }
        {
            isOpenDropDown && 
            <div className={`${className ? className:'simi-simple-input-select'}`} ref={InputSelectRef}>{Options}</div>
        }
        {
            hiddenInput && typeof hiddenInput === 'object' ?
            <input type="hidden" {...hiddenInput} defaultValue={selected} /> :
            hiddenInput === true ?
            <input type="hidden" style={{display: "none"}} name="input-select" defaultValue={selected} /> : null
        }
        </React.Fragment>
    );
}
export default Select