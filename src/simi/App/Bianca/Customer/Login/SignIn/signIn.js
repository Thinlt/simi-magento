import React, { Component } from 'react';
import { bool, func } from 'prop-types';
import { Form } from 'informed';
import TextInput from 'src/components/TextInput';
import { isRequired } from 'src/util/formValidators';
import Identify from 'src/simi/Helper/Identify'
import {configColor} from 'src/simi/Config'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import UserIcon from '../../../../../BaseComponents/Icon/User';
import Key from '../../../../../BaseComponents/Icon/Key';
import Checkbox from 'src/simi/BaseComponents/Checkbox';
import Facebook from 'src/simi/BaseComponents/Icon/Facebook';
import Instagram from 'src/simi/BaseComponents/Icon/Instagram';
import Twitter from 'src/simi/BaseComponents/Icon/Twitter';
import GooglePlus from 'src/simi/BaseComponents/Icon/TapitaIcons/GooglePlus';

require("./signIn.scss")

class SignIn extends Component {
    state = {
        isSeleted: false
    }

    handleCheckBox = () => {
        this.setState({isSeleted: !this.state.isSeleted})
    }

    static propTypes = {
        isGettingDetails: bool,
        onForgotPassword: func.isRequired,
        signIn: func
    };

    render() {
        const {isSeleted} = this.state;
        const {classes} = this.props;

        return (
            <div className='root sign-in-form'>
                {TitleHelper.renderMetaHeader({
                    title:Identify.__('Sign In')
                })}
                <Form
                    className='form'
                    getApi={this.setFormApi}
                    onSubmit={() => this.onSignIn()}
                >
                <div className='userInput'>
                    <UserIcon className='userIcon' style={{width:'16px',height:'16px'}}/>
                    <TextInput
                        classes={classes}
                        style={{paddingLeft:"56px"}}
                        autoComplete="email"
                        field="email"
                        validate={isRequired}
                        validateOnBlur
                        placeholder="Email"
                    />
                </div>
                <div className='passwordInput'>
                    <Key className='passwordIcon' style={{width:'16px',height:'16px'}}/>
                    <TextInput
                        classes={classes}
                        style={{paddingLeft:"56px"}}
                        autoComplete="current-password"
                        field="password"
                        type="password"
                        validate={isRequired}
                        validateOnBlur
                        placeholder="Password"
                    />
                </div>
                    <div className='signInAction'>
                        <Checkbox onClick={this.handleCheckBox} label={Identify.__("Remember me")} selected={isSeleted}/>
                        <button
                            type="button"
                            className='forgotPassword'
                            onClick={this.handleForgotPassword}
                        >
                            {Identify.__('Forgot password')}
                        </button>
                    </div>
                    <div className='signInButtonCtn'>
                        <button 
                            priority="high" className='signInButton' type="submit" 
                            style={{backgroundColor: '#101820', color: configColor.button_text_color}}>
                            {Identify.__('Sign In'.toUpperCase())}
                        </button>
                    </div>
                </Form>
                <div className='signInDivider'>
                    <span className='signInWith'>{Identify.__("or sign in with".toUpperCase())}</span>
                </div>
                <div className='socialMedia'>
                    <Facebook className="social-icon"/>
                    <Twitter className="social-icon"/>
                    <GooglePlus className="social-icon"/>
                    <Instagram className="social-icon"/>
                    <Instagram className="social-icon"/>
                </div>
                <div className='showCreateAccountButtonCtn'>
                    <button priority="high" className='showCreateAccountButton' onClick={this.showCreateAccountForm} type="submit">
                        {Identify.__('Create an Account')}
                    </button>
                </div>
            </div>
        );
    }

    handleForgotPassword = () => {
        this.props.onForgotPassword();
    };

    onSignIn() {
        const username = this.formApi.getValue('email');
        const password = this.formApi.getValue('password');
        this.props.onSignIn(username, password)
    }

    setFormApi = formApi => {
        this.formApi = formApi;
    };

    showCreateAccountForm = () => {
        this.props.showCreateAccountForm();
    };
}

export default SignIn;