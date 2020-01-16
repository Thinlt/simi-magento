import React, { Component } from 'react';
import LoginOTP from './LoginOTP';
import { Form } from 'informed';
import classes from './phoneLogin.css';

class PhoneLogin extends Component {
    render() {
        return (
            <LoginOTP
                classes={classes}
                onSignIn={this.props.simiSignedIn}
                getUserDetails={this.props.getUserDetails}
            />
        )
    }
}

export default PhoneLogin;
