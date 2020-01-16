/* eslint-disable jsx-a11y/no-noninteractive-element-interactions */
/* eslint-disable jsx-a11y/click-events-have-key-events */
import React, { useState, useEffect } from 'react';
import Pagination from '../../Components/Pagination';
import PaginationTable from '../../Components/PaginationTable';
import Identify from 'src/simi/Helper/Identify';
import { removeCode } from 'src/simi/Model/Customer';

const GiftVouchers = props => {
    const { setGiftCode, setLoading, isPhone } = props;
    let {giftCode, historyCodes} = props

    if(!Array.isArray(giftCode)) {
        giftCode = [giftCode]
    };
    if(!Array.isArray(historyCodes)) {
        historyCodes = [historyCodes]
    };
    // if(typeof(historyCodes)==='object') historyCodes = [historyCodes];
    const cols =
        [
            { title: Identify.__("Code"), width: "14.02%" },
            { title: Identify.__("Added Date"), width: "15.67%" },
            { title: Identify.__("Expired Date"), width: "12.06%" },
            { title: Identify.__("Balance"), width: "12.58%" },
            { title: Identify.__("Status"), width: "12.27%" },
            { title: Identify.__("Action"), width: "12.27%" }
        ];
    
    const colsHistory = 
        [
            { title: Identify.__("Status"), width: "14.02%" },
            { title: Identify.__("Code"), width: "15.67%" },
            { title: Identify.__("Order"), width: "12.06%" },
            { title: Identify.__("Changed tieme"), width: "12.58%" },
        ]

    const formatDate = (date) =>{
        let m = date.getMonth() + 1;
        m = m < 10 ? "0"+m : m;
        if(Identify.isRtl()){
            date = date.getFullYear() + '/' + m + '/' + date.getDate() ;
            return date;
        }
        date = date.getDate() + '/' + m + '/' + date.getFullYear()
        return date;
    };

    const removeGiftCode = (codeId, code) => {
        const data = {
            code,
            id: codeId
        }
        setLoading(true);
        removeCode(removeGiftCodeCallBack, data);
    }

    const removeGiftCodeCallBack = (data) => {
        setGiftCode(data);
        setLoading(false);
    } 

    const renderListVoucher = (item, index) => {
        const addedDate = new Date(item.created_at);
        const expireDate = new Date(item.expired_at);
        // render on mobile nontable
        if (isPhone) {
            return (
                <div className="item">
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Code")}</div>
                        <div className="item-value">{Identify.__(`${item.code}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Added Date")}</div>
                        <div className="item-value">{Identify.__(`${formatDate(addedDate)}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Expired Date")}</div>
                        <div className="item-value">{item.expired_at && Identify.__(`${formatDate(expireDate)}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Balance")}</div>
                        <div className="item-value">{Identify.__(`${item.balance}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Status")}</div>
                        <div className="item-value">{Identify.__(`${item.status}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label"></div>
                        <div className="item-value">
                            <a href="#" className="action" onClick={()=>removeGiftCode(item.id, item.code)}>{Identify.__('Remove')}</a>
                        </div>
                    </div>
                </div>
            );
        }
        // on desktop
        return (
            <tr key={index}>
                <td data-title={Identify.__("Code")}>
                    {Identify.__(`${item.code}`)}
                </td>
                <td
                    data-title={Identify.__("Added Date")}
                >
                    {Identify.__(`${formatDate(addedDate)}`)}
                </td>
                <td data-title={Identify.__("Expired Date")}>
                    {!item.expired_at
                    ? null
                    :
                        Identify.__(`${formatDate(expireDate)}`)
                    }   
                </td>
                <td data-title={Identify.__("Balance")}>
                    {Identify.__(`${item.balance}`)}
                </td>
                <td data-title={Identify.__("Status")}>
                    {Identify.__(`${item.status}`)}
                </td>
                <td className="action" data-title={Identify.__("Action")} onClick={() => removeGiftCode(item.id, item.code)}>
                    {Identify.__(`Remove`)}   
                </td>
            </tr>
        )
    }

    const renderHistoryVoucher = (item, index) => {
        const changedTime = new Date(item.updated_at);
        // render on mobile nontable
        if (isPhone) {
            return (
                <div className="item">
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Status")}</div>
                        <div className="item-value">{Identify.__(`${item.status}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Code")}</div>
                        <div className="item-value">{Identify.__(`${item.code}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Order")}</div>
                        <div className="item-value">{Identify.__(`${item.order_id}`)}</div>
                    </div>
                    <div className="row-item">
                        <div className="item-label">{Identify.__("Changed Time")}</div>
                        <div className="item-value">{item.expired_at && Identify.__(`${formatDate(changedTime)}`)}</div>
                    </div>
                </div>
            );
        }
        // on desktop
        return (
            <tr key={index}>
                <td data-title={Identify.__("Status")}>
                    {Identify.__(`${item.status}`)}
                </td>
                <td
                    data-title={Identify.__("Code")}
                >
                    {Identify.__(`${item.code}`)}
                </td>
                <td data-title={Identify.__("Order")}>
                    {Identify.__(`${item.order_id}`)}
                </td>
                <td data-title={Identify.__("Changed Time")}>
                    {item.expired_at
                    ? null
                    :
                        Identify.__(`${formatDate(changedTime)}`)
                    }   
                    
                </td>
            </tr>
        )
    }

    return (
        <React.Fragment>
            <div className="vouchers">
                <div className="box-title my-vouchers">
                    {Identify.__("My Gift Vouchers")}
                </div>
                {!giftCode || giftCode.length == 0
                ?
                    <div className="text-center">
                        {Identify.__("You have no gift voucher")}
                    </div>
                :   isPhone ? 
                    <Pagination
                        renderItem={renderListVoucher}
                        cols={cols}
                        data={giftCode}
                        limit={5}
                    /> :
                    <PaginationTable
                        renderItem={renderListVoucher}
                        cols={cols}
                        data={giftCode}
                        limit={5}
                    />
                }
            </div>
            <div className="history">
                <div className="box-title vouchers-history">
                    {Identify.__("History")}
                </div>
                {!historyCodes || historyCodes.length == 0
                ?
                    <div className="text-center">
                        {Identify.__("You have no code in your history")}
                    </div>
                :   isPhone ? 
                    <Pagination
                        renderItem={renderHistoryVoucher}
                        cols={colsHistory}
                        data={historyCodes}
                        limit={4}
                    /> :
                    <PaginationTable
                        renderItem={renderHistoryVoucher}
                        cols={colsHistory}
                        data={historyCodes}
                        limit={4}
                    />
                }
            </div>
        </React.Fragment>
    );
};

export default GiftVouchers;
