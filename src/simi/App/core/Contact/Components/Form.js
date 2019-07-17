/* eslint-disable prefer-const */
import React, { Component } from 'react';
import Identify from 'src/simi/Helper/Identify';
import { validateEmail } from 'src/simi/Helper/Validation';
// import {compose} from 'redux';
import classify from "src/classify";
import defaultClasses from "../style.css";
import {Colorbtn} from '../../../../BaseComponents/Button';
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading'
import { sendContact } from '../../../../Model/Contact';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { connect } from 'src/drivers';
import { compose } from 'redux';

const $ = window.$;
class Form extends Component {
    proceedData(data){
        if(data){
            hideFogLoading()
            if (data.errors.length) {
                const errors = data.errors.map(error => {
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
    }

    validateForm = () => {
        let formCheck = true;
        $('#contact-form').find('.required').each(function () {
            if ($(this).val() === '' || $(this).val().length === 0) {
                formCheck = false;
                $(".message").show();
            } else {
                $(".message").hide();
                if ($(this).attr('name') === 'email') {
                    if (!validateEmail($(this).val())) {
                        formCheck = false;
                        $(".invalid-email").show();
                    }
                }
            }
        });

        return formCheck;
    };

    handleSubmit = (e) => {
        e.preventDefault();
        const isValidated = this.validateForm()
        const form = $('#contact-form').serializeArray();
        let value = {};
        if(isValidated){
            for(let i in form){
                let field = form[i];
                
                value[field.name] = field.value;
            }
            showFogLoading();
            sendContact(value,this.proceedData.bind(this))
        }
        // console.log(value)
    }

    render() {
        const { classes } = this.props
        // console.log(data, 'log ')
        
        const errorMessage = <small className="message" style={{display: 'none', color:"#fa0a11"}}>This field is required</small>
        const errorEmail = <small className="invalid-email" style={{display: 'none', color:"#fa0a11"}}>Invalid email</small>

        return (
            <div className={classes['form-container']}>
                <form id="contact-form" onSubmit={this.handleSubmit}>
                    <h2>{Identify.__("Contact Us")}</h2>
                    <div className='form-group'>
                        <input type="text" className={`form-control ${classes['base-textField']} required`} name="name" placeholder="Name *" />
                        {errorMessage}
                    </div>
                    <div className='form-group'>
                        <input type="text" className={`form-control ${classes['base-textField']} required`} name="company" placeholder="Company Name *" />
                        {errorMessage}
                    </div>
                    <div className='form-group'>
                        <input type="text" className={`form-control ${classes['base-textField']} required`} name="email" placeholder="Email Address *" />
                        {errorMessage}
                        {errorEmail}
                    </div>
                    <div className='form-group'>
                        <input type="text" className={`form-control ${classes['base-textField']} required`} name="phone" placeholder="Telephone *" />
                        {errorMessage}
                    </div>
                    <div className="form-group fg-textarea">
                        <textarea className={`form-control ${classes['base-textareaField']} required`} name="message" cols="30" rows="5" placeholder="Enter your message here" ></textarea>
                        {errorMessage}
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

const mapDispatchToProps = {
    toggleMessages,
}

export default compose(
    classify(defaultClasses),
    connect(
        null,
        mapDispatchToProps
    )
) (Form);

// export default classify(defaultClasses)(Form);