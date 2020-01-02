import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import {StaticRate} from 'src/simi/App/Bianca/BaseComponents/Rate';

require('./TopReview.scss')

const TopReview = props => {
    const { reviews } = props
    return (
        <div className="review-rate">
            <StaticRate className="rate-star" rate={reviews.rate} size={14} width={80}/>
            <span className="review-count">
                {reviews.number} {(reviews.number > 1) ? Identify.__('reviews') : Identify.__('review')}
            </span>
        </div>
    )
}

export default TopReview