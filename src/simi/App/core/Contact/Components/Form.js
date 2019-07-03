/* eslint-disable prefer-const */
import React, { Component } from 'react';
import Identify from 'src/simi/Helper/Identify'
// import {compose} from 'redux';
import classify from "src/classify";
import defaultClasses from "../style.css";
import {Colorbtn} from '../../../../BaseComponents/Button';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import { sendContact } from 'src/actions/contact';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading'

const $ = window.$;
class Form extends Component {
    handleSubmit = (e) => {
        e.preventDefault();
        const form = $('#contact-form').serializeArray();
        let value = {};
        for(let i in form){
            let field = form[i];
            value[field.name] = field.value;
        }
        // console.log(value)
        showFogLoading()
        this.props.sendContact(value)
        // this.processData(this.props.data)
    }

    render() {
        const { classes, data } = this.props
        // console.log(data, 'log ')
        if(data){
            hideFogLoading()
            console.log(data.length)
            if (data.length) {
                const errors = data.map(error => {
                    return {
                        type: 'error',
                        message: error.message,
                        auto_dismiss: true
                    }
                });
                
                this.props.toggleMessages(errors)
            }
        } else {
            this.props.toggleMessages({type: 'success', message: Identify.__('Thank you, we will contact you soon')})
        }
        return (
            <div className={classes['form-container']}>
                <form id="contact-form" onSubmit={this.handleSubmit}>
                    <h2>{Identify.__("Contact Us")}</h2>
                    <div className='form-group'>
                        <input type="text" className={`form-control ${classes['base-textField']} required`} name="name" placeholder="Name *" required={true}/>
                    </div>
                    <div className='form-group'>
                        <input type="text" className={`form-control ${classes['base-textField']} required`} name="company" placeholder="Company Name *" required={true}/>
                    </div>
                    <div className='form-group'>
                        <input type="text" className={`form-control ${classes['base-textField']} required`} name="email" placeholder="Email Address *" required={true}/>
                    </div>
                    <div className='form-group'>
                        <input type="text" className={`form-control ${classes['base-textField']} required`} name="phone" placeholder="Telephone *" required={true}/>
                    </div>
                    <div className="form-group fg-textarea">
                        <textarea className={`form-control ${classes['base-textareaField']}`} name="message" cols="30" rows="5" placeholder="Enter your message here" required={true}></textarea>
                    </div>
                    <div className={classes["flex__space-between"]}>
                        <span className={classes["requirement"]}>*Required fields</span>
                        <Colorbtn type="submit" className={classes['submit-btn']} text="Submit"/>
                    </div>
                    {/* <button></button> */}
                </form>
            </div>
        );
    }
}

const mapStateToProps = ({contact}) => {
    const { data } = contact;
    console.log(data, 'data ......')
    return {
        data
    }
}

const mapDispatchToProps = {
    toggleMessages,
    sendContact
}

export default compose(
    classify(defaultClasses),
    connect(
        mapStateToProps,
        mapDispatchToProps
    )
) (Form);

// export default classify(defaultClasses)(Form);