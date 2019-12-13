import React, {useEffect, useState} from 'react'
import Identify from "src/simi/Helper/Identify";
// import Scroller from "./Scroller";
import OwlCarousel from 'react-owl-carousel2';

const Instagram = (props) => {
    const {history, isPhone} = props;
    const [insData, setInsData] = useState();

    const getUserInstagram = async (name) => {
        // let response = await fetch(`/instagram/${name}/?__a=1`);
        let response = await fetch(`/rest/V1/simiconnector/proxy/instagram/?path=${name}/?__a=1`);
        let data = await response.json();
        if (data && data[0]) {
            return data[0];
        }
        return '';
    }

    useEffect(() => {
        const {data} = props;
        if (data) {
            getUserInstagram(data).then(res => {
                Identify.storeDataToStoreage(Identify.SESSION_STOREAGE, 'instagram', res);
                setInsData(res);
            });
        } else {
            const instagramStored = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'instagram');
            if (instagramStored) {
                setInsData(instagramStored);
            }
        }
    }, []);

    const actionViewAll = () => {
        const {data} = props;
        window.location.href = `https://www.instagram.com/${data}`;
    }

    const nodeItem = (ins) => {
        const { node } = ins;
        return node;
    }

    const renderInsItem = (item, index) => {
        return (
            <div className="item" key={index}>
                <a href={`https://www.instagram.com/p/${item.shortcode}`} target="_blank" rel="noopener noreferrer">
                    <img className="img-responsive" src={item.thumbnail_src} alt={item.accessibility_caption} />
                </a>
            </div>
        );
    }

    const renderInsView = () => {
        let html = null;
        if (insData) {
            const { user } = insData.graphql;
            if (user && !user.is_private) {
                const { edges } = user.edge_owner_to_timeline_media;
                if (edges.length) {
                    let instagramData = [];
                    instagramData = edges.map((ins, index) => {
                        // const limit = isPhone ? 3 : 8;
                        const limit = 18;
                        if (index < limit) {
                            return renderInsItem(nodeItem(ins), index);
                        }
                        return null;
                    });
                    html = instagramData;
                }
            }
        }
        return html;
    }

    const items = renderInsView();

    const left = '<svg class="chevron-left" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" style="display: inline-block; color: rgba(255, 255, 255, 0.87); fill: rgb(0, 0, 0); height: 24px; width: 24px; user-select: none; transition: all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms;"><path d="M14 20c0.128 0 0.256-0.049 0.354-0.146 0.195-0.195 0.195-0.512 0-0.707l-8.646-8.646 8.646-8.646c0.195-0.195 0.195-0.512 0-0.707s-0.512-0.195-0.707 0l-9 9c-0.195 0.195-0.195 0.512 0 0.707l9 9c0.098 0.098 0.226 0.146 0.354 0.146z"></path></svg>';
    const right = '<svg class="chevron-right" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" style="display: inline-block; color: rgba(255, 255, 255, 0.87); fill: rgb(0, 0, 0); height: 24px; width: 24px; user-select: none; transition: all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms;"><path d="M5 20c-0.128 0-0.256-0.049-0.354-0.146-0.195-0.195-0.195-0.512 0-0.707l8.646-8.646-8.646-8.646c-0.195-0.195-0.195-0.512 0-0.707s0.512-0.195 0.707 0l9 9c0.195 0.195 0.195 0.512 0 0.707l-9 9c-0.098 0.098-0.226 0.146-0.354 0.146z"></path></svg>';

    const options = {
        stagePadding: isPhone ? 35 : 41.5,
        // items: 1,
        margin: isPhone ? 15 : 16,
        nav: true,
        autoplay: false,
        navText: [left, right],
        responsive:{
            0:{
                items:1
            },
            375:{
                items:2
            },
            1024:{
                items:5
            },
            1366:{
                items:6
            },
            1600:{
                items:7
            }
        }
    };

    return (
        <div className={`instagram-slider ${isPhone ? 'phone-view':''}`}>
            <h3 className="title">{Identify.__('Shop Our Instagram')}</h3>
            {/* <Scroller lastItems={lastItems} history={history} slideSettings={slideSettings} isPhone={isPhone}>
                {renderInsView()}
            </Scroller> */}
            <div className="container instagram-container">
                <div className="carousel-block">
                    { items && 
                        <OwlCarousel options={options}>
                            {items}
                        </OwlCarousel>
                    }
                </div>
            </div>
            
            <div className="view-all">
                <div className="btn" onClick={actionViewAll}><span>{Identify.__('View all')}</span></div>
            </div>
            {/* <div className="instagram-block">
                <div className="block-inner">
                    {renderInsView()}
                </div>
                <div className="view-all">
                    <div className="btn" onClick={actionViewAll}><span>{Identify.__('View all')}</span></div>
                </div>
            </div> */}
        </div>
    );
}
export default Instagram