import React, {useState} from 'react';
// import Loading from "src/simi/BaseComponents/Loading";
import Identify from 'src/simi/Helper/Identify'
import { formatPrice } from 'src/simi/Helper/Pricing';
import Pagination from '../Pagination';
import PaginationTable from '../PaginationTable';
import { Link } from 'react-router-dom';
import defaultClasses from './style.scss'
import classify from "src/classify";
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { connect } from 'src/drivers';
import { compose } from 'redux';

const OrderList = props => {
    const { showForDashboard, data } = props
    const [limit, setLimit] = useState(10);
    const [title, setTitle] = useState(10)
    const cols =
        [
            { title: Identify.__("Order #"), width: "14.02%" },
            { title: Identify.__("Date"), width: "15.67%" },
            { title: Identify.__("Total"), width: "12.06%" },
            { title: Identify.__("Status"), width: "12.58%" },
            { title: Identify.__("Action"), width: "12.27%" },
        ];

    const currentPage = 1;

    const renderOrderItem = (item, index) => {
        let date = Date.parse(item.created_at);
        date = new Date(date);
        let m = date.getMonth() + 1;
        m = m < 10 ? "0" + m : m;
        date = date.getDate() + "/" + m + "/" + date.getFullYear();
        const location = {
            pathname: "/orderdetails.html/" + item.increment_id,
            state: { orderData: item }
        };
        // render on mobile nonetable
        if (props.isPhone) {
            return (
                <div className="item">
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Order #")}</div>
                        <div className="item-value">{Identify.__(item.increment_id)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Date")}</div>
                        <div className="item-value">{date}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Total")}</div>
                        <div className="item-value">{formatPrice(parseFloat(item.grand_total))}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Status")}</div>
                        <div className="item-value">{item.status}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Action")}</div>
                        <div className="item-value">
                            <Link className="view-order" to={location}>{Identify.__('View Order')}</Link>
                        </div>
                    </div>
                </div>
            );
        }
        // on desktop
        return (
            <tr key={index}>
                <td data-title={Identify.__("Order #")}>
                    {Identify.__(item.increment_id)}
                </td>
                <td
                    data-title={Identify.__("Date")}
                >
                    {date}
                </td>
                <td data-title={Identify.__("Total")}>
                    {formatPrice(parseFloat(item.grand_total))}
                </td>
                <td className="order-status" data-title={Identify.__("Status")}>
                    {item.status}
                </td>
                <td data-title="">
                    <Link className="view-order" to={location}>{Identify.__('View Order')}</Link>
                </td>
            </tr>
        )
    }
    let listOrder = [];
    if(data){
        listOrder = data.sort((a,b)=>{
            return  (b.entity_id)? (b.entity_id - a.entity_id):(b.id - a.id)
        })
    }
    return (
        <div className='customer-recent-orders'>
            {!data|| data.length === 0
                ? (
                    <div className="text-center">
                        {Identify.__("You have no items in your order")}
                    </div>
                ) : (
                    props.isPhone ? 
                    <Pagination
                        renderItem={renderOrderItem}
                        data={showForDashboard ? listOrder.slice(0, 5) : listOrder}
                        cols={cols}
                        showPageNumber={!showForDashboard}
                        limit={typeof(limit) === 'string' ? parseInt(limit): limit}
                        setLimit={setLimit}
                        currentPage={currentPage}
                        title={title}
                        setTitle={setTitle}
                    /> :
                    <PaginationTable
                        renderItem={renderOrderItem}
                        data={showForDashboard ? listOrder.slice(0, 5) : listOrder}
                        cols={cols}
                        showPageNumber={!showForDashboard}
                        limit={typeof(limit) === 'string' ? parseInt(limit): limit}
                        setLimit={setLimit}
                        currentPage={currentPage}
                        title={title}
                        setTitle={setTitle}
                    />
                )
            }
        </div>
    )
}

const mapDispatchToProps = {
    toggleMessages,
}

export default compose(
    classify(defaultClasses),
    connect(
        null,
        mapDispatchToProps
    )
) (OrderList);