import React, {useReducer, useMemo} from 'react';
import Identify from 'src/simi/Helper/Identify';
import Pagination from './pagination';
import ListItem from './listItem';
// import Loading from "src/simi/BaseComponents/Loading";

const List = props => {
    const { items } = props;

    const editAddressHandle = (id) => {
        props.editAddress(id);
    }

    const deleteAddressHandle = (id) => {
        props.deleteAddress(id);
    }

    const renderItems = (itemsRender) => {
        let rendering = items
        if (typeof itemsRender !== 'indefined') {
            rendering = itemsRender
        }
        return rendering.map((item, index) => {
            return <ListItem data={item} key={index} editAddress={editAddressHandle} deleteAddress={() => deleteAddressHandle(index)}/>
        })
    }

    const reducer = (state, action) => {
        return {...state, ...{dataItems: action.items}}
    }

    const [state, dispatch] = useReducer(reducer, {dataItems: items});
    const memoizedItems = useMemo(() => renderItems(state.dataItems), [state]);

    const renderPagination = () => {
        if (items.length < 1) {
            return null
        }
        return <Pagination dispatch={dispatch} dataItems={items} pageSize={10}/>
    }

    return (
        <div className="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th className="col firstname">{Identify.__("First Name")}</th>
                        <th className="col lastname">{Identify.__("Last Name")}</th>
                        <th className="col streetaddress">{Identify.__("Street Address")}</th>
                        <th className="col city">{Identify.__("City")}</th>
                        <th className="col country">{Identify.__("Country")}</th>
                        <th className="col state">{Identify.__("State")}</th>
                        <th className="col zip">{Identify.__("Zip/Postal Code")}</th>
                        <th className="col phone">{Identify.__("Phone")}</th>
                        <th className="col actions"></th>
                    </tr>
                </thead>
                <tbody>{memoizedItems}</tbody>
            </table>
            {renderPagination()}
        </div>
    );
}

export default List;
