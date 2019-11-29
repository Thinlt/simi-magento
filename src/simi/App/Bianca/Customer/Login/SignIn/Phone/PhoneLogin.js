import React, { Component } from 'react';
import { Form } from 'informed';
require('./phoneLogin.scss');

class PhoneLogin extends Component {
    render() {
        return(
            <Form>
                <div className="phone-login">
                    Buyer Phone Login Form
                </div>
            </Form>
        )
    }
}

export default PhoneLogin;
