import React, { useEffect, useState } from 'react';
import PaginationTable from '../../Components/Orders/PaginationTable';
import TextBox from 'src/simi/BaseComponents/TextBox';
import Identify from 'src/simi/Helper/Identify';
import { Colorbtn } from 'src/simi/BaseComponents/Button';

const GiftVouchers = props => {

    const cols =
        [
            { title: Identify.__("Code"), width: "14.02%" },
            { title: Identify.__("Added Date"), width: "15.67%" },
            // { title: Identify.__("Ship to"), width: "33.40%" },
            { title: Identify.__("Expired Date"), width: "12.06%" },
            { title: Identify.__("Balance"), width: "12.58%" },
            { title: Identify.__("Status"), width: "12.27%" },
            { title: Identify.__("Action"), width: "12.27%" }
        ];

    const renderListVoucher = () => {

    }

    return (
        <div>
            <form className="form-add-voucher">
                <TextBox
                    label={Identify.__('ADD A GIFT VOUCHERS')}
                    name="voucher"
                    className="add-voucher"
                    placeholder="Specify Gift Code"
                    // onChange={handleOnChange}
                />
                <Colorbtn type="submit" 
                    className="add-voucher-btn" 
                    text={Identify.__("Add to my list")}
                />
            </form>

            <PaginationTable
                cols={cols}
            />
        </div>
    );
};

export default GiftVouchers;
