import React from 'react';
import PropTypes from 'prop-types';

import Identify from 'src/simi/Helper/Identify'
import classes from './formSubmissionSuccessful.css';

const FormSubmissionSuccessful = props => {
    const {successMessage} = props
    const textMessage = successMessage?successMessage:Identify.__('If there is an account associated with that email, you will receive an email with a link to change your password');
    const { onContinue } = props;

    return (
        <div>
            <p className={classes.text}>{textMessage}</p>
            <div className={classes.buttonContainer}>
                <div 
                    onClick={onContinue}
                    className={classes.submitButton}
                >
                    <span className={classes.continue}>{Identify.__('Continue Shopping')}</span>
                </div>
            </div>
        </div>
    );
}
FormSubmissionSuccessful.propTypes = {
    onContinue: PropTypes.func.isRequired,
    successMessage: PropTypes.string
};


export default FormSubmissionSuccessful;
