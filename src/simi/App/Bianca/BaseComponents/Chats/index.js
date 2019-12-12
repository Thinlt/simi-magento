import React, {useEffect, useState} from 'react';
import Identify from "src/simi/Helper/Identify";
import { getOS } from 'src/simi/App/Bianca/Helper';
import IconTelephone from "src/simi/App/Bianca/BaseComponents/Icon/Telephone";
import IconWhatsapp from "src/simi/App/Bianca/BaseComponents/Icon/Whatsapp";
import IconBubble from "src/simi/App/Bianca/BaseComponents/Icon/Bubble";
import LiveChat from 'react-livechat';
import Modal from 'react-responsive-modal';
import CloseIcon from 'src/simi/App/Bianca/BaseComponents/Icon/Close';
import PhoneCode from 'react-phone-code';
import {sendRequest} from 'src/simi/Network/RestMagento';

require('./style.scss');
if (getOS() === 'MacOS') require('./style-ios.scss');

const $ = window.$;

const Chats = (props) => {
    const {history, isPhone} = props;
    const [liveChatRef, setLiveChatRef] = useState();
    const [openContactModal, setOpenContactModal] = useState(false);
    const [contactPhone, setContactPhone] = useState('');
    const [contactPhoneCode, setContactPhoneCode] = useState('');
    const [submitedContactResult, setSubmitedContactResult] = useState();
    const storeConfig = Identify.getStoreConfig() || {};
    const config = storeConfig.simiStoreConfig && storeConfig.simiStoreConfig.config || {};
    const {instant_contact, livechat} = config || {};
    const data = instant_contact;
    const livechatLicense = livechat && livechat.license || '';

    const contactAction = () => {
        setOpenContactModal(true);
        setSubmitedContactResult(null);
    }

    const onCloseContact = (e) => {
        setOpenContactModal(false);
    }

    const onChangeContactPhone = (e) => {
        if (e.target.value === undefined || e.target.value === '') {
            $('.contact-input.phone-input').addClass('invalid');
        } else {
            $('.contact-input.phone-input').removeClass('invalid');
        }
        if (contactPhone.length === 0) {
            setContactPhone(contactPhoneCode + e.target.value);
        } else {
            setContactPhone(e.target.value);
        }
    }

    const onChangeContactName = (e) => {
        if (e.target.value === undefined || e.target.value === '') {
            $('.contact-input.name-input').addClass('invalid');
        } else {
            $('.contact-input.name-input').removeClass('invalid');
        }
    }

    const onChangeContactPhoneCode = (code) => {
        setContactPhone(code);
        setContactPhoneCode(code);
    }

    const whatsappAction = () => {
        let {phone} = data;
        let phoneNum;
        if (phone && typeof phone === 'string') {
            phoneNum = phone;
        } else if(phone && phone instanceof Array) {
            phoneNum = phone[0];
        }
        if (phoneNum) {
            window.location.href = 'http://api.whatsapp.com/send?phone=' + phoneNum.split('-').join('').split(' ').join('').match(/(\(.*?\))?\d+/)[0].match(/\d+/g).join('');
        }
    }

    const livechatAction = () => {
        if (liveChatRef) {
            liveChatRef.open_chat_window();
            if($('#chat-widget-container').length){
                $('#chat-widget-container').css({visibility: 'visible'});
            }
        }
    }

    const onChatLoaded = (ref) => {
        ref.on_after_load = () => {
            ref.hide_chat_window();
            setLiveChatRef(ref);
        }
    }

    const onChatWindowMinimized = (e) => {
        if (window.LC_API) {
            window.LC_API.hide_chat_window();
        }
    }

    const submitContactForm = () => {
        let reqData = {
            'name': $('#contact-name').val(),
            'phone': contactPhone,
            'time': $('#contact-time').val(),
        }
        let error = false;
        if (reqData.name === undefined || reqData.name === '') {
            $('.contact-input.name-input').addClass('invalid');
            error = true;
        }
        if (reqData.phone.replace(contactPhoneCode, '') === undefined || reqData.phone.replace(contactPhoneCode, '') === '') {
            $('.contact-input.phone-input').addClass('invalid');
            error = true;
        }
        if (error) return;
        sendRequest('/rest/V1/simiconnector/contact', (data) => {
            if (data && data === true) {
                // setOpenContactModal(false);
                setSubmitedContactResult(true);
            } else {
                setSubmitedContactResult(false);
            }
        }, 'POST', null, reqData);
    }

    return (
        <div className={`chats-block ${isPhone ? 'mobile':''}`}>
            <div className="icons-inner">
                <div className="chat-icons contact" onClick={contactAction}>
                    <IconTelephone style={{width: '24px', height: '24px', fill: '#fff'}}/>
                </div>
                <div className="chat-icons whatsapp" onClick={whatsappAction}>
                    <IconWhatsapp style={{width: '24px', height: '24px', fill: '#fff'}}/>
                </div>
                {
                    livechat && livechat.enabled === '1' &&
                    <div className="chat-icons livechat" onClick={livechatAction}>
                        <IconBubble style={{width: '20px', height: '20px', fill: '#fff'}}/>
                    </div>
                }
            </div>
            {
                livechat && livechat.enabled === '1' && livechatLicense &&
                <LiveChat className={`${liveChatRef ? 'livechat-active':'livechat-disabled'}`} license={parseInt(livechatLicense)} onChatLoaded={onChatLoaded} onChatWindowMinimized={(e) => onChatWindowMinimized(e)} />
            }

            <Modal open={openContactModal} onClose={onCloseContact}
                overlayId={'contact-modal-overlay'}
                modalId={'contact-modal'}
                closeIconId={'contact-modal-close'}
                closeIconSize={16}
                closeIconSvgPath={<CloseIcon style={{fill: '#101820'}}/>}
            >
                <div className="contact-wrap">
                    <h3 className="title"><span>{Identify.__('Instant Order')}</span></h3>
                    <span className="header-text">{Identify.__('Please, specify your phone number and when it will be convenient for you to take a call.')}</span>
                    {
                        submitedContactResult === true ?
                            <div className="message success">{Identify.__('Thank you for submitting the information! We will contact you soon.')}</div>
                        :
                        submitedContactResult === false ? 
                            <div className="message error"><h3>{Identify.__('Please try again!')}</h3></div>
                        :
                        <div className="contact-form">
                            <div className="form-row name">
                                <label htmlFor="contact-name">{Identify.__('Name')}</label>
                                <input id="contact-name" className="contact-input name-input" name="name" placeholder={Identify.__('Your name')}
                                onChange={onChangeContactName} />
                            </div>
                            <div className="form-row phone">
                                <label htmlFor="contact-phone">{Identify.__('Phone Number')}</label>
                                <div className="contact-input phone-input">
                                    <label className="arrow-down" htmlFor="phone-code"></label>
                                    <PhoneCode
                                        onSelect={onChangeContactPhoneCode} // required
                                        showFirst={['KW', 'US']}
                                        defaultValue='KW'
                                        id='phone-code'
                                        name='phone-code'
                                        className='phone-code'
                                        optionClassName='phone-code-option'
                                    />
                                    <input name="contact-phone" onChange={onChangeContactPhone} 
                                        value={contactPhone} 
                                        placeholder={contactPhoneCode}/>
                                </div>
                            </div>
                            <div className="form-row time">
                                <label htmlFor="contact-time">{Identify.__('Time')}</label>
                                <div className="contact-input time-input">
                                    <select id="contact-time" name="time">
                                        <option value="now">{Identify.__('Now')}</option>
                                        <option value="next">{Identify.__('Next day')}</option>
                                    </select>
                                </div>
                            </div>
                            <div className="form-btn">
                                <div className="btn" onClick={submitContactForm}><span>{Identify.__('Submit')}</span></div>
                            </div>
                        </div>

                    }
                </div>
            </Modal>
        </div>
    );
}
export default Chats