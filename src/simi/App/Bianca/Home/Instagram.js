import React, {useEffect, useState} from 'react'
import Identify from "src/simi/Helper/Identify";

const Instagram = (props) => {
    const {history, isPhone} = props;
    const [insData, setInsData] = useState();

    const getUserInstagram = async (name) => {
        let response = await fetch(`https://www.instagram.com/${name}/?__a=1`);
        let data = await response.json();
        return data;
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
        window.location.href = `https://www.instagram.com/${name}`;
    }

    const nodeItem = (ins) => {
        const { node } = ins;
        return node;
    }

    const renderInsItem = (item, index) => {
        return <div className="ins-item" key={index}>
            <a href={`https://www.instagram.com/p/${item.shortcode}`} target="_blank" rel="noopener noreferrer">
                <img src={item.thumbnail_src} alt={item.accessibility_caption} />
            </a>
        </div>
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
                        const limit = isPhone ? 3 : 8;
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

    return (
        <div className="instagram-block">
            <div className="block-inner">
                {renderInsView()}
            </div>
            <div className="view-all">
                <div className="btn" onClick={actionViewAll}><span>{Identify.__('View all')}</span></div>
            </div>
        </div>
    );
}
export default Instagram