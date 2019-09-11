import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import OptionBase from '../OptionBase';
import {formatPrice as helperFormatPrice} from 'src/simi/Helper/Pricing';
import SelectField from '@material-ui/core/Select';
import MenuItem from '@material-ui/core/MenuItem';
import { compose } from 'redux';
import { connect } from 'src/drivers';
// import classify from 'src/classify';
import { LazyComponent } from 'src/simi/BaseComponents/LazyComponent/';
require('./giftcardoptions.scss');

const DatePicker = (props)=>{
    return <LazyComponent component={()=>import('./DateType')} {...props}/>
}

class GiftcardOptions extends OptionBase {

    state = {
        isSendToFriend: false,
        deliveryMethod: "email",
        aw_gc_template: "aw_giftcard_email_template",
        aw_gc_amount: "",
        selectedClass: null
    }

    constructor(props){
        super(props);
        this.classes = {}
        this.extraField = props.extraField
        this.timezoneOption = [];
        this.deliveryDateRef = React.createRef();
        this.awGcTemplateRef = React.createRef();
        this.awGcSenderNameRef = React.createRef();
        this.awGcRecipientNameRef = React.createRef();
        this.awGcRecipientEmailRef = React.createRef();
        this.awGcRecipientPhoneRef = React.createRef();
        this.required = ['aw_gc_amount', 'aw_gc_recipient_name', 'aw_gc_recipient_email', 'aw_gc_sender_name', 
            'aw_gc_sender_email', 'aw_gc_template', 'aw_gc_delivery_method', 'aw_gc_delivery_date', 'aw_gc_delivery_date_timezone'];
        this.isCheckOptionRequired = false;

        if(this.extraField instanceof Object && this.extraField.hasOwnProperty('attribute_values')){
            const options = this.extraField.attribute_values;
            if (options.aw_gc_amounts) {
                this.state.aw_gc_amount = options.aw_gc_amounts[0].price
            }
        }
    }

    componentWillMount(){
        const aw_gc_timezones = this.extraField.aw_gc_timezones || null;
        if (aw_gc_timezones) {
            for (const key in aw_gc_timezones) {
                this.timezoneOption.push(
                    <option value={aw_gc_timezones[key].value} key={key}>{Identify.__(aw_gc_timezones[key].label)}</option>
                );
            }
        }
        this.updatePrices(this.state.aw_gc_amount)
    }

    componentDidMount(){
        this.props.myRef(this)
    }

    toggleSendToFriend(e){
        this.setState({isSendToFriend: !this.state.isSendToFriend});
    }

    chooseDeliveryMethod(e){
        let value = e.target.value;
        this.setState({deliveryMethod: value});
    }

    selectTemplate = (template_id) => {
        this.awGcTemplateRef.current.value = template_id;
        if (this.isCheckOptionRequired) {
            this.checkOptionRequired()
        }
        this.setState({aw_gc_template: template_id});
    }

    formChange = () => {
        if (this.isCheckOptionRequired) {
            this.checkOptionRequired()
        }
    }

    deliveryDateChange = () => {
        if (this.isCheckOptionRequired) {
            this.checkOptionRequired()
        }
    }

    gcAmountChange = (event) => {
        jQuery('form [name="'+event.target.name+'"]').val(event.target.value)
        if (this.isCheckOptionRequired) {
            this.checkOptionRequired()
        }
        this.setState({
            aw_gc_amount: event.target.value
        })
        this.updatePrices(event.target.value)
    }

    checkOptionRequired = () => {
        this.isCheckOptionRequired = true;
        let isValidForm = true;
        this.required.map((name, key) => {
            let el = jQuery('form [name="'+name+'"]');
            const inputVal = el.val();
            const errorCss = {border: "1px solid #b91c1c"}
            const errorClass = 'invalid'
            if (inputVal === '' || inputVal === null) {
                if (el.attr('type') === 'hidden') {
                    if (jQuery('form [data-input-bounded-for="'+name+'"]').length) {
                        jQuery('form [data-input-bounded-for="'+name+'"]').addClass(errorClass).css(errorCss);
                    } else {
                        el.next().addClass(errorClass);
                        el.next().css(errorCss);
                    }
                } else {
                    el.addClass(errorClass)
                    el.css(errorCss);
                }
                isValidForm = false;
            } else {
                if (el.attr('type') === 'hidden') {
                    if (jQuery('form [data-input-bounded-for="'+name+'"]').length) {
                        jQuery('form [data-input-bounded-for="'+name+'"]').removeClass(errorClass).css({border: ""});
                    } else {
                        el.next().removeClass(errorClass);
                        el.next().css({border: ""});
                    }
                } else {
                    el.removeClass(errorClass)
                    el.css({border: ""});
                }
            }
        })
        if (!isValidForm) {
            return false
        }
        return true
    }

