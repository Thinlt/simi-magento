import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import {StaticRate} from 'src/simi/App/Bianca/BaseComponents/Rate';

require('./topReview.scss')

const TopReview = props => {
    const { app_reviews } = props
    return (
        <div className="review-rate">
            <StaticRate className="rate-star" rate={app_reviews.rate} size={14} width={80} isRtl={Identify.isRtl()}/>
            <span className="review-count">
                {app_reviews.number} {(app_reviews.number > 1) ? Identify.__('reviews') : Identify.__('review')}
            </span>
        </div>
    )
}

export default TopReview