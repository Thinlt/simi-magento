import React, { useState } from 'react';
import Identify from "src/simi/Helper/Identify";
import Modal from 'react-responsive-modal';
import CloseIcon from 'src/simi/App/Bianca/BaseComponents/Icon/Close';
import { getOS } from 'src/simi/App/Bianca/Helper';
import {sendRequest} from 'src/simi/Network/RestMagento';
import useWindowSize from 'src/simi/App/Bianca/Hooks';
import Loading from 'src/simi/BaseComponents/Loading';

require('./style.scss');
if (["MacOS", "iOS"].includes(getOS())) require('./style-ios.scss');

const SizeGuide = (props) => {
    const $ = window.$;
    const {product, customerId, customerFirstname, customerLastname, history, isSignedIn, isPopup, onClose} = props;
    const [submitting, setSubmitting] = useState(false);
    const [success, setSuccess] = useState(null);
    const size = useWindowSize();
    const isPhone = (size.width < 1024) || false;

    const storeConfig = Identify.getStoreConfig();
    const { size_guide } = storeConfig && storeConfig.simiStoreConfig && storeConfig.simiStoreConfig.config || {};

    const submitForm = () => {
        if (!isSignedIn) {
            history && history.push({pathname: '/login.html', pushTo: history.location.pathname});
            return;
        }
        let postData = {
            bust: $('.size-guide #bust').val(),
            waist: $('.size-guide #waist').val(),
            hip: $('.size-guide #hip').val(),
            product_id: product && product.id || '',
            product_name: product && product.name || '',
            customer_id: customerId || '',
            customer_name: customerFirstname && `${customerFirstname} ${customerLastname}` || ''
        }
        const validateField = ['bust', 'waist', 'hip'];
        let validated = true;
        for(let i in validateField){
            if (!postData[validateField[i]] && validateField[i]) {
                $(`.size-guide #${validateField[i]}`).addClass('error');
                validated = false;
            } else {
                $(`.size-guide #${validateField[i]}`).removeClass('error');
            }
        }
        if (!validated) {
            return;
        }
        if (!submitting) {
            sendRequest('/rest/V1/simiconnector/sizechart', (data) => {
                if (data === true || data === 'true' || data === 1 || data === '1') {
                    setSuccess(true);
                } else {
                    setSuccess(false);
                }
                setSubmitting(false);
            }, 'POST', null, postData);
            setSubmitting(true);
        }
    }

    const onCloseModal = () => {
        if(onClose && typeof onClose === 'function'){
            onClose();
        }
        setSubmitting(false);
    }

    return (
        <Modal open={isPopup} onClose={onCloseModal}
                overlayId={'size_guide-modal-overlay'}
                modalId={'size_guide-modal'}
                closeIconId={'size_guide-modal-close'}
                closeIconSize={16}
                closeIconSvgPath={<CloseIcon style={{fill: '#101820'}}/>}
                classNames={{overlay: Identify.isRtl()?"rtl-root":""}}
            >
            <div className={`size-guide ${isPhone ? 'mobile':''}`}>
                <div className="title"><h3 className="_text_ios">{Identify.__('Size Guide')}</h3></div>
                <div className="header"><span className="_text">{Identify.__('Enter your measurement to get your personalized product.')}</span></div>
                {
                    success === true && 
                    <div className="header"><span className="_text">{Identify.__('Thank you for submitting information. We will contact you soon.')}</span></div>
                }
                {
                    success === false && 
                    <div className="header"><span className="_text error">{Identify.__('There was an error. Please try again')}</span></div>
                }
                <div className="form-your-size">
                    <div className="form">
                        <div className="form-row">
                            <span className="_text_ios">{Identify.__('BUST')}</span>
                            <input id="bust" name="bust" placeholder={Identify.__('Bust (in cm)')}/>
                        </div>
                        <div className="form-row">
                            <span className="_text_ios">{Identify.__('WAIST')}</span>
                            <input id="waist" name="waist" placeholder={Identify.__('Waist (in cm)')}/>
                        </div>
                        <div className="form-row">
                            <span className="_text_ios">{Identify.__('HIP')}</span>
                            <input id="hip" name="hip" placeholder={Identify.__('Hip (in cm)')}/>
                        </div>
                        <div className="form-submit">
                            <div className={`btn ${submitting ? 'disabled':''}`} onClick={submitForm}>
                                <span className="_text_ios">{Identify.__('Submit')}</span>
                            </div>
                        </div>
                    </div>
                </div>
                {
                    submitting && <Loading />
                }
                <div className="size-chart">
                    <div className="chart-title"><h4 className="_text_ios">{Identify.__('Size Chart')}</h4></div>
                    <div className="chart-table">
                        {
                            size_guide && size_guide.image_file && size_guide.image_file.path &&
                            (
                                isPhone && size_guide.image_file_mobile && size_guide.image_file_mobile.path ?
                                <img className="img-responsive" src={`${window.SMCONFIGS.media_url_prefix}${size_guide.image_file_mobile.path}`} alt="Size guide"/>
                                :
                                <img className="img-responsive" src={`${window.SMCONFIGS.media_url_prefix}${size_guide.image_file_mobile.path}`} alt="Size guide"/>
                            )
                        }
                        {/* <div className="chart-t-row row-header">
                            <div className="chart-t-cell first">{Identify.__('Size')}</div>
                            <div className="chart-t-cell">{Identify.__('Numerical Size')}</div>
                            <div className="chart-t-cell">{Identify.__('Bust')}</div>
                            <div className="chart-t-cell">{Identify.__('Waist')}</div>
                            <div className="chart-t-cell">{Identify.__('High Hip')}</div>
                            <div className="chart-t-cell last">{Identify.__('Low Hip')}</div>
                        </div>
                        <div className="chart-t-row row-body">
                            <div className="chart-t-cell first">{Identify.__('XXS')}</div>
                            <div className="chart-t-cell">{Identify.__('00')}</div>
                            <div className="chart-t-cell">{Identify.__('30.5" - 31.5"')}</div>
                            <div className="chart-t-cell">{Identify.__('23" - 24"')}</div>
                            <div className="chart-t-cell">{Identify.__('30" - 31"')}</div>
                            <div className="chart-t-cell last">{Identify.__('33.5" - 34.5"')}</div>
                        </div>
                        <div className="chart-t-row row-body">
                            <div className="chart-t-cell first">{Identify.__('XS')}</div>
                            <div className="chart-t-cell">{Identify.__('00')}</div>
                            <div className="chart-t-cell">{Identify.__('30.5" - 31.5"')}</div>
                            <div className="chart-t-cell">{Identify.__('23" - 24"')}</div>
                            <div className="chart-t-cell">{Identify.__('30" - 31"')}</div>
                            <div className="chart-t-cell last">{Identify.__('33.5" - 34.5"')}</div>
                        </div>
                        <div className="chart-t-row row-body">
                            <div className="chart-t-cell first">{Identify.__('S')}</div>
                            <div className="chart-t-cell">{Identify.__('00')}</div>
                            <div className="chart-t-cell">{Identify.__('30.5" - 31.5"')}</div>
                            <div className="chart-t-cell">{Identify.__('23" - 24"')}</div>
                            <div className="chart-t-cell">{Identify.__('30" - 31"')}</div>
                            <div className="chart-t-cell last">{Identify.__('33.5" - 34.5"')}</div>
                        </div>
                        <div className="chart-t-row row-body">
                            <div className="chart-t-cell first">{Identify.__('M')}</div>
                            <div className="chart-t-cell">{Identify.__('00')}</div>
                            <div className="chart-t-cell">{Identify.__('30.5" - 31.5"')}</div>
                            <div className="chart-t-cell">{Identify.__('23" - 24"')}</div>
                            <div className="chart-t-cell">{Identify.__('30" - 31"')}</div>
                            <div className="chart-t-cell last">{Identify.__('33.5" - 34.5"')}</div>
                        </div>
                        <div className="chart-t-row row-body">
                            <div className="chart-t-cell first">{Identify.__('L')}</div>
                            <div className="chart-t-cell">{Identify.__('00')}</div>
                            <div className="chart-t-cell">{Identify.__('30.5" - 31.5"')}</div>
                            <div className="chart-t-cell">{Identify.__('23" - 24"')}</div>
                            <div className="chart-t-cell">{Identify.__('30" - 31"')}</div>
                            <div className="chart-t-cell last">{Identify.__('33.5" - 34.5"')}</div>
                        </div>
                        <div className="chart-t-row row-body">
                            <div className="chart-t-cell first">{Identify.__('XL')}</div>
                            <div className="chart-t-cell">{Identify.__('00')}</div>
                            <div className="chart-t-cell">{Identify.__('30.5" - 31.5"')}</div>
                            <div className="chart-t-cell">{Identify.__('23" - 24"')}</div>
                            <div className="chart-t-cell">{Identify.__('30" - 31"')}</div>
                            <div className="chart-t-cell last">{Identify.__('33.5" - 34.5"')}</div>
                        </div>
                        <div className="chart-t-row row-body">
                            <div className="chart-t-cell first">{Identify.__('XXL')}</div>
                            <div className="chart-t-cell">{Identify.__('00')}</div>
                            <div className="chart-t-cell">{Identify.__('30.5" - 31.5"')}</div>
                            <div className="chart-t-cell">{Identify.__('23" - 24"')}</div>
                            <div className="chart-t-cell">{Identify.__('30" - 31"')}</div>
                            <div className="chart-t-cell last">{Identify.__('33.5" - 34.5"')}</div>
                        </div> */}
                    </div>
                </div>

            </div>
        </Modal>
    )
}

export default SizeGuide