    getParams = () =>{
        if(!this.checkOptionRequired()){
            return false;
        }
        let aw_gc_params = {
            aw_gc_amount: this.state.aw_gc_amount,
            aw_gc_recipient_name: this.awGcRecipientNameRef.current && this.awGcRecipientNameRef.current.value || '',
            aw_gc_recipient_email: this.awGcRecipientEmailRef.current && this.awGcRecipientEmailRef.current.value || '',
            aw_gc_recipient_phone: this.awGcRecipientPhoneRef.current && this.awGcRecipientPhoneRef.current.value || '',
            aw_gc_sender_name: this.awGcSenderNameRef.current && this.awGcSenderNameRef.current.value || '',
            aw_gc_sender_email: this.props.email || 'guest@guest.com',
            aw_gc_template: this.state.aw_gc_template,
            aw_gc_delivery_method: this.state.deliveryMethod,
            aw_gc_delivery_date: this.state.aw_gc_delivery_date,
            aw_gc_delivery_date_timezone: this.state.aw_gc_delivery_date_timezone,
            aw_gc_message: this.state.aw_gc_message
        }
        this.params = Object.assign(this.params, aw_gc_params);
        return this.params;
    }

    // validateRequired = (value) => {
    //     return !value ? Identify.__('This is a required field.') : undefined;
    // }

    formatPrice = (price) => {
        return helperFormatPrice(price)
    }

