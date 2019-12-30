import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import Check from 'src/simi/BaseComponents/Icon/TapitaIcons/SingleSelect';
import {configColor} from "src/simi/Config";
import Dropdownoption from 'src/simi/BaseComponents/Dropdownoption/'
import { withRouter } from 'react-router-dom';


const Sortby = props => {
    const { history, location, sortByData, isPhone } = props;
    const { search } = location;
    let dropdownItem = null

    const changedSortBy = (item) => {
        if (dropdownItem)
            dropdownItem.handleToggle()
        const queryParams = new URLSearchParams(search);
        queryParams.set('product_list_order', item.key);
        queryParams.set('product_list_dir', item.direction);
        history.push({ search: queryParams.toString() });
    }

    let selections = [];
    const orders = [
        {"key":"name","value":"Name","direction":"asc"},
        {"key":"name","value":"Name","direction":"desc"},
        {"key":"price","value":"Price","direction":"asc"},
        {"key":"price","value":"Price","direction":"desc"},
    ];

    let sortByTitle = isPhone ? <React.Fragment>{Identify.__('Sort')} <span className={'icon-arrow-down'}></span></React.Fragment> : Identify.__('Sort by');

    selections = orders.map((item) => {
        let itemCheck = ''
        const itemTitle = (
            <React.Fragment>
                {Identify.__(item.value)} <span className={item.direction === 'asc'? 'icon-arrow-up' :'icon-arrow-down'}></span>
            </React.Fragment>
        )

        if (sortByData && sortByData[`${item.key}`] === item.direction.toUpperCase()) {
            itemCheck = (
                <span className="is-selected">
                    <Check color={configColor.button_background} style={{width: 16, height: 16, marginRight: 4}}/>
                </span>
            )
            sortByTitle = itemTitle
        }
        return (
            <div 
                role="presentation"
                key={Identify.randomString(5)}
                className="dir-item"
                onClick={()=>changedSortBy(item)}
            >
                <div className="dir-title">
                    {itemTitle}
                </div>
                <div className="dir-check">
                {itemCheck}
                </div>
            </div>
        );
    });

    return (
        <React.Fragment>
            {
                selections.length === 0 ?
                <span></span> : 
                <div className={`sort-by-select ${sortByData ? 'sorting':''}`}>
                    <Dropdownoption 
                        title={sortByTitle}
                        ref={(item)=> {dropdownItem = item}}
                    >
                        {selections}
                    </Dropdownoption>
                </div>
            }
        </React.Fragment>
    )
}

export default (withRouter)(Sortby);