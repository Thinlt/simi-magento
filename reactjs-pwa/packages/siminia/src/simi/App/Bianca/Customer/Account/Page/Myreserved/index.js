import React, { useState } from 'react';
import Identify from "src/simi/Helper/Identify";
import Loading from "src/simi/BaseComponents/Loading";
import TitleHelper from 'src/simi/Helper/TitleHelper';
import {getMyReserved, cancelMyReserved} from 'src/simi/Model/Customer';
import { showToastMessage } from 'src/simi/Helper/Message';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import Pagination from '../../Components/Pagination';
import PaginationTable from '../../Components/PaginationTable';

const Myreserved = props => {
    const {isPhone} = props;
    const [data, setData] = useState(null)
    const [curPage, setCurPage] = useState(1)
    if (!data) {
        getMyReserved((data) => setData(data));
    }
    if (!data || !data.items) {
        return <Loading />
    }

    const cols =
        [
            { title: Identify.__("Product name"), width: "14.02%" },
            { title: Identify.__("Date"), width: "12.06%" },
            { title: Identify.__("Store"), width: "15.67%" },
            { title: Identify.__("Status"), width: "12.58%" },
            { title: Identify.__("Action"), width: "12.27%" },
        ];

    const cancelReserved = (id) => {
        if(confirm(Identify.__('Are you sure?')) === true){
            showFogLoading();
            cancelMyReserved((data)=> {
                if (data.error) {
                    showToastMessage(Identify.__(data.error));
                }
                if (data.items) {
                    setData(data);
                }
                hideFogLoading();
            }, id);
            setTimeout(() => {
                hideFogLoading();
            }, 10000);
        }
    }

    const formatDate = (datestring) =>{
        let date = new Date(datestring)
        let m = date.getMonth() + 1;
        m = m < 10 ? "0"+m : m;
        if(Identify.isRtl()){
            date = date.getFullYear() + '/' + m + '/' + date.getDate() ;
            return date;
        }
        date = date.getDate() + '/' + m + '/' + date.getFullYear()
        return date;
    };

    const renderItem = (item, index) => {
        // render on mobile nontable
        if (isPhone) {
            return (
                <div className="item" key={index}>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Product name")}</div>
                        <div className="item-value">{Identify.__(`${item.product_name}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Date")}</div>
                        <div className="item-value">{formatDate(item.reservation_date)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Store")}</div>
                        <div className="item-value">{Identify.__(item.store_name)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Status")}</div>
                        <div className="item-value" key={item.status}>{Identify.__(item.status)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Action")}</div>
                        <div className="item-value">
                        {item.status && 
                            (!item.status.toLowerCase().includes('pending') ?
                                <div className={`action disabled`}>{Identify.__('Cancel')}</div>
                                :
                                <div className={`action`} onClick={() => cancelReserved(item.id)}>{Identify.__('Cancel')}</div>
                            )
                        }
                        </div>
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
                    data-title={Identify.__("Date")}
                >
                    {formatDate(item.reservation_date)}
                </td>
                <td data-title={Identify.__("Store")}>
                    {Identify.__(item.store_name)}
                </td>
                <td className="order-status" data-title={Identify.__("Status")} key={item.status}>
                    {Identify.__(item.status)}
                </td>
                {item.status ? 
                    (!item.status.toLowerCase().includes('pending')?
                        <td className={`action disabled`} data-title={Identify.__("Action")}>
                            {Identify.__('Cancel')}
                        </td> :
                        <td className={`action`} data-title={Identify.__("Action")} onClick={() => cancelReserved(item.id)}>
                            {Identify.__('Cancel')}
                        </td>
                    )
                    :
                    <td className={`action`} data-title={Identify.__("Action")}></td>
                }
            </tr>
        )
    }

    const {items} = data;
    
    return (
        <div className='account-my-reserved'>
            {TitleHelper.renderMetaHeader({
                title: Identify.__('My Reserved Products'),
                desc: Identify.__('My Reserved Products') 
            })}
            <div className="customer-page">
                <div className="customer-page-title">
                    {Identify.__("My Reserved Products")}
                </div>
                <div className='account-my-products'>
                    {isPhone ?
                    <Pagination
                        renderItem={renderItem}
                        cols={cols}
                        data={items}
                        key={Identify.randomString(3)}
                        currentPage={parseInt(curPage)}
                        changedPage={(id) => setCurPage(id)}
                    /> :
                    <PaginationTable
                        renderItem={renderItem}
                        cols={cols}
                        data={items}
                        key={Identify.randomString(3)}
                        currentPage={parseInt(curPage)}
                        changedPage={(id) => setCurPage(id)}
                    />
                }
                </div>
            </div>
        </div>
    )
}
export default Myreserved