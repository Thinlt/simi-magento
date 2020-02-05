import React, { useState, useEffect } from 'react';
import {sendRequest} from 'src/simi/Network/RestMagento';
import Loading from 'src/simi/BaseComponents/Loading';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import Identify from 'src/simi/Helper/Identify';
import { resourceUrl } from 'src/simi/Helper/Url';
// import Pagination from 'src/simi/BaseComponents/Pagination';
import { StaticRate } from 'src/simi/App/Bianca/BaseComponents/Rate';

require('./reviewList.scss');

const ReviewList = props => {

    const [data, setData] = useState();
    const [curPage, setCurPage] = useState(1);

    const requestReviews = (vendorId, page = 1, limit = 10) => {
        if (vendorId) {
            showFogLoading();
            sendRequest(`/rest/V1/simiconnector/vendors/${vendorId}/reviews?limit=${limit}&page=${page}`, (data) => {
                if (data) {
                    setData(data);
                } else {
                    setData([]);
                }
                hideFogLoading();
            }, 'GET', null, null);
            setCurPage(page);
        }
    }

    useEffect(() => {
        const {vendorId} = props;
        if (vendorId) {
            requestReviews(vendorId);
        }
    }, []);

    const renderListItem = () => {
        if (data && data.reviews && data.reviews.length) {
            return (
                <div className="list-review-item">
                    {data.reviews.map((item) => (renderItem(item)))}
                </div>
            );
        }
        return (
            <div className="text-center">{Identify.__('Review is empty')}</div>
        );
    };

    const renderPagination = () => {
        const {vendorId} = props;
        const {total, page_size} = data || {};
        if (vendorId, data && total && page_size) {
            const minPage = 1;
            const maxPage = Math.ceil(parseInt(total) / parseInt(page_size));
            let pages = [];
            if (maxPage > 1) {
                for(let p=minPage; p<=maxPage; p++){
                    pages.push(
                        <span className={`${curPage===p?'disabled':''}`} 
                            onClick={() => curPage===p ? false : requestReviews(vendorId, p)} key={p}>{p}
                        </span>
                    );
                }
                return (
                    <div className="pagination">(<div className="scroller">{pages}</div>)</div>
                );
            }
        }
        return null;
    };
    
    const renderItem = item => {
        const createdAt = new Date(item.created_at);
        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        return (
            <div className="review-item item" key={item.review_id}>
                <div className="item-title flex">{item.title}</div>
                <div className="item-detail">{item.detail}</div>
                <div className="review-item-detail">
                    <div
                        className="item-created flex"
                        style={{
                            marginLeft: Identify.isRtl() ? 0 : 'auto',
                            marginRight: Identify.isRtl() ? 'auto' : 0
                        }}
                    >
                        <span>{`${createdAt.getDate()} ${monthNames[createdAt.getMonth()]} ${createdAt.getFullYear()}`}</span>
                        <span>{item.nickname}</span>
                    </div>
                    <div className="item-rate">
                        <StaticRate rate={item.rate_points} />
                    </div>
                </div>
                <div className="product">
                    <img src={resourceUrl(item.product_image, { type: 'image-product', width: 82 })} alt={item.product_name}/>
                    <span>{item.product_name}</span>
                </div>
            </div>
        );
    };

    if (!data) {
        return null;
        // return <Loading />;
    }

    return (
        <div className="review-list">
            <div className="items-wrap">
                {renderListItem()}
                {renderPagination()}
            </div>
        </div>
    );
};
export default ReviewList;
