import React, { useReducer, useEffect, useCallback } from 'react';
import Loading from 'src/simi/BaseComponents/Loading';
import Identify from 'src/simi/Helper/Identify';
import {showToastMessage} from 'src/simi/Helper/Message';
import { sendRequest } from 'src/simi/Network/RestMagento';
import Loadmore from './loadMore';
import { StaticRate } from 'src/simi/App/Bianca/BaseComponents/Rate';

require('./reviewList.scss');

const ReviewList = props => {
    const { product_id } = props;
    const pageSize = 3;
    const api_data = Identify.ApiDataStorage('product_list_review');
    const initData =
        api_data &&
        api_data instanceof Object &&
        api_data.hasOwnProperty(product_id)
            ? api_data[product_id]
            : null;
    const reducer = (state, action) => {
        return {...state, ...action}
    }
    const [state, dispatch] = useReducer(reducer, {data: initData, page: 1});

    useEffect(() => {
        if (!state.data) {
            sendRequest(`rest/V1/simiconnector/reviews`, apiCallBack, 'GET', {'filter[product_id]': product_id, limit: pageSize}, {})
        }
    }, []);

    const loadMorePage = useCallback((page) => {
        if (page === 0) {
            dispatch({page: 1, data: {...state.data, ...{reviews: state.data.reviews.slice(0, 3), from: 0}}}); //Show less
        } else {
            dispatch({page: page, loadingMore: true});
            sendRequest(`rest/V1/simiconnector/reviews`, apiCallBack, 'GET', {'filter[product_id]': product_id, limit: 5, offset: page}, {})
        }
    });

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
                        <StaticRate rate={item.rate_points} isRtl={Identify.isRtl()}/>
                    </div>
                </div>
            </div>
        );
    };

    const renderListItem = () => {
        const {data} = state;
        if (data && data.reviews && data.reviews.length) {
            return (
                <div className="list-review-item">
                    {data.reviews && data.reviews.length &&
                        data.reviews.map((item) => renderItem(item))
                    }
                    <Loadmore items={state.data.reviews} itemCount={data.total} currentPage={state.page} updateSetPage={loadMorePage} loading={state.loadingMore} />
                </div>
            );
        }
        return (
            <div className="text-center">{Identify.__('Review is empty')}</div>
        );
    };

    const apiCallBack = data => {
        if (data.errors) {
            const errors = data.errors;
            let text = '';
            for (const i in errors) {
                const error = errors[i];
                text += error.message + ' ';
            }
            /* if (text !== '') {
                showToastMessage(text);
            } */
            dispatch({
                data: state.data || data,
                loadingMore: false
            });
        } else {
            let newData = state.data || {};
            if (newData.reviews) {
                if (data && data.reviews) {
                    newData.reviews = [...newData.reviews, ...data.reviews];
                }
                if (data && data.from) {
                    newData.from = data.from;
                }
            } else {
                newData = data;
            }
            dispatch({
                data: newData,
                loadingMore: false
            });
            const api_data = {};
            api_data[props.product_id] = newData;
            // Identify.ApiDataStorage('product_list_review', 'update', api_data);
        }
    };

    const renderAverageStar = () => {
        const { count, total } = state.data;
        let averageStar = 0;
        let roundedAverageStar = 0;
        if (total !== 0) {
            averageStar =
                (5 * count['5_star'] +
                4 * count['4_star'] +
                3 * count['3_star'] +
                2 * count['2_star'] +
                1 * count['1_star']
                ) / total;
            roundedAverageStar = averageStar.toFixed(1);
        }
        return (
            <div className="total-rate">
                <StaticRate rate={averageStar} size={24} width={137} isRtl={Identify.isRtl()}/>
                <p>{Identify.__(`This has a rating of ${roundedAverageStar}/5`)}</p>
            </div>
        );
    };

    if (!state.data) {
        return <Loading />;
    }

    if (!state.data.reviews && state.data.errors) {
        return (
            <div>
                {
                    state.data.errors.length && <p className="error">{Identify.__('Review is empty')}</p>
                }
            </div>
        );
    }

    return (
        <div>
            <h2 className="review-list-title">
                <span>{Identify.__(`${state.data.total} Customer Reviews`)}</span>
            </h2>
            {renderAverageStar()}
            {renderListItem()}
        </div>
    );
};
export default ReviewList;
