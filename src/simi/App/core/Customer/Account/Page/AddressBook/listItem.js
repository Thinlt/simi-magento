import React from 'react';
import Identify from 'src/simi/Helper/Identify';

const ListItem = props => {

    const editAddressHandle = (id) => {
        props.editAddress(id);
    }

    const deleteAddressHandle = (id) => {
        props.deleteAddress(id);
    }

    const { data } = props;
    const { id } = data;

    return (
        <tr>
            <td data-th={Identify.__("First Name")}>{data.firstname}</td>
            <td data-th={Identify.__("Last Name")}>{data.lastname}</td>
            <td data-th={Identify.__("Street Address")}>{data.street}</td>
            <td data-th={Identify.__("City")}>{data.city}</td>
            <td data-th={Identify.__("Country")}>{data.country}</td>
            <td data-th={Identify.__("State")}>{data.region_code}</td>
            <td data-th={Identify.__("Zip/Postal Code")}>{data.postcode}</td>
            <td data-th={Identify.__("Phone")}>{data.telephone}</td>
            <td data-th={Identify.__("Actions")}>
                <a href="" onClick={e => {e.preventDefault(); editAddressHandle(id)}}>{Identify.__("Edit")}</a>
                |
                <a href="" onClick={e => {e.preventDefault(); deleteAddressHandle(id)}}>{Identify.__("Delete")}</a>
            </td>
        </tr>
    );
}

export default ListItem;
