import React, {useState, useMemo, useRef} from 'react';
import Identify from "src/simi/Helper/Identify";
import { getOS } from 'src/simi/App/Bianca/Helper';
import IconTelephone from "src/simi/App/Bianca/BaseComponents/Icon/Telephone";
import IconWhatsapp from "src/simi/App/Bianca/BaseComponents/Icon/Whatsapp";
import IconBubble from "src/simi/App/Bianca/BaseComponents/Icon/Bubble";
import IconBubble2 from "src/simi/App/Bianca/BaseComponents/Icon/Bubble2";
import IconCross from "src/simi/App/Bianca/BaseComponents/Icon/Cross";
// import LiveChat from 'react-livechat';
import Modal from 'react-responsive-modal';
import CloseIcon from 'src/simi/App/Bianca/BaseComponents/Icon/Close';
import {sendRequest} from 'src/simi/Network/RestMagento';
import Loading from 'src/simi/BaseComponents/Loading';
import PhoneCodes from './PhoneData';
import Select from 'src/simi/App/Bianca/BaseComponents/FormInput/Select';

require('./style.scss');
if (getOS() === 'MacOS') require('./style-ios.scss');

const $ = window.$;

const Chats = (props) => {
    const {history, isPhone} = props;
    const [liveChatRef, setLiveChatRef] = useState();
    const [openContactModal, setOpenContactModal] = useState(false);
    const [contactPhone, setContactPhone] = useState('');
    const [contactPhoneCode, setContactPhoneCode] = useState(PhoneCodes.KW.code);
    const [contactTime, setContactTime] = useState('');
    const [submittingContact, setSubmittingContact] = useState(false);
    const [submitedContactResult, setSubmitedContactResult] = useState();
    const TriggerPhoneSelectRef = useRef(null);
    const storeConfig = Identify.getStoreConfig() || {};
    const config = storeConfig.simiStoreConfig && storeConfig.simiStoreConfig.config || {};
    const {contact_us, instant_contact, livechat} = config || {};
    const data = instant_contact;
    const livechatLicense = livechat && livechat.license || '';

    const contact_times = contact_us && contact_us.times && contact_us.times.split(',') || [];

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

    const onChangeContactTime = (code) => {
        setContactTime(code);
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
            return 'http://api.whatsapp.com/send?phone=' + phoneNum.split('-').join('').split(' ').join('').match(/(\(.*?\))?\d+/)[0].match(/\d+/g).join('');
            // window.location.href = 'http://api.whatsapp.com/send?phone=' + phoneNum.split('-').join('').split(' ').join('').match(/(\(.*?\))?\d+/)[0].match(/\d+/g).join('');
        }
        return '';
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

    const onChatOpen = (e) => {
        if (!$('.chats-menu').hasClass('open')) {
            $('.chats-menu').addClass('open');
        } else {
            $('.chats-menu').removeClass('open');
        }
        if (!$('.chats-group').hasClass('closed')) {
            $('.chats-group').addClass('closed').removeClass('open');
        } else {
            $('.chats-group').removeClass('closed').addClass('open');
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
        setSubmittingContact(true);
        sendRequest('/rest/V1/simiconnector/contact', (data) => {
            if (data && data === true) {
                // setOpenContactModal(false);
                setSubmitedContactResult(true);
            } else {
                setSubmitedContactResult(false);
            }
            setSubmittingContact(false);
        }, 'POST', null, reqData);
    }

    const phoneItems = useMemo(() => {
        let items = []
        Object.values(PhoneCodes).forEach((item, index) => {
            items.push({
                label: `${Identify.__(item.name)} (${item.code})`,
                value: item.code
            });
        });
        return items;
    } , [PhoneCodes]);

    const timeItems = contact_times.map((time) => {
        return {
            label: Identify.__(time.trim()),
            value: time.trim()
        }
    })

    return (
        <div className={`chats-block ${isPhone ? 'mobile':''}`}>
            <div className="icons-inner">
                <div className="chats-group closed">
                {
                    contact_us && contact_us.enabled && contact_us.enabled === '1' &&
                    <div className="chat-icons contact bubbleIcons d3" onClick={contactAction}>
                        <IconTelephone style={{width: '24px', height: '24px', fill: '#fff'}}/>
                    </div>
                }
                {
                    data && data.phone &&
                    <a href={whatsappAction()} alt="Whatsapp" target="_blank" rel="nofollow">
                        <div className="chat-icons whatsapp bubbleIcons d2">
                            <IconWhatsapp style={{width: '24px', height: '24px', fill: '#fff'}}/>
                        </div>
                    </a>
                }
                {
                    livechat && livechat.enabled === '1' &&
                    <a href={`https://direct.lc.chat/${livechatLicense}/`} alt="Live chat" target="_blank" rel="nofollow">
                        <div className="chat-icons livechat bubbleIcons d1" onClick={livechatAction}>
                            <IconBubble style={{width: '20px', height: '20px', fill: '#fff'}}/>
                        </div>
                    </a>
                }
                </div>
                {
                    ((livechat && livechat.enabled === '1') || 
                    (contact_us && contact_us.enabled && contact_us.enabled === '1') || 
                    (data && data.phone)) &&
                    <div className="chat-icons chats-menu" onClick={onChatOpen}>
                        <IconBubble2 style={{width: '20px', height: '20px', fill: '#fff'}}/>
                        <IconCross style={{width: '20px', height: '20px', fill: '#fff'}}/>
                    </div>
                }
            </div>
            {/* {
                livechat && livechat.enabled === '1' && livechatLicense &&
                <LiveChat className={`${liveChatRef ? 'livechat-active':'livechat-disabled'}`} license={parseInt(livechatLicense)} onChatLoaded={onChatLoaded} onChatWindowMinimized={(e) => onChatWindowMinimized(e)} />
            } */}

            <Modal open={openContactModal} onClose={onCloseContact}
                overlayId={'contact-modal-overlay'}
                modalId={'contact-modal'}
                closeIconId={'contact-modal-close'}
                classNames={{overlay: Identify.isRtl()?"rtl-root":""}}
                closeIconSize={16}
                closeIconSvgPath={<CloseIcon style={{fill: '#101820'}}/>}
            >
                <div className={`contact-wrap ${isPhone ? 'mobile':''}`}>
                    <h3 className="title"><span>{Identify.__('Instant Contact')}</span></h3>
                    { submitedContactResult !== true && 
                        <span className="header-text">{Identify.__('Please, specify your phone number and when it will be convenient for you to take a call.')}</span>
                    }
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
                                    <label className="arrow-down" htmlFor="contact-phone" ref={TriggerPhoneSelectRef}></label>
                                    <input id='contact-phone' name="contact-phone" type="tel" onChange={onChangeContactPhone} 
                                        value={contactPhone} 
                                        placeholder={contactPhoneCode} />
                                    {
                                        <Select items={phoneItems} onChange={onChangeContactPhoneCode}
                                            triggerRef={TriggerPhoneSelectRef}
                                        />
                                    }
                                </div>
                            </div>
                            <div className="form-row time">
                                <label htmlFor="contact-time">{Identify.__('Time')}</label>
                                {
                                    contact_us && contact_us.enabled && contact_us.enabled === '1' && contact_us.times &&
                                    <Select className="contact-input time-input" 
                                        items={timeItems} onChange={onChangeContactTime} 
                                        showSelected={true}
                                        selected={timeItems[0]}
                                        placeholder={Identify.__('Time')} 
                                        hiddenInput={{name: 'time', id: 'contact-time', defaultValue: timeItems[0] ? timeItems[0].value : ''}}
                                    />
                                }
                            </div>
                            <div className="form-btn">
                            {
                                submittingContact === true ? <Loading /> :
                                <div className="btn" onClick={submitContactForm}><span>{Identify.__('Submit')}</span></div>
                            }
                            </div>
                        </div>
                    }
                </div>
            </Modal>
        </div>
    );
}
export default Chats