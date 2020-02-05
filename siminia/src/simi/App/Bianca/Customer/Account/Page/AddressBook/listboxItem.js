import React, { useCallback } from 'react';
import Identify from 'src/simi/Helper/Identify';
import Pencil from 'src/simi/BaseComponents/Icon/Pencil';
import Trash from 'src/simi/App/Bianca/BaseComponents/Icon/Trash';

const ListItem = props => {

    const { data, address_fields_config } = props;
    const addressConfig = address_fields_config;
    const { id } = data;

    const deleteCallback = useCallback((e) => {
        e.preventDefault();
        if (confirm(Identify.__("Are you sure?"))) {
            props.deleteAddress(id);
        }
    }, [id]);

    const editAddressHandle = (e) => {
        e.preventDefault();
        props.editAddress(id);
    }

    return (
        <div className="address-box dash-column-box additional-address">
            <div className="white-box-content">
                <div className="box-content">
                    <address>
                        {data.firstname} {data.lastname}<br/>
                        {(!addressConfig || addressConfig && addressConfig.street_show) && data.street ? 
                            <>{data.street.map((address, index) => {
                            return <React.Fragment key={index}>{address}<br/></React.Fragment>;
                        })}</> : ''}
                        {(!addressConfig || addressConfig && addressConfig.zipcode_show) && data.postcode ? <>{data.postcode}, </> : ''}
                        {(!addressConfig || addressConfig && addressConfig.city_show) && data.city ? <>{data.city}, </>: ''}
                        {(!addressConfig || addressConfig && addressConfig.region_id_show) && data.region ? <>{data.region.region_code}<br/></>: ''}
                        {(!addressConfig || addressConfig && addressConfig.country_id_show) ? <>{data.country}<br/></> : ''}
                        {(!addressConfig || addressConfig && addressConfig.telephone_show) && data.telephone && 
                            <>
                                T: <a href={"tel:"+data.telephone}>{data.telephone}</a>
                            </>
                        }
                    </address>
                </div>
                <div className="box-action">
                    <div className="address-action address-edit" onClick={editAddressHandle}>
                        <Pencil style={{width: '16px', height: '16px'}}/>
                        <div>{Identify.__("Edit".toUpperCase())}</div>
                    </div>
                    <div className="address-action address-remove" onClick={deleteCallback}>
                        <Trash style={{width: '16px', height: '16px'}}/>
                        <div>{Identify.__("Remove".toUpperCase())}</div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default ListItem;
