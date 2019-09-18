import React, {useRef, useState} from 'react';
import Identify from "src/simi/Helper/Identify";
import { connect } from 'src/drivers';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { SimiMutation } from 'src/simi/Network/Query';
import {validateEmail} from 'src/simi/Helper/Validation';
import Loading from "src/simi/BaseComponents/Loading";
import MUTATION_GRAPHQL from 'src/simi/queries/guestSubscribe.graphql'

const Subscriber = props => {
    const emailInput = useRef('');
    const [invalid, setInvalid] = useState('');
    const [submited, setSubmited] = useState(false);
    const [waiting, setWaiting] = useState(false);

    const formAction = (action, e) => {
        e.preventDefault();
        if (validateForm()) {
            subscribeAction(action);
        }
        setSubmited(true);
    }

    const validateForm = () => {
        if (!validateEmail(emailInput.current.value)) {
            setInvalid('error invalid');
            return false;
        }
        setInvalid('');
        return true;
    }

    const validateChange = () => {
        if (submited) {
            validateForm();
        }
    }

    const subscribeAction = (action) => {
        setWaiting(true);
        action({variables: {email: emailInput.current.value}})
    }

    return (
        <SimiMutation mutation={MUTATION_GRAPHQL}>
            {(subscribeCall, { data }) => {
                let isResponse = false;
                if (data && data.subscribe && data.subscribe.message) {
                    isResponse = true;
                    if (data.subscribe.status === '1') {
                        if (waiting) {
                            props.toggleMessages([{
                                type: data.subscribe.status === '1' ? 'success':'error',
                                message: data.subscribe.message,
                                auto_dismiss: true
                            }]);
                            emailInput.current.value = ''; //reset form
                            setWaiting(false);
                            setSubmited(false);
                        }
                    }
                }
                const className = props.className ? props.className : '';
                const classForm = `${className} subscriber-form ${invalid}`;
                return (
                    <>
                    <div className={classForm}>
                        <form className={props.formClassName} onSubmit={(e) => {formAction(subscribeCall, e)}}>
                            <label htmlFor="subcriber-email">{Identify.__('Email *')}</label>
                            <input id="subcriber-email" onChange={validateChange} ref={emailInput} name="email" className={`email ${invalid}`} />
                            <button type="submit"><i className="icon-arrow-right icons"></i></button>
                        </form>
                        
                    </div>
                    { (waiting && !isResponse) && <Loading/> }
                    </>
                )
            }}
        </SimiMutation>
    )
}

const mapDispatchToProps = {
    toggleMessages,
}

export default connect(
    null,
    mapDispatchToProps
)(Subscriber);