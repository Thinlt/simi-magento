import React from 'react';
import Pagination from '../../Components/Pagination';
import PaginationTable from '../../Components/PaginationTable';
import Identify from 'src/simi/Helper/Identify';

const SizeChart = props => {
    const { isPhone } = props;
    const { items } = props.data;
    const cols =
        [
            { title: Identify.__("Product name"), width: "14.02%" },
            { title: Identify.__("Bust (in cm)"), width: "15.67%" },
            // { title: Identify.__("Ship to"), width: "33.40%" },
            { title: Identify.__("Waist (in cm)"), width: "12.06%" },
            { title: Identify.__("Hip (in cm)"), width: "12.58%" },
        ];

    const renderSizeChart = (item, index) => {
        // render on mobile nontable
        if (isPhone) {
            return (
                <div className="item">
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Product name")}</div>
                        <div className="item-value">{Identify.__(`${item.product_name}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Bust (in cm)")}</div>
                        <div className="item-value">{Identify.__(`${item.bust}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Waist (in cm)")}</div>
                        <div className="item-value">{Identify.__(`${item.waist}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Hip (in cm)")}</div>
                        <div className="item-value">{`${Identify.__(item.hip)}"`}</div>
                    </div>
                </div>
            );
        }
        // on desktop
        return (
            <tr key={index}>
                <td data-title={Identify.__("Product name")}>
                    {Identify.__(item.product_name)}
                </td>
                <td
                    data-title={Identify.__("Bust (in cm)")}
                >
                    {`${Identify.__(item.bust)}"`}
                </td>
                <td data-title={Identify.__("Waist (in cm)")}>
                    {`${Identify.__(item.waist)}"`}
                </td>
                <td className="order-status" data-title={Identify.__("Hip (in cm)")}>
                    {`${Identify.__(item.hip)}"`}
                </td>
            </tr>
        )
    }

    return (
        <div>
            {isPhone ?
                <Pagination
                    renderItem={renderSizeChart}
                    cols={cols}
                    data={items}
                /> :
                <PaginationTable
                    renderItem={renderSizeChart}
                    cols={cols}
                    data={items}
                />
            }
        </div>
    );
};

export default SizeChart;