    renderOptions = () => {
        const { classes } = this;
        const isSendToFriend = this.state.isSendToFriend;
        const deliveryMethod = this.state.deliveryMethod;
        if(this.extraField instanceof Object && this.extraField.hasOwnProperty('attribute_values')){
            const options = this.extraField.attribute_values;
            const aw_gc_email_templates = options.aw_gc_email_templates || null;
            const mediaUrlPath = this.extraField.aw_gc_template_image_url_path || null;
            if(!options) return <div></div>;
            return (
                <div className="product-options">
                    <div id="giftcard-option" className="giftcard-option">
                        <form id="giftcard-form" onChange={this.formChange}>
                            <div className="option-row">
                                <label htmlFor="aw_gc_amount">{Identify.__('CARD VALUE *')}</label>
                                <div data-input-bounded-for={"aw_gc_amount"}>
                                    <SelectField value={this.state.aw_gc_amount} onChange={this.gcAmountChange} displayEmpty={true} inputProps={{
                                        name: 'aw_gc_amount',
                                        id: 'aw_gc_amount'
                                    }}>
                                        <MenuItem key={Identify.randomString(5)} value={""}>
                                            {Identify.__('Choose an amount')}
                                        </MenuItem>
                                        {
                                            options.aw_gc_amounts && 
                                            options.aw_gc_amounts.map((value, key) => {
                                                return (
                                                    <MenuItem key={key} value={value.price}>
                                                        {this.formatPrice(parseFloat(value.price))}
                                                    </MenuItem>
                                                )
                                            })
                                        }
                                    </SelectField>
                                </div>
                            </div>
                            <div className="option-row">
                                <input id="aw_gc_is_send_to_friend" onClick={(e) => this.toggleSendToFriend(e)} type="checkbox" />
                                <label htmlFor="aw_gc_is_send_to_friend">{Identify.__('Send the gift voucher to a friend')}</label>
                            </div>
                            {
                                isSendToFriend && 
                                <React.Fragment>
                                    <div className="option-row option-templates">
                                        <label>{Identify.__('Select a template *')}</label>
                                        <input type="hidden" name="aw_gc_template" value={this.state.aw_gc_template} ref={this.awGcTemplateRef} />
                                        <ul>
                                            {
                                                mediaUrlPath && aw_gc_email_templates && 
                                                aw_gc_email_templates.map((value, key) => {
                                                    let selectedClass = this.state.aw_gc_template === value.template ? "selected" : ''
                                                    return (
                                                        <li key={key} onClick={() => this.selectTemplate(value.template)} className={selectedClass}>
                                                            <img className="aw-gc-template-image" style={{maxWidth: "80px", maxHeight: "80px"}}
                                                                src={mediaUrlPath + value.image}
                                                                alt="" />
                                                        </li>
                                                    )
                                                }, this)
                                            }
                                        </ul>
                                    </div>
                                    <div className="option-row option-sender-name">
                                        <label>{Identify.__('Sender name *')}</label>
                                        <input type="text" name="aw_gc_sender_name" ref={this.awGcSenderNameRef} placeholder={Identify.__("Sender name")} />
                                    </div>
                                    <div className="option-row option-recipient-name">
                                        <label>{Identify.__('Recipient name *')}</label>
                                        <input type="text" name="aw_gc_recipient_name" ref={this.awGcRecipientNameRef} placeholder={Identify.__("Recipient name")} />
                                    </div>
                                    <div className="option-row option-delivery-method">
                                        <label>{Identify.__('Delivery method *')}</label>
                                        <select name="aw_gc_delivery_method" defaultValue={deliveryMethod} onChange={(e) => this.chooseDeliveryMethod(e)}>
                                            <option value="email">{Identify.__('Email')}</option>
                                            <option value="sms">{Identify.__('SMS')}</option>
                                            <option value="whatsapp">{Identify.__('Whatsapp')}</option>
                                        </select>
                                    </div>
                                    {
                                        deliveryMethod === "email" && 
                                        <div className="option-row option-recipient-email">
                                            <label>{Identify.__('Recipient email *')}</label>
                                            <input type="text" name="aw_gc_recipient_email" ref={this.awGcRecipientEmailRef} placeholder={Identify.__("Recipient email")} />
                                        </div>
                                    }
                                    {
                                        (deliveryMethod === "sms" || deliveryMethod === "whatsapp") && 
                                        <div className="option-row option-recipient-phone">
                                            <label>{Identify.__('Recipient phone *')}</label>
                                            <input type="text" name="aw_gc_recipient_phone" ref={this.awGcRecipientPhoneRef} placeholder={Identify.__("Recipient phone number")} />
                                        </div>
                                    }
                                    <div className="option-row option-delivery-date">
                                        <label>{Identify.__('Send date *')}</label>
                                        <input type="hidden" ref={this.deliveryDateRef} name="aw_gc_delivery_date" placeholder={Identify.__('Select date')}/>
                                        <DatePicker className="date-picker" datetime={false} id='aw_gc_delivery_date' inputRef={this.deliveryDateRef} onChange={this.deliveryDateChange} parent={this} classes={classes}/>
                                    </div>
                                    <div className="option-row option-delivery-timezone">
                                        <label>{Identify.__('Timezone *')}</label>
                                        <select name="aw_gc_delivery_date_timezone" defaultValue="">
                                            <option value="" disabled>{Identify.__('Select timezone')}</option>
                                            { this.timezoneOption }
                                        </select>
                                    </div>
                                    <div className="option-row option-message">
                                        <label>{Identify.__('Message')}</label>
                                        <input type="text" name="aw_gc_message" placeholder={Identify.__('Custom message')}/>
                                    </div>
                                </React.Fragment>
                            }
                        </form>
                    </div>
                </div>
            );
        }
    }

    updatePrices = (amount = this.state.aw_gc_amount) => {
        let exclT = 0;
        let inclT = 0;
        exclT += parseFloat(amount);
        inclT += parseFloat(amount);
        this.parentObj.Price && this.parentObj.Price.setCustomPrice(exclT, inclT);
    }
    
    render(){
        return (
            <div>
                {this.renderOptions()}
            </div>
        )
    }
}

const mapStateToProps = ({ user }) => {
    const { currentUser, isSignedIn } = user
    const { firstname, lastname, email } = currentUser;
    return {
        firstname,
        lastname,
        email,
        isSignedIn
    };
}
// export default GiftcardOptions;
export default compose(
    connect(
        mapStateToProps
    )
)(GiftcardOptions);