import React, { Component } from 'react';
import classify from 'src/classify';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import PageTitle from 'src/simi/App/core/Customer/Account/Components/PageTitle';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import Vouchers from './Vouchers';
import defaultClasses from './style.scss';

class MyGiftVouchers extends Component {
    render() {
        return (
            <div className='my-gift-vouchers-area'>
                <PageTitle title={'GIFT VOUCHERS'}/>
                <Vouchers/>
            </div>
        )
    }
}

const mapDispatchToProps = {
    toggleMessages,
};

export default compose(
    classify(defaultClasses),
    connect(
        null,
        mapDispatchToProps
    )
)(MyGiftVouchers);
