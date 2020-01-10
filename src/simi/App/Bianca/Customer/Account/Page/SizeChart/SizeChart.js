import React from 'react';
import PaginationTable from '../../Components/Orders/PaginationTable';
import Identify from 'src/simi/Helper/Identify';

const SizeChart = props => {
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
            <PaginationTable
                renderItem={renderSizeChart}
                cols={cols}
                data={items}
            />
        </div>
    );
};

export default SizeChart;
