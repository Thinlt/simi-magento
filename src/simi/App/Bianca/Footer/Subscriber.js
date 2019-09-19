import React, {useRef, useState, useEffect} from 'react';
import Identify from "src/simi/Helper/Identify";
import { connect } from 'src/drivers';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { SimiMutation } from 'src/simi/Network/Query';
import {validateEmail} from 'src/simi/Helper/Validation';
import Loading from "src/simi/BaseComponents/Loading";
import MUTATION_GRAPHQL from 'src/simi/queries/guestSubscribe.graphql'

const Subscriber = props => {
    const emailInput = useRef('');
    const mutationRef = useRef('');
    const [invalid, setInvalid] = useState('');
    const [submited, setSubmited] = useState(false);
    const [waiting, setWaiting] = useState(false);

    const formAction = (e) => {
        e.preventDefault();
        if (validateForm()) {
            $(mutationRef.current).trigger('click');
        }
        setSubmited(true);
    }

    const mutationAction = (mutation) => {
        mutation({variables: {email: emailInput.current.value}});
        setWaiting(true);
    }

    const responseLoaded = (data) => {
        if (data && data.subscribe && data.subscribe.message) {
            setWaiting(false);
            if (data.subscribe.status === '1') {
                if (waiting) {
                    props.toggleMessages([{
                        type: data.subscribe.status === '1' ? 'success':'error',
                        message: data.subscribe.message,
                        auto_dismiss: true
                    }]);
                    emailInput.current.value = ''; //reset form
                }
            }
        }
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

    const className = props.className ? props.className : '';
    const classForm = `${className} subscriber-form ${invalid}`;

    return (
        <React.Fragment>
            <SimiMutation mutation={MUTATION_GRAPHQL}>
                {(subscribeCall, { data }) => {
                    if (data && data.subscribe && data.subscribe.message) {
                        responseLoaded(data);
                    }
                    return (
                        <button onClick={e => mutationAction(subscribeCall)} ref={mutationRef} style={{display: 'none'}}/>
                    )
                }}
            </SimiMutation>
            <React.Fragment>
                {
                    console.log('loading..')
                }
                <div className={classForm}>
                    <form className={props.formClassName} onSubmit={formAction}>
                        <label htmlFor="subcriber-email">{Identify.__('Email *')}</label>
                        <input id="subcriber-email" onChange={validateChange} ref={emailInput} name="email" className={`email ${invalid}`} />
                        <button type="submit"><i className="icon-arrow-right icons"></i></button>
                    </form>
                </div>
                { waiting && <Loading/> }
            </React.Fragment>
        </React.Fragment>
    )
}

const mapDispatchToProps = {
    toggleMessages,
}

export default connect(
    null,
    mapDispatchToProps
)(Subscriber);