import React, {useState, useEffect} from 'react'
import BreadCrumb from 'src/simi/BaseComponents/BreadCrumb';
import {getStorelocators} from 'src/simi/Model/Storelocator'
import {showToastMessage} from 'src/simi/Helper/Message';
import Storelist from './Storelist'
import MapBranch from './MapBranch'
import Identify from 'src/simi/Helper/Identify'
import { getOS } from 'src/simi/App/Bianca/Helper';
import { Colorbtn } from 'src/simi/BaseComponents/Button';
import TextBox from 'src/simi/BaseComponents/TextBox';
require('./style.scss');

export const StoreLocator = props => {
    const loadedFromCache = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'cached_store_locators')
    const [stores, setStores] = useState(loadedFromCache)
    const [expanded, setExpanded] = useState(null)
    const [markerFocus, setMarkerFocus] = useState({id: null, center: null })
    const [showingDetailItem, setShowingDetailItem] = useState(null)
    const [isPhone, setIsPhone] = useState(window.innerWidth < 1024);
    
    const processData = (data) => {
        if (data.errors) {
            let message = ''
            data.errors.map(value => {
                message += value.message
            })
            showToastMessage(message?message:Identify.__('Problem occurred.'))
        } else {
            Identify.storeDataToStoreage(Identify.SESSION_STOREAGE, 'cached_store_locators', data);
        }
        setStores(data)
    }
    if (!stores) {
        getStorelocators(processData)
    }
    
    const onDirection = () => {
        if (markerFocus && markerFocus.center) {
            const centerF = markerFocus.center;
            const lat = centerF.lat;
            const lng = centerF.lng;
            if($('input[name="store_locator_postcode"]').val()){
                const postcode = $('input[name="store_locator_postcode"]').val();
                window.open(`https://www.google.com/maps/dir/?api=1&origin=${postcode}&destination=${lat},${lng}`,'_blank');
            }
        }
    }

	const resizePhone = () => {
		$(window).resize(function() {
			const width = window.innerWidth;
			const newIsPhone = width < 1024;
			if (isPhone !== newIsPhone) {
				setIsPhone(newIsPhone);
			}
		});
    };
    
	useEffect(() => {
		resizePhone();
	});
    
    return (
        <div className="store-locator-ctn container">
            <BreadCrumb
                breadcrumb={[
                    { name: 'Home', link: '/' },
                    { name: 'Store Locators' }
                ]}
            />
            {showingDetailItem && <div className="back-store-btn" onClick={()=>setShowingDetailItem(null)} role="presentation">{Identify.__('Back')}</div>}
            {
                stores &&
                <div className="branch-main-content">
                    <div className="branch-map">
                        <MapBranch
                            data={stores}
                            currentLocation={stores.hasOwnProperty('current_location') && stores.current_location ? stores.current_location : ''}
                            markerFocus={markerFocus}
                            multiple={!showingDetailItem}
                            height={isPhone?343:572}
                        />
                        {
                            (markerFocus && markerFocus.center) && (
                                <div className="detail__direction-map">
                                    <TextBox
                                        type="text"
                                        name="store_locator_postcode"
                                        placeholder={Identify.__(
                                            "Enter postcode for directions"
                                        )}
                                    />
                                    <Colorbtn
                                        className="store_locator_btnSubmit"
                                        text={Identify.__("Get directions")}
                                        onClick={() => onDirection()}
                                    />
                                </div>
                            )
                        }
                    </div>
                    <div className="branch-list-results" style={isPhone?{}:{maxHeight: showingDetailItem?'unset':(markerFocus && markerFocus.center) ? 592 : 572, overflow: 'scroll'}} >
                        <Storelist
                            data={stores}
                            expanded={expanded} setExpanded={setExpanded}
                            markerFocus={markerFocus} setMarkerFocus={setMarkerFocus}
                            showingDetailItem={showingDetailItem} setShowingDetailItem={setShowingDetailItem}
                            isPhone={isPhone}
                        />
                    </div>
                </div>
            }
        </div>
    )
}
export default StoreLocator