import React, {useState} from 'react';
import useWindowSize from 'src/simi/App/Bianca/Hooks';
import Identify from "src/simi/Helper/Identify";
import { getOS } from 'src/simi/App/Bianca/Helper';
// import Loading from 'src/simi/BaseComponents/Loading';
import { withRouter } from 'react-router-dom';
import BreadCrumb from "src/simi/App/Bianca/BaseComponents/BreadCrumb";
import Select from 'src/simi/App/Bianca/BaseComponents/FormInput/Select';
import ArrowDown from 'src/simi/App/Bianca/BaseComponents/Icon/ArrowDown';
import {submitQuote, uploadFile} from 'src/simi/Model/Clothing';
import {showToastMessage} from 'src/simi/Helper/Message';
import {validateEmail, validateTelephone, validateNumber} from 'src/simi/Helper/Validation';
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading';
import CrossIcon from 'src/simi/App/Bianca/BaseComponents/Icon/Cross';


require('./ClothingService.scss');

const $ = window.$;

const ClothingService = (props) => {

    const dataValidate = {
        name: ['empty'],
        phone: ['empty'],
        email: ['empty', 'email'],
        address: ['empty'],
        service: ['empty'],
        qty: ['empty'],
        detail: ['empty'],
        files: ['empty']
    }
    const resetFormData = () => {
        let formData = {}
        for(let name in dataValidate){
            if (name === 'files') {
                formData[name] = {};
            }else{
                formData[name] = '';
            }
        }
        return formData;
    }

    const [isFormSubmited, setIsFormSubmited] = useState(false);
    const [formData, setFormData] = useState(resetFormData());
    const [error, setError] = useState();
    const windowSize = useWindowSize();
    const isPhone = windowSize.width < 1024;
    const storeConfig = Identify.getStoreConfig() || {};
    const {service} = storeConfig && storeConfig.simiStoreConfig && storeConfig.simiStoreConfig.config || {};
    const {types, description} = service;

    const uploadFileReturn = (data) => {
        hideFogLoading()
        let files = formData && formData.files || {};
        if (data && data.uploadfile && data.uploadfile.title) {
            files[data.uploadfile.title] = data.uploadfile;
        }
        onChangeInput('files', files)
    }

    const removeFile = (id) => {
        let files = formData && formData.files || {};
        if (files[id]) {
            delete files[id];
        }
        onChangeInput('files', files)
    }

    const getBase64 = (file, cb) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function () {
            cb(reader.result)
        };
        reader.onerror = function (error) {
            console.log('Error: ', error);
        };
    }

    const selectedFile = (id) => {
        if (document.getElementById(id)) {
            const input = document.getElementById(id)
            const filePath = input.files[0]
            if (filePath) {
                getBase64(filePath, (result) => {
                    if (result) {
                        let base64 = result.split("base64,");
                        base64 = base64[base64.length-1];
                        base64 = base64.split('"');
                        base64 = base64[0];
                        showFogLoading()
                        const fileData = {
                            type: filePath.type,
                            name: filePath.name,
                            size: filePath.size,
                            base64: base64
                        }
                        uploadFile(uploadFileReturn, {fileData})
                        input.value = '';
                        return
                    }
                    showToastMessage(Identify.__('Cannot read file content'))
                    hideFogLoading()
                });
            }
        }
    }

    
    const chooseServiceType = (item) => {
        onChangeInput('service', item)
    }

    const onChangeInput = (name, value) => {
        let newFormData = {...formData}
        newFormData[name] = value;
        setFormData(newFormData);
    }

    const onSubmitForm = () => {
        //Submit form api
        let error = false;
        for(let name in dataValidate){
            if (validate(name, dataValidate[name], true) === 'error') {
                error = true;
                break;
            }
        }
        if (!error && formData) {
            let postData = formData;
            postData.service = formData.service && formData.service.value || '';
            showFogLoading()
            submitQuote((resData) => {
                if (resData.error) {
                    setError(resData.error);
                } else {
                    setError('');
                    showToastMessage(Identify.__('Thank you for your request. Bianca will contact you soon for further details.'));
                    setFormData(resetFormData());
                }
                hideFogLoading()
                setIsFormSubmited(false);
            }, postData)
        } else {
            setIsFormSubmited(true);
        }
    }

    // validate data
    const validate = (name, types = [], force = false) => {
        let isInvalid = false;
        if (isFormSubmited || force) {
            types.forEach((type) => {
                switch(type){
                    case 'empty':
                        if ((formData[name] instanceof Object || formData[name] instanceof Array) 
                            && Object.keys(formData[name]).length === 0) {
                                isInvalid = true;
                        } else {
                            if ([undefined, null, ''].includes(formData[name])) isInvalid = true;
                        }
                        break;
                    case 'email':
                        if (!validateEmail(formData[name])) isInvalid = true;
                        break;
                    case 'phone':
                        if (!validateTelephone(formData[name])) isInvalid = true;
                        break;
                    case 'number':
                        if (!validateNumber(formData[name])) isInvalid = true;
                        break;
                    default:
                        break;
                }
            })
            if (isInvalid) return 'error';
        }
        return '';
    }

    const breadcrumb = () => {
        const breadcrumbs = [{name: Identify.__("Home"), link: '/'}, {name: Identify.__("Clothing Alterations")}];
        return (
            <BreadCrumb breadcrumb={breadcrumbs} history={props.history} />
        );
    }
    return (
        <div className={`clothing-service ${isPhone?'mobile':''} ${getOS()}`}>
            <div className="container">
                {breadcrumb()}
                <div className="header">
                    <h1>{Identify.__('CLOTHING ALTERATIONS')}</h1>
                    <p>{Identify.__(description)}</p>
                </div>
                <div className="quote-form">
                    <h3>{Identify.__('Please fill in the form below for a quote.')}</h3>
                    <div className="form">
                        <div className="form-row">
                            <label htmlFor="name">{Identify.__('Name')}<span>*</span></label>
                            <div className={`form-input ${validate('name', ['empty'])}`}>
                                <input value={formData['name']} onChange={(e) => onChangeInput('name', e.target.value)} id="name" name="name" placeholder={Identify.__(`User's name`)} />
                            </div>
                        </div>
                        <div className="form-row">
                            <label htmlFor="phone">{Identify.__('Phone Number')}<span>*</span></label>
                            <div className={`form-input ${validate('phone', ['empty'])}`}>
                                <input value={formData['phone']} onChange={(e) => onChangeInput('phone', e.target.value)} id="phone" name="phone" placeholder={Identify.__(`Phone number`)}/>
                            </div>
                        </div>
                        <div className="form-row">
                            <label htmlFor="email">{Identify.__('Email Address')}<span>*</span></label>
                            <div className={`form-input ${validate('email', ['empty', 'email'])}`}>
                                <input value={formData['email']} onChange={(e) => onChangeInput('email', e.target.value)} type="email" id="email" name="email" placeholder={Identify.__(`Email address`)}/>
                            </div>
                        </div>
                        <div className="form-row">
                            <label htmlFor="address">{Identify.__('Address')}<span>*</span></label>
                            <div className={`form-input ${validate('address', ['empty'])}`}>
                                <input value={formData['address']} onChange={(e) => onChangeInput('address', e.target.value)} id="address" name="address" placeholder={Identify.__(`Address`)}/>
                            </div>
                        </div>
                        <div className="form-row">
                            <label htmlFor="service">{Identify.__('Type Of Service')}<span>*</span></label>
                            <Select className={`form-input service-type ${validate('service', ['empty'])}`}
                                items={types}
                                selected={formData['service']}
                                showSelected={true} 
                                placeholder={Identify.__(`Please select`)} 
                                onChangeItem={chooseServiceType} 
                                icon={<ArrowDown />}
                                hiddenInput={{name: 'service'}}
                            />
                        </div>
                        <div className="form-row">
                            <label htmlFor="qty">{Identify.__('Quantity')}<span>*</span></label>
                            <div className={`form-input ${validate('qty', ['empty'])}`}>
                                <input value={formData['qty']} onChange={(e) => onChangeInput('qty', e.target.value)} id="qty" name="qty" placeholder={Identify.__(`Quantity`)}/>
                            </div>
                        </div>
                        <div className="form-row">
                            <label htmlFor="detail">{Identify.__('Details')}<span>*</span></label>
                            <div className={`form-input detail ${validate('detail', ['empty'])}`}>
                                <textarea value={formData['detail']} onChange={(e) => onChangeInput('detail', e.target.value)} id="detail" name="detail" placeholder={Identify.__(`Please describe what work you need to be done in as much detail as possible.`)}/>
                            </div>
                        </div>
                        <div className="form-row">
                            <label>{Identify.__('Upload Files')}<span>*</span></label>
                            <div className={`form-input files ${validate('files', ['empty'])}`}>
                                <span>{Identify.__(`You can upload up to 4 files`)}</span>
                                {Object.values(formData.files).length < 4 &&
                                    <div className="upload-file">
                                        <label htmlFor="service-files">{Identify.__('Choose file')}</label>
                                        <input onChange={() => selectedFile('service-files')} id={`service-files`} style={{display: 'none'}} name="files" type="file" accept=".gif,.jpg,.jpeg,.png,.doc,.docx,audio/*,video/*,image/*,text/xml,text/html,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                                    </div>
                                }
                                {Object.values(formData.files).length > 0 && 
                                    <div className="added-files">
                                    {Object.values(formData.files).map((file, index) => {
                                        if (!file) return null;
                                        return <div className="file-item" key={index}>
                                            <span>{file.title}</span>
                                            <i onClick={() => removeFile(file.title)} className="remove-file"><CrossIcon color="#101820"/></i>
                                        </div>
                                    })}
                                    </div>
                                }
                            </div>
                        </div>
                        <div className="form-submit">
                            <div className="btn" onClick={onSubmitForm}><span>{Identify.__('Submit')}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default withRouter(ClothingService)