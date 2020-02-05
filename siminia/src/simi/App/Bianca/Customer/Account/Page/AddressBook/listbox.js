import React, {useReducer, useCallback} from 'react';
import Identify from 'src/simi/Helper/Identify';
import Pagination from './pagination';
import ListItem from './listboxItem';

const List = props => {
    const { items, address_fields_config, address_option } = props;
    const addressConfig = address_fields_config;

    const editAddressHandle = (id) => {
        props.editAddress(id);
    }

    const deleteAddressHandle = (id) => {
        props.mutaionCallback({ variables: {id: id}});
        props.dispatchDelete(id);
        dispatch({dataItems: items});
    }

    const renderItems = (itemsRender) => {
        let rendering = items;
        if (typeof itemsRender !== 'undefined') {
            rendering = itemsRender;
        }
        return rendering.map((item, index) => {
            item.index = index; // add index of array to item
            return <ListItem data={item} editAddress={editAddressHandle} deleteAddress={deleteAddressHandle} key={index}
                address_fields_config={addressConfig} address_option={address_option}
            />
        })
    }

    const reducer = (state, action) => {
        return {...state, ...{dataItems: action.items}}
    }
    const reducerMemoized = useCallback(reducer, [items]);
    const [state, dispatch] = useReducer(reducerMemoized, {dataItems: items});

    const renderPagination = () => {
        if (items.length < 1) {
            return null
        }
        return <Pagination className="pagination" dispatch={dispatch} dataItems={items} pageSize={10}/>
    }

    if (!items) {
        return null
    }

    return (
        <div className="address-content">
            {renderItems(items)}
        </div>
    );
}

export default List;
