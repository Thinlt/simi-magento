import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import OptionBase from '../OptionBase'
import defaultClasses from './downloadableoptions.css'
import Checkbox from '../OptionType/Checkbox';

class DownloadableOptions extends OptionBase {
    constructor(props){
        super(props);
        this.classes = defaultClasses
        this.exclT = 0;
        this.inclT = 0;
        console.log(this.data)
    }

    renderOptions =()=>{
        const objOptions = [];
        if (this.data.download_options) {
            const attributes = this.data.download_options;
            for (const i in attributes) {
                const attribute = attributes[i];
                const element = this.renderAttribute(attribute,i);
                objOptions.push(element);
            }
        }
        return (
            <div>
                <form id="downloadableOption" className="product-options-tablet">
                    {objOptions}
                </form>
            </div>
        );
    };

    renderAttribute = (attribute,id)=>{
        return (
            <div key={Identify.randomString(5)} className="option-select">
                <div className="option-title">
                    <span>{attribute.title}</span>
                </div>
                <div className="option-content">
                    <div className="options-list">
                        {this.renderMultiCheckbox(attribute, id)}
                    </div>
                </div>
            </div>
        )
    };

    renderMultiCheckbox =(ObjOptions, id = '0')=>{
        const {classes} = this
        const options = ObjOptions.value;
        const objs = [];
        for (const i in options) {
            const item = options[i];
            const element = (
                <div key={Identify.randomString(5)} className="option-row">
                    <Checkbox id={id} title={item.title} value={item.id} parent={this} item={item} classes={classes}/>
                </div>
            );

            objs.push(element);
        }
        return objs;
    };

    updatePrices = (selected = this.selected)=>{
        let exclT = 0;
        let inclT = 0;
        const downloadableOptions = this.data.download_options;
        selected = selected[0];
        for (const d in downloadableOptions) {
            const option = downloadableOptions[d];
            const values = option.value;
            for (const v in values) {
                const value = values[v];
                if (Array.isArray(selected)) {
                    if (selected.indexOf(value.id) !== -1) {
                        if (value.price_excluding_tax) {
                            exclT += parseFloat(value.price_excluding_tax.price);
                            inclT += parseFloat(value.price_including_tax.price);
                        } else {
                            //excl and incl is equal when server return only one price
                            exclT += parseFloat(value.price);
                            inclT += parseFloat(value.price);
                        }
                    }
                } else {
                    if (value.id === selected) {
                        //add price
                        if (value.price_excluding_tax) {
                            exclT += parseFloat(value.price_excluding_tax.price);
                            inclT += parseFloat(value.price_including_tax.price);
                        } else {
                            //excl and incl is equal when server return only one price
                            exclT += parseFloat(value.price);
                            inclT += parseFloat(value.price);
                        }
                    }
                }

            }
        }
        this.parentObj.Price.setDownloadableOptionPrice(inclT, exclT);
    }

    setParamQty = ()=>{
        const qty = $('input.option-qty').val();
        this.params['qty'] = qty;
    };

    getParamsCustomOption = () => {
        if(this.Custom && this.Custom.data.custom_options){
            const params = this.Custom.selected;
            this.params['options'] = params;
        }
    };

    getParams = ()=>{
        if(!this.checkOptionRequired()) return false;
        this.selected = this.selected[0];
        this.setParamOption('links');
        this.getParamsCustomOption();
        this.setParamQty();
        return this.params;
    };

    getParamsCustomOption = () => {
        if(this.Custom && this.Custom.data.custom_options){
            const params = this.Custom.selected;
            this.params['options'] = params;
        }
    };

    getParams = ()=>{
        if(!this.checkOptionRequired() || !this.Custom.checkOptionRequired()) return false;
        this.selected = this.selected[0];
        this.setParamOption('links');
        return this.params;
    };

    render(){
        return (
            <div>
                {this.renderOptions()}
            </div>
        )
    }
}
export default DownloadableOptions;