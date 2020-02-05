import React from "react";
import Identify from 'src/simi/Helper/Identify'
import { smoothScrollToView } from 'src/simi/Helper/Behavior';

const checkTimeOpen = times => {
    let html = null;
    const time = [];
    if (
        times.monday_status &&
        times.tuesday_status &&
        times.wednesday_status &&
        times.thursday_status &&
        times.friday_status
    ) {
        if (
            times.monday_open === times.tuesday_open &&
            times.monday_open === times.wednesday_open &&
            times.monday_open === times.thursday_open &&
            times.monday_open === times.friday_open &&
            (times.monday_close === times.tuesday_close &&
                times.monday_close === times.wednesday_close &&
                times.monday_close === times.thursday_close &&
                times.monday_close === times.friday_close)
        ) {
            time.push(
                Identify.__(
                    `Monday - Friday ${times.monday_open}h - ${
                        times.monday_close
                    }h`
                )
            );
        } else {
            time.push(
                Identify.__(
                    `Monday ${times.monday_open}h - ${times.monday_close}h`
                )
            );
            time.push(
                Identify.__(
                    `Tuesday ${times.tuesday_open}h - ${
                        times.tuesday_close
                    }h`
                )
            );
            time.push(
                Identify.__(
                    `Wednesday ${times.wednesday_open}h - ${
                        times.wednesday_close
                    }h`
                )
            );
            time.push(
                Identify.__(
                    `Thursday ${times.thursday_open}h - ${
                        times.thursday_close
                    }h`
                )
            );
            time.push(
                Identify.__(
                    `Friday ${times.friday_open}h - ${times.friday_close}h`
                )
            );
        }
    } else {
        if (times.monday_status && times.monday_open !== times.monday_close) {
            time.push(
                Identify.__(
                    `Monday ${times.monday_open}h - ${times.monday_close}h`
                )
            );
        }
        if (times.tuesday_status && times.tuesday_open !== times.tuesday_close) {
            time.push(
                Identify.__(
                    `Tuesday ${times.tuesday_open}h - ${
                        times.tuesday_close
                    }h`
                )
            );
        }
        if (times.wednesday_status && times.wednesday_open !== times.wednesday_close) {
            time.push(
                Identify.__(
                    `Wednesday ${times.wednesday_open}h - ${
                        times.wednesday_close
                    }h`
                )
            );
        }
        if (times.thursday_status && times.thursday_open !== times.thursday_close) {
            time.push(
                Identify.__(
                    `Thursday ${times.thursday_open}h - ${
                        times.thursday_close
                    }h`
                )
            );
        }
        if (times.friday_status && times.friday_open !== times.friday_close) {
            time.push(
                Identify.__(
                    `Friday ${times.friday_open}h - ${times.friday_close}h`
                )
            );
        }
    }

    if (times.saturday_status && times.saturday_open !== times.saturday_close) {
        time.push(
            Identify.__(
                `Saturday ${times.saturday_open}h - ${
                    times.saturday_close
                }h`
            )
        );
    }
    if ((parseInt(times.sunday_status) === 1) && times.sunday_open !== times.sunday_close) {
        time.push(
            Identify.__(
                `Sunday ${times.sunday_open}h - ${times.sunday_close}h`
            )
        );
    }

    if (time.length) {
        html = time.map((item, index) => {
            return <p key={index} style={{whiteSpace: 'nowrap'}}>{item}</p>;
        });
    }

    return html;
};

const ImageGallery = props => {
    const { item } = props
    if (!item || !item.image_gallery || !item.image_gallery.length)
        return ''
    return (
        <div className="store-image-gallery">
            {
                item.image_gallery.map((image, key) => {
                    return (
                        <div className="store-image-item" key={key} style={{backgroundImage: `url("${image}")`}}></div>
                    )
                })
            }
        </div>
    )
}

const openingOur = (item) => {
    return (
        <React.Fragment>
            <b className="title">{Identify.__("Opening Hours")}</b>
            <div className="box-store-br-item">
                {checkTimeOpen(item)}
            </div>
        </React.Fragment>
    )
}
const StoreSummary = props => {
    const { item, setShowingDetailItem, showingDetail, isPhone } = props;
    return (
        <React.Fragment>
            <div className="branch-content-panel">
                <div className="column-store-item">
                    <div className="box-store-br-item">
                        <b className="title">{Identify.__("Address")}</b>
                        {item.address && <p>{item.address}</p>}
                        {item.city && <p>{item.city}</p>}
                        {item.zipcode && <p>{item.zipcode}</p>}
                    </div>
                    {
                        isPhone && <div className="box-store-br-item">{openingOur(item)}</div>
                    }
                    <div className="box-store-br-item">
                        <b className="title">{Identify.__("Contact")}</b>
                        {item.phone && (
                            <p>
                                <span className="br-item-label">
                                    {Identify.__("Tel: ")}
                                </span>
                                <a href={`tel:${item.phone}`}>
                                    {item.phone}
                                </a>
                            </p>
                        )}
                        {item.email && (
                            <p style={{display:'flex',flexWrap:'wrap'}}>
                                <span className="br-item-label">
                                    {Identify.__("Email: ")}
                                </span>
                                <a href={`mailto:${item.email}`}>
                                    {item.email}
                                </a>
                            </p>
                        )}
                        {item.fax && (
                            <p>
                                <span className="br-item-label">
                                    {Identify.__("Whatsapp: ")}
                                </span>
                                <a href={`https://wa.me/${item.fax}`}>
                                    {item.fax}
                                </a>
                            </p>
                        )}
                    </div>
                </div>
                <div className="column-store-item">
                    {
                        !isPhone && openingOur(item)
                    }
                    {
                        !showingDetail &&
                        <div className="detail-branch-btn" 
                            role="presentation"
                            onClick={()=> {smoothScrollToView($("#id-message")); setShowingDetailItem(item)}}>
                                {Identify.__("Details")}
                        </div>
                    }
                </div>
            </div>
            {
                showingDetail && 
                <ImageGallery item={item} />
            }
        </React.Fragment>
    );
}

export default StoreSummary;
