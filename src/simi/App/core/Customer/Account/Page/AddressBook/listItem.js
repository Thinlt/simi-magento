import React, { useCallback, useMemo } from 'react';
import Identify from 'src/simi/Helper/Identify';

const ListItem = props => {

    const { data, classes, address_fields_config } = props;
    const { id } = data;

    const deleteCallback = useCallback((e) => {
        e.preventDefault();
        props.deleteAddress(id);
    }, [id]);

    const editAddressHandle = (id) => {
        props.editAddress(id);
    }

    return (
        <tr>
            <td data-th={Identify.__("First Name")}>{data.firstname}</td>
            <td data-th={Identify.__("Last Name")}>{data.lastname}</td>
            {address_fields_config.street_show ?  
                <td data-th={Identify.__("Street Address")}>{data.street}</td>
                : null
            }
            {address_fields_config.city_show ?  
                <td data-th={Identify.__("City")}>{data.city}</td>
                : null
            }
            {address_fields_config.country_id_show ?  
                <td data-th={Identify.__("Country")}>{data.country}</td>
                : null
            }
            {address_fields_config.region_id_show ?  
                <td data-th={Identify.__("State")}>{data.region_code}</td>
                : null
            }
            {address_fields_config.zipcode_show ?  
                <td data-th={Identify.__("Zip/Postal Code")}>{data.postcode}</td>
                : null
            }
            {address_fields_config.telephone_show ?  
                <td data-th={Identify.__("Phone")}>{data.telephone}</td>
                : null
            }
            <td data-th={Identify.__("Actions")}>
                <a className={classes["edit"]} href="" onClick={e => {e.preventDefault(); editAddressHandle(id)}}>{Identify.__("Edit")}</a>
                |
                <a className={classes["delete"]} href="" onClick={deleteCallback}>{Identify.__("Delete")}</a>
            </td>
        </tr>
    );
}

export default ListItem;
