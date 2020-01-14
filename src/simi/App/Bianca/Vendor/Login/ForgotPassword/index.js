import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import ForgotPasswordForm from './ForgotPasswordForm';
import FormSubmissionSuccessful from './FormSubmissionSuccessful';
import TitleHelper from 'src/simi/Helper/TitleHelper'
import Identify from 'src/simi/Helper/Identify'
import {forgotPassword} from 'src/simi/Model/Customer'
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading'
import {showToastMessage} from 'src/simi/Helper/Message';
import classes from './forgotPassword.css';

class ForgotPassword extends Component {

    state  = {resetSubmited: false}

    static propTypes = {
        classes: PropTypes.shape({
            instructions: PropTypes.string
        })
    };

    handleFormSubmit = ({ email }) => {
        showFogLoading()
        forgotPassword(this.resetSubmited, email)
    }

    resetSubmited = (data) => {
        hideFogLoading()
        if (data && !data.errors) {
            let text = '';
            if (data.message) {
                const messages = data.message;
                for (const i in messages) {
                    const message = messages[i];
                    text += message + ' ';
                }
            }
            this.successMessage = text
            this.setState({ resetSubmited: true})
            if(this.props.hideDesigner){
                this.props.hideDesigner();
            }
        } else {
            let messages = ''
            data.errors.map(value => {
                messages +=  value.message
            })
            showToastMessage(messages)
            this.setState({ resetSubmited: false})
        }
    }

    handleContinue = () => {
        this.props.history.push('/');
    };

    render() {
        const { history, email } = this.props;
        const {resetSubmited} = this.state

        if (resetSubmited) {
            return (
                <FormSubmissionSuccessful
                    email={email}
                    onContinue={this.handleContinue}
                    successMessage={this.successMessage}
                />
            );
        }

        return (
            <Fragment>
                {TitleHelper.renderMetaHeader({
                    title:Identify.__('Forgot password')
                })}
                <div className={`${classes['wrap']} ${Identify.isRtl() ? classes['rtl-wrap'] : null}`}>
                    <div className={classes["title"]}>{Identify.__("forgot password?".toUpperCase())}</div>
                    <p className={classes.instructions}>
                        {Identify.__('Enter your email address to reset your password.')}
                    </p>
                    <ForgotPasswordForm
                        onSubmit={this.handleFormSubmit}
                        history={history}
                    />
                </div>
            </Fragment>
        );
    }
}

export default ForgotPassword;
