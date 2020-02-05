import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import { Whitebtn } from 'src/simi/BaseComponents/Button'
import { SwipeableRate } from 'src/simi/App/Bianca/BaseComponents/Rate'
import { submitReview } from 'src/simi/Model/Product'
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading'
import {showToastMessage} from 'src/simi/Helper/Message'
import {smoothScrollToView} from 'src/simi/Helper/Behavior'
import { compose } from 'redux';
import { connect } from 'src/drivers';
import { withRouter } from 'react-router-dom';

require('./newReview.scss');

const NewReview = props => {
    const {product} = props
    if (!product.simiExtraField || !product.simiExtraField.app_reviews || !product.simiExtraField.app_reviews.form_add_reviews || !product.simiExtraField.app_reviews.form_add_reviews.length)
        return ''

    const form_add_review = product.simiExtraField.app_reviews.form_add_reviews[0]
    const { rates } = form_add_review
    if (!rates)
        return ''

    const setData = (data) => {
        hideFogLoading()
        smoothScrollToView($('#root'))
        if (data.errors) {
            if (data.errors.length) {
                const errors = data.errors.map(error => {
                    return {
                        type: 'error',
                        message: error.message,
                        auto_dismiss: false
                    }
                })
                props.toggleMessages(errors)
            }
        } else {
            if (data.message && data.message) {
                props.toggleMessages([{
                    type: 'success',
                    message: Array.isArray(data.message)?data.message[0]:data.message,
                    auto_dismiss: false
                }])
                $('#new-rv-nickname').val('')
                $('#new-rv-title').val('')
                $('#new-rv-detail').val('')
            }
        }
    }

    const handleSubmitReview = () => {
        // const nickname = $('#new-rv-nickname').val()
        const title = $('#new-rv-title').val()
        const detail = $('#new-rv-detail').val()
        const { isSignedIn, history, firstname, lastname } = props
        if (!isSignedIn) {
            history.push({pathname: '/login.html', pushTo: history.location.pathname})
            return
        }
        const nickname = lastname ? `${firstname} ${lastname}` : `${firstname}`;
        if (!nickname || !title || !detail) {
            showToastMessage(Identify.__('Please fill in all required fields'));
        } else {
            const params = {
                product_id: product.id,
                ratings: {},
                nickname,
                title,
                detail
            };
            const star = $('.select-star');
            for (let i = 0; i < star.length; i++) {
                const rate_key = $(star[i]).attr('data-key');
                const point = $(star[i]).attr('data-point');
                params.ratings[rate_key] = point;
            }
            showFogLoading()
            const submitRevRest = submitReview(setData, params);
        }
    }
    
    return (
        <div>
            <div className="review-form">
                <p className="your-rating-title">{Identify.__('Your Review')}</p>
                <table className="table">
                    <tbody>
                    {rates.map((item, index) => {
                        return (
                            <tr key={index}>
                                <td className="label-item" width="50px">{Identify.__(item.rate_code)}</td>
                                    <td id={item.rate_code}><SwipeableRate rate={1} size={24} rate_option={item.rate_options} rate_code={item.rate_code} change={true}/></td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
                <div className="form-content">
                    {/* <div className="form-group">
                        <p className="label-item">{Identify.__('Nickname')}<span className="rq">*</span></p>
                        <input type="text" id="new-rv-nickname" className="form-control" name="nickname" style={{background : '#f2f2f2'}} required placeholder={Identify.__('')}/>
                    </div> */}
                    <div className="form-group">
                        <p className="label-item">{Identify.__('Your review title:')}{/*<span className='rq'>*</span>*/}</p>
                        <input type="text" id="new-rv-title" className="form-control" name="title" style={{background : '#f2f2f2'}} required placeholder={Identify.__('Please write your review title.')}/>
                    </div>
                    <div className="form-group">
                        <p className="label-item">{Identify.__('Your review:')}{/*<span className="rq">*</span>*/}</p>
                        <textarea id="new-rv-detail" name="detail" className={`form-control`} rows="10" style={{background : '#f2f2f2'}} placeholder={Identify.__('Please write your review.')}></textarea>
                    </div>
                    <div className="btn-submit-review-ctn">
                        <Whitebtn 
                            text={Identify.__('Submit Review')}
                            className="btn-submit-review"
                            onClick={handleSubmitReview}
                        />
                    </div>
                </div>
            </div>
        </div>
    )
}

const mapStateToProps = ({ user }) => {
    const { currentUser, isSignedIn } = user;
    const { firstname, lastname, id } = currentUser;

    return {
        isSignedIn,
        firstname,
        lastname,
        customerId: id
    };
}
export default compose(connect(mapStateToProps), withRouter)(NewReview);