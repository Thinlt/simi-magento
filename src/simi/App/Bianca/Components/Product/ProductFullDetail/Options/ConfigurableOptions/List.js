import React, { useCallback, useState } from 'react';
import { arrayOf, object } from 'prop-types';


const List = (props) => {
    
    const {items, getItemKey, onSelectionChange, attribute_code, defaultSelection} = props;

    const [selectedKey, setSelectedKey] = useState(defaultSelection);
    const [focused, setFocused] = useState();

    const clickCallback = useCallback((key, value) => {
        onSelectionChange(new Map().set(key, value));
        setSelectedKey(key);
    });

    const handleFocus = (key) => {
        setFocused(key);
    }

    const isSelected = (key) => key === selectedKey;
    const hasFocus = (key) => key === focused;

    const getClassName = (name, isSelected, _hasFocus) => `${name} ${isSelected ? '_selected' : ''} ${_hasFocus ? '_focused' : ''}`;

    const childrens = items.map((item, index) => {
        const {label} = item;
        const key = getItemKey && getItemKey(item, index) || index;
        
        return (
            <li className={getClassName('list-item', isSelected(key), hasFocus(key))} 
                onFocus={() => handleFocus(key)}
                onClick={() => clickCallback(key, label)} key={key}>
                <span>{label}</span>
            </li>
        );
    });
    
    return (
        <ul className={`option-${attribute_code}`}>{childrens}</ul>
    );
}

List.propTypes = {
    items: arrayOf(object)
};


export default List;
