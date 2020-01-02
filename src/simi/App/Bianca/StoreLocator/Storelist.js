import React from 'react'
import Expansion from 'src/simi/App/Bianca/BaseComponents/Expansion'
import Identify from 'src/simi/Helper/Identify'
import StoreSummary from './StoreSummary'

const Storelist = props => {
    let html = null;
    const { 
        expanded, setExpanded,
        data, postcode, setMarkerFocus,
        setShowingDetailItem, showingDetailItem
    } = props;

    const onDirectMile = reLatLng => {
        if (postcode && postcode !== "list-all" && reLatLng) {
            const lat = reLatLng.lat;
            const lng = reLatLng.lng;
            window.open(
                `https://www.google.com/maps/dir/?api=1&origin=${lat},${lng}&destination=${postcode}`
            );
        }
    }

    const handleExpand = expanded => {
        setExpanded(expanded)
        if (expanded === false) {
            setMarkerFocus({ id: null });
        }
    }

    if (data && data.storelocations.length > 0) {
        html = data.storelocations.map((item, index) => {
            const item_id = item.simistorelocator_id;
            const reLatLng = {
                lat: Number(item.latitude),
                lng: Number(item.longitude)
            };
            const title = (
                <div
                    className="branch-title-panel"
                    onClick={() => setMarkerFocus({ id:item_id, center: reLatLng })}
                    role="presentation"
                >
                    <span>
                        <span className="count">{index + 1}</span>
                        {Identify.__(item.store_name)}
                    </span>
                    {(typeof item.distance ==='number' && item.distance !== 0) && (
                        <span
                            className="direct-mile"
                            onClick={() => onDirectMile(reLatLng)}
                            role="presentation"
                        >
                            {Math.round(Math.round(
                                100 *
                                    metterToMile *
                                    parseInt(item.distance, 10)
                            ) / 100)}
                            {Identify.__(" mi")}
                            <Location
                                className="location-icon"
                                style={{ width: 22, height: 22 }}
                            />
                        </span>
                    )}
                </div>
            );
            if (showingDetailItem && showingDetailItem.simistorelocator_id !== item.simistorelocator_id) {
                return
            } else if (showingDetailItem) {
                return  (
                    <StoreSummary 
                        key={item_id}
                        item={item} setShowingDetailItem={setShowingDetailItem}
                        showingDetail={true}/>
                )
            }
            const content = (
                <React.Fragment>
                    <StoreSummary 
                        item={item} setShowingDetailItem={setShowingDetailItem}
                        showingDetail={false}/>
                </React.Fragment>
            );

            return (
                <Expansion
                    id={index}
                    key={item_id}
                    title={title}
                    content={content}
                    icon_color="#FFFFFF"
                    handleExpand={index => handleExpand(index)}
                    expanded={expanded}
                />
            );
        });
    } else {
        html = Identify.__("No store has been found");
    }

    return html;
};

export default Storelist