import React, { Component } from 'react';
import LoginOTP from './LoginOTP';
import classify from 'src/classify';
import { simiSignedIn } from 'src/simi/Redux/actions/simiactions';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import classes from './phoneLogin.css';

class PhoneLogin extends Component {

    constructor(props) {
        super(props)
    }

    render() {
        return (
            <LoginOTP
                classes={classes}
                onSignIn={this.props.simiSignedIn}
                openVModal={this.props.openVModal}
                closeVModal={this.props.closeVerifyModal}
            // getUserDetails={this.props.getUserDetails}
            />
        )
    }
}

const mapDispatchToProps = {
    simiSignedIn
}

export default compose(
    classify(classes),
    connect(
        null,
        mapDispatchToProps
    )
)(PhoneLogin);
