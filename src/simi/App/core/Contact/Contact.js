import React, { Component } from 'react';
import ContactForm from './Components/Form';
import Info from './Components/Info';
// import Loading from 'src/simi/BaseComponents/Loading'
import {compose} from 'redux';
import classify from "src/classify";
import defaultClasses from "./style.css";

class Contact extends Component {
    render() {
        return (
            <div className="contact-page">
                <div className="container">
                    <div className="col-xs-12 col-sm-6">
                        <ContactForm/>
                    </div>
                    <div className="col-xs-12 col-sm-6">
                        <Info/>
                    </div>
                </div>
            </div>
        );
    }
}

export default compose(
    classify(defaultClasses)
)(Contact);