import React, {useState} from 'react'
import BreadCrumb from 'src/simi/BaseComponents/BreadCrumb';
import {getStorelocators} from 'src/simi/Model/Storelocator'
import {showToastMessage} from 'src/simi/Helper/Message';
import Storelist from './Storelist'
import MapBranch from './MapBranch'
import Identify from 'src/simi/Helper/Identify'
import { getOS } from 'src/simi/App/Bianca/Helper';
require('./style.scss');

if (getOS() === 'MacOS') require('./style-mac.scss');

export const StoreLocator = props => {
    const loadedFromCache = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'cached_store_locators')
    const [stores, setStores] = useState(loadedFromCache)
    const [expanded, setExpanded] = useState(null)
    const [markerFocus, setMarkerFocus] = useState({id: null, center: null })
    const [showingDetailItem, setShowingDetailItem] = useState(null)
    
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
    
    console.log(props)
    console.log(stores)
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
                            multiple={true}
                            height={665}
                        />
                    </div>
                    <div className="branch-list-results">
                        <Storelist
                            data={stores}
                            expanded={expanded} setExpanded={setExpanded}
                            markerFocus={markerFocus} setMarkerFocus={setMarkerFocus}
                            showingDetailItem={showingDetailItem} setShowingDetailItem={setShowingDetailItem}
                        />
                    </div>
                </div>
            }
        </div>
    )
}
export default StoreLocator